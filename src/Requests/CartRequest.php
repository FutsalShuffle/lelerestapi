<?php
namespace App\Requests;
use App\Contracts\Request;
use App\Response\Response;

class CartRequest implements Request
{

    public $add = false;
    public $delete = false;
    public $setQty = false;
    public $customization_id = 0;
    public $qty = 1;
    public $id_product = 0;
    public $id_product_attribute = 0;
    public $id_address_delivery = 0;

    /**
     * load
     *
     * @return LoginRequest
     */
    public static function load()
    {
        $e = new self();
        if (\Tools::getValue('add')) {
            $e->add = 1;
        }
        if (\Tools::getValue('delete')) {
            $e->delete = 1;
        }
        if (\Tools::getValue('setQty')) {
            $e->setQty = 1;
        }
        $e->id_product = \Tools::getValue('id_product', 0);
        $e->id_product_attribute = \Tools::getValue('id_product_attribute', 0);
        $e->qty = abs(\Tools::getValue('qty', 1));
        
        return $e;
    }
    
    public function validate()
    {
        $response = new Response();

        if (!$this->id_product) {
            $response->setError('id_product', 'Product is not specified');
        }

        if ($this->qty <= 0) {
            $response->setError('qty', 'Invalid quantity');
        }

        if ($response->hasErrors()) {
            $response->setResponseCode(400);
            $response->returnResponse();
        }
        return true;
    }
}