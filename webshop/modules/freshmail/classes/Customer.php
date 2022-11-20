<?php

namespace FreshMail;

class Customer
{
    public static function getLastOrder(\Customer $customer)
    {
        $query = (new \DbQuery())->select('*')
            ->from('orders')
            ->where('id_customer = '.(int)$customer->id)
            ->orderBy('date_add DESC');

        return \Db::getInstance()->getRow($query);
    }
}