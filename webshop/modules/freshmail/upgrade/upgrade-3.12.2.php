<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_12_2($module)
{
    $sql = [
        "ALTER TABLE `PREFIX_freshmail_product_notification` ADD `id_lang` INT NOT NULL"
    ];

    return Db::getInstance()->execute( str_replace('PREFIX_', _DB_PREFIX_, $sql[0]))
        && $module->registerHook('actionObjectSpecificPriceAddBefore')
        && $module->registerHook('actionObjectSpecificPriceAddAfter')
    ;
}

