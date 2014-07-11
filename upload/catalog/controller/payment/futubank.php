<?php

/**
 *
 *  Futubank OpenCart payment plugin v1.0
 *
 */ 

define('FUTUBANK_VERSION', '1.0');

include('futubank_core.php');

class ControllerPaymentFutubank extends Controller {

    protected function index() {
        $this->id = 'payment';
        $this->data = array_merge($this->data, array(
            'button_confirm'  => $this->language->get('button_confirm'),
            'button_back'     => $this->language->get('button_back'),
            'futubank_url'    => $this->get_form_object()->get_url(),
            'futubank_fields' => FutubankForm::array_to_hidden_fields($this->get_form()),
        ));
        $this->template = $this->get_template();
        $this->render();
    }

    public function success() {
        $this->redirect(HTTPS_SERVER . 'index.php?route=checkout/success');
    }

    public function fail() {
        $this->redirect(HTTPS_SERVER . 'index.php?route=checkout/checkout');
    }

    public function callback() {
        $this->get_form_object();
        if (!$this->request->post) {
            echo "ERROR: empty request\n";
        } else if (!$ff->is_signature_correct($this->request->post)) {
            echo "ERROR: wrong signature\n";
        } else {
            $this->load->model('checkout/order');
            $order_id = $this->request->post['order_id'];

            if ($ff->is_order_completed($this->request->post)) {
                $order_info = $this->model_checkout_order->getOrder($order_id);
                $new_order_status_id = $this->config->get('futubank_order_status_id');
                $current_order_status_id = $order_info['order_status_id'];

                if (!$current_order_status_id) {
                    $this->model_checkout_order->confirm($order_id, $new_order_status_id, 'futubank');
                } else if ($current_order_status_id != $new_order_status_id) {
                    $this->model_checkout_order->update($order_id, $new_order_status_id, 'futubank', TRUE);
                }
            }

            echo "OK$order_id\n";
        }
    }

    private function get_form_object() { 
        $version = defined('VERSION')?VERSION:'Unknown';
        $plugin_version = defined('FUTUBANK_VERSION')?FUTUBANK_VERSION:'Unknown';
        $cms_info = 'OpenCart v. ' . $version;
        return new FutubankForm(
            $this->config->get('futubank_merchant_id'),
            $this->config->get('futubank_secret_key'),
            $this->config->get('futubank_mode') == 'test',
            $plugin_version,
            $cms_info
        );
    }

    private function get_form() {
        $this->load->model('checkout/order');

        $currency = 'RUB';
        $order_id = $this->session->data['order_id'];

        $order_info = $this->model_checkout_order->getOrder($order_id);

        $amount = $this->currency->format($order_info['total'], $currency, $order_info['currency_value'], false);
        
        return $this->get_form_object()->compose(
            $amount,
            $currency,
            $order_id,
            $order_info['email'],
            $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'],
            $order_info['telephone'],
            HTTPS_SERVER . 'index.php?route=payment/futubank/success',
            HTTPS_SERVER . 'index.php?route=payment/futubank/fail',
            $this->get_back_url()
        );
    }

    private function get_back_url() {
        if ($this->request->get['route'] != 'checkout/guest_step_3') {
            return HTTPS_SERVER . 'index.php?route=checkout/checkout';
        } else {
            return HTTPS_SERVER . 'index.php?route=checkout/guest_step_2';
        }
    }

    private function get_template() {
        $custom_template =  $this->config->get('config_template') . '/template/payment/futubank.tpl';
        if (file_exists(DIR_TEMPLATE . $custom_template)) {
            return $custom_template;
        } else {
            return 'default/template/payment/futubank.tpl';
        }
    }

}

?>
