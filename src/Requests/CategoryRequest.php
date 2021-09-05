<?php
namespace App\Requests;
use App\Contracts\Request;

class CategoryRequest implements Request
{
    public $id_category;
    public $p;
    public $nbProducts;
    
    /**
     * load
     *
     * @return CategoryRequest
     */
    public static function load()
    {
        $e = new self();
        $e->id_category = \Tools::getValue('id_category', 0);
        $e->p = \Tools::getValue('p', 1);
        $e->nbProducts = \Tools::getValue('n', 15);
        return $e;
    }
    
    /**
     * @return array
     */
    public function validate()
    {
        $errors = [];
        if ($this->id_category <= 0) {
            $errors['id_category'] =  'Invalid category id';
        }
        if ($this->p <= 0) {
            $errors['p'] =  'Invalid page number';
        }
        return $errors;
    }
}