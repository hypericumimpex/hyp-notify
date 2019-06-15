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

class smpush_browser_push extends smpush_controller {

  public function __construct() {
    parent::__construct();
  }

  private static function safari() {
    $output = '

function smpushSafari(){
  var pushButton = jQuery(".smpush-push-permission-button");
    pushButton.removeAttr("disabled");
    
    if(smpush_getCookie("smpush_safari_device_token") != ""){
      pushButton.html("'.addslashes(self::$apisetting['desktop_btn_unsubs_text']).'");
      jQuery("#smpushIconRequest").tooltipster("content","'.addslashes(self::$apisetting['desktop_icon_unsubs_text']).'");
    }
    else{
      pushButton.html("'.addslashes(self::$apisetting['desktop_btn_subs_text']).'");
    }
    
    pushButton.click(function() {
      var permissionData = window.safari.pushNotification.permission("'.self::$apisetting['safari_web_id'].'");
      smpushCheckRemotePermission(permissionData);
    });
    jQuery(".smpush-push-subscriptions-button").click(function() {
      var permissionData = window.safari.pushNotification.permission("'.self::$apisetting['safari_web_id'].'");
      smpushCheckRemotePermission(permissionData);
    });
    
    if("'.self::$apisetting['desktop_request_type'].'" == "native"){
      document.getElementsByClassName("smpush-push-permission-button")[0].click();
    }
}

var smpushCheckRemotePermission = function (permissionData) {
  var pushButton = jQuery(".smpush-push-permission-button");
  if (permissionData.permission === "default") {
    window.safari.pushNotification.requestPermission(
        "'.rtrim(get_bloginfo('wpurl'), '/') .'/'.self::$apisetting['push_basename'].'/safari",
        "'.self::$apisetting['safari_web_id'].'",
        {},
        smpushCheckRemotePermission
    );
  }
  else if (permissionData.permission === "denied") {
    if(smpush_getCookie("smpush_safari_device_token") != ""){
      smpush_endpoint_unsubscribe(smpush_getCookie("smpush_safari_device_token"));
    }
    smpush_setCookie("smpush_desktop_request", "true", 10);
    smpush_setCookie("smpush_safari_device_token", "false", -1);
    smpush_setCookie("smpush_device_token", "false", -1);
    smpushDrawNotifyPopup();
  }
  else if (permissionData.permission === "granted") {
    smpushDestroyReqWindow(false);
    if(smpush_getCookie("smpush_safari_device_token") != ""){
      smpush_endpoint_unsubscribe(smpush_getCookie("smpush_safari_device_token"));
      smpush_setCookie("smpush_desktop_request", "true", 10);
      smpush_setCookie("smpush_safari_device_token", "false", -1);
      smpush_setCookie("smpush_device_token", "false", -1);
      smpush_setCookie("smpush_desktop_welcmsg_seen", "false", -1);
      pushButton.attr("disabled","disabled");
      jQuery(".smpush-push-subscriptions-button").attr("disabled","disabled");
      jQuery(".smpush-push-subscriptions-button").html("'.self::$apisetting['desktop_modal_saved_text'].'");
    }
    else{
      if(smpush_getCookie("smpush_safari_device_token") == ""){
        smpush_setCookie("smpush_safari_device_token", permissionData.deviceToken, 365);
        smpush_endpoint_subscribe(permissionData.deviceToken);
      }
      else{
        smpushDestroyReqWindow(false);
      }
      pushButton.attr("disabled","disabled");
      jQuery(".smpush-push-subscriptions-button").attr("disabled","disabled");
      jQuery(".smpush-push-subscriptions-button").html("'.self::$apisetting['desktop_modal_saved_text'].'");
    }
  }
};

';
    return $output;
  }
  
