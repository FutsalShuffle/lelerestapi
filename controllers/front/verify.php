<?php

use App\Controllers\RestControllerAuth;
use App\Exceptions\ExceptionNotAllowed;
/**
 * LelerestapiVerifyModuleFrontController
 */
class LelerestapiVerifyModuleFrontController extends RestControllerAuth
{

    public function proccessGetMethod()
    {
        if ($this->user && $this->context->customer->id) {
            $this->response->setResult('customer', $this->user);
            $this->response->returnResponse();
        }
        return ExceptionNotAllowed::init(['customer' => 'Not logged in']);
    }
    public function proccessPostMethod()
    {
        return ExceptionNotAllowed::init();
    }
}