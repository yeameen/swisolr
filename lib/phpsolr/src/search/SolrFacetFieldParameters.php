<?php
/**
 * The class {@link SolrFacetFieldParameters}.
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

Solr::autoload('SolrQuery');

/**
 * Common parameters for facet fields.
 * 
 * @see http://wiki.apache.org/solr/SimpleFacetParameters
 * 
 * @package php_solr
 * @subpackage search
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 * @since 0.4.1
 */
abstract class SolrFacetFieldParameters {
    /**
     * The prefix.
     * 
     * @var string
     */
    private $prefix = null;

    /**
     * Facet ordering.
     * 
     * Either {@link SolrQuery::FACET_SORT_LEX} or
     * {@link SolrQuery::FACET_SORT_COUNT}.
     * 
     * @var string
     */
    private $sort = null;

    /**
     * The maximum number of constraint counts.
     * 
     * @var integer
     */
    private $limit = null;

    /**
     * The offset for the list of constraints.
     * 
     * @var integer
     */
    private $offset = null;

    /**
     * The minimum counts for facet fields.
     */
    private $minCount = null;
	
    /**
     * Include "missing value" as a facet count?
     * 
     * @var bool
     */
    private $missing = null;
    
    /**
     * Prefix for facet parameters.
     * 
     * @var string
     */
    protected $fieldName = null;
    
    /**
     * Builds a query parameter.
     * 
     * This is a helper function for {@link getQueryStringPart()}. If
     * {@link $fieldName} is set, this function generates parameters for this
     * specific field.
     * 
     * @uses urlencode()
     * 
     * @param string $name       Name of the parameter.
     * @param string $value      Value of the parameter.
     * @param bool   $withPrefix Prepend field name (if present)?
     * @return string
     */
    protected function buildQueryParameter ($name, $value, $withPrefix = true) {
        return '&'
             . (is_null($this->fieldName) || !$withPrefix ? '' : 'f.' . urlencode($this->fieldName) . '.')
             . 'facet.' . urlencode($name)
             . '=' . urlencode($value);
    }

    /**
     * Returns the faceting part of the HTTP query.
     * 
     * @param string $solrSpecVersion The servers specification version (optional).
     * 
     * @return string
     */
    public function getQueryStringPart($solrSpecVersion = null) {
        $queryStringPart = '';
        if (!is_null($this->prefix)) {
            if (!is_null($solrSpecVersion) && version_compare($solrSpecVersion, '1.2', '<')) {
                throw new SolrException('The facet.prefix parameter is not supported by this Solr server.');
            }
            $queryStringPart .= $this->buildQueryParameter('prefix', $this->prefix);
        }
        if (!is_null($this->sort)) {
            if (!is_null($solrSpecVersion) && version_compare($solrSpecVersion, '1.4', '<')) {
                $queryStringPart .= $this->buildQueryParameter(
                    'sort',
                    ($this->sort == SolrQuery::FACET_SORT_COUNT ? 'true' : 'false')
                );
            } else {
                $queryStringPart .= $this->buildQueryParameter('sort', $this->sort);
            }
        }
        if (!is_null($this->limit)) {
            $queryStringPart .= $this->buildQueryParameter('limit', $this->limit);
        }
        if (!is_null($this->offset)) {
            $queryStringPart .= $this->buildQueryParameter('offset', $this->offset);
        }
        if (!is_null($this->minCount)) {
            $queryStringPart .= $this->buildQueryParameter('mincount', $this->minCount);
        }
        if (!is_null($this->missing)) {
            $queryStringPart .= $this->buildQueryParameter('missing', ($this->missing ? 'true' : ''));
        }
        return $queryStringPart;
    }
    
    /**
     * Sets the facet prefix.
     * 
     * The terms on which to facet are limited to those starting with the given
     * string prefix.
     * 
     * @param string $prefix
     */
    public function setPrefix ($prefix = null) {
        if (is_null($prefix)) {
            $this->prefix = null;
           return;
        }

        if (!is_scalar($prefix) || strval($prefix) === '') {
            throw new InvalidArgumentException();
        }
        $this->prefix = strval($prefix);
    }
    
    /**
     * Sets the ordering of the facets.
     * 
     * Please use the constants {@link SolrQuery::FACET_SORT_LEX} or
     * {@link SolrQuery::FACET_SORT_COUNT} here.
     * 
     * Passing null or calling this function without a parameter resets your
     * setting to the server default.
     * 
     * @param string $sort Either {@link SolrQuery::FACET_SORT_LEX} or
     *                     {@link SolrQuery::FACET_SORT_COUNT}.
     */
    public function setSort ($sort = null) {
        if (!in_array($sort, array(SolrQuery::FACET_SORT_COUNT, SolrQuery::FACET_SORT_LEX, null), true)) {
            throw new InvalidArgumentException ();
        }

        $this->sort = $sort;
    }
    
    /**
     * Sets the maximum number of constraint counts.
     * 
     * This param indicates the maximum number of constraint counts that
     * should be returned for the facet fields.
     * 
     * A negative value means unlimited. Passing null or calling this function
     * without a parameter resets your setting to the server default.
     * 
     * @param integer $limit
     */
    public function setLimit ($limit = null) {
        if (is_null($limit)) {
            $this->limit = null;
           return;
        }

        if (!is_numeric($limit)) {
            throw new InvalidArgumentException();
        }
        $this->limit = intval($limit);
    }
    
    /**
     * Sets the offset for the the list of constraints to allow paging.
     * 
     * Passing null or calling this function without a parameter resets your
     * setting to the server default.
     * 
     * @param integer $offset
     */
    public function setOffset ($offset = null) {
        if (is_null($offset)) {
            $this->offset = null;
           return;
        }

        if (!is_numeric($offset) || $offset < 0) {
            throw new InvalidArgumentException();
        }
        $this->offset = intval($offset);
    }
    
    /**
     * Sets the minimum counts for facet fields.
     * 
     * Passing null or calling this function without a parameter resets your
     * setting to the server default.
     * 
     * @param integer $minCount
     */
    public function setMinCount ($minCount = null) {
        if (is_null($minCount)) {
            $this->minCount = null;
           return;
        }

        if (!is_numeric($minCount) || $minCount < 0) {
            throw new InvalidArgumentException();
        }
        $this->minCount = intval($minCount);
    }
    
    /**
     * Include "missing value" as a facet count?
     * 
     * Set to "true" this param indicates that in addition to the Term based
     * constraints of a facet field, a count of all matching results which have
     * no value for the field should be computed.
     * 
     * Passing null or calling this function without a parameter resets your
     * setting to the server default.
     * 
     * @param bool $missing
     */
    public function setMissing ($missing = null) {
        if (!is_null($missing) && !is_bool($missing)) {
            throw new InvalidArgumentException ();
        }
        $this->missing = $missing;
    }
}

?>