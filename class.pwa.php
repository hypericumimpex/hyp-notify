<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class smpush_support_pwaforwp {
  private $helper;
  private $apisettings;

  public function __construct($apisettings){
    $this->apisettings = $apisettings;
    $this->helper = new smpush_helper();
  }

  public function manifest($manifest){
    if ($this->apisettings['pwaforwp_support'] == 0){
      return $manifest;
    }
    $manifest['gcm_sender_id'] = $this->apisettings['chrome_projectid'];
    return $manifest;
  }
  
  public function sw_name_modify($name){
    if ($this->apisettings['pwaforwp_support'] == 0){
      return $name;
    }
    $smart_sw_name = $this->smart_sw_link($name);
    if($smart_sw_name == $name){
      return $name;
    }

    $url = pwaforwp_site_url();
    $home_url = pwaforwp_home_url();

    if(!is_multisite() && trim($url) !== trim($home_url)){
      $url = esc_url_raw($home_url.'?'.pwaforwp_query_var('sw_query_var').'=1&'.pwaforwp_query_var('sw_file_var').'='.'pwa-sw.js');
    } else {
      $url = esc_url(pwaforwp_home_url().'pwa-sw.js');
    }

    $smart_sw_content = $this->helper->readlocalfile(ABSPATH.'/'.$smart_sw_name);
    if(strpos($smart_sw_content, $url) === false){
      $smart_sw_content .= PHP_EOL."importScripts('".$url."')";
      $this->helper->storelocalfile(ABSPATH.'/'.$smart_sw_name, $smart_sw_content);
      $this->helper->log('success write on PWA SW: '.ABSPATH.'/'.$smart_sw_name.' @ '.PHP_EOL."importScripts('".$url."')");
    }

    return $smart_sw_name;
  }

  public function smart_sw_link($name){
    if($this->apisettings['desktop_webpush'] == 1 && file_exists(ABSPATH.'/smart_push_sw.js')){
      $sw_file = 'smart_firebase_sw.js';
    }
    elseif(file_exists(ABSPATH.'/smart_service_worker.js')){
      $sw_file = 'smart_service_worker.js';
    }
    else{
      //$sw_file = '/?smpushprofile=service_worker';
      $sw_file = $name;
    }
    return $sw_file;
  }

}