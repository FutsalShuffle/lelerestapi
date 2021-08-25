<?php
namespace App\Requests;
use App\Contracts\Request;
use App\Response\Response;

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
    
    public function validate()
    {
        $response = new Response();
        if ($this->id_category <= 0) {
            $response->setError('id_category', 'Invalid category id');
        }
        if ($this->p <= 0) {
            $response->setError('p', 'Invalid page number');
        }
        if ($response->hasErrors()) {
            $response->setResponseCode(400);
            $response->returnResponse();
        }
        return true;
    }
}