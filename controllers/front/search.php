<?php
use App\Controllers\RestController;
use App\Services\SearchService;
use App\Exceptions\ExceptionNotAllowed;

/**
 * LelerestapiCategoriesModuleFrontController
 */
class LelerestapiSearchModuleFrontController extends RestController
{
    public function proccessGetMethod()
    {
        $data = (new SearchService($this->context))->getProducts();
        return $this->response->returnJson($data);
    }

    public function proccessPostMethod()
    {
        return ExceptionNotAllowed::init();
    }
}