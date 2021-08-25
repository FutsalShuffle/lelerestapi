<?php

use App\Controllers\RestController;
use App\Requests\CategoryRequest;
/**
 * LelerestapiCategoryModuleFrontController
 */
class LelerestapiCategoryModuleFrontController extends RestController
{
    public $request;

    public function __construct()
    {
        parent::__construct();
        $this->request = new CategoryRequest();
        $this->request->load();
        if (!$this->request->id_category) {
            return $this->response->return404Error();
        }
    }
    
    /**
     * display
     *
     * @return void
     */
    public function proccessGetMethod()
    {
        $category = new Category((int)$this->request->id_category, false, $this->context->language->id);
        if (!$category->id) {
            return $this->response->return404Error();
        }
        $this->response->setResult('category', $category);
        $this->response->setResult('products', $category->getProducts($this->context->language->id, $this->request->p, $this->request->nbProducts, null, null, false, true, false, 0, false, null));
        return $this->response->returnResponse();
    }
    
}