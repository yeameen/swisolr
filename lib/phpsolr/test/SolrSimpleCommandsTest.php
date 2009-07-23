<?php
/**
 * Test case {@link SolrSimpleCommandsTest}.
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
Solr::autoload('SolrHttpClient');
Solr::autoload('SolrException');

/**
 *  Test case for the simple commands.
 * 
 * @package php_solr
 * @subpackage testcases
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 */
class SolrSimpleCommandsTest extends PHPUnit_Framework_TestCase {
    /**
     * A Solr instance with a mocked HTTP_Client 
     *
     * @var SolrConnection
     */
    private $solr = null;
    
    /**
     * HTTP client mock object.
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $httpClientMock;

    function setUp () {
        $this->httpClientMock = $this->getMock('SolrHttpClient', array('sendRequest'));
        $this->solr = SolrConnection::__set_state(array(
            'httpClient' => $this->httpClientMock
        ));
    }
    
    /**
     * Tests the commit command.
     */
    function testCommit () {
        $this->httpClientMock
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                $this->equalTo('/update'),
                $this->equalTo('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<commit/>' . "\n")
            )
            ->will($this->returnValue(new HTTP_Request2_Response('HTTP/1.0 200 OK')));
        $this->solr->commit();
    }
    
    /**
     * Tests the optimize command.
     */
    function testOptimize () {
        $this->httpClientMock
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                $this->equalTo('/update'),
                $this->equalTo('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<optimize/>' . "\n")
            )
            ->will($this->returnValue(new HTTP_Request2_Response('HTTP/1.0 200 OK')));
        $this->solr->optimize();
    }
    
    /**
     * Tests the ping command.
     */
    function testPing () {
        $this->httpClientMock
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->equalTo('/admin/ping'))
            ->will($this->returnValue(new HTTP_Request2_Response('HTTP/1.0 200 OK')));
        $this->assertTrue($this->solr->ping());
    }
    
    /**
     * Tests the ping command with a simulated internal server error.
     */
    function testPing500 () {
        $this->httpClientMock
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->equalTo('/admin/ping'))
            ->will($this->throwException(new SolrException('Solr returned HTTP status 500.')));
        $this->assertFalse($this->solr->ping());
    }
    
    /**
     * Tests {@link SolrConnection::testfetchServerInfo()}.
     */
    function testFetchServerInfo () {
        $response = new HTTP_Request2_Response('HTTP/1.0 200 OK');
        $response->appendBody('{"responseHeader":{"status":0,"QTime":10},"lucene":{"solr-spec-version":"1.2.2008.03.21.05.21.15","solr-impl-version":"1.2.0 - buildd - 2008-03-21 05:21:15","lucene-spec-version":"2.3.2","lucene-impl-version":"2.3.2 ${svnversion} - buildd - 2008-09-26 01:53:33"}}');

        $this->httpClientMock
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->equalTo('/admin/system/?wt=json'))
            ->will($this->returnValue($response));

        $this->assertEquals('1.2.2008.03.21.05.21.15', $this->solr->getSolrSpecVersion());
        $this->assertTrue(version_compare($this->solr->getSolrSpecVersion(), '1.3', '<'));
        $this->assertTrue(version_compare($this->solr->getSolrSpecVersion(), '1.2', '>='));
        $this->assertFalse(version_compare($this->solr->getSolrSpecVersion(), '1.1', '<'));
        $this->assertFalse(version_compare($this->solr->getSolrSpecVersion(), '1.3', '>='));
    }
    
    /**
     * Tests {@link SolrConnection::deleteById()}.
     */
    function testDeleteById () {
        $this->httpClientMock
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                $this->equalTo('/update'),
                $this->equalTo('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<delete><id>4711</id></delete>' . "\n")
            )
            ->will($this->returnValue(new HTTP_Request2_Response('HTTP/1.0 200 OK')));
        $this->solr->deleteById('4711');
    }
    
    /**
     * Tests {@link SolrConnection::deleteByQuery()}.
     */
    function testDeleteByQuery () {
        $this->httpClientMock
            ->expects($this->once())
            ->method('sendRequest')
            ->with(
                $this->equalTo('/update'),
                $this->equalTo('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<delete><query>foo:bar</query></delete>' . "\n")
            )
            ->will($this->returnValue(new HTTP_Request2_Response('HTTP/1.0 200 OK')));
        $this->solr->deleteByQuery('foo:bar');
    }
}

?>