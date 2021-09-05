<?php

use App\Controllers\RestController;
use App\Services\CategoryService;
/**
 * LelerestapiCategoryModuleFrontController
 */
class LelerestapiCategoryModuleFrontController extends RestController
{
    public $request;

    public function proccessGetMethod()
    {
        $data = (new CategoryService($this->context))->getCategoryData();
        return $this->response->returnJson($data);
    }
    
}