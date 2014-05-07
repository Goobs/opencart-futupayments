<?php
class ControllerPaymentFutubank extends Controller {

    protected function index() {
        $this->id = 'payment';
        $this->data = array_merge($this->data, array(
            'button_confirm' => $this->language->get('button_confirm'),
            'button_back'    => $this->language->get('button_back'),
            'futubank_url'   => $this->get_futubank_url(),
            'futubank_form'  => $this->get_futubank_form(),
        ));
        $this->template = $this->get_template();
        $this->render();
    }

    private function get_futubank_form() {
        $this->load->model('checkout/order');

        $currency = 'RUB';
        $order_id = $this->session->data['order_id'];
        
        $order_info = $this->model_checkout_order->getOrder($order_id); 
        $amount = $this->currency->convert($order_info['total'], $order_info['currency_code'], $currency);
        $order_description = implode(' ', array(
            $this->config->get('config_store'),
            $order_info['payment_firstname'],
            $order_info['payment_address_1'],
            $order_info['payment_address_2'],
            $order_info['payment_city'],
            $order_info['email'],
        ));
        
        $form = array(
            'merchant'       => $this->config->get('futubank_merchant_id'),
            'unix_timestamp' => time(),
            'salt'           => '00000000000000000000000000000000',
            'amount'         => $amount,
            'currency'       => $currency,
            'description'    => "Заказ №$order_id",
            'order_id'       => $order_id,
            'success_url'    => HTTPS_SERVER . 'index.php?route=payment/futubank/success',
            'fail_url'       => HTTPS_SERVER . 'index.php?route=payment/futubank/fail',
            'cancel_url'     => $this->get_back_url(),
            'meta'           => '',
        );
        
        $form['signature'] = $this->get_signature($form);

        return $form;
    }

    private function get_futubank_url() {
        if ($this->config->get('futubank_mode') == 'real') {
            return 'https://secure.futubank.com/pay/';
        } else {
            return 'https://secure.futubank.com/testing-pay/';
        }
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

    public function success() {
        $this->redirect(HTTPS_SERVER . 'index.php?route=checkout/success');
    }

    public function fail() {
        $this->redirect(HTTPS_SERVER . 'index.php?route=checkout/checkout');
    }

    public function callback() {
        $signature = $this->get_signature($this->request->post);
        if (!$this->request->post) {
            echo "ERROR: empty request\n";
        } else if ($signature !== $this->request->post['signature']) {
            echo "ERROR: wrong signature\n";
            #var_dump($this->request->post);
        } else {
            if ($this->request->post['state'] == 'COMPLETE') {
                
                $this->load->model('checkout/order');
                
                $order_id = $this->request->post['order_id'];
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

    private function get_signature(array $params) {
        $keys = array_keys($params);
        sort($keys);
        $chunks = array();
        foreach ($keys as $k) {
            if ($params[$k] && ($k != 'signature')) {
                $chunks[] = $k . '=' . base64_encode($params[$k]);
            }
        }
        $secret_key = $this->config->get('futubank_secret_key');
        return self::double_sha1($secret_key, implode('&', $chunks));
    }

    private static function double_sha1($secret_key, $data) {
        return sha1($secret_key . sha1($secret_key . $data));
    }
}

?>