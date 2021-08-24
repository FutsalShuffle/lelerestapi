<?php
/**
 * Class ContextCore.
 *
 * @since 1.5.0.1
 */
class Context extends ContextCore
{
    public function setCart(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function setCurrency(Currency $currency)
    {
        $this->currency = $currency;
    }

    public function setLanguage(Language $lang)
    {
        $this->language = $lang;
    }

    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;
    }
}
