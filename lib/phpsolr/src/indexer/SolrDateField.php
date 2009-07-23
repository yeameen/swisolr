<?php
/**
 * The class {@link SolrDateField}.
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
 * A date field that can be indexed by Solr.
 *
 * @package php_solr
 * @subpackage indexer
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 * @since 0.2.2
 */
class SolrDateField implements SolrField {
    /**
     * The date format expected by Solr.
     */
    const DATE_FORMAT = 'Y-m-d\TH:i:s\Z';

    /**
     * The name of the field.
     *
     * @var string
     */
    private $name;
    
    /**
     * The date value of this date field in Solr's preferred format.
     *
     * @var string
     */
    private $value;
    
    /**
     * The boost factor for this field.
     *
     * @return float
     */
    private $boost;

    /**
     * Constructor for {@link SolrDateField}.
     *
     * @param string  $name      The name of this field.
     * @param integer $timestamp The value of this date field as Unix time stamp.
     * @param float   $boost     The boost factor for this field (optional).
     */
    public function __construct ($name, $timestamp, $boost = null) {
        if (!is_string($name)) {
            throw new InvalidArgumentException('Invalid field name.');     
        }
        $this->name = $name;
        if (!is_numeric($timestamp)) {
            throw new InvalidArgumentException('Invalid timestamp.');
        }
        $timezone = date_default_timezone_get();
        // Solr expects UTC time.
        date_default_timezone_set('UTC');
        $this->value = date(self::DATE_FORMAT, intval($timestamp));
        // Restoring previous timezone settings.
        date_default_timezone_set($timezone);
        if (!is_null($boost)) {
            if (!is_numeric($boost)) {
                throw new InvalidArgumentException('Invalid boost value.');
            }
            $this->boost = floatval($boost);
        }
    }

    /**
     * Returns the name of this field.
     *
     * @return string
     */
    public function getName () {
        return $this->name;
    }
    
    /**
     * Returns the value of this field as string.
     *
     * @return string
     */
    public function getValue () {
        return $this->value;
    }

    /**
     * Returns the boost factor for this field.
     *
     * @return float
     */
    public function getBoost () {
        return $this->boost;
    }
}

?>