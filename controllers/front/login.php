<?php
use App\Controllers\RestController;

class LelerestapiLoginModuleFrontController extends RestController
{
    public $email;
    public $password;

    public function __construct()
    {
        parent::__construct();
        $this->email = Tools::getValue('email');
        $this->password = Tools::getValue('password');
    }

    public function proccessPostMethod()
    {
        if (!Validate::isEmail($this->email)) {
            $this->response->setError('email', 'Please enter valid email');
        }
        if (!$this->password) {
            $this->response->setError('password', 'Please enter password');
        }
        if (!empty($this->result['errors'])) $this->ajaxDie(Tools::jsonEncode($this->result));
        $customer = new Customer();
        if (!$customer->getByEmail($this->email, $this->password) && !$customer->id) {
            $this->response->setError('customer', 'Email or password is not correct');
        }
        if ($this->response->hasErrors()) return $this->response->returnResponse();
        $this->response->setResult('customer', RestApiHelpers::CustomerToArray($customer, true));
        return $this->response->returnResponse();
    }

    public function proccessGetMethod()
    {
        return $this->response->return403Error();
    }
}