<?php
namespace App\Response;

class Response {
    private $result = [];
    private $responseCode = 200;
    private $errors = [];

    public function setResult($key,$result) {
        $this->result[$key] = $result;
    }
    public function setResponseCode($code) {
        $this->responseCode = $code;
    }
    public function setError($key,$error) {
        $this->errors[$key] = $error;
    }

    /**
     * Возвращает json с результатом и ошибками из стейта
     * 
     */
    public function returnResponse()
    {
        header('Content-Type: application/json');
        header("HTTP/1.0 ".$this->responseCode."");
        $obj = [
            'success' => !$this->hasErrors(),
            'result' => $this->result,
            'errors' =>$this->errors
        ];
        return die(json_encode($obj));
    }

    public static function return403Error()
    {
        header('Content-Type: application/json');
        header('HTTP/1.0 403 Forbidden');
        $obj = [
            'success' => false,
            'result' => [],
            'errors' => ['Not Allowed']
        ];
        return die(json_encode($obj));
    }

    public static function return404Error()
    {
        header('Content-Type: application/json');
        header('HTTP/1.0 404 Not Found');
        $obj = [
            'success' => false,
            'result' => [],
            'errors' => ['Not Found']
        ];
        return die(json_encode($obj));
    }

    /**
     * Проверка на ошибки
     * @return boolean
     * 
     */
    public function hasErrors()
    {
        return (bool)count($this->errors);
    }

    /**
     * Возвращает array как json с 200 статусом
     * @param array $data
     * 
     */
    public function returnJson(array $data)
    {
        header('Content-Type: application/json');
        header("HTTP/1.0 ".$this->responseCode."");
        $obj = [
            'success' => true,
            'result' => $data,
            'errors' => []
        ];
        return die(json_encode($obj));
    }
}