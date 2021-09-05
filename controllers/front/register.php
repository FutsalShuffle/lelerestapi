<?php
use App\Controllers\RestController;
use App\Exceptions\ExceptionNotAllowed;
use App\Services\RegisterService;
/**
 * LelerestapiRegisterModuleFrontController
 */
class LelerestapiRegisterModuleFrontController extends RestController
{
    public function proccessPostMethod()
    {
        $data = (new RegisterService($this->context))->register();
        return $this->response->returnJson($data);
    }

    public function proccessGetMethod()
    {
        return ExceptionNotAllowed::init();
    }
}