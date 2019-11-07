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

class smpush_cronsend extends smpush_controller {
  private static $startTime;
  private static $totalSent;
  private static $iosCounter;
  private static $andCounter;
  private static $wpCounter;
  private static $wp10Counter;
  private static $bbCounter;
  private static $chCounter;
  private static $saCounter;
  private static $fiCounter;
  private static $Counter9;
  private static $Counter10;
  private static $Counter11;
  private static $Counter12;
  private static $Counter13;
  private static $Counter14;
  private static $Counter15;
  private static $iosDelIDS;
  private static $andDelIDS;
  private static $wpDelIDS;
  private static $wp10DelIDS;
  private static $bbDelIDS;
  private static $chDelIDS;
  private static $saDelIDS;
  private static $fiDelIDS;
  private static $DelIDS9;
  private static $DelIDS10;
  private static $DelIDS11;
  private static $DelIDS12;
  private static $DelIDS13;
  private static $DelIDS14;
  private static $DelIDS15;
  private static $iosDevices;
  private static $andDevices;
  private static $wpDevices;
  private static $wp10Devices;
  private static $bbDevices;
  private static $chDevices;
  private static $saDevices;
  private static $fiDevices;
  private static $Devices9;
  private static $Devices10;
  private static $Devices11;
  private static $Devices12;
  private static $Devices13;
  private static $Devices14;
  private static $Devices15;
  private static $tempunique;
  private static $sendoptions;
  private static $post;
  private static $post_latitude;
  private static $post_longitude;
  private static $post_terms;
  private static $iosFeedback = false;

  public function __construct() {
    parent::__construct();
    global $_wp_using_ext_object_cache;
    $_wp_using_ext_object_cache = null;
  }

  public static function runWooReminders() {
    if(self::$apisetting['e_woo_abandoned'] == 0){
      return;
    }
    $lastrun = get_option('smpush_woo_reminders');
    $interval = (self::$apisetting['e_woo_aband_maxage']*3600)/2;
    $UNIXTIMENOW = time();

    if(!empty($lastrun) && ($lastrun+$interval) > $UNIXTIMENOW){
      return;
    }

    global $wpdb;
    $carts = $wpdb->get_results("SELECT * FROM $wpdb->usermeta WHERE meta_key='_woocommerce_persistent_cart_1' GROUP BY user_id ORDER BY umeta_id DESC", ARRAY_A);
    if($carts){
      $cartlink = wc_get_cart_url();
      foreach($carts as $cart){
        $cartdata = unserialize($cart['meta_value']);
        if(empty($cartdata['cart'])) continue;
        if(isset($cartdata['smpush_reminder_counts']) && $cartdata['smpush_reminder_counts'] >= self::$apisetting['e_woo_aband_times']){
          //echo 'exceeded number of remiders<br>';
          continue;
        }
        if(isset($cartdata['smpush_reminder_lstime']) && $cartdata['smpush_reminder_lstime']+(self::$apisetting['e_woo_aband_interval']*3600) > $UNIXTIMENOW){
          //echo 'closed for last time remider<br>';
          continue;
        }
        if(!isset($cartdata['smpush_reminder_lstime'])){
          $last_active = strtotime(get_user_meta($cart['user_id'], 'last_activity', true));
          if($last_active+(self::$apisetting['e_woo_aband_maxage']*3600) > $UNIXTIMENOW){
            //echo 'still not abandoned<br>';
            continue;
          }
          $cartdata['smpush_reminder_counts'] = 0;
        }
        //echo 'process abandoned cart '.$cart[umeta_id].'<br>';
        $cartdata['smpush_reminder_counts'] += 1;
        $cartdata['smpush_reminder_lstime'] = $UNIXTIMENOW;
        $wpdb->query("UPDATE $wpdb->usermeta SET meta_value='".serialize($cartdata)."' WHERE umeta_id='$cart[umeta_id]'");

        $totalmoney = 0;
        foreach($cartdata['cart'] as $cartitem){
          $totalmoney += $cartitem['line_total'];
        }
        $userdata = get_userdata($cart['user_id']);

        $variables = array('{productscount}','{totalmoney}','{customer_name}');
        $replace = array(count($cartdata['cart']), $totalmoney, $userdata->display_name);

        if(self::$apisetting['e_woo_aband_last_rem'] == 1 && $cartdata['smpush_reminder_counts'] >= self::$apisetting['e_woo_aband_times']){
          $message = str_replace($variables, $replace, self::$apisetting['e_woo_aband_last_message']);
          $cronsetting = array();
          $cronsetting['name'] = 'Abandoned Cart';
          $cronsetting['desktop_title'] = str_replace($variables, $replace, self::$apisetting['e_woo_aband_last_title']);
          $cronsetting['desktop_link'] = $cartlink;
          smpush_sendpush::SendCronPush(array(0 => $cart['user_id']), $message, array(), 'userid', $cronsetting);
        }
        else{
          $message = str_replace($variables, $replace, self::$apisetting['e_woo_aband_message']);
          $cronsetting = array();
          $cronsetting['name'] = 'Abandoned Cart';
          $cronsetting['desktop_title'] = str_replace($variables, $replace, self::$apisetting['e_woo_aband_title']);
          $cronsetting['desktop_link'] = $cartlink;
          smpush_sendpush::SendCronPush(array(0 => $cart['user_id']), $message, array(), 'userid', $cronsetting);
        }
      }
    }

    update_option('smpush_woo_reminders', $UNIXTIMENOW);
  }

