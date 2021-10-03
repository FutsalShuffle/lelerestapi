<?php
use App\Controllers\RestControllerAuth;
use App\Services\AddressService;
/**
 * LelerestapiCarriersModuleFrontController
 */
class LelerestapiCarriersModuleFrontController extends RestControllerAuth
{
    public function proccessGetMethod()
    {
        $data = (new AddressService($this->context))->getCustomerAddresses();
        return $this->response->returnJson((array)$data);
    }

    public function proccessPostMethod()
    {
        $data = (new AddressService($this->context))->createAddress();
        return $this->response->returnJson((array)$data);
    }
}