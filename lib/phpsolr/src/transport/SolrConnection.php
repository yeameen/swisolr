<?php
/**
 * The class {@link SolrConnection}.
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
 * @subpackage transport
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 * @copyright 2008, 2009, Alexander M. Turek
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version $Id$
 */

Solr::autoload('SolrHttpClient');

/**
 * A Solr Connection.
 * 
 * To open a new connection, simply call {@link Solr::connect()}.
 * 
 * @package php_solr
 * @subpackage transport
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 * @uses XMLWriter 
 */
class SolrConnection {
    /**
     * The HTTP Client.
     *
     * @var SolrHttpClient
     */
    protected $httpClient = null;
    
    /**
     * The specification version of the Solr server.
     * 
     * @var string
     */
    private $solrSpecVersion = null;
    
    /**
     * Constructor for {@link Solr}.
     */
    private function __construct() {}

    /**
     * Opens a new Solr connection.
     *
     * @param string $url The URL for Solr queries.
     * @return Solr
     * 
     * @throws SolrException
     */
    public static function connect ($url = 'http://localhost:8983/solr') {
        $solr = new self();
        $solr->httpClient = new SolrHttpClient($url);
        
        if (!$solr->ping()) {
            Solr::autoload('SolrException');
            throw new SolrException('Unable to connect to Solr.');
        }
        
        return $solr;
    }

    /**
     * Adds one or multiple documents to the index.
     * 
     * Example 1: Adding a singe document to the Solr index.
     * 
     * <code>
     * require_once 'php_solr/Solr.php';
     * Solr::autoload('SolrSimpleDocument');
     * Solr::autoload('SolrSimpleField');
     * 
     * $doc = new SolrSimpleDocument(array(
     *     new SolrSimpleField('id', '42'),
     *     new SolrSimpleField('name', 'Arthur Dent')
     * ));
     * $solr = Solr::connect();
     * $solr->add($doc);
     * $solr->commit();
     * </code>
     * 
     * Example 2: Adding two documents with a single command.
     * 
     * <code>
     * require_once 'php_solr/Solr.php';
     * Solr::autoload('SolrSimpleDocument');
     * Solr::autoload('SolrSimpleField');
     * 
     * $doc1 = new SolrSimpleDocument(array(
     *     new SolrSimpleField('id', '42'),
     *     new SolrSimpleField('name', 'Arthur Dent')
     * ));
     * $doc2 = new SolrSimpleDocument(array(
     *     new SolrSimpleField('id', '24'),
     *     new SolrSimpleField('name', 'Marvin')
     * ));
     * $solr = Solr::connect();
     * $solr->add(array($doc1, $doc2));
     * $solr->commit();
     * </code>
     * 
     * {@link SolrSimpleDocument} is only a sample implementation of a Solr
     * document. If your documents are already mapped to objects, it is not
     * necessary to convert them to {@link SolrSimpleDocument} instances.
     * Instead, it is sufficient to implement {@link SolrDocument} inside
     * your classes.
     * 
     * The same applies to objects that shall be mapped to fields. All you
     * need to do is implementing {@link SolrField}.
     * 
     * <code>
     * require_once 'php_solr/Solr.php';
     * Solr::autoload('SolrDocument');
     * Solr::autoload('SolrField');
     * 
     * class MyNameClass implements SolrField {
     *     private $first_name;
     *     private $last_name;
     * 
     *     ...
     * 
     *     public function getName () {
     *         return 'name';
     *     }
     * 
     *     public function getValue () {
     *         return $this->first_name . ' ' . $this->last_name;
     *     }
     * 
     *     public function getBoost () {
     *         return null;
     *     }
     * }
     * 
     * class MyDocumentClass implements SolrDocument {
     *     private $id;
     *     private $name; // a MyNameClass object
     * 
     *     ...
     * 
     *     public function getFields () {
     *         return array(
     *             new SolrSimpleField('id', $this->id),
     *             $name
     *         );
     *     }
     * 
     *     public function getBoost () {
     *         return null;
     *     }
     * }
     * </code>
     *
     * @param SolrDocument|array(SolrDocument) $docs The document(s).
     * 
     * @throws SolrException
     */
    public function add ($docs) {
        if ($docs instanceof SolrDocument) {
            $docs = array($docs);
        } elseif (!is_array($docs)) {
            throw new SolrException('Invalid document or documents array.');
        }
        
        $xmlWriter = new XMLWriter();
        $xmlWriter->openMemory();
        $xmlWriter->startDocument('1.0', 'UTF-8');
        $xmlWriter->startElement('add'); // <add>
        
        foreach ($docs as $currentDocument) {
            if (!($currentDocument instanceof SolrDocument )) {
                Solr::autoload('SolrException');
                throw new SolrException('Invalid document or documents array.');
            }
        
            $xmlWriter->startElement('doc'); // <doc>
            if (!is_null($currentDocument->getBoost())) {
                $xmlWriter->writeAttribute('boost', $currentDocument->getBoost());
            }
            foreach ($currentDocument->getFields() as $currentField) {
                $xmlWriter->startElement('field'); // <field>
                $xmlWriter->writeAttribute('name', $currentField->getName());
                if (!is_null($currentField->getBoost())) {
                    $xmlWriter->writeAttribute('boost', $currentField->getBoost());
                }
                $xmlWriter->text($currentField->getValue());
                $xmlWriter->endElement(); // </field>
            }
            $xmlWriter->endElement(); // </doc>
        }

        $xmlWriter->endElement(); // </add>
        $xmlWriter->endDocument();
        $this->httpClient->sendRequest('/update', $xmlWriter->outputMemory());
    }
    
