<?php

use App\Controllers\RestControllerAuth;

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
        
        $this->response->setError('customer', 'Not logged in');
        $this->response->returnResponse();
    }
    public function proccessPostMethod()
    {
        return $this->response->return403Error();
    }
}