  private static function bootstrap($options) {
    
    switch(self::$apisetting['desktop_popup_position']):
      case 'center':
        $popup_pos = '["center", "middle"]';
        break;
      case 'topcenter':
        $popup_pos = '["center", "top"]';
        break;
      case 'topright':
        $popup_pos = '["right - 20", "top + 20"]';
        break;
      case 'topleft':
        $popup_pos = '["left + 20", "top + 20"]';
        break;
      case 'bottomright':
        $popup_pos = '["right - 20", "bottom - 20"]';
        break;
      case 'bottomleft':
        $popup_pos = '["left + 20", "bottom - 20"]';
        break;
    endswitch;
    
    switch(self::$apisetting['desktop_icon_position']):
      case 'topright':
        $icon_tooltip_pos = 'left';
        $icon_pos = 'top: 10px; right: 10px;';
        break;
      case 'topleft':
        $icon_tooltip_pos = 'right';
        $icon_pos = 'top: 10px; left: 10px;';
        break;
      case 'bottomright':
        $icon_tooltip_pos = 'left';
        $icon_pos = 'bottom: 10px; right: 10px;';
        break;
      case 'bottomleft':
        $icon_tooltip_pos = 'right';
        $icon_pos = 'bottom: 10px; left: 10px;';
        break;
    endswitch;
    
    return '

"use strict";

var smpush_isPushEnabled = false;
var devicetype = smpush_browser();
var settings = JSON.parse(\''.$options.'\');
smpush_debug(devicetype);

function smpush_debug(object) {
  if('.self::$apisetting['desktop_debug'].' == 1){
    console.log(object);
  }
}

function smpushUrlB64ToUint8Array(base64String) {
  const padding = "=".repeat((4 - base64String.length % 4) % 4);
  const base64 = (base64String + padding)
    .replace(/\-/g, "+")
    .replace(/_/g, "/");

  const rawData = window.atob(base64);
  const outputArray = new Uint8Array(rawData.length);

  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }
  return outputArray;
}

function smpush_endpoint_subscribe(subscriptionId) {
  if(subscriptionId == ""){
    return false;
  }
  smpush_setCookie("smpush_desktop_request", "true", 365);
  smpush_setCookie("smpush_device_token", subscriptionId, 365);
  
  var data = {};
  data["device_token"] = subscriptionId;
  data["device_type"] = devicetype;
  data["active"] = 1;
  data["latitude"] = (smpush_getCookie("smart_push_smio_coords_latitude") != "")? smpush_getCookie("smart_push_smio_coords_latitude") : "";
  data["longitude"] = (smpush_getCookie("smart_push_smio_coords_longitude") != "")? smpush_getCookie("smart_push_smio_coords_longitude") : "";
  
  var subsChannels = [];
  jQuery("input.smpush_desktop_channels_subs:checked").each(function(index) {
    subsChannels.push(jQuery(this).val());
  });
  subsChannels = subsChannels.join(",");
  
  if(jQuery(".smpush-push-subscriptions-button").length > 0 && jQuery("#smpush_subscription_form").length == 0){
    var apiService = "channels_subscribe";
    data["channels_id"] = subsChannels;
  }
  else{
    var apiService = "savetoken";
  }
  
  smpushDestroyReqWindow(false);
  
  jQuery.ajax({
    method: "POST",
    url: "'.rtrim(get_bloginfo('wpurl'), '/') .'/?smpushcontrol="+apiService,
    data: data
  })
  .done(function( msg ) {
    smpushWelcomeMSG();
    jQuery(".smpush-push-subscriptions-button").attr("disabled","disabled");
    jQuery(".smpush-push-subscriptions-button").html("'.self::$apisetting['desktop_modal_saved_text'].'");
    smpush_debug("Data Sent");
    if('.self::$apisetting['desktop_gps_status'].' == 1){
      smpushUpdateGPS();
    }
    smpush_link_user_cookies();
  });
}

function smpush_endpoint_unsubscribe(subscriptionId) {
  jQuery("#smpushIconRequest").tooltipster("content","'.addslashes(self::$apisetting['desktop_icon_message']).'");
  jQuery.ajax({
    method: "POST",
    url: "'.rtrim(get_bloginfo('wpurl'), '/') .'/?smpushcontrol=deletetoken",
    data: { device_token: subscriptionId, device_type: devicetype}
  })
  .done(function( msg ) {
    smpush_debug("Data Sent");
    smpush_setCookie("smpush_linked_user", "false", -1);
    smpush_setCookie("smpush_safari_device_token", "false", -1);
    smpush_setCookie("smpush_device_token", "false", -1);
    smpush_setCookie("smpush_desktop_request", "false", -1);
    smpush_setCookie("smpush_desktop_welcmsg_seen", "false", -1);
  });
}

function smpush_test_browser(){
  if("safari" in window && "pushNotification" in window.safari){
    return true;
  }
  if (typeof(ServiceWorkerRegistration) != "undefined" && ("showNotification" in ServiceWorkerRegistration.prototype)) {
    return true;
  }
  if(Notification.permission === "denied"){
    return false;
  }
  return false; 
}

function smpush_browser() {
  if("safari" in window){
    return "safari";
  }
  if (navigator.userAgent.indexOf(" OPR/") >= 0) {
    return "opera";
  }
  if (navigator.userAgent.indexOf("Edge") >= 0) {
    return "edge";
  }
  if (navigator.userAgent.match(/chrome/i)) {
    return "chrome";
  }
  if (navigator.userAgent.match(/SamsungBrowser/i)) {
    return "samsung";
  }
  if (navigator.userAgent.match(/firefox/i)) {
    return "firefox";
  }
}

function smpush_bootstrap_init(){
  if(settings[devicetype] == 0){
    smpush_debug("Browser support is closed by admin settings");
    return;
  }
  var pushSupported = smpush_test_browser();
  if(! pushSupported){
    smpushDrawUnSupportedPopup();
    smpush_debug("Browser not support push notification");
    return;
  }
  
  if(smpush_getCookie("smpush_desktop_request") != "true"){
    jQuery("body").append("<style>'.str_replace('"', '\'', self::$apisetting['desktop_popup_css']).'</style>");
    setTimeout(function(){ smpushDrawReqWindow() }, '.(self::$apisetting['desktop_delay']*1000).');
  }
  else{
    smpush_link_user_cookies();
    if('.self::$apisetting['desktop_gps_status'].' == 1){
      smpushUpdateGPS();
    }
    var pushButton = jQuery(".smpush-push-permission-button");
    smpush_isPushEnabled = true;
    pushButton.html("'.addslashes(self::$apisetting['desktop_btn_unsubs_text']).'");
    jQuery("#smpushIconRequest").tooltipster("content","'.addslashes(self::$apisetting['desktop_icon_unsubs_text']).'");
    pushButton.removeAttr("disabled");
    jQuery(".smpush-push-subscriptions-button").removeAttr("disabled");
    jQuery(".smpush-push-subscriptions-button").click(function() {
      smpush_endpoint_subscribe(smpush_getCookie("smpush_device_token"));
    });
    pushButton.click(function() {
      smpush_endpoint_unsubscribe(smpush_getCookie("smpush_device_token"));
      jQuery(".smpush-push-permission-button").remove();
      jQuery(".smpush-push-subscriptions-button").remove();
    });
  }
  smpushDrawReqIcon();
}

function smpushUpdateGPS(){
  if(smpush_getCookie("smpush_device_token") != "" && smpush_getCookie("smart_push_smio_coords_latitude") == ""){
    if (! navigator.geolocation) {
      smpush_debug("Geolocation is not supported for this Browser/OS.");
      return;
    }
    var geoSuccess = function(startPos) {
      smpush_debug(startPos.coords.latitude);
      smpush_debug(startPos.coords.longitude);
      smpush_setCookie("smart_push_smio_coords_latitude", startPos.coords.latitude, (1/24));
      smpush_setCookie("smart_push_smio_coords_longitude", startPos.coords.longitude, (1/24));
      
      smpush_endpoint_subscribe(smpush_getCookie("smpush_device_token"));
    };
    var geoError = function(error) {
      smpush_debug("Error occurred. Error code: " + error.code);
      /*0: unknown error, 1: permission denied, 2: position unavailable (error response from location provider), 3: timed out*/
    };
    navigator.geolocation.getCurrentPosition(geoSuccess);
  }
}

function smpushDestroyReqWindow(dismiss){
  jQuery("#smart_push_smio_window").remove();
  jQuery("#smart_push_smio_overlay").remove();
  
  if(dismiss){
    var requestAgainPeriod = '.((empty(self::$apisetting['desktop_reqagain']))? 1: self::$apisetting['desktop_reqagain']).';
  }
  else{
    var requestAgainPeriod = 365;
  }
  
  smpush_setCookie("smpush_desktop_request", "true", requestAgainPeriod);
  if("'.self::$apisetting['desktop_paytoread'].'" == "2" && jQuery("#SMIOPayToReadButton").length > 0 && smpush_getCookie("smpush_device_token") != ""){
    location.reload();
  }
}

function smpushDrawNotifyPopup(){
  if("'.self::$apisetting['desktop_paytoread'].'" != "1")return;
  jQuery("#smart_push_smio_window").remove();
  jQuery("#smart_push_smio_overlay").remove();
  
  jQuery("body").append(\''.self::buildPopupLayout(true).'\');
  document.getElementById("smart_push_smio_overlay").style.opacity = "'.((empty(self::$apisetting['desktop_paytoread_darkness']))? 0.8:(self::$apisetting['desktop_paytoread_darkness']/10) ).'";
  document.getElementById("smart_push_smio_window").style.position = "fixed";
  if("'.self::$apisetting['black_overlay'].'" == "1"){
    document.getElementById("smart_push_smio_overlay").style.display = "block";
  }
  document.getElementById("smart_push_smio_window").style.display = "block";

  document.getElementById("smart_push_smio_window").style.left = ((window.innerWidth/2) - (document.getElementById("smart_push_smio_window").offsetWidth/2)) + "px";
  document.getElementById("smart_push_smio_window").style.top = ((window.innerHeight/2) - (document.getElementById("smart_push_smio_window").offsetHeight/2)) + "px";
}

function smpushDrawUnSupportedPopup(){
  if("'.self::$apisetting['desktop_paytoread'].'" != "1")return;
  jQuery("#smart_push_smio_window").remove();
  jQuery("#smart_push_smio_overlay").remove();
  
  jQuery("body").append(\''.self::buildPopupLayout(self::$apisetting['desktop_notsupport_msg']).'\');
  document.getElementById("smart_push_smio_overlay").style.opacity = "'.((empty(self::$apisetting['desktop_paytoread_darkness']))? 0.8:(self::$apisetting['desktop_paytoread_darkness']/10) ).'";
  document.getElementById("smart_push_smio_window").style.position = "fixed";
  if("'.self::$apisetting['black_overlay'].'" == "1"){
    document.getElementById("smart_push_smio_overlay").style.display = "block";
  }
  document.getElementById("smart_push_smio_window").style.display = "block";

  document.getElementById("smart_push_smio_window").style.left = ((window.innerWidth/2) - (document.getElementById("smart_push_smio_window").offsetWidth/2)) + "px";
  document.getElementById("smart_push_smio_window").style.top = ((window.innerHeight/2) - (document.getElementById("smart_push_smio_window").offsetHeight/2)) + "px";
  
  document.getElementById("smart_push_smio_allow").style.display = "none";
}

function smpushIntializePopupBox(){
  jQuery("#smart_push_smio_window").remove();
  jQuery("#smart_push_smio_overlay").remove();
  jQuery("body").append(\''.self::buildPopupLayout().'\');
  document.getElementById("smart_push_smio_overlay").style.opacity = "'.((empty(self::$apisetting['desktop_paytoread_darkness']))? 0.8:(self::$apisetting['desktop_paytoread_darkness']/10) ).'";
  document.getElementById("smart_push_smio_window").style.position = "fixed";
  if("'.self::$apisetting['black_overlay'].'" == "1"){
    document.getElementById("smart_push_smio_overlay").style.display = "block";
  }
  document.getElementById("smart_push_smio_window").style.display = "block";
  
  var position = "'.self::$apisetting['desktop_popup_position'].'";

  if(position == "topright"){
    document.getElementById("smart_push_smio_window").style.right = "10px";
    document.getElementById("smart_push_smio_window").style.top = "10px";
  }
  else if(position == "topleft"){
    document.getElementById("smart_push_smio_window").style.left = "10px";
    document.getElementById("smart_push_smio_window").style.top = "10px";
  }
  else if(position == "bottomright"){
    document.getElementById("smart_push_smio_window").style.bottom = "10px";
    document.getElementById("smart_push_smio_window").style.right = "10px";
  }
  else if(position == "bottomleft"){
    document.getElementById("smart_push_smio_window").style.left = "10px";
    document.getElementById("smart_push_smio_window").style.bottom = "10px";
  }
  else if(position == "topcenter"){
    document.getElementById("smart_push_smio_window").style.left = ((window.innerWidth/2) - (document.getElementById("smart_push_smio_window").offsetWidth/2)) + "px";
    document.getElementById("smart_push_smio_window").style.top = "0";
  }
  else{
    document.getElementById("smart_push_smio_window").style.left = ((window.innerWidth/2) - (document.getElementById("smart_push_smio_window").offsetWidth/2)) + "px";
    document.getElementById("smart_push_smio_window").style.top = ((window.innerHeight/2) - (document.getElementById("smart_push_smio_window").offsetHeight/2)) + "px";
  }
}

function smpushDrawReqWindow(){
  if("'.self::$apisetting['desktop_request_type'].'" == "popup"){
    smpushIntializePopupBox();
  }
  else if("'.self::$apisetting['desktop_request_type'].'" == "subs_page"){
  }
  else{
    if("'.self::$apisetting['desktop_paytoread'].'" == "1"){
      jQuery("body").append(\'<div id="smart_push_smio_overlay" tabindex="-1" style="opacity:'.((empty(self::$apisetting['desktop_paytoread_darkness']))? 0.8:(self::$apisetting['desktop_paytoread_darkness']/10) ).'; display: block;ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=40); background-color:#000; position: fixed; left: 0; right: 0; top: 0; bottom: 0; z-index: 10000;"></div>\');
    }
    jQuery("body").append("<button class=\"smpush-push-permission-button\" style=\"display:none\" disabled>'.addslashes(self::$apisetting['desktop_btn_subs_text']).'</button>");
  }
  
  if ("safari" in window) {
      smpushSafari();
  } else {
      smpushGeko();
  }
}

function smpushDrawReqIcon(){
  if("'.self::$apisetting['gdpr_icon'].'" == "1"){
    jQuery("body").append("<div class=\"tooltip_templates\"><div id=\"smpush_tooltip_gdpr_ver_text\"><img src=\"'.((empty(self::$apisetting['desktop_popupicon']))? smpush_imgpath.'/alert.png' : self::$apisetting['desktop_popupicon']).'\" /><p>'.addslashes(self::$apisetting['desktop_icon_message']).'</p><p id=\"smpush_gdpr_hint\">'.addslashes(self::$apisetting['gdpr_ver_text']).'</p></div></div>");
    jQuery("body").append("<button class=\"smpush-push-permission-button smpushTooltip\" id=\"smpushIconRequest\" style=\"'.$icon_pos.'\" data-tooltip-content=\"#smpush_tooltip_gdpr_ver_text\" disabled></button>");
  }
  else if("'.self::$apisetting['desktop_request_type'].'" == "icon"){
    jQuery("body").append("<button class=\"smpush-push-permission-button smpushTooltip\" id=\"smpushIconRequest\" style=\"'.$icon_pos.'\" title=\"'.addslashes(self::$apisetting['desktop_icon_message']).'\" disabled></button>");
  }
  else{
    return;
  }
  if("'.self::$apisetting['desktop_paytoread'].'" == "1"){
    jQuery("body").append(\'<div id="smart_push_smio_overlay" tabindex="-1" style="opacity:'.((empty(self::$apisetting['desktop_paytoread_darkness']))? 0.8:(self::$apisetting['desktop_paytoread_darkness']/10) ).'; display: block;ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=40); background-color:#000; position: fixed; left: 0; right: 0; top: 0; bottom: 0; z-index: 10000;"></div>\');
  }
  jQuery("body").append("<style>#smpushIconRequest{display: block;position: fixed;width: 50px;height: 50px;background-image: url('.((!empty(self::$apisetting['desktop_iconimage']))? self::$apisetting['desktop_iconimage'] : smpush_imgpath.'/alert.png').');background-repeat: no-repeat;background-position: center;background-size: 40px 40px;text-indent: -9999px;padding: 0;margin: 0;border: 0;z-index: 999999999;border-radius: 50px;-webkit-border-radius: 50px;-moz-border-radius: 50px;-webkit-box-shadow: 7px 3px 16px 0px rgba(50, 50, 50, 0.2);-moz-box-shadow:    7px 3px 16px 0px rgba(50, 50, 50, 0.2);box-shadow:7px 3px 16px 0px rgba(50, 50, 50, 0.2);}</style>");
  smpushTooltip();
  if(smpush_getCookie("smpush_desktop_request") == "true"){
    if ("safari" in window) {
      smpushSafari();
    } else {
        smpushGeko();
    }
  }
}

function smpush_link_user_cookies() {
  if(smpush_getCookie("smpush_fresh_linked_user") != "" && smpush_getCookie("smpush_linked_user") == "" && smpush_getCookie("smpush_device_token") != ""){
    smpush_endpoint_subscribe(smpush_getCookie("smpush_device_token"));
    smpush_setCookie("smpush_linked_user", "true", 15);
    smpush_setCookie("smpush_fresh_linked_user", "", -1);
  }
}

function smpushWelcomeMSG(){
  if(smpush_getCookie("smpush_desktop_welcmsg_seen") == "true"){
    return;
  }
  if("'.self::$apisetting['desktop_welc_redir'].'" == "1"){
    setTimeout(function(){ window.location="'.self::$apisetting['desktop_welc_redir_link'].'"; }, 4000);
  }
  if("'.self::$apisetting['desktop_welc_status'].'" == "0"){return;}
  smpush_setCookie("smpush_desktop_welcmsg_seen", "true", 365);
  if("safari" in window){
    var n = new Notification(
      "'.addslashes(self::$apisetting['desktop_welc_title']).'",
      {
        "body": "'.addslashes(self::$apisetting['desktop_welc_message']).'",
        "tag" : "'.self::$apisetting['desktop_welc_link'].'"
      }
    );
    n.onclick = function () {
      this.close();
      window.open("'.self::$apisetting['desktop_welc_link'].'", "_blank");
    };
  }
  else{
    navigator.serviceWorker.ready.then(function(registration) {
      registration.showNotification("'.addslashes(self::$apisetting['desktop_welc_title']).'", {
        icon: "'.self::$apisetting['desktop_welc_icon'].'",
        body: "'.addslashes(self::$apisetting['desktop_welc_message']).'",
        tag: "'.addslashes(self::$apisetting['desktop_welc_link']).'",
        requireInteraction: true
      });
    });
    self.addEventListener("notificationclick", function (event) {
      event.notification.close();
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
  }
}

function smpush_setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires + ";path='.COOKIEPATH.'";
}

function smpush_getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(";");
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==" "){
          c = c.substring(1);
        }
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}

function smpushTooltip() {
  jQuery(".smpushTooltip").tooltipster({side: "left", contentCloning: true, interactive: true});
}

if ("safari" in window && "pushNotification" in window.safari) {
  document.addEventListener("DOMContentLoaded", function(event) {
    smpush_bootstrap_init();
  });
}
else{
  window.addEventListener("load", function() {
    smpush_bootstrap_init();
  });
}

function openFBpopup(url, elm){
  var new_fbwindow = window.open(url, "", "width=800,height=600");
  new_fbwindow.onbeforeunload = function(){ $(elm).hide(); }
}

';
  }
  
  private static function chrome() {
    if(file_exists(ABSPATH.'/smart_manifest.js')){
      $manifest_link = rtrim(get_bloginfo('wpurl'), '/').'/smart_manifest.js?version='.self::$apisetting['settings_version'];
    }
    else{
      $manifest_link = rtrim(get_bloginfo('wpurl'), '/').'/?smpushprofile=manifest&version='.self::$apisetting['settings_version'];
    }
    if(file_exists(ABSPATH.'/smart_service_worker.js')){
      $sw_link = rtrim(get_bloginfo('wpurl'), '/').'/smart_service_worker.js?version='.self::$apisetting['settings_version'];
    }
    else{
      $sw_link = rtrim(get_bloginfo('wpurl'), '/').'/?smpushprofile=service_worker&version='.self::$apisetting['settings_version'];
    }
    $output = '
if("'.self::$apisetting['pwa_support'].'" == "0" && ("'.self::$apisetting['desktop_webpush'].'" == "0" || "'.self::$apisetting['desktop_webpush_old'].'" == "1")){
  document.getElementsByTagName("HEAD")[0].insertAdjacentHTML("afterbegin", "<link rel=\"manifest\" href=\"'. $manifest_link .'\">");
}

function smpush_endpointWorkaround(endpoint){
	var device_id = "";
	if(endpoint.indexOf("mozilla") > -1){
        device_id = endpoint.split("/")[endpoint.split("/").length-1]; 
    }
	else if(endpoint.indexOf("send/") > -1){
		device_id = endpoint.slice(endpoint.search("send/")+5);
	}
  else{
    smpush_debug(endpoint);
    smpush_debug("error while getting device_id from endpoint");
    alert("error while getting device_id from endpoint");
    window.close();
  }
  smpush_debug(device_id);
	return device_id;
}

function smpush_sendSubscriptionToServer(subscription) {
  if("'.addslashes(self::$apisetting['desktop_webpush']).'" == "1"){
    var subscriptionId = subscription;
  }
  else{
    var subscriptionId = smpush_endpointWorkaround(subscription.endpoint);
  }
  smpush_debug(subscriptionId);
  if(smpush_getCookie("smpush_device_token") == ""){
    smpush_endpoint_subscribe(subscriptionId);
  }
  else{
    smpushDestroyReqWindow(false);
  }
}

function smpush_unsubscribe() {
  smpush_setCookie("smpush_desktop_request", "true", 10);
  var pushButton = jQuery(".smpush-push-permission-button");
  pushButton.attr("disabled","disabled");

  navigator.serviceWorker.ready.then(function(serviceWorkerRegistration) {
    serviceWorkerRegistration.pushManager.getSubscription().then(
      function(pushSubscription) {
        if (!pushSubscription) {
          smpush_isPushEnabled = false;
          pushButton.removeAttr("disabled");
          pushButton.html("'.addslashes(self::$apisetting['desktop_btn_subs_text']).'");
          return;
        }
        
        var subscriptionId = smpush_endpointWorkaround(pushSubscription.endpoint);
        smpush_debug(subscriptionId);
        smpush_endpoint_unsubscribe(subscriptionId);

        pushSubscription.unsubscribe().then(function() {
          pushButton.removeAttr("disabled");
          pushButton.html("'.addslashes(self::$apisetting['desktop_btn_subs_text']).'");
          smpush_isPushEnabled = false;
        }).catch(function(e) {
          smpush_debug("Unsubscription error: ", e);
          pushButton.removeAttr("disabled");
        });
      }).catch(function(e) {
        smpush_debug("Error thrown while unsubscribing from push messaging.", e);
      });
  });
}

function smpush_subscribe() {
  var pushButton = jQuery(".smpush-push-permission-button");
  pushButton.attr("disabled","disabled");
  
  if("'.addslashes(self::$apisetting['desktop_webpush']).'" == "1"){
    var applicationServerKey = smpushUrlB64ToUint8Array("'.addslashes(self::$apisetting['chrome_vapid_public']).'");
    var subsConfig = { userVisibleOnly: true, applicationServerKey: applicationServerKey };
  }
  else{
    var subsConfig = { userVisibleOnly: true };
  }

  navigator.serviceWorker.ready.then(function(serviceWorkerRegistration) {
    serviceWorkerRegistration.pushManager.subscribe(subsConfig)
      .then(function(subscription) {
        smpush_isPushEnabled = true;
        pushButton.html("'.addslashes(self::$apisetting['desktop_btn_unsubs_text']).'");
        jQuery("#smpushIconRequest").tooltipster("content","'.addslashes(self::$apisetting['desktop_icon_unsubs_text']).'");
        pushButton.removeAttr("disabled");
        if("'.addslashes(self::$apisetting['desktop_webpush']).'" == "1"){
          let subscriptionData = JSON.parse(JSON.stringify(subscription));
          let subscriptionServer = {"endpoint": subscriptionData.endpoint, "auth": subscriptionData.keys.auth, "p256dh": subscriptionData.keys.p256dh};
          smpush_debug(subscriptionServer);
          return smpush_sendSubscriptionToServer(btoa(JSON.stringify(subscriptionServer)));
        }
        else{
          return smpush_sendSubscriptionToServer(subscription);
        }
      })
      .catch(function(e) {
        if (Notification.permission === "denied") {
          smpushDrawNotifyPopup();
          smpush_debug("Permission for Notifications was denied");
          pushButton.attr("disabled","disabled");
          smpush_endpoint_unsubscribe(smpush_getCookie("smpush_device_token"));
        } else {
          smpush_debug(e);
        }
      });
  });
}

function smpush_initialiseState() {
  if (!("showNotification" in ServiceWorkerRegistration.prototype)) {
    smpush_debug("Notifications aren\'t supported.");
    return;
  }

  if (Notification.permission === "denied") {
    smpushDrawNotifyPopup();
    smpush_debug("The user has blocked notifications.");
    smpush_endpoint_unsubscribe(smpush_getCookie("smpush_device_token"));
    return;
  }

  if (!("PushManager" in window)) {
    smpush_debug("Push messaging isn\'t supported.");
    return;
  }

  navigator.serviceWorker.ready.then(function(serviceWorkerRegistration) {
    serviceWorkerRegistration.pushManager.getSubscription()
      .then(function(subscription) {
        var pushButton = jQuery(".smpush-push-permission-button");
        pushButton.removeAttr("disabled");

        if (!subscription) {
          if("'.self::$apisetting['desktop_request_type'].'" == "native"){
            document.getElementsByClassName("smpush-push-permission-button")[0].click();
          }
          return;
        }

        pushButton.html("'.addslashes(self::$apisetting['desktop_btn_unsubs_text']).'");
        jQuery("#smpushIconRequest").tooltipster("content","'.addslashes(self::$apisetting['desktop_icon_unsubs_text']).'");
        smpush_isPushEnabled = true;
        smpush_sendSubscriptionToServer(subscription);
      })
      .catch(function(err) {
        smpush_debug("Error during getSubscription()", err);
      });
  });
}

function smpushGeko(){
  if ("serviceWorker" in navigator) {
    if("'.addslashes(self::$apisetting['desktop_webpush']).'" == "1" && "'.addslashes(self::$apisetting['desktop_webpush_old']).'" == "0"){
      navigator.serviceWorker.register("'.rtrim(get_bloginfo('wpurl'), '/') .'/smart_push_sw.js").then(smpush_initialiseState).catch(function(error){ smpush_debug(error); });
    }
    else{
      navigator.serviceWorker.register("'.$sw_link.'").then(smpush_initialiseState).catch(function(error){ smpush_debug(error); });
    }
  } else {
    smpush_debug("Service workers aren\'t supported in this browser.");
  }
  
  if(jQuery(".smpush-push-permission-button").length < 1){
    return false;
  }
  
  var pushButton = jQuery(".smpush-push-permission-button");

  pushButton.click(function() {
    if (smpush_isPushEnabled) {
      smpush_unsubscribe();
    } else {
      smpush_subscribe();
    }
  });
  
  jQuery(".smpush-push-subscriptions-button").click(function() {
    smpush_subscribe();
  });
}

';
    return $output;
  }
  
  public static function start_all_lisenter() {
    $options = array('chrome' => 0, 'firefox' => 0, 'opera' => 0, 'edge' => 0, 'samsung' => 0, 'safari' => 0);
    if(self::$apisetting['desktop_status'] == 1 && self::$apisetting['desktop_chrome_status'] == 1){
      $options['chrome'] = 1;
    }
    if(self::$apisetting['desktop_status'] == 1 && self::$apisetting['desktop_firefox_status'] == 1){
      $options['firefox'] = 1;
    }
    if(self::$apisetting['desktop_status'] == 1 && self::$apisetting['desktop_opera_status'] == 1){
      $options['opera'] = 1;
    }
    if(self::$apisetting['desktop_status'] == 1 && self::$apisetting['desktop_edge_status'] == 1){
      $options['edge'] = 1;
    }
    if(self::$apisetting['desktop_status'] == 1 && self::$apisetting['desktop_samsung_status'] == 1){
      $options['samsung'] = 1;
    }
    if(self::$apisetting['desktop_status'] == 1 && self::$apisetting['desktop_safari_status'] == 1){
      $options['safari'] = 1;
    }
    $options = json_encode($options);
    $output = self::bootstrap($options);
    $output .= self::chrome();
    $output .= self::safari();
    $output = preg_replace('/\s+/', ' ', $output);
    if(! file_exists(smpush_dir.'/js/frontend_webpush.js')){
      $helper = new smpush_helper();
      $helper->storelocalfile(smpush_dir.'/js/frontend_webpush.js', $output);
    }
    echo $output;
  }
  
  private static function buildPopupLayout($second_msg=false){
    if($second_msg === true){
      $second_msg = str_replace('\'', '`', htmlspecialchars(nl2br(self::$apisetting['desktop_paytoread_message'])));
    }
    elseif($second_msg === false){
      $second_msg = str_replace('\'', '`', htmlspecialchars(nl2br(self::$apisetting['desktop_modal_message'])));
    }
    else{
      $second_msg = str_replace('\'', '`', htmlspecialchars(nl2br($second_msg)));
    }
    $html = '<style>';
    if(empty(self::$apisetting['desktop_popup_layout']) || self::$apisetting['desktop_popup_layout'] == 'modern'){
      $html .= '
#smart_push_smio_window{
direction:ltr;display: none;width:600px;max-width: 87%;background-color: white; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; padding: 17px; border-radius: 5px; text-align: center; overflow: hidden; z-index: 99999999;
}
#smart_push_smio_logo{
border-radius:50%;max-width:150px;max-height:150px;width:50%;height:50%;
}
#smart_push_smio_msg{
margin-top: 23px;color: #797979; font-size: 18px; text-align: center; font-weight: 300;padding: 0;line-height: normal;
}
#smart_push_smio_note{
color: #797979; font-size: 15px; text-align: center; font-weight: 300; position: relative; float: none; margin: 16px 0; padding: 0; line-height: normal;
}
#smart_push_smio_agreement{
color: #9e9e9e; font-size: 13px; text-align: center; font-weight: 300; position: relative; float: none; margin: 16px 0; padding: 0; line-height: normal;
}
#smart_push_smio_footer{
text-align: center;
}
#smart_push_smio_not_allow{
background-color: #9E9E9E;text-transform: none; color: white; border: none; box-shadow: none; font-size: 17px; font-weight: 500; -webkit-border-radius: 4px; border-radius: 5px; padding: 10px 32px; margin: 5px; cursor: pointer;
}
#smart_push_smio_allow{
background-color: #8BC34A;text-transform: none; color: white; border: none; box-shadow: none; font-size: 17px; font-weight: 500; -webkit-border-radius: 4px; border-radius: 5px; padding: 10px 32px; margin: 5px ; cursor: pointer;
}
';
    }
    elseif(self::$apisetting['desktop_popup_layout'] == 'native'){
      $html .= '
#smart_push_smio_window{
direction:ltr;display:none;max-width: 87%;z-index:99999999;font-family: Helvetica Neue, Helvetica, Arial, sans-serif;text-align:left;margin-top: 5px;border: 1px solid rgb(170, 170, 170);background: rgb(251, 251, 251);width: 320px;font-size: 13px;padding: 12px 12px 12px 6px;border-radius: 2px;box-shadow: rgba(0, 0, 0, 0.298039) 0px 2px 1px 0px;
}
#smart_push_smio_window:after {
bottom: 100%;left: 20%;border: solid transparent;content: " ";height: 0;width: 0;position: absolute;pointer-events: none;border-color: rgba(255, 255, 255, 0);border-bottom-color: #fff;border-width: 10px;margin-left: -10px;
}
#smart_push_smio_close{
position: absolute;right: 5px;top: 2px;background: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAQCAIAAABGNLJTAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAABpSURBVChTzZBLCsAgDER7Zg9iCDmqSzd9MCJtbSvunEXI540kHnVFm9LuHhGlFJUklDRVohvNLKWUc4ZDJJQ02/hBQ5iZDEKJNNt43LsbRhS90Hp1TneU2Fe6Gv6ulOHzyrUfnGoXutYTA3eKL8daaukAAAAASUVORK5CYII=");width: 12px;height: 13px;cursor: pointer;
}
#smart_push_smio_logo{
border:0;float:left;width:24px;height:24px;
}
#smart_push_smio_msg{
margin:2px 0 20px 30px;width: 100%;font-weight: 300;
}
#smart_push_smio_note{
display:none;
}
#smart_push_smio_agreement{
margin: 10px 0;font-weight: 300;color: #6d7471;font-size: 12px;
}
#smart_push_smio_footer{
text-align: right;
}
#smart_push_smio_not_allow{
display: inline-block;width: 80px;border-radius: 1px;border: 1px solid rgb(170, 170, 170);box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 1px 0px;text-align: center;padding: 4px 2px 5px;cursor: pointer;background: #fff;text-transform: none;color:#000;font-weight:300;
}
#smart_push_smio_allow{
display: inline-block;width: 80px;border-radius: 1px;border: 1px solid rgb(170, 170, 170);box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 1px 0px;text-align: center;padding: 4px 2px 5px;cursor: pointer;background: #fff;text-transform: none;color:#000;font-weight:300;
}
      ';
    }
    elseif(self::$apisetting['desktop_popup_layout'] == 'flat'){
      $html .= '
#smart_push_smio_window{
direction:ltr;display: none;z-index: 99999999;max-width:87%;width: 500px;margin: 0 auto;box-shadow: 0 0 20px 3px rgba(0,0,0,.22);background: #fff;padding: 1.286em;border-bottom-left-radius: 2px;border-bottom-right-radius: 2px;font-family: Roboto,Noto,Helvetica Neue,Helvetica,Arial,sans-serif;
}
#smart_push_smio_logo{
float: left;width:80px;height:80px;margin: 10px 0 0 10px;border:0;
}
#smart_push_smio_msg{
margin:0;margin-left:100px;padding:7px 10px;font-size: 19px;line-height:19px;cursor: default;color: #666;text-align:left;
}
#smart_push_smio_note{
margin:0;margin-left:100px;padding:7px 10px;font-size: 16px;line-height:19px;cursor: default;color: #666;text-align:left;
}
#smart_push_smio_agreement{
font-size: 14px;font-weight: 400;margin: 30px 0 0;padding: 0;line-height: 19px;color: #9E9E9E;text-align: left;
}
#smart_push_smio_footer{
text-align: right;margin-top: 35px;
}
#smart_push_smio_not_allow{
background: transparent;color: #4285f4;font-size: 1em;text-transform: uppercase;font-weight: 400;line-height: 1.5;text-align: center;white-space: nowrap;vertical-align: middle;cursor: pointer;letter-spacing: .05em;transition: background-color 75ms ease;border:0;margin:0 10px 0 0;padding-top:8px;
}
#smart_push_smio_allow{
box-shadow: 0 2px 5px 0 rgba(0,0,0,.16), 0 2px 6px 0 rgba(0,0,0,.12);background: #4285f4;color: #fff;padding: .714em 2em;font-size: 1em;text-transform: uppercase;border-radius: 2px;font-weight: 400;line-height: 1.5;text-align: center;white-space: nowrap;vertical-align: middle;cursor: pointer;letter-spacing: .05em;transition: background-color 75ms ease;border: 1px solid transparent;margin:0 10px 0 0;
}
      ';
    }
    elseif(self::$apisetting['desktop_popup_layout'] == 'fancy'){
      $html .= '
#smart_push_smio_window{
direction:ltr;display:none;max-width:94%;z-index:99999999;font-family: Helvetica Neue, Helvetica, Arial, sans-serif;text-align:left;width:600px;height:200px;background:#fff;padding:0;border-radius: 15px;-webkit-border-radius: 15px;-moz-border-radius: 15px;
}
#smart_push_smio_logo{
float:left;width:200px;height:200px;margin: 0 10px 0 0;border:0;border-radius: 15px 0px 0px 15px;-moz-border-radius: 15px 0px 0px 15px;-webkit-border-radius: 15px 0px 0px 15px;
}
#smart_push_smio_msg{
margin:0;margin-left:205px;padding:20px 0 15px 0;font-size: 1.143em;line-height:19px;cursor: default;color: #ff5722;text-align:left;font-weight:700;
}
#smart_push_smio_note{
margin:0;margin-left:205px;padding:0 5px 10px 0;font-size: 15px;line-height:19px;cursor: default;color: #ff5722;text-align:left;
}
#smart_push_smio_agreement{
margin: 0;margin-left: 205px;padding: 0;font-size: 13px;line-height: 17px;color: #9E9E9E;text-align: left;
}
#smart_push_smio_footer{
text-align: center;margin-top: 30px;
}
.smart_push_gdpr_enabled #smart_push_smio_footer{
margin-top: 20px;
}
#smart_push_smio_not_allow{
width:120px;text-transform: none;padding:10px;margin: 0 5px;border:0;border-radius: 5px;-webkit-border-radius: 5px;-moz-border-radius: 5px;background:#ff5722;color:#fff;font-weight:700;cursor:pointer;
}
#smart_push_smio_allow{
width:120px;text-transform: none;padding:10px;margin: 0 5px;border:0;border-radius: 5px;-webkit-border-radius: 5px;-moz-border-radius: 5px;background:#ff5722;color:#fff;font-weight:700;cursor:pointer;
}
#smart_push_smio_copyrights{
position: absolute;padding: 0;font-size: 11px;color: #ccc;left: 210px;bottom: 0;
}
@media (max-width: 450px) {
  #smart_push_smio_logo{
    width:100px;height:100px;
  }
  #smart_push_smio_msg{
    margin-left:105px;
  }
  #smart_push_smio_note{
    margin-left:105px;
  }
}
      ';
    }
    elseif(self::$apisetting['desktop_popup_layout'] == 'ocean'){
      $html .= '
#smart_push_smio_window{
direction:ltr;display:none;max-width:94%;z-index:99999999;font-family: Helvetica Neue, Helvetica, Arial, sans-serif;text-align:left;width:440px;background:#fff;padding:0;border: 1px solid #D0D0D0;border-radius: 0 0 4px 4px;-webkit-border-radius: 0 0 4px 4px;-moz-border-radius: 0 0 4px 4px;box-shadow: 1px 1px 2px #DCDCDC;
}
#smart_push_smio_logo{
float:left;width:74px;height:74px;margin:10px;border:0;
}
#smart_push_smio_msg{
font: 15px/17px open_sansbold,Arial,Helvetica,sans-serif;margin:0;margin-left:100px;padding:13px 0 5px 0;cursor:default;color:#4A4A4A;text-align:left;font-weight:bold;
}
#smart_push_smio_note{
font: 15px/17px open_sanslight,Arial,Helvetica,sans-serif;margin:0;margin-left:100px;padding:0 5px 10px 0;cursor:default;color:#4A4A4A;text-align:left;
}
#smart_push_smio_agreement{
font: 12px open_sanslight,Arial,Helvetica,sans-serif;margin:0;margin-left:100px;padding:0 5px 10px 0;color:#9E9E9E;text-align:left;
}
#smart_push_smio_footer{
text-align: right;margin: 0px 15px 7px 0;
}
#smart_push_smio_not_allow{
width:115px;text-transform: none;padding:5px;margin: 0 5px;border:1px solid #ddd;border-radius: 3px;-webkit-border-radius: 3px;-moz-border-radius: 3px;background:#fff;color:#aaa;font-weight:300;cursor:pointer;font-size: 14px;
}
#smart_push_smio_allow{
width:115px;text-transform: none;padding:5px;margin: 0 5px;border:1px solid #aaa;border-radius: 3px;-webkit-border-radius: 3px;-moz-border-radius: 3px;background:#fff;color:#000;font-weight:300;cursor:pointer;font-size: 14px;
}
#smart_push_smio_not_allow:hover{background-color: #fff}
#smart_push_smio_allow:hover{background-color: #fff}
#smart_push_smio_copyrights{
position: absolute;padding: 0;font-size: 11px;color: #ccc;left: 210px;bottom: 0;
}
@media (max-width: 450px) {
  #smart_push_smio_logo{
    width:50px;height:50px;
  }
  #smart_push_smio_msg{
    margin-left:70px;
  }
  #smart_push_smio_note{
    margin-left:70px;
  }
}
      ';
    }
    elseif(self::$apisetting['desktop_popup_layout'] == 'dark'){
      $html .= '
#smart_push_smio_window{
direction:ltr;display: none;width:540px;max-width: 87%;background-color: #373737; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; padding: 12px 0; border-radius: 5px; text-align: left; overflow: hidden; z-index: 99999999;
}
#smart_push_smio_logo{
float:left;width:80px;height:80px;margin-left:10px;border:0;
}
#smart_push_smio_msg{
margin-left:100px;margin-top: 23px;color: #e1e1df; font-size: 18px; font-weight: 300;padding: 0 5px 0 0;line-height: normal;
}
#smart_push_smio_note{
color: #828284;font-size: 15px;font-weight: 300; position: relative;margin:56px 0 20px 0;padding:20px 5px;line-height: normal;border-top: solid 1px #464646;border-bottom: solid 1px #464646;text-align: center;
}
#smart_push_smio_agreement{
color: #828284;font-size: 12px;font-weight: 300; position: relative;padding:0 10px 10px 10px;line-height: normal;
}
#smart_push_smio_footer{
text-align: center;
}
#smart_push_smio_not_allow{
background-color: #5f5f5f;text-transform: none; color: #929292; border: none; box-shadow: none; font-size: 17px; font-weight: 500; -webkit-border-radius: 20px; border-radius: 20px; padding: 10px 32px; margin: 5px; cursor: pointer;
}
#smart_push_smio_allow{
background-color: #5db166;text-transform: none; color: #fff; border: none; box-shadow: none; font-size: 17px; font-weight: 500; -webkit-border-radius: 20px; border-radius: 20px; padding: 10px 32px; margin: 5px ; cursor: pointer;
}
      ';
    }
    if(empty(self::$apisetting['desktop_popupicon'])){
      $logo = smpush_imgpath.'/megaphone.png';
    }
    else{
      $logo = self::$apisetting['desktop_popupicon'];
    }
    if(self::$apisetting['desktop_paytoread'] != 1){
      $unsubsBTN = '<button type="button" onclick="smpushDestroyReqWindow(true)" id="smart_push_smio_not_allow">'.addslashes(self::$apisetting['desktop_modal_cancel_text']).'</button>';
    }
    else{
      $unsubsBTN = '';
    }
    if(self::$apisetting['gdpr_ver_option'] == 1){
      $agreement = '<p id="smart_push_smio_agreement">'.self::$apisetting['gdpr_ver_text_processed'].'</p>';
      $gdpr_class = 'smart_push_gdpr_enabled';
    }
    else{
      $agreement = $gdpr_class = '';
    }
    $html .= '
