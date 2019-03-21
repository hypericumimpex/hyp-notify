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

class smpush_shortcode extends smpush_controller {

  public function __construct() {
    parent::__construct();
  }
  
  public static function subscription($args){
    if(!is_user_logged_in()){
      echo '<p>'.__('Please login first to customize your notifications', 'smpush-plugin-lang').' '.'<a href="'.wp_login_url().'">'.__('login now', 'smpush-plugin-lang').'</a></p>';
      return;
    }
    if(self::$apisetting['subspage_geo_status'] == 1){
      wp_enqueue_script('smpush-gmap-source');
      wp_enqueue_script('smpush-gmap-js');
    }
    if(self::$apisetting['subspage_plat_msn'] == 1){
      wp_enqueue_script('smpush-fb-sdk');
    }
    wp_enqueue_style('smpush-frontend');
    wp_enqueue_script('smpush-selectize');
    wp_enqueue_style('smpush-selectize');
    $_REQUEST['oneuserid'] = get_current_user_id();
    $_REQUEST['user_id'] = get_current_user_id();
    $smpush_api = new smpush_api();
    $subscription = $smpush_api->subscription();
    ob_start();
    include(smpush_dir.'/pages/subscription_page.php');
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
  }
  
  public static function fbloign($args){
    wp_enqueue_style('smpush-frontend');
    include(smpush_dir.'/lib/facebook/fbsdk.php');
    $facebook = new FacebookSDK(array(
      'appId' => (!empty(self::$apisetting['fbnotify_appid']))? self::$apisetting['fbnotify_appid'] : self::$apisetting['msn_appid'],
      'secret' => (!empty(self::$apisetting['fbnotify_secret']))? self::$apisetting['fbnotify_secret'] : self::$apisetting['msn_secret'],
      'cookie' => false
     ));
    $fbloginurl = $facebook->getLoginUrl($params = array('scope' => 'public_profile,email', 'redirect_uri' => get_bloginfo('wpurl').'/'.self::$apisetting['push_basename'].'/facebook/?action=login'));
    
    $width = (empty($args['width']))? self::$apisetting['fblogin_btn_width'] : $args['width'];
    $height = (empty($args['height']))? self::$apisetting['fblogin_btn_height'] : $args['height'];
    $text = (empty($args['text']))? self::$apisetting['fblogin_btn_text'] : $args['text'];
    $color = (empty($args['color']))? self::$apisetting['fblogin_btn_color'] : $args['color'];
    $bgcolor = (empty($args['bgcolor']))? self::$apisetting['fblogin_btn_bgcolor'] : $args['bgcolor'];
    if(! empty($args['redirect'])){
      $redirect = get_bloginfo('wpurl').'/'.$args['redirect'];
    }
    elseif(! empty(self::$apisetting['fblogin_btn_redirect'])){
      $redirect = get_bloginfo('wpurl').'/'.self::$apisetting['fblogin_btn_redirect'];
    }
    else{
      $redirect = get_bloginfo('wpurl');
    }
    $icon = self::$apisetting['fblogin_btn_icon'];

    $output = '<style>.smpush-fblogin-button{color:'.$color.'!important;background-color:'.$bgcolor.';width:'.$width.'px;height:'.$height.'px;line-height:'.($height-3).'px;}</style>';
    $output .= '<a href="#" onclick="return smpushOpenFBpopup(\''.$fbloginurl.'\', this)" class="smpush-fblogin-button"><img src="'.$icon.'" /> '.$text.'</a>';
    $output .= '<script data-cfasync="false" type="text/javascript">function smpushOpenFBpopup(url, elm){var new_fbwindow = window.open(url, "", "width=800,height=600");new_fbwindow.addEventListener("message", function(event){if(event.data[0] == "action" && event.data[1] == "success_fblogin"){setTimeout(function(){ window.location="'.$redirect.'"; }, 3500);}}, false);return false;}</script>';
    return $output;
  }
  
  public static function push_history($args){
    wp_enqueue_style('smpush-frontend');
    $output = '';
    $output .= '<div id="smpush-notif-center">';
    if(!is_user_logged_in()){
      echo '<p>'.__('Please login first to check your notification center', 'smpush-plugin-lang').' '.'<a href="'.wp_login_url().'">'.__('login now', 'smpush-plugin-lang').'</p></div>';
      return;
    }
    $wpdateformat = get_option('date_format').' '.get_option('time_format');
    $class = (empty($args['class']))? '' : $args['class'];
    $id = (empty($args['id']))? 'smpush-notification-center' : $args['id'];
    $_REQUEST['perpage'] = (empty($args['limit']))? 10 : intval($args['limit']);
    $_REQUEST['order'] = (empty($args['order']))? 'desc' : $args['order'];
    $_REQUEST['mainPlatforms'] = (empty($args['platform']))? 'web' : $args['platform'];
    $_REQUEST['userid'] = get_current_user_id();
    $smpush_api = new smpush_api();
    $smpush_api->ParseOutput = false;
    $notifications = $smpush_api->get_archive();
    if(!empty($notifications)){
      $output .= '<ul id="'.$id.'" class="'.$class.'">';
      foreach($notifications as $notification){
        $output .= '<li>';
        if(!empty($notification['link'])){
          $output .= '<a href="'.$notification['link'].'" target="_blank">';
        }
        $output .= $notification['message'].' <span>'.gmdate($wpdateformat, strtotime($notification['starttime'])).'</span>';
        if(!empty($notification['link'])){
          $output .= '</a>';
        }
        $output .= '</li>';
      }
      $output .= '</ul>';
    }
    else{
      $output .= '<p>'.__('Your notification center is empty !', 'smpush-plugin-lang').'</p>';
    }
    $output .= '</div>';
    return $output;
  }
  
