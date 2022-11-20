<?php

namespace FreshMail\Discount;

use FreshMail\Entity\Cart;
use FreshMail\Repository\Carts;

class Percent extends AbstractDiscount{

    function apply(\Cart $cart, Cart $fmCart){
        if(!empty($fmCart->id_cart_rule)){
            return;
        }

        $cartRule = $this->addCartRule(
            $this->settings->discount_percent,
            $this->settings->discount_lifetime,
            'Discount for abandoned cart',
            $cart->id_currency
        );

        $fmCart->id_cart_rule = $cartRule->id;
        $fmCart->save();
    }

    public static function addCartRule($discount_percent, $discount_lifetime, $discount_name, $id_currency)
    {
        $cartRule = new \CartRuleCore();

        $cartRule->date_from = date("Y-m-d H:i:s");
        $cartRule->date_to = date('Y-m-d H:i:s', strtotime('+'.$discount_lifetime.' HOURS'));
        $cartRule->free_shipping = false;
        $cartRule->minimum_amount_currency = 1;
        $cartRule->reduction_percent = (int)$discount_percent;
        $cartRule->reduction_tax = true;
        $cartRule->reduction_currency = $id_currency;
        $cartRule->active = true;
        $cartRule->partial_use = false;
        $cartRule->code = 'fm_'.substr(md5(time()), 0,10);

        foreach (\Language::getLanguages() as $lang)
        {
            $name = \Module::getInstanceByName('freshmail')->l($discount_name);
            if(empty($name)){
                $name = 'FreshMail';
            }
            $cartRule->name[$lang['id_lang']] = $name;
        }
        $cartRule->save();

        return $cartRule;
    }
}