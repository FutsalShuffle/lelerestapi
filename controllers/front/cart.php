<?php
use App\Controllers\RestControllerAuth;
use App\Requests\CartRequest;
/**
 * LelerestapiCartModuleFrontController
 */
class LelerestapiCartModuleFrontController extends RestControllerAuth
{
    public $request;

    public function __construct()
    {
        parent::__construct();
        $this->request = CartRequest::load();
    }

    public function proccessGetMethod()
    {
        $this->response->setResult('cart',$this->getSummary());
        return $this->response->returnResponse();
    }

    public function proccessPostMethod()
    {
        $this->request->validate();
        if ($this->request->add) {
            return $this->processChangeProductInCart();
        }
        if ($this->request->delete) {
            return $this->processDeleteProductInCart();
        }
        if ($this->request->setQty) {
            return $this->processSetProductQty();
        }
    }

    public function processSetProductQty() {/*TODO*/}

    public function getSummary()
    {
        $cart = new Cart($this->context->cart->id);
        $summary = $cart->getSummaryDetails(null, true);

        if ($this->context->customer->id) {
            $customer = new Customer($this->context->customer->id);
            $address = new Address($this->context->cart->id_address_delivery);
            $summary['address'] = $address;
            $summary['customer'] = $customer;
        } else {
            $summary['address'] = new Address(null);
            $summary['customer'] = new Customer(null);
        }

        $currency = $this->context->currency;
        foreach ($summary['products'] as $key => $product) {
            $summary['products'][$key]['image_url'] = Context::getContext()->link->getImageLink(
                $product['link_rewrite'],
                $product['id_image']
            );
            $summary['products'][$key]['link'] = Context::getContext()->link->getProductLink(
                (int)$product['id_product'],
                $product['link_rewrite'],
                $product['category'],
                $product['ean13']
            );

            $summary['products'][$key]['price_unit'] = Tools::displayPrice($product['price_wt'], $currency);
            $summary['products'][$key]['price_total'] = Tools::displayPrice($product['total_wt'], $currency);
        }
        $summary['token'] = Tools::getToken();
        $summary['currencySign'] = $currency->sign;
        return $summary;
    }

    private function processDeleteProductInCart()
    {
        $customization_product = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'customization`'
            . ' WHERE `id_cart` = ' . (int) $this->context->cart->id
            . ' AND `id_product` = ' . (int) $this->request->id_product
            . ' AND `id_customization` != ' . (int) $this->request->customization_id
        );

        if (count($customization_product)) {
            $product = new Product((int) $this->request->id_product);
            if ($this->request->id_product_attribute > 0) {
                $minimal_quantity = (int) Attribute::getAttributeMinimalQty($this->request->id_product_attribute);
            } else {
                $minimal_quantity = (int) $product->minimal_quantity;
            }

            $total_quantity = 0;
            foreach ($customization_product as $custom) {
                $total_quantity += $custom['quantity'];
            }

            if ($total_quantity < $minimal_quantity) {
                $this->response->setResponseCode(400);
                $this->response->setError('global', [$this->trans(
                    'You must add %quantity% minimum quantity',
                    array('%quantity%' => $minimal_quantity),
                    'Shop.Notifications.Error'
                )]);
                return $this->response->returnResponse();
            }
        }

        $data = array(
            'id_cart' => (int) $this->context->cart->id,
            'id_product' => (int) $this->request->id_product,
            'id_product_attribute' => (int) $this->request->id_product_attribute,
            'customization_id' => (int) $this->request->customization_id,
            'id_address_delivery' => (int) 0,
        );

        Hook::exec('actionObjectProductInCartDeleteBefore', $data, null, true);

        if ($this->context->cart->deleteProduct(
            $this->request->id_product,
            $this->request->id_product_attribute,
            $this->request->customization_id,
            $this->request->id_address_delivery
        )) {
            Hook::exec('actionObjectProductInCartDeleteAfter', $data);

            if (!Cart::getNbProducts((int) $this->context->cart->id)) {
                $this->context->cart->setDeliveryOption(null);
                $this->context->cart->gift = 0;
                $this->context->cart->gift_message = '';
                $this->context->cart->update();
            }

            $isAvailable = $this->areProductsAvailable();
            if (true !== $isAvailable) {
                $this->response->setResponseCode(400);
                $this->response->setError('global', [$isAvailable]);
                return $this->response->returnResponse();
            }
        }

