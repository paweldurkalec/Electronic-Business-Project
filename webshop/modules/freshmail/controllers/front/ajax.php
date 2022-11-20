<?php

class freshmailajaxModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    private $priceFormatter;


    function init()
    {
        parent::init();

        $jsonArr = ['status' => 'false', 'message' => 'Error in request'];

        if ($action = Tools::getValue('action')) {
            if (method_exists($this, $action)) {
                $jsonArr = $this->$action();
            }
        }

        die(json_encode($jsonArr));
    }

    private function getInfo()
    {
        if($this->context->customer->isLogged()){
            return [
                'is_logged' => true,
                'email' => $this->context->customer->email
            ];
        }
        $freshmailToken = md5(time());
        $this->context->cookie->fm_token = $freshmailToken;

        return [
            'is_logged' => false,
            'token' => $freshmailToken
        ];
    }

    private function saveInfo()
    {


        if(!$this->context->customer->isLogged() &&
            $this->context->cookie->fm_token != Tools::getValue('fm_token')
        ){
            return [
                'status' => 'false',
                'message' => $this->module->l('Error on reading request')
            ];;
        }

        $email = Tools::getValue('email');
        if(!Validate::isEmail($email)){
            return [
                'status' => 'false',
                'message' => $this->module->l('E-mail is not valid')
            ];
        }

        $idProductAttribute = 0;
        $groups = Tools::getValue('group');

        $idProduct = (int)Tools::getValue('id_product');;

        if (!empty($groups)) {
            $idProductAttribute = (int) Product::getIdProductAttributeByIdAttributes(
                $idProduct,
                $groups,
                true
            );
        }

        $product = new \Product($idProduct);

        $productNotifiaction = new \FreshMail\Entity\ProductNotification();
        $productNotifiaction->id_shop = $this->context->shop->id;
        $productNotifiaction->id_lang = $this->context->language->id;
        $productNotifiaction->email = pSQL($email);
        $productNotifiaction->id_product = $idProduct;
        $productNotifiaction->id_product_attribute = (int)$idProductAttribute;
        if( StockAvailable::getQuantityAvailableByProduct($idProduct, $idProductAttribute) <= 0 &&  !\Product::isAvailableWhenOutOfStock($product->out_of_stock)){
            $productNotifiaction->type = 'available';
        } else {
            $productNotifiaction->type = 'discount';
        }
        $productNotifiaction->active = 1;

        if(!$productNotifiaction->alreadyExists()){
            $productNotifiaction->save();
        }

        return [
            'status' => 'true',
            'message' => $this->module->l('Email saved')
        ];
    }
}