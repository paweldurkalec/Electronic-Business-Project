<?php

namespace FreshMail;

use Configuration;
use FreshMail\Entity\Cart;
use FreshMail\Entity\EmailTemplate;
use FreshMail\Entity\ProductNotification;
use FreshMail\Repository\AsyncJobs;
use FreshMail\Repository\FormRepository;
use FreshMail\Repository\FreshmailAbandonCartSettings;
use FreshMail\Repository\FreshmailSettings;
use FreshMail\Service\FormService;
use FreshMail\Service\ProductNotificationService;
use PrestaShop\PrestaShop\Adapter\Entity\Cache;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use Validate;

trait Hooks
{

    public function getHooks()
    {
        return [
            'displayBackOfficeHeader',
            'actionCustomerAccountAdd',
            'actionDeleteGDPRCustomer',
            'actionObjectCustomerDeleteAfter',
            'actionObjectCartAddAfter',
            'actionObjectCustomerUpdateBefore',
            'displayReassurance',
            'displayAdminProductsExtra',
            'actionProductDelete',
            'actionUpdateQuantity',
            'actionObjectProductUpdateBefore',
            'actionObjectProductUpdateAfter',
            'actionProductAttributeUpdate',
            'actionObjectSpecificPriceAddBefore',
            'actionObjectSpecificPriceAddAfter',
            'actionFrontControllerSetMedia',
        ];
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        // $this->context->controller->addCSS($this->_path . 'views/css/freshmail-core.css', 'all');

        $ets = new \FreshMail\Repository\EmailToSynchronize(\Db::getInstance());
        $list = $ets->getListToSync();
        if(!empty($list[0])){
            $this->context->smarty->assign([
                'pendingSend' => true,
                'sendUrl' => $this->context->link->getBaseLink(null,true).'modules/'.$this->name.'/cron/send_subscribers.php?hash='.$list[0]['hash_list'].'&token='.$this->getCronToken()
            ]);
        }

        $this->context->smarty->assign([
            'base_url' => $this->context->link->getBaseLink(null,true),
        ]);

        $aj = new AsyncJobs(\Db::getInstance());
        $jobs = $aj->getRunningJobs();
        if(!empty($jobs)){
            Tools::asyncJobPing();
        }

        return $this->display(_PS_MODULE_DIR_ .'freshmail', 'views/templates/admin/header.tpl');
    }
     public function hookActionCustomerAccountAdd($params){
        $customer = $params['newCustomer'];

        if($customer->newsletter){
            $this->addSubscriber($customer->email, $customer->firstname);
        }
    }

    public function hookActionDeleteGDPRCustomer($customer)
    {
        $this->deleteSubscriber($customer['email']);
    }

    public function hookActionObjectCustomerDeleteAfter($params)
    {
        $this->deleteSubscriber($params['object']->email);
    }


    public function getFreshmailList() : FreshmailList
    {
        $freshmailSettings = (new FreshmailSettings())->findForShop($this->context->shop->id);
        $fm = new FreshmailList($freshmailSettings);
        if(empty($freshmailSettings->subscriber_list_hash) || !$fm->check()){
            return false;
        }

        return $fm;
    }

    public function hookActionObjectCartAddAfter($params)
    {
        $cart = $params['object'];
        if(
            !(new FreshmailAbandonCartSettings(\Db::getInstance()))->findForShop($cart->id_shop)->enabled
        ){
            return;
        }

        $fmCart = new Cart();
        $fmCart->id_cart = $cart->id;
        $fmCart->cart_token = sha1(time()).md5(time());
        $fmCart->save();
    }

    public function hookActionObjectCustomerUpdateBefore($params)
    {
        $old = new \Customer($params['object']->id);
        if($old->newsletter == $params['object']->newsletter){
            return;
        }
        if(0 == $params['object']->newsletter){
            $this->deleteSubscriber($params['object']->email);
        } else {
            $this->addSubscriber($params['object']->email, $params['object']->firstname);
        }

    }

    private function deleteSubscriber($email){
        if(empty($email) || !Validate::isEmail($email)){
            return;
        }
        $fmList = $this->getFreshmailList();
        if(empty($fmList)){
            return;
        }
        $fmList->deleteSubscriber(new Subscriber($email));

    }

    private function addSubscriber($email, $name){
        if(empty($email) || !Validate::isEmail($email)){
            return;
        }
        $fmList = $this->getFreshmailList();
        if(empty($fmList)){
            return;
        }
        $subscriber = new Subscriber($email);
        $subscriber->custom_fields[\Freshmail::NAME_TAG] = $name;
        $fmList->addSubscriber($subscriber);

    }

