<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) . '/phpsolr/src/Solr.php';
/**
 * Description of SwiSolr
 *
 * @author developer
 */
abstract class SwiSolr extends Solr {

    /**
     * this is a hashmap of all the classes location with
     * their calssname. this stores all the classes that
     * parent Solr classes hasn't implemented
     *
     * @var array
     */
    private static $classMap = array(
        'SolrCurrencyField'             => 'indexer/SolrCurrencyField.php',
        'SwiSolrConnection'             => 'transport/SwiSolrConnection.php',
        'SwiSolrQuery'                  => 'search/SwiSolrQuery.php',
        'SolrSearchField'               => 'interfaces/SolrSearchField.php',
        'SolrSimpleSearchField'         => 'search/SolrSimpleSearchField.php',
        'SolrConfiguration'             => 'config/Configuration.php',
        'SolrPrioritySearchField'       => 'interfaces/SolrPrioritySearchField.php',
        'SolrSimplePrioritySearchField' => 'search/SolrSimplePrioritySearchField.php'
    );

    /**
     * this will load the file containing the class
     *
     * @param string $className
     */
    public static function autoload ($className) {

        if (!is_string($className)) {
    	    throw new InvalidArgumentException();
    	}
        if (array_key_exists($className, self::$classMap)) {
            require_once  dirname(__FILE__) . '/' . self::$classMap[$className];
        } else {
            parent::autoload($className);
        }
    }

    /**
     *
     * @param string $url
     * @return SolrConnection
     */
    public static function connect ($url = null) {
        self::autoload('SolrConnection');
        if ($url == null) {
            self::autoload('SolrConfiguration');
            $url = Configuration::getServerUrl();
        }
        return parent::connect($url);
    }
}
?>
