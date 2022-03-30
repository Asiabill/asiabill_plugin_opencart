<?php 
class ModelExtensionPaymentAsiabillCreditCard extends Model {

  	public function getMethod($address) {

  		$helper = new Asiabill\Helper();
  		$code = $helper->payMethodCode('cc');

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

		if( $this->config->get('payment_'.$code.'_visa') ){
            $logo .= '<img src="catalog/view/theme/default/image/asiabill/visa_card.png">';
		}
        if( $this->config->get('payment_'.$code.'_mastercard') ){
            $logo .= '<img src="catalog/view/theme/default/image/asiabill/master_card.png">';
        }
        if( $this->config->get('payment_'.$code.'_jcb') ){
            $logo .= '<img src="catalog/view/theme/default/image/asiabill/jcb_card.png">';
        }
        if( $this->config->get('payment_'.$code.'_ae') ){
            $logo .= '<img src="catalog/view/theme/default/image/asiabill/ae_card.png">';
        }
        if( $this->config->get('payment_'.$code.'_discover') ){
            $logo .= '<img src="catalog/view/theme/default/image/asiabill/discover_card.png">';
        }
        if( $this->config->get('payment_'.$code.'_diners') ){
            $logo .= '<img src="catalog/view/theme/default/image/asiabill/diners_card.png">';
        }

        if( $this->config->get('payment_asiabill_general_logo_type') == '0' ){
            $logo = '';
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
