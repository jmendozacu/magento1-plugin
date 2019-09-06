Ceevo Payment Module for Magento 1 CE

This is a Payment Module for Magento 1 Community Edition, that gives you the ability to process payments through payment service providers running on Ceevo platform.

Requirements

Magento 1

Note: this module has been tested only with Magento 1 Community Edition, it may not work as intended with Magento 1 Enterprise Edition. You also need to install omnipay via composer.

Installation 
Create folders local and Mage if not there
1) Copy folder NewModule into app/code/local/Mage folder.
2) Copy newmodule.xml into app/etc/modules folder.
3) copy form folder into app\design\frontend\base\default\template\newmodule (create newmodule folder).
4) After that you will see the plugin in admin . configure and save  the settings.
Some ex cmd if using docker:

docker cp magento1-plugin/Mage/. 257da291fcbc:/var/www/html/app/code/local
docker cp magento1-plugin/NewModule.xml 257da291fcbc:/var/www/html/app/etc/modules
docker cp magento1-plugin/new/. 257da291fcbc:/var/www/html/app/design/frontend/base/default/template/newmodule

5. check module inside system -> configration -> Advanced -> Disable Module Output


      NOTE :  Save config

 6. Module name Mage_NewModule listed on modules and enable.

