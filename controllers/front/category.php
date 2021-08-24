<?php

use App\Controllers\RestController;
/**
 * LelerestapiCategoryModuleFrontController
 */
class LelerestapiCategoryModuleFrontController extends RestController
{
    public $category_id;
    public $p = 1;
    public $nbProducts = 15;

    public function __construct()
    {
        parent::__construct();
        $this->category_id = Tools::getValue('id_category', 0);
        if (!$this->category_id) {
            return $this->response->return404Error();
        }
        $this->p = (int)Tools::getValue('p', 1);
        $this->nbProducts = (int)Tools::getValue('nbProducts', 15);
    }
    
    /**
     * display
     *
     * @return void
     */
    public function proccessGetMethod()
    {
        $category = new Category((int)$this->category_id, false, $this->context->language->id);
        if (!$category->id) {
            return $this->response->return404Error();
        }
        $this->response->setResult('category', $category);
        $this->response->setResult('products', $category->getProducts($this->context->language->id, $this->p, $this->nbProducts, null, null, false, true, false, 0, false, null));
        return $this->response->returnResponse();
    }
    
}