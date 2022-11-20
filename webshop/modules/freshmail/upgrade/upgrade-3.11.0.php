<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_11_0($module)
{
    $sql = [
        'CREATE TABLE IF NOT EXISTS `PREFIX_freshmail_follow_up_sended`(
            `id` INT NOT NULL AUTO_INCREMENT ,
            `id_freshmail_follow_up` INT NOT NULL,
            `id_customer` INT NOT NULL ,
            `email` VARCHAR (150),
            `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
            PRIMARY KEY (`id`)
        ) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;',

        'CREATE TABLE IF NOT EXISTS `PREFIX_freshmail_setting_completed`(
            `id` INT NOT NULL AUTO_INCREMENT ,       
            `id_shop` INT NOT NULL,
            `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
            PRIMARY KEY (`id`)
        ) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;',

        'INSERT INTO `PREFIX_freshmail_setting_completed`(id_shop) SELECT id_shop FROM PREFIX_freshmail_setting',
    ];

    return Db::getInstance()->execute( str_replace('PREFIX_', _DB_PREFIX_, $sql[0]))
        && Db::getInstance()->execute( str_replace('PREFIX_', _DB_PREFIX_, $sql[1]))
        && Db::getInstance()->execute( str_replace('PREFIX_', _DB_PREFIX_, $sql[2]))
        && \FreshMail\Installer\InstallerFactory::getInstaller($module)->installTab(
        'FreshmailNotification', 'Notifications', 'AdminFreshmail', false
        )
        && changeAbandonCardTab()
        && changeBirthdayTab()
        && \FreshMail\Installer\InstallerFactory::getInstaller($module)->installTab(
        'AdminFreshmailFollowUp', 'Follow Up', 'FreshmailNotification', false, 'settings_applications'
        );
}

function changeBirthdayTab(){
    $idTab = (int)Tab::getIdFromClassName('AdminFreshmailBirthday');
    $tab = new Tab($idTab);
    $tab->id_parent = (int)Tab::getIdFromClassName('FreshmailNotification');
    return $tab->save();
}

function changeAbandonCardTab(){
    $idTab = (int)Tab::getIdFromClassName('AdminFreshmailAbandonedCartConfig');
    $tab = new Tab($idTab);
    $tab->id_parent = (int)Tab::getIdFromClassName('FreshmailNotification');
    return $tab->save();
}