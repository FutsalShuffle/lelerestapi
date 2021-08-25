<?php
require_once dirname(__FILE__).'/vendor/autoload.php';
require_once dirname(__FILE__).'/classes/vendor/autoload.php';

/**
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}
class Lelerestapi extends Module
{
    protected $config_form = false;

    const REST_AUTO_PAYMENTS = 'REST_AUTO_PAYMENTS';
    const REST_PRIVATE_KEY   = 'REST_PRIVATE_KEY';
    const REST_USE_JWT       = 'REST_USE_JWT';

    public function __construct()
    {
        $this->name = 'lelerestapi';
        $this->tab = 'administration';
        $this->version = '0.0.1';
        $this->author = 'andrele82';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l("Lele's REST API");
        $this->description = $this->l('REST API module for prestashop 1.6+');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        $this->installDB();

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function installDB()
    {
        $sql[] = "Db::getInstance()->execute('
        CREATE TABLE `'._DB_PREFIX_.'favorite_product` (
        `id_favorite_product` int(10) unsigned NOT NULL auto_increment,
        `id_product` int(10) unsigned NOT NULL,
        `id_customer` int(10) unsigned NOT NULL,
        `id_shop` int(10) unsigned NOT NULL,
        `date_add` datetime NOT NULL,
          `date_upd` datetime NOT NULL,
        PRIMARY KEY (`id_favorite_product`))
        ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8');";

        foreach ($sql as $db) {
            DB::getInstance()->execute($db);
        }

        Configuration::updateValue(self::REST_AUTO_PAYMENTS, 0);
        Configuration::updateValue(self::REST_PRIVATE_KEY, 'andrele82');
        Configuration::updateValue(self::REST_USE_JWT, 1);
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitLelerestapiModule')) == true) {
            $this->postProcess();
        }
        return $this->renderForm();
    }

        /**
     * getConfigForm
     * Форма в админке в настройках модуля
     * @return array
     */
    protected function getConfigForm()
    {
        $carriers = Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Use automatic payment methods detection (only for PS 1.7+)'),
                        'name' => self::REST_AUTO_PAYMENTS,
                        ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Private jwt key (is used for encrypting the session key)'),
                        'name' => self::REST_PRIVATE_KEY,
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Save'),
                ),
            ),   
        );
    }

    /**
     * renderForm
     * Форма prestashop для админки
     * @return void
     */
    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPwReactCartModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        foreach ($this->getConfigFormValues() as $key=>$value) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    public function getConfigFormValues()
    {
        return [
            self::REST_AUTO_PAYMENTS => Configuration::get(self::REST_AUTO_PAYMENTS),
            self::REST_PRIVATE_KEY   => Configuration::get(self::REST_PRIVATE_KEY),
            self::REST_USE_JWT       => Configuration::get(self::REST_USE_JWT),
        ];
    }
}
