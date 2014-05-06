<?php
class ModelPaymentFutubank extends Model {
    public function getMethod($address) {
        $this->load->language('payment/futubank');

        if ($this->config->get('futubank_status')) {
            $config_zone_id = (int)$this->config->get('futubank_geo_zone_id');
            $address_zone_id = (int)$address['zone_id'];
            $country_id = (int)$address['country_id'];
            $query = $this->db->query(
                "SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone " .
                "WHERE geo_zone_id = '$config_zone_id' " .
                "AND country_id = '$country_id' " .
                "AND (zone_id = '$address_zone_id' OR zone_id = '0')"
            );
            if (!$config_zone_id) {
                $status = TRUE;
            } elseif ($query->num_rows) {
                $status = TRUE;
            } else {
                $status = FALSE;
            }
        } else {
            $status = FALSE;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code'       => 'futubank',
                'title'      => $this->language->get('text_title'),
                'sort_order' => $this->config->get('futubank_sort_order')
            );
        }
        return $method_data;
    }
}
?>