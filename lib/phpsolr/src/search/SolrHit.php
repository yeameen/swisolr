<?php
/**
 * The class {@link SolrHit}.
 * 
 * Copyright (c) 2008, Alexander M. Turek
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

Solr::autoload('SolrDocument');

/**
 * A document found by {@link SolrConnection::query()}.
 * 
 * An instance of this class works like an associative array. If a documents
 * contains a field with multiple values, an array is returned.
 * 
 * <code>
 * $solr = Solr::connect();
 * $result = $solr->query('php');
 * 
 * $firstHit = $result[0];
 * 
 * echo 'This is our first hit. Solr rated it with a score of ' . $firstHit->getScore() . "\n";
 * echo 'Field foo: ' . $firstHit['foo'] . "\n";
 * echo 'First value for field bar: ' . $firstHit['bar'][0] . "\n";
 * echo 'Second value for field bar: ' . $firstHit['bar'][1] . "\n";
 * </code>
 * 
 * Please note that the array access is provided read-only. Any attempt to
 * unset or change fields will lead to a {@link BadMethodCallException}.
 * 
 * Since instances of this class may act as {@link SolrDocument}s, you could
 * drop them into {@link SolrConnection::add()}. This may be useful, if you
 * want to quickly add a document found in index A to an index B.
 * 
 * Please note, that the hits only contain those fields requested in the
 * corresponding query (except for the score field). See
 * {@link SolrQuery::setFieldList()} for more details. 
 * 
 * @package php_solr
 * @subpackage search
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 * @since 0.3.0 
 */
class SolrHit implements ArrayAccess, Iterator, Countable, SolrDocument {
    /**
     * The fields of this hit.
     * 
     * @var array
     */
    private $fields;
    
    /**
     * The score for this hit.
     * 
     * @var float
     */
    private $score = null;
    
    /**
     * Constructor for {@link SolrHit}.
     */
    public function __construct($jsonObject) {
    	if (!is_object($jsonObject)) {
    		throw new InvalidArgumentException();
    	}
    	$this->fields = (array) $jsonObject;
    	
    	if (array_key_exists('score', $this->fields)) {
            $this->score = floatval($this->fields['score']);
            unset($this->fields['score']);
    	}
    }
    
    /**
     * Returns the score for this hit.
     * 
     * Returns null, if the score is not included in the result.
     * 
     * @return float
     */
    public function getScore () {
    	return $this->score;
    }
    
    /**
     * {@link Iterator} implementation.
     * 
     * @since 0.3.1
     */
    public function rewind() {
        reset($this->fields);
    }

    /**
     * {@link Iterator} implementation.
     * 
     * @since 0.3.1
     */
    public function current() {
        return current($this->fields);
    }

    /**
     * {@link Iterator} implementation.
     * 
     * @since 0.3.1
     */
    public function key() {
        return key($this->fields);
    }

    /**
     * {@link Iterator} implementation.
     * 
     * @since 0.3.1
     */
    public function next() {
        return next($this->fields);
    }

    /**
     * {@link Iterator} implementation.
     * 
     * @since 0.3.1
     */
    public function valid() {
        return !is_null(key($this->fields));
    }

    /**
     * {@link Countable} implementation.
     * 
     * @since 0.3.1
     */
    public function count () {
        return count($this->fields);
    }
    
    /**
     * {@link ArrayAccess} implementation.
     * 
     * @param string $offset
     * @param mixed  $value
     */
    public function offsetSet($offset, $value) {
        throw new BadMethodCallException('Overwirting SolrHit fields is not supported.');
    }
    
    /**
     * {@link ArrayAccess} implementation.
     * 
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset) {
        return array_key_exists($offset, $this->fields);
    }
    
    /**
     * {@link ArrayAccess} implementation.
     * 
     * @param string $offset
     */
    public function offsetUnset($offset) {
        throw new BadMethodCallException('Overwirting SolrHit fields is not supported.');
    }
    
    /**
     * {@link ArrayAccess} implementation.
     * 
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        if (!array_key_exists($offset, $this->fields)) {
            throw new OutOfBoundsException();
        }

        return $this->fields[$offset];
    }

    /**
     * {@link SolrDocument} implementation.
     * 
     * @return float
     */
    public function getBoost () {
        return null;
    }

    /**
     * {@link SolrDocument} implementation.
     * 
     * @uses SolrSimpleField
     * 
     * @return array(SolrField)
     */
    public function getFields () {
        Solr::autoload('SolrSimpleField'); 
    	
        $fields = array();
        foreach ($this->fields as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $part) {
                    $fields[] = new SolrSimpleField($key, $part);
                }
            } else {
                $fields[] = new SolrSimpleField($key, strval($value));
            }
        }
        return $fields;
    }
}

?>