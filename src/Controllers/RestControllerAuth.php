<?php
namespace App\Controllers;
use App\Contracts\RestFrontController;
use App\Classes\MainRestApi;
use App\Exceptions\ExceptionNotAllowed;

class RestControllerAuth extends RestFrontController
{
    public $user;

    public function __construct()
	{
		parent::__construct();
        $this->user = MainRestApi::validateUser();
        $customer = MainRestApi::getUserObject();
        if (!$customer) ExceptionNotAllowed::init();
        $this->context->setCustomer($customer);
        $cart = MainRestApi::getCustomerCartObject($this->context->customer);
        $this->context->setCart($cart);
        $this->context->cookie->id_cart = $cart->id;
        if (!$this->user || !$this->context->customer->id) {
            ExceptionNotAllowed::init();
        }
	}
}