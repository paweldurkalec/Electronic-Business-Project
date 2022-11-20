<?php

$flock = fopen(__FILE__, 'r');
if( !flock($flock, LOCK_EX | LOCK_NB  )){
    die('Synchronization pending');
}

use FreshMail\Repository\FreshmailSettings;

require_once __DIR__ . '/../../../config/config.inc.php';

if(!Module::isInstalled('freshmail') || !($module = Module::getInstanceByName('freshmail')) ){
    die('Module isn\'t installed');
}

if(!\FreshMail\Tools::is_cli() && Tools::getValue('token') != $module->getCronToken() ) {
    die('Bad token');
}

if(empty($_GET['email'])){
    die('Email not set');
}
$email = $_GET['email'];

set_time_limit(0);

$activeFollowUps = (new \FreshMail\Repository\FollowUps())->getActive();

$service = new \FreshMail\Service\FollowUpService();
$followUps = [];

foreach ($activeFollowUps as $activeFollowUp) {
    if(empty($followUps[$activeFollowUp['id_freshmail_follow_up']])){
        $followUp = $followUps[$activeFollowUp['id_freshmail_follow_up']] = new \FreshMail\Entity\FollowUp($activeFollowUp['id_freshmail_follow_up']);

        $sql = 'SELECT *
            FROM `PREFIX_customer` c
            INNER JOIN `PREFIX_orders` o ON c.id_customer = o.id_customer
            WHERE `email` = \''.pSQL($email).'\'  
                AND c.id_shop = '.(int)$followUp->id_shop
            . ' ORDER BY o.date_add DESC '
        ;

        $customer = Db::getInstance()->getRow(str_replace('PREFIX_',_DB_PREFIX_,$sql));
        if(!empty($customer)){
           if( $service->sendNotifications(new Customer($customer['id_customer']), $followUp )){
               echo "FollowUp ". $followUp->id, ' send to ',$email, '<BR>';
           }
        }
    }
}





echo "Follow up - done\n";
