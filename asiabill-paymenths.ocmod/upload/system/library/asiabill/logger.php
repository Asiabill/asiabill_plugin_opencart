<?php
namespace Asiabill;

class Logger
{
    var $dir_system;
    private $handle;

    function __construct(){
        $this->dir_system = DIR_SYSTEM;
    }

    function openFile($filename,$logging = 0){
        if( $logging == '1' ){
            $this->handle = fopen($this->logPath() . $filename, 'a');
        }
    }

    function mkDir(){
        if(!is_dir($this->logPath()) ){
            @mkdir($this->logPath(),0777);
        }
    }

    function logPath(){
        return $this->dir_system.'storage/logs/asiabill/';
    }

    function write($message){
        if($this->handle){
            fwrite($this->handle, date('Y-m-d H:i:s') . ' - ' . print_r($message, true) . "\n");
        }
    }

    public function destruct(){
        if( $this->handle ){
            fclose($this->handle);
        }
    }


}