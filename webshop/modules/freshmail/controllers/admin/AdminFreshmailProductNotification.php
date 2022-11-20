<?php

require_once __DIR__.'/AdminFreshmailBase.php';

class AdminFreshmailProductNotificationController extends AdminFreshmailBaseController
{
    const TPL_DIR = 'PrestaShop';

    const TPL_AVAILABLE_CATEGORY = 6;

    const TPL_DISCOUNT_CATEGORY = 7;

    public $display = 'edit';

    public $bootstrap = true;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        if(Tools::getIsset('ajax')){
            $method = Tools::getValue('action');

            if(method_exists($this,$method)){
                $this->$method();
            }
            die();
        }
    }

    private function deleteNotification(){
        $productNotification = new \FreshMail\Entity\ProductNotification((int)Tools::getValue('id_notification'));
        $productNotification->delete();

        echo json_encode([
           'status' => 'ok'
        ]);
    }

    public function renderForm()
    {
        $this->submit_action = $this->display . $this->identifier;
        $this->show_form_cancel_button = false;

        return parent::renderForm() . $this->availableForm() . $this->discountForm() . $this->extendedInfo();
    }

    public function postProcess()
    {
        parent::postProcess(); // TODO: Change the autogenerated stub

        if(!Tools::getIsset('submit_available') && !Tools::getIsset('submit_discount')){
            return;
        }

        if(Tools::getIsset('submit_available')){
            Configuration::updateValue(
                \FreshMail\ProductNotifications::CONFIG_KEY_AVAILABLE,
                Tools::getValue(FreshMail\ProductNotifications::CONFIG_KEY_AVAILABLE),
                false, null, $this->context->shop->id
            );

            $this->savePrompt(\FreshMail\ProductNotifications::CONFIG_KEY_AVAILABLE_PROMPT);
            $this->saveValues('available');
        } elseif(Tools::getIsset('submit_discount')){
            Configuration::updateValue(
                \FreshMail\ProductNotifications::CONFIG_KEY_DISCOUNT,
                Tools::getValue(\FreshMail\ProductNotifications::CONFIG_KEY_DISCOUNT),
                false, null, $this->context->shop->id
            );
            Configuration::updateValue(
                \FreshMail\ProductNotifications::CONFIG_KEY_DISCOUNT_TRIGGER,
                Tools::getValue(\FreshMail\ProductNotifications::CONFIG_KEY_DISCOUNT_TRIGGER),
                false, null, $this->context->shop->id
            );

            $this->savePrompt(\FreshMail\ProductNotifications::CONFIG_KEY_DISCOUNT_PROMPT);
            $this->saveValues('discount');
        }


        if(empty($this->errors)){
            Tools::redirect($this->context->link->getAdminLink('AdminFreshmailProductNotification'));
        }
    }

    private function availableForm()
    {
        $this->notificationForm('available', self::TPL_AVAILABLE_CATEGORY);
        $this->fields_form['legend'] = [
            'title' => $this->module->l('Product availability notification'),
        ];
        $this->fields_form['input'][1]['desc'] = $this->module->l('eg. Notify me when the product will be available again');

        array_unshift($this->fields_form['input'],
            [
                'type' => 'switch',
                'label' => $this->module->l('Enable product availability notification'),
                'name' => \FreshMail\ProductNotifications::CONFIG_KEY_AVAILABLE,
                'required' => true,
                'class' => 'switch prestashop-switch',
                'is_bool' => true,
                'values' => [
                    [
                        'id' => 'active_on_available',
                        'value' => 1,
                        'label' => $this->module->l('Enabled')
                    ],
                    [
                        'id' => 'active_off_available',
                        'value' => 0,
                        'label' => $this->module->l('Disabled')
                    ]
                ]
            ]
        );

        $this->fields_value[\FreshMail\ProductNotifications::CONFIG_KEY_AVAILABLE] =
            Configuration::get(\FreshMail\ProductNotifications::CONFIG_KEY_AVAILABLE, null, null, $this->context->shop->id);

        $this->fields_value['prompt_text'] = $this->getPrompt(\FreshMail\ProductNotifications::CONFIG_KEY_AVAILABLE_PROMPT);

        return  parent::renderForm();

    }

    private function discountForm()
    {
        $this->notificationForm('discount', self::TPL_DISCOUNT_CATEGORY);
        $this->fields_form['legend'] = [
            'title' => $this->module->l('Product discount notification'),
        ];
        $this->fields_form['input'][1]['desc'] = $this->module->l('eg. Notify me when the price will be lower.');

        array_unshift(
            $this->fields_form['input'],
            [
                'type' => 'text',
                'label' => $this->module->l('Send product price discount notification when discount is bigger than'),
                'name' =>  \FreshMail\ProductNotifications::CONFIG_KEY_DISCOUNT_TRIGGER,
                'required' => false,
                'class' => 'fixed-width-xs',
                'desc' => $this->module->l('In %')
            ]
        );
        array_unshift(
            $this->fields_form['input'],
            [
                'type' => 'switch',
                'label' => $this->module->l('Enable product price discount notification'),
                'name' => \FreshMail\ProductNotifications::CONFIG_KEY_DISCOUNT,
                'required' => true,
                'class' => 'switch prestashop-switch',
                'is_bool' => true,
                'values' => [
                    [
                        'id' => 'active_on_discount',
                        'value' => 1,
                        'label' => $this->module->l('Enabled')
                    ],
                    [
                        'id' => 'active_off_discount',
                        'value' => 0,
                        'label' => $this->module->l('Disabled')
                    ]
                ]
            ]
        );

        $this->fields_value[\FreshMail\ProductNotifications::CONFIG_KEY_DISCOUNT_TRIGGER] =
            Configuration::get(\FreshMail\ProductNotifications::CONFIG_KEY_DISCOUNT_TRIGGER,null, null, $this->context->shop->id);
        $this->fields_value[\FreshMail\ProductNotifications::CONFIG_KEY_DISCOUNT] =
            Configuration::get(\FreshMail\ProductNotifications::CONFIG_KEY_DISCOUNT,null, null, $this->context->shop->id);

        $this->fields_value['prompt_text'] = $this->getPrompt(\FreshMail\ProductNotifications::CONFIG_KEY_DISCOUNT_PROMPT);

        return  parent::renderForm();
    }

    private function notificationForm($type, $tplCategory){
        $this->submit_action = $this->display . $this->identifier;
        $this->show_form_cancel_button = false;

        $this->fields_form = [
            'legend' => [
                'title' => ''
            ],
            'input' => [
                [
                    'type' => 'select',
                    'label' => $this->module->l('Template'),
                    'desc' => $this->module->l('Lists of templates defined in the Freshmail application'),
                    'name' => 'tpl',
                    'multiple' => false,
                    'required' => true,
                    'options' => [
                        'query' => $this->getTpl($tplCategory), //$lists,
                        'id' => 'id_hash',
                        'name' => 'name'
                    ]
                ],[
                    'type' => 'text',
                    'label' => $this->module->l('Prompt text on button'),
                    'name' => 'prompt_text',
                    //'autoload_rte' => true,
                    'lang' => true,
                    'hint' => 'This text will appear on the product page',
                    'desc' => ''
                ],[
                    'type' => 'text',
                    'label' => $this->module->l('Email subject'),
                    'name' => 'email_subject',
                    //'autoload_rte' => true,
                    'lang' => true,
                ],[
                    'id' => $type,
                    'type' => 'textarea',
                    'label' => $this->module->l('Description'),
                    'name' => 'content',
                    'autoload_rte' => true,
                    'lang' => true,
                    'hint' => '{content}'
                ]
            ],
            'submit' => [
                'name' => 'submit_'.$type,
                'title' => $this->module->l('Save'),
                'class' => 'btn btn-default pull-right'
            ],
        ];

        $this->fields_value = $this->getValues($type);
    }

    public function getValues($type)
    {
        $item = \FreshMail\Entity\EmailTemplate::getByType($type);
        return [
            'tpl' => $item->tpl,
            'email_subject' => $item->email_subject,
            'content' => $item->content,
            'type' => $item->type
        ];
    }

    public function saveValues($type)
    {
        $item = \FreshMail\Entity\EmailTemplate::getByType($type);

        $item->tpl = Tools::getValue('tpl');
        $item->id_shop = $this->context->shop->id;
        foreach (Language::getIDs(false) as $id_lang) {
            if (isset($_POST['content_'.$id_lang])) {
                $item->content[$id_lang] = Tools::getValue('content_'.$id_lang);
            }
            if (isset($_POST['email_subject_'.$id_lang])) {
                $item->email_subject[$id_lang] = Tools::getValue('email_subject_'.$id_lang);
            }
        }

        $item->save();
    }


    public function savePrompt($type)
    {
        $prompt = [];
        foreach (Language::getIDs(false) as $id_lang) {
            $prompt[$id_lang] = Tools::getValue('prompt_text_'.$id_lang);
        }
        Configuration::updateValue(
            $type,
            $prompt,
            false, null, $this->context->shop->id
        );
    }

    public function getPrompt($type)
    {
        $prompt = [];
        foreach (Language::getIDs(false) as $id_lang) {
            $prompt[$id_lang] =  Configuration::get($type,$id_lang, null, $this->context->shop->id);
        }

        return $prompt;
    }

    public function getTpl($category)
    {
        parent::init();

        $tplList = [
            ['id_hash' => '', 'name' => $this->module->l('Choose')]
        ];
        if(!empty($this->freshmail) && $this->freshmail->check()){
            $this->freshmail->getEmailsTemplates(self::TPL_DIR);
            $tpls = $this->freshmail->getEmailsTemplates(self::TPL_DIR);
            \FreshMail\Tools::filterTplByCategory($tpls, $category);

            $tplList = array_merge($tplList, $tpls );
        }

        return $tplList;
    }

    private function extendedInfo()
    {
        $templateFile = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/product_notifications.tpl';
        return $this->context->smarty->fetch($templateFile);

    }
}