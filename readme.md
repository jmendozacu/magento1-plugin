# Ceevo Payment Module for Magento 1 CE

This is a Payment Module for Magento 1 Community Edition, that gives you the ability to process payments through payment service providers running on Ceevo platform.

## Requirements

Magento 1

Note: this module has been tested only with Magento 1 Community Edition, it may not work as intended with Magento 1 Enterprise Edition. 

### Instructions need to be follow:- (Kindly add the below files in specified mentioned folder structure)

Note: ..\ (Paths starts from ROOT directory)

### app Directory:
..\app\code\local\Mage\CeevoPayment\Block\Form\Cc.php  
..\app\code\local\Mage\CeevoPayment\Block\Info\Cc.php   
..\app\code\local\Mage\CeevoPayment\controllers\PaymentController.php 
..\app\code\local\Mage\CeevoPayment\etc\config.xml 
..\app\code\local\Mage\CeevoPayment\etc\system.xml
..\app\code\local\Mage\CeevoPayment\Helper\Data.php  
..\app\code\local\Mage\CeevoPayment\Model\cacert.pem  
..\app\code\local\Mage\CeevoPayment\Model\Paymentmethod.php   
..\app\code\local\Mage\CeevoPayment\Model\Paymentmethodtypes.php   
..\app\code\local\Mage\CeevoPayment\Model\SecureFlag.php   
..\app\code\local\Mage\CeevoPayment\Model\Transaction.php   
..\app\code\local\Mage\CeevoPayment\Model\Transactiontype.php   
..\app\code\local\Mage\CeevoPayment\sql\newmodule_setup\mysql4-install-0.1.0.php   
..\app\design\frontend\base\default\layout\ceevopayment.xml   
..\app\design\frontend\base\default\template\ceevopayment\form\cc.phtml   
..\app\etc\modules\CeevoPayment.xml   

### javascript Directory:
..\js\ceevo\ceevo_script.js  


### Modify js file for subfolder
If you are running magento in subfolder like http://localhost/magento then in  file app/design/frontend/base/default/layout/ceevopayment.xml  on line no 8 change script type="text/javascript" src="/js/ceevo/ceevo_script.js"></script> to script type="text/javascript" src="/{subfoldername}/js/ceevo/ceevo_script.js"></script>


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
