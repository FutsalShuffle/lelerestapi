<?php
require_once dirname(__FILE__).'/RestApiHelpers.php';
use \Firebase\JWT\JWT;

/**
 * MainRestApi
 */
class MainRestApi {
    const key = 'andrele82';
    
    /**
     * createJwt
     *
     * @param  mixed $email
     * @return JWT
     */
    public static function createJwt(string $email) {
        $payload = array(
            "email" => $email,
        );
        return JWT::encode($payload, self::key);
    }
    
    /**
     * getToken
     *
     * @return string|bool
     */
    public static function getToken() {
        foreach (getallheaders() as $name => $value) {
            if ($name === 'Authorization') {
                return str_replace('Bearer ', '', $value); 
            }
        }
        return false;
    }
    
    /**
     * validateUser
     *
     * @return array|bool
     */
    public static function validateUser() {
        if (!MainRestApi::getToken()) return false;
        $token = MainRestApi::getToken();
        $user = JWT::decode($token, self::key, array('HS256'));
        if ($user->email)
            return MainRestApi::getUserByJwt($user->email);
        else
            return false;
    }

    public static function getUserObject()
    {
        if (!MainRestApi::getToken()) return false;
        $token = MainRestApi::getToken();
        $user = JWT::decode($token, self::key, array('HS256'));
        if (!$user->email)
            return false;
        
        $customer = new Customer();
        if ($customer->getByEmail($user->email) && $customer->id) {
            return $customer;
        }
        return false;
    }

    
    public static function getCustomerCartObject(Customer $customer)
    {
        if (!$id = RestApiHelpers::getCustomerCartId($customer)) return false;
        return new Cart((int)$id);
    }
        
    /**
     * getUserByJwt
     *
     * @param  mixed $email
     * @return array|bool
     */
    public static function getUserByJwt($email) {
        $customer = new Customer();
        if ($customer->getByEmail($email) && $customer->id) {
            return RestApiHelpers::CustomerToArray($customer);
        }
        return false;
    }
}