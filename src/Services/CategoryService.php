<?php
namespace App\Services;
use App\Requests\CategoryRequest;
use App\Exceptions\ExceptionNotFound;
use App\Exceptions\ExceptionInvalidData;

class CategoryService
{
    public $context;

    public function __construct($context)
    {
        $this->context = $context;
    }
    
    /**
     * get Category 
     * @return array
     */
    public function getCategoryData()
    {
        $request = CategoryRequest::load();
        $errors = $request->validate();

        if (count($errors)) {
            ExceptionInvalidData::init($errors);
        }

        $category = new \Category((int)$request->id_category, $this->context->language->id);
        if (!$category->id) {
            ExceptionNotFound::init();
        }

        $products = $category->getProducts($this->context->language->id, $request->p, $request->nbProducts, null, null, false, true, false, 0, false, null);
        foreach ($products as &$product) {
            $product['price_display'] = \Tools::displayPrice($product['price']);
        }

        return [
            'products' => $products,
            'children' => \Category::getChildren($category->id, $this->context->language->id),
            'category' => $category
        ];
    }
}