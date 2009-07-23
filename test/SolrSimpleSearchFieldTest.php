<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../lib/SwiSolr.php';

SwiSolr::autoload('SolrSimpleSearchField');
/**
 * Description of SwiSolrConnectionTest
 *
 * @author ashabul yeameen
 */
class SolrSimpleSearchFieldTest extends PHPUnit_Framework_Testcase {
    private $validFieldName;
    private $validFieldValue;
    private $validBoost;

    private $invalidFieldName;
    private $invalidFieldValue;
    private $invalidBoost;

    function setUp () {
        $this->validFieldName = 'content';
        $this->validFieldValue = 'blog';
        $this->validBoost = 3.0;
        
        $this->invalidFieldName = 123;
        $this->invalidFieldValue = 123;
        $this->invalidBoost = 'blog';
    }

    /**
     * successful initialization with valid values
     */
    public function testInitializationWithValidValues() {
        $searchField = new SolrSimpleSearchField($this->validFieldName, $this->validFieldValue, $this->validBoost);

        $this->assertType('SolrSimpleSearchField', $searchField);
        $this->assertEquals('"content:blog"^3', $searchField->toQuery());
    }

    /**
     * successful initialization with partial values
     */
    public function testInitializationWithPartialValues() {
        // setting only field name
        $searchField = new SolrSimpleSearchField('content');
        $this->assertEquals("content:*", $searchField->toQuery());

        // setting field name and value
        $searchField = new SolrSimpleSearchField('content', 'blog');
        $this->assertEquals("content:blog", $searchField->toQuery());
        
    }

    public function testFailureOnInvalidFieldName() {
        $this->setExpectedException('InvalidArgumentException');
        $searchField = new SolrSimpleSearchField($this->invalidFieldName, $this->validFieldValue);
    }
    
    public function testFailureOnInvalidFieldValue() {
        $this->setExpectedException('InvalidArgumentException');
        $searchField = new SolrSimpleSearchField($this->validFieldName, $this->invalidFieldValue);
    }

    public function testFailureOnInvalidBoost() {
        $this->setExpectedException('InvalidArgumentException');
        $searchField = new SolrSimpleSearchField($this->validFieldName, $this->validFieldValue, $this->invalidBoost);
    }
}
?>