  public static function convertToFirebase() {
    if(empty(self::$apisetting['desktop_used_webpush']) || empty(self::$apisetting['chrome_vapid_public'])){
      return;
    }
    $webpushTokens = self::$pushdb->get_results(self::parse_query("SELECT {id_name} AS id, {token_name} AS device_token, {type_name} AS device_type  FROM {tbname} WHERE {active_name}='1' AND {firebase_name}='0'"));
    if($webpushTokens){
      $api = new smpush_api('', '', '', true);
      foreach($webpushTokens as $webpushToken){
        if(substr($webpushToken->device_token, 0, 1) == '{'){
          if(smpush_env == 'logs'){
            self::log('converting token: '.$webpushToken->device_token);
          }
          $new_token = self::$firebase->convert($webpushToken->device_token);
          if($new_token === false){
            self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='0' WHERE {id_name}='$webpushToken->id'"));
          } elseif(! empty($new_token)){
            if(smpush_env == 'logs'){
              self::log('success converted to: '.$new_token);
            }
            $api->process_refresh_token($webpushToken->id, $new_token, $webpushToken->device_type);
          }
        }
      }
    }
  }

  public static function runEventQueue() {
    global $wpdb;
    $TIMENOW = gmdate('Y-m-d H:i:s', current_time('timestamp'));
    $events = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_events_queue WHERE pushtime<'$TIMENOW' ORDER BY id DESC");
    if($events){
      $eventManager = new smpush_events();
      foreach($events as $event){
        $wpdb->query("DELETE FROM ".$wpdb->prefix."push_events_queue WHERE id='$event->id'");
        $eventManager::post_status_change($event->new_status, $event->old_status, $event->post_id, unserialize($event->post));
      }
    }
  }
  
