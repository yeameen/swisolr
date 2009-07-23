<?php
/**
 * Test case {@link SolrFacetFieldTest}.
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
 * @subpackage testcases
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 * @copyright 2009, Alexander M. Turek
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version $Id$
 */

require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../src/Solr.php';
Solr::autoload('SolrFacetField');

/**
 *  Test case for {@link SolrFacetField}.
 * 
 * @package php_solr
 * @subpackage testcases
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 */
class SolrFacetFieldTest extends PHPUnit_Framework_TestCase {
    /**
     * The object under test.
     * 
     * @var SolrFacetField
     */
    private $field;

    /**
     * Sets up the faceting object.
     */
    function setUp () {
        $this->field = new SolrFacetField('foo');
    }
    
    /**
     * Tests {@link SolrFacetField::getName()}.
     */
    function testGetName () {
        $this->assertEquals('foo', $this->field->getName());
    }

    /**
     * Test for a field without specific parameters.
     */
    function testNoParameters () {
        $this->assertEquals('&facet.field=foo', $this->field->getQueryStringPart());
    }
    
    /**
     * Test for a field with a specific mincount parameter.
     */
    function testMinCount () {
        $this->field->setMinCount(0);
        $this->assertEquals('&facet.field=foo&f.foo.facet.mincount=0', $this->field->getQueryStringPart());
        $this->field->setMinCount();
        $this->assertEquals('&facet.field=foo', $this->field->getQueryStringPart());
    }
}

?>