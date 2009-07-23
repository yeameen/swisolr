<?php
/**
 * Test case {@link SolrHttpClientTest}.
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
require_once 'HTTP/Request2/Adapter/Mock.php';
require_once dirname(__FILE__) . '/../src/Solr.php';
Solr::autoload('SolrHttpClient');

/**
 *  Test case for {@link SolrHttpClient}.
 * 
 * @package php_solr
 * @subpackage testcases
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 */
class SolrHttpClientTest extends PHPUnit_Framework_TestCase {
	/**
	 * The HTTP client.
	 * 
	 * @var SolrHttpClientMock
	 */
    private $mockedClient = null;
    
    /**
     * Mocked HTTP_Request2.
     * 
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $mockedRequest = null;
    
    /**
     * Sets up the mocked request.
     */
    public function setUp () {
        $this->mockedClient = new SolrHttpClientMock('http://localhost:8080/solr');
        $this->mockedRequest = $this->getMock(
            'HTTP_Request2',
            array(
                'setUrl',
                'setAdapter',
                'setHeader',
                'setBody',
                'setMethod',
                'send'
            )
        );
    	$this->mockedClient->setMock($this->mockedRequest);
    	$this->mockedRequest->expects($this->at(1))
    	                    ->method('setAdapter')
    	                    ->with($this->equalTo('HTTP_Request2_Adapter_Curl'))
    	                    ->will($this->returnValue($this->mockedRequest));
        $this->mockedRequest->expects($this->at(2))
                            ->method('setHeader')
                            ->with(
                                $this->equalTo('User-Agent'),
                                $this->equalTo('php_solr/' . Solr::VERSION))
                            ->will($this->returnValue($this->mockedRequest));
    }
    
    /**
     * Tests a successful GET request.
     */
    public function testSuccessfulPing () {
    	$response = new HTTP_Request2_Response('HTTP/1.0 200 OK');
    	
        $this->mockedRequest->expects($this->at(0))
                            ->method('setUrl')
                            ->with($this->equalTo('http://localhost:8080/solr/admin/ping'))
                            ->will($this->returnValue($this->mockedRequest));
        $this->mockedRequest->expects($this->at(3))
                            ->method('setMethod')
                            ->with($this->equalTo(HTTP_Request2::METHOD_GET))
                            ->will($this->returnValue($this->mockedRequest));
        $this->mockedRequest->expects($this->at(4))
                            ->method('send')
                            ->will($this->returnValue($response));

        $this->assertSame($response, $this->mockedClient->sendRequest('/admin/ping'));
    }
    
    /**
     * Tests if the status code 500 is handled correctly.
     * 
     * @expectedException SolrException
     */
    public function testHttp500 () {
        $response = new HTTP_Request2_Response('HTTP/1.0 500 OK');
        
        $this->mockedRequest->expects($this->at(0))
                            ->method('setUrl')
                            ->with($this->equalTo('http://localhost:8080/solr/admin/ping'))
                            ->will($this->returnValue($this->mockedRequest));
        $this->mockedRequest->expects($this->at(3))
                            ->method('setMethod')
                            ->with($this->equalTo(HTTP_Request2::METHOD_GET))
                            ->will($this->returnValue($this->mockedRequest));
        $this->mockedRequest->expects($this->at(4))
                            ->method('send')
                            ->will($this->returnValue($response));

        $this->mockedClient->sendRequest('/admin/ping');
    }
    
    /**
     * Tests a successful POST request.
     */
    public function testSuccessfulCommit () {
        $response = new HTTP_Request2_Response('HTTP/1.0 200 OK');
        
        $this->mockedRequest->expects($this->at(0))
                            ->method('setUrl')
                            ->with($this->equalTo('http://localhost:8080/solr/update'))
                            ->will($this->returnValue($this->mockedRequest));
        $this->mockedRequest->expects($this->at(3))
                            ->method('setMethod')
                            ->with($this->equalTo(HTTP_Request2::METHOD_POST))
                            ->will($this->returnValue($this->mockedRequest));
        $this->mockedRequest->expects($this->at(4))
                            ->method('setHeader')
                            ->with(
                                $this->equalTo('Content-Type'),
                                $this->equalTo('text/xml')
                            )
                            ->will($this->returnValue($this->mockedRequest));
        $this->mockedRequest->expects($this->at(5))
                            ->method('setBody')
                            ->with($this->equalTo('<?xml version="1.0" encoding="utf-8" ?><commit />'))
                            ->will($this->returnValue($this->mockedRequest));
        $this->mockedRequest->expects($this->at(6))
                            ->method('send')
                            ->will($this->returnValue($response));

        $this->assertSame($response, $this->mockedClient->sendRequest('/update', '<?xml version="1.0" encoding="utf-8" ?><commit />'));
    }
}

/**
 * A mock class for {@link SolrHttpClientTest}.
 * 
 * @package php_solr
 * @subpackage testcases
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 */
class SolrHttpClientMock extends SolrHttpClient {
	/**
	 * A mocked HTTP_Request2.
	 * 
	 * @var HTTP_Request2
	 */
	private $mock;
	
	/**
	 * Sets the mock object.
	 * 
	 * @param HTTP_Request2 $mock
	 */
	public function setMock (HTTP_Request2 $mock) {
		$this->mock = $mock;
	}
	
	/**
	 * Returns the mocked HTTP_Request2 object.
	 * 
	 * @return HTTP_Request2
	 */
	protected function createRequest() {
		return $this->mock;
	}
}

?>