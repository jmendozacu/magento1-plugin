#!/bin/bash
# installation of Ceevo plugin for Magneto 1

WEBROOT=/var/www/html
if [ ! -d "$WEBROOT/app/code/local/Mage" ]; then
	mkdir $WEBROOT/app/code/local
	mkdir $WEBROOT/app/code/local/Mage
fi
cp -r ./Mage/NewModule $WEBROOT/app/code/local/Mage/
cp ./NewModule.xml $WEBROOT/app/etc/modules/
if [ ! -d "$WEBROOT/app/design/frontend/base/default/template/newmodule" ]; then
	mkdir $WEBROOT/app/design/frontend/base/default/template/newmodule
fi
cp -r ./form $WEBROOT/app/design/frontend/base/default/template/newmodule/
cp ./layout/newmodule.xml $WEBROOT/app/design/frontend/base/default/layout/
