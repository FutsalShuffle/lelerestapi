<?php
use App\Controllers\RestController;
use App\Services\ProductService;
use App\Exceptions\ExceptionNotAllowed;
/**
 * LelerestapiCategoryModuleFrontController
 */
class LelerestapiProductModuleFrontController extends RestController
{
    public function proccessGetMethod()
    {
        $data = (new ProductService($this->context))->getProduct();
        return $this->response->returnJson($data);
    }
    
    public function proccessPostMethod()
    {
        return ExceptionNotAllowed::init();
    }
    
}