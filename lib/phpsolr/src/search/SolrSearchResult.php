<?php
/**
 * The class {@link SolrSearchResult}.
 * 
 * Copyright (c) 2008, 2009, Alexander M. Turek
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * - Neither the name of the author nor the names of the contributors may be
 *   used to endorse or promote products derived from this software without
 *   specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 * 
 * @package php_solr
 * @subpackage search
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 * @copyright 2008, 2009, Alexander M. Turek
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version $Id$
 */

Solr::autoload('SolrHit');
Solr::autoload('SolrFacetCounts');

/**
 * A search result.
 * 
 * An instance of this class is returned by {@link SolrConnection::query()}.
 * This object represents a the result of the query sent. It may be browsed via
 * a foreach loop as if it was an array of {@link SolrHit} objects.
 * 
 * <code>
 * require_once 'php_solr/Solr.php';
 * 
 * $solr = Solr::connect();
 * $result = $solr->query('php');
 * 
 * echo 'We have found ' . $result->getNumFound() . ' documents total.' . "\n";
 * echo 'Our result set contains ' . count($result) . ' documents.' . "\n";
 * echo 'Let\'s start with document no. ' . $result->getStart() . '.' . "\n";
 * 
 * foreach ($result as $currentHit) {
 *     assert($currentHit instanceof SolrHit);
 *     // do something
 * }
 * </code>
 * 
 * Alternatively, the hits can be accessed by index:
 * 
 * <code>
 * for ($i = 0; $i < count($result); $i++) {
 *     $currentHit = $result[$i];
 *     assert($currentHit instanceof SolrHit);
 *     // do something
 * }
 * </code>
 * 
 * The array access is provided read-only. Any attempt to unset or change
 * array elements will result in a {@link BadMethodCallException}.
 * 
 * If there are facets attached to the result, you can access them via the
 * {@link $facets} property. Please refer to {@link SolrFacetCounts} for more
 * details.
 * 
 * @package php_solr
 * @subpackage search
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 * @since 0.3.0
 * 
 * @property-read SolrFacetCounts $facets The facets attached to this result.
 */
class SolrSearchResult implements Iterator, Countable, ArrayAccess {
    /**
     * The total number of documents found.
     * 
     * @var integer
     */
    private $numFound = 0;
    
    /**
     * The result offset.
     * 
     * @var integer
     */
    private $start = 0;
    
    /**
     * The documents as decoded JSON objects.
     * 
     * @var array(object)
     */
    private $docs = array();
    
    /**
     * The facets attached to this result.
     * 
     * @var SolrFacetCounts
     */
    private $facets = null;
    
    /**
     * Constructor for {@link SolrSearchResult}.
     */
    private function __construct () {}
    
    /**
     * Getter for magic properties.
     * 
     * @param string $property The parameter.
     * @return mixed
     */
    public function __get ($property) {
        if (!is_string($property)) {
            throw new InvalidArgumentException();
        }
        
        switch ($property) {
            case 'facets':
                return $this->facets;
            default:
                throw new OutOfRangeException('Undefined property.');
        }
    }

    /**
     * Parses the JSON response of the StandardRequestHandler.
     * 
     * @param string $response
     * @return SolrSearchResult
     * 
     * @uses json_decode
     */
    public static function parseResponse ($response) {
        if (!is_string($response)) {
            throw new InvalidArgumentException();
        }
        
        $searchResult = new self();
        $jsonResponse = json_decode($response);
        
        $searchResult->start    = intval($jsonResponse->response->start);
        $searchResult->numFound = intval($jsonResponse->response->numFound);
        $searchResult->docs     = $jsonResponse->response->docs;
        
        $searchResult->facets
            = isset($jsonResponse->facet_counts)
            ? SolrFacetCounts::parseResponse($jsonResponse->facet_counts)
            : new SolrFacetCounts();

        return $searchResult;
    }
    
    /**
     * Returns the result offset.
     * 
     * @return integer
     */
    public function getStart () {
    	return $this->start;
    }
    
    /**
     * Returns the total number of documents found.
     * 
     * @return integer
     */
    public function getNumFound () {
    	return $this->numFound;
    }
    
    /**
     * {@link Iterator} implementation.
     */
    public function rewind() {
        reset($this->docs);
    }

    /**
     * {@link Iterator} implementation.
     */
    public function current() {
        $current = current($this->docs);
        if (is_bool($current)) {
            return $current;
        }
        return new SolrHit($current);
    }

    /**
     * {@link Iterator} implementation.
     */
    public function key() {
        return key($this->docs);
    }

    /**
     * {@link Iterator} implementation.
     */
    public function next() {
    	$next = next($this->docs);
    	if (is_bool($next)) {
    		return $next;
    	}
        return new SolrHit($next);
    }

    /**
     * {@link Iterator} implementation.
     */
    public function valid() {
        return !is_null(key($this->docs));
    }

    /**
     * {@link Countable} implementation.
     */
    public function count () {
    	return count($this->docs);
    }
    
    /**
     * {@link ArrayAccess} implementation.
     * 
     * @param string $offset
     * @param mixed  $value
     */
    public function offsetSet($offset, $value) {
        throw new BadMethodCallException('Overwirting search results is not supported.');
    }
    
    /**
     * {@link ArrayAccess} implementation.
     * 
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset) {
        return array_key_exists($offset, $this->docs);
    }
    
    /**
     * {@link ArrayAccess} implementation.
     * 
     * @param string $offset
     */
    public function offsetUnset($offset) {
        throw new BadMethodCallException('Deleting search results is not supported.');
    }
    
    /**
     * {@link ArrayAccess} implementation.
     * 
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        if (!array_key_exists($offset, $this->docs)) {
            throw new OutOfBoundsException();
        }

        return new SolrHit($this->docs[$offset]);
    }
}

?>