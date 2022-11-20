<?php

namespace FreshMail\Entity;

use ObjectModel;

class ProductNotification extends ObjectModel
{
    public $id_shop;
    public $id_lang;
    public $id_product;
    public $id_product_attribute;
    public $active;
    public $email;
    public $type;

    public static $definition = [
        'table' => 'freshmail_product_notification',
        'primary' => 'id_freshmail_product_notification',
        'multilang' => false,
        'fields' => [
            'id_shop' => [
                'type' => self::TYPE_INT
            ],
            'id_lang' => [
                'type' => self::TYPE_INT
            ],
            'id_product' => [
                'type' => self::TYPE_INT
            ],
            'id_product_attribute' => [
                'type' => self::TYPE_INT
            ],
            'active' => [
                'type' => self::TYPE_BOOL
            ],
            'email' => [
                'type' => self::TYPE_STRING
            ],
            'type' => [
                'type' => self::TYPE_STRING,
            ],
        ]
    ];

    public function alreadyExists()
    {
        $query = (new \DbQuery())->select('count(*)')
            ->from(self::$definition['table'])
            ->where('email = "'.pSQL($this->email).'"')
            ->where('id_product = '.(int)$this->id_product)
            ->where('id_product_attribute = '.(int)$this->id_product_attribute)
            ->where('type = "'.pSQL($this->type).'"')
        ;

        return \Db::getInstance()->getValue($query);
    }


}

