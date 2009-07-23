<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author ashabul yeameen
 *
 * @since 03 Jun, 2009
 */
interface SolrSearchField {
    /**
     * returns name of the field
     *
     * @return string
     */
    public function getField();

    /**
     * returns value of the field
     *
     * @return string
     */
    public function getValue();

    /**
     * returns fields to be boost
     *
     * @return float
     */
    public function getBoost();
}
?>
