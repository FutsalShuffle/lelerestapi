<?php
use App\Controllers\RestController;

/**
 * LelerestapiRegisterModuleFrontController
 */
class LelerestapiRegisterModuleFrontController extends RestController
{
    public function proccessPostMethod()
    {
        $firstname = Tools::getValue('firstname');
        $lastname  = Tools::getValue('lastname');
        $email     = Tools::getValue('email');
        $password  = Tools::getValue('password');
        if (!$firstname || !$lastname) {
            $this->response->setError('firstname', 'Please enter your name');
        }
        if (!$email) {
            $this->response->setError('email', 'Please enter valid email');
        }
        if (!$password) {
            $this->response->setError('password', 'Please enter password');
        }
        if ($this->response->hasErrors())  {
            return $this->response->returnResponse();
        }
        $customer = new Customer();
        if ($customer->getByEmail($email) && $customer->id) {
            $this->response->setError('email', 'This email is already taken');
            return $this->response->returnResponse();
        }
        $customer->firstname = $firstname;
        $customer->lastname  = $lastname;
        $customer->email     = $email;
        $customer->passwd    = Tools::encrypt($password);
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