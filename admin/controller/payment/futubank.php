<?php

class ControllerPaymentFutubank extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('payment/futubank');
        $this->document->setTitle = $this->language->get('heading_title');
        
        $this->load->model('setting/setting');
        $this->load->model('localisation/order_status');
        $this->load->model('localisation/geo_zone');

        if (
            ($this->request->server['REQUEST_METHOD'] == 'POST') && 
            $this->validate()
        ) {
            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('futubank', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect(HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token']);
        }

        $this->data = array_merge($this->data, array(
            # == Form == 

            # merchant_id
            'entry_merchant_id'     => $this->language->get('entry_merchant_id'),           
            'error_merchant_id'     => isset($this->error['merchant_id']) ? $this->error['merchant_id'] : '',

            # secret_key
            'entry_secret_key'      => $this->language->get('entry_secret_key'),
            'error_secret_key'      => isset($this->error['secret_key']) ? $this->error['secret_key'] : '',

            # urls
            'callback_url'          => HTTP_CATALOG . 'index.php?route=payment/futubank/callback',
            'success_url'           => HTTP_CATALOG . 'index.php?route=payment/futubank/success',
            'fail_url'              => HTTP_CATALOG . 'index.php?route=payment/futubank/fail',

            # mode
            'entry_mode'            => $this->language->get('entry_mode'),
            'entry_mode_test'       => $this->language->get('entry_mode_test'),
            'entry_mode_real'       => $this->language->get('entry_mode_real'),

            # order status
            'entry_order_status'    => $this->language->get('entry_order_status'),
            'order_statuses'        => $this->model_localisation_order_status->getOrderStatuses(),

            # geo zone
            'entry_geo_zone'        => $this->language->get('entry_geo_zone'),
            'text_all_zones'        => $this->language->get('text_all_zones'),
            'geo_zones'             => $this->model_localisation_geo_zone->getGeoZones(),

             # status
            'entry_status'          => $this->language->get('entry_status'),
            'text_enabled'          => $this->language->get('text_enabled'),
            'text_disabled'         => $this->language->get('text_disabled'),

             # sort order
            'entry_sort_order'      => $this->language->get('entry_sort_order'),

            # == Etc ==

            'heading_title'         => $this->language->get('heading_title'), 
            
            'button_save'           => $this->language->get('button_save'),
            'button_cancel'         => $this->language->get('button_cancel'),

            'action'                => $this->url->link('payment/futubank', 'token=' . $this->session->data['token'], 'SSL'),
            'cancel'                => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
            
            'breadcrumbs'           => array(
                array(
                    'text'      => $this->language->get('text_home'),
                    'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
                    'separator' => false,
                ),
                array(
                    'text'      => $this->language->get('text_payment'),
                    'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
                    'separator' => ' :: ',
                ),
                array(
                    'text'      => $this->language->get('heading_title'),
                    'href'      => $this->url->link('payment/futubank', 'token=' . $this->session->data['token'], 'SSL'),
                    'separator' => ' :: ',
                ),
            ),
        ));

        $this->initial('futubank_merchant_id');
        $this->initial('futubank_secret_key');
        $this->initial('futubank_mode', 'test');
        $this->initial('futubank_order_status_id');
        $this->initial('futubank_geo_zone_id', 0);  
        $this->initial('futubank_status', 0);
        $this->initial('futubank_sort_order', 1);

        $this->template = 'payment/futubank.tpl';
        $this->children = array(
            'common/header',
            'common/footer',
        );

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    private function initial($k, $default=null) {
        if (isset($this->request->post[$k])) {
            $v = $this->request->post[$k];
        } else {
            $v = $this->config->get($k);
        }
        $this->data[$k] = $v ? $v : $default;
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'payment/futubank')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if (!$this->request->post['secret_key']) {
            $this->error['secret_key'] = $this->language->get('error_secret_key');
        }
        if (!$this->request->post['merchant_id']) {
            $this->error['merchant_id'] = $this->language->get('error_merchant_id');
        }
        return $this->error ? FALSE : TRUE;
    }
}
?>