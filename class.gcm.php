<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class smpush_gcm {
  private $helper;
  private $apisettings;
  private $invalidTokens;
  private $chromeTokens;

  public function __construct($apisettings){
    $this->apisettings = $apisettings;
    $this->helper = new smpush_helper();
    $this->invalidTokens = [];
    $this->chromeTokens = [];
  }

  public function collectResponse(){
    if(! empty($this->chromeTokens)){
      $this->chrome();
    }
    return $this->invalidTokens;
  }

  public function firefox($id, $token){
    $chandle = curl_init();
    curl_setopt($chandle, CURLOPT_URL, 'https://updates.push.services.mozilla.com/wpush/v1/'.$token);
    curl_setopt($chandle, CURLOPT_HTTPHEADER, array('TTL: 5184000'));
    curl_setopt($chandle, CURLOPT_POST, TRUE);
    curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
    if(defined('WP_PROXY_HOST')){
      curl_setopt($chandle, CURLOPT_PROXY, WP_PROXY_HOST);
      curl_setopt($chandle, CURLOPT_PROXYPORT, WP_PROXY_PORT);
      curl_setopt($chandle, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
      if(defined('WP_PROXY_USERNAME')){
        curl_setopt($chandle, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME.':'.WP_PROXY_PASSWORD);
        curl_setopt($chandle, CURLOPT_PROXYAUTH, CURLAUTH_ANY);
      }
    }
    if(smpush_env == 'debug'){
      $result = true;
      $httpcode = 200;
      $this->helper->log('sent to: '.$token);
    }
    else{
      $result = curl_exec($chandle);
      $httpcode = curl_getinfo($chandle, CURLINFO_HTTP_CODE);
    }
    if (($httpcode == 404 || $httpcode == 410)) {
      array_push($this->invalidTokens, $id);
    }
    curl_close($chandle);
  }

  public function reset(){
    $this->invalidTokens = [];
    $this->chromeTokens = [];
  }

  public function queue($id, $token, $type){
    if($type == 'firefox'){
      $this->firefox($id, $token);
    } else {
      array_push($this->chromeTokens, ['id' => $id, 'token' => $token]);
    }
  }

  public function chrome(){
    $devices = [];
    foreach($this->chromeTokens as $device){
      $devices[] = $device['token'];
    }
    $fields = array('registration_ids' => $devices, 'data' => ['test' => 'data']);
    $headers = array('Authorization: key='.$this->apisettings['chrome_apikey'], 'Content-Type: application/json');
    $chandle = curl_init();
    curl_setopt($chandle, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($chandle, CURLOPT_POST, true);
    curl_setopt($chandle, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
    if(defined('WP_PROXY_HOST')){
      curl_setopt($chandle, CURLOPT_PROXY, WP_PROXY_HOST);
      curl_setopt($chandle, CURLOPT_PROXYPORT, WP_PROXY_PORT);
      curl_setopt($chandle, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
      if(defined('WP_PROXY_USERNAME')){
        curl_setopt($chandle, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME.':'.WP_PROXY_PASSWORD);
        curl_setopt($chandle, CURLOPT_PROXYAUTH, CURLAUTH_ANY);
      }
    }
    curl_setopt($chandle, CURLOPT_POSTFIELDS, json_encode($fields, defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0));
    if(smpush_env == 'debug'){
      $result = true;
      $httpcode = 200;
      $this->helper->log($devices);
    }
    else{
      $result = curl_exec($chandle);
      $httpcode = curl_getinfo($chandle, CURLINFO_HTTP_CODE);
    }
    $result = json_decode($result, true);
    if(!empty($result['results'])){
      foreach ($result['results'] AS $key => $status) {
        if (isset($status['error'])) {
          if ($status['error'] == 'InvalidRegistration' || $status['error'] == 'NotRegistered' || $status['error'] == 'MismatchSenderId') {
            array_push($this->invalidTokens, $this->chromeTokens[$key]['id']);
          }
        }
        elseif (isset($status['registration_id'])) {
          array_push($this->invalidTokens, $this->chromeTokens[$key]['id']);
        }
      }
    }
    curl_close($chandle);
  }

}