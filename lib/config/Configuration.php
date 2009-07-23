<?php

/**
 * Stores common configuration for a service
 *
 * @author ashabul yeameen
 * @since 4 Jun, 2009
 */
class Configuration {
    private static $defaultConfigArray = array(
        'server-address'        => 'http://127.0.0.1',
        'server-port'           => '8080',
        'server-directory'      => 'solr',
        'search-field-default'  => 'content'
    );


    private static $allowedChange = array('server-address', 'server-port', 'server-directory');
    private static $configArray = null;

    private static $fieldPrioritiesFile = 'fieldPriorities.xml';
    private static $fieldPriorities = null;

    private function  __construct() {}

    public static function get($pKey) {
        if (self::$configArray == null || empty(self::$configArray)) {
            self::$configArray = self::$defaultConfigArray;
        }
        return self::$configArray[$pKey];
    }

    public static function put($key, $value) {
        if(!in_array($key, self::$allowedChange)) {
            throw Exception('You cannot change the value');
        }
        self::$configArray[$key] = $value;
    }

    public static function getServerUrl() {
        return self::get('server-address') . ':' . self::get('server-port') . '/'
                    . self::get('server-directory');
    }

    public static function getFieldPriorities() {
        if (self::$fieldPriorities == null) {
            self::loadFieldPriorities();
        }
        return self::$fieldPriorities;
    }

    private static function loadFieldPriorities() {
        $doc = new DOMDocument();
        $doc->load( dirname(__FILE__) . '/' . self::$fieldPrioritiesFile );

        $fields = $doc->getElementsByTagName( "field" );

        foreach( $fields as $field ) {
            self::$fieldPriorities[$field->getAttribute('name')] = $field->nodeValue;
        }
    }

    public static function reset() {
        self::$configArray = null;
    }
}
?>
