<?php
/**
 * The test case {@link SolrSimpleDocumentTest}.
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
Solr::autoload('SolrSimpleDocument');
Solr::autoload('SolrSimpleField');

/**
 *  Test case for {@link SolrSimpleDocument}.
 * 
 * @package php_solr
 * @subpackage testcases
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 */
class SolrSimpleDocumentTest extends PHPUnit_Framework_TestCase {
	/**
	 * Tests the normal usage of the class.
	 */
    function testNormalUsage () {
    	$doc = new SolrSimpleDocument();
    	$this->assertEquals(array(), $doc->getFields());
    	$this->assertNull($doc->getBoost());
    	
    	$field1 = new SolrSimpleField('dummy1', 'foobar');
    	$doc->addField($field1);
    	$this->assertEquals(array($field1), $doc->getFields());
    	$this->assertNull($doc->getBoost());
    	
    	$field2 = new SolrSimpleField('dummy2', 'foobar', 3.414);
    	$doc->addField($field2);
    	$this->assertEquals(array($field1, $field2), $doc->getFields());
    	$this->assertNull($doc->getBoost());
    }
    
    /**
     * Tests the constructor.
     */
    function testConstructor () {
    	$field1 = new SolrSimpleField('dummy1', 'foobar');
        $field2 = new SolrSimpleField('dummy2', 'foobar');
        $doc = new SolrSimpleDocument(array($field1, $field2));
        $this->assertEquals(array($field1, $field2), $doc->getFields());
        $this->assertNull($doc->getBoost());
        
        $field1 = new SolrSimpleField('dummy1', 'foobar');
        $doc = new SolrSimpleDocument(array($field1, $field2), 3.414);
        $this->assertEquals(array($field1, $field2), $doc->getFields());
        $this->assertEquals(3.414, $doc->getBoost());
    }
    
    /**
     * Test the constructor with invalid fields.
     * 
     * @expectedException InvalidArgumentException
     */
    function testInvalidFields () {
    	$field1 = new SolrSimpleField('dummy1', 'foobar');
    	$field2 = new Dummy();
    	$doc = new SolrSimpleDocument(array($field1, $field2));
    }
    
    /**
     * Test the constructor with invalid fields.
     * 
     * @expectedException InvalidArgumentException
     */
    function testInvalidBoost () {
        $field1 = new SolrSimpleField('dummy1', 'foobar');
        $field2 = new SolrSimpleField('dummy2', 'foobar');
        $doc = new SolrSimpleDocument(array($field1, $field2), 'foo');
    }
}

/**
 * Dummy Class
 * 
 * @package php_solr
 * @subpackage testcases
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 * @ignore
 */
class Dummy {}

?>