<?php
/**
 * The class {@link SolrSimpleField}.
 * 
 * Copyright (c) 2008, Alexander M. Turek
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
 * @copyright 2008, Alexander M. Turek
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version $Id$
 */

Solr::autoload('SolrField');

/**
 * A Solr Field.
 * 
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 * @package php_solr
 * @subpackage indexer
 */
class SolrSimpleField implements SolrField {
	/**
	 * The name of the field.
	 *
	 * @var string
	 */
	private $name = null;
	
	/**
	 * The value of the field.
	 *
	 * @var integer
	 */
	private $value = null;
	
	/**
	 * Boost value for this field.
	 *
	 * @var float
	 */
	private $boost = null;
	
    /**
     * Creates a new Solr Field.
     *
     * @param string $name  The name of the field.
     * @param string $value The field value.
     * @param float  $boost Boost setting (optional).
     */
	public function __construct ($name, $value, $boost = null) {
        if (!is_string($name)) {
            throw new InvalidArgumentException('Invalid field name.');    	
        }
        $this->name = $name;
        if (!is_string($value)) {
            throw new InvalidArgumentException('Invalid field value.');
        }
        $this->value = $value;
	    if (!is_null($boost)) {
	        if (!is_numeric($boost)) {
	            throw new InvalidArgumentException('Invalid boost value.');
	        }
	        $this->boost = floatval($boost);
        }
    }
    
    /**
     * Returns the name of the field.
     *
     * @return string
     */
    public function getName () {
    	return $this->name;
    }
    
    /**
     * Returns the value of the field.
     *
     * @return string
     */
    public function getValue () {
    	return $this->value;
    }
    
    /**
     * Returns the boost setting for this field.
     * 
     * If the default boost shall be used for this field, null is returned.
     *
     * @return float
     */
    public function getBoost () {
    	return $this->boost;
    }
}

?>