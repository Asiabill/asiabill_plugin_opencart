<?php 
class ModelExtensionPaymentAsiabillCrypto extends Model {

  	public function getMethod($address) {

  		$helper = new Asiabill\Helper();
  		$code = $helper->payMethodCode('crypto');

        $method_data = array();
		$status = false;

		if ($this->config->get('payment_'.$code.'_status')) {

      		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_'.$code.'_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
			
			if (!$this->config->get('payment_'.$code.'_geo_zone_id')) {
        		$status = true;
      		} elseif ($query->num_rows) {
      		  	$status = true;
      		}
      	}

        $logo = '';

      	if( $this->config->get('payment_asiabill_general_logo_type') == '1' ){
            $logo = '<img src="catalog/view/theme/default/image/asiabill/crypto.png">';
		}


		if ($status) {
      		$method_data = array(
        		'code'       => $code,
        		'title'      => $this->config->get('payment_'.$code.'_title'),
				'sort_order' => $this->config->get('payment_'.$code.'_sort_order'),
                'terms'      => $logo,
      		);
    	}
   
    	return $method_data;
  	}

}
?>
