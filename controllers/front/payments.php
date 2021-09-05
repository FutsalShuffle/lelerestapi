
<?php
use App\Controllers\RestControllerAuth;

class LelerestapiPaymentsModuleFrontController extends RestControllerAuth
{

    public function proccessPostMethod()
    {
        $this->response->return403Error();
    }

    public function proccessGetMethod()
    {
        $this->response->setResult('payments', $this->getPaymentOptions());
        return $this->response->returnResponse();
    }

    public function getPaymentOptions()
    {
        $payment_methods = array();
        if (Configuration::get(Lelerestapi::REST_AUTO_PAYMENTS)) {
            foreach (PaymentModule::getInstalledPaymentModules() as $index=>$payment) {
                $module = Module::getInstanceByName($payment['name']);
                if (Validate::isLoadedObject($module)) {
                    $payment = $module->hookPaymentOptions(['cart'=>$this->context->cart]);
                    if ($payment) {
                        foreach ($payment as $paymentOption) {
                            $payment_methods[]['module'] = $paymentOption->toArray();
                            $payment_methods[]['name'] = $module->displayName;
                        }
                    }
                }
            }
        } else {
            $payment_methods = PWReactPayment::getAll();
        }

        return $payment_methods;
    }
}