<?php


namespace FreshMail\Entity;

use FreshMail\Interfaces\TransactionEmail;
use ObjectModel;

class EmailTemplate extends ObjectModel implements TransactionEmail
{
    public $id;

    public $id_shop;
    public $type;
    public $tpl;
    public $email_subject;
    public $content;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'freshmail_email_template',
        'primary' => 'id_freshmail_email_template',
        'multilang' => true,
        'fields' => [
            'id_shop' => [
                'type' => self::TYPE_INT,
                'required' => true
            ],
            'type' => [
                'type' => self::TYPE_STRING,
                'required' => true
            ],
            'tpl' => [
                'type' => self::TYPE_STRING,
                'required' => false
            ],
            'email_subject' => [
                'lang' => true,
                'type' => self::TYPE_STRING,
            ],
            'content' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
            ],

        ],
    ];


    public function getTemplate()
    {
        return $this->tpl;
    }

    public function getSubject($idLang)
    {
        return $this->email_subject[$idLang];
    }

    public function getContent($idLang)
    {
        return $this->content[$idLang];
    }

    public static function getByType($type, $id_lang = null, $id_shop = 0){
        $query = (new \DbQuery())->select(self::$definition['primary'])
            ->from(self::$definition['table'])
            ->where('`type` = "'.pSQL($type).'"');

        if(!empty($id)) {
            $query->where('`id_shop` = ' . (int)$id_shop);
        }

        $id = \Db::getInstance()->getValue($query);

        $object = new static($id, $id_lang);
        if(!\Validate::isLoadedObject($object)){
            $object->type = $type;
        }

        return $object;
    }
}