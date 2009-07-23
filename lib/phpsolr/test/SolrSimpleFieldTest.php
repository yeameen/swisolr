<?php
/**
 * Test case {@link SolrFieldTest}.
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
Solr::autoload('SolrSimpleField');

/**
 *  Test case for {@link SolrSimpleField}.
 * 
 * @package php_solr
 * @subpackage testcases
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 */
class SolrSimpleFieldTest extends PHPUnit_Framework_TestCase {
	/**
	 * Tests the normal usage of the class.
	 */
    function testNormalUsage () {
        $field = new SolrSimpleField('dummy', 'foobar');

        $this->assertEquals('dummy', $field->getName());
        $this->assertEquals('foobar', $field->getValue());
        $this->assertNull($field->getBoost());
    }
    
    /**
     * Tests the normal usage of the class with boost factor.
     */
    function testNormalUsageWithBoost () {
        $field = new SolrSimpleField('dummy', 'foobar', 3.5);
        
        $this->assertEquals('dummy', $field->getName());
        $this->assertEquals('foobar', $field->getValue());
        $this->assertEquals(3.5, $field->getBoost());
    }
    
    /**
     * Tests the normal usage of the class with boost factor NULL.
     */
    function testNormalUsageWithNullBoost () {
        $field = new SolrSimpleField('dummy', 'foobar', null);
        
        $this->assertEquals('dummy', $field->getName());
        $this->assertEquals('foobar', $field->getValue());
        $this->assertNull($field->getBoost());
    }

    /**
     * Tests if an exception is thrown for invalid values.
     * 
     * @expectedException InvalidArgumentException
     * @dataProvider provideInvalidValues
     */
    function testInvalidValues ($name, $value) {
        new SolrSimpleField($name, $value);
    }
    
    /**
     * Provides Data for {@link testInvalidValues}.
     *
     * @return array
     */
    function provideInvalidValues () {
        return array(
            array(null, 'foobar'),
            array('dummy', null),
            array(array(), 'foobar'),
            array('dummy', array())
        );
    }
    
    /**
     * Tests if an exception is thrown for invalid boost values.
     * 
     * @expectedException InvalidArgumentException
     * @dataProvider provideInvalidBoostValues
     */
    function testInvalidBoostValues ($boost) {
        new SolrSimpleField('dummy', 'foobar', $boost);
    }
    
    /**
     * Provides Data for {@link testInvalidValues}.
     *
     * @return array
     */
    function provideInvalidBoostValues () {
        return array(
            array('foobar'),
            array(array())
        );
    }
}

