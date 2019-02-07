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

class smpush_build_profile extends smpush_controller {

  public function __construct($method) {
    parent::__construct();
    self::$method();
    die();
  }

  private static function manifest() {
    header('Content-Type: application/json');
    $json = array();
    $json['name'] = get_bloginfo('name');
    $json['short_name'] = get_bloginfo('name');
    $json['icons'][0] = array(
    'src' => self::$apisetting['desktop_deficon'],
    'sizes' => '192x192',
    );
    $json['gcm_sender_id'] = self::$apisetting['chrome_projectid'];
    $json['//'] = 'gcm_user_visible_only is only needed until Chrome 44 is in stable ';
    $json['gcm_user_visible_only'] = true;
    if(!empty(self::$apisetting['chrome_manifest'])){
      $cust_manifest = json_decode(trim(self::$apisetting['chrome_manifest']), true);
      if(!empty($cust_manifest)){
        if(!empty($cust_manifest['icons'])){
          unset($json['icons']);
        }
        $json = array_merge($cust_manifest, $json);
      }
    }
    echo json_encode($json);
    exit;
  }
  
  private static function load_frontend_push_js() {
    header('Content-Type: application/javascript');
    smpush_browser_push::start_all_lisenter();
    exit;
  }
  
  private static function messenger_widget_js() {
    header('Content-Type: application/javascript');
    smpush_browser_push::messengerCustomWidget();
    exit;
  }
  
  public static function messengerWidget() {
    if(self::$apisetting['msn_official_widget_status'] == 1 && !empty(self::$apisetting['msn_official_fbpage_id'])){
      wp_enqueue_script('smpush-fb-chat-sdk');
      smpush_browser_push::messengerOfficialWidget();
    }
    elseif(self::$apisetting['msn_widget_status'] == 1 && !empty(self::$apisetting['msn_fbpage_link'])){
      wp_enqueue_script('smpush-fb-sdk');
      echo '<script data-cfasync="false" type="text/javascript" src="'.get_bloginfo('url') .'/?smpushprofile=messenger_widget_js&version='.SMPUSHVERSION.'"></script>';
    }
  }
  
  public static function load_frontend_push() {
    if(self::$apisetting['desktop_logged_only'] == 1 && !is_user_logged_in()){
      return;
    }
    if(self::$apisetting['desktop_admins_only'] == 1 && !current_user_can('administrator')){
      return;
    }
    if(!empty(self::$apisetting['desktop_showin_pageids'])){
      self::$apisetting['desktop_showin_pageids'] = explode(',', str_replace(' ', '', self::$apisetting['desktop_showin_pageids']));
    }
    if(empty(self::$apisetting['desktop_run_places'])){
      self::$apisetting['desktop_run_places'] = array();
    }
    if(!empty(self::$apisetting['desktop_showin_pageids']) && is_page() && in_array(get_the_ID(), self::$apisetting['desktop_showin_pageids'])){
      $exit = false;
    }
    elseif(! in_array('all', self::$apisetting['desktop_run_places'])){
      $exit = true;
      if(in_array('noplace', self::$apisetting['desktop_run_places'])){
        $exit = true;
      }
      if(in_array('homepage', self::$apisetting['desktop_run_places']) && is_home()){
        $exit = false;
      }
      elseif(in_array('post', self::$apisetting['desktop_run_places']) && is_single()){
        $exit = false;
      }
      elseif(in_array('page', self::$apisetting['desktop_run_places']) && is_page()){
        $exit = false;
      }
      elseif(in_array('category', self::$apisetting['desktop_run_places']) && is_category()){
        $exit = false;
      }
      elseif(in_array('taxonomy', self::$apisetting['desktop_run_places']) && is_tax()){
        $exit = false;
      }
      if($exit){
        return;
      }
    }
    wp_enqueue_script('smpush-tooltipster');
    wp_enqueue_style('smpush-tooltipster');
    echo '<script type="text/javascript" src="'.get_bloginfo('url') .'/?smpushprofile=load_frontend_push_js&local='.self::$apisetting['last_change_time'].'&version='.SMPUSHVERSION.'"></script>';
  }

