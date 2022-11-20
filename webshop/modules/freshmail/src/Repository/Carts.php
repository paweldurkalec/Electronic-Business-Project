<?php

namespace FreshMail\Repository;

use Freshmail\Entity\AbandonedCartSettings;
use FreshMail\Entity\Cart;
use Freshmail\Entity\FreshmailSetting;

class Carts extends AbstractRepository {

    public function collectNotifyCarts(AbandonedCartSettings $abandonSettings)
    {
        if(!$abandonSettings->enabled){
            return [];
        }

        $whereNot = sprintf(
            '%s NOT IN (SELECT %s FROM %s)',
            Cart::$definition['primary'],Cart::$definition['primary'],_DB_PREFIX_.'freshmail_cart_notify'
        );

        $query = new \DbQuery();
        $query
            ->select('max(fc.id_cart) as id_cart, c.id_customer')
            ->from(\CartCore::$definition['table'], 'c')
            ->innerJoin(Cart::$definition['table'], 'fc', 'c.id_cart = fc.id_cart')
            ->where(sprintf('c.date_upd < (NOW() - INTERVAL %s HOUR)', $abandonSettings->send_after) )
            ->where(str_replace('PREFIX_', _DB_PREFIX_,'c.id_cart NOT IN (SELECT id_cart FROM PREFIX_orders)'))
            ->where($whereNot)
            ->where(str_replace('PREFIX_', _DB_PREFIX_, '    
                    c.date_upd > ( SELECT max(date_add) FROM PREFIX_orders o WHERE o.id_customer  = c.id_customer ) 
                    OR  0 = ( SELECT count(*) FROM PREFIX_orders o WHERE o.id_customer  = c.id_customer )')
            )
            ->groupBy('c.id_customer')
        ;

        $carts = $this->db->executeS($query);
        return array_column($carts, 'id_cart');
    }


    public function getByToken(\Cart $cart): Cart
    {
        $query = new DbQuery();
        $query
            ->select(Cart::$definition['primary'])
            ->from(Cart::$definition['table'])
            ->where('id_cart = '.$cart->id);

        return new Cart($this->db->getValue($query));
    }

    public function getByCart(\Cart $cart): Cart
    {
        $query = new \DbQuery();
        $query
            ->select(Cart::$definition['primary'])
            ->from(Cart::$definition['table'])
            ->where('id_cart = ' . $cart->id);

        return new Cart($this->db->getValue($query));
    }

    public function getByHash($hash): Cart
    {
        $query = new \DbQuery();
        $query
            ->select(Cart::$definition['primary'])
            ->from(Cart::$definition['table'])
            ->where('cart_token = "' . pSQL($hash).'"');

        return new Cart($this->db->getValue($query));
    }


}