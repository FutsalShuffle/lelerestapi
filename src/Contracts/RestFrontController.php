<?php

namespace App\Contracts;
use App\Response\Response;

abstract class RestFrontController extends \ModuleFrontController
{
    public $ajax = 1;
    public $id_lang;
    public $response;

    public function __construct()
    {
        parent::__construct();
        $this->response = new Response();
        $this->id_lang = \Tools::getValue('id_lang', 1);
        $this->context->setLanguage(new \Language((int)$this->id_lang));
        $this->context->setCurrency(new \Currency(1));
        if (!$this->context->cart) $this->context->setCart(new \Cart());
    }

    public function init()
    {
        parent::init();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $this->proccessGetMethod();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->proccessPostMethod();
        }

        $this->response->return403Error();
    }

    public function proccessPostMethod() {}
    public function proccessGetMethod() {}
}