  public static function messenger($args){
    wp_enqueue_style('smpush-frontend');
    $output = '';
    $width = (empty($args['width']))? self::$apisetting['msn_btn_width'] : $args['width'];
    $height = (empty($args['height']))? self::$apisetting['msn_btn_height'] : $args['height'];
    $text = (empty($args['text']))? self::$apisetting['msn_btn_text'] : $args['text'];
    $color = (empty($args['color']))? self::$apisetting['msn_btn_color'] : $args['color'];
    $bgcolor = (empty($args['bgcolor']))? self::$apisetting['msn_btn_bgcolor'] : $args['bgcolor'];
    $icon = self::$apisetting['msn_btn_icon'];

    $output .= '<style>.smpush-btn-ctrlq.smpush-btn-fb-button{color:'.$color.'!important;background-color:'.$bgcolor.';width:'.$width.'px;height:'.$height.'px;line-height:'.($height-3).'px;}</style>
<div class="smpush-btn-fb-livechat">
  <div class="smpush-btn-ctrlq smpush-btn-fb-overlay"></div>
  <div class="smpush-btn-fb-widget">
    <div class="smpush-btn-ctrlq fb-close"></div>
    <div class="fb-page" data-href="'.self::$apisetting['msn_fbpage_link'].'" data-tabs="messages" data-width="360" data-height="400" data-small-header="true" data-hide-cover="true" data-show-facepile="false">
      <div cite="'.self::$apisetting['msn_fbpage_link'].'" class="fb-xfbml-parse-ignore"> </div>
    </div>
  </div>
  <a href="'.self::$apisetting['msn_btn_fblink'].'" class="smpush-btn-ctrlq smpush-btn-fb-button"><img src="'.$icon.'" /> '.$text.'</a>
</div>';
    $output .= '<script data-cfasync="false" type="text/javascript">
jQuery(document).ready(function(){var t={delay:125,overlay:jQuery(".smpush-btn-fb-overlay"),widget:jQuery(".smpush-btn-fb-widget"),button:jQuery(".smpush-btn-fb-button")};setTimeout(function(){jQuery("div.smpush-btn-fb-livechat").fadeIn()},8*t.delay),jQuery(".smpush-btn-ctrlq").on("click",function(e){e.preventDefault(),t.overlay.is(":visible")?(t.overlay.fadeOut(t.delay),t.widget.stop().animate({opacity:0},2*t.delay,function(){jQuery(this).hide("slow"),t.button.show()})):t.button.fadeOut("medium",function(){t.widget.stop().show().animate({opacity:1},2*t.delay),t.overlay.fadeIn(t.delay)})})});</script>
';
    return $output;
  }
  
  public static function woo_messenger_checkout(){
    if(self::$apisetting['subspage_plat_msn'] == 1 && self::$apisetting['msn_woo_checkout'] == 1){
      wp_enqueue_script('smpush-fb-sdk');
      wp_enqueue_style('smpush-frontend');
      echo '<div id="smpush_msn_woo_checkout">
        '.__('Awesome! Just click on the below button to give us permission to send you notification messages to your Facebook Messenger to follow up your order updates.', 'smpush-plugin-lang').'<br />
        <div class="fb-send-to-messenger" 
          messenger_app_id="'.self::$apisetting['msn_appid'].'" 
          page_id="'.self::$apisetting['msn_official_fbpage_id'].'" 
          data-ref="subscribed" 
          color="white" 
          size="xlarge"></div>
      </div>';
    }
  }
  
  public static function woo_messenger_cartbtn(){
    if(self::$apisetting['subspage_plat_msn'] == 1 && self::$apisetting['msn_woo_cartbtn'] == 1){
      echo '<div style="margin-top: 14px;clear: both;float: left;">';
      self::messenger(array('width' => 200));
      echo '</div>';
    }
  }

  public static function woo_waiting_notifier($availability_text){
    wp_localize_script('smpush-frontend', 'smpush_jslang', array(
      'siteurl' => get_bloginfo('wpurl'),
      'saving_text' => __('saving...', 'smpush-plugin-lang'),
    ));

    if(self::$apisetting['e_woo_waiting'] == 1){
      $stock_status = get_post_meta(get_the_ID(), '_stock_status', true);
      if($stock_status == 'outofstock'){
        wp_enqueue_style('smpush-frontend');
        wp_enqueue_script('smpush-frontend');
        $availability_text .= '<button type="button" value="'.get_the_ID().'" id="smpush_woo_waiting_button" class="single_add_to_cart_button button alt">'.__('Notify me when is available', 'smpush-plugin-lang').'</button>';
      }
    }
    return $availability_text;
  }

}