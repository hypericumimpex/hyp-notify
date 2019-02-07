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

class smpush_widget extends WP_Widget {

  function __construct() {
    parent::__construct(false, __('Push Notification Subscription', 'smpush-plugin-lang'));
  }
  
  static function shortcode() {
    $apisettings = get_option('smpush_options');
    $apisettings['gdpr_ver_text_processed'] = smpush_helper::processGDPRText($apisettings['gdpr_privacylink'], $apisettings['gdpr_termslink'], $apisettings['gdpr_ver_text']);

    $activewidget = array();
    $widgets = get_option('widget_smpush_widget');
    if(!empty($widgets)){
      foreach($widgets as $key => $instance){
        if(! is_numeric($key))          break;
        $activewidget = $instance;
      }
    }
    if(empty($activewidget)){
      $activewidget = array(
          'container' => 'div',
          'container_class' => '',
          'head' => 'h2',
          'head_class' => '',
          'head_title' => __('Get Notified Of New Posts', 'smpush-plugin-lang'),
          'message' => __('Turn on desktop push notification', 'smpush-plugin-lang'),
          'save_channels_btn' => __('Update Subscriptions', 'smpush-plugin-lang'),
          'show_channels' => 1
      );
    }
    $activewidget['gdpr_ver_option'] = $apisettings['gdpr_ver_option'];
    $activewidget['gdpr_ver_text_processed'] = $apisettings['gdpr_ver_text_processed'];
    $smpush_widget = new smpush_widget();
    return $smpush_widget->widget(array(), $activewidget, false);
  }
  
  function widget($args=array(), $instance, $output = true) {
    if(!empty($instance['logged_only']) && $instance['logged_only'] == 1 && !is_user_logged_in()){
      return;
    }
    $enableSaveChannelBTN = false;
    $settings = get_option('smpush_options');
    $settings['gdpr_ver_text_processed'] = smpush_helper::processGDPRText($settings['gdpr_privacylink'], $settings['gdpr_termslink'], $settings['gdpr_ver_text']);

    if($instance['show_channels'] == 1 && is_user_logged_in()){
      global $wpdb;
      $channels = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'push_channels ORDER BY title ASC');
      $subschannels = get_user_meta(get_current_user_id(), 'smpush_subscribed_channels', true);
      if($subschannels !== false){
        $enableSaveChannelBTN = true;
      }
      if(empty($subschannels)){
        $subschannels = array();
      }
      else{
        $subschannels = explode(',', $subschannels);
      }
    }
    if(empty($subschannels)){
      $subschannels = array();
    }
    $instance['gdpr_ver_option'] = $settings['gdpr_ver_option'];
    $instance['gdpr_ver_text_processed'] = $settings['gdpr_ver_text_processed'];

