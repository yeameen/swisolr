<?php
    require_once dirname(__FILE__) . '/lib/SwiSolr.php';

    SwiSolr::autoload('SwiSolrQuery');
    SwiSolr::autoload('SwiSolrSearchField');
    SwiSolr::autoload('SolrSimpleSearchField');

    $solr = SwiSolr::connect('http://127.0.0.1:9080/solr1');
//    $solr = SwiSolr::connect();

    /* first query with string */
//    $query = new SwiSolrQuery("title:this^5.0 OR content:blog^1.0");
//    $query->setFieldList(array('id', 'title', 'content', 'score'));
//    
//    $query->setRows(2);
//    print_r($solr);
//    $result = $solr->query($query);
//
//    echo "<br />";
//    echo 'found ' . $result->getNumFound() . ' documents total.' . "<br />\n";
//    echo 'result set contains ' . count($result) . ' documents.' . "<br />\n";
//    echo 'starting with document no. ' . $result->getStart() . '.' . "<br />\n";
//    echo "<br />\n";
//
//    $hit = $result[0];
//
//    print_r($hit);
//
//    /* second query */
//    $query = new SwiSolrQuery(array(
//            new SolrSimpleSearchField('title', 'blog'),
//            new SolrSimpleSearchField('content', 'blog')
//        ));
//    echo "The query - " . $query->getQuery() . "<br />\n";
//
//    $result = $solr->query($query);
//
//    
//    echo 'found ' . $result->getNumFound() . ' documents total.' . "<br />\n";
//    echo 'result set contains ' . count($result) . ' documents.' . "<br />\n";
//    echo 'starting with document no. ' . $result->getStart() . '.' . "<br />\n";
//
//    $hit = $result[0];
//    print_r($hit);

    $query = new SolrQuery("contentType:activity");
    $query->facets->setFields("content");
    echo $query->getQueryString();
    $result = $solr->query($query);
    $contentArray = array_rand($result->facets->fields["content"], 10);
//    shuffle($contentArray);
    print_r($contentArray);
?>