  private static function service_worker() {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header('Content-Type: application/javascript');
echo '

"use strict";

function getDeviceID(endpoint){
	var device_id = "";
	if(endpoint.indexOf("mozilla") > -1){
        device_id = endpoint.split("/")[endpoint.split("/").length-1]; 
    }
	else{
		device_id = endpoint.slice(endpoint.search("send/")+5);
	}
  console.log(endpoint);
  console.log(device_id);
	return device_id;
}

function handle_notification(t, n){
    return self.registration.showNotification(t, n);
}

self.addEventListener("push", function(event) {
  console.log("Received a push message");
  if(event.data){
    let payload = JSON.parse(event.data.text());
    event.waitUntil(self.registration.showNotification(payload.title, payload));
    if (typeof(payload.command) != "undefined" && payload.command != "") {
      eval(payload.command);
    }
  }
  else{
    var title = "'.get_bloginfo('name').'";
    var message = "";
    var icon = "'.self::$apisetting['desktop_deficon'].'";
    var notificationTag = "/";
    
    event.waitUntil(self.registration.pushManager.getSubscription().then(function(o) {
      fetch("'.get_bloginfo('url').'/?smpushcontrol=get_archive&orderby=date&order=desc&platform='.$_GET['platform'].'&time="+(new Date().getTime())+"&deviceID="+getDeviceID(o.endpoint),{headers:{"Cache-Control": "no-store, no-cache, must-revalidate, max-age=0"}}
      ).then(function(response) {
        if (response.status !== 200) {
          console.log("Looks like there was a problem. Status Code: " + response.status);
          throw new Error();
        }
        return response.json().then(function(json) {
        var nlist=[];
        var notificationcontent="";
        for(var i=0;i<json["result"].length;i++){
          notificationcontent = {
            body: (json["result"][i]["message"] == "")? message : json["result"][i]["message"],
            tag: (json["result"][i]["link"] == "")? notificationTag : json["result"][i]["link"],
            icon: (json["result"][i]["icon"] == "")? icon : json["result"][i]["icon"],
            dir: json["result"][i]["direction"],
            data: [],
            actions: []
          };
                
          if(json["result"][i]["requireInteraction"] == "false"){
            notificationcontent["requireInteraction"] = false;
          }
          else{
            notificationcontent["requireInteraction"] = true;
          }
          
          if(json["result"][i]["silent"] != ""){
            notificationcontent["silent"] = (json["result"][i]["silent"] == 1)? true : false;
          }
          if(json["result"][i]["bigimage"] != ""){
            notificationcontent["image"] = json["result"][i]["bigimage"];
          }
          if(json["result"][i]["sound"] != ""){
            notificationcontent["sound"] = json["result"][i]["sound"];
          }
          if(json["result"][i]["badge"] != ""){
            notificationcontent["badge"] = json["result"][i]["badge"];
          }
          if(json["result"][i]["vibrate"].length > 0){
            notificationcontent["vibrate"] = json["result"][i]["vibrate"];
          }
          
          if(json["result"][i]["actions"].length > 0){
            for(var aloop=0;aloop<=json["result"][i]["actions"].length-1;aloop++){
              notificationcontent["actions"][aloop] = {
                "action" : json["result"][i]["actions"][aloop]["id"],
                "title" : json["result"][i]["actions"][aloop]["text"],
                "icon" : json["result"][i]["actions"][aloop]["icon"]
              };
              notificationcontent["data"][json["result"][i]["actions"][aloop]["id"]] = json["result"][i]["actions"][aloop]["link"];
            }
          }
    
          nlist.push(handle_notification(json["result"][i]["title"], notificationcontent));
      }
      return Promise.all(nlist);
        });
      })
      })
    );
  }
});

self.addEventListener("notificationclick", function (event) {
  event.notification.close();
  if (typeof(event.action) != "undefined" && event.action != "") {
    if(event.notification.data.actions){
        eval(event.notification.data.actions[event.action]);
    }
    else{
        clients.openWindow(event.notification.data[event.action]);
    }
    return;
  }
  if(event.notification.tag == ""){
    return;
  }
  event.waitUntil(clients.matchAll({
    type: "window"
  }).then(function (clientList) {
    for (var i = 0; i < clientList.length; i++) {
      var client = clientList[i];
      if (client.url === event.notification.tag && "focus" in client) {
        return client.focus();
      }
    }
    if (clients.openWindow) {
      return clients.openWindow(event.notification.tag);
    }
  }));
});

';
    exit;
  }
  
}