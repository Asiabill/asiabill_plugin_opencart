
<?php

class ControllerExtensionPaymentAsiabill extends Controller {

    var $code;
    var $payment_method;
    var $helper;
    var $ab_url;
    var $logger;

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->language('extension/payment/asiabill');
        $this->load->model('checkout/order');

        $this->helper = new Asiabill\Helper($this);
        @$this->code = $this->session->data['payment_method']['code']?$this->session->data['payment_method']['code']:$this->helper->payMethodCode($this->request->post['remark']);
        $this->ab_url = new Asiabill\Url($this);
        $this->payment_method = $this->helper->paymentMethod($this->code);
        $this->logger = new Asiabill\Logger();
        $this->logger->openFile('info-'.date('Y-m').'.log',$this->config->get('payment_asiabill_general_logging'));
    }

    public function confirm(){
        if( empty($this->session->data['order_id']) || empty($this->code) ){
            return 'error';
        }
        $this->logger->write('pay with '.$this->payment_method);
        $this->logger->write('create order '.$this->config->get('payment_asiabill_general_order_status_created'));
        //$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_asiabill_general_order_status_created'), $this->language->get('created_order'),false);
        return true;
    }

    public function form(){

        // 不存在订单号 || 商品未空 || 不存在支付方式

        if( empty($this->session->data['order_id']) ||  empty($this->payment_method) ){
            $this->redirect($this->url->link('checkout/cart'));
        }

        $order_id = $this->session->data['order_id'];

        $order_info = $this->model_checkout_order->getOrder($order_id);
        $order_product_data = $this->model_checkout_order->getOrderProducts($order_id);

        $this->setSession();

        $mode = $this->config->get('payment_'.$this->code.'_mode');

        $test = $mode == '1'? '': 'test_';


        $parameter_data = [
            'merNo' => $this->config->get('payment_'.$this->code.'_'.$test.'mer_no'),
            'gatewayNo' => $this->config->get('payment_'.$this->code.'_'.$test.'gateway_no'),
            'orderNo' => $order_info['order_id'],
            'orderAmount' => sprintf('%.2f',$this->currency->format($order_info['total'], $order_info['currency_code'], '', false)),
            'orderCurrency' => $order_info['currency_code'],
            'paymentMethod' => $this->payment_method,
            'firstName' => $order_info['payment_firstname'],
            'lastName' => $order_info['payment_lastname'],
            'email' => $order_info['email'],
            'phone' =>  $order_info['telephone'],
            'city' => $order_info['payment_city'],
            'state' => $order_info['payment_zone'],
            'country' => $order_info['payment_iso_code_2'],
            'address' => empty($order_info['payment_address_2'])?$order_info['payment_address_1']:$order_info['payment_address_1'].' '.$order_info['payment_address_2'],
            'zip' => $order_info['payment_postcode'],
            'shipFirstName' => $order_info['shipping_firstname'],
            'shipLastName' => $order_info['shipping_lastname'],
            'shipPhone' => $order_info['telephone'],
            'shipCountry' => $order_info['shipping_iso_code_2'],
            'shipState' => $order_info['shipping_zone'],
            'shipCity' => $order_info['shipping_city'],
            'shipAddress' => empty($order_info['shipping_address_2'])?$order_info['shipping_address_1']:$order_info['shipping_address_2'].' '.$order_info['payment_address_2'],
            'shipZip' => $order_info['shipping_postcode'],
            'returnUrl' => $this->ab_url->catalogUrl()->returnUrl(),
            'callbackUrl' => $this->ab_url->catalogUrl()->webhookUrl(),
            'remark' => $this->helper->payMethodCode('',$this->code,'key'),
            'interfaceInfo' => 'Open-Cart 3x',
            'interfaceVersion' => $this->config->get('payment_asiabill_general_edition'),
            'isMobile' => $this->helper->isMobile(),
            'goods_detail' => $this->helper->goodsDetail($order_product_data)
        ];

        $parameter_data['signInfo'] = $this->helper->get3PartSign($parameter_data,$this->config->get('payment_'.$this->code.'_'.$test.'key'));

        $this->logger->write('parameter '.json_encode($parameter_data));

        $data['footer'] = '';//$this->load->controller('common/footer');
        $data['header'] = '';//$this->load->controller('common/header');

        $parameters = array();
        foreach ($parameter_data as $key => $val){
            $parameters[] = array('key' => $key, 'val' => $val);
        }

        $data['action_url'] = $this->ab_url->abRequestUrl($mode);

        $data['parameters'] = $parameters;

        $this->logger->write('redirect to '.$data['action_url']);
        $this->logger->destruct();
        $this->response->setOutput($this->load->view('/extension/payment/asiabill/form', $data));
    }

	public function result() {

        if( $this->request->server['REQUEST_METHOD'] !== 'POST' ){
            $this->redirect($this->url->link('common/home'));
        }

        $this->logger->write('return '.json_encode($this->request->post));

        $data = $this->helper->checkVerification();

        $this->logger->write(json_encode($data));

        if( $this->config->get('payment_asiabill_general_webhook') == '0' ){
            $this->helper->changeOrderStatus($data);
        }

        if( $data['result']  ){
            $route = 'checkout/success';
        }else{
            $this->session->data['error'] = $data['order_info'];
            $route = 'checkout/checkout';
        }
        $this->logger->write('redirect to '.$this->url->link($route));
        $this->logger->destruct();
        $this->redirect($this->url->link($route));
	}

	public function webhook(){
        if( $this->request->server['REQUEST_METHOD'] == 'POST' && $this->config->get('payment_asiabill_general_webhook') == '1' ){

            $this->logger->write('notify '.json_encode($this->request->post));

            if( @in_array($this->request->post['notifyType'],['PaymentResult','OrderStatusChanged']) ){
                $this->code = $this->code?$this->code:$this->helper->payMethodCode($this->request->post['remark']);
                $data = $this->helper->checkVerification($this->code);
                $this->logger->write(json_encode($data));
                $this->helper->changeOrderStatus($data);
                $this->logger->destruct();
            }
        }
        echo 'success';
        exit();
    }

    public function setSession(){
        $config = new Config();
        $config->load('default');
        $config->load('catalog');
        $session_name = $config->get('session_name');
        header('Set-Cookie: '.$session_name.' = '.$_COOKIE[$session_name].'; SameSite=None; Secure',false);
    }

    private function redirect($url){
        echo '<script>window.location.href="'.$url.'"</script>';
        exit();
    }
    
}
?>
