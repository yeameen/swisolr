<?php
/**
 * The class {@link SolrFacetFieldParameters}.
 * 
 * Copyright (c) 2009, Alexander M. Turek
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
 * @subpackage search
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 * @copyright 2009, Alexander M. Turek
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version $Id$
 */

Solr::autoload('SolrFacetFieldParameters');

/**
 * A facet field.
 * 
 * @see http://wiki.apache.org/solr/SimpleFacetParameters
 * 
 * @package php_solr
 * @subpackage search
 * @author Alexander M. Turek <rabus@users.sourceforge.net>
 * @since 0.4.1
 */
class SolrFacetField extends SolrFacetFieldParameters {
    /**
     * Constructor for {@link SolrFacetField}.
     * 
     * @param string $name The name of the field.
     */
    public function __construct ($fieldName) {
        if (!is_string($fieldName) || $fieldName === '') {
            throw new InvalidArgumentException();
        }
        $this->fieldName = $fieldName;
    }
    
    /**
     * Returns the faceting part of the HTTP query.
     * 
     * @uses urlencode()
     * 
     * @param string $solrSpecVersion The servers specification version (optional).
     * 
     * @return string
     */
    public function getQueryStringPart ($solrSpecVersion = null) {
        return $this->buildQueryParameter('field', $this->fieldName, false)
             . parent::getQueryStringPart($solrSpecVersion);
    }
    
    /**
     * Returns the name of this field.
     * 
     * @return string
     */
    public function getName () {
        return $this->fieldName;
    }
}

?>