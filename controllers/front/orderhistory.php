<?php
use App\Controllers\RestControllerAuth;
use App\Exceptions\ExceptionNotFound;
use App\Exceptions\ExceptionNotAllowed;

/**
 * LelerestapiOrderHistoryModuleFrontController
 */
class LelerestapiOrderHistoryModuleFrontController extends RestControllerAuth
{
    public function proccessGetMethod()
    {
        $history = Order::getCustomerOrders((int)$this->context->customer->id);
        if (!$history) {
            ExceptionNotFound::init();
        }
        $this->response->setResult('history', $history);
        return $this->response->returnResponse();
    }

    public function proccessPostMethod()
    {
        return ExceptionNotAllowed::init();
    }
    
}