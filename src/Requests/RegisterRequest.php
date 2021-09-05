<?php
namespace App\Requests;
use App\Contracts\Request;

class RegisterRequest implements Request
{
    public $email;
    public $password;
    public $firstname;
    public $lastname;
    
    /**
     * load
     *
     * @return RegisterRequest
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

    /**
     * @return array
     */
    public function validate()
    {
        $errors = [];

        if (!\Validate::isEmail($this->email)) {
            $errors['email'] = 'Please enter valid email';
        }

        if (!$this->password && strlen($this->password) < 6) {
            $errors['password'] = 'Please enter correct password';
        }
        
        if (!$this->lastname) {
            $errors['lastname'] = 'Please enter correct lastname';
        }

        if (!$this->firstname) {
            $errors['firstname'] = 'Please enter correct firstname';
        }

        return $errors;
    }
}