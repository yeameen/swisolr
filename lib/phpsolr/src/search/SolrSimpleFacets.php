<?php
/**
 * The class {@link SolrSimpleFacets}.
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

Solr::autoload('SolrFacetFieldParameters');
Solr::autoload('SolrFacetField');

/**
 * A set of parameters to enable Solr's SimpleFacets in {@link SolrQuery}.
 * 
 * Facets are enabled for your query if you specify at least one facet field
 * using the {@link setFields()} method.
 * 
 * Example: Enabling faceting for the field 'category'.
 * 
 * <code>
 * $query = new SolrQuery('hello world');
 * $query->facets->setFields('category');
 * </code>
 * 
 * @see http://wiki.apache.org/solr/SimpleFacetParameters
 * 
 * @package php_solr
 * @subpackage search
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 * @since 0.4.0
 * 
 * @todo Allow per-field parameters.
 */
class SolrSimpleFacets extends SolrFacetFieldParameters {
    /**
     * The queries that should be used as facets.
     * 
     * @var array
     */
    private $queries = null;
    
    /**
     * The fields which should be treated as a facets.
     * 
     * This is an array of {@link SolrFacetField}.
     * 
     * @var array
     */
    private $fields = null;

    /**
     * Returns the faceting part of the HTTP query.
     * 
     * @uses urlencode()
     * 
     * @param string $solrSpecVersion The servers specification version (optional).
     * 
     * @return string
     */
    public function getQueryStringPart ($solrSpecVersion = null) {
        if (empty($this->fields) && empty($this->queries)) {
            return '';
        }
        $queryStringPart = '&facet=true';
        // Facet queries
        if (!empty($this->queries)) {
            foreach ($this->queries as $currentQuery) {
                $queryStringPart .= '&facet.query=' . urlencode($currentQuery);
            }
        }
        // Facet fields
        if (!empty($this->fields)) {
            foreach ($this->fields as $currentField) {
                $queryStringPart .= $currentField->getQueryStringPart($solrSpecVersion);
            }
            $queryStringPart .= parent::getQueryStringPart($solrSpecVersion);
        }
        return $queryStringPart;
    }
    
    /**
     * Sets the query/queries that should be used to generate a facet count.
     * 
     * Faceting will be considered disabled unless you specify at least one
     * field or query. Multiple queries may be specified as array.
     * 
     * Passing null or calling this function without a parameter removes all
     * facet queries.
     * 
     * @param string|array $queries
     */
    public function setQueries ($queries = null) {
        if (is_null($queries)) {
            $this->queries = null;
            return;
        }
        if (is_string($queries)) {
            $queries = array($queries);
        } elseif (!is_array($queries) || empty($queries)) {
            throw new InvalidArgumentException();
        }

        foreach ($queries as $currentQuery) {
            if (!is_string($currentQuery)) {
                throw new InvalidArgumentException();
            }
            $currentQuery = trim($currentQuery);
            if (strlen($currentQuery) < 1) {
                throw new InvalidArgumentException();
            }
        }
        $this->queries = array_values($queries);
    }
    
    /**
     * Sets the field(s) which should be treated as a facet(s).
     * 
     * Faceting will be considered disabled unless you specify at least one
     * field or query. Multiple fields may be specified as array.
     * 
     * Passing null or calling this function without a parameter removes all
     * facet fields.
     * 
     * @param SolrFacetField|string|array $fields The fields.
     */
    public function setFields ($fields = null) {
        if (is_null($fields)) {
            $this->fields = null;
            return;
        }
        if (is_string($fields) || $fields instanceof SolrFacetField) {
            $fields = array($fields);
        } elseif (!is_array($fields) || empty($fields)) {
            throw new InvalidArgumentException();
        }

        $this->fields = array();
        foreach ($fields as $currentField) {
            if (is_string($currentField)) {
                $this->fields[] = new SolrFacetField($currentField);
            } elseif ($currentField instanceof SolrFacetField) {
                $this->fields[] = $currentField;
            } else {
                throw new InvalidArgumentException();
            }
        }
    }
}

?>