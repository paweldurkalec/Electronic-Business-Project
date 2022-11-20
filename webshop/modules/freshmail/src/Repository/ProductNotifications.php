<?php

namespace FreshMail\Repository;

use DbQuery;
use FreshMail\Entity\ProductNotification;

class ProductNotifications extends AbstractRepository
{
    public function getActive($idProduct, $type){
        $query = new DbQuery();
        $query
            ->select('*')
            ->from(ProductNotification::$definition['table'])
            ->where('id_product = '.(int)$idProduct)
            ->where('type = "'.pSQL($type).'"')
            ->orderBy('date_add DESC')
        ;

        return \Db::getInstance()->executeS($query);
    }

    public function getActiveDiscount($idProduct){
        return $this->getActive($idProduct, 'discount');
    }

    public function getActiveAvailable($idProduct){
        return $this->getActive($idProduct, 'available');
    }

    public function deleteByIdProduct($idProduct){
        $this->db->delete(
            ProductNotification::$definition['table'],
            'id_product = '.(int)$idProduct
        );
    }

}