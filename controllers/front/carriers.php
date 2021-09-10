<?php
use App\Controllers\RestControllerAuth;
use App\Services\CarriersService;
/**
 * LelerestapiCarriersModuleFrontController
 */
class LelerestapiCarriersModuleFrontController extends RestControllerAuth
{
    public function proccessGetMethod()
    {
        $data = (new CarriersService($this->context))->getCarriers();
        return $this->response->returnJson($data);
    }

    public function proccessPostMethod()
    {
        (new CarriersService($this->context))->setCarrier();
        return $this->response->returnResponse();
    }
}