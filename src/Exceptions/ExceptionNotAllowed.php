<?php
namespace App\Exceptions;
use App\Contracts\CustomExceptionInterface;

class ExceptionNotAllowed implements CustomExceptionInterface
{
    public static function init($errors = null)
    {
        header('Content-Type: application/json');
        header('HTTP/1.0 403 Not Allowed');
        if ($errors === null) $errors = ['global' => 'Not Allowed'];
        $obj = [
            'success' => false,
            'result' => [],
            'errors' => $errors
        ];
        return die(json_encode($obj));
    }
}
