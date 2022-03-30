
<?php

class ControllerExtensionPaymentAsiabillTruemoney extends Controller {

    var $code_key = 'TRUEMONEY';
    var $code;
    var $url;

	function __construct($registry)
    {
        parent::__construct($registry);
        $helper = new Asiabill\Helper();
        $this->code = $helper->payMethodCode($this->code_key);
        $this->url = new Asiabill\Url($this);
    }

    // 付款信息
    public function index() {
        if($this->session->data['order_id']  ){
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET payment_method = '". $this->config->get('payment_'.$this->code.'_title') ."' where order_id = '". $this->session->data['order_id'] ."' ");
        }
        $data['confirm_url'] = $this->url->catalogUrl()->confirmUrl();
        $data['form_url'] = $this->url->catalogUrl()->formUrl();
        return $this->load->view('/extension/payment/asiabill/index', $data);
    }




}
?>
