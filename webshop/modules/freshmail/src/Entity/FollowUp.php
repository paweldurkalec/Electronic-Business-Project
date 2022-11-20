<?php


namespace FreshMail\Entity;

use FreshMail\Interfaces\TransactionEmail;
use ObjectModel;

class FollowUp extends ObjectModel implements TransactionEmail
{
    public $id;

    public $id_shop;
    public $id_follow_up;
    public $enable;
    public $tpl;
    public $send_after;
    public $email_subject;
    public $content;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'freshmail_follow_up',
        'primary' => 'id_freshmail_follow_up',
        'multilang' => true,
        'fields' => [
            'id_shop' => [
                'type' => self::TYPE_INT,
                'required' => true
            ],
            'id_follow_up' => [
                'type' => self::TYPE_INT,
                'required' => true
            ],
            'enable' => [
                'type' => self::TYPE_BOOL,
            ],
            'send_after' => [
                'type' => self::TYPE_INT,
                'required' => false
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

}