    /**
     * Deletes the document with the given ID.
     * 
     * @param string $id The ID of the document.
     * 
     * @since 0.4.1
     */
    public function deleteById ($id) {
        if (!is_string($id)) {
            throw new InvalidArgumentException('The ID was expected to be given as string.');
        }
        $xmlWriter = new XMLWriter();
        $xmlWriter->openMemory();
        $xmlWriter->startDocument('1.0', 'UTF-8');
        $xmlWriter->startElement('delete'); // <delete>
        $xmlWriter->startElement('id'); // <id>
        $xmlWriter->text($id);
        $xmlWriter->endElement(); // </id>
        $xmlWriter->endElement(); // </delete>
        $xmlWriter->endDocument();

        $this->httpClient->sendRequest('/update', $xmlWriter->outputMemory());
    }
    
    /**
     * Deletes the document Which match the given query.
     * 
     * @param string $query The query.
     * 
     * @since 0.4.1
     */
    public function deleteByQuery ($query) {
        if (!is_string($query)) {
            throw new InvalidArgumentException('The ID was expected to be given as string.');
        }
        $xmlWriter = new XMLWriter();
        $xmlWriter->openMemory();
        $xmlWriter->startDocument('1.0', 'UTF-8');
        $xmlWriter->startElement('delete'); // <delete>
        $xmlWriter->startElement('query'); // <query>
        $xmlWriter->text($query);
        $xmlWriter->endElement(); // </query>
        $xmlWriter->endElement(); // </delete>
        $xmlWriter->endDocument();

        $this->httpClient->sendRequest('/update', $xmlWriter->outputMemory());
    }

    /**
     * Sends a query to the Solr server.
     * 
     * Although it is recommended to provide the query as a {@link SolrQuery}
     * object, it is also possible to pass it as a string. So, the following
     * two lines of code produce exactly the same result:
     * 
     * <code>
     * $result = $solr->query('hello world');
     * $result = $solr->query(new SolrQuery('hello world'));
     * </code>
     * 
     * Please refer to the {@link SolrQuery} class for more details on query
     * parameters and the query syntax.
     * 
     * @see SolrQuery
     * @uses json_decode()
     * 
     * @param SolrQuery|string $query The query.
     * 
     * @return SolrSearchResult
     */
    public function query ($query) {
        Solr::autoload('SolrSearchResult');
        
        if (is_string($query)) {
            Solr::autoload('SolrQuery');
            $query = new SolrQuery($query);
        } elseif (!($query instanceof SolrQuery)) {
            throw new InvalidArgumentException('Invalid query.');
        }
        
        $response = $this->httpClient->sendRequest('/select' . $query->getQueryString());
        
        return SolrSearchResult::parseResponse($response->getBody());
    }

