<?php
require_once dirname(__FILE__) . '/lib/SwiSolr.php';

// connect to the server
$solr = SwiSolr::connect('http://127.0.0.1:9080/solr1');

// delete all document
$solr->deleteByQuery("*:*");
$solr->commit();

echo "deleted all successfully";
?>
