<?php
namespace App\Exceptions;
use App\Contracts\CustomExceptionInterface;

class ExceptionNotFound implements CustomExceptionInterface
{
    public static function init($errors = null)
    {
        header('Content-Type: application/json');
        header('HTTP/1.0 404 Not Found');
        if ($errors === null) $errors = ['global' => 'Not Found'];
        $obj = [
            'success' => false,
            'result' => [],
            'errors' => $errors
        ];
        return die(json_encode($obj));
    }
}
