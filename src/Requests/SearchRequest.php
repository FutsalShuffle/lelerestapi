<?php
namespace App\Requests;
use App\Contracts\Request;

class SearchRequest implements Request
{
    public $p;
    public $nbProducts;
    public $q;
    
    /**
     * load
     *
     * @return LoginRequest
     */
    public static function load()
    {
        $e = new self();
        $e->q = \Tools::getValue('q', '');
        $e->nbProducts = \Tools::getValue('n', 15);
        $e->p = \Tools::getValue('p', 1);
        return $e;
    }
    
    /**
     * @return array
     */
    public function validate()
    {
        $errors = [];

        return $errors;
    }
}