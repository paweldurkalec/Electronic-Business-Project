<?php

namespace FreshMail\Service;

use FreshMail\Entity\EmailTemplate;
use FreshMail\Entity\ProductNotification;
use FreshMail\Freshmail;
use FreshMail\Repository\FreshmailSettings;
use FreshMail\Sender\Sender;

class ProductNotificationService
{
    private static $freshmailSetings = [];
    public function sendNotifications($email, $id_lang, $id_shop, EmailTemplate $object, $tags = [])
    {

        if(empty(self::$freshmailSetings[$id_shop])){
            self::$freshmailSetings[$id_shop] = (new FreshmailSettings(\Db::getInstance()))->findForShop($id_shop);
        }

        $customer = new \Customer();
        $customer->email = $email;
        $customer->firstname = '';
        $customer->id_lang = $id_lang;

        return (new Sender(self::$freshmailSetings[$id_shop]->api_token))->send($customer, $object, $tags);
    }

    public function getProductHtml($id_shop, $template_hash) : string
    {
        if(empty(self::$freshmailSetings[$id_shop])){
            self::$freshmailSetings[$id_shop] = (new FreshmailSettings(\Db::getInstance()))->findForShop($id_shop);
        }
        $freshmailApi = new Freshmail(self::$freshmailSetings[$id_shop]->api_token);

        return $freshmailApi->getProductHtml($template_hash);
    }

    public function collectEmails($type, $id_product, $id_product_attribute )
    {
        $query = (new \DbQuery())->select('distinct(email) as email, id_lang')
            ->from(ProductNotification::$definition['table'])
            ->where('`type` = "'.pSQL($type).'"')
            ->where('id_product = '.(int)$id_product)
            ->where('id_product_attribute = '.(int)$id_product_attribute)
            ->where('active = 1 ')
        ;
        $query = str_replace('PREFIX_',_DB_PREFIX_, $query);
        return \Db::getInstance()->executeS($query);
    }

    public function saveSendDiscountNotification($email, $id_product, $id_product_attribute, $price)
    {
        \Db::getInstance()->insert(
            'freshmail_product_notification_log',
            [
                'email' => pSQL($email),
                'id_product' => (int)$id_product,
                'id_product_attribute' => (int)$id_product_attribute,
                'product_price' => $price,
                'type' => 'discount',
            ]
        );
    }

    public function saveSendAvailabilityNotification($email, $id_product, $id_product_attribute, $id_shop)
    {
        \Db::getInstance()->insert(
            'freshmail_product_notification_log',
            [
                'email' => pSQL($email),
                'id_product' => (int)$id_product,
                'id_product_attribute' => (int)$id_product_attribute,
                'id_shop' => (int)$id_shop,
                'type' => 'available',
            ]
        );
    }

    public function enableAvailabilityNotification($id_product, $id_product_attribute)
    {
        \Db::getInstance()->update(
            ProductNotification::$definition['table'],
            [
                'active' => 1
            ],
            'id_product = '.(int)$id_product
            . ' AND id_product_attribute = '.(int)$id_product_attribute
            . ' AND type = "available"'
        );
    }
    public function disableAvailabilityNotification($email, $id_product, $id_product_attribute)
    {
        \Db::getInstance()->update(
            ProductNotification::$definition['table'],
            [
                'active' => 0
            ],
            'id_product = '.(int)$id_product
                . ' AND id_product_attribute = '.(int)$id_product_attribute
                . ' AND type = "available"'
                . ' AND email = "'.pSQL($email).'"'
        );
    }
    public function deleteNotification($email, $id_product, $id_product_attribute, $id_shop, $type)
    {
        \Db::getInstance()->delete(
            ProductNotification::$definition['table'],
            'id_product = '.(int)$id_product
            . ' AND id_product_attribute = '.(int)$id_product_attribute
            . ' AND type = "'.pSQL($type).'"'
            . ' AND email = "'.pSQL($email).'"'
            . ' AND id_shop = "'.(int)($id_shop).'"'
        );
    }

    public function deleteAvailabilityNotification($email, $id_product, $id_product_attribute, $id_shop)
    {
        $this->deleteNotification($email, $id_product, $id_product_attribute, $id_shop,'available');
    }

    public function deleteDiscountNotification($email, $id_product, $id_product_attribute, $id_shop)
    {
        $this->deleteNotification($email, $id_product, $id_product_attribute, $id_shop,'discount');
    }


}