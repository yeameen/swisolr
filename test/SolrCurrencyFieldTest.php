<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../lib/SwiSolr.php';

SwiSolr::autoload('SolrCurrencyField');


/**
 * Description of SolrCurrencyFieldTest
 *
 * @author chowdhury ashabul yeameen
 * @since 28 May, 2009
 */
class SolrCurrencyFieldTest extends PHPUnit_Framework_Testcase {

    private $fieldName;
    private $fieldValue;
    private $boost;

    function setUp () {
        $this->fieldName = 'fieldName';
        $this->fieldValue = 123.12;
        $this->boost = 30.4;
    }

    /**
     * check if field initialization is successful with all valied
     * values and without boost value
     */
    function testInitializationWithoutBoost() {

        // test without boost value
        $field = new SolrCurrencyField($this->fieldName, $this->fieldValue);

        // check if this is an instance of SolrField and has those values
        $this->assertType('SolrField', $field);
        $this->assertEquals($this->fieldName, $field->getName());
        $this->assertEquals($this->fieldValue, $field->getValue());
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_NUMERIC, $field->getValue());
    }

    /**
     * check if field initialization is successful with all valied
     * values and a boost value
     */
    function testInitializationWithBoost() {
        $field = new SolrCurrencyField($this->fieldName, $this->fieldValue, $this->boost);

        $this->assertType('SolrField', $field);

        $this->assertEquals($this->fieldName, $field->getName());

        $this->assertEquals($this->fieldValue, $field->getValue());
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_NUMERIC, $field->getValue());

        $this->assertEquals($this->boost, $field->getBoost());
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_NUMERIC, $field->getBoost());
    }

    function testInitializationWithNullBoost() {
        $field = new SolrCurrencyField($this->fieldName, $this->fieldValue);
        $this->assertType('SolrField', $field);

        $this->assertNull($field->getBoost());
    }

    /**
     * check if initialization fails with invalid field name
     */
    function testInitializationWithInvalidName() {
        $invalidName = 123.1;
        $this->setExpectedException('InvalidArgumentException');
        
        $field = new SolrCurrencyField($invalidName, $this->fieldValue);
    }


    /**
     * check if initialization fails with invalid field value
     */
    function testInitializationWithInvalidValue() {
       $invalidValue = "blah";
       $this->setExpectedException('InvalidArgumentException');

       $field = new SolrCurrencyField($this->fieldName, $invalidValue);
    }

    /**
     * check whether it accepts integer field value
     */
    function testInitializationWithIntegerValue() {
        $value = 123;
        $field = new SolrCurrencyField($this->fieldName, $value);
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_NUMERIC, $field->getValue());
        $this->assertEquals($value, $field->getValue());
    }

    /**
     * check if it accepts field value with comma
     */
    function testInitializationWithNumericValueWithComma() {
        $value = "12,123.50";
        $storedValue = 12123.50;
        $field = null;

       $field = new SolrCurrencyField($this->fieldName, $value, "currency should tolerant to comma in currency value field");

        // check that value stored correctly
        $this->assertEquals($storedValue, $field->getValue());
    }
}
?>
