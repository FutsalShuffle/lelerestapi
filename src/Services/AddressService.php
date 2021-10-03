<?php
namespace App\Services;
use App\Requests\AddressRequest;
use App\Exceptions\ExceptionInvalidData;

class AddressService
{
    public $context;

    public function __construct($context)
    {
        $this->context = $context;
    }
    
    public function createAddress()
    {
        $data = AddressRequest::load();

        $errors = $data->validate();
        if (count($errors)) {
            ExceptionInvalidData::init($errors);
        }

        $address = new \Address();
        $address->id_country = $data->id_country ? $data->id_country : $this->context->country->id;
        $address->city = $data->city;
        $address->id_state = $data->id_state;
        $address->address1 = $data->address1;
        $address->postcode = $data->postcode;
        $address->phone = $data->phone;
        $address->mobile_phone = $data->mobile_phone;
        $address->alias = $data->alias;
        $address->other = $data->other;

        if (!$address->save()) {
            ExceptionInvalidData::init([]);
        }

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'cart_product`
        SET `id_address_delivery` = ' . (int) $address->id . '
        WHERE  `id_cart` = ' . (int) $this->context->cart->id . '
            AND `id_address_delivery` = ' . (int) $this->context->cart->id_address_delivery;
        \Db::getInstance()->execute($sql);

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'customization`
            SET `id_address_delivery` = ' . (int) $address->id . '
            WHERE  `id_cart` = ' . (int) $this->context->cart->id . '
                AND `id_address_delivery` = ' . (int) $this->context->cart->id_address_delivery;
        \Db::getInstance()->execute($sql);

        $this->context->cart->id_address_delivery = (int)$address->id;
        $this->context->cart->id_address_invoice = (int)$address->id;
        $this->context->cart->update();

        return $address;
    }

    public function getCustomerAddresses()
    {
        $cacheId = 'AddressService::getCustomerAddresses_'.(int) $this->context->customer->id;
        if (!\Cache::isStored($cacheId)) {
            $result = \DB::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'address` WHERE id_customer = '.(int)$this->context->customer->id . ' AND deleted = 0 AND active = 1');
            \Cache::store($cacheId, $result);

            return $result;
        }

        return \Cache::retrieve($cacheId); 
    }
}