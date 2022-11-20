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

set_time_limit(0);

$service = new \FreshMail\Service\FollowUpService();
$followUps = [];
for ($idFollowUp = 1; $idFollowUp <= (int)\FreshMail\FollowUps::NUM_FOLLOW_UPS; $idFollowUp++){
    foreach (Shop::getShops() as $shop){
        if(!Configuration::get(\FreshMail\FollowUps::CONFIG_KEY_ACTIVE.$idFollowUp, null, null, $shop['id_shop'])){
            continue;
        }
        $followUps[] = [
            'id_shop' => $shop['id_shop'],
            'id_follow_up' => $idFollowUp
        ];
    }
}


foreach ($followUps as $followUp) {
    $sendFollowsUp[] = [
        'customers' => $service->collectEmails($followUp['id_follow_up'], $followUp['id_shop']),
        'followupId' => $followUp['id_follow_up'],
        'shopId' => $followUp['id_shop']
    ];
}

foreach ($sendFollowsUp as $sendItem){
    if(empty($sendItem['customers'])){
        continue;
    }
    foreach ($sendItem['customers'] as $send){
        $customer = new Customer($send['id_customer']);
        if($service->sendNotifications($customer, \FreshMail\Entity\EmailTemplate::getByType('follow_up'.$sendItem['followupId'], $sendItem['shopId']), ['id_follow_up' => $sendItem['followupId']] )){
            $service->saveFollowUpInfo($sendItem['followupId'], $sendItem['shopId'], $customer);
        }
    }
}


echo "Follow up - done\n";
