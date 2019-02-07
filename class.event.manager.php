<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class smpush_event_manager extends smpush_controller {

  public function __construct() {
    parent::__construct();
  }

  public static function page() {
    global $wpdb;
    self::load_jsplugins();
    $pageurl = admin_url().'admin.php?page=smpush_events';
    if ($_POST) {
      if(smpush_env_demo){
        echo 1;
        exit;
      }
      if (empty($_POST['title']) || empty($_POST['message']) || empty($_POST['event_post_type'])) {
        self::jsonPrint(0, __('All fields are required.', 'smpush-plugin-lang'));
      }
      $conditions = array();
      foreach($_POST['conditions']['attri'] as $key => $value){
        if(!empty($_POST['conditions']['attri'][$key]) && !empty($_POST['conditions']['sign'][$key])){
          $conditions['attri'][$key] = $_POST['conditions']['attri'][$key];
          $conditions['sign'][$key] = $_POST['conditions']['sign'][$key];
          $conditions['value'][$key] = $_POST['conditions']['value'][$key];
        }
      }
      $data = array();
      $data['title'] = $_POST['title'];
      $data['event_type'] = $_POST['event_type'];
      $data['post_type'] = $_POST['event_post_type'];
      $data['subject'] = $_POST['subject'];
      $data['message'] = $_POST['message'];
      $data['fbmsn_message'] = $_POST['fbmsn_message'];
      $data['fbnotify_message'] = $_POST['fbnotify_message'];
      $data['email_message'] = $_POST['email_message'];
      $data['subs_filter'] = $_POST['subs_filter'];
      $data['notify_segment'] = $_POST['notify_segment'];
      $data['userid_field'] = $_POST['userid_field'];
      $data['msg_template'] = $_POST['msg_template'];
      $data['conditions'] = (empty($conditions))? '' : serialize($conditions);
      $data['payload_fields'] = (empty($_POST['payload']))? '' : serialize($_POST['payload']);
      $data['desktop_link'] = (isset($_POST['desktop_link']))? 1 : 0;
      $data['email'] = (isset($_POST['email']))? 1 : 0;
      $data['status'] = (isset($_POST['status']))? 1 : 0;
      $data['ignore'] = (isset($_POST['ignore']))? 1 : 0;
      if (!empty($_POST['id'])) {
        $wpdb->update($wpdb->prefix.'push_events', $data, array('id' => $_POST['id']));
      } else {
        $wpdb->insert($wpdb->prefix.'push_events', $data);
      }
      echo 1;
      exit;
    }
    elseif (isset($_GET['loadAttri'])) {
      $post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE post_type='$_GET[smpush_post_type]' LIMIT 0,1", 'ARRAY_A');
      if(empty($post)){
        echo json_encode(array('status' => 0));
        exit;
      }
      $selcOptions = '<option value="">'.__('Choose Attribute', 'smpush-plugin-lang').'</option><optgroup label="'.__('Post Attributes', 'smpush-plugin-lang').'">';
      foreach($post as $column => $value){
        $selcOptions .= '<option>'.$column.'</option>';
      }
      $selcOptions .= '</optgroup>';
      $postIDs = $wpdb->get_row("SELECT GROUP_CONCAT(ID SEPARATOR ',') AS ids FROM $wpdb->posts WHERE post_type='$_GET[smpush_post_type]' ORDER BY ID DESC LIMIT 0,1000", 'ARRAY_A');
      $postIDs['ids'] = rtrim($postIDs['ids'], ',');
      $metakeys = $wpdb->get_results("SELECT DISTINCT(meta_key) AS meta_key FROM $wpdb->postmeta WHERE post_id IN($postIDs[ids])", 'ARRAY_A');
      $selcOptions .= '<optgroup label="'.__('Postmeta Attributes', 'smpush-plugin-lang').'">';
      foreach($metakeys as $metakey){
        $selcOptions .= '<option value="meta_'.$metakey['meta_key'].'">'.$metakey['meta_key'].'</option>';
      }
      $selcOptions .= '</optgroup>';
      
      $postFuncs = '<option value="">'.__('Pass Any ID To Function And Get', 'smpush-plugin-lang').'</option>
      <optgroup label="'.__('Post Functions', 'smpush-plugin-lang').'">
        <option value="post_title">'.__('Title', 'smpush-plugin-lang').'</option>
        <option value="post_content">'.__('Content', 'smpush-plugin-lang').'</option>
        <option value="post_permalink">'.__('Permalink', 'smpush-plugin-lang').'</option>
        <option value="post_date">'.__('Publish Date', 'smpush-plugin-lang').'</option>
        <option value="post_mod_date">'.__('Last Update Date', 'smpush-plugin-lang').'</option>
        <option value="post_categories">'.__('Categories', 'smpush-plugin-lang').'</option>
        <option value="post_categories_ids">'.__('Categories IDs', 'smpush-plugin-lang').'</option>
        <option value="post_tags">'.__('Tags', 'smpush-plugin-lang').'</option>
      </optgroup>
      <optgroup label="'.__('User Functions', 'smpush-plugin-lang').'">
        <option value="user_title">'.__('Display Name', 'smpush-plugin-lang').'</option>
        <option value="user_title_fpost">'.__('Display Name Using Post ID', 'smpush-plugin-lang').'</option>
        <option value="user_email">'.__('Email', 'smpush-plugin-lang').'</option>
        <option value="user_name">'.__('Username', 'smpush-plugin-lang').'</option>
      </optgroup>
      <optgroup label="'.__('Time Functions', 'smpush-plugin-lang').'">
        <option value="format_date">'.__('Format Date', 'smpush-plugin-lang').'</option>
        <option value="remain_time">'.__('Remaining Time', 'smpush-plugin-lang').'</option>
      </optgroup>';
      $taxs = get_object_taxonomies($_GET['smpush_post_type'], 'names');
      if(!empty($taxs)){
        $postFuncs .= '<optgroup label="'.__('Taxonomies Return IDs', 'smpush-plugin-lang').'">';
        foreach($taxs as $tax){
          $postFuncs .= '<option>'.$tax.'</option>';
        }
        $postFuncs .= '</optgroup>';
      }
      
      if(!empty($taxs)){
        $selcOptions .= '<optgroup label="'.__('Taxonomies IDs', 'smpush-plugin-lang').'">';
        foreach($taxs as $tax){
          $selcOptions .= '<option value="tax_func_'.$tax.'">'.$tax.'</option>';
        }
        $selcOptions .= '</optgroup>';
      }
      
      $html = array('status' => 1, 'postAttrs' => $selcOptions, 'postFuncs' => $postFuncs);
      echo json_encode($html);
      exit;
    }
    elseif (isset($_GET['delete'])) {
      if(smpush_env_demo){
        echo 1;
        exit;
      }
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_events WHERE id='$_GET[id]'");
      wp_redirect($pageurl);
    }
    elseif (isset($_GET['id'])) {
      if ($_GET['id'] == -1) {
        $event = array('id' => 0, 'title' => '', 'event_type' => '1', 'msg_template' => 0, 'post_type' => '', 'subject' => '', 'message' => '', 'fbmsn_message' => '', 'fbnotify_message' => '', 'email_message' => '', 'notify_segment' => 'all', 'subs_filter' => 'all', 'userid_field' => '', 'desktop_link' => 1, 'email' => 0, 'status' => 1, 'ignore' => 0);
      }
      else {
        $event = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."push_events WHERE id='$_GET[id]'", 'ARRAY_A');
        $event['conditions'] = unserialize($event['conditions']);
        $event['payload_fields'] = unserialize($event['payload_fields']);
        $event = stripslashes_deep($event);
      }
      $templates = $wpdb->get_results("SELECT id,name FROM ".$wpdb->prefix."push_archive WHERE send_type='template' ORDER BY id ASC", 'ARRAY_A');
      include(smpush_dir.'/pages/event_form.php');
      exit;
    }
    else {
      $events = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_events ORDER BY id ASC");
      wp_enqueue_script('jquery-ui-tabs');
      wp_enqueue_style('smpush-jquery-smoothness');
      include(smpush_dir.'/pages/event_manage.php');
    }
  }
  
}