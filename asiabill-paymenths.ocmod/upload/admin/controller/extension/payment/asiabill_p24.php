<?php

class ControllerExtensionPaymentAsiabillP24 extends Controller {

    var $code;
    var $title = 'P24';
    var $webhook;
    var $logger;
    var $log_path;
    private $asiabill_data;
    private $error = array();

    public function __construct($registry)
    {
        parent::__construct($registry);

        $helper = new Asiabill\Helper($registry);
        $this->code = $helper->payMethodCode('p24');

        $this->load->language('extension/payment/'.$this->code);

        $this->asiabill_data = new Asiabill\Admin\Data($this);
        $url = new Asiabill\Url($registry);
        $this->webhook = $url->catalogUrl('auto')->webhookUrl();
        $this->logger = new Asiabill\Logger();
        $this->log_path = $this->logger->logPath();
    }

    public function index(){

        $post_data = self::checkParameter();

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $post_data) {
            $this->load->model('setting/setting');

            if( $this->request->post['payment_asiabill_general_logging'] == '1' ){
                $this->logger->mkDir();
            }

            foreach ($post_data as $code => $item){
                $this->model_setting_setting->editSetting($code, $item);
            }

            $data['success'] = $this->language->get('text_success');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['breadcrumbs'] = array(
            array(
                'href'      => HTTPS_SERVER . 'index.php?route=common/home&token='. $this->session->data['user_token'],
                'text'      => $this->language->get('text_home'),
            ),
            array(
                'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true),
                'text'      => $this->language->get('text_extension'),
            ),
            array(
                'href' => $this->url->link('extension/payment/asiabillpay', 'user_token=' . $this->session->data['user_token'], true),
                'text'      => $this->language->get('heading_title'),
            )
        );

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);
        $data['action'] = $this->url->link('extension/payment/'.$this->code, 'user_token=' . $this->session->data['user_token'], true);

        $settings = $this->asiabill_data->getAdminSettings(array(
            'payment' => self::cardTypeSettings()
        ));
        $data['settings_general'] = $this->load->view('extension/payment/asiabill_settings', ['settings' => $settings['general']]);
        $data['settings_asiabill'] = $this->load->view('extension/payment/asiabill_settings', ['settings' => $settings['asiabill']]);
        $data['settings_payment'] = $this->load->view('extension/payment/asiabill_settings', ['settings' => $settings['payment']]);
        if( !empty($this->error) ){
            $data['error'] = $this->error;
        }

        $this->response->setOutput($this->load->view('extension/payment/asiabill_payment', $data), $this->config->get('config_compression'));
    }

    private function cardTypeSettings(){
        return [];
    }

    private function checkParameter(){

        if( $this->request->server['REQUEST_METHOD'] != 'POST' ){
            return false;
        }

        $post_data = array();

        foreach ($this->request->post as $key => $val){

            $key_arr = explode('_',$key);
            $code = $key_arr[0].'_'.$key_arr[1].'_'.$key_arr[2];

            $post_data[$code][$key] = trim(addslashes($val));
        }
        if( $this->request->post['payment_'.$this->code.'_mode'] == '0'  ){
            return $post_data;
        }

        $mer_no = $this->request->post['payment_'.$this->code.'_mer_no'];
        $gateway_no = $this->request->post['payment_'.$this->code.'_gateway_no'];

        if( strlen($mer_no) < 5 ){
            $this->error[] = $this->language->get('error_mer_no');
        }

        if( strlen($gateway_no) < 8 ){
            $this->error[] = $this->language->get('error_gateway_no');
        }

        if( empty($this->request->post['payment_'.$this->code.'_key']) ){
            $this->error[] = $this->language->get('error_key');
        }

        if( $mer_no != substr($gateway_no,0,5) ){
            $this->error[] = $this->language->get('error_match');
        }

        if( count($this->error) > 0 ){
            return false;
        }else{
            return $post_data;
        }

    }

}
