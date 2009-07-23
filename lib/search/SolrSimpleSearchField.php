<?php

SwiSolr::autoload('SolrSearchField');

/**
 * Description of SwiSolrSearchField
 *
 * @author ashabul yeameen
 *
 * @since 31 May, 2009
 */
class SolrSimpleSearchField implements SolrSearchField {

    /**
     * the search field
     *
     * @var string
     */
    protected $field;

    /**
     * value of the search field
     *
     * @var string
     */
    protected $value;

    /**
     * value of the boost
     *
     * @var float
     */
    protected $boost;


    public function __construct ($field, $value = '*', $boost = 1.0) {
    	if (!is_string($field) || !is_string($value) || !is_float($boost)) {
            throw new InvalidArgumentException('Invalid field/value/boost');
    	}

    	$this->field = $field;
        $this->value = $value;
        $this->boost = (float)$boost;
    }

    /**
     * get the name of the field to be searched on
     *
     * @return string
     */
    public function getField() {
        return $this->field;
    }

    /**
     * get the search string for the field
     *
     * @return string
     */
    public function getValue() {
        return $this->value;
    }


    public function getBoost() {
        return $this->boost;
    }


    /**
     * return as lucene search query
     *
     * @return string
     */
    public function toQuery() {
        $query = $this->field . ':' . $this->value;
        if ($this->boost != 1.0) {
            $query = '"' . $query . '"^' . $this->boost;
        }
        return $query;
    }
}
?>
