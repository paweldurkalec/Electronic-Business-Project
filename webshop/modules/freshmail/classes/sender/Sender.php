<?php

namespace FreshMail\Sender;

use FreshMail\Freshmail;
use FreshMail\FreshmailApiV3;
use FreshMail\Interfaces\TransactionEmail;
use FreshMail\Repository\Birthdays;
use FreshMail\Sender\Service\CartData;
use FreshMail\TransactionalEmail;

class Sender
{
    private $bearer_token;

    public function __construct($bearer_token)
    {
        $this->bearer_token = $bearer_token;
    }

    private function getMailHtml(TransactionEmail $object) : string
    {
        $tpl = $object->getTemplate();
        if(!empty($tpl)){
            $fm = new Freshmail($this->bearer_token);
            $html = $fm->getTemplateHtml($tpl);

            if(!empty($html)){
                return $html;
            }
        }

        return '';
    }

    public function send(\Customer $customer, TransactionEmail $object, $tags = []) : bool
    {
        $shop = new \Shop($object->id_shop);

        $str_replace  = [
            'search' => [
                '{firstname}',
                '{lastname}',
                '{content}',
                '{shop_url}',
                '{company_name}',
                '{company_address}',

            ],
            'replace' => [
                $customer->firstname,
                $customer->lastname,
                $object->getContent($customer->id_lang),
                $shop->getBaseURL(),
                \Configuration::get('PS_SHOP_NAME', $customer->id_lang, null, $shop->id),
                CartData::getShopAddress(),
            ],
        ];

        foreach ($tags as $search => $replace){
            $str_replace['search'][] = $search;
            $str_replace['replace'][] = $replace;
        }

        $html = str_replace(
            $str_replace['search'],
            $str_replace['replace'],
            $this->getMailHtml($object)
        );

        $recipient = new Email($customer->email, $customer->firstname);
        $sender = new Email(\Configuration::get('PS_SHOP_EMAIL'), $shop->name);

        $fmApi = new FreshmailApiV3($this->bearer_token);

        return $fmApi->sendTransactionalEmail(
            new TransactionalEmail($recipient, $sender, $object->getSubject($customer->id_lang), $html)
        );
    }

}