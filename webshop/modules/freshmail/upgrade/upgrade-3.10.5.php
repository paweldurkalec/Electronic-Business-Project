<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_10_5($module)
{
    return $module->registerHook('actionObjectCustomerUpdateBefore');
}
