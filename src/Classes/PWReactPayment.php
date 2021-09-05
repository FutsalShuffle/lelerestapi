<?php

class PWReactPayment extends ObjectModel
{
    public $id_pwreact_payment;
    public $name;
    public $description;
    public $module;
    public $submit_controller;
    
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'pwreact_payment',
        'primary' => 'id_pwreact_payment',
        'multilang' => false,
        'fields' => array(
            'description' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'required' => true),
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'required' => true),
            'module' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'required' => true),
            'submit_controller' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'required' => true),
        )
    );

    public static function getAll()
    {
        $sql = DB::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'pwreact_payment`');
        $pool = [];
        foreach ($sql as $key=>$item) {
            $pool[$key]['name'] = $item['name'];
            $pool[$key]['module']['action'] = '/module/'.$item['module'].'/'.$item['submit_controller'];
            $pool[$key]['module']['module_name'] = $item['module'];
            $pool[$key]['module']['call_to_action_text'] = $item['description'];
        }
        return $pool;
    }
}