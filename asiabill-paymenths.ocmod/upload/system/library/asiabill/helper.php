<?php

namespace Asiabill;


class Helper
{

    private $controller;

    function __construct($controller = '')
    {
        $this->controller = $controller;
    }

    function payMethodCode($key = '',$value = '',$return='value'){
        $codes = array(
            'cc' => 'asiabill_creditcard',
            'ali' => 'asiabill_alipay',
            'wechat' => 'asiabill_wechatpay',
            'crypto' => 'asiabill_crypto',
            'directpay' => 'asiabill_directpay',
            'ebanx' => 'asiabill_ebanx',
            'giropay' => 'asiabill_giropay',
            'ideal' => 'asiabill_ideal',
            'p24' => 'asiabill_p24',
            'paysafecard' => 'asiabill_paysafecard',
        );

        if( $return == 'key' ){
            return array_search($value,$codes,true);
        }

        if( isset($codes[$key]) ){
            return $codes[$key];
        }

        return 'asiabill_'.strtolower($key);
    }

    function paymentMethod($code){
        $method = array(
            'asiabill_creditcard' => 'Credit Card',
            'asiabill_alipay' => 'Alipay',
            'asiabill_wechatpay' => 'WeChatPay',
            'asiabill_crypto' => 'CryptoPayment',
            'asiabill_directpay' => 'directpay',
            'asiabill_ebanx' => 'Ebanx',
            'asiabill_giropay' => 'giropay',
            'asiabill_ideal' => 'ideal',
            'asiabill_p24' => 'p24',
            'asiabill_paysafecard' => 'paysafecard',
        );

        if( key_exists($code,$method) ){
            return $method[$code];
        }
        return strtoupper(str_replace('asiabill_','',$code));
    }

    function goodsDetail($products=array()){
        if( !is_array($products) ){
            return '';
        }

        $product_data = array();
        foreach ($products as $i => $val) {
            if ($i == 10) break;
            $productName   = strlen($val['name']) > 130 ? substr($val['name'], 0, 130) : $val['name'];
            $product_data[] = [
                'productName' => htmlspecialchars($productName,ENT_QUOTES),
                'quantity'    => $val['quantity'],
                'price'       => sprintf('%.2f', $val['price'])
            ];
        }
        return json_encode($product_data);
    }

    function get3PartSign($data,$key){
        $string = $data['merNo'] . $data['gatewayNo'] . $data['orderNo'] . $data['orderCurrency'] . $data['orderAmount'] . $data['returnUrl'] . $key;
        return $this->signInfo($string);
    }

    function getResultSign($data,$key){
        $string = $data['merNo'] . $data['gatewayNo'] . $data['tradeNo'] . $data['orderNo'] . $data['orderCurrency'] . $data['orderAmount'] . $data['orderStatus'] . $data['orderInfo'] . $key;
        return $this->signInfo($string);
    }

    function signInfo($str){
        $str = str_replace(array("&","<",">","\"","'"),array('&amp;','&lt;','&gt;','&quot;',''),$str);
        $sign_info = strtoupper(hash("sha256" , $str ));
        return $sign_info;
    }

    function isMobile(){
        $is_mobile = 0;
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $is_iphone = (strpos($agent, 'iphone')) ? true : false;
        $is_ipad = (strpos($agent, 'ipad')) ? true : false;
        $is_android = (strpos($agent, 'android')) ? true : false;
        if( $is_iphone || $is_ipad || $is_android ){
            $is_mobile = 1;
        }
        return $is_mobile;
    }

    function checkVerification(){

        $post = $this->controller->request->post;
        $code = $this->controller->code;

        $mode = $this->controller->config->get('payment_'.$code.'_mode');
        $test = $mode == '1'? '': 'test_';
        $key = $this->controller->config->get('payment_'.$code.'_'.$test.'key');

        $result = false;
        $new_status = 0;
        $message = 'TradeNo: '.$post['tradeNo'].'| orderStatus: '.$post['orderStatus'].' | orderInfo: ' . $post['orderInfo'];
        $order_id = 0;

        if($post['signInfo'] == $this->getResultSign($post,$key) ){
            $order_info = $this->controller->model_checkout_order->getOrder($post['orderNo']);

            if( $order_info && $this->paymentMethod($order_info['payment_code']) ){

                $order_id = $order_info['order_id'];

                switch ($post['orderStatus']){
                    case 1:
                        $new_status = $this->controller->config->get('payment_asiabill_general_order_status_success');
                        $result = true;
                        break;
                    case 0:
                        if( substr($post['orderInfo'],0,5) == 'I0061' ){
                            // 相同订单号已经成功，并且重复交易
                            $new_status = $this->controller->config->get('payment_asiabill_general_order_status_success');
                            $result = true;
                        }elseif( substr($post['orderInfo'],0,5) == 'E0008' ){
                            // 取消订单
                            $new_status = $this->controller->config->get('payment_asiabill_general_order_status_cancel');
                        }else{
                            $new_status = $this->controller->config->get('payment_asiabill_general_order_status_failed');
                        }
                        break;
                    case -1:
                    case -2:
                        $new_status = 1;
                        $result = true;
                        break;
                    default:
                        $new_status = 0;
                }
            }
        }

        return array(
            'order_id' => $order_id,
            'result' => $result,
            'status' => $new_status,
            'message' => $message,
            'order_info' => $post['orderInfo']
        );

    }

    function changeOrderStatus($data){
        if( $data['order_id'] > 0 ){
            $order_info = $this->controller->model_checkout_order->getOrder($data['order_id']);
            $this->controller->logger->write('order_status '.$order_info['order_status_id']);
            if( $order_info['order_status_id'] != $data['status'] && $order_info['order_status_id'] != $this->controller->config->get('payment_asiabill_general_order_status_success') ){
                $this->controller->logger->write('add order history '.$data['status']);
                $this->controller->model_checkout_order->addOrderHistory($this->controller->request->post['orderNo'], $data['status'], $data['message'], FALSE);
            }
        }
    }

}