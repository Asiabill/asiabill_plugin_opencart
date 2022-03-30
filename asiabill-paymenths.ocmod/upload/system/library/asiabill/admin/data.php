<?php
namespace Asiabill\Admin;

class Data
{
    private $controller;
    var $edition = 'v1.5';
    var $url;

    function __construct($controller){
        $this->controller = $controller;
    }

    function language($key){
        return $this->controller->language->get($key);
    }

    function getAdminSettings($lone_settings = array()){

        $geo_zones_option = $this->geoZonesOption();
        $order_status_option = $this->orderStatusOption();

        $settings = array(
            'general' => array(
                array(
                    'name' => 'payment_asiabill_general_edition',
                    'title' => $this->language('entry_edition'),
                    'type' => 'hidden',
                    'description' => '',
                    'value' => $this->edition
                ),
                array(
                    'name' => 'payment_asiabill_general_order_status_success',
                    'title' => $this->language('entry_order_status_success'),
                    'type' => 'select',
                    'option' => $order_status_option,
                    'description' => '',
                    'value' => $this->getValue('payment_asiabill_general_order_status_success','15')
                ),
                array(
                    'name' => 'payment_asiabill_general_order_status_failed',
                    'title' => $this->language('entry_order_status_failed'),
                    'type' => 'select',
                    'option' => $order_status_option,
                    'description' => '',
                    'value' => $this->getValue('payment_asiabill_general_order_status_failed','10')
                ),
                array(
                    'name' => 'payment_asiabill_general_order_status_cancel',
                    'title' => $this->language('entry_order_status_cancel'),
                    'type' => 'select',
                    'option' => $order_status_option,
                    'description' => '',
                    'value' => $this->getValue('payment_asiabill_general_order_status_cancel','7')
                ),
                array(
                    'name' => 'payment_asiabill_general_logging',
                    'title' => $this->language('entry_logging'),
                    'type' => 'select',
                    'option' => array(
                        array(
                            'text' => $this->language('text_no'),
                            'value' => '0'
                        ),
                        array(
                            'text' => $this->language('text_yes'),
                            'value' => '1'
                        )
                    ),
                    'description' => sprintf($this->language('entry_logging_description'),$this->controller->log_path),
                    'value' => $this->getValue('payment_asiabill_general_logging')
                ),
                array(
                    'name' => 'payment_asiabill_general_webhook',
                    'title' => $this->language('entry_webhook'),
                    'type' => 'select',
                    'option' => array(
                        array(
                            'text' => $this->language('text_no'),
                            'value' => '0'
                        ),
                        array(
                            'text' => $this->language('text_yes'),
                            'value' => '1'
                        )
                    ),
                    'description' => sprintf($this->language('entry_webhook_description'),$this->controller->webhook,$this->controller->webhook),
                    'value' => $this->getValue('payment_asiabill_general_sort_order','1')
                ),
                array(
                    'name' => 'payment_asiabill_general_logo_type',
                    'title' => $this->language('entry_logo_type'),
                    'type' => 'select',
                    'option' => array(
                        array(
                            'text' => $this->language('entry_none'),
                            'value' => '0'
                        ),
                        array(
                            'text' => $this->language('entry_one_way'),
                            'value' => '1'
                        )
                    ),
                    'description' => '',
                    'value' => $this->getValue('payment_asiabill_general_logo_type','0')
                )
            ),
            'asiabill' => array(
                array(
                    'name' => 'payment_'.$this->controller->code.'_mode',
                    'title' => $this->language('entry_transaction_mode'),
                    'type' => 'select',
                    'option' => array(
                        array(
                            'text' => $this->language('entry_transaction_test'),
                            'value' => '0'
                        ),
                        array(
                            'text' => $this->language('entry_transaction_live'),
                            'value' => '1'
                        )
                    ),
                    'description' => '',
                    'value' => $this->getValue('payment_'.$this->controller->code.'_mode')
                ),

                array(
                    'name' => 'payment_'.$this->controller->code.'_mer_no',
                    'title' => $this->language('entry_mer_no'),
                    'type' => 'text',
                    'description' => $this->language('entry_mer_no_description'),
                    'value' => $this->getValue('payment_'.$this->controller->code.'_mer_no')
                ),
                array(
                    'name' => 'payment_'.$this->controller->code.'_gateway_no',
                    'title' => $this->language('entry_gateway_no'),
                    'type' => 'text',
                    'description' => '',
                    'value' => $this->getValue('payment_'.$this->controller->code.'_gateway_no')
                ),
                array(
                    'name' => 'payment_'.$this->controller->code.'_key',
                    'title' => $this->language('entry_key'),
                    'type' => 'text',
                    'description' => '',
                    'value' => $this->getValue('payment_'.$this->controller->code.'_key')
                ),
                array(
                    'name' => 'payment_'.$this->controller->code.'_test_mer_no',
                    'title' => $this->language('entry_test_mer_no'),
                    'type' => 'text',
                    'description' => $this->language('entry_mer_no_description'),
                    'value' => $this->getValue('payment_'.$this->controller->code.'_test_mer_no','12246')
                ),
                array(
                    'name' => 'payment_'.$this->controller->code.'_test_gateway_no',
                    'title' => $this->language('entry_test_gateway_no'),
                    'type' => 'text',
                    'description' => '',
                    'value' => $this->getValue('payment_'.$this->controller->code.'_test_gateway_no','12246002')
                ),
                array(
                    'name' => 'payment_'.$this->controller->code.'_test_key',
                    'title' => $this->language('entry_test_key'),
                    'type' => 'text',
                    'description' => '',
                    'value' => $this->getValue('payment_'.$this->controller->code.'_test_key','12H4567r')
                ),

            ),
            'payment' => array(
                array(
                    'name' => 'payment_'.$this->controller->code.'_status',
                    'title' => $this->language('entry_status'),
                    'type' => 'select',
                    'option' => array(
                        array(
                            'text' => $this->language('text_disabled'),
                            'value' => '0'
                        ),
                        array(
                            'text' => $this->language('text_enabled'),
                            'value' => '1'
                        )
                    ),
                    'description' => '',
                    'value' => $this->getValue('payment_'.$this->controller->code.'_status','0')
                ),
                array(
                    'name' => 'payment_'.$this->controller->code.'_sort_order',
                    'title' => $this->language('entry_sort_order'),
                    'type' => 'text',
                    'description' => '',
                    'value' => $this->getValue('payment_'.$this->controller->code.'_sort_order','1')
                ),
                array(
                    'name' => 'payment_'.$this->controller->code.'_title',
                    'title' => $this->language('entry_payment_title'),
                    'type' => 'text',
                    'description' => '',
                    'value' => $this->getValue('payment_'.$this->controller->code.'_title',$this->controller->title)
                ),
                array(
                    'name' => 'payment_'.$this->controller->code.'_geo_zone_id',
                    'title' => $this->language('entry_geo_zone'),
                    'type' => 'select',
                    'option' => $geo_zones_option,
                    'description' => '',
                    'value' => $this->getValue('payment_'.$this->controller->code.'_geo_zone_id','0')
                ),
            )
        );


        foreach ($settings as $key => $value){
            if( key_exists($key,$lone_settings) ){
                $settings[$key] = array_merge($value,$lone_settings[$key]);
            }
        }

        return $settings;
    }


    function geoZonesOption(){
        $text_all_zones = $this->controller->language->get('text_all_zones');
        $this->controller->load->model('localisation/geo_zone');
        $geo_zones = $this->controller->model_localisation_geo_zone->getGeoZones();

        $arr1 = array(
            array(
                'text' => $text_all_zones,
                'value' => '0'
            )
        );
        $arr2 = array();
        foreach ($geo_zones as $key => $val){
            $arr2[$key] = [
                'text' => $val['name'],
                'value' => $val['geo_zone_id']
            ];
        }
        return array_merge($arr1,$arr2);
    }

    function orderStatusOption(){
        $this->controller->load->model('localisation/order_status');
        $order_statuses = $this->controller->model_localisation_order_status->getOrderStatuses();
        $arr = array();
        foreach ($order_statuses as $key => $val){
            $arr[] = array(
                'text' => $val['name'],
                'value' => $val['order_status_id']
            );
        }
        return $arr;
    }

    function getValue($key,$default = ''){
        $value = key_exists($key,$this->controller->request->post)?trim($this->controller->request->post[$key]):$this->controller->config->get($key);
        if( is_null($value) ) $value = $default;
        return $value;
    }

}