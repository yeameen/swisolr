<?php
/**
 * The class {@link Solr}.
 * 
 * Copyright (c) 2008, 2009, Alexander M. Turek
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * - Neither the name of the author nor the names of the contributors may be
 *   used to endorse or promote products derived from this software without
 *   specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 * 
 * @package php_solr
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 * @copyright 2008, 2009, Alexander M. Turek
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version $Id$
 */

/**
 * Solr base class.
 * 
 * To open a new connection, simply call {@link connect}. 
 * 
 * @package php_solr
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 */
abstract class Solr {
    /**
     * The version of this library.
     *
     * The version information is sent along with the user agent header when
     * querying Solr.
     */
    const VERSION = '0.4.1';

    /**
     * This hash map contains the location of all classes and interfaces.
     * 
     * @var array
     */
    private static $classMap = array(
        'Solr'                      => 'Solr.php',
        'SolrConnection'            => 'transport/SolrConnection.php',
        'SolrDateField'             => 'indexer/SolrDateField.php',
        'SolrDocument'              => 'interfaces/SolrDocument.php',
        'SolrException'             => 'SolrException.php',
        'SolrFacetCounts'           => 'search/SolrFacetCounts.php',
        'SolrFacetFieldParameters'  => 'search/SolrFacetFieldParameters.php',
        'SolrFacetField'            => 'search/SolrFacetField.php',
        'SolrField'                 => 'interfaces/SolrField.php',
        'SolrHit'                   => 'search/SolrHit.php',
        'SolrHttpClient'            => 'transport/SolrHttpClient.php',
        'SolrQuery'                 => 'search/SolrQuery.php',
        'SolrSearchResult'          => 'search/SolrSearchResult.php',
        'SolrSimpleDocument'        => 'indexer/SolrSimpleDocument.php',
        'SolrSimpleFacets'          => 'search/SolrSimpleFacets.php',
        'SolrSimpleField'           => 'indexer/SolrSimpleField.php'
    );

    /**
     * An __autoload() implementation for this library.
     * 
     * @param string $class Name of the class to load.
     * 
     * @since 0.3.0
     */
    public static function autoload ($class) {
    	if (!is_string($class)) {
    	    throw new InvalidArgumentException();
    	}
    	if (array_key_exists($class, self::$classMap)) {
    	    require_once dirname(__FILE__) . '/' . self::$classMap[$class]; 
    	}
    }
    
    /**
     * Opens a new Solr connection.
     *
     * @param string $url The URL for Solr queries.
     * @return SolrConnection
     * 
     * @throws SolrException
     */
    public static function connect ($url = 'http://localhost:8983/solr') {
        self::autoload('SolrConnection');
    	return SolrConnection::connect($url);
    }
}

?>
