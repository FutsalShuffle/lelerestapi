<?php
namespace App\Requests;
use App\Contracts\Request;
use App\Response\Response;

class RegisterRequest implements Request
{
    public $email;
    public $password;
    public $firstname;
    public $lastname;
    
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
        $e->firstname = \Tools::getValue('firstname', '');
        $e->lastname  = \Tools::getValue('lastname', '');
        return $e;
    }
    
    public function validate()
    {
        $response = new Response();

        if (!\Validate::isEmail($this->email)) {
            $response->setError('email', 'Please enter valid email');
        }

        if (!$this->password && strlen($this->password) < 6) {
            $response->setError('password', 'Please enter correct password');
        }
        
        if (!$this->lastname) {
            $response->setError('lastname', 'Please enter correct lastname');
        }

        if (!$this->firstname) {
            $response->setError('firstname', 'Please enter correct firstnam');
        }

        if ($response->hasErrors()) {
            $response->setResponseCode(400);
            $response->returnResponse();
        }
        return true;
    }
}