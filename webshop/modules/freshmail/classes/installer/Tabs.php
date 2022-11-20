<?php

namespace FreshMail\Installer;

class Tabs
{
    private static function base(){
        return [
            [ 'controller' => 'AdminFreshmailConfig', 'name' => 'Configuration', 'icon' => 'settings', ],
            [ 'controller' => 'FreshmailNotification', 'name' => 'Notifications', 'icon' => 'settings_applications', 'parent' => 'AdminFreshmail'],
            [ 'controller' => 'AdminFreshmailAbandonedCartConfig', 'name' => 'Abandoned cart', 'icon' => 'shopping_cart', 'parent' => 'FreshmailNotification'],
            [ 'controller' => 'AdminFreshmailSuggestion', 'name' => 'Send suggestion', 'icon' => 'settings_applications', 'parent' => 'AdminFreshmail'],
        ];
    }

    private function extended(){
        return [
            [ 'controller' => 'AdminFreshmailSubscribers', 'name' => 'List of subscribers', 'icon' => 'list'],
            [ 'controller' => 'AdminFreshmailFormConfig', 'name' => 'Forms', 'icon' => 'settings_applications'],
            [ 'controller' => 'AdminFreshmailBirthday', 'name' => 'Birthday emails', 'icon' => 'settings_applications', 'parent' => 'FreshmailNotification'],
            [ 'controller' => 'AdminFreshmailFollowUp', 'name' => 'Follow Up', 'icon' => 'settings_applications', 'parent' => 'FreshmailNotification'],
            [ 'controller' => 'AdminFreshmailProductNotificationBase', 'name' => 'Product notifications', 'icon' => 'settings_applications', 'parent' => 'FreshmailNotification'],
            [ 'controller' => 'AdminFreshmailProductNotification', 'name' => 'Product notifications', 'icon' => 'settings_applications', 'parent' => 'AdminFreshmailProductNotificationBase'],
            [ 'controller' => 'AdminFreshmailProductNotificationList', 'name' => 'Product notifications list', 'icon' => 'settings_applications', 'parent' => 'AdminFreshmailProductNotificationBase'],
        ];
    }

    public static function getTabs($type = 'base'){
        $tabs = self::base();
        if('extended' == $type){
            $tabs = array_merge($tabs, self::extended());
        }
        return $tabs;
    }

    public static function install(\Module $module, $type = 'base'){
        $tabs = self::getTabs($type);
        $installer = InstallerFactory::getInstaller($module);

        $result = true;

        foreach ($tabs as $tab){
            $parent = (isset($tab['parent'])) ? $tab['parent'] : 'AdminFreshmail';
            $result &= $installer->installTab($tab['controller'], $tab['name'], $parent, false, $tab['icon']);
        }
        return $result;
    }
}
