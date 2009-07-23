<?php
/**
 * The class {@link SolrQuery}.
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

/**
 * A query for Solr's StandardRequestHandler.
 * 
 * Please note that this library operates with UTF-8 strings, just like Solr.
 * Because of that, it is mandatory that all strings passed to any of
 * {@link SolrQuery}'s methods (especially the fields and the query itself)
 * are encoded in UTF-8.
 * 
 * Please have a look at {@link SolrSimpleFacets} if you want to use faceting.
 * 
 * Example: Sending the query "php mysql" and applying the filter
 * "year:[2005 TO *]" to it.
 * 
 * <code>
 * require_once 'php_solr/Solr.php';
 * Solr::autoload('SolrQuery');
 * 
 * $query = new SolrQuery('php mysql');
 * $query->setFilters('year:[2005 TO *]');
 * 
 * $solr = Solr::connect();
 * $result = $solr->query($query);
 * </code>
 * 
 * @see http://wiki.apache.org/solr/StandardRequestHandler
 * @see http://wiki.apache.org/solr/SolrQuerySyntax
 * 
 * @package php_solr
 * @subpackage search
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 * @since 0.3.0
 * 
 * @property-read SolrSimpleFacets $facets Facets
 */
class SolrQuery {
    /**
     * AND operator.
     */
    const OP_AND = 'AND';
    /**
     * OR operator.
     */
    const OP_OR = 'OR';

    /**
     * Sort facet by count.
     * 
     * @var string
     */
    const FACET_SORT_COUNT = 'count';

    /**
     * Sort facet lexicographically.
     * 
     * @var string
     */
    const FACET_SORT_LEX = 'lex';

    /**
     * The query.
     *
     * @var string
     */
    private $query;

    /**
     * The default operator.
     *
     * @var string
     */
    private $operator = null;

    /**
     * The default field.
     * 
     * @var string
     */
    private $defaultField = null;

    /**
     * The maximum number of documents that shall be returned.
     *
     * @var integer
     */
    private $rows = null;
    
    /**
     * The offset for the returned result set.
     *
     * @var integer
     */
    private $start = null;
    
    /**
     * The list of fields to return.
     *
     * @var array(string)
     */
    private $fields = null;
    
    /**
     * The filter queries to apply.
     * 
     * @var array(string)
     */
    private $filters = null;
    