  public static function processMessages() {
    global $wpdb;
    $UNIXTIMENOW = current_time('timestamp');
    $TIMENOW = gmdate('Y-m-d H:i:s', $UNIXTIMENOW);
    if(!empty(self::$apisetting['cron_limit'])){
      $limit = 'LIMIT 0,'.self::$apisetting['cron_limit'];
    }
    else{
      $limit = '';
    }
    $queuemsg = $wpdb->get_row("SELECT GROUP_CONCAT(id SEPARATOR ',') AS ids FROM ".$wpdb->prefix."push_archive WHERE send_type IN('now','time','geofence','custom') AND processed='0' AND status='1' AND starttime<='$TIMENOW' $limit", ARRAY_A);
    if(!empty($queuemsg['ids'])){
      $queuemsg['ids'] = trim($queuemsg['ids'], ',');
      $wpdb->query("UPDATE ".$wpdb->prefix."push_archive SET processed='1' WHERE id IN($queuemsg[ids])");
      $messages = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_archive WHERE id IN($queuemsg[ids])", ARRAY_A);
      if($messages) {
        foreach($messages as $message) {
          $message['options'] = unserialize($message['options']);
          $UNIXTIMENOW = current_time('timestamp');
          $TIMENOW = gmdate('Y-m-d H:i:s', $UNIXTIMENOW);
          $deviceIDs = smpush_sendpush::calculateDevices($message['id']);
          if(empty($deviceIDs)){
            $wpdb->update($wpdb->prefix.'push_archive', array('endtime' => gmdate('Y-m-d H:i:s', current_time('timestamp'))), array('id' => $message['id']));
          }
          else{
            $deviceIDs = explode(',', $deviceIDs);
            if(smpush_env == 'debug'){
              self::log('number of devices: '.count($deviceIDs));
            }
            $devCount = ceil(count($deviceIDs)/30000);
            if(smpush_env == 'debug'){
              self::log('number of loops: '.$devCount);
            }
            if(!empty($message['options']['post_id'])){
              self::$post = get_post($message['options']['post_id']);
              if(self::$apisetting['subspage_post_type'] == self::$post->post_type){
                if(self::$apisetting['subspage_geo_status'] == 1 && !empty(self::$apisetting['subspage_geo_lat']) && !empty(self::$apisetting['subspage_geo_lng'])){
                  self::$post_latitude = get_post_meta($message['options']['post_id'], self::$apisetting['subspage_geo_lat'], true);
                  self::$post_longitude = get_post_meta($message['options']['post_id'], self::$apisetting['subspage_geo_lng'], true);
                }
                elseif(self::$apisetting['subspage_geo_status'] == 1 && !empty(self::$apisetting['subspage_geo_acf'])){
                  $acflatlng = get_field(self::$apisetting['subspage_geo_acf'], self::$post->ID);
                  if(!empty($acflatlng)){
                    self::$post_latitude = $acflatlng['lat'];
                    self::$post_longitude = $acflatlng['lng'];
                  }
                }
                elseif(self::$apisetting['subspage_geo_status'] == 1){
                  $geolatlng = apply_filters('smpush_subscription_geofence', $message['options']['post_id']);
                  if(!empty($geolatlng)){
                    self::$post_latitude = $geolatlng['latitude'];
                    self::$post_longitude = $geolatlng['longitude'];
                  }
                }
                if(self::$apisetting['subspage_cats_status'] == 1){
                  self::$post_terms = wp_get_post_terms(self::$post->ID, self::$apisetting['subspage_post_type_tax'], array('fields' => 'all'));
                }
              }
            }
            for($devLoop=0;$devLoop<$devCount;$devLoop++){
              $tempDeviceIDs = array();
              for($subdevLoop=0;$subdevLoop<30000;$subdevLoop++){
                if(!isset($deviceIDs[(($devLoop*30000)+$subdevLoop)])){
                  break;
                }
                $tempDeviceIDs[] = $deviceIDs[(($devLoop*30000)+$subdevLoop)];
              }
              $tempDeviceIDs = implode(',', $tempDeviceIDs);
              if(self::$apisetting['msgs_interval'] > 0){
                $wpdb->query("UPDATE ".$wpdb->prefix."sm_push_tokens SET receive_again_at='".(current_time('timestamp')+(self::$apisetting['msgs_interval']*60))."' WHERE id IN($tempDeviceIDs)");
              }
              else{
                $wpdb->query("UPDATE ".$wpdb->prefix."sm_push_tokens SET receive_again_at='0' WHERE id IN($tempDeviceIDs)");
              }
              $devices = self::$pushdb->get_results(self::parse_query("SELECT {id_name} AS id, {token_name} AS device_token,{type_name} AS device_type,{counter_name} AS counter,userid,{firebase_name} AS firebase FROM {tbname} WHERE {id_name} IN($tempDeviceIDs) ORDER BY {type_name}"), ARRAY_A);
              if($devices){
                if($message['send_type'] == 'geofence'){
                  $geodevices = array();
                  foreach($devices as $geodevice){
                    $geodevices[] = $geodevice['id'];
                  }
                  self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {geotimeout_name}='$UNIXTIMENOW' WHERE {id_name} IN(".implode(',', $geodevices).")"));
                }
                $cronInserSql = '';
                $cronInserCounter = 0;
                foreach($devices as $device){
                  $cronInserCounter++;
                  $passOptions = -1;
                  if(!empty($message['options']['post_id']) && !empty($device['userid'])){
                    if(!empty($message['options']['once_notify'])){
                      $isReceived = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."push_history WHERE postid='".$message['options']['post_id']."' AND userid='".$device['userid']."' AND platform='".self::platformType($device['device_type'])."'");
                      if($isReceived){
                        self::log('user receive a message about this post');
                        continue;
                      }
                    }
                    if(self::$apisetting['subspage_post_type'] == self::$post->post_type){
                      $passOptions = self::checkSubsription($device['userid']);
                      if($passOptions === false){
                        self::log('does not match user entries');
                        continue;
                      }
                    }
                    $platLock = self::platformType($device['device_type']);
                    if(isset($passOptions[$platLock]) && $passOptions[$platLock] == 0){
                      if(smpush_env == 'debug'){
                        self::log('platform is locked by user');
                      }
                      continue;
                    }
                  }
                  if(isset($message['options']['subs_filter'])){
                    if($message['options']['subs_filter'] == 'only_have' && $passOptions == -1){
                      self::log('send to users have subs only');
                      continue;
                    }
                    if($message['options']['subs_filter'] == 'not_have' && $passOptions != -1){
                      self::log('send to users do not have subs only');
                      continue;
                    }
                  }

                  if(!empty($device['userid'])){
                    $wpdb->insert($wpdb->prefix.'push_history', array('platform' => self::platformType($device['device_type']), 'userid' => $device['userid'], 'msgid' => $message['id'], 'postid' => ((empty($message['options']['post_id']))? 0 : $message['options']['post_id']), 'timepost' => $message['starttime']));
                  }

                  $badgeCounter = 0;
                  if (self::$apisetting['android_msg_counter'] == 1 && $device['device_type'] == 'android') {
                    self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {counter_name}={counter_name}+1 WHERE {id_name}='$device[id]'"));
                    $badgeCounter = $device['counter']+1;
                  }
                  if (self::$apisetting['ios_msg_counter'] == 1 && $device['device_type'] == 'ios') {
                    self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {counter_name}={counter_name}+1 WHERE {id_name}='$device[id]'"));
                    $badgeCounter = $device['counter']+1;
                  }
                  $cronInserSql .= "('$device[id]','$device[device_token]','$device[device_type]','$UNIXTIMENOW','$message[id]','$badgeCounter','$device[firebase]'),";
                  if($cronInserCounter >= 1000){
                    $wpdb->query('INSERT INTO '.$wpdb->prefix.'push_cron_queue (`token_id`,`token`,`device_type`,`sendtime`,`sendoptions`,`counter`,firebase) VALUES '.rtrim($cronInserSql, ',')).';';
                    $cronInserSql = '';
                    $cronInserCounter = 0;
                  }
                }
                if($cronInserCounter < 1000 && $cronInserCounter > 0 && !empty($cronInserSql)){
                  $wpdb->query('INSERT INTO '.$wpdb->prefix.'push_cron_queue (`token_id`,`token`,`device_type`,`sendtime`,`sendoptions`,`counter`,firebase) VALUES '.rtrim($cronInserSql, ',')).';';
                }
              }
            }
          }
          if(!empty($message['options']['emailgroups']) || !empty($message['options']['email_wp_users'])){
            $usergroupsql = '';
            if(!empty($message['options']['emailgroups'])){
              foreach($message['options']['emailgroups'] as $user_role){
                $usergroupsql .= 'OR '.$wpdb->usermeta.'.meta_value LIKE \'%'.$user_role.'%\'';
              }
              $usergroupsql = 'AND ('.ltrim($usergroupsql, 'OR ').')';
            }
            $extraWPEmails = $wpdb->get_results("SELECT $wpdb->users.user_email,$wpdb->usermeta.user_id FROM $wpdb->users
                INNER JOIN $wpdb->usermeta ON($wpdb->usermeta.user_id=$wpdb->users.ID AND $wpdb->usermeta.meta_key='".$wpdb->prefix."capabilities' $usergroupsql)
                GROUP BY $wpdb->users.ID");
            if(!empty($extraWPEmails)){
              foreach($extraWPEmails as $extraWPEmail){
                $passOptions = -1;
                if(!empty($message['options']['post_id'])){
                  if(smpush_env == 'debug'){
                    self::log('checking user ID '.$extraWPEmail->user_id.' email subscription');
                  }
                  $passOptions = self::checkSubsription($extraWPEmail->user_id);
                  if($passOptions === false){
                    continue;
                  }
                  if(!empty($message['options']['email_wp_users']) && empty($passOptions['email'])){
                    if(smpush_env == 'debug'){
                      self::log('user does not use subscription page for emails or is locked');
                    }
                    continue;
                  }
                  elseif(isset($passOptions['email']) && $passOptions['email'] == 0){
                    if(smpush_env == 'debug'){
                      self::log('platform is locked by user');
                    }
                    continue;
                  }
                }
                if(isset($message['options']['subs_filter'])){
                  if($message['options']['subs_filter'] == 'only_have' && $passOptions == -1){
                    continue;
                  }
                  if($message['options']['subs_filter'] == 'not_have' && $passOptions != -1){
                    continue;
                  }
                }
                $crondata = array(
                  'token' => $extraWPEmail->user_email,
                  'device_type' => 'email',
                  'sendtime' => $UNIXTIMENOW,
                  'sendoptions' => $message['id']
                );
                $wpdb->insert($wpdb->prefix.'push_cron_queue', $crondata);
                if(!empty($extraWPEmail->user_id)){
                  $wpdb->insert($wpdb->prefix.'push_history', array('platform' => 'email', 'userid' => $extraWPEmail->user_id, 'msgid' => $message['id'], 'postid' => ((empty($message['options']['post_id']))? 0 : $message['options']['post_id']), 'timepost' => $message['starttime']));
                }
              }
            }
          }
          if(!empty($message['repeat_interval'])){
            $sendtime = strtotime($message['starttime']);
            $UNIXTIMENOW = current_time('timestamp');
            while($sendtime < $UNIXTIMENOW){
              $sendtime = strtotime($message['repeat_interval'].' '.$message['repeat_age'], $sendtime);
            }
            $wpdb->update($wpdb->prefix.'push_archive', array('processed' => 0, 'starttime' => gmdate('Y-m-d H:i:s', $sendtime)), array('id' => $message['id']));
          }
        }
      }
      unset($messages);
    }
  }
  
