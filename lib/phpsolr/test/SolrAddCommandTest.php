<?php
/**
 * Test case {@link SolrAddCommandTest}.
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
Solr::autoload('SolrConnection');
Solr::autoload('SolrDocument');
Solr::autoload('SolrField');
Solr::autoload('SolrHttpClient');

/**
 *  Test case for the <add /> command.
 * 
 * @package php_solr
 * @subpackage testcases
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 */
class SolrAddCommandTest extends PHPUnit_Framework_TestCase {
    /**
     * A {@link SolrConnection} instance with a mocked HTTP_Client.
     *
     * @var SolrConnection
     */
    private $solr = null;
    
    /**
     * HTTP_Client mock object.
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $httpClientMock;
    
    /**
     * {@link SolrDocument} stub.
     *
     * @var PHPUnit_FrameworkMockObject_MockObject
     */
    private $document;

    /**
     * Sets up the mock objects.
     */
    function setUp () {
    	$this->httpClientMock = $this->getMock('SolrHttpClient', array('sendRequest'));
        $this->solr = SolrConnection::__set_state(array(
            'httpClient' => $this->httpClientMock
        ));
        $field = $this->getMock('SolrField');
        $field->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $field->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue('bar'));
        $field->expects($this->any())
            ->method('getBoost')
            ->will($this->returnValue(null));
        $this->document = $this->getMock('SolrDocument');
        $this->document
            ->expects($this->any())
            ->method('getFields')
            ->will($this->returnValue(array($field)));
        $this->document
            ->expects($this->any())
            ->method('getBoost')
            ->will($this->returnValue(null));
	}
	
    /**
     * Tests if one simple document is added correctly.
     */
    function testAddOneDocument () {
        $this->httpClientMock
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                $this->equalTo('/update'),
                $this->equalTo('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<add><doc><field name="foo">bar</field></doc></add>' . "\n")
            )
            ->will($this->returnValue(new HTTP_Request2_Response('HTTP/1.0 200 OK')));
        $this->solr->add($this->document);
    }
    
    /**
     * Tests if three simple documenst are added correctly.
     */
    function testAddThreeDocument () {
        $this->httpClientMock
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                $this->equalTo('/update'),
                $this->equalTo('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<add><doc><field name="foo">bar</field></doc><doc><field name="foo">bar</field></doc><doc><field name="foo">bar</field></doc></add>' . "\n")
            )
            ->will($this->returnValue(new HTTP_Request2_Response('HTTP/1.0 200 OK')));
        $this->solr->add(array(
            $this->document,
            $this->document,
            $this->document
        ));
    }
    
    /**
     * Tests if a document with multiple fields is added correctly.
     */
    function testAddComplexDocument () {
        $field1 = $this->getMock('SolrField');
        $field1->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('dummy1'));
        $field1->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue('foobar'));
        $field1->expects($this->any())
            ->method('getBoost')
            ->will($this->returnValue(null));
        $field2 = $this->getMock('SolrField');
        $field2->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('dummy2'));
        $field2->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue('foobar'));
        $field2->expects($this->any())
            ->method('getBoost')
            ->will($this->returnValue(1.6));
        $field3 = $this->getMock('SolrField');
        $field3->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('dummy3'));
        $field3->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue('foobar'));
        $field3->expects($this->any())
            ->method('getBoost')
            ->will($this->returnValue(null));
        $document = $this->getMock('SolrDocument');
        $document
            ->expects($this->any())
            ->method('getFields')
            ->will($this->returnValue(array($field1, $field2, $field3)));
        $document
            ->expects($this->any())
            ->method('getBoost')
            ->will($this->returnValue(2.5));

        $this->httpClientMock
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                $this->equalTo('/update'),
                $this->equalTo('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<add><doc boost="2.5"><field name="dummy1">foobar</field><field name="dummy2" boost="1.6">foobar</field><field name="dummy3">foobar</field></doc></add>' . "\n")
            )
            ->will($this->returnValue(new HTTP_Request2_Response('HTTP/1.0 200 OK')));

        $this->solr->add($document);
    }
}

?>