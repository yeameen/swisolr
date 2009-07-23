<?php
/**
 * Test case {@link SolrSimpleFacetsTest}.
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
Solr::autoload('SolrSimpleFacets');
Solr::autoload('SolrFacetField');
Solr::autoload('SolrQuery');

/**
 *  Test case for {@link SolrSimpleFacets}.
 * 
 * @package php_solr
 * @subpackage testcases
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 */
class SolrSimpleFacetsTest extends PHPUnit_Framework_TestCase {
    /**
     * The object under test.
     * 
     * @var SolrFacetParameters
     */
    private $facets;

    /**
     * Sets up the faceting object.
     */
    function setUp () {
        $this->facets = new SolrSimpleFacets();
    }

    /**
     * Test for disabled faceting.
     * 
     * If no fields have been set for faceting, the query string part should
     * be empty.
     */
    function testNoFaceting () {
        $this->assertEquals('', $this->facets->getQueryStringPart());
    }
    
    /**
     * Tests facet queries.
     */
    function testQueries () {
        $this->facets->setQueries('foo');
        $this->assertEquals('&facet=true&facet.query=foo', $this->facets->getQueryStringPart());
        $this->facets->setQueries(array('foo', 'bar'));
        $this->assertEquals('&facet=true&facet.query=foo&facet.query=bar', $this->facets->getQueryStringPart());
        $this->facets->setLimit(20); // This parameter should be ignored for query faceting!
        $this->assertEquals('&facet=true&facet.query=foo&facet.query=bar', $this->facets->getQueryStringPart());
        $this->facets->setQueries();
        $this->assertEquals('', $this->facets->getQueryStringPart());
    }
    
    /**
     * This test justs sets two fields and checks the generated query string part.
     */
    function testJustFields () {
        $this->facets->setFields('foo');
        $this->assertEquals('&facet=true&facet.field=foo', $this->facets->getQueryStringPart());
        $this->facets->setFields(null);
        $this->assertEquals('', $this->facets->getQueryStringPart());
        $this->facets->setFields(array('foo', 'bar'));
        $this->assertEquals('&facet=true&facet.field=foo&facet.field=bar', $this->facets->getQueryStringPart());
        $this->facets->setFields();
        $this->assertEquals('', $this->facets->getQueryStringPart());
    }
    
    /**
     * This test uses {@link SolrFacetField} objects instead of plain strings.
     */
    function testFieldObjects () {
        $field1 = new SolrFacetField('foo');
        $field2 = new SolrFacetField('bar');
        $this->facets->setFields($field1);
        $this->assertEquals('&facet=true&facet.field=foo', $this->facets->getQueryStringPart());
        $this->facets->setFields(array($field1, $field2));
        $this->assertEquals('&facet=true&facet.field=foo&facet.field=bar', $this->facets->getQueryStringPart());
        $field2->setMinCount(0);
        $this->assertEquals('&facet=true&facet.field=foo&facet.field=bar&f.bar.facet.mincount=0', $this->facets->getQueryStringPart());
    }
    
    /**
     * Tests {@link SolrFacetParameters::setPrefix()}.
     */
    function testPrefix () {
        $this->facets->setPrefix('foo');
        $this->assertEquals('', $this->facets->getQueryStringPart('1.2'));
        $this->facets->setFields(array('foo', 'bar'));
        $this->assertEquals('&facet=true&facet.field=foo&facet.field=bar&facet.prefix=foo', $this->facets->getQueryStringPart('1.2'));
        $this->facets->setPrefix();
        $this->assertEquals('&facet=true&facet.field=foo&facet.field=bar', $this->facets->getQueryStringPart('1.2'));
    }
    
    /**
     * Tests {@link SolrFacetParameters::setSort()}.
     */
    function testSort () {
        $this->facets->setSort(SolrQuery::FACET_SORT_COUNT);
        $this->assertEquals('', $this->facets->getQueryStringPart('1.2'));
        $this->facets->setFields(array('foo', 'bar'));
        $this->assertEquals('&facet=true&facet.field=foo&facet.field=bar&facet.sort=true', $this->facets->getQueryStringPart('1.2'));
        $this->assertEquals('&facet=true&facet.field=foo&facet.field=bar&facet.sort=count', $this->facets->getQueryStringPart('1.4'));
        $this->facets->setSort(SolrQuery::FACET_SORT_LEX);
        $this->assertEquals('&facet=true&facet.field=foo&facet.field=bar&facet.sort=false', $this->facets->getQueryStringPart('1.2'));
        $this->assertEquals('&facet=true&facet.field=foo&facet.field=bar&facet.sort=lex', $this->facets->getQueryStringPart('1.4'));
        $this->facets->setSort();
        $this->assertEquals('&facet=true&facet.field=foo&facet.field=bar', $this->facets->getQueryStringPart('1.2'));
    }
    
    /**
     * Tests {@link SolrFacetParameters::setLimit()}.
     */
    function testLimit () {
        $this->facets->setLimit(10);
        $this->assertEquals('', $this->facets->getQueryStringPart());
        $this->facets->setFields(array('foo', 'bar'));
        $this->assertEquals('&facet=true&facet.field=foo&facet.field=bar&facet.limit=10', $this->facets->getQueryStringPart());
        $this->facets->setLimit();
        $this->assertEquals('&facet=true&facet.field=foo&facet.field=bar', $this->facets->getQueryStringPart());
    }
    
    /**
     * Tests {@link SolrFacetParameters::setOffset()}.
     */
    function testOffset () {
        $this->facets->setOffset(10);
        $this->assertEquals('', $this->facets->getQueryStringPart());
        $this->facets->setFields(array('foo', 'bar'));
        $this->assertEquals('&facet=true&facet.field=foo&facet.field=bar&facet.offset=10', $this->facets->getQueryStringPart());
        $this->facets->setOffset();
        $this->assertEquals('&facet=true&facet.field=foo&facet.field=bar', $this->facets->getQueryStringPart());
    }
    
    /**
     * Tests {@link SolrFacetParameters::setMinCount()}.
     */
    function testMinCount () {
        $this->facets->setMinCount(1);
        $this->assertEquals('', $this->facets->getQueryStringPart());
        $this->facets->setFields(array('foo', 'bar'));
        $this->assertEquals('&facet=true&facet.field=foo&facet.field=bar&facet.mincount=1', $this->facets->getQueryStringPart());
        $this->facets->setMinCount();
        $this->assertEquals('&facet=true&facet.field=foo&facet.field=bar', $this->facets->getQueryStringPart());
    }
    
    /**
     * Tests {@link SolrFacetParameters::setMissing()}.
     */
    function testMissing () {
        $this->facets->setMissing(true);
        $this->assertEquals('', $this->facets->getQueryStringPart());
        $this->facets->setFields(array('foo', 'bar'));
        $this->assertEquals('&facet=true&facet.field=foo&facet.field=bar&facet.missing=true', $this->facets->getQueryStringPart());
        $this->facets->setMissing(false);
        $this->assertEquals('&facet=true&facet.field=foo&facet.field=bar&facet.missing=', $this->facets->getQueryStringPart());
        $this->facets->setMissing();
        $this->assertEquals('&facet=true&facet.field=foo&facet.field=bar', $this->facets->getQueryStringPart());
    }
}

?>