#!/bin/bash
#
# $Id$

rm -rf docs
phpdoc -ti "php_solr API Documentation" \
    -d ./src \
    -f "./ChangeLog,./LICENSE" \
    -t ./docs \
    -o HTML:Smarty:PHP