    /**
     * Faceting parameters.
     * 
     * @var SolrSimpleFacets
     */
    private $facets = null;
    
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
                if (is_null($this->facets)) {
                    Solr::autoload('SolrSimpleFacets');
                    $this->facets = new SolrSimpleFacets();
                }
                return $this->facets;
            default:
            	throw new OutOfRangeException('Undefined property.');
        }
    }

    /**
     * Constructor for {@link SolrQuery}.
     * 
     * @param string $query The query.
     */
    public function __construct ($query) {
    	if (!is_string($query)) {
    		throw new InvalidArgumentException('Invalid query.');
    	}
    	$this->query = $query;
    }
    
    /**
     * return the query
     *
     * @return string
     */
    public function getQuery() {
        return $this->query;
    }



    /**
     * Build the query string of the URL for this query.
     *
     * @uses urlencode()
     * 
     * @param string $solrSpecVersion The servers specification version (optional).
     * 
     * @return string
     */
    public function getQueryString ($solrSpecVersion = null) {
//        $queryString = '?qt=standard&wt=json&q=' . urlencode($this->query);
        $queryString = '?qt=standard&wt=json&q=' . urlencode($this->getQuery());
        if (!is_null($this->start)) {
            $queryString .= '&start=' . urlencode($this->start);
        }
        if (!is_null($this->rows)) {
            $queryString .= '&rows=' . urlencode($this->rows);
        }
        if (!is_null($this->operator)) {
            $queryString .= '&q.op=' . urlencode($this->operator);
        }
        if (!is_null($this->defaultField)) {
            $queryString .= '&df=' . urlencode($this->defaultField);
        }
        if (!is_null($this->fields)) {
            $queryString .= '&fl=' . urlencode(join(',', $this->fields));
        }
        if (!is_null($this->filters)) {
            foreach ($this->filters as $currentFilter) {
                $queryString .= '&fq=' . urlencode($currentFilter);
            }
        }
        if (!is_null($this->facets)) {
            $queryString .= $this->facets->getQueryStringPart($solrSpecVersion);
        }
        return $queryString;
    }
    
    /**
     * Sets the list of fields to return.
     * 
     * This parameter can be used to specify a set of fields to return,
     * limiting the amount of information in the response. When returning the
     * results to the client, only fields in this list will be included.
     *
     * The string "score" can be used to indicate that the score of each
     * document for the particular query should be returned as a field, and the
     * string "*" can be used to indicate all stored fields the document has.
     *
     * Examples
     *
     * <code>
     * // Only return the "id", "name", and "price" fields
     * $query->setFieldList(array('id', 'name', 'price'));
     *
     * // Return the "id" field and the score 
     * $query->setFieldList(array('id', 'score'));
     *
     * // Return all fields the each document has
     * $query->setFieldList('*');
     * 
     * // Return all fields each document has, along with the score
     * $query->setFieldList(array('*', 'score'));
     * </code>
     * 
     * Passing null or calling this function without a parameter resets your
     * setting to the server default.
     * 
     * Please note that Solr is unable to provide the value of fields that are
     * not marked as stored in your Schema.xml!
     *
     * @param string|array(string) $fields
     */
    public function setFieldList ($fields = null) {
    	if (is_null($fields)) {
    		$this->fields = null;
    		return;
    	}
        if (is_string($fields)) {
            $fields = array($fields);
        } elseif (!is_array($fields) || empty($fields)) {
            throw new InvalidArgumentException();
        }

        foreach ($fields as $currentField) {
            if (!is_string($currentField)) {
                throw new InvalidArgumentException();
            }
            $currentField = trim($currentField);
            if (strlen($currentField) < 1) {
                throw new InvalidArgumentException();
            }
        }
        $this->fields = array_values($fields);
    }
    
    /**
     * Sets the filter queries for this query.
     * 
     * This parameter can be used to specify one ore multiple queries that can
     * be used to restrict the super set of documents that can be returned,
     * without influencing score.
     * 
     * Passing null or calling this function without a parameter drops all
     * filters.
     * 
     * @param string|array(string) $filters
     * 
     * @since 0.3.2
     */
    public function setFilters ($filters = null) {
        if (is_null($filters)) {
            $this->filters = null;
            return;
        }
        if (is_string($filters)) {
            $filters = array($filters);
        } elseif (!is_array($filters) || empty($filters)) {
            throw new InvalidArgumentException();
        }

        foreach ($filters as $currentFilter) {
            if (!is_string($currentFilter)) {
                throw new InvalidArgumentException();
            }
            $currentFilter = trim($currentFilter);
            if (strlen($currentFilter) < 1) {
                throw new InvalidArgumentException();
            }
        }
        $this->filters = array_values($filters);
    }
    
    /**
     * Sets the default field for this query.
     * 
     * Passing null or calling this function without a parameter resets your
     * setting to the server default.
     * 
     * @param string $defaultField
     */
    public function setDefaultField ($defaultField = null) {
        if (!is_null($defaultField) && !is_string($defaultField)) {
            throw new InvalidArgumentException();
        }
        $this->defaultField = $defaultField;
    }
    
    /**
     * Specify the default operator.
     * 
     * Specifies the default operator for query expressions, overriding the
     * default operator specified in Solr's schema.xml. Possible values are
     * {@link OP_AND} or {@link OP_OR}.
     * 
     * Passing null or calling this function without a parameter resets your
     * setting to the server default.
     * 
     * Example: Setting the default operator to AND.
     * 
     * <code>
     * $query->setOperator(SolrQuery::OP_AND);
     * </code>
     *
     * @param string $operator
     */
    public function setOperator ($operator = null) {
        if (!is_null($operator) && $operator != self::OP_AND && $operator != self::OP_OR) {
        	throw new InvalidArgumentException();
        }
        $this->operator = $operator;
    }
    
    /**
     * Sets the maximum number of documents.
     * 
     * When specified, this parameter indicates the maximum number of documents
     * from the complete result set to return to the client for every request.
     * 
     * Passing null or calling this function without a parameter resets your
     * setting to the server default.
     *
     * @param integer $rows
     */
    public function setRows ($rows = null) {
        if (is_null($rows)) {
            $this->rows = null;
    	   return;
        }

        if (!is_numeric($rows) || intval($rows) < 1) {
            throw new InvalidArgumentException();
        }
        $this->rows = intval($rows);
    }

    /**
     * Sets the offset for the returned result set.
     * 
     * When specified, this parameter indicates the offset in the complete
     * result set for the queries where the set of returned documents should
     * begin.
     * 
     * Passing null or calling this function without a parameter resets your
     * setting to the server default.
     *
     * @param integer $start
     */
    public function setStart ($start = null) {
        if (is_null($start)) {
            $this->start = null;
            return;
        }

        if (!is_numeric($start) || intval($start) < 0) {
            throw new InvalidArgumentException();
        }
        $this->start = intval($start);
    }
}

?>