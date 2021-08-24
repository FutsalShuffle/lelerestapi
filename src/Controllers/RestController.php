<?php
namespace App\Controllers;
use App\Contracts\RestFrontController;
require_once dirname(__FILE__).'/../../classes/Main.php';
require_once dirname(__FILE__).'/../../classes/RestApiHelpers.php';

class RestController extends RestFrontController
{
    public $ajax = 1;
    public $result = [];

    public function setResult($key, $value)
    {
        $this->result['success'] = 1;
        $this->result[$key] = $value;
    }

    public function setErrors($key, $value)
    {
        $this->result['success'] = 0;
        $this->result['errors'][$key] = $value;
    }
}