<?php
/**
 * Description of SwiSolrQuery
 *
 * @author ashabul yeameen
 * @since 31 May, 2009
 */


Solr::autoload('SolrQuery');

class SwiSolrQuery extends SolrQuery {

  private $searchFields;
  private $sortField = null;

  function __construct($param = null) {
      if (is_array($param)) {
        $this->searchFields = array();
        foreach ($param as $searchField) {
            if (!($searchField instanceof SolrSearchField)) {
                throw InvalidArgumentException('Invalid search field for the search');
            }
            $this->searchFields[] = $searchField;
        }
      }else if ($param instanceof SolrPrioritySearchField) {
          $this->searchFields = null;
          parent::__construct($param->toQuery());
      } else if (is_string($param)) {
          $this->searchFields = null;
          parent::__construct($param);
      } else {
          throw InvalidArgumentException('Invalid parameter for SwiSolrQuery');
      }
  }

  /**
   * returns the query
   * 
   * @return string
   */
  public function getQuery() {
    // if no search field
    if($this->searchFields == null) {
        return parent::getQuery();
    }
    $queryStrings = array();
    foreach($this->searchFields as $searchField) {
      $queryStrings[] = $searchField->toQuery();
    }
    return join(' AND ', $queryStrings);
  }

  /**
   *
   * @param SwiSolrSearchField $searchField
   */
  public function addSearchField($searchField) {
    $this->searchFields[] = $searchField;
  }

    /**
   * @param <string> $solrSpecVerion
   * @return <string>
   */
  public function getQueryString($solrSpecVerion = null) {
    $queryString = parent::getQueryString($solrSpecVersion);
    if (!is_null($this->sortField)) {
      $queryString .= '&sort=' . urlencode($this->sortField['fieldName'] . ' ' . $this->sortField['order']);
    }
    return $queryString;
  }

  /**
   * add sort field
   */
  public function setSortField($fieldName, $order = 'desc') {
    $this->sortField['fieldName'] = $fieldName;
    $this->sortField['order'] = ($order == 'desc' || $order == 'asc')? $order : $order;
  }

}
?>
