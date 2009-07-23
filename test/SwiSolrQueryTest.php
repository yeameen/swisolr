<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../lib/SwiSolr.php';

SwiSolr::autoload('SwiSolrQuery');
SwiSolr::autoload('SolrSimpleSearchField');

/**
 * Description of SwiSolrQueryTest
 *
 * @author developer
 */
class SwiSolrQueryTest extends PHPUnit_Framework_Testcase {

    private $queryString;

    function setUp() {
        $this->queryString = 'blog';
    }

    /**
     * test successful initialization by string
     */
    public function testInitilizationWithString() {
        $query = new SwiSolrQuery($this->queryString);
        $this->assertType('SolrQuery', $query);
        $this->assertType('SwiSolrQuery', $query);
        $this->assertEquals($this->queryString, $query->getQuery());
    }

    /**
     * test successful initialization by SolrSearchField objects
     */
    public function testSuccessfulInitializationWithSolrSearchField() {
        $searchFields = array(
            new SolrSimpleSearchField('title', 'blog'),
            new SolrSimpleSearchField('content', 'blog')
        );
        $queryString = "title:blog AND content:blog";
        $query = new SwiSolrQuery($searchFields);
        $this->assertEquals($queryString, $query->getQuery());
    }

    public function testInitializationWithSolrPrioritySearchField() {
        $searchField = new SolrSimplePrioritySearchField('blog');
        $expecedQuery = 'title:blog^10.0 content:blog^1.0 ';

        $query = new SwiSolrQuery($searchField);
        $this->assertEquals($expecedQuery, $query->getQuery());
    }
}
?>
