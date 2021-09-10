<?php
use App\Controllers\RestController;
use App\Services\CategoriesService;
/**
 * LelerestapiCategoriesModuleFrontController
 */
class LelerestapiCategoriesModuleFrontController extends RestController
{
    public static $cache;

    public function proccessGetMethod()
    {
        $data = (new CategoriesService($this->context))->getCategories();
        return $this->response->returnJson($data);
    }
}