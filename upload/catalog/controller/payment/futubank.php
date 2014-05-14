<?php

include('futubank_core.php');

class ControllerPaymentFutubank extends Controller {

    protected function index() {
        $this->id = 'payment';
        $this->data = array_merge($this->data, array(
            'button_confirm'  => $this->language->get('button_confirm'),
            'button_back'     => $this->language->get('button_back'),
            'futubank_url'    => FutubankForm::get_url($this->config->get('futubank_mode')),
            'futubank_fields' => FutubankForm::array_to_hidden_fields($this->get_futubank_form()),
        ));
        $this->template = $this->get_template();
        $this->render();
    }

    private function get_form_object() { 
        return new FutubankForm(
            $this->config->get('futubank_merchant_id'),
            $this->config->get('futubank_secret_key')
        );;
    }

    private function get_futubank_form() {

        $this->load->model('checkout/order');

        $currency = 'RUB';
        $order_id = $this->session->data['order_id'];

        $order_info = $this->model_checkout_order->getOrder($order_id);
        $amount = $this->currency->convert($order_info['total'], $order_info['currency_code'], $currency);

        $client_email = $order_info['email'];
        $client_name = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
        $client_phone = $order_info['telephone'];

        return $this->get_form_object()->compose(
            $amount,
            $currency,
            $order_id,
            $client_email,
            $client_name,
            $client_phone,
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

    public function success() {
        $this->redirect(HTTPS_SERVER . 'index.php?route=checkout/success');
    }

    public function fail() {
        $this->redirect(HTTPS_SERVER . 'index.php?route=checkout/checkout');
    }

    public function callback() {
        $signature = $this->get_form_object()->get_signature($this->request->post);

        if (!$this->request->post) {
            echo "ERROR: empty request\n";
        } else if ($signature !== $this->request->post['signature']) {
            echo "ERROR: wrong signature\n";
            #var_dump($this->request->post);
        } else {
            $this->load->model('checkout/order');
            $order_id = $this->request->post['order_id'];

            if ($this->request->post['state'] == 'COMPLETE') {
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
}

?>