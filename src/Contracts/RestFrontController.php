<?php

namespace App\Contracts;
use App\Response\Response;
use App\Exceptions\ExceptionNotAllowed;

abstract class RestFrontController extends \ModuleFrontController
{
    public $ajax = 1;
    public $id_lang;
    public $response;
    public $id_currency;

    public function __construct()
    {
        parent::__construct();
        $this->response = new Response();
        $this->id_lang = (int)\Tools::getValue('id_lang', 1);
        $this->id_currency = (int)\Tools::getValue('id_currency', 1);
        $this->context->setLanguage(new \Language((int)$this->id_lang));
        $this->context->setCurrency(new \Currency($this->id_currency));
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

        // if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        //     return $this->proccessPutMethod();
        // }

        ExceptionNotAllowed::init();
    }

    public function proccessPostMethod() {}
    public function proccessGetMethod() {}
}