<?php
namespace App\Services;
use App\Exceptions\ExceptionInvalidData;
use App\Requests\LoginRequest;
use App\Classes\RestApiHelpers;

class LoginService
{
    public $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    public function auth()
    {
        $request = LoginRequest::load();
        $customer = new \Customer();
        $errors = $request->validate();
        if (count($errors)) {
            ExceptionInvalidData::init($errors);
        }
        if (!$customer->getByEmail($request->email, $request->password) && !$customer->id) {
            ExceptionInvalidData::init(['login' => 'Email or password is not correct']);
        }
        return RestApiHelpers::CustomerToArray($customer, true);
    }

}