<?php
/**
 * Testsuite for all unit tests.
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
require_once dirname(__FILE__) . '/SolrAddCommandTest.php';
require_once dirname(__FILE__) . '/SolrDateFieldTest.php';
require_once dirname(__FILE__) . '/SolrFacetFieldTest.php';
require_once dirname(__FILE__) . '/SolrHitTest.php';
require_once dirname(__FILE__) . '/SolrHttpClientTest.php';
require_once dirname(__FILE__) . '/SolrQueryCommandTest.php';
require_once dirname(__FILE__) . '/SolrQueryTest.php';
require_once dirname(__FILE__) . '/SolrSearchResultTest.php';
require_once dirname(__FILE__) . '/SolrSimpleCommandsTest.php';
require_once dirname(__FILE__) . '/SolrSimpleDocumentTest.php';
require_once dirname(__FILE__) . '/SolrSimpleFacetsTest.php';
require_once dirname(__FILE__) . '/SolrSimpleFieldTest.php';

/**
 * Runs all tests.
 * 
 * @package php_solr
 * @subpackage testcases
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 */
class AllSolrTests extends PHPUnit_Framework_TestSuite {
    /**
     * Constructs the test suite handler.
     */
    public function __construct() {
        $this->setName('AllSolrTests');
        $this->addTestSuite('SolrAddCommandTest');
        $this->addTestSuite('SolrDateFieldTest');
        $this->addTestSuite('SolrFacetFieldTest');
        $this->addTestSuite('SolrHitTest');
        $this->addTestSuite('SolrHttpClientTest');
        $this->addTestSuite('SolrQueryCommandTest');
        $this->addTestSuite('SolrQueryTest');
        $this->addTestSuite('SolrSearchResultTest');
        $this->addTestSuite('SolrSimpleCommandsTest');
        $this->addTestSuite('SolrSimpleDocumentTest');
        $this->addTestSuite('SolrSimpleFacetsTest');
        $this->addTestSuite('SolrSimpleFieldTest');
    }

    /**
     * Creates the suite.
     */
    public static function suite() {
        return new self();
    }
}

