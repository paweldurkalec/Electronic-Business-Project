<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_13_0($module)
{
    return \FreshMail\Installer\InstallerFactory::getInstaller($module)->installTab(
        'AdminFreshmailSuggestion', 'Send suggestion', 'AdminFreshmail', false, 'settings_applications'
    );
}

