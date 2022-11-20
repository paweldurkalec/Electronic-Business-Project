<?php

namespace FreshMail\Service;

use FreshMail\Customer;
use FreshMail\Discount\Percent;
use FreshMail\Entity\Cart;
use FreshMail\Entity\EmailTemplate;
use FreshMail\Entity\FollowUp;
use FreshMail\FollowUps;
use FreshMail\Repository\FreshmailSettings;
use FreshMail\Sender\Sender;
use FreshMail\Sender\Service\CartData;
use FreshMail\Tools;

class FollowUpService
{
    private static $freshmailSetings = [];
    public function sendNotifications(\Customer $customer, EmailTemplate $object, $extra = [])
    {
        if(!\Validate::isLoadedObject($customer)){
            return;
        }

        if(empty(self::$freshmailSetings[$customer->id_shop])){
            self::$freshmailSetings[$customer->id_shop] = (new FreshmailSettings(\Db::getInstance()))->findForShop($customer->id_shop);
        }

        $tags = [];
        if(!empty($lastOrder = Customer::getLastOrder($customer))){
            $date = explode(' ', $lastOrder['date_add']);
            $tags['{last_order_date}'] = $date[0];
        }

        $discountType = \Configuration::get(FollowUps::CONFIG_KEY_DISCOUNT_TYPE.$extra['id_follow_up'],null, null, $customer->id_shop);
        if($discountType != FollowUps::DISCOUNT_NONE) {
            $tags = array_merge($tags, $this->prepareDiscount($discountType, $extra['id_follow_up'], $customer->id_shop ));
        }

        return (new Sender(self::$freshmailSetings[$customer->id_shop]->api_token))->send($customer, $object, $tags);
    }

    public function collectEmails($followUpId, $idShop, $date = null)
    {
        $key = FollowUps::CONFIG_KEY_ACTIVE.$followUpId;
        $sendAfter = (int)\Configuration::get(FollowUps::CONFIG_KEY_SEND_AFTER.$followUpId, null, null, $idShop);
        if(!\Configuration::get($key, null, null, $idShop)
            || $sendAfter < 1){
            return [];
        }
        $date = empty($date) ? date('Y-m-d') : date('Y-m-d', strtotime($date));

        $query = (new \DbQuery())->select('*')
            ->from('customer', 'c')
            ->leftJoin('orders', 'o', 'c.id_customer = o.id_customer')

            ->where(
                'c.id_shop = '.(int)$idShop.' AND ABS(DATEDIFF("'.$date.'", o.date_add)) = ' . (int)$sendAfter
            )
            ->where('c.id_customer NOT IN (
                SELECT id_customer FROM PREFIX_freshmail_follow_up_sended WHERE follow_up = "follow_up'.(int)$followUpId.'" 
                AND id_shop = '.(int)$idShop.' AND DATE(date_add) = CURDATE()
            )')
            ->groupBy('c.id_customer')
            ->orderBy('id_order DESC')
        ;

        $query = str_replace('PREFIX_',_DB_PREFIX_, $query);
        return \Db::getInstance()->executeS($query);
    }

    public function saveFollowUpInfo($followUpId, $idShop, \Customer $customer){
        \Db::getInstance()->insert(
            'freshmail_follow_up_sended',
            [
                'follow_up' => 'follow_up'.(int)$followUpId,
                'id_customer' => (int)$customer->id,
                'id_shop' => (int)$idShop,
                'email' => pSQL($customer->email)
            ]
        );
    }

    public function prepareDiscount($discountType, $followUpId, $idShop){
        $code = '';
        $validto = '';

        if(FollowUps::DISCOUNT_CODE == $discountType){
            $fmCart = new Cart();
            $code = \Configuration::get(FollowUps::CONFIG_KEY_DISCOUNT_CODE . $followUpId, null, null, $idShop);
            $fmCart->discount_code = $code;
            $validto = CartData::getDiscountValidTo($fmCart);
        } elseif(FollowUps::DISCOUNT_PERCENT == $discountType) {
            $cartRule = Percent::addCartRule(
                \Configuration::get(FollowUps::CONFIG_KEY_DISCOUNT_PERCENT . $followUpId, null, null, $idShop),
                \Configuration::get(FollowUps::CONFIG_KEY_DISCOUNT_LIFETIME . $followUpId, null, null, $idShop),
                'Discount for follow up',
                \Configuration::get('PS_CURRENCY_DEFAULT', null, null, $idShop)
            );
            $code = $cartRule->code;
            $validto = $cartRule->date_to;
        }

        return [
            '{discount_code}' => $code,
            '{cartrule_validto}' => $validto,
        ];
    }
}