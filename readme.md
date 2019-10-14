# Ceevo Payment Module for Magento 1 CE

This is a Payment Module for Magento 1 Community Edition, that gives you the ability to process payments through payment service providers running on Ceevo platform.

## Requirements

Magento 1

Note: this module has been tested only with Magento 1 Community Edition, it may not work as intended with Magento 1 Enterprise Edition. 

## Installation 
You can either the installation script, or copy the files manually.

### Running the installation script
1. Git clone this repository to the web root folder (e.g. **/var/www/html**)
`git clone https://github.com/goceevo/magento1-plugin.git`
2. Go to the plugin folder
`cd magento1-plugin`
3. Run the installation script
`./install.sh`

-- or  --

### Copy files Manually
1) Under the folder **app/code**, create folders **local** and **Mage** if not there.
1) Copy folder **CeevoPayment** into **app/code/local/Mage** folder.
1) Copy **CeevoPayment.xml** into **app/etc/modules** folder.
1) Under the folder **/app/design/frontend/base/default/template/**,  create **ceevopayment** folder.
1) Copy **form** folder into **app/design/frontend/base/default/template/ceevopayment**.
1) Copy the file **layout/ceevopayment.xml** to **app\design\frontend\base\default\layout**
1) If you are running magento in subfolder like http://localhost/magento then in  file app/design/frontend/base/default/layout/ceevopayment.xml  on line no 8 change script type="text/javascript" src="/js/ceevo/ceevo_script.js"></script> to script type="text/javascript" src="/{subfoldername}/js/ceevo/ceevo_script.js"></script>
1) Add file ceevo_script.js in /js/ceevo/ folder. You need to create /ceevo folder inside js folder


### Verify Your Installation
After the files are in place, please go to Magento backend to verify your installation and setup the plugin

1. Click **System**, and then **Configuration**
![](https://raw.githubusercontent.com/goceevo/magento1-plugin/master/readme_images/magento_backend.png)
2. Click **Save Config**
![](https://raw.githubusercontent.com/goceevo/magento1-plugin/master/readme_images/save_config.png)
3. Go to **Advanced** 
![](https://raw.githubusercontent.com/goceevo/magento1-plugin/master/readme_images/Advanced_config.png)
4. Click **Disable Modules Output** to open it up
![](https://raw.githubusercontent.com/goceevo/magento1-plugin/master/readme_images/disable_modules_output.png)
5. Check if **Mage_CeevoPayment** is there
![](https://raw.githubusercontent.com/goceevo/magento1-plugin/master/readme_images/ceevo_module.png)
6. Click **Payment Methods** and then **Ceevo Payment** to config your plugin
![](https://raw.githubusercontent.com/goceevo/magento1-plugin/master/readme_images/ceevo_payment_method.png)
