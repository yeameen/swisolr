<?php
/**
 * The class {@link SolrSimpleDocument}.
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
 * @subpackage indexer
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 * @copyright 2008, 2009, Alexander M. Turek
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version $Id$
 */

Solr::autoload('SolrDocument');

/**
 * A Solr Document.
 * 
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 * @package php_solr
 * @subpackage indexer
 */
class SolrSimpleDocument implements SolrDocument {
	/**
	 * The fields of this document.
	 *
	 * @var array(SolrField)
	 */
    private $fields = array();
    
    /**
     * Boost value for this field.
     *
     * @var float
     */
    private $boost = null;
    
    /**
     * Creates a new Solr document.
     *
     * @param array(SolrField) $fields The fields of the document.
     * @param float            $boost  The boost value for this document (optional).
     */
    public function __construct (array $fields = array(), $boost = null) {
        foreach ($fields as $currentField) {
        	if (!($currentField instanceof SolrField)) {
        		throw new InvalidArgumentException('Invalid document field.');
        	}
        	$this->fields[] = $currentField;
        }
        if (!is_null($boost)) {
        	if (!is_numeric($boost)) {
        	    throw new InvalidArgumentException('Invalid boost value.');
        	}
        	$this->boost = floatval($boost);
        }
    }
    
    /**
     * Adds a field
     *
     * @param SolrField $field
     */
    public function addField (SolrField $field) {
    	$this->fields[] = $field;
    }
    
    /**
     * Returns the fields of this document.
     *
     * @return array(SolrField)
     */
    public function getFields () {
        return $this->fields;
    }
    
    /**
     * Returns the boost factor for this document.
     * 
     * If the default boost shall be used for this document, null is returned.
     *
     * @return float
     */
    public function getBoost () {
    	return $this->boost;
    }
}

?>