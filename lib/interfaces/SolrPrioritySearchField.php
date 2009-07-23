<?php
/**
 * Interface for priority search field. Concrete implementation will receive a string
 * and return the query depending on the options defined in Configuration or passed
 * as parameter
 *
 * @author ashabul yeameen
 * @since 17 June, 2009
 */
interface SolrPrioritySearchField {

    /**
     * returns query in lucene format
     * @return string
     */
    public function toQuery();

    /**
     * returns the keyword
     * @return string
     */
    public function getKeyword();
}
?>
