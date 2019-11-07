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

class smpush_api extends smpush_controller {
  public $counter = 0;
  public $dateformat;
  public $queryorder;
  protected $carry;

  public function __construct($method='', $returnValue=false, $carry = '', $silent_init = false){
    if($silent_init) return;
    if(!isset($_REQUEST['orderby'])){
      $_REQUEST['orderby'] = '';
    }
    if(!empty($_REQUEST['device_token']) && in_array($_REQUEST['device_type'], self::$webPlatforms)){
      if(base64_encode(base64_decode($_REQUEST['device_token'], true)) === $_REQUEST['device_token']){
        $_REQUEST['device_token'] = base64_decode($_REQUEST['device_token']);
      }
    }
    if(isset($_REQUEST['order'])){
      if(strtolower($_REQUEST['order']) == 'asc')
          $this->queryorder = 'ASC';
      elseif(strtolower($_REQUEST['order']) == 'desc')
          $this->queryorder = 'DESC';
      else
          $this->queryorder = false;
    }
    if(!empty($_REQUEST['device_token']) && ! in_array($_REQUEST['device_type'], self::$webPlatforms)){
      $_REQUEST['device_token'] = urldecode($_REQUEST['device_token']);
    }
    if(empty($method)){
      return;
    }
    else{
      $method = trim($method, '/');
    }
    $auth_key = $this->get_option('auth_key');
    $this->ParseOutput = true;
    $this->internalAPI = false;
    $this->carry = $carry;
    self::$returnValue = $returnValue;
    $samedomain = false;
    if(!empty($carry)){
      $samedomain = true;
    }
    if(!empty($_SERVER['HTTP_REFERER']) && parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) == $_SERVER['HTTP_HOST']){
      $samedomain = true;
    }
    define('SMPUSH_API_SAME_ORIGINE', $samedomain);
    if(!$samedomain && !empty($auth_key) && isset($auth_key) && !in_array($method, array('safari','facebook','get_link','get_archive','go','tracking','unsubscribe','cron_job'))){
      if($this->get_option('complex_auth') == 1){
        $auth_keys = array();
        $minutenow = date('i');
        $minuteafter = ($minutenow+1 > 59)? 0 : $minutenow+1;
        $minutebefore = ($minutenow-1 < 0)? 59 : $minutenow-1;
        $auth_keys[] = md5(date('m/d/Y').$auth_key.date('H').$minutenow);
        $auth_keys[] = md5(date('m/d/Y').$auth_key.date('H').$minuteafter);
        $auth_keys[] = md5(date('m/d/Y').$auth_key.date('H').$minutebefore);
      }
      else{
        $auth_keys = array($auth_key);
      }
      if(!empty($_REQUEST['auth_key'])){
        $input_auth_key = $_REQUEST['auth_key'];
      }
      else{
        $input_auth_key = $this->checkReqHeader('auth_key');
      }
      if(!in_array($input_auth_key, $auth_keys))
        return $this->output(0, __('Authentication failed: Authentication key is required to proceed', 'smpush-plugin-lang'));
    }
    
