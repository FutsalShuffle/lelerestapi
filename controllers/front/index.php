<?php
use App\Controllers\RestController;
use App\Exceptions\ExceptionNotAllowed;
use App\Services\IndexService;

/**
 * LelerestapiCustomPageModuleFrontController
 */
class LelerestapiInitModuleFrontController extends RestController
{

    public function proccessGetMethod()
    {
        $data = (new IndexService($this->context))->getIndexPage();
        return $this->response->returnJson($data);
    }

    public function processPostMethod()
    {
        return ExceptionNotAllowed::init();
    }
}