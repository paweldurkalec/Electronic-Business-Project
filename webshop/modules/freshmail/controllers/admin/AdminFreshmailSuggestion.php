<?php

require_once __DIR__.'/AdminFreshmailBase.php';

class AdminFreshmailSuggestionController extends ModuleAdminController
{
    const SUGGESTION_EMAIL = 'prestashop@freshmail.pl';

    protected $display = 'edit';

    public $bootstrap = true;

    public $lang = false;

    public function renderForm()
    {
        $this->submit_action = $this->display . $this->identifier;
        $this->show_form_cancel_button = false;

        $this->fields_form = [
            'legend' => [
                'title' => $this->module->l('Send suggestion about plugin'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->module->l('Email subject'),
                    'name' => 'email_subject',
                    //'autoload_rte' => true,
                    'lang' => false,
                    'required' => true,
                ],[
                    'type' => 'textarea',
                    'label' => $this->module->l('Description'),
                    'name' => 'content',
               //     'autoload_rte' => true,
                    'required' => true,
                ]
            ],
            'submit' => [
                'name' => 'submitFreshmailSuggestion',
                'title' => $this->module->l('Save'),
                'class' => 'btn btn-default pull-right'
            ],

        ];




        return $this->notification() . parent::renderForm() . $this->extendedInfo();
    }

    public function postProcess()
    {
        if(Tools::isSubmit('submitFreshmailSuggestion')){
            $content = trim(Tools::getValue('content'));
            $subject = trim(Tools::getValue('email_subject_1'));

            if(!empty($content)){
                if(empty($subject)){
                    $subject = 'Wiadomość dotycząca wtyczki PrestaShop';
                }

                $msg =<<<EOF
                Adres: {$_SERVER['HTTP_HOST']}<br>
                Wersja wtyczki: {$this->module->version}<br>
                <ln/>
                Wiadomość: {$content}
EOF;
                $result = Mail::send(
                    $this->context->language->id,
                    'suggestion',
                    $subject,
                    ['{msg}' => $msg],
                    self::SUGGESTION_EMAIL,
                    self::SUGGESTION_EMAIL,
                    Configuration::get('PS_SHOP_EMAIL'),
                    Configuration::get('PS_SHOP_EMAIL'),
                    null,
                    null,
                    _PS_MODULE_DIR_.'freshmail/mails/'
                );

                if($result){
                    $this->context->cookie->suggestion_sent = 1;
                    Tools::redirect(
                        $this->context->link->getAdminLink('AdminFreshmailSuggestion')
                    );
                }
            }


        }
        return parent::postProcess(); // TODO: Change the autogenerated stub
    }

    private function notification(){
        if(empty($this->context->cookie->suggestion_sent)){
            return '';
        }

        unset($this->context->cookie->suggestion_sent);
        $this->context->smarty->assign([
            'success' => $this->module->l('Thank for message. We will answer as soon as possible.')
        ]);


        $templateFile = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/alerts.tpl';
        return $this->context->smarty->fetch($templateFile);
    }

    private function extendedInfo()
    {
        $templateFile = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/suggestion.tpl';
        return $this->context->smarty->fetch($templateFile);

    }

}