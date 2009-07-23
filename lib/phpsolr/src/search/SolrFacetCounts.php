<?php
/**
 * The class {@link SolrFacetCounts}.
 * 
 * Copyright (c) 2009, Alexander M. Turek
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
 * @copyright 2009, Alexander M. Turek
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version $Id$
 */

/**
 * The facet counts attached to a {@link SolrSearchResult}.
 * 
 * php_solr currently only supports facet fields. If there are facet fields
 * attached to your result, you can iterate over the {@link $fields} array to
 * access them.
 * 
 * Example: A search result with a facet field "author" with 10 hits for author
 * "Smith" and 5 hits for author "Miller".
 * 
 * <code>
 * // $searchResult is a SolrSearchResult object returned by
 * // SolrConnection::query().
 * 
 * foreach ($searchResult->facets['author'] as $value => $count) {
 *     echo "I have found $count books for the author $value.\n";
 * }
 * </code>
 * 
 * The result would look like this:
 * 
 * <pre>
 * I have found 10 books for the author Smith.
 * I have found 5 books for the author Miller.
 * </pre>
 * 
 * @package php_solr
 * @subpackage search
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 * @since 0.4.0
 * 
 * @property-read array $fields Facet fields.
 */
class SolrFacetCounts {
    /**
     * The facet fields.
     * 
     * Format:
     * <code>
     * array(
     *     $fieldName => array(
     *         $value => $count 
     *     )
     * );
     * </code>
     * 
     * @var array
     */
    private $fields = array();

    /**
     * Query facets.
     * 
     * @var array
     */
    private $queries = array();

    /**
     * Parses the 'facet_fields' part of the JSON response.
     * 
     * @param object $jsonResponse
     * @return SolrFacetCounts
     */
	public static function parseResponse ($jsonResponse) {
        $facets = new self();
        
        foreach ($jsonResponse->facet_fields as $currentField => $currentCounts) {
            $facets->fields[$currentField] = array();
            while (!empty($currentCounts)) {
                list($currentValue, $currentCount) = $currentCounts;
                $facets->fields[$currentField][$currentValue] = $currentCount;
                $currentCounts = array_slice($currentCounts, 2);
            }
            $facets->queries = (array) $jsonResponse->facet_queries;
        }
        
        return $facets;
    }
    
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
            case 'fields':
                return $this->fields;
            case 'queries':
                return $this->queries;
            default:
                throw new OutOfRangeException('Undefined property.');
        }
    }
}

?>