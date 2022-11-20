<?php

class AdminFreshmailProductNotificationListController extends ModuleAdminController
{
    public $bootstrap = true;
    public $table = 'freshmail_product_notification';
    public $identifier = 'id_product';
    public $lang = false;
    public $actions = ['view'];
    public $list_no_link = true;

    public function renderList()
    {

       $this->deleted = false;
       $this->colorOnBackground = false;
       $this->multishop_context = Shop::CONTEXT_ALL;
       $this->imageType = 'gif';
       $this->fieldImageSettings = [
           'name' => 'icon',
           'dir' => 'os',
       ];


       $this->display = 'list';
       parent::__construct();

        $this->fields_list = [
            'product_name' => [
                'title' => $this->module->l('Product name'),
                'callback' => 'displayProduct_name'

            ],
            'type' => [
                'title' => $this->module->l('Type'),
                'callback' => 'displayType'
            ],
            'num_items' => [
                'title' => $this->module->l('Count'),
            ],
        ];

        $this->_select .= 'count(*) as num_items, "" as product_name';

        $this->_orderBy = 'num_items';
        $this->_defaultOrderWay = 'DESC';
        $this->_group = ' GROUP BY id_product, id_product_attribute, type ';

        return parent::renderList(); // TODO: Change the autogenerated stub
   }

    public function displayViewLink($token, $id, $name = null)
    {
        $link = $this->context->link->getAdminLink('AdminProducts', false, ['id_product' => $id]).'#tab-hooks';
        return '<a target="_blank" href="'.$link.'" class="btn btn-default" title="Zobacz"><i class="icon-search-plus"></i>Zobacz</a>';
    }

    public function displayType($value, $row){
        switch ($value){
            case 'available':
                return $this->module->l('Availability');
            case 'discount':
                return $this->module->l('Discount');
        }
    }

    public function displayProduct_name($value, $row){
        return Product::getProductName($row['id_product'], $row['id_product_attribute']);
    }

}