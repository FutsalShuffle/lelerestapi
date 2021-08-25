<?php
use App\Controllers\RestController;
use App\Requests\RegisterRequest;
/**
 * LelerestapiRegisterModuleFrontController
 */
class LelerestapiRegisterModuleFrontController extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->request = RegisterRequest::load();
        $this->request->validate();
    }

    public function proccessPostMethod()
    {
        $customer = new Customer();
        if ($customer->getByEmail($this->request->email) && $customer->id) {
            $this->response->setError('email', 'This email is already taken');
            return $this->response->returnResponse();
        }
        $customer->firstname = $this->request->firstname;
        $customer->lastname  = $this->request->lastname;
        $customer->email     = $this->request->email;
        $customer->passwd    = Tools::encrypt($this->request->password);
        $customer->deleted   = false;
        if ($customer->add()) {
            $this->response->setResult('customer', RestApiHelpers::CustomerToArray($customer, true));
            return $this->response->returnResponse();
        }
        $this->response->setError('global', 'Unknown error');
        $this->response->setResponseCode(500);
        return $this->response->returnResponse();
    }

    public function proccessGetMethod()
    {
        $this->response->return403Error();
    }
}