    public function hookDisplayReassurance($params)
    {
        if( !Configuration::get(\FreshMail\ProductNotifications::CONFIG_KEY_AVAILABLE, null, null, $this->context->shop->id)
            && !Configuration::get(\FreshMail\ProductNotifications::CONFIG_KEY_DISCOUNT, null, null, $this->context->shop->id)
        ){
            return;
        }

        if(!\Tools::getValue('id_product') && !\Tools::getValue('id_product_attribute')){
            return;
        }
        $idProduct = (int)\Tools::getValue('id_product');
        $idProductAttribute = (int)\Tools::getValue('id_product_attribute');

        $product = new \Product($idProduct);

        if( \StockAvailable::getQuantityAvailableByProduct($idProduct, $idProductAttribute) <= 0 &&  !\Product::isAvailableWhenOutOfStock($product->out_of_stock)){
            $promptText =  Configuration::get(\FreshMail\ProductNotifications::CONFIG_KEY_AVAILABLE_PROMPT, $this->context->language->id, null, $this->context->shop->id);
        } else {
            $promptText =   Configuration::get(\FreshMail\ProductNotifications::CONFIG_KEY_DISCOUNT_PROMPT, $this->context->language->id, null, $this->context->shop->id);
        }

        $this->context->smarty->assign([
            'ajax_info' => $this->context->link->getModuleLink($this->name, 'ajax', ['action' => 'getInfo']),
            'ajax_save' => $this->context->link->getModuleLink($this->name, 'ajax', ['action' => 'saveInfo']),
            'prompt_text' => $promptText
        ]);

        return $this->fetch('module:'.$this->name.'/views/templates/hook/displayReassurance.tpl');
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        $repository = new \FreshMail\Repository\ProductNotifications();
        $this->context->smarty->assign([
            'remove_notification_link' => $this->context->link->getAdminLink('AdminFreshmailProductNotification').'&action=deleteNotification&ajax=1' ,
            'tpl_path' => _PS_MODULE_DIR_ . $this->name . '/views/templates/hook/',
            'list_discount' => $repository->getActiveDiscount($params['id_product']),
            'list_available' => $repository->getActiveAvailable($params['id_product']),
        ]);
        return $this->fetch('module:'.$this->name.'/views/templates/hook/displayAdminProductsExtra.tpl');
    }

    public function hookActionProductDelete($params)
    {
        $idProduct = (int)$params['id_product'];
        (new \FreshMail\Repository\ProductNotifications())->deleteByIdProduct($idProduct);
    }

    public function hookActionUpdateQuantity($params)
    {
        if(!Configuration::get(ProductNotifications::CONFIG_KEY_AVAILABLE, null, null, $this->context->shop->id)){
            return false;
        }
        $quantity = (int) $params['quantity'];

        if ($quantity <= 0) {
            $product = new \Product((int)$params['id_product']);

            if(!\Product::isAvailableWhenOutOfStock($product->out_of_stock)){
                (new ProductNotificationService())->enableAvailabilityNotification($params['id_product'], $params['id_product_attribute'] );
            }
            return false;
        }

        if (isset($params['id_shop'])) {
            return false;
        }

        $this->sendProductAvailableNotification((int) $params['id_product'],  $params['id_product_attribute']);
    }

    private static $pricesCombination = [];
    private static $price = null;
    private function buildPriceCache(\Product $product){
        if($product->hasAttributes()){
            foreach ($product->getWsCombinations() as $combination){
                self::$pricesCombination[$combination['id']] = \Product::getPriceStatic($product->id, false, $combination['id']);
            }
        }
        self::$price = \Product::getPriceStatic($product->id, false);
    }

    public function hookActionObjectProductUpdateBefore($params)
    {
        $this->buildPriceCache( $params['object']);
    }

    public function hookActionObjectProductUpdateAfter($params)
    {
        $product = $params['object'];
        if($product->hasAttributes()){
            return;
        }

        \Product::flushPriceCache();
        $price = \Product::getPriceStatic($product->id, false);

        if($this->hasPriceNotificationTrigger(self::$price, $price)){
            $this->sendProductPriceNotification($product->id,0, self::$price, $price);
        }
    }

    public function hookActionProductAttributeUpdate($params)
    {
        $combination = new \Combination($params['id_product_attribute']);
        if(!Validate::isLoadedObject($combination)){
            return;
        }
        \Product::flushPriceCache();
        $price = \Product::getPriceStatic($combination->id_product, false, $params['id_product_attribute']);

        if($this->hasPriceNotificationTrigger(self::$pricesCombination[$params['id_product_attribute']], $price)){
            $this->sendProductPriceNotification($combination->id_product, $params['id_product_attribute'], self::$pricesCombination[$params['id_product_attribute']], $price);
        }
    }

    public function hookActionObjectSpecificPriceAddBefore($params)
    {
        $product = new \Product($params['object']->id_product);
        $this->buildPriceCache($product);
    }

