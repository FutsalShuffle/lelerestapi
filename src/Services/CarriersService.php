<?php
namespace App\Services;

class CarriersService
{
    public $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    /**
     * getCarriers
     * Получение всех доступных способов доставки
     * @return array
     */
    public function getCarriers()
    {
        $carriers = array();
        $availableCarriers = array();
        $allCarriers = \Carrier::getCarriers($this->context->language->id, true, false, false, null, \Carrier::ALL_CARRIERS); //all deliveries
        $address = \Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'address`
                WHERE `id_customer` = ' . (int)($this->context->customer->id) . ' AND `deleted` = 0 ORDER BY `id_address` DESC');

        $country = new \Country((int)$address['id_country']);
        $delivery_option_list = $this->context->cart->getDeliveryOptionList(null, true);
        if(!empty($delivery_option_list) && is_array($delivery_option_list)){
            foreach (reset($delivery_option_list) as $key => $option) {
                foreach ($option['carrier_list'] as $carrier) {
                    $price = $this->context->cart->getPackageShippingCost((int)$carrier['instance']->id, true, null, null, $country->id_zone);
                    $availableCarriers[] = $carrier['instance']->id; 
                }
            }
        }
        foreach($allCarriers as $carrier){
            $price = $this->context->cart->getPackageShippingCost((int)$carrier['id_carrier'], true, null, null, $country->id_zone);
            $carrier['price'] = ($price == 0)?$this->l('Free'):\Tools::displayPrice($price);
            $carrier['available'] = in_array($carrier['id_carrier'], $availableCarriers);
            if($carrier['available']){
                $carriers[] = $carrier;
            }
        }

        return [
            'carriers' => $carriers
        ];
    }

    
    /**
     * setCarrier
     * Крепит способ перевозчика к корзине
     * @return void
     */
    public function setCarrier()
    {
        $id_carrier = \Tools::isSubmit('id_carrier') ? \Tools::getValue('id_carrier') :
            \Carrier::getDefaultCarrierSelection(\Carrier::getCarriers($this->context->language->id));

        $delivery_option_list = $this->context->cart->getDeliveryOptionList(null, true);
        $key = $id_carrier .',';
        foreach ($delivery_option_list as $id_address => $options)
        {
            if (isset($options[$key]))
            {
                $address = new \Address($this->context->cart->id_address_delivery);
                $this->context->cart->id_carrier = $id_carrier;
                //need to substitute the current address
                $this->context->cart->setDeliveryOption(array($address->id => $key));
                if (isset($this->context->cookie->id_country))
                    unset($this->context->cookie->id_country);
                if (isset($this->context->cookie->id_state))
                    unset($this->context->cookie->id_state);
                break;
            }
        }
        $this->context->cart->update();
        return [];
    }
}