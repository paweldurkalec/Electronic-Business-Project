<?php


$flock = fopen(__FILE__, 'r');
if (!flock($flock, LOCK_EX | LOCK_NB)) {
    die('Synchronization pending');
}

use FreshMail\Repository\FreshmailSettings;

require_once __DIR__ . '/../../../config/config.inc.php';


/*if (!Module::isInstalled('freshmail') || Tools::getValue('token') != Module::getInstanceByName('freshmail')->getCronToken() ) {
    die('Bad token');
}*/
Module::getInstanceByName('freshmail');

// 1. utwórz listę
// 2. paczkuj na elementy
// 3. wyślij
// 4. usuń listy

$abandonRepository = new \FreshMail\Repository\FreshmailAbandonCartSettings(Db::getInstance());
$activeAbandon = $abandonRepository->getActive();
$cartsRepository = new \FreshMail\Repository\Carts(Db::getInstance());
$settingsRepository = new FreshmailSettings(Db::getInstance());
$cartService = new \FreshMail\Service\AbandonCartService($cartsRepository, $settingsRepository, $abandonRepository);


foreach ($activeAbandon as $abandon) {
    $settings = new \Freshmail\Entity\AbandonedCartSettings($abandon['id_freshmail_cart_setting']);

    $cart = new Cart();
    $cart->id_shop = 1;
    $cart->id_lang = Tools::getValue('id_lang') ? Tools::getValue('id_lang') : (int)Configuration::get('PS_LANG_DEFAULT');
    $fmCart = new \FreshMail\Entity\Cart();
/*    $fmCart = $cartsRepository->getByCart($cart);
    $cartService->setDiscount($settings, $cart, $fmCart);*/
    $email = new \FreshMail\Sender\Email('lukasz.kolanko@lizardmedia.pl', 'Łukasz');
    //$email = 'lukasz.kolanko@lizardmedia.pl';(new Customer($cart->id_customer))->email;
    $cartService->sendNotifications($cart, $fmCart, $email, new \FreshMail\Sender\Service\MockCartData());
}
