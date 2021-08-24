<?php
use App\Controllers\RestControllerAuth;

/**
 * LelerestapiCarriersModuleFrontController
 */
class LelerestapiCarriersModuleFrontController extends RestControllerAuth
{
    public function proccessGetMethod()
    {
        $cart = new Cart((int)$this->context->cart->id);
        $country = new Country((int)Tools::getValue('id_country', 1));
        $carriers = Carrier::getCarriersForOrder((int)$country->id_zone, null, $cart);
        $this->response->setResult('carriers', $carriers);
        return $this->response->returnResponse();
    }

    public function proccessPostMethod()
    {
        return $this->response->return403Error();
    }  
}