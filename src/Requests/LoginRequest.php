<?php
namespace App\Requests;
use App\Contracts\Request;

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
    
    /**
     * @return array
     */
    public function validate()
    {
        $errors = [];

        if (!\Validate::isEmail($this->email)) {
            $errors['email'] = 'Please enter valid email';
        }

        if (!$this->password) {
            $errors['password'] = 'Please enter password';
        }

        return $errors;
    }
}