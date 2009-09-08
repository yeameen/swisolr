<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SwiSolrConnection
 *
 * @author yeameen
 */


SwiSolr::autoload('SolrConnection');
class SwiSolrConnection extends SolrConnection {


    public function  __construct() {
        SwiSolr::autoload('SolrDocument');
        SwiSolr::autoload('SolrConfiguration');
    }

    public static function connect ($url) {
        $solr = new self();
        $parent = parent::connect($url);
        $solr->httpClient = $parent->httpClient;
        return $solr;
    }
    /**
     * add solrdocument for indexing
     *
     * @param SolrDocument $docs
     */
    public function add ($docs) {
//        echo "executed\n\n\n\n";
        if ($docs instanceof SolrDocument) {
            $docs = array($docs);
        }
        $defaultFields = Configuration::get('default-fields');

        foreach($docs as $doc) {
            foreach ($defaultFields as $field_key => $field_value) {
                $doc->addField(new SolrSimpleField($field_key, $field_value));
            }
        }
        print_r($docs);
        return parent::add($docs);
    }
}
?>
