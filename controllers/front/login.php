<?php
use App\Controllers\RestController;
use App\Requests\LoginRequest;

class LelerestapiLoginModuleFrontController extends RestController
{
    public $email;
    public $password;
    public $request;

    public function __construct()
    {
        parent::__construct();
        $this->request = LoginRequest::load();
    }

    public function proccessPostMethod()
    {
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