</style>
<div id="smart_push_smio_overlay" tabindex="-1" style="opacity:'.((empty(self::$apisetting['desktop_paytoread_darkness']))? 0.8:(self::$apisetting['desktop_paytoread_darkness']/10) ).'; display: none;ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=40); background-color:#000; position: fixed; left: 0; right: 0; top: 0; bottom: 0; z-index: 10000;"></div>
<div id="smart_push_smio_window" class="'.$gdpr_class.'">
  <div id="smart_push_smio_close" onclick="smpushDestroyReqWindow(true)" '.((self::$apisetting['desktop_popup_layout'] != 'native' || self::$apisetting['desktop_paytoread'] == 1)? 'style="display:none"': '').'></div>
  <img id="smart_push_smio_logo" src="'.$logo.'" />
  <p id="smart_push_smio_msg">'.addslashes(self::$apisetting['desktop_modal_title']).'</p>
  <p id="smart_push_smio_note">'.$second_msg.'</p>
  '.$agreement.'
  <div id="smart_push_smio_footer">
    '.$unsubsBTN.'
    <button type="button" class="smpush-push-permission-button" id="smart_push_smio_allow" disabled>'.addslashes(self::$apisetting['desktop_btn_subs_text']).'</button> 
  </div>
</div>
    ';
    return $html;
  }
  
  public static function messengerOfficialWidget(){
    echo '<div class="fb-customerchat" minimized="true" page_id="'.self::$apisetting['msn_official_fbpage_id'].'"></div>';
  }
  
  public static function messengerCustomWidget(){
      $html = '<style>.smpush-fb-livechat,.smpush-fb-widget{display:none}.smpush-ctrlq.smpush-fb-button{position:fixed;right:26px;cursor:pointer}.smpush-ctrlq.smpush-fb-close{position:absolute;right:3px;cursor:pointer}.smpush-ctrlq.smpush-fb-button{z-index:99;background:url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjwhRE9DVFlQRSBzdmcgIFBVQkxJQyAnLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4nICAnaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkJz48c3ZnIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDEyOCAxMjgiIGhlaWdodD0iMTI4cHgiIGlkPSJMYXllcl8xIiB2ZXJzaW9uPSIxLjEiIHZpZXdCb3g9IjAgMCAxMjggMTI4IiB3aWR0aD0iMTI4cHgiIHhtbDpzcGFjZT0icHJlc2VydmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPjxnPjxyZWN0IGZpbGw9IiMwMDg0RkYiIGhlaWdodD0iMTI4IiB3aWR0aD0iMTI4Ii8+PC9nPjxwYXRoIGQ9Ik02NCwxNy41MzFjLTI1LjQwNSwwLTQ2LDE5LjI1OS00Niw0My4wMTVjMCwxMy41MTUsNi42NjUsMjUuNTc0LDE3LjA4OSwzMy40NnYxNi40NjIgIGwxNS42OTgtOC43MDdjNC4xODYsMS4xNzEsOC42MjEsMS44LDEzLjIxMywxLjhjMjUuNDA1LDAsNDYtMTkuMjU4LDQ2LTQzLjAxNUMxMTAsMzYuNzksODkuNDA1LDE3LjUzMSw2NCwxNy41MzF6IE02OC44NDUsNzUuMjE0ICBMNTYuOTQ3LDYyLjg1NUwzNC4wMzUsNzUuNTI0bDI1LjEyLTI2LjY1N2wxMS44OTgsMTIuMzU5bDIyLjkxLTEyLjY3TDY4Ljg0NSw3NS4yMTR6IiBmaWxsPSIjRkZGRkZGIiBpZD0iQnViYmxlX1NoYXBlIi8+PC9zdmc+) center no-repeat #0084ff;width:60px;height:60px;text-align:center;bottom:24px;border:0;outline:0;border-radius:60px;-webkit-border-radius:60px;-moz-border-radius:60px;-ms-border-radius:60px;-o-border-radius:60px;box-shadow:0 1px 6px rgba(0,0,0,.06),0 2px 32px rgba(0,0,0,.16);-webkit-transition:box-shadow .2s ease;background-size:80%;transition:all .2s ease-in-out}.smpush-ctrlq.smpush-fb-button:focus,.smpush-ctrlq.smpush-fb-button:hover{transform:scale(1.1);box-shadow:0 2px 8px rgba(0,0,0,.09),0 4px 40px rgba(0,0,0,.24)}.smpush-fb-widget{background:#fff;z-index:100;position:fixed;width:360px;height:400px;overflow:hidden;opacity:0;bottom:0;right:24px;border-radius:6px;-o-border-radius:6px;-webkit-border-radius:6px;box-shadow:0 5px 40px rgba(0,0,0,.16);-webkit-box-shadow:0 5px 40px rgba(0,0,0,.16);-moz-box-shadow:0 5px 40px rgba(0,0,0,.16);-o-box-shadow:0 5px 40px rgba(0,0,0,.16)}.fb-credit{text-align:center;margin-top:8px}.fb-credit a{transition:none;color:#bec2c9;font-family:Helvetica,Arial,sans-serif;font-size:12px;text-decoration:none;border:0;font-weight:400}.smpush-ctrlq.smpush-fb-overlay{z-index:98;position:fixed;height:100vh;width:100vw;-webkit-transition:opacity .4s,visibility .4s;transition:opacity .4s,visibility .4s;top:0;left:0;background:rgba(0,0,0,.05);display:none}.smpush-ctrlq.smpush-fb-close{z-index:4;padding:0 6px;background:#365899;font-weight:700;font-size:11px;color:#fff;margin:8px;border-radius:3px}.smpush-ctrlq.smpush-fb-close::after{content:"x";font-family:sans-serif}</style>
<div class="smpush-fb-livechat">
  <div class="smpush-ctrlq smpush-fb-overlay"></div>
  <div class="smpush-fb-widget">
    <div class="smpush-ctrlq smpush-fb-close"></div>
    <div class="fb-page" data-href="'.self::$apisetting['msn_fbpage_link'].'" data-tabs="messages" data-width="360" data-height="400" data-small-header="true" data-hide-cover="true" data-show-facepile="false">
      <div cite="'.self::$apisetting['msn_fbpage_link'].'" class="fb-xfbml-parse-ignore"> </div>
    </div>
  </div>
  <a href="'.self::$apisetting['msn_fbpage_link'].'" title="'.self::$apisetting['msn_widget_title'].'" class="smpush-ctrlq smpush-fb-button"></a> 
</div>';
echo 'document.getElementsByTagName("BODY")[0].insertAdjacentHTML("beforeend", \''.preg_replace('/\s+/', ' ', $html).'\');
jQuery(document).ready(function(){var t={delay:125,overlay:jQuery(".smpush-fb-overlay"),widget:jQuery(".smpush-fb-widget"),button:jQuery(".smpush-fb-button")};setTimeout(function(){jQuery("div.smpush-fb-livechat").fadeIn()},8*t.delay),jQuery(".smpush-ctrlq").on("click",function(e){e.preventDefault(),t.overlay.is(":visible")?(t.overlay.fadeOut(t.delay),t.widget.stop().animate({bottom:0,opacity:0},2*t.delay,function(){jQuery(this).hide("slow"),t.button.show()})):t.button.fadeOut("medium",function(){t.widget.stop().show().animate({bottom:"30px",opacity:1},2*t.delay),t.overlay.fadeIn(t.delay)})})});
';
  }
  
}