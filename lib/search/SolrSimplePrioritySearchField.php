<?php

SwiSolr::autoload('SolrPrioritySearchField');
SwiSolr::autoload('SolrConfiguration');

/**
 * Description of SolrSimplePriorityField
 *
 * @author ashabul yeameen
 */
class SolrSimplePrioritySearchField implements SolrPrioritySearchField {

    /**
     * The search keyword
     * @var string
     */
    protected $keyword;

    public function  __construct($keyword, $options = array()) {
        if(!is_string($keyword)) {
            throw new InvalidArgumentException('Invalid keyword');
        }
        $this->keyword = $keyword;
    }

    /**
     * return the keyword
     * @return <type>
     */
    public function getKeyword() {
        return $this->keyword;
    }

    /**
     * return search query in lucene search format
     * @return string
     */
    public function toQuery() {
        $fields = Configuration::getFieldPriorities();
        $query = NULL;
        if (!empty($fields)) {
            $query = '';
            foreach($fields as $fieldName => $priority) {
                $query .= $fieldName . ':' . $this->getKeyword() . '^' . $priority . ' ';
            }
        }
        return $query;
    }
}
?>
