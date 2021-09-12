<?php
namespace App\Classes;
use \Firebase\JWT\JWT;
use App\Exceptions\ExceptionInvalidData;

/**
 * MainRestApi
 */
class MainRestApi {
    const key = 'andrele82';
    
    /**
     * createJwt
     * Создает JWT токен
     *
     * @param string $email
     * @return string
     */
    public static function createJwt($email) {
        $payload = array(
            "email" => $email,
        );
        return JWT::encode($payload, self::key);
    }
    
    /**
     * getToken
     * Получение токена из запроса
     *
     * @return string|bool
     */
    public static function getToken() {
        foreach (self::getHeaders() as $name => $value) {
            if ($name === 'Authorization') {
                return str_replace('Bearer ', '', $value); 
            }
        }
        return false;
    }

    public static function getHeaders()
    {
        if (!function_exists('getallheaders')) {
            function getallheaders() {
            $headers = [];
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
            return $headers;
            }
        }
        return getallheaders();
    }
    
    /**
     * validateUser
     *
     * @return array|bool
     */
    public static function validateUser() {
        if (!self::getToken()) return false;
        $token = self::getToken();
        try {
            $user = JWT::decode($token, self::key, array('HS256'));
        } catch (\Exception $e) {
            ExceptionInvalidData::init();
        }
        if ($user->email)
            return self::getUserByJwt($user->email);
        else
            return false;
    }

    public static function getUserObject()
    {
        if (!self::getToken()) return false;
        $token = self::getToken();
        $user = JWT::decode($token, self::key, array('HS256'));
        if (!$user->email)
            return false;
        
        $customer = new \Customer();
        if ($customer->getByEmail($user->email) && $customer->id) {
            return $customer;
        }
        return false;
    }

    
    public static function getCustomerCartObject(\Customer $customer)
    {
        if (!$id = RestApiHelpers::getCustomerCartId($customer)) return false;
        return new \Cart((int)$id);
    }
        
    /**
     * getUserByJwt
     *
     * @param  mixed $email
     * @return array|bool
     */
    public static function getUserByJwt($email) {
        $customer = new \Customer();
        if ($customer->getByEmail($email) && $customer->id) {
            return RestApiHelpers::CustomerToArray($customer);
        }
        return false;
    }
}