    if(! $output){
      ob_start();
    }
    include(smpush_dir.'/pages/widget.php');
    if(! $output){
      $output = ob_get_contents();
      ob_end_clean();
      return $output;
    }
  }

  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['container'] = strip_tags($new_instance['container']);
    $instance['container_class'] = strip_tags($new_instance['container_class']);
    $instance['head'] = strip_tags($new_instance['head']);
    $instance['head_class'] = strip_tags($new_instance['head_class']);
    $instance['custom_css'] = strip_tags($new_instance['custom_css']);
    $instance['head_title'] = strip_tags($new_instance['head_title']);
    $instance['message'] = strip_tags($new_instance['message']);
    $instance['save_channels_btn'] = strip_tags($new_instance['save_channels_btn']);
    if(isset($new_instance['show_channels'])){
      $instance['show_channels'] = 1;
    }
    else{
      $instance['show_channels'] = 0;
    }
    if(isset($new_instance['logged_only'])){
      $instance['logged_only'] = 1;
    }
    else{
      $instance['logged_only'] = 0;
    }
    return $instance;
  }

  function form($instance) {
    if (empty($instance)) {
      $instance = array();
    }
    $defaults = array(
    'container' => '',
    'container_class' => '',
    'head' => '',
    'head_class' => '',
    'custom_css' => '',
    'head_title' => __('Get Notified Of New Posts', 'smpush-plugin-lang'),
    'message' => __('Turn on desktop push notification', 'smpush-plugin-lang'),
    'save_channels_btn' => __('Update Subscriptions', 'smpush-plugin-lang'),
    'show_channels' => 1,
    'logged_only' => 0,
    );
    $instance = array_merge($defaults, $instance);
    $container = $instance['container'];
    $container_class = $instance['container_class'];
    $head = $instance['head'];
    $head_class = $instance['head_class'];
    $custom_css = $instance['custom_css'];
    $head_title = $instance['head_title'];
    $message = $instance['message'];
    $save_channels_btn = $instance['save_channels_btn'];
    $show_channels = $instance['show_channels'];
    $logged_only = $instance['logged_only'];
    ?>
    <p>
      <label for="<?php echo $this->get_field_id('container'); ?>"><?php echo __('Container Tag', 'smpush-plugin-lang')?>:</label>
      <input class="widefat" type="text" id="<?php echo $this->get_field_id('container'); ?>" placeholder="e.g. aside, section or div" name="<?php echo $this->get_field_name('container'); ?>" value="<?php echo esc_attr($container); ?>">
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('container_class'); ?>"><?php echo __('Container CSS Class Name', 'smpush-plugin-lang')?>:</label>
      <input class="widefat" type="text" id="<?php echo $this->get_field_id('container_class'); ?>" name="<?php echo $this->get_field_name('container_class'); ?>" value="<?php echo esc_attr($container_class); ?>">
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('head'); ?>"><?php echo __('Head Title Tag', 'smpush-plugin-lang')?>:</label>
      <input class="widefat" type="text" id="<?php echo $this->get_field_id('head'); ?>" placeholder="e.g. h1, h2 or label" name="<?php echo $this->get_field_name('head'); ?>" value="<?php echo esc_attr($head); ?>">
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('head_class'); ?>"><?php echo __('Head Title CSS Class Name', 'smpush-plugin-lang')?>:</label>
      <input class="widefat" type="text" id="<?php echo $this->get_field_id('head_class'); ?>" name="<?php echo $this->get_field_name('head_class'); ?>" value="<?php echo esc_attr($head_class); ?>">
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('head_title'); ?>"><?php echo __('Head Title', 'smpush-plugin-lang')?>:</label>
      <input class="widefat" type="text" id="<?php echo $this->get_field_id('head_title'); ?>" name="<?php echo $this->get_field_name('head_title'); ?>" value="<?php echo esc_attr($head_title); ?>">
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('message'); ?>"><?php echo __('Message', 'smpush-plugin-lang')?>:</label>
      <input class="widefat" type="text" id="<?php echo $this->get_field_id('message'); ?>" name="<?php echo $this->get_field_name('message'); ?>" value="<?php echo esc_attr($message); ?>">
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('save_channels_btn'); ?>"><?php echo __('Save Channels Button Text', 'smpush-plugin-lang')?>:</label>
      <input class="widefat" type="text" id="<?php echo $this->get_field_id('save_channels_btn'); ?>" name="<?php echo $this->get_field_name('save_channels_btn'); ?>" value="<?php echo esc_attr($save_channels_btn); ?>">
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('show_channels'); ?>">
        <input class="widefat" type="checkbox" id="<?php echo $this->get_field_id('show_channels'); ?>" value="1" name="<?php echo $this->get_field_name('show_channels'); ?>" <?php if($show_channels == 1): ?>checked="checked"<?php endif;?> /> <?php echo __('Show channels subscription if user is logged', 'smpush-plugin-lang')?>
      </label>
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('logged_only'); ?>">
        <input class="widefat" type="checkbox" id="<?php echo $this->get_field_id('logged_only'); ?>" value="1" name="<?php echo $this->get_field_name('logged_only'); ?>" <?php if($logged_only == 1): ?>checked="checked"<?php endif;?> /> <?php echo __('Show this widget for logged users only', 'smpush-plugin-lang')?>
      </label>
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('custom_css'); ?>"><?php echo __('Custom CSS', 'smpush-plugin-lang')?>:</label>
      <textarea class="widefat" rows="8" id="<?php echo $this->get_field_id('custom_css'); ?>" placeholder="<?php echo __('Write CSS code to customise this widget design', 'smpush-plugin-lang')?>" name="<?php echo $this->get_field_name('custom_css'); ?>"><?php echo esc_attr($custom_css)?></textarea>
    </p>
    <?php
  }

}
