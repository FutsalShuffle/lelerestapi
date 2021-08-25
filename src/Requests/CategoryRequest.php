<?php
namespace App\Requests;
use App\Contracts\Request;

class CategoryRequest implements Request
{
    public $id_category;
    public $p;
    public $nbProducts;

    public function load()
    {
        $this->id_category = \Tools::getValue('id_category', 0);
        $this->p = \Tools::getValue('p', 1);
        $this->nbProducts = \Tools::getValue('n', 15);
    }
}