    public function hookActionObjectSpecificPriceAddAfter($params)
    {
        \SpecificPrice::flushCache();

        $specificPrice = $params['object'];
        $product = new \Product($specificPrice->id_product);
        if( $specificPrice->id_product_attribute ){
            $price = \Product::getPriceStatic($specificPrice->id_product, false, $specificPrice->id_product_attribute);

            if($this->hasPriceNotificationTrigger(self::$pricesCombination[$specificPrice->id_product_attribute], $price)){
                $this->sendProductPriceNotification($specificPrice->id_product, $specificPrice->id_product_attribute, self::$pricesCombination[$specificPrice->id_product_attribute], $price);
            }
        } else {
            foreach ($product->getWsCombinations() as $combination){
                $price = \Product::getPriceStatic($specificPrice->id_product, false, $combination['id'], 6, null, false, true, 1, false, null, null, null, $specific_price, true, true, null, true);

                if($this->hasPriceNotificationTrigger(self::$pricesCombination[$combination['id']], $price)){
                    $this->sendProductPriceNotification($specificPrice->id_product, $combination['id'], self::$pricesCombination[$combination['id']], $price);
                }
            }
        }
    }

    private static $trigger = null;
    public function hasPriceNotificationTrigger($before, $after)
    {
        if($after > $before){
            return false;
        }

        if(!Configuration::get(ProductNotifications::CONFIG_KEY_DISCOUNT, null, null, $this->context->shop->id)){
            return false;
        }

        if(null === self::$trigger){
            self::$trigger = \Configuration::get( ProductNotifications::CONFIG_KEY_DISCOUNT_TRIGGER, null, null, $this->context->shop->id);
        }
        if(1 > (int)self::$trigger){
            return false;
        }

        return (1 - $after / $before) * 100 >= self::$trigger;
    }

    public function sendProductPriceNotification($id_product, $id_product_attribute, $price_before, $price_after)
    {
        $emailTpl = EmailTemplate::getByType('discount');

        $tags = $this->prepareProductData($id_product, $id_product_attribute, $emailTpl);
        $tags['{discount}'] = '';

        if(!empty($price_before)){
            $tags['{discount}'] = ((1 - $price_after / $price_before) * 100) . '%';
        }

        $notificationService = new ProductNotificationService();

        foreach ($notificationService->collectEmails('discount', $id_product, $id_product_attribute) as $email){
            $notificationService->sendNotifications($email['email'], $email['id_lang'], $this->context->shop->id, $emailTpl, $tags);
            $notificationService->saveSendDiscountNotification($email['email'], $id_product, $id_product_attribute, $price_after);
            $notificationService->deleteDiscountNotification($email['email'], $id_product, $id_product_attribute, $this->context->shop->id);
        }
    }

    public function sendProductAvailableNotification($id_product, $id_product_attribute)
    {
        $emailTpl = EmailTemplate::getByType('available', null, $this->context->shop->id);
        $tags = $this->prepareProductData($id_product, $id_product_attribute, $emailTpl);

        $notificationService = new ProductNotificationService();
        $id_shop = $this->context->shop->id;

        foreach ($notificationService->collectEmails('available',  $id_product, $id_product_attribute) as $email){
            $notificationService->sendNotifications($email['email'], $email['id_lang'], $this->context->shop->id, $emailTpl, $tags);
            $notificationService->saveSendAvailabilityNotification($email['email'], $id_product, $id_product_attribute, $id_shop);
            // $notificationService->disableAvailabilityNotification($email['email'], $id_product, $id_product_attribute, $id_shop);
            $notificationService->deleteAvailabilityNotification($email['email'], $id_product, $id_product_attribute, $id_shop);
        }
    }

    private function prepareProductData($id_product, $id_product_attribute, EmailTemplate $email_template){
        $priceFormatter = new PriceFormatter();

        $tags = [
            '{product_name}' => \Product::getProductName($id_product, $id_product_attribute),
            '{product_url}' => $this->context->link->getProductLink($id_product),
            '{product_price}' => $priceFormatter->format(\Product::getPriceStatic($id_product, true, $id_product_attribute)),
            '{products_list}' => '',
            '{img}' => '',
            '{img_big}' => '',
        ];

        $cover = \Product::getCover($id_product);
        if(!empty($cover['id_image'])){
            $productName = \Product::getProductName($id_product);
            $tags['{img}'] = $this->context->link->getImageLink($productName, $cover['id_image'], 'cart_default');
            $tags['{img_big}'] = $this->context->link->getImageLink($productName, $cover['id_image'], 'home_default');
        }

        $productsHtml = (new ProductNotificationService())->getProductHtml($this->context->shop->id, $email_template->tpl);
        if(!empty($productsHtml)){
            $tags['{products_list}'] = str_replace(
                ['{product_cover}', '{product_cover_big}' , '{product_name}', '{product_quantity}', '{unit_price_tax_incl}'],
                [ $tags['{img}'], $tags['{img_big}'], $tags['{product_name}'], 1, $tags['{product_price}'] ],
                $productsHtml
            );
        }

        return $tags;
    }

    public function hookActionFrontControllerSetMedia()
    {
        if(empty($this->context->controller->php_self)
            || 'product' != $this->context->controller->php_self
        ){
            return;
        }

        $this->context->controller->registerJavascript('modules-freshmail', '/modules/' . $this->name . '/views/js/header.js');
    }
}