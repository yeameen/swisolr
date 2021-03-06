<?php
    require_once dirname(__FILE__) . '/lib/SwiSolr.php';

    SwiSolr::autoload('SolrSimpleDocument');
    SwiSolr::autoload('SolrSimpleField');


//    $solr = SwiSolr::connect('http://127.0.0.1:8080/solr');
    $solr = SwiSolr::connect('http://127.0.0.1:9080/solr1/');

    $doc = new SolrSimpleDocument(array(
      new SolrSimpleField('id', 'blog-6'),
      new SolrSimpleField('contentType', 'post'),
      new SolrSimpleField('dbId', '6'),
      new SolrSimpleField('title', 'this is'),
      new SolrSimpleField('content', 'that is')
    ));
try {
    $solr->add($doc);
    $solr->commit();
} catch (Exception $e) {
    echo $e;
}

    /*
     * delete the previous data. there is also another function named deleteByQuery.
     * but deleteById is safer
     */

//    $solr->deleteById('blog-4');
    $solr->commit();

    /* now optimize the index */
//    $solr->optimize();


    echo "Successfully added the document";

?>
