<?php
namespace Asiabill;

class Url
{
    private $domain = 'https://safepay.asiabill.com/';
    private $test_domain = 'https://testpay.asiabill.com/';
    private $controller;
    var $catalog_url;

    function __construct($controller){
        $this->controller = $controller;
    }

    public function catalogUrl($ssl = 'auto'){
        if ( defined( 'HTTP_CATALOG' ) ) {
            $url = HTTP_CATALOG;
            $ssl_url = HTTPS_CATALOG;

        } else {
            $url = HTTP_SERVER;
            $ssl_url = HTTPS_SERVER;
        }

        @$ssl_config = true;

        // Explicit HTTPS
        if ( true === $ssl || ( 'auto' === $ssl && $ssl_config ) ) {
            $this->catalog_url = preg_match( '~^http(s)?://~', $ssl_url ) ? $ssl_url : "https://$ssl_url";
            // Explicit HTTP
        } elseif ( false === $ssl || ( 'auto' === $ssl && !$ssl_config ) ) {
            $this->catalog_url = preg_match( '~^http(s)?://~', $url ) ? $url : "http://$ssl_url";
            // Protocol-less scheme
        } else {
            $this->catalog_url = preg_replace( '~^http(s)?://~', '//', $url );
        }
        return $this;
    }

    public function webhookUrl(){
        return $this->catalog_url.'index.php?route=extension/payment/asiabill/webhook';
    }

    public function returnUrl(){
        return $this->catalog_url.'index.php?route=extension/payment/asiabill/result';
    }

    public function confirmUrl(){
        return $this->catalog_url.'index.php?route=extension/payment/asiabill/confirm';
    }

    public function formUrl(){
        return $this->catalog_url.'index.php?route=extension/payment/asiabill/form';
    }

    public function abRequestUrl($mode)
    {
        if($mode == '1'){
            return $this->domain.'Interface/V2';
        }
        return $this->test_domain.'Interface/V2';
    }
    
}