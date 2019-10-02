#!/bin/bash
# installation of Ceevo plugin for Magneto 1
# Please copy the plugin folder to the web root (e.g. /var/www/html) and run this script
if [ ! -d "../app/code/local/Mage" ]; then
	mkdir ../app/code/local
	mkdir ../app/code/local/Mage
fi
cp -r ./Mage/NewModule ../app/code/local/Mage/
cp ./NewModule.xml ../app/etc/modules/
if [ ! -d "../app/design/frontend/base/default/template/newmodule" ]; then
	mkdir ../app/design/frontend/base/default/template/newmodule
fi
cp -r ./form ../app/design/frontend/base/default/template/newmodule/
cp ./layout/newmodule.xml ../app/design/frontend/base/default/layout/
