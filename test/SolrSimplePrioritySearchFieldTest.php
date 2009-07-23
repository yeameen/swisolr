<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../lib/SwiSolr.php';

SwiSolr::autoload('SolrSimplePrioritySearchField');

/**
 * Description of SolrSimplePrioritySearchFieldTest
 *
 * @author developer
 */
class SolrSimplePrioritySearchFieldTest extends PHPUnit_Framework_Testcase {
    var $searchKeyword;

    function setUp() {
        $this->searchKeyword = "blog";
    }

    function testSuccessfulInitialization() {
        $this->searchKeyword = 'blog';

        $searchField = new SolrSimplePrioritySearchField($this->searchKeyword);
        $this->assertEquals($this->searchKeyword, $searchField->getKeyword());
    }

    function testQueryBuildingAccordingToPriority() {
        $this->searchKeyword = 'blog';
        $expecedQuery = 'title:blog^10.0 content:blog^1.0 ';

        $searchField = new SolrSimplePrioritySearchField($this->searchKeyword);
        $this->assertEquals($expecedQuery, $searchField->toQuery());
    }
}
?>
