<?php
use App\Controllers\RestController;
use App\Services\LoginService;
use App\Exceptions\ExceptionNotAllowed;

class LelerestapiLoginModuleFrontController extends RestController
{
    public function proccessPostMethod()
    {
        $data = (new LoginService($this->context))->auth();
        return $this->response->returnJson($data);
    }

    public function proccessGetMethod()
    {
        return ExceptionNotAllowed::init();
    }
}