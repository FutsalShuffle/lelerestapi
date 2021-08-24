<?php
namespace App\Controllers;
use App\Contracts\RestFrontController;
require_once dirname(__FILE__).'/../../classes/Main.php';
require_once dirname(__FILE__).'/../../classes/RestApiHelpers.php';
require_once dirname(__FILE__).'/../../classes/FavoriteProduct.php';

class RestControllerAuth extends RestFrontController
{
    public $result = [];
    public $user;

    public function __construct()
	{
		parent::__construct();
        $this->user = \MainRestApi::validateUser();
        $this->context->setCustomer(\MainRestApi::getUserObject());
        $this->context->setCart(\MainRestApi::getCustomerCartObject($this->context->customer));
        if (!$this->user || !$this->context->customer->id) {
            $this->response->return403Error();
        }
	}

    public function setResult($key, $value)
    {
        $this->result['success'] = 1;
        $this->result[$key] = $value;
    }

    public function setErrors($key, $value)
    {
        $this->result['success'] = 0;
        $this->result['errors'][$key] = $value;
    }
}