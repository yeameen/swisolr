<?php
/**
 * Test case {@link SolrQueryCommandTest}.
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
Solr::autoload('SolrConnection');
Solr::autoload('SolrHttpClient');
Solr::autoload('SolrSearchResult');

/**
 *  Test case for {@link SolrConnection::query()}.
 * 
 * @package php_solr
 * @subpackage testcases
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 */
class SolrQueryCommandTest extends PHPUnit_Framework_TestCase {
    /**
     * A Solr instance with a mocked HTTP_Client 
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
     * Sets up the mock objects.
     */
    function setUp () {
        $this->httpClientMock = $this->getMock('SolrHttpClient', array('sendRequest'));
        $this->solr = SolrConnection::__set_state(array(
            'httpClient' => $this->httpClientMock
        ));
    }
	
    /**
     * Tests the returned result of {@link Solr::query()}.
     */
    function testQuery () {
    	$response = new HTTP_Request2_Response('HTTP/1.0 200 OK');
    	$solrResponse = '{"responseHeader":{"status":0,"QTime":0,"params":{"q":"php","qt":"standard","wt":"json"}},"response":{"numFound":1,"start":0,"docs":[{"id":42,"title":"PHP 5"}]}}';
    	$response->appendBody($solrResponse);
    	
        $this->httpClientMock
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->equalTo('/select?qt=standard&wt=json&q=php'))
            ->will($this->returnValue($response));

        $this->assertEquals(
            SolrSearchResult::parseResponse($solrResponse),
            $this->solr->query('php')
        );
    }
    
    /**
     * Tests invalid arguments.
     * 
     * @param $param The argument to test.
     * 
     * @expectedException InvalidArgumentException
     * @dataProvider provideInvalidArguments
     */
    function testInvalidArguments ($param) {
    	$this->solr->query($param);
    }
    
    /**
     * Data provider for {@link testInvalidArguments}.
     * 
     * @return array
     */
    function provideInvalidArguments () {
        return array(
            array(array()),
            array(null)
        );
    }
}

?>