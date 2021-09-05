<?php
use App\Controllers\RestControllerAuth;

/**
 * LelerestapiOrderHistoryModuleFrontController
 */
class LelerestapiOrderHistoryModuleFrontController extends RestControllerAuth
{
    public function proccessGetMethod()
    {
        $history = Order::getCustomerOrders((int)$this->context->customer->id);
        if (!$history) {
            $this->response->setError('history', "Couldn't get history data");
            return $this->response->returnResponse();
        }
        $this->response->setResult('history', $history);
        return $this->response->returnResponse();
    }

    public function proccessPostMethod()
    {
        return $this->response->return403Error();
    }
    
}