<?php
use App\Controllers\RestController;
use App\Requests\LoginRequest;
use App\Classes\RestApiHelpers;
use App\Exceptions\ExceptionInvalidData;
use App\Exceptions\ExceptionNotAllowed;

class LelerestapiLoginModuleFrontController extends RestController
{
    public $request;

    public function proccessPostMethod()
    {
        $this->request = LoginRequest::load();
        $customer = new Customer();
        $errors = $this->request->validate();
        if (count($errors)) {
            ExceptionInvalidData::init($errors);
        }
        if (!$customer->getByEmail($this->request->email, $this->request->password) && !$customer->id) {
            ExceptionInvalidData::init(['login' => 'Email or password is not correct']);
        }
        $this->response->setResult('customer', RestApiHelpers::CustomerToArray($customer, true));
        return $this->response->returnResponse();
    }

    public function proccessGetMethod()
    {
        return ExceptionNotAllowed::init();
    }
}