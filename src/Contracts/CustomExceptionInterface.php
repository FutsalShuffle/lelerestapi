<?php
namespace App\Contracts;

interface CustomExceptionInterface
{
    public static function init($errors = null);
}