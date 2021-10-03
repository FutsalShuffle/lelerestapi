<?php
namespace App\Requests;
use App\Contracts\Request;

class AddressRequest implements Request
{
    public $city;
    public $address1;
    public $postcode;
    public $phone;
    public $company;
    public $other;
    public $alias;
    public $phone_mobile;
    public $id_country;
    public $id_state;
    
    /**
     * load
     *
     * @return LoginRequest
     */
    public static function load()
    {
        $e = new self();
        $e->city = \Tools::getValue('city', '');
        $e->address1 = \Tools::getValue('address1', '');
        $e->postcode = \Tools::getValue('postcode', 0);
        $e->phone = \Tools::getValue('phone', '');
        $e->other = \Tools::getValue('other', '');
        $e->alias = \Tools::getValue('alias', 'My address');
        $e->phone_mobile = \Tools::getValue('phone_mobile', '');
        $e->id_country = \Tools::getValue('id_country', 0);
        $e->id_state = \Tools::getValue('id_state', 0);

        return $e;
    }
    
    /**
     * @return array
     */
    public function validate()
    {
        $errors = [];

        if (!$this->city)
        {
            $errors['city'] = 'Please enter a valid city';
        }

        if (!$this->address1)
        {
            $errors['address1'] = 'Please enter a valid address';
        }

        if (!$this->postcode)
        {
            $errors['postcode'] = 'Please enter a valid postcode';
        }

        if (!$this->phone && !$this->mobile_phone)
        {
            $errors['phone'] = 'Please enter a valid phone number';
        }

        return $errors;
    }
}