<?php
/**
 * Test case {@link SolrQueryTest}.
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
 * @subpackage testcases
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 * @copyright 2008, 2009, Alexander M. Turek
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version $Id$
 */

require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../src/Solr.php';
Solr::autoload('SolrQuery');

/**
 *  Test case for {@link SolrQuery}.
 * 
 * @package php_solr
 * @subpackage testcases
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 */
class SolrQueryTest extends PHPUnit_Framework_TestCase {
    private $solrQuery = 'hello solr world';
    private $queryString = '?qt=standard&wt=json&q=hello+solr+world';

    /**
     * Tests the constructor.
     */
    function testPlainQuery () {
        $query = new SolrQuery($this->solrQuery);
        $this->assertEquals($this->queryString, $query->getQueryString());
    }
    
    /**
     * Tests {@link SolrQuery::setFieldList()}.
     */
    function testFieldList () {
        $query = new SolrQuery($this->solrQuery);
        $query->setFieldList('*');
        $this->assertEquals(
            $this->queryString . '&fl=%2A',
            $query->getQueryString()
        );
        
        $query->setFieldList(array('foo', 'bar'));
        $this->assertEquals(
            $this->queryString . '&fl=foo%2Cbar',
            $query->getQueryString()
        );
        
        $query->setFieldList();
        $this->assertEquals(
            $this->queryString,
            $query->getQueryString()
        );
    }
    
    /**
     * Tests {@link SolrQuery::setFieldList()} with invalid parameters.
     * 
     * @param mixed $param The parameter that shall be passed as "field list".
     * 
     * @expectedException InvalidArgumentException
     * @dataProvider      provideInvalidFieldLists
     */
    function testInvalidFieldList ($param) {
        $query = new SolrQuery($this->solrQuery);
        $query->setFieldList($param);
    }
    
    /**
     * Provides parameters for {@link testInvalidFieldList}.
     *
     * @return array
     */
    function provideInvalidFieldLists () {
    	return array(
    	   array(1.5),
    	   array(''),
    	   array(array('foobar', "  \n  "))
    	);
    }
    
    /**
     * Tests {@link SolrQuery::setFilters()}.
     */
    function testFilters () {
        $query = new SolrQuery($this->solrQuery);
        $query->setFilters('foo bar');
        $this->assertEquals(
            $this->queryString . '&fq=foo+bar',
            $query->getQueryString()
        );
        
        $query->setFilters(array('foo', 'bar'));
        $this->assertEquals(
            $this->queryString . '&fq=foo&fq=bar',
            $query->getQueryString()
        );
        
        $query->setFilters();
        $this->assertEquals(
            $this->queryString,
            $query->getQueryString()
        );
    }
    
    /**
     * Tests {@link SolrQuery::setOperator()}.
     */
    function testOperator () {
        $query = new SolrQuery($this->solrQuery);
        $query->setOperator(SolrQuery::OP_AND);
        $this->assertEquals(
            $this->queryString . '&q.op=AND',
            $query->getQueryString()
        );
        
        $query->setOperator(SolrQuery::OP_OR);
        $this->assertEquals(
            $this->queryString . '&q.op=OR',
            $query->getQueryString()
        );
        
        $query->setOperator();
        $this->assertEquals(
            $this->queryString,
            $query->getQueryString()
        );
    }
    
    /**
     *  Tests {@link SolrQuery::setOperator()} with invalid parameters.
     * 
     * @expectedException InvalidArgumentException
     */
    function testInvalidOperator () {
        $query = new SolrQuery($this->solrQuery);
        $query->setOperator('XOR');
    }
    
    /**
     * Tests {@link SolrQuery::setDefaultField()}.
     */
    function testDefaultField () {
        $query = new SolrQuery($this->solrQuery);
        $query->setDefaultField('foo');
        $this->assertEquals(
            $this->queryString . '&df=foo',
            $query->getQueryString()
        );
        
        $query->setDefaultField();
        $this->assertEquals(
            $this->queryString,
            $query->getQueryString()
        );
    }
    
    /**
     * Tests {@link SolrQuery::setRows()}.
     */
    function testRows () {
        $query = new SolrQuery($this->solrQuery);
        $query->setRows(10);
        $this->assertEquals(
            $this->queryString . '&rows=10',
            $query->getQueryString()
        );
        
        $query->setRows();
        $this->assertEquals(
            $this->queryString,
            $query->getQueryString()
        );
    }
    
    /**
     *  Tests {@link SolrQuery::setRows()} with invalid parameters.
     * 
     * @expectedException InvalidArgumentException
     * @dataProvider      provideInvalidRowCounts
     */
    function testInvalidRowCounts ($param) {
        $query = new SolrQuery($this->solrQuery);
        $query->setRows($param);
    }
    
    /**
     * Provides data for {@link testInvalidRowCounts()}.
     *
     * @return array
     */
    function provideInvalidRowCounts () {
        return array(
            array('foo'),
            array(0),
            array(-42)
        );
    }
    
    /**
     * Tests {@link SolrQuery::setStart()}.
     */
    function testStart () {
        $query = new SolrQuery($this->solrQuery);
        $query->setStart(10);
        $this->assertEquals(
            $this->queryString . '&start=10',
            $query->getQueryString()
        );
        
        $query->setStart(0);
        $this->assertEquals(
            $this->queryString . '&start=0',
            $query->getQueryString()
        );
        
        $query->setStart();
        $this->assertEquals(
            $this->queryString,
            $query->getQueryString()
        );
    }
    
    /**
     *  Tests {@link SolrQuery::setStart()} with invalid parameters.
     * 
     * @expectedException InvalidArgumentException
     * @dataProvider      provideInvalidOffsets
     */
    function testInvalidOffsets ($param) {
        $query = new SolrQuery($this->solrQuery);
        $query->setStart($param);
    }
    
    /**
     * Provides data for {@link testInvalidOffsets()}.
     *
     * @return array
     */
    function provideInvalidOffsets () {
        return array(
            array('foo'),
            array(-42)
        );
    }
    
    /**
     * Tests the magic faceting parameter.
     */
    function testFaceting () {
        $query = new SolrQuery($this->solrQuery);
        $this->assertType('SolrSimpleFacets', $query->facets);
        $this->assertEquals('', $query->facets->getQueryStringPart());
        $query->facets->setFields('foo');
        $this->assertEquals($this->queryString . '&facet=true&facet.field=foo', $query->getQueryString());
    }
}

?>