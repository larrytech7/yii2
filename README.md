Follow the instructions below to add the plugin to your Yii2 App.

CONFIGURATION
-------------

      1/     Copy the /controller/PaymenProcessorController.php file to your `controllers` folder.
      2/     Copy the /models/TransactionData.php file to your `models` folder
      3/     Copy the payment-processor directory to your `views` directory
      4/     Copy the script part of the /views/layouts/main.php file to your footer view page if you use one. Put this at the very last before the ending body tag.
### Switch to Live modes

Now open the PaymentProcessorController.php file and update all the TODOS accordingly as indicated.
Add your own stripe keys
Open the /views/payment-processor/processor file and modify it according to what you see in the TODO label

### Run Composer

~~~
composer require league/omnipay omnipay/stripe
~~~

Now the setup should be complete. Send me your live url path to the payment page for testing.