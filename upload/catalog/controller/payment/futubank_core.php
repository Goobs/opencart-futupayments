<?php

class FutubankForm {
    private $merchant_id;
    private $secret_key;

    public function __construct($merchant_id, $secret_key) {
        $this->merchant_id = $merchant_id;
        $this->secret_key = $secret_key;
    }

    public function compose(
        $amount,
        $currency,
        $order_id,
        $client_email,
        $client_name,
        $client_phone,
        $success_url,
        $fail_url,
        $cancel_url,
        $meta=''
    ) {
        $form = array(
            'merchant'       => $this->merchant_id,
            'unix_timestamp' => time(),
            'salt'           => $this->get_salt(32),
            'amount'         => $amount,
            'currency'       => $currency,
            'description'    => "Заказ №$order_id",
            'order_id'       => $order_id,
            'client_email'   => $client_email,
            'client_name'    => $client_name,
            'client_phone'   => $client_phone,
            'success_url'    => $success_url,
            'fail_url'       => $fail_url,
            'cancel_url'     => $cancel_url,
            'meta'           => $meta,
        );
        $form['signature'] = $this->get_signature($form);
        return $form;
    }

    public static function array_to_hidden_fields(array $form) {
        $result = '';
        foreach ($form as $k => $v) {
            $result .= '<input name="' . htmlspecialchars($k) . '" value="' . htmlspecialchars($v) . '" type="hidden">';
        }
        return $result;
    }

    public static function get_url($mode) {
        if ($mode == 'real') {
            return 'https://secure.futubank.com/pay/';
        } else if ($mode == 'test') {
            return 'https://secure.futubank.com/testing-pay/';
        } else {
            throw new Exception('mode must be "test" or "real"');
        }
    }

    public function get_signature(array $params) {
        $keys = array_keys($params);
        sort($keys);
        $chunks = array();
        foreach ($keys as $k) {
            if ($params[$k] && ($k != 'signature')) {
                $chunks[] = $k . '=' . base64_encode($params[$k]);
            }
        }
        return $this->double_sha1(implode('&', $chunks));
    }

    private function double_sha1($data) {
        for ($i = 0; $i < 2; $i++) {
            $data = sha1($this->secret_key . $data);   
        }
        return $data;
    }

    private function get_salt($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $result;
    }
}
