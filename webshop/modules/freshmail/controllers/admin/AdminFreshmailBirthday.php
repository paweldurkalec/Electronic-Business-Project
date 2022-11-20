<?php

require_once __DIR__.'/AdminFreshmailBase.php';

class AdminFreshmailBirthdayController extends AdminFreshmailBaseController
{
    const TPL_DIR = 'PrestaShop';

    const TPL_CATEGORY = 4;

    private $freshmailBirthday;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'edit';

        parent::__construct();

        /*$this->template = 'cart_config.tpl';
        $this->override_folder = '/';*/
        $this->freshmailBirthday = (new \FreshMail\Repository\Birthdays(Db::getInstance()))->findForShop($this->context->shop->id);
    }

    public function init()
    {
        parent::init();
        if(Tools::getIsset('send')){
            require_once __DIR__.'/../../cron/birthday.php';
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminFreshmailBirthday').'&sent');
        }

        if(Tools::getIsset('sent')){
            $this->confirmations[] = $this->module->l('Emails sent');
        }
    }

    public function renderForm()
    {
        $this->submit_action = $this->display . $this->identifier;
        $this->show_form_cancel_button = false;

        $this->fields_form = [
            'legend' => [
                'title' => $this->module->l('Birthday e-mails'),
            ],
            'input' => [
                [
                    'type' => 'switch',
                    'label' => $this->module->l('Enable'),
                    'name' => 'birthday',
                    'required' => true,
                    'class' => 'switch prestashop-switch',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled')
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled')
                        ]
                    ],
                ],
            ],
            'submit' => [
                'name' => 'submitFreshmailBirthday',
                'title' => $this->module->l('Save'),
                'class' => 'btn btn-default pull-right'
            ],



        ];

        $this->fields_value['birthday'] = $this->freshmailBirthday->enable && Validate::isLoadedObject($this->freshmailBirthday);
        if($this->fields_value['birthday']){
            $this->extendForm();
        }

        return parent::renderForm() . $this->extendedInfo();
    }

    public function postProcess()
    {
        if(isset($_POST['submitFreshmailBirthday'])){
            $this->freshmailBirthday->enable = (bool)Tools::getValue('birthday');

            if(isset($_POST['birthday_tpl'])){
                $this->freshmailBirthday->tpl = Tools::getValue('birthday_tpl');
            }
            foreach (Language::getIDs(false) as $id_lang) {
                if (isset($_POST['content_'.$id_lang])) {
                    $this->freshmailBirthday->content[$id_lang] = $_POST['content_'.$id_lang];
                }
                if (isset($_POST['email_subject_'.$id_lang])) {
                    $this->freshmailBirthday->email_subject[$id_lang] = $_POST['email_subject_'.$id_lang];
                }
            }

            if(!Validate::isLoadedObject($this->freshmailBirthday)){
                $this->freshmailBirthday->id_shop = $this->context->shop->id;
            }

            $this->freshmailBirthday->save();
        }
        return parent::postProcess(); // TODO: Change the autogenerated stub
    }


    public function run()
    {
        if(Tools::getIsset('ajax')){
            return $this->ajax();
        }
        return parent::run();
    }

    private function extendForm(){
        $this->warnings[] = $this->module->l('Full functionality requires setting a periodic task: ').' '
            . $this->context->link->getBaseLink().'modules/freshmail/cron/birthday.php?token='.$this->module->getCronToken()
            . '<br>' .$this->module->l('or use cli').': '. _PS_MODULE_DIR_ . 'freshmail/cron/birthday.php'
        ;
        $this->fields_form['input'][] = [
            'type' => 'select',
            'label' => $this->module->l('Template'),
            'desc' => $this->module->l('Lists of templates defined in the Freshmail application'),
            'name' => 'birthday_tpl',
            'multiple' => false,
            'required' => true,
            'options' => [
                'query' => $this->getTpl(), //$lists,
                'id' => 'id_hash',
                'name' => 'name'
            ]
        ];


        $this->fields_form['input'][] = [
            'type' => 'text',
            'label' => $this->module->l('Email subject'),
            'name' => 'email_subject',
            //'autoload_rte' => true,
            'lang' => true,
        ];
        $this->fields_form['input'][] = [
            'type' => 'textarea',
            'label' => $this->module->l('Description'),
            'name' => 'content',
            'autoload_rte' => true,
            'lang' => true,
            'hint' => $this->module->l('{content}')
        ];

        $this->fields_form['buttons'] = [
            [
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminFreshmailBirthday').'&send',
                'title' => $this->module->l("Send to today's birthday people"),
                'icon' => 'process-icon-refresh'
            ]
        ];

        $this->fields_value['birthday_tpl'] = $this->freshmailBirthday->tpl;
        $this->fields_value['content'] = $this->freshmailBirthday->content;
        $this->fields_value['email_subject'] = $this->freshmailBirthday->email_subject;

    }

    private function extendedInfo()
    {
        $templateFile = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/birthday.tpl';
        return $this->context->smarty->fetch($templateFile);

    }

    public function getTpl()
    {
        parent::init();

        $tplList = [
            ['id_hash' => '', 'name' => $this->module->l('Choose')]
        ];
        if(!empty($this->freshmail) && $this->freshmail->check()){
            $this->freshmail->getEmailsTemplates(self::TPL_DIR);
            $tpls = $this->freshmail->getEmailsTemplates(self::TPL_DIR);
            \FreshMail\Tools::filterTplByCategory($tpls, self::TPL_CATEGORY);

            $tplList = array_merge($tplList, $tpls );
        }

        return $tplList;
    }
}