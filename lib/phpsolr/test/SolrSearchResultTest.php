<?php
/**
 * Test case {@link SolrSearchResultTest}.
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
 * @subpackage testcases
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 * @copyright 2008, Alexander M. Turek
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version $Id$
 */

require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../src/Solr.php';
Solr::autoload('SolrSearchResult');
Solr::autoload('SolrHit');
Solr::autoload('SolrFacetCount');

/**
 *  Test case for {@link SolrSearchResult}.
 * 
 * @package php_solr
 * @subpackage testcases
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 */
class SolrSearchResultTest extends PHPUnit_Framework_TestCase {
    /**
     * Tests the returned result of {@link Solr::query()}.
     */
    function testDecode () {
        $solrResponse = '{"responseHeader":{"status":0,"QTime":0,"params":{"q":"php","qt":"standard","wt":"json"}},"response":{"numFound":1,"start":0,"docs":[{"id":42,"title":"PHP 5"}]}}';
        $searchResult = SolrSearchResult::parseResponse($solrResponse);

        $this->assertEquals(0, $searchResult->getStart());
        $this->assertEquals(1, $searchResult->getNumFound());
    }
    
    /**
     * Tests the {@link Iterator} implementation.
     */
    function testInterator () {
        $solrResponse = '{"responseHeader":{"status":0,"QTime":0,"params":{"q":"php","qt":"standard","wt":"json"}},"response":{"numFound":1,"start":0,"docs":[{"id":42,"title":"PHP 5"}]}}';
        $searchResult = SolrSearchResult::parseResponse($solrResponse);
        
        foreach ($searchResult as $currentHit) {
            $this->assertEquals(
                new SolrHit(json_decode('{"id":42,"title":"PHP 5"}')),
                $currentHit
            );
        }
    }
    
    /**
     * Tests the {@link Countable} implementation.
     */
    function testCountable () {
        $solrResponse = '{"responseHeader":{"status":0,"QTime":0,"params":{"q":"php","qt":"standard","wt":"json"}},"response":{"numFound":1,"start":0,"docs":[{"id":42,"title":"PHP 5"}]}}';
        $searchResult = SolrSearchResult::parseResponse($solrResponse);
        
        $this->assertEquals(1, count($searchResult));
    }
    
    /**
     * Tests the {@link ArrayAccess} implementation.
     */
    function testArrayAccess () {
        $solrResponse = '{"responseHeader":{"status":0,"QTime":0,"params":{"q":"php","qt":"standard","wt":"json"}},"response":{"numFound":1,"start":0,"docs":[{"id":42,"title":"PHP 5"},{"id":1337,"title":"PHP 6"}]}}';
        $searchResult = SolrSearchResult::parseResponse($solrResponse);
        
        $this->assertEquals(
            new SolrHit(json_decode('{"id":42,"title":"PHP 5"}')),
            $searchResult[0]
        );
        $this->assertEquals(
            new SolrHit(json_decode('{"id":1337,"title":"PHP 6"}')),
            $searchResult[1]
        );
    }
    
    /**
     * Tests faceting.
     */
    function testFaceting () {
        $solrResponse = '{"responseHeader":{"status":0,"QTime":0,"params":{"facet":"true","q":"php","facet.limit":"3","facet.field":"author","qt":"standard","wt":"json","rows":"0"}},"response":{"numFound":241,"start":0,"docs":[]},"facet_counts":{"facet_queries":{"jahr:[* TO 2004]":42,"jahr:[2005 TO *]":24},"facet_fields":{"author":["foo",25,"bar",10,"foobar",5]}}}';
        $searchResult = SolrSearchResult::parseResponse($solrResponse);
        
        $actualFacets = $searchResult->facets;
        $this->assertType('SolrFacetCounts', $actualFacets);
        
        $expectedFacetFields = array(
            'author' => array(
                'foo'    => 25,
                'bar'    => 10,
                'foobar' => 5
            )
        );
        
        $this->assertEquals($expectedFacetFields, $actualFacets->fields);
        
        $expectedFacetQueries = array(
            'jahr:[* TO 2004]' => 42,
            'jahr:[2005 TO *]' => 24
        );
        
        $this->assertEquals($expectedFacetQueries, $actualFacets->queries);
    }
}

?>