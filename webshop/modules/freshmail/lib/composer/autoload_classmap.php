<?php

// autoload_classmap.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'AbandonCartDiscount' => $baseDir . '/src/Interface/AbandonCartDiscount.php',
    'AdminFreshmailAbandonedCartConfigController' => $baseDir . '/controllers/admin/AdminFreshmailAbandonedCartConfig.php',
    'AdminFreshmailAjaxController' => $baseDir . '/controllers/admin/AdminFreshmailAjax.php',
    'AdminFreshmailBaseController' => $baseDir . '/controllers/admin/AdminFreshmailBase.php',
    'AdminFreshmailBirthdayController' => $baseDir . '/controllers/admin/AdminFreshmailBirthday.php',
    'AdminFreshmailConfigController' => $baseDir . '/controllers/admin/AdminFreshmailConfig.php',
    'AdminFreshmailDashboardController' => $baseDir . '/controllers/admin/AdminFreshmailDashboardController.php',
    'AdminFreshmailFollowUpController' => $baseDir . '/controllers/admin/AdminFreshmailFollowUp.php',
    'AdminFreshmailFormConfigController' => $baseDir . '/controllers/admin/AdminFreshmailFormConfig.php',
    'AdminFreshmailProductNotificationController' => $baseDir . '/controllers/admin/AdminFreshmailProductNotification.php',
    'AdminFreshmailProductNotificationListController' => $baseDir . '/controllers/admin/AdminFreshmailProductNotificationList.php',
    'AdminFreshmailSubscribersController' => $baseDir . '/controllers/admin/AdminFreshmailSubscribers.php',
    'AdminFreshmailWizardController' => $baseDir . '/controllers/admin/AdminFreshmailWizardController.php',
    'Composer\\InstalledVersions' => $vendorDir . '/composer/InstalledVersions.php',
    'FreshMail\\Customer' => $baseDir . '/classes/Customer.php',
    'FreshMail\\Discount\\AbstractDiscount' => $baseDir . '/classes/discount/AbstractDiscount.php',
    'FreshMail\\Discount\\Custom' => $baseDir . '/classes/discount/Custom.php',
    'FreshMail\\Discount\\None' => $baseDir . '/classes/discount/None.php',
    'FreshMail\\Discount\\Percent' => $baseDir . '/classes/discount/Percent.php',
    'FreshMail\\Entity\\AsyncJob' => $baseDir . '/src/Entity/AsyncJob.php',
    'FreshMail\\Entity\\Birthday' => $baseDir . '/src/Entity/Birthday.php',
    'FreshMail\\Entity\\Cart' => $baseDir . '/src/Entity/Cart.php',
    'FreshMail\\Entity\\CartNotify' => $baseDir . '/src/Entity/CartNotify.php',
    'FreshMail\\Entity\\Email' => $baseDir . '/src/Entity/Email.php',
    'FreshMail\\Entity\\EmailTemplate' => $baseDir . '/src/Entity/EmailTemplate.php',
    'FreshMail\\Entity\\EmailToSynchronize' => $baseDir . '/src/Entity/EmailToSynchronize.php',
    'FreshMail\\Entity\\EmailsSynchronized' => $baseDir . '/src/Entity/EmailsSynchronized.php',
    'FreshMail\\Entity\\FollowUp' => $baseDir . '/src/Entity/FollowUp.php',
    'FreshMail\\Entity\\Form' => $baseDir . '/src/Entity/Form.php',
    'FreshMail\\Entity\\ProductNotification' => $baseDir . '/src/Entity/ProductNotification.php',
    'FreshMail\\FollowUps' => $baseDir . '/classes/FollowUps.php',
    'FreshMail\\Freshmail' => $baseDir . '/classes/Freshmail.php',
    'FreshMail\\FreshmailApiV3' => $baseDir . '/classes/FreshmailApiV3.php',
    'FreshMail\\FreshmailCode' => $baseDir . '/classes/FreshmailCode.php',
    'FreshMail\\FreshmailList' => $baseDir . '/classes/FreshmailList.php',
    'FreshMail\\Hooks' => $baseDir . '/classes/Hooks.php',
    'FreshMail\\HooksForms' => $baseDir . '/classes/HooksForms.php',
    'FreshMail\\Installer' => $baseDir . '/classes/installer/Installer.php',
    'FreshMail\\Installer\\InstallerFactory' => $baseDir . '/classes/installer/InstallerFactory.php',
    'FreshMail\\Installer\\Tabs' => $baseDir . '/classes/installer/Tabs.php',
    'FreshMail\\Interfaces\\Installer' => $baseDir . '/interfaces/Installer.php',
    'FreshMail\\Interfaces\\Sender' => $baseDir . '/src/Interface/Sender.php',
    'FreshMail\\Interfaces\\TransactionEmail' => $baseDir . '/src/Interface/TransactionEmail.php',
    'FreshMail\\ProductNotifications' => $baseDir . '/classes/ProductNotifications.php',
    'FreshMail\\Repository\\AbstractRepository' => $baseDir . '/src/Repository/AbstractRepository.php',
    'FreshMail\\Repository\\AsyncJobs' => $baseDir . '/src/Repository/AsyncJobs.php',
    'FreshMail\\Repository\\Birthdays' => $baseDir . '/src/Repository/Birthdays.php',
    'FreshMail\\Repository\\Carts' => $baseDir . '/src/Repository/Carts.php',
    'FreshMail\\Repository\\EmailToSynchronize' => $baseDir . '/src/Repository/EmailToSynchronize.php',
    'FreshMail\\Repository\\EmailsSynchronized' => $baseDir . '/src/Repository/EmailsSynchronized.php',
    'FreshMail\\Repository\\FormRepository' => $baseDir . '/src/Repository/FormRepository.php',
    'FreshMail\\Repository\\FreshmailAbandonCartSettings' => $baseDir . '/src/Repository/FreshmailAbandonCartSettings.php',
    'FreshMail\\Repository\\FreshmailSettings' => $baseDir . '/src/Repository/FreshmailSettings.php',
    'FreshMail\\Repository\\ProductNotifications' => $baseDir . '/src/Repository/ProductNotifications.php',
    'FreshMail\\Repository\\Subscribers' => $baseDir . '/src/Repository/Subscribers.php',
    'FreshMail\\Sender\\AbstractSender' => $baseDir . '/classes/sender/AbstractSender.php',
    'FreshMail\\Sender\\Email' => $baseDir . '/classes/sender/Email.php',
    'FreshMail\\Sender\\Factory' => $baseDir . '/classes/sender/Factory.php',
    'FreshMail\\Sender\\FmSender' => $baseDir . '/classes/sender/FmSender.php',
    'FreshMail\\Sender\\Legacy' => $baseDir . '/classes/sender/Legacy.php',
    'FreshMail\\Sender\\Sender' => $baseDir . '/classes/sender/Sender.php',
    'FreshMail\\Sender\\Service\\CartData' => $baseDir . '/classes/sender/Service/CartData.php',
    'FreshMail\\Sender\\Service\\CartDataCollector' => $baseDir . '/classes/sender/Service/CartDataCollector.php',
    'FreshMail\\Sender\\Service\\MockCartData' => $baseDir . '/classes/sender/Service/MockCartData.php',
    'FreshMail\\Service\\AbandonCartService' => $baseDir . '/src/Service/AbandonCartService.php',
    'FreshMail\\Service\\AccountService' => $baseDir . '/src/Service/AccountService.php',
    'FreshMail\\Service\\AsyncJobService' => $baseDir . '/src/Service/AsyncJobService.php',
    'FreshMail\\Service\\AuthService' => $baseDir . '/src/Service/AuthService.php',
    'FreshMail\\Service\\BirthdayService' => $baseDir . '/src/Service/BirthdayService.php',
    'FreshMail\\Service\\FollowUpService' => $baseDir . '/src/Service/FollowUpService.php',
    'FreshMail\\Service\\FormService' => $baseDir . '/src/Service/FormService.php',
    'FreshMail\\Service\\ProductNotificationService' => $baseDir . '/src/Service/ProductNotificationService.php',
    'FreshMail\\Subscriber' => $baseDir . '/classes/Subscriber.php',
    'FreshMail\\SubscriberCollection' => $baseDir . '/classes/SubscriberCollection.php',
    'FreshMail\\Tools' => $baseDir . '/classes/Tools.php',
    'FreshMail\\TransactionalEmail' => $baseDir . '/classes/TransactionalEmail.php',
    'Freshmail\\Entity\\AbandonedCartSettings' => $baseDir . '/src/Entity/AbandonedCartSettings.php',
    'Freshmail\\Entity\\FreshmailEmail' => $baseDir . '/src/Entity/FreshmailEmail.php',
    'Freshmail\\Entity\\FreshmailSetting' => $baseDir . '/src/Entity/FreshmailSetting.php',
    'freshmailRestoreModuleFrontController' => $baseDir . '/controllers/front/restore.php',
    'freshmailajaxModuleFrontController' => $baseDir . '/controllers/front/ajax.php',
);
