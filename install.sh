#!/bin/bash
# installation of Ceevo plugin for Magneto 1

WEBROOT=/var/www/html
if [ ! -d "$WEBROOT/app/code/local/Mage" ]; then
	mkdir -p $WEBROOT/app/code/local/Mage
fi
cp -r ./Mage/CeevoPayment $WEBROOT/app/code/local/Mage/
cp ./CeevoPayment.xml $WEBROOT/app/etc/modules/
if [ ! -d "$WEBROOT/app/design/frontend/base/default/template/ceevopayment" ]; then
	mkdir $WEBROOT/app/design/frontend/base/default/template/ceevopayment
fi
cp -r ./form $WEBROOT/app/design/frontend/base/default/template/ceevopayment/
cp ./layout/ceevopayment.xml $WEBROOT/app/design/frontend/base/default/layout/
if [ ! -d "$WEBROOT/js/ceevo" ]; then
	mkdir $WEBROOT/js/ceevo
fi
cp ./ceevo_script.js $WEBROOT/js/ceevo/