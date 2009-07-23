<?php
/**
 * Test case {@link SolrHitTest}.
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
 * @copyright 2008, 2009, Alexander M. Turek
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version $Id$
 */

require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../src/Solr.php';
Solr::autoload('SolrHit');

/**
 *  Test case for {@link SolrHit}.
 * 
 * @package php_solr
 * @subpackage testcases
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 */
class SolrHitTest extends PHPUnit_Framework_TestCase {
    /**
     * Tests normal usage with score.
     */
    function testWithScore () {
        $jsonCode = '{"id":42,"title":"PHP 5","score":4.102452}';
        $hit = new SolrHit(json_decode($jsonCode));
        
        $this->assertEquals(4.102452, $hit->getScore());
        $this->assertEquals(42, $hit['id']);
        $this->assertEquals('PHP 5', $hit['title']);
    }
    
    /**
     * Tests, if the score can be accessed directly.
     * 
     * @expectedException OutOfBoundsException
     */
    function testAccessingScore () {
        $jsonCode = '{"id":42,"title":"PHP 5","score":4.102452}';
        $hit = new SolrHit(json_decode($jsonCode));
        $hit['score'];
    }
    
    /**
     * Tests the bevior of {@link SolrHit::getScore()} if the score was not provided.
     */
    function testWithoutScore () {
        $jsonCode = '{"id":42,"title":"PHP 5"}';
        $hit = new SolrHit(json_decode($jsonCode));
        
        $this->assertNull($hit->getScore());
    }
    
    /**
     * Tests against the {@link Iterator} interface.
     */
    function testIterator () {
        $jsonCode = '{"id":42,"title":"PHP 5"}';
        $hit = new SolrHit(json_decode($jsonCode));
        
        $expected = array(
            'id' => 42,
            'title' => 'PHP 5'
        );
        
        foreach ($hit as $key => $value) {
        	list($expectedKey, $expectedValue) = each($expected);
        	$this->assertEquals($expectedKey, $key);
        	$this->assertEquals($expectedValue, $value);
        }
    }
    
    function testCountable () {
        $jsonCode = '{"id":42,"title":"PHP 5"}';
        $hit = new SolrHit(json_decode($jsonCode));
        
        $this->assertEquals(2, count($hit));
    }
    
    /**
     * Tests against the {@link ArrayAccess} interface.
     */
    function testArrayAccess () {
        $jsonCode = '{"id":42,"title":"PHP 5"}';
        $hit = new SolrHit(json_decode($jsonCode));
        
        $this->assertTrue(isset($hit['id']));
        $this->assertFalse(isset($hit['foo']));
    }
    
    /**
     * Tests the {@link SolrDocument} implementation.
     */
    function testSolrDocument () {
        $jsonCode = '{"id":42,"title":"PHP 5"}';
        $hit = new SolrHit(json_decode($jsonCode));
        
        $this->assertNull($hit->getBoost());
        
        $fields = $hit->getFields();
        $this->assertType('array', $fields);
        $this->assertEquals(2, count($fields));
        
        $this->assertType('SolrField', $fields[0]);
        $this->assertNull($fields[0]->getBoost());
        $this->assertEquals('id', $fields[0]->getName());
        $this->assertEquals(42, $fields[0]->getValue());
        
        $this->assertType('SolrField', $fields[1]);
        $this->assertNull($fields[1]->getBoost());
        $this->assertEquals('title', $fields[1]->getName());
        $this->assertEquals('PHP 5', $fields[1]->getValue());
    }
}

?>