    if(method_exists($this, $method))
        return $this->$method();
    else
        return $this->output(0, __('You called unavailable method', 'smpush-plugin-lang').' `'.$method.'`');
  }

  public function cron_job(){
    smpush_cronsend::cronStart();
  }

  public function send_notification(){
    $this->CheckParams(array('message'));

    foreach($_REQUEST as $key => $request){
      if(! is_array($request)){
        $_REQUEST[$key] = urldecode($request);
      }
    }

    $setting = array();
    if(!empty($_REQUEST['expire'])){
      $setting['expire'] = $_REQUEST['expire'];
    }
    if(!empty($_REQUEST['ios_slide'])){
      $setting['ios_slide'] = stripslashes($_REQUEST['ios_slide']);
    }
    if(!empty($_REQUEST['ios_badge'])){
      $setting['ios_badge'] = $_REQUEST['ios_badge'];
    }
    if(!empty($_REQUEST['ios_sound'])){
      $setting['ios_sound'] = $_REQUEST['ios_sound'];
    }
    if(!empty($_REQUEST['ios_cavailable'])){
      $setting['ios_cavailable'] = $_REQUEST['ios_cavailable'];
    }
    if(!empty($_REQUEST['ios_launchimg'])){
      $setting['ios_launchimg'] = stripslashes($_REQUEST['ios_launchimg']);
    }
    if(!empty($_REQUEST['customparams'])){
      $setting['extra_type'] = 'json';
      $setting['extravalue'] = stripslashes($_REQUEST['customparams']);
    }
    if(!empty($_REQUEST['android_customparams'])){
      $setting['and_extra_type'] = 'json';
      $setting['and_extravalue'] = stripslashes($_REQUEST['android_customparams']);
    }
    if(!empty($_REQUEST['wp_customparams'])){
      $setting['wp_extra_type'] = 'json';
      $setting['wp_extravalue'] = stripslashes($_REQUEST['wp_customparams']);
    }
    if(!empty($_REQUEST['bb_customparams'])){
      $setting['bb_extra_type'] = 'json';
      $setting['bb_extravalue'] = stripslashes($_REQUEST['bb_customparams']);
    }
    if(!empty($_REQUEST['desktop_link'])){
      $setting['desktop_link'] = stripslashes($_REQUEST['desktop_link']);
    }
    if(!empty($_REQUEST['desktop_title'])){
      $setting['desktop_title'] = stripslashes($_REQUEST['desktop_title']);
    }
    if(!empty($_REQUEST['desktop_icon'])){
      $setting['desktop_icon'] = stripslashes($_REQUEST['desktop_icon']);
    }
    if(!empty($_REQUEST['android_title'])){
      $setting['android_title'] = stripslashes($_REQUEST['android_title']);
    }
    if(!empty($_REQUEST['android_icon'])){
      $setting['android_icon'] = stripslashes($_REQUEST['android_icon']);
    }
    if(!empty($_REQUEST['android_sound'])){
      $setting['android_sound'] = stripslashes($_REQUEST['android_sound']);
    }
    if(!empty($_REQUEST['sendtime'])){
      $sendtime = strtotime(stripslashes($_REQUEST['sendtime']), current_time('timestamp'));
    }
    else{
      $sendtime = 0;
    }
    if(!empty($_REQUEST['latitude']) AND ! empty($_REQUEST['longitude']) AND ! empty($_REQUEST['radius'])) {
      $gps_loc_filter = array();
      $gps_loc_filter['latitude'] = $_REQUEST['latitude'];
      $gps_loc_filter['longitude'] = $_REQUEST['longitude'];
      $gps_loc_filter['radius'] = $_REQUEST['radius'];
      if(!empty($_REQUEST['gps_expire'])){
        $gps_loc_filter['gps_expire'] = $_REQUEST['gps_expire'];
      }
    }
    else{
      $gps_loc_filter = false;
    }
    
    if(!empty($_REQUEST['device_token'])){
      $this->CheckParams(array('device_token','device_type'));
      $tokenid = self::$pushdb->get_var(self::parse_query("SELECT {id_name} FROM {tbname} WHERE {md5token_name}='".md5($_REQUEST['device_token'])."' AND {type_name}='$_REQUEST[device_type]'"));
      smpush_sendpush::SendCronPush(array($tokenid), $_REQUEST['message'], '', 'tokenid', $setting, $sendtime, false, $gps_loc_filter);
      $this->output(1, __('Message sent successfully', 'smpush-plugin-lang'));
    }
    elseif(!empty($_REQUEST['user_id'])){
      $tokeninfo = self::$pushdb->get_row(self::parse_query("SELECT {token_name} AS device_token,{type_name} AS device_type FROM {tbname} WHERE userid='$_REQUEST[user_id]' AND {active_name}='1'"));
      if($tokeninfo){
        smpush_sendpush::SendCronPush($_REQUEST['user_id'], $_REQUEST['message'], '', 'userid', $setting, $sendtime, false, $gps_loc_filter);
        $this->output(1, __('Message sent successfully', 'smpush-plugin-lang'));
      }
      else{
        $this->output(0, __('Did not find data about this user or the user is inactive', 'smpush-plugin-lang'));
      }
    }
    elseif(!empty($_REQUEST['channel'])){
      if($_REQUEST['channel'] == 'all'){
        smpush_sendpush::SendCronPush('all', $_REQUEST['message'], '', '', $setting, $sendtime, false, $gps_loc_filter);
      }
      else{
        smpush_sendpush::SendCronPush($_REQUEST['channel'], $_REQUEST['message'], '', 'channel', $setting, $sendtime, false, $gps_loc_filter);
      }
      $this->output(1, __('Message sent successfully', 'smpush-plugin-lang'));
    }
    elseif(!empty($_REQUEST['platform'])){
      $setting['platforms'] = explode(',', $_REQUEST['platform']);
      smpush_sendpush::SendCronPush(((empty($sendtime)? 'now' : 'time')), $_REQUEST['message'], '', '', $setting, $sendtime, false, $gps_loc_filter);
      $this->output(1, __('Message sent successfully', 'smpush-plugin-lang'));
    }
    elseif(!empty($_REQUEST['latitude'])) {
      $this->CheckParams(array('longitude','radius'));
      smpush_sendpush::SendCronPush('all', $_REQUEST['message'], '', '', $setting, $sendtime, false, $gps_loc_filter);
      $this->output(1, __('Message sent successfully', 'smpush-plugin-lang'));
    }
    $this->output(1, __('Wrong parameters', 'smpush-plugin-lang'));
  }

  public function process_refresh_token($tokenid, $token, $type){
    if(empty($tokenid)){
      return false;
    }

    self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {token_name}='".$token."',{md5token_name}='".md5($token)."',{firebase_name}='1' WHERE {id_name}='$tokenid'"));

    self::$firebase->subscribeToTopic('public', $token);
    self::$firebase->subscribeToTopic($type, $token);

    $userid = self::$pushdb->get_var(self::parse_query("SELECT userid FROM {tbname} WHERE {id_name}='$tokenid'"));
    if(!empty($userid)){
      self::$firebase->subscribeToTopic('user_'.$userid, $token);
    }

    global $wpdb;
    $subschans = $wpdb->get_results("SELECT channel_id FROM ".SMPUSHTBPRE."push_relation WHERE token_id='$tokenid'");
    if($subschans){
      foreach($subschans AS $subschan){
        self::$firebase->subscribeToTopic('channel_'.$subschan->channel_id, $token);
      }
    }
  }

  public function refresh_token(){
    $this->CheckParams(array('device_token','device_type','device_old_token'));

    $tokenid = self::$pushdb->get_var(self::parse_query("SELECT {id_name} FROM {tbname} WHERE {md5token_name}='".md5($_REQUEST['device_old_token'])."' AND {type_name}='$_REQUEST[device_type]'"));
    if($tokenid){
      $this->process_refresh_token($tokenid);
    }
    return $this->output(1, __('Subscription has been refreshed successfully', 'smpush-plugin-lang'));
  }

  public function savetoken($printout=true){
    $this->CheckParams(array('device_token','device_type'));
    if(empty($_REQUEST['device_info'])){
      $_REQUEST['device_info'] = '';
    }
    if(!empty($_REQUEST['token_type']) && $_REQUEST['token_type'] == 'amp'){
      $new_token = self::$firebase->convert($_REQUEST['device_token']);
      if(!empty($new_token)){
        $_REQUEST['device_token'] = $new_token;
        $_REQUEST['firebase'] = 1;
      }
    }
    if(!isset($_REQUEST['active'])){
      $_REQUEST['active'] = 1;
    }
    if(!empty($_REQUEST['firebase']) && $_REQUEST['device_type'] == 'safari'){
      $_REQUEST['firebase'] = 0;
    }
    if(empty($_REQUEST['latitude']) OR empty($_REQUEST['longitude'])){
      $_REQUEST['latitude'] = '0';
      $_REQUEST['longitude'] = '0';
      $locationinfo = smpush_geoloc::get_location_info();
      if($locationinfo !== false){
        $_REQUEST['latitude'] = $locationinfo['latitude'];
        $_REQUEST['longitude'] = $locationinfo['longitude'];
      }
    }

    global $wpdb;
    $device_type = $_REQUEST['device_type'];
    $types_name = $wpdb->get_row("SELECT ios_name,iosfcm_name,android_name,wp_name,bb_name,chrome_name,safari_name,firefox_name,wp10_name,fbmsn_name,fbnotify_name,opera_name,edge_name,samsung_name,email_name FROM ".$wpdb->prefix."push_connection WHERE id='".self::$apisetting['def_connection']."'", ARRAY_A);
    $types_name = array_flip($types_name);
    if(!isset($types_name[$device_type])){
      $supported_types = implode(' , ', array_flip($types_name));
      $this->output(0, __('Wrong device type value. System supports the following device types', 'smpush-plugin-lang').' '.$supported_types);
    }
    if($_REQUEST['active'] == 1 && !empty($_REQUEST['latitude']) && !empty($_REQUEST['longitude'])){
      $wpdb->query("UPDATE ".$wpdb->prefix."push_archive SET processed='0' WHERE send_type='geofence' AND status='1'");
    }
    $tokenid = self::$pushdb->get_var(self::parse_query("SELECT {id_name} FROM {tbname} WHERE {md5token_name}='".md5($_REQUEST['device_token'])."' AND {type_name}='$_REQUEST[device_type]'"));
    if($tokenid > 0){
      self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='$_REQUEST[active]',{info_name}='$_REQUEST[device_info]',{latitude_name}='$_REQUEST[latitude]',{longitude_name}='$_REQUEST[longitude]',{gpstime_name}='".current_time('timestamp')."' WHERE {id_name}='$tokenid'"));
      if(!empty($_REQUEST['user_id'])){
        self::$pushdb->query(self::parse_query("UPDATE {tbname} SET userid='$_REQUEST[user_id]' WHERE {id_name}='$tokenid'"));
        if(!empty($_REQUEST['firebase'])){
          self::$firebase->subscribeToTopic('user_'.$_REQUEST['user_id'], $_REQUEST['device_token']);
        }
        if(isset($_REQUEST['channels_id'])){
          update_user_meta($_REQUEST['user_id'], 'smpush_subscribed_channels', $_REQUEST['channels_id']);
        }
      }
      elseif(is_user_logged_in()){
        self::$pushdb->query(self::parse_query("UPDATE {tbname} SET userid='".get_current_user_id()."' WHERE {id_name}='$tokenid'"));
        if(!empty($_REQUEST['firebase'])){
          self::$firebase->subscribeToTopic('user_'.get_current_user_id(), $_REQUEST['device_token']);
        }
        setcookie('smpush_linked_user', 'true', (time()+2592000), COOKIEPATH);
      }
      do_action('smpush_update_subscriber', $tokenid, $_REQUEST['device_type']);
      if(!$printout) return $tokenid;
      return $this->output(1, __('Token saved successfully', 'smpush-plugin-lang'));
    }
    do_action('smpush_new_subscriber_presaved', $_REQUEST['device_token'], $_REQUEST['device_type']);
    self::$pushdb->query(self::parse_query("INSERT INTO {tbname} ({token_name},{md5token_name},{type_name},{info_name},{active_name},{latitude_name},{longitude_name},{gpstime_name},{postdate},{firebase_name}) VALUES ('$_REQUEST[device_token]','".md5($_REQUEST['device_token'])."','$device_type','$_REQUEST[device_info]','$_REQUEST[active]','$_REQUEST[latitude]','$_REQUEST[longitude]','".current_time('timestamp')."','".gmdate('Y-m-d H:i:s', current_time('timestamp'))."','1')"));
    $tokenid = self::$pushdb->insert_id;
    if($tokenid === false){
      return $this->output(0, __('Push database connection error', 'smpush-plugin-lang'));
    }

    if(!empty($_REQUEST['firebase'])){
      self::$firebase->subscribeToTopic('public', $_REQUEST['device_token']);
      self::$firebase->subscribeToTopic($_REQUEST['device_type'], $_REQUEST['device_token']);
    }

    $this->saveStats($_REQUEST['device_type'], 'newdevice');
    if(!empty($_REQUEST['user_id'])){
      self::$pushdb->query(self::parse_query("UPDATE {tbname} SET userid='$_REQUEST[user_id]' WHERE {id_name}='$tokenid'"));
      if(isset($_REQUEST['channels_id'])){
        update_user_meta($_REQUEST['user_id'], 'smpush_subscribed_channels', $_REQUEST['channels_id']);
      }
    }
    elseif(is_user_logged_in()){
      self::$pushdb->query(self::parse_query("UPDATE {tbname} SET userid='".get_current_user_id()."' WHERE {id_name}='$tokenid'"));
      setcookie('smpush_linked_user', 'true', (time()+2592000), COOKIEPATH);
    }
    $defconid = self::$apisetting['def_connection'];
    self::$pushdb->query(self::parse_query("UPDATE ".SMPUSHTBPRE."push_connection SET counter=counter+1 WHERE id='$defconid'"));
    if(isset($_REQUEST['channels_id']) && !empty($_REQUEST['user_id'])){
      self::updateUserChannels($_REQUEST['user_id'], $_REQUEST['channels_id']);
    }
    elseif(!empty($_REQUEST['channels_id']) && $_REQUEST['channels_id'] == -1){
    }
    elseif(!empty($_REQUEST['channels_id'])){
      $chids = explode(',', $_REQUEST['channels_id']);
      foreach($chids AS $chid){
        $wpdb->query("INSERT INTO ".SMPUSHTBPRE."push_relation (channel_id,token_id,connection_id) VALUES ('$chid','$tokenid','$defconid')");
      }
      $wpdb->query("UPDATE ".SMPUSHTBPRE."push_channels SET `count`=`count`+1 WHERE id IN($_REQUEST[channels_id])");
    }
    else{
      $defchid = $wpdb->get_var("SELECT id FROM ".SMPUSHTBPRE."push_channels WHERE `default`='1'");
      $wpdb->query("INSERT INTO ".SMPUSHTBPRE."push_relation (channel_id,token_id,connection_id) VALUES ('$defchid','$tokenid','$defconid')");
      $wpdb->query("UPDATE ".SMPUSHTBPRE."push_channels SET `count`=`count`+1 WHERE id='$defchid'");
    }
    do_action('smpush_new_subscriber_saved', $tokenid, $_REQUEST['device_type']);
    if(!$printout) return $tokenid;
    return $this->output(1, __('Token saved successfully', 'smpush-plugin-lang'));
  }

  public function deletetoken(){
    if(SMPUSH_API_SAME_ORIGINE === true && is_user_logged_in() && !empty($_POST['source']) && $_POST['source'] == 'delete_button'){
      $_REQUEST['user_id'] = get_current_user_id();
    }

    if(!empty($_REQUEST['user_id'])){
      $tokens = self::$pushdb->get_results(self::parse_query("SELECT {type_name} AS platform,{id_name} AS tokenid FROM {tbname} WHERE userid='$_REQUEST[user_id]'"));
      if($tokens){
        foreach($tokens as $token){
          $this->saveStats($token->platform, 'invdevice');
          self::$pushdb->query(self::parse_query("DELETE FROM {tbname} WHERE {id_name}='$token->tokenid'"));
          self::$pushdb->query("DELETE FROM ".SMPUSHTBPRE."push_relation WHERE token_id='$token->tokenid'");
        }
      }
      self::$pushdb->query("DELETE FROM ".SMPUSHTBPRE."push_subscriptions WHERE userid='$_REQUEST[user_id]'");
      if(SMPUSH_API_SAME_ORIGINE === true){
        setcookie('smpush_desktop_request', '', time()-3600, COOKIEPATH);
        setcookie('smpush_safari_device_token', '', time()-3600, COOKIEPATH);
        setcookie('smpush_device_token', '', time()-3600, COOKIEPATH);
        setcookie('smpush_desktop_welcmsg_seen', '', time()-3600, COOKIEPATH);
        setcookie('smpush_linked_user', '', time()-3600, COOKIEPATH);
        setcookie('smpush_fresh_linked_user', '', time()-3600, COOKIEPATH);
        setcookie('smart_push_smio_coords_latitude', '', time()-3600, COOKIEPATH);
        setcookie('smart_push_smio_coords_longitude', '', time()-3600, COOKIEPATH);
      }
      return $this->output(1, __('Token subscription deleted successfully', 'smpush-plugin-lang'));
    }
    else{
      $this->CheckParams(array('device_token','device_type'));
      $token = self::$pushdb->get_row(self::parse_query("SELECT {type_name} AS platform,{id_name} AS tokenid FROM {tbname} WHERE {md5token_name}='".md5($_REQUEST['device_token'])."' AND {type_name}='$_REQUEST[device_type]'"));
      if(!empty($token->tokenid)){
        $this->saveStats($token->platform, 'invdevice');
        self::$pushdb->query(self::parse_query("DELETE FROM {tbname} WHERE {id_name}='$token->tokenid'"));
        self::$pushdb->query("DELETE FROM ".SMPUSHTBPRE."push_relation WHERE token_id='$token->tokenid'");
      }
      return $this->output(1, __('Token subscription deleted successfully', 'smpush-plugin-lang'));
    }
  }

  public function channels_subscribe(){
    if(!empty($_REQUEST['user_id']) && empty($_REQUEST['oneuserid']) && (empty($_REQUEST['device_token']) || empty($_REQUEST['device_type']))){
      $tokenid = self::$pushdb->get_var(self::parse_query("SELECT {id_name} AS tokenid FROM {tbname} WHERE userid='$_REQUEST[user_id]' ORDER BY {id_name} ASC LIMIT 0,1"));
    }
    elseif(empty($_REQUEST['oneuserid'])){
      $tokenid = $this->savetoken(false);
    }

    if(isset($_REQUEST['channels_id']) && !empty($_REQUEST['oneuserid'])){
      self::editSubscribedChannels(0, $_REQUEST['channels_id'], $_REQUEST['oneuserid']);
    }
    elseif(isset($_REQUEST['channels_id']) && !empty($_REQUEST['user_id'])){
      self::updateUserChannels($_REQUEST['user_id'], $_REQUEST['channels_id']);
    }
    elseif(isset($_REQUEST['channels_id'])){
      self::editSubscribedChannels($tokenid, $_REQUEST['channels_id']);
    }
    return $this->output(1, __('Subscription saved successfully', 'smpush-plugin-lang'));
  }

  public static function updateUserChannels($userid, $newchannels){
    global $wpdb;
    $tokens = $wpdb->get_results(self::parse_query("SELECT {id_name} AS tokenid FROM {tbname} WHERE userid='$userid'"));
    if($tokens){
      foreach($tokens AS $token){
        self::editSubscribedChannels($token->tokenid, $newchannels);
      }
    }
  }

  public static function editSubscribedChannels($tokenid, $newchannels, $oneuserid=0){
    global $wpdb;
    if(!empty($oneuserid)){
      $defconid = 0;
      $where = SMPUSHTBPRE."push_relation.userid='$oneuserid'";
    }
    else{
      $defconid = self::$apisetting['def_connection'];
      $where = SMPUSHTBPRE."push_relation.token_id='$tokenid' AND ".SMPUSHTBPRE."push_relation.connection_id='$defconid'";
    }
    $newchannels = explode(',', $newchannels);

    $subschans = self::$pushdb->get_results(self::parse_query("SELECT ".SMPUSHTBPRE."push_relation.channel_id,{tbname}.{firebase_name} AS firebase,{tbname}.{token_name} AS token FROM ".SMPUSHTBPRE."push_relation
    LEFT JOIN {tbname} ON({tbname}.{id_name}=".SMPUSHTBPRE."push_relation.token_id)
    WHERE $where"));
    $chids = [];
    $chinfos = [];
    if($subschans){
      foreach($subschans AS $subschan){
        $chids[] = $subschan->channel_id;
        $chinfos[$subschan->channel_id] = $subschan;
      }
    }

    $channels_toadd = array_diff($newchannels, $chids);
    if(!empty($channels_toadd)){
      if(!empty($tokenid)){
        $newtoken = self::$pushdb->get_row(self::parse_query("SELECT {firebase_name} AS firebase,{token_name} AS token FROM {tbname} WHERE {id_name}='$tokenid'"));
      }
      foreach($channels_toadd AS $chid){
        $wpdb->query("INSERT INTO ".SMPUSHTBPRE."push_relation (channel_id,token_id,connection_id,userid) VALUES ('$chid','$tokenid','$defconid','$oneuserid')");
        if(!empty($newtoken) && $newtoken->firebase == 1){
          self::$firebase->subscribeToTopic('channel_'.$chid, $newtoken->token);
        }
      }
      $wpdb->query("UPDATE ".SMPUSHTBPRE."push_channels SET `count`=`count`+1 WHERE id IN(".implode(',', $channels_toadd).")");
    }

    $channels_todel = array_diff($chids, $newchannels);
    if(!empty($channels_todel)){
      foreach($channels_todel AS $chid){
        $wpdb->query("DELETE FROM ".SMPUSHTBPRE."push_relation WHERE $where AND channel_id='$chid'");
        if($chinfos[$chid]->firebase == 1){
          self::$firebase->unsubscribeFromTopic('channel_'.$chid, $chinfos[$chid]->token);
        }
      }
      $wpdb->query("UPDATE ".SMPUSHTBPRE."push_channels SET `count`=`count`-1 WHERE id IN(".implode(',', $channels_todel).")");
    }
  }

  public function facebook(){
    if($_GET['action'] == 'callback'){
      if(isset($_GET['hub_challenge'])){
        echo $_GET['hub_challenge'];
        exit;
      }
      $input = json_decode(file_get_contents('php://input'), true);
      $fbuid = $input['entry'][0]['messaging'][0]['sender']['id'];
      $_REQUEST['device_token'] = $fbuid;
      $_REQUEST['device_type'] = 'fbmsn';
      $fbprofile = json_decode($this->buildCurl('https://graph.facebook.com/v2.12/'.$fbuid.'?fields=first_name,last_name&access_token='.self::$apisetting['msn_accesstoken']), true);
      if(!empty($fbprofile['first_name']) || !empty($fbprofile['lname'])){
        $_REQUEST['device_info'] = trim($fbprofile['first_name'].' '.$fbprofile['lname']);
      }
      if(!empty($input['entry'][0]['messaging'][0]['optin']['ref'])){
        $reference = explode('_', $input['entry'][0]['messaging'][0]['optin']['ref']);
        $reference_command = $reference[0];
        $reference_userid = $reference[1];
      }
      if(!empty($input['entry'][0]['messaging'][0]['message']['text']) && $input['entry'][0]['messaging'][0]['message']['text'] == self::$apisetting['msn_subs_command']){
        $this->savetoken(false);
        $params = array(
        'access_token' => self::$apisetting['msn_accesstoken'],
        'recipient' => json_encode(array('id' => $fbuid)),
        'message' => json_encode(array('text' => __('Got your request and processed your subscription successfully...thank you', 'smpush-plugin-lang')))
        );
        $helper = new smpush_helper();
        $helper->buildCurl('https://graph.facebook.com/v2.10/me/messages', false, $params);
      }
      elseif(!empty($input['entry'][0]['messaging'][0]['message']['text']) && $input['entry'][0]['messaging'][0]['message']['text'] == self::$apisetting['msn_unsubs_command']){
        $params = array(
        'access_token' => self::$apisetting['msn_accesstoken'],
        'recipient' => json_encode(array('id' => $fbuid)),
        'message' => json_encode(array('text' => __('Got your request and processed your subscription termination successfully...thank you', 'smpush-plugin-lang')))
        );
        $helper = new smpush_helper();
        $helper->buildCurl('https://graph.facebook.com/v2.10/me/messages', false, $params);
        $this->deletetoken();
      }
      elseif(!empty($input['entry'][0]['messaging'][0]['optin']['ref']) && $reference_command == 'subscribed'){
        $_REQUEST['user_id'] = $reference_userid;
        $this->savetoken(false);
      }
      elseif(!empty($input['entry'][0]['messaging'][0]['optin']['ref']) && $reference_command == 'unsubscribed'){
        $this->deletetoken();
      }
      elseif(empty(self::$apisetting['msn_subs_command'])){
        $this->savetoken(false);
      }
    }
    elseif($_GET['action'] == 'login'){
      if(!empty($_REQUEST['code'])){
        $fburl = 'https://graph.facebook.com/v2.10/oauth/access_token';
        $fburl .= '?client_id='.((empty(self::$apisetting['fbnotify_appid']))? self::$apisetting['msn_appid'] : self::$apisetting['fbnotify_appid']);
        $fburl .= '&client_secret='.((empty(self::$apisetting['fbnotify_secret']))? self::$apisetting['msn_accesstoken'] : self::$apisetting['fbnotify_secret']);
        $fburl .= '&redirect_uri='.get_bloginfo('wpurl').'/'.self::$apisetting['push_basename'].'/facebook/?action=login';
        $fburl .= '&code='.$_REQUEST['code'];
        $data = json_decode($this->buildCurl($fburl), true);
        if(!empty($data['access_token'])){
          $fbprofile = json_decode($this->buildCurl('https://graph.facebook.com/v2.10/me?fields=name,email,first_name,last_name&access_token='.$data['access_token']), true);
          if(!empty($fbprofile['id'])){
            unset($_REQUEST);
            $_REQUEST['device_token'] = $fbprofile['id'];
            $_REQUEST['device_type'] = 'fbnotify';
            if(!empty($fbprofile['first_name']) || !empty($fbprofile['last_name'])){
              $_REQUEST['device_info'] = trim($fbprofile['first_name'].' '.$fbprofile['last_name']);
            }

            if(!empty(self::$apisetting['fbnotify_appid']) && self::$apisetting['fblogin_regin_fbnotifs'] == 1){
              $this->savetoken(false);
            }
            if(!empty($fbprofile['email']) && self::$apisetting['fblogin_regin_newsletter'] == 1){
              $_REQUEST['device_token'] = $fbprofile['email'];
              $_REQUEST['device_type'] = 'email';
              $this->savetoken(false);
            }
            if(self::$apisetting['fblogin_regin_wpuser'] == 1){
              $this->fblogin($fbprofile, $data['access_token']);
            }
          }
          echo __('Successful subscription...thank you', 'smpush-plugin-lang');
          echo '<script>parent.postMessage(["action", "success_fblogin"],"*");</script>';
        }
      }
      echo '<script>setTimeout(function(){ window.close(); }, 3000);</script>';
    }
    elseif($_GET['action'] == 'canvas'){
      if(!empty($_GET['outlink'])){
        echo '<script type="text/javascript"> window.top.location.href = "'.urldecode($_GET['outlink']).'"; </script>';
        exit;
      }
      elseif(!empty($_GET['inapp'])){
        echo '<script type="text/javascript"> window.location = "'.urldecode($_GET['inapp']).'"; </script>';
        exit;
      }
      if(!empty($_REQUEST['signed_request'])){
        $data = $this->parse_signed_request($_REQUEST['signed_request'], self::$apisetting['fbnotify_secret']);
        if(!empty($data['oauth_token'])){
          $fbprofile = json_decode($this->buildCurl('https://graph.facebook.com/v2.10/me?fields=name,email,picture,gender,locale,first_name,last_name,link,timezone&access_token='.$data['oauth_token']), true);
          if(!empty($fbprofile['id'])){
            unset($_REQUEST);
            $_REQUEST['device_token'] = $fbprofile['id'];
            $_REQUEST['device_type'] = 'fbnotify';
            if(!empty($fbprofile['first_name']) || !empty($fbprofile['lname'])){
              $_REQUEST['device_info'] = trim($fbprofile['first_name'].' '.$fbprofile['lname']);
            }
            $this->savetoken(false);
          }
        }
        else{
          $this->fbAuthenticate();
          exit;
        }
      }
      if(self::$apisetting['fbnotify_method'] == 'iframe'){
        if(empty(self::$apisetting['fbnotify_width'])){
          self::$apisetting['fbnotify_width'] = 800;
        }
        if(empty(self::$apisetting['fbnotify_height'])){
          self::$apisetting['fbnotify_height'] = 800;
        }
        echo '<iframe width="'.self::$apisetting['fbnotify_width'].'px" height="'.self::$apisetting['fbnotify_height'].'px" src="'.self::$apisetting['fbnotify_applink'].'"></iframe>';
      }
      else{
        echo '<script>window.location="'.self::$apisetting['fbnotify_applink'].'"</script>';
      }
    }
    exit;
  }

  private function fbAuthenticate() {
    include(smpush_dir.'/lib/facebook/fbsdk.php');
    echo '<!DOCTYPE html><html><head>'
    . '<title>'.get_bloginfo('name').'</title><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">'
    . '<style>.alert-success{margin: 0 20px}button.fbloginbtn{margin:50px auto}div.container{padding:20px 0;max-width:600px;margin-top:25px;background-color:#fff;border:1px solid #ccc;-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;}@media screen and (max-width: 610px) {div.container {margin-right: 15px;margin-left: 15px}</style>'
    . '<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">'
    . '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">'
    . '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">'
    . '<body style="font-family: "Open Sans", sans-serif;"><div class="container text-center">';

    $facebook = new FacebookSDK(array(
      'appId' => (empty(self::$apisetting['msn_appid']))? self::$apisetting['fbnotify_appid']:self::$apisetting['msn_appid'],
      'secret' => (empty(self::$apisetting['msn_secret']))? self::$apisetting['fbnotify_secret']:self::$apisetting['msn_secret'],
      'cookie' => false
     ));

    $fbloginurl = $facebook->getLoginUrl($params = array('scope' => 'public_profile,email', 'redirect_uri' => get_bloginfo('wpurl').'/'.self::$apisetting['push_basename'].'/facebook/?action=login'));
    echo '<button type="button" class="fbloginbtn btn btn-primary" onclick=\'openFBpopup("'.$fbloginurl.'", this)\'><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEwAACxMBAJqcGAAAAWlJREFUSIntU79LQlEYPff10Aj19ZPQSkMcGuo/cKqpplb3XIIIbYkKkmhMszWnNseotVVqaUoqrITsoViG6GD4631NPnwNvmu+mjzb+TjfOfc7cBlAzOuLBUhhASZgGoZAyRIJkUR8LcK8vtMgwMLGGP8EbQmksMDfmAMAC4rd1jI1aYNlyATGgMpXHZlcqaNc5DV2OYZxsLkE98yIOrtL5bEeuuy4J/AGhDYWNea84LrAPmGFxzWq8vvnd6TlIuRc2ZgAyWrW8OjZNR5ePnhW+StqBxFxa3UviO6swDM7ppkdbS+j2VSQTOWxe3zVW4BkHYRk0VbU4vVGU2/9dxW1UChWdDXM64vpFjrnHkfscFXl/r1zPKYLXI/o6YJ+QD/gfwK4/oHZJMJpl1SeyZVQrTW4AkRAyQKCo5OoWmvg6fWTy7AdpEAWiIRI15u8YBQeeEte3DgXbssAmwdgM8KXFMgA7Sfi/pNvMHlsbazqbs0AAAAASUVORK5CYII=" />'
    . '&nbsp;&nbsp;'.__('Facebook Login', 'smpush-plugin-lang').'</button>';

    echo '<script>function openFBpopup(url, elm){var new_fbwindow = window.open(url, "", "width=800,height=600");var popupTick = setInterval(function() {if (new_fbwindow.closed) {clearInterval(popupTick);window.top.location.href="https://apps.facebook.com/'.self::$apisetting['fbnotify_appid'].'";}}, 500);}</script>';
    echo '</div></body></html>';
  }

  private function fblogin($profile, $access_token){
    global $wpdb;
    //id,name,email,picture,gender,locale,first_name,last_name,link,timezone
    $newpass = wp_generate_password(10, false);
    $userid = $wpdb->get_var("SELECT user_id FROM ".$wpdb->usermeta." WHERE meta_key='smpush_social_id' AND meta_value='".$profile['id']."'");
    if (empty($userid)) {
      $duplicate = $wpdb->get_var("SELECT ID FROM ".$wpdb->users." WHERE user_login='$profile[id]' OR user_email='$profile[email]'");
      if(!empty($duplicate)){
        echo "<script language=\"text/javascript\">\n";
        echo "alert('".__('Username or Email is already exists in our records!', 'smpush-plugin-lang')."');\n";
        echo "window.close();";
        echo "</script>";
        return false;
      }
      $userdata = array(
      'user_login' => $profile['id'],
      'user_email' => $profile['email'],
      'display_name' => addslashes($profile['name']),
      'nickname' => addslashes($profile['name']),
      'first_name' => addslashes($profile['first_name']),
      'last_name' => addslashes($profile['last_name']),
      'user_pass' => $newpass,
      );
      $userid = wp_insert_user($userdata);
      update_user_meta($userid, 'smpush_social_id', $profile['id']);
      update_user_meta($userid, 'smpush_fb_token', $access_token);
      update_user_meta($userid, 'gender', $profile['gender']);
    }
    else {
      update_user_meta($userid, 'smpush_fb_token', $access_token);
    }

    update_user_meta($userid, 'profile_picture', addslashes($profile['picture']['data']['url']));
    update_user_meta($userid, 'website', addslashes($profile['link']));
    wp_set_auth_cookie($userid, true, false);
  }

  public function safari(){
    if(strpos($this->carry, '/devices/') !== false){
      preg_match('/devices\/([a-zA-Z0-9]+)\/registrations/', htmlspecialchars_decode($this->carry), $matches);
      $deviceToken = $matches[1];
      if(empty($deviceToken)){
        die();
      }
      $_REQUEST['device_token'] = $deviceToken;
      $_REQUEST['device_type'] = 'safari';
      if($_SERVER['REQUEST_METHOD'] == "POST"){
        $_REQUEST['active'] = '1';
        $this->savetoken();
      }
      elseif($_SERVER['REQUEST_METHOD'] == "DELETE"){
        $_REQUEST['active'] = '0';
        $this->savetoken();
        $this->saveStats('safari', 'invdevice');
      }
    }
    elseif(strpos($this->carry, '/pushPackages') !== false){
      if(empty(self::$apisetting['safari_pack_path']) || !file_exists(self::$apisetting['safari_pack_path'])){
        $packpath = $this->buildSafariPackFile(self::$apisetting);
        self::$apisetting['safari_pack_path'] = $packpath;
        self::$apisetting = array_map('wp_slash', self::$apisetting);
        update_option('smpush_options', self::$apisetting);
      }
      else{
        $packpath = self::$apisetting['safari_pack_path'];
      }
      if(!file_exists($packpath)){
        echo 'something error';
        exit;
      }
      ob_end_clean();
      ob_end_flush();
      header('Content-type: application/zip; charset=utf-8');
      header('Content-Disposition: attachment; filename="package.zip"');
      header('Content-Length: '.filesize($packpath));
      header('Pragma: public');
      header('Expires: 0');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Cache-Control: public');
      header('Content-Transfer-Encoding: binary');
      if(function_exists('file_get_contents')){
        echo file_get_contents($packpath);
      }
      elseif(function_exists('fopen')){
        $handle = fopen($packpath, 'r');
        $content = fread($handle, filesize($packpath));
        fclose($handle);
        echo $content;
      }
      die;
    }
    elseif(strpos($this->carry, '/log') !== false){
      $body = file_get_contents('php://input');
      $body = json_decode($body, true);
      if(!empty($body['logs'])){
        global $wpdb;
        foreach($body['logs'] as $error => $log){
          $wpdb->insert($wpdb->prefix.'push_archive', array('send_type' => 'feedback', 'message' => $log, 'starttime' => gmdate('Y-m-d H:i:s', current_time('timestamp'))));
        }
      }
    }
    $this->output(1, __('Success', 'smpush-plugin-lang'));
  }

  public function get_link(){
    $this->CheckParams(array('id'));
    if(!empty($_GET['call']) && $_GET['call'] == 'silent'){
      $this->saveStats($_REQUEST['platform'], 'clicks', $_GET['id']);
      exit;
    }
    global $wpdb;
    $message = $wpdb->get_row("SELECT id,options FROM ".$wpdb->prefix."push_archive WHERE id='$_REQUEST[id]'", ARRAY_A);
    $message['options'] = unserialize($message['options']);
    if(!empty($_REQUEST['platform'])){
      $this->saveStats($_REQUEST['platform'], 'clicks', $message['id']);
    }
    if(!empty($_REQUEST['platform']) && $_REQUEST['platform'] == 'fbmsn' && !empty($message['options']['fbmsn_link'])){
      $link = urldecode(self::cleanString($message['options']['fbmsn_link']));
    }
    else{
      $link = urldecode(self::cleanString($message['options']['desktop_link']));
    }
    if(!empty($link) && !empty($message['options']['desktop_utm_source'])){
      $utm = 'utm_source='.$message['options']['desktop_utm_source'].'&utm_medium='.$message['options']['desktop_utm_medium'].'&utm_campaign='.$message['options']['desktop_utm_campaign'];
      if(strpos($link, '?') !== false){
        $link .= '&'.$utm;
      } else {
        $link .= '?'.$utm;
      }
    }
    if(empty($link)){
      $link = get_bloginfo('wpurl');
    }
    echo '<script data-cfasync="false" type="text/javascript">window.location="'.$link.'"</script>';
    exit;
  }

  public function clicks(){
    $this->CheckParams(array('clicks','platform'));
    if(!in_array($_REQUEST['platform'], self::$platforms)){
      $supported_types = implode(' , ', self::$platforms);
      $this->output(0, __('Wrong device type value. System supports the following device types', 'smpush-plugin-lang').' '.$supported_types);
    }
    if(!empty($_REQUEST['msgid'])){
      $this->saveStats($_REQUEST['platform'], 'clicks', $_REQUEST['msgid'], $_REQUEST['clicks']);
    }
    else{
      $this->saveStats($_REQUEST['platform'], 'clicks', 0, $_REQUEST['clicks']);
    }
    $this->output(1, 'Saved successfully');
  }

  public function views(){
    $this->CheckParams(array('views','platform'));
    if(!in_array($_REQUEST['platform'], self::$platforms)){
      $supported_types = implode(' , ', self::$platforms);
      $this->output(0, __('Wrong device type value. System supports the following device types', 'smpush-plugin-lang').' '.$supported_types);
    }
    if(!empty($_REQUEST['msgid'])){
      $this->saveStats($_REQUEST['platform'], 'views', $_REQUEST['msgid'], $_REQUEST['views']);
    }
    else{
      $this->saveStats($_REQUEST['platform'], 'views', 0, $_REQUEST['views']);
    }
    $this->output(1, 'Saved successfully');
  }

  public function go(){
    $this->CheckParams(array('id','platform','target'));
    global $wpdb;
    if(!in_array($_REQUEST['platform'], self::$platforms)){
      exit;
    }
    if(!empty($_GET['deviceid'])){
      $viewid = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."push_newsletter_views WHERE msgid='$_GET[id]' AND deviceid='$_GET[deviceid]' AND action='click'");
      if(!$viewid){
        $data = array();
        $data['msgid'] = $_GET['id'];
        $data['deviceid'] = $_GET['deviceid'];
        $data['platid'] = $_GET['platform'];
        $data['action'] = 'click';
        $wpdb->insert($wpdb->prefix.'push_newsletter_views', $data);

        $this->saveStats($_REQUEST['platform'], 'clicks', $_REQUEST['id']);
      }
    }
    else{
      $this->saveStats($_REQUEST['platform'], 'clicks', $_REQUEST['id']);
    }
    $link = urldecode(self::cleanString($_REQUEST['target']));
    if(empty($link)){
      $link = get_bloginfo('wpurl');
    }
    echo '<script data-cfasync="false" type="text/javascript">window.location="'.$link.'"</script>';
    exit;
  }

  public function unsubscribe() {
    $this->CheckParams(array('id','platform','deviceid'));
    $deviceID = base64_decode($_GET['deviceid']);

    $device = self::$pushdb->get_row(self::parse_query("SELECT {id_name} AS id,{active_name} AS status FROM {tbname} WHERE {md5token_name}='".md5($deviceID)."' AND {type_name}='{email_name}'"));
    if(! $device){
      global $wpdb;
      $userid = $wpdb->get_var("SELECT id FROM $wpdb->users WHERE user_email='$deviceID'");
      if($userid){
        $emailStatus = $wpdb->get_var("SELECT email FROM ".$wpdb->prefix."push_subscriptions WHERE userid='$userid'");
        if($emailStatus == 1){
          $wpdb->query("UPDATE ".$wpdb->prefix."push_subscriptions SET email='0' WHERE userid='$userid'");
          $this->saveStats($_GET['platform'], 'invdevice');
        }
      }
    }
    elseif($device && $device->status == 1){
      self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='0' WHERE {id_name}='$device->id'"));
      $this->saveStats($_GET['platform'], 'invdevice');
    }

    echo 'successfully unsubscribed';
    exit;
  }

  public function tracking() {
    $this->CheckParams(array('id','platform','deviceid'));
    global $wpdb;

    if(is_numeric($_GET['deviceid'])){
      $where = "deviceid='$_GET[deviceid]'";
    }
    else{
      $where = "device_hash='".md5(base64_decode($_GET['deviceid']))."'";
    }

    $viewid = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."push_newsletter_views WHERE msgid='$_GET[id]' AND $where AND action='view'");
    if(!$viewid){
      $data = array();
      $data['msgid'] = $_GET['id'];
      if(is_numeric($_GET['deviceid'])){
        $data['deviceid'] = $_GET['deviceid'];
      }
      else{
        $data['device_hash'] = md5(base64_decode($_GET['deviceid']));
      }
      $data['platid'] = $_GET['platform'];
      $data['action'] = 'view';
      $wpdb->insert($wpdb->prefix.'push_newsletter_views', $data);

      $this->saveStats($_GET['platform'], 'views', $_GET['id']);
    }
    header('Content-Type: image/gif');
    echo $this->readlocalfile(smpush_dir.'/images/unnamed.gif');
    exit;
  }

  public function get_archive(){
    global $wpdb;
    $order = 'DESC';
    $where = '';
    $push_archiveTB = $wpdb->prefix.'push_archive';
    if(!empty($_REQUEST['order'])){
      if(strtolower($_REQUEST['order']) == 'asc') $order = 'ASC';
      else $order = 'DESC';
    }
    if(!empty($_REQUEST['platform'])){
      if($_REQUEST['platform'] == 'chrome'){
        $where = "AND $push_archiveTB.desktop LIKE '%chrome%'";
      }
      elseif($_REQUEST['platform'] == 'firefox'){
        $where = "AND $push_archiveTB.desktop LIKE '%firefox%'";
      }
      elseif($_REQUEST['platform'] == 'safari'){
        $where = "AND $push_archiveTB.desktop LIKE '%safari%'";
      }
      elseif($_REQUEST['platform'] == 'opera'){
        $where = "AND $push_archiveTB.desktop LIKE '%opera%'";
      }
      elseif($_REQUEST['platform'] == 'samsung'){
        $where = "AND $push_archiveTB.desktop LIKE '%samsung%'";
      }
      elseif($_REQUEST['platform'] == 'edge'){
        $where = "AND $push_archiveTB.desktop LIKE '%edge%'";
      }
      else{
        die();
      }
    }
    if(!empty($_REQUEST['deviceID'])){
      if(self::$apisetting['desktop_offline'] == 1){
        $historyLimit = 4;
      }
      else{
        $historyLimit = 1;
      }
      $sql = "SELECT $push_archiveTB.id,$push_archiveTB.message,$push_archiveTB.starttime,$push_archiveTB.options FROM ".$wpdb->prefix."push_desktop_messages
      INNER JOIN $push_archiveTB ON($push_archiveTB.id=".$wpdb->prefix."push_desktop_messages.msgid AND $push_archiveTB.status='1')
      WHERE ".$wpdb->prefix."push_desktop_messages.token='".md5($_REQUEST['deviceID'])."' AND ".$wpdb->prefix."push_desktop_messages.type='$_REQUEST[platform]' ORDER BY ".$wpdb->prefix."push_desktop_messages.timepost DESC LIMIT 0,$historyLimit";
      $gets = $wpdb->get_results($sql, 'ARRAY_A');
      if(!$gets) return $this->output(1, array());
      if($gets){
        foreach($gets as $get){
          $this->saveStats($_REQUEST['platform'], 'views', $get['id']);
        }
        $wpdb->query("DELETE FROM ".$wpdb->prefix."push_desktop_messages WHERE token='".md5($_REQUEST['deviceID'])."' AND type='$_REQUEST[platform]'");
      }
    }
    elseif($_REQUEST['userid']){
      if(!empty($_REQUEST['mainPlatforms'])){
        if($_REQUEST['mainPlatforms'] == 'mobile'){
          $where = "AND ".$wpdb->prefix."push_history.platform='mobile'";
        }
        elseif($_REQUEST['mainPlatforms'] == 'fbmsn'){
          $where = "AND ".$wpdb->prefix."push_history.platform='fbmsn'";
        }
        elseif($_REQUEST['mainPlatforms'] == 'fbnotify'){
          $where = "AND ".$wpdb->prefix."push_history.platform='fbnotify'";
        }
        elseif($_REQUEST['mainPlatforms'] == 'email'){
          $where = "AND ".$wpdb->prefix."push_history.platform='email'";
        }
        else{
          $where = "AND ".$wpdb->prefix."push_history.platform='web'";
        }
      }
      else{
        $where = "AND ".$wpdb->prefix."push_history.platform='web'";
      }
      $sql = "SELECT $push_archiveTB.id,$push_archiveTB.message,".$wpdb->prefix."push_history.timepost AS starttime,".$wpdb->prefix."push_history.platform AS mainPlatform,$push_archiveTB.options FROM ".$wpdb->prefix."push_history
      INNER JOIN $push_archiveTB ON($push_archiveTB.id=".$wpdb->prefix."push_history.msgid AND $push_archiveTB.status='1')
      WHERE ".$wpdb->prefix."push_history.userid='$_REQUEST[userid]' $where GROUP BY ".$wpdb->prefix."push_history.msgid ORDER BY ".$wpdb->prefix."push_history.timepost $order";
      $sql = $this->Paging($sql, $wpdb);
      $gets = $wpdb->get_results($sql, 'ARRAY_A');
      if(!$gets) return $this->output(0, __('No result found', 'smpush-plugin-lang'));
    }
    else{
      $sql = "SELECT id,message,starttime,options FROM ".$wpdb->prefix."push_archive WHERE send_type IN('now','time','geofence','custom') $where ORDER BY id ".$order;
      $sql = $this->Paging($sql, $wpdb);
      $gets = $wpdb->get_results($sql, 'ARRAY_A');
      if(!$gets) return $this->output(0, __('No result found', 'smpush-plugin-lang'));
    }
    $siteurl = get_bloginfo('wpurl');
    $messages = array();
    foreach ($gets as $get){
      $options = unserialize($get['options']);
      $message = array();
      $message['id'] = $get['id'];
      if(!empty($get['mainPlatform'])){
        if($get['mainPlatform'] == 'web'){
          $message['message'] = html_entity_decode(preg_replace("/U\+([0-9A-F]{4,5})/i", "&#x\\1;", self::cleanString($get['message'])), ENT_NOQUOTES, 'UTF-8');
        }
        elseif($get['mainPlatform'] == 'mobile'){
          $message['message'] = html_entity_decode(preg_replace("/U\+([0-9A-F]{4,5})/i", "&#x\\1;", self::cleanString($get['message'])), ENT_NOQUOTES, 'UTF-8');
        }
        elseif($get['mainPlatform'] == 'fbmsn'){
          $message['message'] = html_entity_decode(preg_replace("/U\+([0-9A-F]{4,5})/i", "&#x\\1;", self::cleanString($options['fbmsn_message'])), ENT_NOQUOTES, 'UTF-8');
        }
        elseif($get['mainPlatform'] == 'fbnotify'){
          $message['message'] = html_entity_decode(preg_replace("/U\+([0-9A-F]{4,5})/i", "&#x\\1;", self::cleanString($options['fbnotify_message'])), ENT_NOQUOTES, 'UTF-8');
        }
        elseif($get['mainPlatform'] == 'email'){
          $message['message'] = htmlspecialchars_decode(self::cleanString($options['email']));
        }
      }
      else{
        $message['message'] = html_entity_decode(preg_replace("/U\+([0-9A-F]{4,5})/i", "&#x\\1;", self::cleanString($get['message'])), ENT_NOQUOTES, 'UTF-8');
      }
      $message['starttime'] = $get['starttime'];
      $message['mobtitle'] = self::cleanString($options['android_title'], true);
      $message['title'] = self::cleanString($options['desktop_title'], true);
      if(empty($_REQUEST['platform']) && (empty($_REQUEST['mainPlatforms']) || $_REQUEST['mainPlatforms'] != 'web')){
        $message['link'] = '';
        $payload = json_decode($options['extravalue'], true);
        if(!empty($payload) && !empty($payload['link'])){
          $message['link'] = $payload['link'];
        }
      }
      elseif(self::$apisetting['no_disturb'] == 1){
        $message['link'] = $siteurl;
        $message['target'] = $siteurl.'/'.self::$apisetting['push_basename'].'/get_link/?id='.$get['id'].'&platform='.$_REQUEST['platform'];
      }
      else{
        $message['link'] = $siteurl.'/'.self::$apisetting['push_basename'].'/get_link/?id='.$get['id'].'&platform='.$_REQUEST['platform'];
        $message['target'] = '';
      }
      $message['icon'] = (!empty($options['desktop_icon']))? self::cleanString($options['desktop_icon']) : '';

      $message['renotify'] = (self::$apisetting['no_disturb'] == 1)? true : false;

      $message['actions'] = array();
      if(!empty($options['desktop_actions'])){
        foreach($options['desktop_actions']['id'] as $ackey => $action){
          //$message['actions'][$ackey]['keyid'] = self::cleanString($options['desktop_actions']['id'][$ackey]);
          $message['actions'][$ackey]['keyid'] = 'button_id_'.$ackey;
          $message['actions'][$ackey]['id'] = 'button_'.$ackey;
          $message['actions'][$ackey]['text'] = self::cleanString($options['desktop_actions']['text'][$ackey]);
          $message['actions'][$ackey]['icon'] = self::cleanString(urldecode($options['desktop_actions']['icon'][$ackey]));
          $desktop_link = self::cleanString(urldecode($options['desktop_actions']['link'][$ackey]));
          $message['actions'][$ackey]['link'] = $siteurl.'/'.self::$apisetting['push_basename'].'/go/?id='.$get['id'].'&platform='.$_REQUEST['platform'].'&target='.urlencode($desktop_link);
        }
      }
      $message['post_id'] = (empty($options['post_id']))? false : $options['post_id'];
      $message['post_type'] = (empty($options['post_type']))? false : $options['post_type'];
      $message['direction'] = (empty($options['desktop_dir']))? 'auto' : $options['desktop_dir'];
      $message['vibrate'] = (empty($options['desktop_vibrate']))? array() : explode('.', $options['desktop_vibrate']);
      $message['silent'] = (!isset($options['desktop_silent']))? '' : $options['desktop_silent'];
      if(empty($options['desktop_icon'])){
        $message['icon'] = '';
      }
      else{
        $message['icon'] = self::cleanString(urldecode($options['desktop_icon']));
      }
      if(empty($options['desktop_bigimage'])){
        $message['bigimage'] = '';
      }
      else{
        $message['bigimage'] = self::cleanString(urldecode($options['desktop_bigimage']));
      }
      if(empty($options['desktop_badge'])){
        $message['badge'] = '';
      }
      else{
        $message['badge'] = self::cleanString(urldecode($options['desktop_badge']));
      }
      if(empty($options['desktop_sound'])){
        $message['sound'] = '';
      }
      else{
        $message['sound'] = self::cleanString(urldecode($options['desktop_sound']));
      }
      $message['requireInteraction'] = (empty($options['desktop_interaction']))? 'false' : 'true';

      $messages[] = $message;
    }
    return $this->output(1, $messages);
  }

  public function views_tracker(){
    $this->saveStats($_REQUEST['platform'], 'views', $_REQUEST['id']);
  }

  public function device_channels(){
    global $wpdb;
    if(!empty($_REQUEST['user_id']) && empty($_REQUEST['oneuserid']) && (empty($_REQUEST['device_token']) || empty($_REQUEST['device_type']))){
      $tokenid = self::$pushdb->get_var(self::parse_query("SELECT {id_name} AS tokenid FROM {tbname} WHERE userid='$_REQUEST[user_id]' ORDER BY {id_name} ASC LIMIT 0,1"));
    }
    elseif(empty($_REQUEST['oneuserid'])){
      $tokenid = $this->savetoken(false);
    }
    if(!empty($_REQUEST['oneuserid'])){
      $subschans = $wpdb->get_results("SELECT channel_id FROM ".SMPUSHTBPRE."push_relation WHERE userid='$_REQUEST[oneuserid]'");
    }
    else{
      $defconid = self::$apisetting['def_connection'];
      $subschans = $wpdb->get_results("SELECT channel_id FROM ".SMPUSHTBPRE."push_relation WHERE token_id='$tokenid' AND connection_id='$defconid'");
    }
    if($subschans){
      foreach($subschans AS $subschan){
        $chids[] = $subschan->channel_id;
      }
    }
    else $chids = array();
    return $this->get_channels($chids);
  }

  public function save_subscription(){
    if(SMPUSH_API_SAME_ORIGINE === false && !is_user_logged_in()){
      $this->CheckParams(array('user_id'));
    }
    if(SMPUSH_API_SAME_ORIGINE === true){
      $this->internalAPI = true;
      $_POST['user_id'] = get_current_user_id();
    }
    else{
      $this->CheckParams(array('user_id'));
    }
    global $wpdb;
    $subscription = array();
    $subscription['userid'] = $_POST['user_id'];
    if(empty($_POST['categories'])){
      $subscription['categories'] = '';
    }
    else{
      $subscription['categories'] = implode(',', $_POST['categories']);
    }

    $subscription['keywords'] = (empty($_POST['keywords']))? '' : $_POST['keywords'];

    if(empty($_POST['latitude']) || empty($_POST['longitude']) || empty($_POST['radius'])){
      $subscription['latitude'] = 0;
      $subscription['longitude'] = 0;
      $subscription['radius'] = 0;
    }
    else{
      $subscription['latitude'] = $_POST['latitude'];
      $subscription['longitude'] = $_POST['longitude'];
      $subscription['radius'] = $_POST['radius'];
    }

    $subscription['temp'] = (empty($_POST['temp']))? 0 : intval($_POST['temp']);

    $subscription['web'] = (empty($_POST['web']))? 0 : 1;
    $subscription['mobile'] = (empty($_POST['mobile']))? 0 : 1;
    $subscription['msn'] = (empty($_POST['msn']))? 0 : 1;
    $subscription['email'] = (empty($_POST['email']))? 0 : 1;

    $subsid = $wpdb->get_var("SELECT userid FROM ".$wpdb->prefix."push_subscriptions WHERE userid='$_POST[user_id]'");
    if($subsid){
      $wpdb->update($wpdb->prefix.'push_subscriptions', $subscription, array('userid' => $subsid));
    }
    else{
      $wpdb->insert($wpdb->prefix.'push_subscriptions', $subscription);
    }

    $_REQUEST['oneuserid'] = $_POST['user_id'];
    $_REQUEST['channels_id'] = implode(',', $_POST['channels']);
    $this->channels_subscribe();

    return $this->output(1, __('Subscription is saved successfully', 'smpush-plugin-lang'));
  }

  public function subscription(){
    $this->CheckParams(array('user_id'));
    global $wpdb;
    $userSubs = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."push_subscriptions WHERE userid='$_REQUEST[user_id]'", 'ARRAY_A');
    if(!empty($userSubs['categories'])){
      $userSubs['categories'] = explode(',', $userSubs['categories']);
    }

    $subscription = array();

    $_REQUEST['oneuserid'] = $_REQUEST['user_id'];
    $this->internalAPI = true;
    $subscription['channels'] = $this->device_channels();
    if(!empty($_REQUEST['client']) && $_REQUEST['client'] == 'smartwpapp'){
      $this->internalAPI = false;
    }

    $catimageplugin = (function_exists('z_taxonomy_image_url'))?true:false;
    $subscription['categories'] = array();
    $taxonomies = get_terms(array('taxonomy' => self::$apisetting['subspage_post_type_tax']), 'hide_empty=0');
    foreach ($taxonomies as $taxonomy){
      if(!in_array($taxonomy->term_id, self::$apisetting['subspage_category']))        continue;
      $cimage = '';
      if($catimageplugin){
        $cimage = z_taxonomy_image_url($taxonomy->term_id, true);
        if($cimage !== false)
          $cimage = $cimage;
        else
          $cimage = '';
      }
      if(!empty($userSubs['categories']) && in_array($taxonomy->term_id, $userSubs['categories'])){
        $selectedCat = 1;
      }
      else{
        $selectedCat = 0;
      }
      $subscription['categories'][] = array('id' => $taxonomy->term_id, 'name' => $taxonomy->name, 'image' => $cimage, 'selected' => $selectedCat);
    }

    if(empty($userSubs['radius'])){
      $subscription['latitude'] = '';
      $subscription['longitude'] = '';
      $subscription['radius'] = 0;
    }
    else{
      $subscription['latitude'] = $userSubs['latitude'];
      $subscription['longitude'] = $userSubs['longitude'];
      $subscription['radius'] = $userSubs['radius'];
    }

    $subscription['keywords'] = (empty($userSubs['keywords']))? '' : $userSubs['keywords'];
    $subscription['temp'] = $userSubs['temp'];
    $subscription['web'] = (isset($userSubs['web']))? $userSubs['web'] : 1;
    $subscription['mobile'] = (isset($userSubs['mobile']))? $userSubs['mobile'] : 1;
    $subscription['msn'] = (isset($userSubs['msn']))? $userSubs['msn'] : 1;
    $subscription['email'] = (isset($userSubs['email']))? $userSubs['email'] : 1;

    return $this->output(1, $subscription);
  }

  public function get_channels($chids=false){
    global $wpdb;
    if($_REQUEST['orderby'] == 'subscribers')
        $orderby = 'push_channels.`count`';
    elseif($_REQUEST['orderby'] == 'name')
        $orderby = 'push_channels.title';
    elseif($_REQUEST['orderby'] == 'date')
        $orderby = 'push_channels.id';
    else
        $orderby = 'push_channels.id';
    $arg = array(
    'where' => array('push_channels.private'=>0),
    'orderby' => $orderby,
    'order' => ($this->queryorder) ? $this->queryorder:'ASC'
    );
    $sql = "SELECT * FROM ".$wpdb->prefix."push_channels {where} {order}";
    $sql = $this->queryBuild($sql, $arg);
    $channels = $wpdb->get_results($sql, 'ARRAY_A');
    if($channels){
      if($chids !== false){
        foreach($channels AS $channel){
          if(in_array($channel['id'], $chids))
            $channel['subscribed'] = 'yes';
          else
            $channel['subscribed'] = 'no';
          $get[] = $channel;
        }
        return $this->output(1, $get);
      }
      return $this->output(1, $channels);
    }
    else{
      return $this->output(0, __('No result found', 'smpush-plugin-lang'));
    }
  }

  public function add_channel(){
    $this->CheckParams(array('title'));
    global $wpdb;
    if(!empty($_REQUEST['unique'])){
      $bool = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."push_channels WHERE title='$_REQUEST[title]'");
      if($bool){
        $this->output(0, __('This channel name is taken', 'smpush-plugin-lang'));
      }
    }
    $data = array();
    $data['title'] = $_REQUEST['title'];
    $data['description'] = (!empty($_REQUEST['description']))? $_REQUEST['description'] : '';
    $data['private'] = (!empty($_REQUEST['private']))? 1 : 0;
    $data['default'] = 0;
    $data['count'] = 0;
    $wpdb->insert($wpdb->prefix.'push_channels', $data);
    $this->output($wpdb->insert_id, __('Channel added successfully', 'smpush-plugin-lang'));
  }

  public function update_channel(){
    $this->CheckParams(array('id','title','private'));
    global $wpdb;
    if(!empty($_REQUEST['unique'])){
      $bool = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."push_channels WHERE title='$_REQUEST[title]' AND id!='$_REQUEST[id]'");
      if($bool){
        $this->output(0, __('This channel name is taken', 'smpush-plugin-lang'));
      }
    }
    $data = array();
    $data['title'] = $_REQUEST['title'];
    $data['description'] = (!empty($_REQUEST['description']))? $_REQUEST['description'] : '';
    $data['private'] = (!empty($_REQUEST['private']))? 1 : 0;
    $wpdb->update($wpdb->prefix.'push_channels', $data, array('id' => $_REQUEST['id']));
    $this->output(1, __('Channel updated successfully', 'smpush-plugin-lang'));
  }

  public function delete_channel(){
    $this->CheckParams(array('id'));
    global $wpdb;
    $wpdb->delete($wpdb->prefix.'push_channels', array('id' => $_REQUEST['id']));
    $wpdb->delete($wpdb->prefix.'push_relation', array('channel_id' => $_REQUEST['id'], 'connection_id' => self::$apisetting['def_connection']));
    $this->output(1, __('Channel deleted successfully', 'smpush-plugin-lang'));
  }

  public function reset_counter(){
    if(! empty($_REQUEST['userid'])){
      $this->CheckParams(array('device_type'));
      self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {counter_name}='0' WHERE userid='".$_REQUEST['user_id']."' AND {type_name}='$_REQUEST[device_type]'"));
    }
    else{
      $this->CheckParams(array('device_token','device_type'));
      self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {counter_name}='0' WHERE {md5token_name}='".md5($_REQUEST['device_token'])."' AND {type_name}='$_REQUEST[device_type]'"));
    }
    $this->output(1, 'Device has been reset successfully');
  }

  public function woo_waiting_list(){
    $this->CheckParams(array('productid'));
    global $wpdb;
    $userid = $tokenid = 0;
    if(is_user_logged_in()){
      $userid = get_current_user_id();
      $where = "userid='$userid'";
    }
    elseif(!empty($_COOKIE['smpush_safari_device_token'])){
      $this->CheckParams(array('device_type'));
      $tokenid = self::$pushdb->get_var(self::parse_query("SELECT {id_name} FROM {tbname} WHERE {md5token_name}='".md5($_COOKIE['smpush_safari_device_token'])."' AND {type_name}='$_REQUEST[device_type]'"));
      $where = "tokenid='$tokenid'";
    }
    else{
      $this->output(0, 'unregistered device');
    }
    $notifierid = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."push_notifier WHERE $where AND object_id='$_REQUEST[productid]' AND type='wooWaiting'");
    if($notifierid){
      $this->output(0, __('This item is already exist in your waiting list', 'smpush-plugin-lang'));
    }
    else{
      $wpdb->insert($wpdb->prefix.'push_notifier', array('userid' => $userid, 'tokenid' => $tokenid, 'object_id' => $_REQUEST['productid'], 'type' => 'wooWaiting'));
    }
    $this->output(1, __('Item has been added successfully', 'smpush-plugin-lang'));
  }

  private function saveStats($platid, $action, $msgid=0, $rate=1){
    global $wpdb;
    $current_date = gmdate('Y-m-d', current_time('timestamp'));
    $where = (empty($msgid))? '' : 'AND msgid="'.$msgid.'"';
    $statid = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."push_statistics WHERE platid='$platid' AND `date`='$current_date' AND action='$action' $where");
    if(empty($statid)){
      $stat = array();
      $stat['platid'] = $platid;
      $stat['date'] = $current_date;
      $stat['action'] = $action;
      $stat['msgid'] = (empty($msgid))? 0 : $msgid;
      $stat['stat'] = $rate;
      $wpdb->insert($wpdb->prefix.'push_statistics', $stat);
    }
    else{
      $wpdb->query("UPDATE ".$wpdb->prefix."push_statistics SET `stat`=`stat`+$rate WHERE id='$statid'");
    }
  }

  public function debug(){
    $this->output(1, __('Push notification system is active now and work under version', 'smpush-plugin-lang').' '.get_option('smpush_version'));
  }

  public static function delete_relw_app($user_id){
    global $wpdb;
    $wpdb->delete(SMPUSHTBPRE.'sm_push_tokens', array('userid' => $user_id));
    $wpdb->delete(SMPUSHTBPRE.'push_subscriptions', array('userid' => $user_id));
    $wpdb->delete(SMPUSHTBPRE.'push_relation', array('userid' => $user_id));
  }

}