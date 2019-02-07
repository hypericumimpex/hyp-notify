<?php

/*======================================================================*\
|| #################################################################### ||
|| # Push Notification System Wordpress Plugin                        # ||
|| # ---------------------------------------------------------------- # ||
|| # Copyright ©2014-2017 Smart IO Labs Inc. All Rights Reserved.     # ||
|| # This file may not be redistributed in whole or significant part. # ||
|| # --- Smart Push Notification System IS NOT FREE SOFTWARE ---      # ||
|| # https://smartiolabs.com/product/push-notification-system         # ||
|| #################################################################### ||
\*======================================================================*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class blackBerryPushNotification extends smpush_sendpush {

  public static function pushMessage($registatoin_tokens, $payload, $showerror) {
    if ((empty(self::$apisetting['bb_appid']) || empty(self::$apisetting['bb_password']) || empty(self::$apisetting['bb_cpid'])) && $showerror) {
      self::jsonPrint(0, '<p class="error">'.__('Invalid BlackBerry authentication settings', 'smpush-plugin-lang').'</p>');
    }
    $appid = self::$apisetting['bb_appid'];
    $password = self::$apisetting['bb_password'];
    $deliverbefore = gmdate('Y-m-d\TH:i:s\Z', strtotime('+2 minutes'));
    $addresses = '';
    //An array of address must be in PIN format or "push_all"
    foreach ($registatoin_tokens as $value) {
      $addresses .= '<address address-value="'.$value.'"/>';
    }
    $ch = curl_init();
    $messageid = microtime(true);
    $data = '
     --mPsbVQo0a68eIL3OAxnm--
    Content-Type: application/xml
    <?xml version="1.0"?>
    <!DOCTYPE pap PUBLIC "-//WAPFORUM//DTD PAP 2.1//EN"
    "http://www.openmobilealliance.org/tech/DTD/pap_2.1.dtd">
    <pap>
      <push-message push-id="'.$messageid.'" source-reference="'.$appid.'" deliver-before-timestamp="'.$deliverbefore.'">
        '.$addresses.'
        <quality-of-service delivery-method="unconfirmed"/>
      </push-message>
    </pap>
    --mPsbVQo0a68eIL3OAxnm--
    Content-Encoding: binary
    Content-Type: text/html

    '.json_encode($payload, defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0).'

    --mPsbVQo0a68eIL3OAxnm--
    ';

    // set URL and other appropriate options
    if(self::$apisetting['bb_dev_env'] == 1){
      curl_setopt($ch, CURLOPT_URL, 'https://cp'.self::$apisetting['bb_cpid'].'.pushapi.eval.blackberry.com/mss/PD_pushRequest');
    }
    else{
      curl_setopt($ch, CURLOPT_URL, 'https://cp'.self::$apisetting['bb_cpid'].'.pushapi.na.blackberry.com/mss/PD_pushRequest');
    }
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'SAA push application');
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $appid.':'.$password);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_PORT, 443);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    if(defined('WP_PROXY_HOST')){
      curl_setopt($ch, CURLOPT_PROXY, WP_PROXY_HOST);
      curl_setopt($ch, CURLOPT_PROXYPORT, WP_PROXY_PORT);
      curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
      if(defined('WP_PROXY_USERNAME')){
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME.':'.WP_PROXY_PASSWORD);
        curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_ANY);
      }
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: multipart/related; boundary=mPsbVQo0a68eIL3OAxnm; type=application/xml", "Accept: text/html", "Connection: keep-alive"));
    $xmldata = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpcode == 401 && $showerror) {
      self::jsonPrint(0, '<p class="error">'.__('Invalid BlackBerry authentication settings', 'smpush-plugin-lang').'</p>');
    }
    elseif ($httpcode == 503 && $showerror) {
      self::jsonPrint(0, '<p class="error">'.__('BlackBerry push notification server not responding', 'smpush-plugin-lang').'</p>');
    }

    if(function_exists('xml_parser_create')){
      $err = false;
      $p = xml_parser_create();
      xml_parse_into_struct($p, $xmldata, $vals);
      $errorcode = xml_get_error_code($p);
      if ($errorcode > 0) {
        $err = true;
      }
      xml_parser_free($p);
      if (!$err && $vals[1]['tag'] == 'PUSH-RESPONSE' && $vals[2]['attributes']['CODE'] === '1001') {
        return true;
      }
      elseif ($err) {
        self::jsonPrint(0, '<p class="error">'.__('System can not connect with BlackBerry server and returns unkown error', 'smpush-plugin-lang').'</p>');
      }
      else {
        self::jsonPrint(0, '<p class="error">'.__('System can not connect with BlackBerry server and returns error', 'smpush-plugin-lang').': '.$vals[1]['attributes']['DESC'].'</p>');
      }
    }
    return true;
  }

}
