<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_12_0($module)
{
    $sql = [
        "CREATE TABLE IF NOT EXISTS `PREFIX_freshmail_product_notification`(
            `id_freshmail_product_notification` INT NOT NULL AUTO_INCREMENT ,
            `id_shop` INT NOT NULL,
            `id_product` INT(10) UNSIGNED NOT NULL,
            `id_product_attribute` INT(10) UNSIGNED NOT NULL,
            `email` VARCHAR(150),
            `active` TINYINT(1) NOT NULL DEFAULT 0,
            `type` ENUM ('discount', 'available'),
            `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
            PRIMARY KEY (`id_freshmail_product_notification`)
        ) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;",

        "CREATE TABLE IF NOT EXISTS `PREFIX_freshmail_product_notification_log`(
            `id_freshmail_product_notification_log` INT NOT NULL AUTO_INCREMENT ,
            `id_shop` INT NOT NULL,
            `id_product` INT(10) UNSIGNED NOT NULL,
            `id_product_attribute` INT(10) UNSIGNED NOT NULL,
            `type` VARCHAR (32),
            `email` VARCHAR(150),
            `product_price` decimal(20,6) NOT NULL DEFAULT 0.000000,
            `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
            PRIMARY KEY (`id_freshmail_product_notification_log`)
        ) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;",

        "CREATE TABLE IF NOT EXISTS `PREFIX_freshmail_email_template`(
            `id_freshmail_email_template` INT NOT NULL AUTO_INCREMENT ,
            `id_shop` INT NOT NULL,
            `type` VARCHAR (32),
            `tpl` VARCHAR(32),
            `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
            `date_upd` TIMESTAMP NULL,
            PRIMARY KEY (`id_freshmail_email_template`)
        ) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

        CREATE TABLE IF NOT EXISTS `PREFIX_freshmail_email_template_lang`(
            `id_freshmail_email_template` INT NOT NULL,
            `id_lang` INT NOT NULL ,
            `content` TEXT,
            `email_subject` TEXT,
            PRIMARY KEY (`id_freshmail_email_template`, `id_lang`)
        ) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;"
    ];

    $hooks =    [
        'displayReassurance',
        'displayAdminProductsExtra',
        'actionProductDelete',
        'actionUpdateQuantity',
        'actionObjectProductUpdateBefore',
        'actionObjectProductUpdateAfter',
        'actionProductAttributeUpdate',
        'actionFrontControllerSetMedia',
    ];

    return Db::getInstance()->execute( str_replace('PREFIX_', _DB_PREFIX_, $sql[0]))
        && Db::getInstance()->execute( str_replace('PREFIX_', _DB_PREFIX_, $sql[1]))
        && Db::getInstance()->execute( str_replace('PREFIX_', _DB_PREFIX_, $sql[2]))
        && Db::getInstance()->execute( str_replace('PREFIX_', _DB_PREFIX_, $sql[3]))
        && $module->registerHook($hooks)
        && \FreshMail\Installer\InstallerFactory::getInstaller($module)->installTab(
            'AdminFreshmailProductNotificationBase', 'Product notifications', 'FreshmailNotification', false
        )
        && \FreshMail\Installer\InstallerFactory::getInstaller($module)->installTab(
            'AdminFreshmailProductNotification', 'Product notifications', 'AdminFreshmailProductNotificationBase', false
        )
        && \FreshMail\Installer\InstallerFactory::getInstaller($module)->installTab(
            'AdminFreshmailProductNotificationList', 'Product notifications list', 'AdminFreshmailProductNotificationBase', false
        )
    ;
}

