<?php
namespace App\Requests;
use App\Contracts\Request;
use App\Response\Response;

class LoginRequest implements Request
{
    public $email;
    public $password;
    
    /**
     * load
     *
     * @return LoginRequest
     */
    public static function load()
    {
        $e = new self();
        $e->email = \Tools::getValue('email', '');
        $e->password = \Tools::getValue('password', '');
        return $e;
    }
    
    public function validate()
    {
        $response = new Response();

        if (!\Validate::isEmail($this->email)) {
            $response->setError('email', 'Please enter valid email');
        }

        if (!$this->password) {
            $response->setError('password', 'Please enter password');
        }

        if ($response->hasErrors()) {
            $response->setResponseCode(400);
            $response->returnResponse();
        }
        return true;
    }
}