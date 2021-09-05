<?php
namespace App\Controllers;
use App\Contracts\RestFrontController;
use App\Classes\RestApiHelpers;
use App\Classes\MainRestApi;

class RestController extends RestFrontController
{
    public $ajax = 1;
}