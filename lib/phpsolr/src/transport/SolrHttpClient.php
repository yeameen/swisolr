<?php
/**
 * The class {@link SolrHttpClient}.
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
 * @subpackage transport
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 * @copyright 2009, Alexander M. Turek
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version $Id$
 */

/**
 * PEAR::HTTP_Request2
 */
require_once 'HTTP/Request2.php';
require_once 'HTTP/Request2/Response.php';

/**
 * A simple HTTP client for php_solr.
 * 
 * @package php_solr
 * @subpackage transport
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 * @uses HTTP_Request2
 */
class SolrHttpClient {
    /**
     * The base URL for Solr requests.
     * 
     * @var string
     */
    private $solrBaseUrl;

    /**
     * Constructor for {@link SolrHttpClient}.
     * 
     * @param string $solrBaseUrl The base URL for Solr requests.
     */
    public function __construct ($solrBaseUrl = 'http://localhost:8080/solr') {
        if (!is_string($solrBaseUrl)) {
            throw new InvalidArgumentException('Invalid base URL.');
        }
        $this->solrBaseUrl = $solrBaseUrl;
    }

    /**
     * Sends an HTTP request.
     * 
     * @param string $path The relative path for this request.
     * @param string $body The XML code for the request body.
     * 
     * @return HTTP_Request2_Response
     */
    public function sendRequest ($path, $body = null) {
        $request = $this->createRequest();
        $request->setUrl($this->solrBaseUrl . $path)
                ->setAdapter('HTTP_Request2_Adapter_Curl')
                ->setHeader('User-Agent', 'php_solr/' . Solr::VERSION);
        if (!is_null($body)) {
            $request->setMethod(HTTP_Request2::METHOD_POST)
                    ->setHeader('Content-Type', 'text/xml')
                    ->setBody($body);
        } else {
            $request->setMethod(HTTP_Request2::METHOD_GET);
        }

        $response = $request->send();
        if (!($response instanceof HTTP_Request2_Response)) {
        	throw new RuntimeException('Invalid response.');
        }
        if ($response->getStatus() != 200) {
            Solr::autoload('SolrException');
            throw new SolrException(
               'Solr returned HTTP status ' . $response->getStatus() . '.'
            );
        }
        
        return $response;
    }

    /**
     * Creates the HTTP_Request2 instance.
     * 
     * @return HTTP_Request2
     */
    protected function createRequest () {
    	return new HTTP_Request2();
    }
}

?>