    /**
     * Commits all recently added documents.
     *
     * @throws SolrException
     */
    public function commit () {
        $xmlWriter = new XMLWriter();
        $xmlWriter->openMemory();
        $xmlWriter->startDocument('1.0', 'UTF-8');
        $xmlWriter->writeElement('commit'); // <commit />
        $xmlWriter->endDocument();
        
        $this->httpClient->sendRequest('/update', $xmlWriter->outputMemory());
    }
    
    /**
     * Optimizes the index.
     *
     * @throws SolrException
     */
    public function optimize () {
        $xmlWriter = new XMLWriter();
        $xmlWriter->openMemory();
        $xmlWriter->startDocument('1.0', 'UTF-8');
        $xmlWriter->writeElement('optimize'); // <optimize />
        $xmlWriter->endDocument();
        
        $this->httpClient->sendRequest('/update', $xmlWriter->outputMemory());
    }
    
    /**
     * Checks if the server is accesable.
     * 
     * @since 0.2.1
     * @return boolean
     */
    public function ping () {
        try {
            $this->httpClient->sendRequest('/admin/ping');
        } catch (HTTP_Request2_Exception $e) {
            return false;
        } catch (SolrException $e) {
        	return false;
        }

    	return true;
    }

    /**
     * Returns the server's Solr specification version.
     * 
     * Comparing Solr versions can be done with {@link version_compare}.
     * 
     * <code>
     * $solr = Solr::connect();
     * 
     * echo $solr->getSolrSpecVersion() . "\n";
     * 
     * if (version_compare($solr->getSolrSpecVersion(), '1.3', '<')) {
     *     echo 'The server version is older than 1.3.' . "\n";
     * }
     * if (version_compare($solr->getSolrSpecVersion(), '1.2', '>=')) {
     *     echo 'We have at least Solr 1.2 running.' . "\n";
     * }
     * </code>
     * 
     * If you run the example script above against Solr 1.2, you will get the
     * following output:
     * 
     * <pre>
     * 1.2.2008.03.21.05.21.15
     * The server version is older than 1.3.
     * We have at least Solr 1.2 running.
     * </pre>
     * 
     * The information is cached afterwards, so calling this function multiple
     * times in a row is safe.
     * 
     * @return string
     * 
     * @uses json_decode()
     * @since 0.4.0
     */
    public function getSolrSpecVersion () {
        if (is_null($this->solrSpecVersion)) {
            $this->fetchServerInfo();
        }
        if (is_null($this->solrSpecVersion)) {
            throw new RuntimeException('Unable to fetch version information.');
        }
        return $this->solrSpecVersion;
    }
    
    /**
     * Collects useful system information about the Solr server.
     */
    private function fetchServerInfo () {
    	$response = $this->httpClient->sendRequest('/admin/system/?wt=json');
        $systemInfo = json_decode($response->getBody());
        $this->solrSpecVersion = $systemInfo->lucene->{'solr-spec-version'};    	
    }

    /**
     * Creates an instance with the specified state.
     * 
     * This method is meant to be used in unit tests. Don't use it for other
     * purposes.
     *
     * @ignore
     * @param array $vars
     * @return SolrConnection
     */
    public static function __set_state (array $vars) {
        $solr = new self();
        $properties = array('httpClient');
        
        foreach ($properties as $currentProperty) {
            if (array_key_exists($currentProperty, $vars)) {
                $solr->$currentProperty = $vars[$currentProperty];
            }
        }

        return $solr;
    }
}

?>