  public static function checkSubsription($userid) {
    global $wpdb;
    $subsription = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."push_subscriptions WHERE userid='$userid'", 'ARRAY_A');
    if(empty($subsription)){
      return -1;
    }
    else{
      $catMatch = true;
      $keyMatch = true;
      $rateMatch = true;
      $geoMatch = true;
      if(self::$apisetting['subspage_rating'] == 1 && !empty($subsription['temp'])){
        $post_hot_count = get_post_meta(self::$post->ID, 'post_hot_count', true);
        if(empty($post_hot_count) || $post_hot_count < $subsription['temp']){
          if(smpush_env == 'debug'){
            self::log('does not match post temp');
          }
          $rateMatch = false;
        }
      }
      elseif((self::$apisetting['subspage_rating'] == 0 || empty($subsription['temp'])) && self::$apisetting['subspage_matchone'] == 1){
        $rateMatch = false;
      }
      if(self::$apisetting['subspage_keywords'] == 1 && !empty($subsription['keywords'])){
        $subsription['keywords'] = str_replace(',', '|', $subsription['keywords']);
        if(! preg_match('/\s('.$subsription['keywords'].')\s/i', ' '.self::$post->post_title.' ')){
          if(smpush_env == 'debug'){
            self::log('does not match keywords');
          }
          $keyMatch = false;
        }
      }
      elseif((self::$apisetting['subspage_keywords'] == 0 || empty($subsription['keywords'])) && self::$apisetting['subspage_matchone'] == 1){
        $keyMatch = false;
      }
      if(self::$apisetting['subspage_cats_status'] == 1 && !empty($subsription['categories'])){
        if(empty(self::$post_terms)){
          if(smpush_env == 'debug'){
            self::log('does not match taxonomies and no taxonomies');
          }
          $catMatch = false;
        }
        $subsription['categories'] = explode(',', $subsription['categories']);
        $pass_terms = false;
        foreach(self::$post_terms as $post_term) {
          if(in_array($post_term->term_id, $subsription['categories'])){
            $pass_terms = true;
          }
        }
        if($pass_terms === false){
          if(smpush_env == 'debug'){
            self::log('does not match taxonomies');
          }
          $catMatch = false;
        }
      }
      elseif((self::$apisetting['subspage_cats_status'] == 0 || empty($subsription['categories'])) && self::$apisetting['subspage_matchone'] == 1){
        $catMatch = false;
      }
      if(self::$apisetting['subspage_geo_status'] == 1 && !empty($subsription['latitude']) && !empty($subsription['longitude'])){
        $distance = 3959*acos(cos(deg2rad($subsription['latitude']))*cos(deg2rad(self::$post_latitude))*cos(deg2rad(self::$post_longitude)-deg2rad($subsription['longitude']))+sin(deg2rad($subsription['latitude']))*sin(deg2rad(self::$post_latitude)));
        if($distance > $subsription['radius']){
          if(smpush_env == 'debug'){
            self::log('does not match location');
          }
          $geoMatch = false;
        }
      }
      elseif((self::$apisetting['subspage_geo_status'] == 0 || empty($subsription['latitude'])) && self::$apisetting['subspage_matchone'] == 1){
        $geoMatch = false;
      }
      if(self::$apisetting['subspage_matchone'] == 1 && $catMatch === false && $keyMatch === false && $geoMatch === false && $rateMatch === false){
        if(smpush_env == 'debug'){
          self::log('no matches for this user');
        }
        return false;
      }
      elseif(self::$apisetting['subspage_matchone'] == 0 && ($catMatch === false || $keyMatch === false || $geoMatch === false || $rateMatch === false)){
        if(smpush_env == 'debug'){
          self::log('one of matches is missed');
        }
        return false;
      }
      $platslock = array();
      $platslock['web'] = $subsription['web'];
      $platslock['mobile'] = $subsription['mobile'];
      $platslock['fbmsn'] = $subsription['msn'];
      $platslock['email'] = $subsription['email'];
      return $platslock;
    }
  }
  
  public static function cronStart() {
    if(empty(self::$apisetting['purchase_code'])){
      die('Please enter your purchase code in the `Auto Update` page.');
    }

    if(file_exists(smpush_dir.'/index.php.pid') && (filemtime(smpush_dir.'/index.php.pid')+900) < current_time('timestamp')){
      @unlink(smpush_dir.'/index.php.pid');
    }

    if(function_exists('posix_kill') && function_exists('getmypid')){
      include(smpush_dir.'/class.oneprocess.php');

      $pid = new pid(smpush_dir);
      if($pid->already_running) {
        echo 'already process running !';
        self::log('already process running !');
        return false;
      }
    }

    define('processTime', microtime(true));
    register_shutdown_function(array('smpush_cronsend', 'loadedtime'));
    @set_time_limit(0);
    @ini_set('log_errors', 1);
    @ini_set('display_errors', 0);
    if(smpush_env == 'debug'){
      @ini_set('error_reporting', E_ALL);
    }
    else{
      @ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_WARNING);
    }
    @ini_set('error_log', smpush_dir.'/cron_log.log');
    global $wpdb;
    $wpdb->show_errors();
    self::convertToFirebase();
    self::runWooReminders();
    self::runEventQueue();
    self::processMessages();
    self::$startTime = gmdate('Y-m-d H:i:s', current_time('timestamp'));
    self::$totalSent = 0;
    self::$tempunique = '';
    self::resetIOS();
    self::resetAND();
    self::resetWP();
    self::resetWP10();
    self::resetBB();
    self::resetCH();
    self::resetSA();
    self::resetFI();
    self::reset9();
    self::reset10();
    self::reset11();
    self::reset12();
    self::reset13();
    self::reset14();
    self::reset15();
    $TIMENOW = current_time('timestamp');
    if(!session_id()) {
      session_start();
    }
    $types_name = $wpdb->get_row("SELECT ios_name,iosfcm_name,edge_name,android_name,wp_name,bb_name,chrome_name,safari_name,firefox_name,wp10_name,fbmsn_name,fbnotify_name,opera_name,samsung_name,email_name FROM ".$wpdb->prefix."push_connection WHERE id='".self::$apisetting['def_connection']."'");
    $queue = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_cron_queue WHERE $TIMENOW>=sendtime ORDER BY sendoptions ASC");
    if($queue) {
      foreach($queue AS $queueone) {
        if(empty(self::$tempunique)){
          self::$tempunique = $queueone->sendoptions;
          smpush_sendpush::updateStats('', 0, true, $queueone->sendoptions);
        }
        if(self::$tempunique != $queueone->sendoptions){
          if(self::$iosCounter > 0)
            self::sendPushCron('ios');
          if(self::$andCounter > 0)
            self::sendPushCron('android');
          if(self::$wpCounter > 0)
            self::sendPushCron('wp');
          if(self::$wp10Counter > 0)
            self::sendPushCron('wp10');
          if(self::$bbCounter > 0)
            self::sendPushCron('bb');
          if(self::$chCounter > 0)
            self::sendPushCron('chrome');
          if(self::$saCounter > 0)
            self::sendPushCron('safari');
          if(self::$fiCounter > 0)
            self::sendPushCron('firefox');
          if(self::$Counter9 > 0)
            self::sendPushCron('opera');
          if(self::$Counter10 > 0)
            self::sendPushCron('samsung');
          if(self::$Counter11 > 0)
            self::sendPushCron('fbmsn');
          if(self::$Counter12 > 0)
            self::sendPushCron('fbnotify');
          if(self::$Counter13 > 0)
            self::sendPushCron('email');
          if(self::$Counter14 > 0)
            self::sendPushCron('edge');
          if(self::$Counter15 > 0)
            self::sendPushCron('iosfcm');
          self::finishQueue();
          self::$tempunique = $queueone->sendoptions;
          smpush_sendpush::updateStats('', 0, true, $queueone->sendoptions);
        }
        $iosLimit = (self::$apisetting['ios_onebyone'] == 1)? 1 : 1000;
        if(self::$iosCounter >= $iosLimit){
          self::sendPushCron('ios');
        }
        if(self::$andCounter >= 1000){
          self::sendPushCron('android');
        }
        if(self::$wpCounter >= 1000){
          self::sendPushCron('wp');
        }
        if(self::$wp10Counter >= 1000){
          self::sendPushCron('wp10');
        }
        if(self::$bbCounter >= 1000){
          self::sendPushCron('bb');
        }
        if(self::$chCounter >= 1000){
          self::sendPushCron('chrome');
        }
        if(self::$saCounter >= 1000){
          self::sendPushCron('safari');
        }
        if(self::$fiCounter >= 1000){
          self::sendPushCron('firefox');
        }
        if(self::$Counter9 >= 1000){
          self::sendPushCron('opera');
        }
        if(self::$Counter10 >= 1000){
          self::sendPushCron('samsung');
        }
        if(self::$Counter11 >= 1000){
          self::sendPushCron('fbmsn');
        }
        if(self::$Counter12 >= 1000){
          self::sendPushCron('fbnotify');
        }
        if(self::$Counter13 >= 1000){
          self::sendPushCron('email');
        }
        if(self::$Counter14 >= 1000){
          self::sendPushCron('edge');
        }
        if(self::$Counter15 >= 1000){
          self::sendPushCron('iosfcm');
        }
        if($queueone->device_type == $types_name->ios_name) {
          self::$iosDelIDS[] = $queueone->id;
          self::$iosDevices[self::$iosCounter]['token'] = $queueone->token;
          self::$iosDevices[self::$iosCounter]['id'] = $queueone->token_id;
          self::$iosDevices[self::$iosCounter]['badge'] = $queueone->counter;
          self::$iosCounter++;
        }
        elseif($queueone->device_type == $types_name->android_name) {
          self::$andDelIDS[] = $queueone->id;
          self::$andDevices['token'][self::$andCounter] = $queueone->token;
          self::$andDevices['id'][self::$andCounter] = $queueone->token_id;
          self::$andDevices['badge'][self::$andCounter] = $queueone->counter;
          self::$andCounter++;
        }
        elseif($queueone->device_type == $types_name->wp_name) {
          self::$wpDelIDS[] = $queueone->id;
          self::$wpDevices['token'][self::$wpCounter] = $queueone->token;
          self::$wpDevices['id'][self::$wpCounter] = $queueone->token_id;
          self::$wpCounter++;
        }
        elseif($queueone->device_type == $types_name->wp10_name) {
          self::$wp10DelIDS[] = $queueone->id;
          self::$wp10Devices['token'][self::$wp10Counter] = $queueone->token;
          self::$wp10Devices['id'][self::$wp10Counter] = $queueone->token_id;
          self::$wp10Counter++;
        }
        elseif($queueone->device_type == $types_name->bb_name) {
          self::$bbDelIDS[] = $queueone->id;
          self::$bbDevices['token'][self::$bbCounter] = $queueone->token;
          self::$bbDevices['id'][self::$bbCounter] = $queueone->token_id;
          self::$bbCounter++;
        }
        elseif($queueone->device_type == $types_name->chrome_name) {
          self::$chDelIDS[] = $queueone->id;
          self::$chDevices['token'][self::$chCounter] = $queueone->token;
          self::$chDevices['id'][self::$chCounter] = $queueone->token_id;
          self::$chDevices['firebase'][self::$chCounter] = $queueone->firebase;
          self::$chCounter++;
        }
        elseif($queueone->device_type == $types_name->safari_name) {
          self::$saDelIDS[] = $queueone->id;
          self::$saDevices[self::$saCounter]['token'] = $queueone->token;
          self::$saDevices[self::$saCounter]['id'] = $queueone->token_id;
          self::$saCounter++;
        }
        elseif($queueone->device_type == $types_name->firefox_name) {
          self::$fiDelIDS[] = $queueone->id;
          self::$fiDevices['token'][self::$fiCounter] = $queueone->token;
          self::$fiDevices['id'][self::$fiCounter] = $queueone->token_id;
          self::$fiDevices['firebase'][self::$fiCounter] = $queueone->firebase;
          self::$fiCounter++;
        }
        elseif($queueone->device_type == $types_name->opera_name) {
          self::$DelIDS9[] = $queueone->id;
          self::$Devices9['token'][self::$Counter9] = $queueone->token;
          self::$Devices9['id'][self::$Counter9] = $queueone->token_id;
          self::$Devices9['firebase'][self::$Counter9] = $queueone->firebase;
          self::$Counter9++;
        }
        elseif($queueone->device_type == $types_name->samsung_name) {
          self::$DelIDS10[] = $queueone->id;
          self::$Devices10['token'][self::$Counter10] = $queueone->token;
          self::$Devices10['id'][self::$Counter10] = $queueone->token_id;
          self::$Devices10['firebase'][self::$Counter10] = $queueone->firebase;
          self::$Counter10++;
        }
        elseif($queueone->device_type == $types_name->fbmsn_name) {
          self::$DelIDS11[] = $queueone->id;
          self::$Devices11[self::$Counter11]['token'] = $queueone->token;
          self::$Devices11[self::$Counter11]['id'] = $queueone->token_id;
          self::$Counter11++;
        }
        elseif($queueone->device_type == $types_name->fbnotify_name) {
          self::$DelIDS12[] = $queueone->id;
          self::$Devices12[self::$Counter12]['token'] = $queueone->token;
          self::$Devices12[self::$Counter12]['id'] = $queueone->token_id;
          self::$Counter12++;
        }
        elseif($queueone->device_type == $types_name->email_name) {
          self::$DelIDS13[] = $queueone->id;
          self::$Devices13[self::$Counter13]['token'] = $queueone->token;
          self::$Devices13[self::$Counter13]['id'] = $queueone->token_id;
          self::$Counter13++;
        }
        elseif($queueone->device_type == $types_name->edge_name) {
          self::$DelIDS14[] = $queueone->id;
          self::$Devices14['token'][self::$Counter14] = $queueone->token;
          self::$Devices14['id'][self::$Counter14] = $queueone->token_id;
          self::$Devices14['firebase'][self::$Counter14] = $queueone->firebase;
          self::$Counter14++;
        }
        elseif($queueone->device_type == $types_name->iosfcm_name) {
          self::$DelIDS15[] = $queueone->id;
          self::$Devices15['token'][self::$Counter15] = $queueone->token;
          self::$Devices15['id'][self::$Counter15] = $queueone->token_id;
          self::$Devices15['badge'][self::$Counter15] = $queueone->counter;
          self::$Counter15++;
        }
        else{
          continue;
        }
        self::$totalSent++;
      }
      if(self::$iosCounter > 0){
        self::sendPushCron('ios');
      }
      if(self::$andCounter > 0){
        self::sendPushCron('android');
      }
      if(self::$wpCounter > 0){
        self::sendPushCron('wp');
      }
      if(self::$wp10Counter > 0){
        self::sendPushCron('wp10');
      }
      if(self::$bbCounter > 0){
        self::sendPushCron('bb');
      }
      if(self::$chCounter > 0){
        self::sendPushCron('chrome');
      }
      if(self::$saCounter > 0){
        self::sendPushCron('safari');
      }
      if(self::$fiCounter > 0){
        self::sendPushCron('firefox');
      }
      if(self::$Counter9 > 0){
        self::sendPushCron('opera');
      }
      if(self::$Counter10 > 0){
        self::sendPushCron('samsung');
      }
      if(self::$Counter11 > 0){
        self::sendPushCron('fbmsn');
      }
      if(self::$Counter12 > 0){
        self::sendPushCron('fbnotify');
      }
      if(self::$Counter13 > 0){
        self::sendPushCron('email');
      }
      if(self::$Counter14 > 0){
        self::sendPushCron('edge');
      }
      if(self::$Counter15 > 0){
        self::sendPushCron('iosfcm');
      }
    }
    self::finishQueue();
    die();
  }

  public static function sendPushCron($type) {
    global $wpdb;
    self::$sendoptions = unserialize($wpdb->get_var("SELECT options FROM ".$wpdb->prefix."push_archive WHERE id='".self::$tempunique."'"));
    if(empty(self::$sendoptions)){
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_cron_queue WHERE sendoptions='".self::$tempunique."'");
      self::writeLog(__('System did not find the related data for message', 'smpush-plugin-lang').' #'.self::$tempunique.' : '.__('operation cancelled', 'smpush-plugin-lang'));
      die();
    }
    self::$sendoptions['msgid'] = self::$tempunique;
    if($type == 'ios'){
      $DelIDS = implode(',', self::$iosDelIDS);
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_cron_queue WHERE id IN($DelIDS)");
      smpush_sendpush::connectPush(self::$sendoptions['message'], self::$iosDevices, 'ios', self::$sendoptions, true, 0, true, self::$tempunique);
      self::$iosFeedback = true;
      self::resetIOS();
    }
    elseif($type == 'android'){
      $DelIDS = implode(',', self::$andDelIDS);
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_cron_queue WHERE id IN($DelIDS)");
      smpush_sendpush::connectPush(self::$sendoptions['message'], self::$andDevices, 'android', self::$sendoptions, true, 0, true, self::$tempunique);
      self::resetAND();
    }
    elseif($type == 'wp'){
      $DelIDS = implode(',', self::$wpDelIDS);
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_cron_queue WHERE id IN($DelIDS)");
      smpush_sendpush::connectPush(self::$sendoptions['message'], self::$wpDevices, 'wp', self::$sendoptions, true, 0, true, self::$tempunique);
      self::resetWP();
    }
    elseif($type == 'wp10'){
      $DelIDS = implode(',', self::$wp10DelIDS);
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_cron_queue WHERE id IN($DelIDS)");
      smpush_sendpush::connectPush(self::$sendoptions['message'], self::$wp10Devices, 'wp10', self::$sendoptions, true, 0, true, self::$tempunique);
      self::resetWP10();
    }
    elseif($type == 'bb'){
      $DelIDS = implode(',', self::$bbDelIDS);
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_cron_queue WHERE id IN($DelIDS)");
      smpush_sendpush::connectPush(self::$sendoptions['message'], self::$bbDevices, 'bb', self::$sendoptions, true, 0, true, self::$tempunique);
      self::resetBB();
    }
    elseif($type == 'chrome'){
      $DelIDS = implode(',', self::$chDelIDS);
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_cron_queue WHERE id IN($DelIDS)");
      smpush_sendpush::connectPush(self::$sendoptions['message'], self::$chDevices, 'chrome', self::$sendoptions, true, 0, true, self::$tempunique);
      self::resetCH();
    }
    elseif($type == 'safari'){
      $DelIDS = implode(',', self::$saDelIDS);
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_cron_queue WHERE id IN($DelIDS)");
      smpush_sendpush::connectPush(self::$sendoptions['message'], self::$saDevices, 'safari', self::$sendoptions, true, 0, true, self::$tempunique);
      self::resetSA();
    }
    elseif($type == 'firefox'){
      $DelIDS = implode(',', self::$fiDelIDS);
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_cron_queue WHERE id IN($DelIDS)");
      smpush_sendpush::connectPush(self::$sendoptions['message'], self::$fiDevices, 'firefox', self::$sendoptions, true, 0, true, self::$tempunique);
      self::resetFI();
    }
    elseif($type == 'opera'){
      $DelIDS = implode(',', self::$DelIDS9);
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_cron_queue WHERE id IN($DelIDS)");
      smpush_sendpush::connectPush(self::$sendoptions['message'], self::$Devices9, 'opera', self::$sendoptions, true, 0, true, self::$tempunique);
      self::reset9();
    }
    elseif($type == 'samsung'){
      $DelIDS = implode(',', self::$DelIDS10);
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_cron_queue WHERE id IN($DelIDS)");
      smpush_sendpush::connectPush(self::$sendoptions['message'], self::$Devices10, 'samsung', self::$sendoptions, true, 0, true, self::$tempunique);
      self::reset10();
    }
    elseif($type == 'fbmsn'){
      $DelIDS = implode(',', self::$DelIDS11);
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_cron_queue WHERE id IN($DelIDS)");
      smpush_sendpush::connectPush(self::$sendoptions['message'], self::$Devices11, 'fbmsn', self::$sendoptions, true, 0, true, self::$tempunique);
      self::reset11();
    }
    elseif($type == 'fbnotify'){
      $DelIDS = implode(',', self::$DelIDS12);
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_cron_queue WHERE id IN($DelIDS)");
      smpush_sendpush::connectPush(self::$sendoptions['message'], self::$Devices12, 'fbnotify', self::$sendoptions, true, 0, true, self::$tempunique);
      self::reset12();
    }
    elseif($type == 'email'){
      $DelIDS = implode(',', self::$DelIDS13);
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_cron_queue WHERE id IN($DelIDS)");
      smpush_sendpush::connectPush(self::$sendoptions['message'], self::$Devices13, 'email', self::$sendoptions, true, 0, true, self::$tempunique);
      self::reset13();
    }
    elseif($type == 'edge'){
      $DelIDS = implode(',', self::$DelIDS14);
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_cron_queue WHERE id IN($DelIDS)");
      smpush_sendpush::connectPush(self::$sendoptions['message'], self::$Devices14, 'edge', self::$sendoptions, true, 0, true, self::$tempunique);
      self::reset14();
    }
    elseif($type == 'iosfcm'){
      $DelIDS = implode(',', self::$DelIDS15);
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_cron_queue WHERE id IN($DelIDS)");
      smpush_sendpush::connectPush(self::$sendoptions['message'], self::$Devices15, 'iosfcm', self::$sendoptions, true, 0, true, self::$tempunique);
      self::reset15();
    }
  }

  public static function destruct() {
    global $wpdb;
    $wpdb->query("DELETE FROM ".$wpdb->prefix."push_cron_queue WHERE sendoptions='".self::$tempunique."'");
    $wpdb->update($wpdb->prefix.'push_archive', array('endtime' => gmdate('Y-m-d H:i:s', current_time('timestamp'))), array('id' => self::$tempunique));
    if(self::$iosFeedback){
      $wpdb->insert($wpdb->prefix.'push_feedback', array('device_type' => 'ios', 'msgid' => self::$tempunique));
    }
    smpush_sendpush::connectFeedback(0, true, self::$tempunique);
    self::$iosFeedback = false;
  }
  
  public static function loadedtime(){
    echo 'total execution time '.(microtime(true)-processTime).' seconds';
  }
  
  public static function finishQueue() {
    if(self::$totalSent > 0){
      self::destruct();
      smpush_sendpush::updateStats('totalsend', self::$totalSent, true, self::$tempunique);
      smpush_sendpush::updateStats('all', 0, true, self::$tempunique);
      smpush_sendpush::updateStats('reset', 0, true, self::$tempunique);
      self::$totalSent = 0;
    }
  }

  public static function writeLog($log) {
    global $wpdb;
    $wpdb->insert($wpdb->prefix.'push_archive', array('send_type' => 'feedback', 'message' => $log, 'starttime' => self::$startTime, 'endtime' => gmdate('Y-m-d H:i:s', current_time('timestamp'))));
  }

  public static function resetIOS() {
    self::$iosDevices = array();
    self::$iosDelIDS = array();
    self::$iosCounter = 0;
  }

  public static function resetAND() {
    self::$andDevices = array();
    self::$andDelIDS = array();
    self::$andCounter = 0;
  }
  
  public static function resetWP() {
    self::$wpDevices = array();
    self::$wpDelIDS = array();
    self::$wpCounter = 0;
  }
  
  public static function resetWP10() {
    self::$wp10Devices = array();
    self::$wp10DelIDS = array();
    self::$wp10Counter = 0;
  }
  
  public static function resetBB() {
    self::$bbDevices = array();
    self::$bbDelIDS = array();
    self::$bbCounter = 0;
  }
  
  public static function resetCH() {
    self::$chDevices = array();
    self::$chDelIDS = array();
    self::$chCounter = 0;
  }
  
  public static function resetSA() {
    self::$saDevices = array();
    self::$saDelIDS = array();
    self::$saCounter = 0;
  }
  
  public static function resetFI() {
    self::$fiDevices = array();
    self::$fiDelIDS = array();
    self::$fiCounter = 0;
  }
  
  public static function reset9() {
    self::$Devices9 = array();
    self::$DelIDS9 = array();
    self::$Counter9 = 0;
  }
  
  public static function reset10() {
    self::$Devices10 = array();
    self::$DelIDS10 = array();
    self::$Counter10 = 0;
  }
  
  public static function reset11() {
    self::$Devices11 = array();
    self::$DelIDS11 = array();
    self::$Counter11 = 0;
  }
  
  public static function reset12() {
    self::$Devices12 = array();
    self::$DelIDS12 = array();
    self::$Counter12 = 0;
  }
  
  public static function reset13() {
    self::$Devices13 = array();
    self::$DelIDS13 = array();
    self::$Counter13 = 0;
  }
  
  public static function reset14() {
    self::$Devices14 = array();
    self::$DelIDS14 = array();
    self::$Counter14 = 0;
  }
  
  public static function reset15() {
    self::$Devices15 = array();
    self::$DelIDS15 = array();
    self::$Counter15 = 0;
  }
  
}