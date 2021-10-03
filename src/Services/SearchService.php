<?php
namespace App\Services;
use App\Requests\SearchRequest;
use App\Exceptions\ExceptionInvalidData;

class SearchService
{
    public $context;

    public function __construct($context)
    {
        $this->context = $context;
    }
    
    /**
     * Search
     * @return array
     */
    public function getProducts()
    {
        $request = SearchRequest::load();
        $errors = $request->validate();
        if (count($errors)) {
            ExceptionInvalidData::init($errors);
        }

        return \Search::find($this->context->language->id, $request->q, $request->p, $request->nbProducts, 'position', 'desc', true);
    }
}