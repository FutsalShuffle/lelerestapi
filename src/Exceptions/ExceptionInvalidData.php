<?php
namespace App\Exceptions;
use App\Contracts\CustomExceptionInterface;

class ExceptionInvalidData implements CustomExceptionInterface
{
    public static function init($errors = null)
    {
        header('Content-Type: application/json');
        header('HTTP/1.0 400 Bad Request');
        if ($errors === null) $errors = ['global' => 'Bad Request'];
        $obj = [
            'success' => false,
            'result' => [],
            'errors' => $errors
        ];
        return die(json_encode($obj));
    }
}
