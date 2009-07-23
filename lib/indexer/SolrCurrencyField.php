<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Solr::autoload('SolrField');
/**
 * Description of SolrCurrencyField
 *
 * @author chowdhury ashabul yeameen
 * @since 28May 2009
 */
class SolrCurrencyField implements SolrField {
    //put your code here

    /**
     * the name of the field
     *
     * @var string
     */
    private $name;

    /**
     * the value of the field
     *
     * @var double
     */
    private $value;

    /**
     * the boost factor of the field
     *
     * @var float
     */
    private $boost;

    /**
     *
     * @param string $name
     * @param double $value
     * @param float $boost 
     */
    public function __construct($name, $value, $boost = null) {
        if (!is_string($name)) {
            throw new InvalidArgumentException('Invalid field name.');
        }

        // remove commas, if exist
        $normalizedValue = $this->normalizeValue($value);
        
        if (!is_numeric($normalizedValue)) {
            throw new InvalidArgumentException("Invalid field value for currency field");
        }

        $this->name = $name;
        $this->value = (double)$normalizedValue;
        $this->boost = $boost;
    }

    /**
     * Returns the name of this field.
     *
     * @return string
     */
    public function getName () {
        return $this->name;
    }

    /**
     * Returns the value of this field as string.
     *
     * @return double
     */

    public function getValue () {
        return $this->value;
    }

    /**
     * Returns the boost factor for this field.
     *
     * @return float
     */
    public function getBoost () {
        return $this->boost;
    }

    /**
     * removes all commas from the parameter value
     *
     * @param mixed $value
     * @return string
     */
    protected function normalizeValue($value) {
        $strValue = (string)$value;
        return preg_replace("/,/", "", $strValue);
    }
}
?>
