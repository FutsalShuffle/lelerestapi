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
    public function returnResponse()
    {
        header("HTTP/1.0 ".$this->responseCode."");
        header('Content-Type: application/json');
        $obj = [
            'success' => !$this->hasErrors(),
            'result' => $this->result,
            'errors' =>$this->errors
        ];
        return die(json_encode($obj));
    }

    public function return403Error()
    {
        header('HTTP/1.0 403 Forbidden');
        $obj = [
            'success' => false,
            'result' => [],
            'errors' => ['Not Allowed']
        ];
        return die(json_encode($obj));
    }

    public function return404Error()
    {
        header('HTTP/1.0 404 Not Found');
        $obj = [
            'success' => false,
            'result' => [],
            'errors' => ['Not Found']
        ];
        return die(json_encode($obj));
    }

    public function hasErrors()
    {
        return (bool)count($this->errors);
    }
}