        CartRule::autoRemoveFromCart();
        CartRule::autoAddToCart();
        $this->response->setResult('cart', $this->getSummary());
        $this->response->returnResponse();
    }

    private function processChangeProductInCart()
    {
        $mode = (Tools::getIsset('update') && $this->request->id_product) ? 'update' : 'add';
        $product = new Product($this->request->id_product, true, $this->context->language->id);
        if (!$product->id || !$product->active || !$product->checkAccess($this->context->cart->id_customer)) {
            $this->response->setResponseCode(400);
            $this->response->setError('global', [$this->trans(
                'This product (%product%) is no longer available.',
                array('%product%' => $product->name),
                'Shop.Notifications.Error'
            )]);
            return $this->response->returnResponse();
        }

        if (!$this->request->id_product_attribute && $product->hasAttributes()) {
            $minimum_quantity = ($product->out_of_stock == 2)
                ? !Configuration::get('PS_ORDER_OUT_OF_STOCK')
                : !$product->out_of_stock;
            $this->request->id_product_attribute = Product::getDefaultAttribute($product->id, $minimum_quantity);
            if (!$this->request->id_product_attribute) {
                Tools::redirectAdmin($this->context->link->getProductLink($product));
            }
        }

        $qty_to_check = $this->qty;
        $cart_products = $this->context->cart->getProducts();

        if (is_array($cart_products)) {
            foreach ($cart_products as $cart_product) {
                if ($this->productInCartMatchesCriteria($cart_product)) {
                    $qty_to_check = $cart_product['cart_quantity'];
                    if (Tools::getValue('op', 'up') == 'down') {
                        $qty_to_check -= $this->qty;
                    } else {
                        $qty_to_check += $this->qty;
                    }
                    break;
                }
            }
        }

        if ('update' !== $mode && $this->shouldAvailabilityErrorBeRaised($product, $qty_to_check)) {
            $this->response->setResponseCode(400);
            $this->response->setError('global', [$this->trans(
                'The item %product% in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.',
                array('%product%' => $product->name),
                'Shop.Notifications.Error'
            )]);
            return $this->response->returnResponse();
        }

        if (!$this->request->id_product_attribute) {
            if ($qty_to_check < $product->minimal_quantity) {
                $this->response->setResponseCode(400);
                $this->response->setError('global', [$this->trans(
                    'The minimum purchase order quantity for the product %product% is %quantity%.',
                    array('%product%' => $product->name, '%quantity%' => $product->minimal_quantity),
                    'Shop.Notifications.Error'
                )]);
                return $this->response->returnResponse();
            }
        } else {
            $combination = new Combination($this->request->id_product_attribute);
            if ($qty_to_check < $combination->minimal_quantity) {
                $this->response->setResponseCode(400);
                $this->response->setError('global', [$this->trans(
                    'The minimum purchase order quantity for the product %product% is %quantity%.',
                    array('%product%' => $product->name, '%quantity%' => $combination->minimal_quantity),
                    'Shop.Notifications.Error'
                )]);
                return $this->response->returnResponse();
            }
        }

        if (!$this->context->cart->id) {
            if (Context::getContext()->cookie->id_guest) {
                $guest = new Guest(Context::getContext()->cookie->id_guest);
                $this->context->cart->mobile_theme = $guest->mobile_theme;
            }
            $this->context->cart->add();
            if ($this->context->cart->id) {
                $this->context->cookie->id_cart = (int) $this->context->cart->id;
            }
        }

        if (!$product->hasAllRequiredCustomizableFields() && !$this->request->customization_id) {
            $this->response->setResponseCode(400);
            $this->response->setError('global', [$this->trans(
                'Please fill in all of the required fields, and then save your customizations.',
                array(),
                'Shop.Notifications.Error'
            )]);
            return $this->response->returnResponse();
        }

        $cart_rules = $this->context->cart->getCartRules();
        $available_cart_rules = CartRule::getCustomerCartRules(
            $this->context->language->id,
            (isset($this->context->customer->id) ? $this->context->customer->id : 0),
            true,
            true,
            true,
            $this->context->cart,
            false,
            true
        );
        $update_quantity = $this->context->cart->updateQty(
            $this->qty,
            $this->request->id_product,
            $this->request->id_product_attribute,
            0,
            Tools::getValue('op', 'up'),
            0,
            null,
            true,
            true
        );
        if ($update_quantity < 0) {
            $minimal_quantity = ($this->request->id_product_attribute)
                ? Attribute::getAttributeMinimalQty($this->request->id_product_attribute)
                : $product->minimal_quantity;
            
            $this->response->setResponseCode(400);
            $this->response->setError('global', [$this->trans(
                'You must add %quantity% minimum quantity',
                array('%quantity%' => $minimal_quantity),
                'Shop.Notifications.Error'
            )]);
            return $this->response->returnResponse();    
        } elseif (!$update_quantity) {
            $this->response->setResponseCode(400);
            $this->response->setError('global', [$this->trans(
                'You already have the maximum quantity available for this product.',
                array(),
                'Shop.Notifications.Error'
            )]);
            return $this->response->returnResponse(); 
        } elseif ($this->shouldAvailabilityErrorBeRaised($product, $qty_to_check)) {
            $this->response->setResponseCode(400);
            $this->response->setError('global', [$this->trans(
                'The item %product% in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.',
                array('%product%' => $product->name),
                'Shop.Notifications.Error'
            )]);
            return $this->response->returnResponse(); 
        }

        $removed = CartRule::autoRemoveFromCart();
        CartRule::autoAddToCart();
        $this->response->setResult('summary', $this->getSummary());
        $this->response->returnResponse();
    }

    private function shouldAvailabilityErrorBeRaised($product, $qtyToCheck)
    {
        if (($this->request->id_product_attribute)) {
            return !Product::isAvailableWhenOutOfStock($product->out_of_stock) && !Attribute::checkAttributeQty($this->request->id_product_attribute, $qtyToCheck);
        } elseif (Product::isAvailableWhenOutOfStock($product->out_of_stock)) {
            return false;
        }

        $productQuantity = Product::getQuantity(
            $this->request->id_product,
            $this->request->id_product_attribute,
            null,
            $this->context->cart,
            $this->request->customization_id
        );

        return $productQuantity < 0;
    }

    private function productInCartMatchesCriteria($productInCart)
    {
        return (!isset($this->request->id_product_attribute) || (
                    $productInCart['id_product_attribute'] == $this->request->id_product_attribute &&
                    $productInCart['id_customization'] == $this->request->customization_id )
                ) && isset($this->request->id_product) && $productInCart['id_product'] == $this->request->id_product;
    }

    private function areProductsAvailable()
    {
        $product = $this->context->cart->checkQuantities(true);

        if (true === $product || !is_array($product)) {
            return true;
        }
        if ($product['active']) {
            return $this->trans(
                'The item %product% in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.',
                array('%product%' => $product['name']),
                'Shop.Notifications.Error'
            );
        }
        return $this->trans(
            'This product (%product%) is no longer available.',
            array('%product%' => $product['name']),
            'Shop.Notifications.Error'
        );
    }
}
