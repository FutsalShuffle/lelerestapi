<?php
namespace App\Services;
use App\Exceptions\ExceptionInvalidData;
use App\Requests\RegisterRequest;
use App\Classes\RestApiHelpers;

class RegisterService
{
    public $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    /**
     * Customer register
     * @return array
     */
    public function register()
    {
        $request = RegisterRequest::load();
        $errors = $request->validate();
        if (count($errors)) {
            ExceptionInvalidData::init($errors);
        }
        $customer = new \Customer();
        if ($customer->getByEmail($request->email) && $customer->id) {
            ExceptionInvalidData::init(['email'=> 'This email is already taken']);
        }
        $customer->firstname = $request->firstname;
        $customer->lastname  = $request->lastname;
        $customer->email     = $request->email;
        $customer->passwd    = \Tools::encrypt($request->password);
        $customer->deleted   = false;
        if ($customer->add()) {
            return ['customer' => RestApiHelpers::CustomerToArray($customer, true)];
        }
        ExceptionInvalidData::init();
    }
}