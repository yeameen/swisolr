#!/bin/bash
#
# $Id: build_documentation.sh 11 2008-11-26 15:18:40Z rabus $

rm -rf ./coverage
phpunit --coverage-html ./coverage ./test/AllSolrTests.php