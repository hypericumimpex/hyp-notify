<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class smpush_events extends smpush_controller{
  private static $post;
  private static $sendToDevices;
  private static $sendToType;
  private static $wpdateformat;
  private static $payloads = array();
  private static $cronsettings = array();
  private static $desktopLinkOpen = false;
  private static $emailWPUsers = false;
  private static $excludeStatus = "'inherit','auto-draft','future','private','trash'";
  private static $excludeStatusArr = array('inherit','auto-draft','future','private','trash');
  
  public function __construct(){
    parent::__construct();
  }
  
  private static function eventVerifyConditions($conditions){
    foreach($conditions['attri'] as $key => $value){
      $param = $conditions['attri'][$key];
      $sign = $conditions['sign'][$key];
      $value = $conditions['value'][$key];
      if(preg_match('/^meta_/', $param)){
        $param = preg_replace('/^meta_/', '', $param);
        if(isset(self::$post->meta_keys[$param][0])){
          $param = self::$post->meta_keys[$param][0];
        }
        else{
          return false;
        }
      }
      elseif(preg_match('/^tax_func_/', $param)){
        $param = str_replace('tax_func_', '', $param);
        $terms = get_the_terms(self::$post->ID, $param);
        $return = array();
        if(!empty($terms)){
          foreach($terms as $term){
            $return[] = $term->term_id;
          }
        }
        $param = implode(',', $return);
      }
      elseif(isset(self::$post->$param)){
        $param = self::$post->$param;
      }
      else{
        return false;
      }
      if(strtolower($value) == 'now()'){
        $param = strtotime($param);
        $value = current_time('timestamp');
      }
      elseif(strtolower($value) == 'date()'){
        $param = strtotime($param);
        $value = current_time('timestamp');
      }
      switch($sign){
        case '>':
          if($param <= $value)return false;
          break;
        case '>=':
          if($param < $value)return false;
          break;
        case '<':
          if($param >= $value)return false;
          break;
        case '<=':
          if($param > $value)return false;
          break;
        case '=':
          if($param != $value)return false;
          break;
        case 'NOT =':
          if($param == $value)return false;
          break;
        case 'IN':
          $haystack = explode(',', $value);
          $haystack = array_map('trim', $haystack);
          if(!in_array($param, $haystack))return false;
          break;
        case 'NOT IN':
          $haystack = explode(',', $value);
          $haystack = array_map('trim', $haystack);
          if(in_array($param, $haystack))return false;
          break;
        case 'INTERSECT':
          $array1 = explode(',', $value);
          $array2 = explode(',', $param);
          $array1 = array_map('trim', $array1);
          $array2 = array_map('trim', $array2);
          if(!array_intersect($array1, $array2))return false;
          break;
        case 'NOT INTERSECT':
          $array1 = explode(',', $value);
          $array2 = explode(',', $param);
          $array1 = array_map('trim', $array1);
          $array2 = array_map('trim', $array2);
          if(array_intersect($array1, $array2))return false;
          break;
      }
    }
  }
  
  private static function extractParamsValues($message, $ignore){
    if(preg_match_all('/\{\$([^}]+)\}/', $message, $matches)){
      self::$wpdateformat = get_option('date_format').' '.get_option('time_format');
      foreach($matches[1] as $dynparam){
        $params = explode('|', $dynparam);
        $dynparam = $params[0];
        $expression = '{$'.$dynparam;
        $replace = '';
        if(preg_match('/^meta_/', $dynparam)){
          $dynparamfix = preg_replace('/^meta_/', '', $dynparam);
          if(!empty(self::$post->meta_keys[$dynparamfix][0])){
            $replace = self::$post->meta_keys[$dynparamfix][0];
          }
          elseif($ignore == 1){
            continue;
          }
        }
        elseif(preg_match('/^tax_func_/', $dynparam)){
          $dynparam = str_replace('tax_func_', '', $dynparam);
          $terms = get_the_terms(self::$post->ID, $dynparam);
          $return = array();
          if(!empty($terms)){
            foreach($terms as $term){
              $return[] = $term->term_id;
            }
          }
          $replace = implode(',', $return);
        }
        elseif(!empty(self::$post->$dynparam)){
          $replace = self::$post->$dynparam;
        }
        elseif($ignore == 1){
          continue;
        }
        if(isset($params[1])){
          $expression .= '|'.$params[1];
          switch ($params[1]){
            case 'CapitalizeFirst':
              $replace = ucfirst($replace);
              break;
            case 'CapitalizeAllFirst':
              $replace = ucwords($replace);
              break;
            case 'UPPERCASE':
              $replace = strtoupper($replace);
              break;
            case 'lowercase':
              $replace = strtolower($replace);
              break;
            case 'datetime':
              $replace = gmdate(get_option('date_format').' '.get_option('time_format'), strtotime($replace));
              break;
            case 'date':
              $replace = gmdate(get_option('date_format'), strtotime($replace));
              break;
            case 'truncate':
              $replace = smpush_helper::ShortString($replace, ((is_numeric($params[2]))? $params[2] : 150));
              break;
            case 'regular':
              break;
            default :
              $replace = $params[1];
              break;
          }
        }
        if(isset($params[2])){
          $expression .= '|'.$params[2];
        }
        if(isset($params[3])){
          $expression .= '|'.$params[3];
          switch ($params[3]){
            case 'post_title':
              $post = get_post($replace);
              $replace = $post->post_title;
              break;
            case 'post_content':
              $post = get_post($replace);
              $replace = $post->post_content;
              break;
            case 'post_permalink':
              $replace = get_permalink($replace);
              break;
            case 'post_date':
              $post = get_post($replace);
              $replace = gmdate(get_option('date_format'), strtotime($post->post_date));
              break;
            case 'post_mod_date':
              $post = get_post($replace);
              $replace = gmdate(get_option('date_format'), strtotime($post->post_modified));
              break;
            case 'post_categories':
              $cats = wp_get_post_categories($replace, array('fields' => 'names'));
              $replace = implode(', ', $cats);
              break;
            case 'post_categories_ids':
              $cats = wp_get_post_categories($replace, array('fields' => 'id'));
              $replace = implode(', ', $cats);
              break;
            case 'post_tags':
              $posttags = get_the_tags($replace);
              if($posttags) {
                $tags = array();
                foreach($posttags as $tag) {
                  $tags[] = $tag->name; 
                }
                $replace = implode(', ', $tags);
              }
              break;
            case 'user_title_fpost':
              $post = get_post($replace);
              $user_info = get_userdata($post->post_author);
              $replace = $user_info->display_name;
              break;
            case 'user_title':
              $user_info = get_userdata($replace);
              $replace = $user_info->display_name;
              break;
            case 'user_email':
              $user_info = get_userdata($replace);
              $replace = $user_info->user_email;
              break;
            case 'user_name':
              $user_info = get_userdata($replace);
              $replace = $user_info->user_login;
              break;
            case 'format_date':
              $replace = gmdate(self::$wpdateformat, strtotime($replace));
              break;
            case 'remain_time':
              $replace = smpush_helper::remain_time($replace);
              break;
            default :
              $terms = get_the_terms($replace, $params[3]);
              $return = array();
              if(!empty($terms)){
                foreach($terms as $term){
                  $return[] = $term->term_id;
                }
              }
              $replace = implode(',', $return);
              break;
          }
        }
        if(isset($params[2]) && $replace == ''){
          if($params[2] == 'null'){
            $replace = '';
          }
          else{
            $replace = $params[2];
          }
        }
        $expression .= '}';
        $message = str_replace($expression, self::ShortString($replace, 250), $message);
      }
    }
    return $message;
  }
  
  private static function eventManager($event_type, $postid, $channelIDs){
    global $wpdb;
    $events = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_events WHERE event_type='$event_type' AND post_type='".self::$post->post_type."' AND status='1'", 'ARRAY_A');
    if($events){
      foreach($events as $event){
        self::$payloads = array();
        self::$cronsettings = array();
        self::$cronsettings['once_notify'] = $event['once_notify'];
        $event = stripslashes_deep($event);
        self::$desktopLinkOpen = $event['desktop_link'];
        self::$emailWPUsers = $event['email'];
        $conditions = unserialize($event['conditions']);
        $payload_fields = unserialize($event['payload_fields']);
        if(!empty($conditions['attri'])){
          $continue = self::eventVerifyConditions($conditions);
          if($continue === false){
            continue;
          }
        }
        if($event['notify_segment'] == 'custom'){
          $userid_field = $event['userid_field'];
          if(preg_match('/^meta_/', $userid_field)){
            $userid_field = preg_replace('/^meta_/', '', $userid_field);
            if(!empty(self::$post->meta_keys[$userid_field][0])){
              self::$sendToDevices = self::$post->meta_keys[$userid_field][0];
              self::$sendToType = 'userid';
            }
          }
          elseif(!empty(self::$post->$userid_field)){
            self::$sendToDevices = self::$post->$userid_field;
            self::$sendToType = 'userid';
          }
          else{
            continue;
          }
        }
        $message = array();
        $message['subject'] = self::extractParamsValues($event['subject'], $event['ignore']);
        $message['web'] = self::extractParamsValues($event['message'], $event['ignore']);
        $message['fbmsn'] = self::extractParamsValues($event['fbmsn_message'], $event['ignore']);
        $message['fbnotify'] = self::extractParamsValues($event['fbnotify_message'], $event['ignore']);
        $message['email'] = self::extractParamsValues($event['email_message'], $event['ignore']);
        if(!empty($event['payload_fields'])){
          foreach($payload_fields['field'] as $key => $field){
            self::$payloads[$field] = self::extractParamsValues($payload_fields['value'][$key], $event['ignore']);
          }
        }
        switch($event['notify_segment']){
          case 'all':
            if(self::$apisetting['e_post_chantocats'] == 1){
              self::$sendToDevices = self::PushUsersInPostCat($postid);
              self::$sendToType = 'tokenid';
            }
            else{
              self::$sendToDevices = 'all';
              self::$sendToType = 'all';
            }
            break;
          case 'post_owner':
            self::$sendToDevices = self::UsersRelatedPost($postid);
            self::$sendToType = 'userid';
            break;
          case 'post_commenters':
            self::$sendToDevices = self::UsersRelatedPost($postid, 'commenters');
            self::$sendToType = 'userid';
            break;
          case 'comment_mentions':
            self::$sendToDevices = self::UsersRelatedPost($postid, 'mentions');
            self::$sendToType = 'userid';
            break;
          case 'quoted_comment':
            self::$sendToDevices = self::UsersRelatedPost($postid, 'quoted');
            self::$sendToType = 'userid';
            break;
          case 'tev_attendees':
            self::$sendToDevices = self::UsersRelatedPost($postid, 'tev_attendees');
            self::$sendToType = 'userid';
            break;
        }
        self::eventSendQueuedMessage($message, $postid, $event_type, $channelIDs, $event['msg_template'], $event['subs_filter']);
      }
    }
  }
  
  public static function eventSendQueuedMessage($message, $postid, $event_type, $channelIDs, $templateid, $subs_filter){
    if(self::$sendToDevices !== false){
      $mute_activated = get_post_meta($postid, 'smpush_mute_activated', true);
      if(!empty($mute_activated) && $mute_activated == 'yes'){
        return;
      }
      if(!empty($mute_activated) && $mute_activated == 'later'){
        update_post_meta($postid, 'smpush_mute_activated', 'yes');
      }

      switch ($event_type){
        case 'publish':
          $filter = 'newpost';
          break;
        case 'approve':
          $filter = 'apprpost';
          break;
        case 'update':
          $filter = 'postupdated';
          break;
      }
      
      $message['web'] = apply_filters('smpush_events_'.$filter.'_message', $message['web'], $postid);
      $payload = apply_filters('smpush_events_'.$filter.'_payload', $postid, $message['web']);

      if(!empty(self::$payloads) && !empty($payload) && is_array($payload)){
        $payload = array_merge(self::$payloads, $payload);
      }
      elseif(empty(self::$payloads) && !empty($payload) && is_array($payload)){
        $payload = $payload;
      }
      elseif(!empty(self::$payloads)){
        $payload = self::$payloads;
      }
      else{
        $payload = array('relatedvalue' => $postid);
      }
      $payload['relatedvalue'] = $postid;
      $payload['post_id'] = $postid;
      $payload['post_type'] = self::$post->post_type;
      $payload = apply_filters('smpush_events_'.$filter.'_payload_final', $payload, $postid);

      $cronsetting = array();
      $cronsetting['desktop_title'] = self::$post->post_title;
      $cronsetting['desktop_link'] = (empty(self::$desktopLinkOpen))? ''  : get_permalink($postid);
      $post_thumbnail = get_post_thumbnail_id($postid);
      $post_thumbnail_big = wp_get_attachment_image_src($post_thumbnail, 'medium');
      $post_thumbnail_small = wp_get_attachment_image_src($post_thumbnail, 'thumbnail', true);
      if(!empty($post_thumbnail_big)){
        $cronsetting['desktop_bigimage'] = esc_url($post_thumbnail_big[0], ['https']);
        $cronsetting['desktop_icon'] = esc_url($post_thumbnail_small[0], ['https']);
      }
      $cronsetting['name'] = self::$post->post_title;
      $cronsetting['post_id'] = $postid;
      $cronsetting['post_type'] = self::$post->post_type;
      $cronsetting['subs_filter'] = $subs_filter;
      $cronsetting['email_wp_users'] = self::$emailWPUsers;
      $cronsetting = apply_filters('smpush_events_'.$filter.'_settings', $cronsetting, $message['web'], $postid);
      if(! empty(self::$cronsettings)){
        $cronsetting = array_merge($cronsetting, self::$cronsettings);
      }

      smpush_sendpush::SendCronPush(self::$sendToDevices, $message, $payload, self::$sendToType, $cronsetting, 0 , $channelIDs, false, $templateid);
    }
  }

  public static function woocommerce_event($orderid){
    if(empty($_POST['post_ID']) && !is_numeric($orderid)){
      return false;
    }
    $post = new stdClass();
    $post->ID = (empty($_POST['post_ID']))? $orderid : $_POST['post_ID'];
    self::queue_event('publish', 'draft', $post);
  }
  
  public static function queue_event($new_status, $old_status, $post){
    if($post->post_type == 'product' && $post->post_status == 'publish' && isset($_POST['_stock_status'])){
      if($_POST['_manage_stock'] == 'yes' && $_POST['_original_stock'] == 0 && $_POST['_stock'] > 0){
        self::sendWooWaitingProduct($post->ID, $_POST);
      }
      elseif($_POST['_manage_stock'] != 'yes' && $_POST['_stock_status'] == 'instock'){
        $old_stock_status = get_post_meta($post->ID, '_stock_status', true);
        if($old_stock_status == 'outofstock'){
          self::sendWooWaitingProduct($post->ID, $_POST);
        }
      }
    }
    if(isset($_POST['smpush_mute'])){
      if($_POST['smpush_mute'] == 'lock'){
        update_post_meta($post->ID, 'smpush_mute_activated', 'later');
      }
      else{
        update_post_meta($post->ID, 'smpush_mute_activated', $_POST['smpush_mute']);
      }
      if($_POST['smpush_mute'] == 'yes'){
        return;
      }
    }
    else{
      $mute_activated = get_post_meta($post->ID, 'smpush_mute_activated', true);
      if(!empty($mute_activated) && $mute_activated == 'yes'){
        return;
      }
    }
    if(in_array($new_status, self::$excludeStatusArr)){
      return;
    }
    $timenow = current_time('timestamp');
    global $wpdb;
    $wpdb->delete($wpdb->prefix.'push_events_queue', array('post_id' => $post->ID, 'new_status' => $new_status, 'old_status' => $old_status));
    $event = array();
    $event['post_id'] = $post->ID;
    $event['new_status'] = $new_status;
    $event['old_status'] = $old_status;
    $event['post'] = (!empty($_POST)) ? serialize($_POST) : serialize(array());
    if(empty($_POST['smiotime_mm'])){
      $event['pushtime'] = gmdate('Y/m/d H:i:s', $timenow);
    }
    else{
      $event['pushtime'] = $_POST['smiotime_aa'].'/'.$_POST['smiotime_mm'].'/'.$_POST['smiotime_jj'].' '.$_POST['smiotime_hh'].':'.$_POST['smiotime_mn'].':00';
    }
    if(!empty($post->post_type) && $post->post_status == 'publish' && $post->post_type == 'tribe_events'){
      $wpdb->delete($wpdb->prefix.'push_events_queue', array('post_id' => $post->ID));
      if($old_status == 'draft'){
        $wpdb->insert($wpdb->prefix.'push_events_queue', $event);
      }
      $event['old_status'] = 'publish';
      $start_time = strtotime(get_post_meta($post->ID, '_EventStartDate', true));
      if($start_time-604800 > $timenow){
        $event['pushtime'] = gmdate('Y/m/d H:i:s', ($start_time-604800));//1 week
        $wpdb->insert($wpdb->prefix.'push_events_queue', $event);
      }
      if($start_time-172800 > $timenow){
        $event['pushtime'] = gmdate('Y/m/d H:i:s', ($start_time-172800));//2 days
        $wpdb->insert($wpdb->prefix.'push_events_queue', $event);
      }
      if($start_time-86400 > $timenow){
        $event['pushtime'] = gmdate('Y/m/d H:i:s', ($start_time-86400));//1 day
        $wpdb->insert($wpdb->prefix.'push_events_queue', $event);
      }
      if($start_time-7200 > $timenow){
        $event['pushtime'] = gmdate('Y/m/d H:i:s', ($start_time-7200));//2 hours
        $wpdb->insert($wpdb->prefix.'push_events_queue', $event);
      }
    }
    else{
      $wpdb->insert($wpdb->prefix.'push_events_queue', $event);
    }
  }
  
  public static function post_status_change($new_status, $old_status, $postid, $post){
    self::$post = get_post($postid);
    if(empty(self::$post)){
      return false;
    }
    if($new_status == 'draft' && self::$post->post_status == 'draft'){
      return false;
    }
    if(in_array(self::$post->post_status, self::$excludeStatusArr)){
      return;
    }
    self::$post->meta_keys = get_post_meta($postid);
    
    $channelIDs = '';
    if(!isset($post['smpush_all_users']) && !empty($post['smpush_channels'])){
      $channelIDs = implode(',', $post['smpush_channels']);
    }
    
    if(!empty($new_status) && !empty($old_status)){
      if($new_status == 'publish' && $old_status != $new_status){
        $message = self::eventManager('publish', $postid, $channelIDs);
        $message = self::eventManager('approve', $postid, $channelIDs);
      }
      else{
        $message = self::eventManager('update', $postid, $channelIDs);
      }
    }
  }
  
  private static function processNotifBody($type, $subject){
    $type = $type.'_body';
    $message = str_replace(array('{subject}','{comment}'), $subject, stripslashes(self::$apisetting[$type]));
    return $message;
  }

  public static function comment_approved($nowcomment){
    if(self::$apisetting['e_appcomment'] == 1){
      $subject = self::ShortString($nowcomment->comment_content, 200);
      $message = self::processNotifBody('e_appcomment', $subject);
      $postid = $nowcomment->comment_post_ID;
      $commentid = $nowcomment->comment_ID;
      
      $message = apply_filters('smpush_events_approvecomment_message', $message, $postid, $commentid);
      $payload = apply_filters('smpush_events_approvecomment_payload', $postid, $message, $commentid);
      
      $cronsetting = array();
      $post = get_post($postid);
      $cronsetting['desktop_title'] = $post->post_title;
      $cronsetting['desktop_link'] = get_permalink($postid);
      $post_thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($postid), 'medium');
      if(!empty($post_thumbnail)){
        $cronsetting['desktop_icon'] = esc_url($post_thumbnail[0]);
      }
      $cronsetting['post_id'] = $postid;
      $cronsetting['post_type'] = $post->post_type;
      $cronsetting = apply_filters('smpush_events_approvecomment_settings', $cronsetting, $message, $postid);

      $payload['post_id'] = $postid;
      $payload['post_type'] = $post->post_type;
      
      smpush_sendpush::SendCronPush(array(0=>$nowcomment->user_id), $message, $payload, 'userid', $cronsetting);
    }
    self::new_comment($nowcomment->comment_ID, $nowcomment);
  }

  public static function new_comment($commid, $nowcomment){
    global $wpdb;
    if($nowcomment->comment_approved != 0){
      if(self::$apisetting['e_usercomuser'] == 1){
        if($nowcomment->comment_parent > 0){
          $comment = $wpdb->get_row("SELECT comment_post_ID,user_id FROM ".$wpdb->prefix."comments WHERE comment_ID='".$nowcomment->comment_parent."' AND user_id>0", 'ARRAY_A');
          if(!$comment) return false;
          $commentcount = $wpdb->get_var("SELECT COUNT(comment_ID) AS commcount FROM ".$wpdb->prefix."comments WHERE comment_parent='".$nowcomment->comment_parent."' AND comment_approved='1'");
          if($commentcount>0 AND ($commentcount==1 OR $commentcount%5==0)){
            $subject = self::ShortString($nowcomment->comment_content, 200);
            $message = self::processNotifBody('e_usercomuser', $subject);
            $postid = $comment['comment_post_ID'];
            $commentid = $comment['comment_ID'];

            $message = apply_filters('smpush_events_user_reply_touser_message', $message, $postid, $commentid);
            $payload = apply_filters('smpush_events_user_reply_touser_payload', $postid, $message, $commentid);
            
            $cronsetting = array();
            $post = get_post($postid);
            $cronsetting['desktop_title'] = $post->post_title;
            $cronsetting['desktop_link'] = get_comment_link($commentid);
            $post_thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($postid), 'medium');
            if(!empty($post_thumbnail)){
              $cronsetting['desktop_icon'] = esc_url($post_thumbnail[0]);
            }
            $cronsetting = apply_filters('smpush_events_user_reply_touser_settings', $cronsetting, $message, $postid);
            $cronsetting['post_id'] = $postid;
            $cronsetting['post_type'] = $post->post_type;
            $payload['post_id'] = $postid;
            $payload['post_type'] = $post->post_type;

            smpush_sendpush::SendCronPush(array(0=>$comment['user_id']), $message, $payload, 'userid', $cronsetting);
          }
        }
      }
      if(self::$apisetting['e_newcomment'] == 1){
        $postid = $nowcomment->comment_post_ID;
        $commentid = $nowcomment->comment_ID;
        $commentcount = $wpdb->get_var("SELECT COUNT(comment_ID) AS commcount FROM ".$wpdb->prefix."comments WHERE comment_post_ID='$postid' AND comment_approved='1'");
        if($commentcount>0 AND ($commentcount==1 OR $commentcount%10==0)){
          $post = $wpdb->get_row("SELECT post_title,post_author,guid,post_type FROM ".$wpdb->prefix."posts WHERE ID='$postid'", 'ARRAY_A');
          $subject = self::ShortString($post['post_title'], 200);
          $message = self::processNotifBody('e_newcomment', $subject);

          $message = apply_filters('smpush_events_newcomment_message', $message, $postid, $commentid);
          $payload = apply_filters('smpush_events_newcomment_payload', $postid, $message, $commentid);
          
          $cronsetting = array();
          $cronsetting['desktop_title'] = $post['post_title'];
          $cronsetting['desktop_link'] = $post['guid'];
          $post_thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($postid), 'medium');
          if(!empty($post_thumbnail)){
            $cronsetting['desktop_icon'] = esc_url($post_thumbnail[0]);
          }
          $cronsetting = apply_filters('smpush_events_newcomment_settings', $cronsetting, $message, $postid);
          $cronsetting['post_id'] = $postid;
          $cronsetting['post_type'] = $post['post_type'];
          $payload['post_id'] = $postid;
          $payload['post_type'] = $post['post_type'];
          
          smpush_sendpush::SendCronPush(array(0=>$post['post_author']), $message, $payload, 'userid', $cronsetting);
        }
      }
      if(self::$apisetting['e_newcomment_allusers'] == 1){
        $postid = $nowcomment->comment_post_ID;
        $commentid = $nowcomment->comment_ID;
        $commentcount = $wpdb->get_var("SELECT COUNT(comment_ID) AS commcount FROM ".$wpdb->prefix."comments WHERE comment_post_ID='$postid' AND comment_ID<>$commentid AND comment_approved='1'");
        if($commentcount>0){
          $post = $wpdb->get_row("SELECT post_title,post_author,guid,post_type FROM ".$wpdb->prefix."posts WHERE ID='$postid'", 'ARRAY_A');
          $subject = self::ShortString($post['post_title'], 200);
          $message = self::processNotifBody('e_newcomment_allusers', $subject);
          $commentersIDs = self::AllUsersRelatedComment($postid, $commentid, $nowcomment->user_id);

          $message = apply_filters('smpush_events_newcomment_allusers_message', $message, $postid, $commentid);
          $payload = apply_filters('smpush_events_newcomment_allusers_payload', $postid, $message, $commentid);
          
          $cronsetting = array();
          $cronsetting['desktop_title'] = $post['post_title'];
          $cronsetting['desktop_link'] = $post['guid'];
          $post_thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($postid), 'medium');
          if(!empty($post_thumbnail)){
            $cronsetting['desktop_icon'] = esc_url($post_thumbnail[0]);
          }
          $cronsetting = apply_filters('smpush_events_newcomment_allusers_settings', $cronsetting, $message, $postid);
          $cronsetting['post_id'] = $postid;
          $cronsetting['post_type'] = $post['post_type'];
          $payload['post_id'] = $postid;
          $payload['post_type'] = $post['post_type'];
          
          smpush_sendpush::SendCronPush($commentersIDs, $message, $payload, 'userid', $cronsetting);
        }
      }
      if(self::$apisetting['e_newcomment_mentions'] == 1){
        $mentionIDs = array();
        preg_match_all('/\s?>?@([a-zA-Z0-9]+)\s?<?/', $nowcomment->comment_content, $matches);
        if(!empty($matches[1])){
          foreach($matches[1] as $username){
            $userinfo = get_user_by('login', $username);
            if(empty($userinfo)) continue;
            $mentionIDs[] = $userinfo->ID;
          }
          $postid = $nowcomment->comment_post_ID;
          $commentid = $nowcomment->comment_ID;
          $post = $wpdb->get_row("SELECT post_title,post_author,guid,post_type FROM ".$wpdb->prefix."posts WHERE ID='$postid'", 'ARRAY_A');
          $subject = self::ShortString($nowcomment->comment_content, 200);
          $message = self::processNotifBody('e_newcomment_mentions', $subject);

          $message = apply_filters('smpush_events_newcomment_mentions_message', $message, $postid, $commentid);
          $payload = apply_filters('smpush_events_newcomment_mentions_payload', $postid, $message, $commentid);

          $cronsetting = array();
          $cronsetting['desktop_title'] = $post['post_title'];
          $cronsetting['desktop_link'] = get_comment_link($commentid);
          $post_thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($postid), 'medium');
          if(!empty($post_thumbnail)){
            $cronsetting['desktop_icon'] = esc_url($post_thumbnail[0]);
          }
          $cronsetting = apply_filters('smpush_events_newcomment_mentions_settings', $cronsetting, $message, $postid);
          $cronsetting['post_id'] = $postid;
          $cronsetting['post_type'] = $post['post_type'];
          $payload['post_id'] = $postid;
          $payload['post_type'] = $post['post_type'];

          smpush_sendpush::SendCronPush($mentionIDs, $message, $payload, 'userid', $cronsetting);
        }
      }
    }
  }

  private static function UserRelatedComment($commid){
    global $wpdb;
    $userid = $wpdb->get_var("SELECT user_id FROM ".$wpdb->prefix."comments WHERE comment_ID='$commid'");
    if(!$userid) return false;
    return $userid;
  }
  
  private static function AllUsersRelatedComment($postid, $commentid, $user_id){
    global $wpdb;
    $userids = $wpdb->get_results("SELECT DISTINCT(user_id) FROM ".$wpdb->prefix."comments WHERE comment_post_ID='$postid' AND comment_ID<>$commentid AND user_id<>$user_id AND comment_approved='1'");
    if(!$userids) return false;
    else{
      $ids = array();
      foreach($userids as $userid){
        $ids[] = $userid->user_id;
      }
    }
    return $ids;
  }
  
  private static function PushUsersInPostCat($postid){
    global $wpdb;
    $ids = array();
    $channelids = array();
    $post_categories = wp_get_post_categories($postid);
    if(smpush_env == 'debug'){
      self::log('cats of channels vs cats: '.serialize($post_categories));
    }
    foreach($post_categories as $catobject){
      $category = get_category($catobject);
      $channelid = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."push_channels WHERE title LIKE '$category->name'");
      if($channelid){
        $channelids[] = $channelid;
      }
      if(smpush_env == 'debug'){
        self::log('search in channels vs cats: '.serialize($channelids));
      }
    }
    if(!empty($channelids)){
      $channelids = implode(',', $channelids);
      $tokenids = $wpdb->get_results("SELECT DISTINCT(token_id) FROM ".$wpdb->prefix."push_relation WHERE channel_id IN($channelids) AND connection_id='".self::$apisetting['def_connection']."'");
      if($tokenids){
        foreach($tokenids AS $tokenid){
          $ids[] = $tokenid->token_id;
        }
      }
      $tokenids = $wpdb->get_results("SELECT ".$wpdb->prefix."sm_push_tokens.id FROM ".$wpdb->prefix."push_relation
      INNER JOIN ".$wpdb->prefix."sm_push_tokens ON(".$wpdb->prefix."sm_push_tokens.userid=".$wpdb->prefix."push_relation.userid)
      WHERE ".$wpdb->prefix."push_relation.channel_id IN($channelids) AND ".$wpdb->prefix."push_relation.userid<>0");
      if($tokenids){
        foreach($tokenids AS $tokenid){
          $ids[] = $tokenid->id;
        }
      }
      if(empty($ids)){
        return false;
      }
      if(smpush_env == 'debug'){
        self::log('founds devices channels vs cats: '.serialize($ids));
      }
    }
    return $ids;
  }

  private static function AllPushUsers(){
    $ids = array();
    $authorids = self::$pushdb->get_results(self::parse_query("SELECT userid FROM {tbname} WHERE userid>0 AND {active_name}='1'"));
    if(!$authorids) return false;
    foreach($authorids AS $authorid){
      $ids[] = $authorid->userid;
    }
    return $ids;
  }

  private static function UsersRelatedPost($postid, $realtedType=false){
    global $wpdb;
    $ids = array();
    $post = $wpdb->get_row("SELECT post_author,post_type,post_parent,post_content FROM ".$wpdb->prefix."posts WHERE ID='$postid' AND post_status NOT IN(".self::$excludeStatus.") AND post_password=''");
    if(!$post) return false;
    if($realtedType === false || $realtedType == 'commenters'){
      if($post->post_type == 'reply'){
        $bbpost = get_post($post->post_parent);
        if($bbpost->post_author != $post->post_author){
          $ids[] = $bbpost->post_author;
        }
      }
      else{
        $ids[] = $post->post_author;
      }
    }
    if($realtedType == 'commenters'){
      if($post->post_type == 'reply'){
        $sql = "SELECT post_author AS user_id FROM ".$wpdb->prefix."posts WHERE post_parent='$post->post_parent' AND post_status NOT IN(".self::$excludeStatus.") AND post_type='reply' GROUP BY post_author";
      }
      else{
        $sql = "SELECT user_id FROM ".$wpdb->prefix."comments WHERE comment_post_ID='$postid' AND user_id>0 GROUP BY user_id";
      }
      $gets = $wpdb->get_results($sql, 'ARRAY_A');
      if($gets){
        foreach($gets AS $get){
          if($post->post_author == $get['user_id']) continue;
          $ids[] = $get['user_id'];
        }
      }
    }
    elseif($realtedType == 'mentions'){
      preg_match_all('/\s?>?@([a-zA-Z0-9]+)\s?<?/', $post->post_content, $matches);
      if(!empty($matches[1])){
        foreach($matches[1] as $username){
          $userinfo = get_user_by('login', $username);
          if(empty($userinfo)) continue;
          $ids[] = $userinfo->ID;
        }
      }
      self::$cronsettings['post_id'] = wp_get_post_parent_id($postid);
      self::$cronsettings['post_type'] = 'topic';
      self::$payloads['post_id'] = self::$cronsettings['post_id'];
      self::$payloads['post_type'] = 'topic';
    }
    elseif($realtedType == 'quoted'){
      $repliedto = get_post_meta($postid, '_bbp_reply_to');
      if(!empty($repliedto[0])){
        $userid = $wpdb->get_var("SELECT post_author FROM ".$wpdb->prefix."posts WHERE ID='$repliedto[0]' AND post_status NOT IN(".self::$excludeStatus.")");
        if(!empty($userid)){
          $ids[] = $userid;
        }
      }
      self::$cronsettings['post_id'] = wp_get_post_parent_id($postid);
      self::$cronsettings['post_type'] = 'topic';
      self::$payloads['post_id'] = self::$cronsettings['post_id'];
      self::$payloads['post_type'] = 'topic';
    }
    elseif($realtedType == 'tev_attendees'){
      $relAttendees = $wpdb->get_results("SELECT DISTINCT($wpdb->posts.post_author) FROM $wpdb->posts
       INNER JOIN $wpdb->postmeta ON($wpdb->postmeta.meta_value=$postid)
       WHERE $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status NOT IN(".self::$excludeStatus.")");
      if(!empty($relAttendees)){
        foreach($relAttendees as $relAttend){
          if($relAttend->post_author == $post->post_author) continue;
          $ids[] = $relAttend->post_author;
        }
      }
    }
    return $ids;
  }

  public static function buddy_activity($activity){
    if(self::$apisetting['bb_notify_activity'] == 0 || empty($activity->item_id)){
      return false;
    }
    global $wpdb;
    $bb_pages = get_option('bp-pages');
    $message = strip_tags($activity->action);

    $message = apply_filters('smpush_events_bb_message', $message, 'activity_'.$activity->id);
    $payload = apply_filters('smpush_events_bb_payload', 'activity_'.$activity->id, $message);
    $cronsetting = array();
    $cronsetting['name'] = 'BuddyPress Activity';
    $cronsetting['desktop_link'] = get_permalink($bb_pages['activity']);
    
    $user_id = array();
    if($activity->component == 'groups'){
      $cronsetting['desktop_title'] = $wpdb->get_var("SELECT name FROM ".$wpdb->prefix."bp_groups WHERE id='$activity->item_id'");
      $where = '';
      if(self::$apisetting['bb_notify_activity_admins_only'] == 1){
        $where = "AND is_admin='1'";
      }
      $admins = $wpdb->get_results("SELECT user_id FROM ".$wpdb->prefix."bp_groups_members WHERE group_id='$activity->item_id' AND is_confirmed='1' AND is_banned='0' AND user_id<>$activity->user_id $where");
      if($admins){
        foreach($admins as $admin){
          $user_id[] = $admin->user_id;
        }
      }
    }
    elseif($activity->type == 'activity_comment'){
      $commenters = $wpdb->get_results("SELECT user_id FROM ".$wpdb->prefix."bp_activity WHERE item_id='$activity->item_id' AND id<$activity->id AND user_id<>$activity->user_id");
      if($commenters){
        foreach($commenters as $commenter){
          $user_id[] = $commenter->user_id;
        }
      }
    }
    else{
      return false;
    }
    
    if(empty($user_id)){
      return false;
    }
    
    $cronsetting = apply_filters('smpush_events_bb_settings', $cronsetting, $message, 'activity_'.$activity->id);

    smpush_sendpush::SendCronPush($user_id, $message, $payload, 'userid', $cronsetting);
  }
  
  public static function buddy_notifications($notification){
    if(isset(self::$apisetting['bb_notify_'.$notification->component_name]) && self::$apisetting['bb_notify_'.$notification->component_name] == 0){
      return false;
    }
    $bp = buddypress();
    $bp->notifications = new stdClass();
    $bp->notifications->query_loop = new stdClass();
    $bp->notifications->query_loop->notification = $notification;
    $notification_desc = bp_get_the_notification_description();

    $dom = new DOMDocument;
    $dom->loadHTML('<meta http-equiv="content-type" content="text/html; charset=UTF-8">'.$notification_desc);
    $notification_link = $dom->getElementsByTagName('a');
    $notification_link = $notification_link[0]->getAttribute('href');
    $message = $dom->getElementsByTagName('a');
    $message = $message[0]->nodeValue;

    $message = apply_filters('smpush_events_bb_message', $message, $notification->id);
    $payload = apply_filters('smpush_events_bb_payload', $notification->id, $message);

    if($notification->component_name == 'friends'){
      $user_info = get_userdata($notification->item_id);
    }
    else{
      $user_info = get_userdata($notification->secondary_item_id);
    }

    $cronsetting = array();
    $cronsetting['name'] = 'BuddyPress';
    $cronsetting['desktop_title'] = (empty($user_info->display_name))? $user_info->user_nicename : $user_info->display_name;
    $cronsetting['desktop_link'] = $notification_link;
    $cronsetting = apply_filters('smpush_events_bb_settings', $cronsetting, $message, $notification->id);

    smpush_sendpush::SendCronPush(array(0 => $notification->user_id), $message, $payload, 'userid', $cronsetting);
  }
  
  public static function job_manager_alert($alert_id, $force = false) {
    if(isset(self::$apisetting['e_wpjobman_status']) && self::$apisetting['e_wpjobman_status'] == 0){
      return false;
    }
    $WP_Job_Notifier = new WP_Job_Manager_Alerts_Notifier();
    $alert = get_post($alert_id);
    if (!$alert || $alert->post_type !== 'job_alert') {
      return;
    }
    if ($alert->post_status !== 'publish' && !$force) {
      return;
    }

    $tokeninfo = self::$pushdb->get_var(self::parse_query("SELECT {token_name} FROM {tbname} WHERE userid='$alert->post_author' AND {active_name}='1'"));
    if(empty($tokeninfo)){
      return;
    }

    $user_info = get_user_by('id', $alert->post_author);
    $jobs = $WP_Job_Notifier->get_matching_jobs($alert, $force);
    if ($jobs->found_posts || !get_option('job_manager_alerts_matches_only')) {
      
      $message = str_replace(array('{counter}','{alert}'), array(count($jobs), $alert->post_title), self::$apisetting['e_wpjobman_body']);

      $message = apply_filters('smpush_events_wpjobman_message', $message, $alert_id);
      $payload = apply_filters('smpush_events_wpjobman_payload', $alert_id, $message);

      $cronsetting = array();
      $cronsetting['name'] = 'Job Mananger Alert';
      $cronsetting['desktop_title'] = $user_info->display_name;
      $cronsetting['desktop_link'] = get_bloginfo('wpurl').'/job-alerts/?action=view&alert_id='.$alert_id;
      $cronsetting = apply_filters('smpush_events_wpjobman_settings', $cronsetting, $message, $alert_id);
      $cronsetting['post_id'] = $alert_id;
      $cronsetting['email_wp_users'] = self::$emailWPUsers;
      $payload['module'] = 'job-alerts';
      
      smpush_sendpush::SendCronPush(array(0 => $alert->post_author), $message, $payload, 'userid', $cronsetting);
    }
  }
  
  public static function sendWooWaitingProduct($productid, $product){
    global $wpdb;
    $waitingList = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_notifier WHERE `type`='wooWaiting' AND object_id='$productid'", ARRAY_A);
    if($waitingList){
      $sendToUsers = $sendToDevices = array();
      $variables = array('{id}','{title}','{price}','{discount}','{description}','{stock}');
      $replace = array($productid, $product['post_title'], $product['_regular_price'], $product['_sale_price'], $product['content'], $product['_stock']);

      foreach($waitingList as $subscriber){
        $message = str_replace($variables, $replace, self::$apisetting['e_woo_waiting_message']);

        $message = apply_filters('smpush_events_woowaiting_message', $message, $productid);
        $payload = apply_filters('smpush_events_woowaiting_payload', $productid, $message);

        $cronsetting = array();
        $cronsetting['name'] = 'Send back in stock';
        $cronsetting['desktop_title'] = str_replace($variables, $replace, self::$apisetting['e_woo_waiting_title']);
        $cronsetting['desktop_link'] = get_permalink($productid);
        $cronsetting = apply_filters('smpush_events_woowaiting_settings', $cronsetting, $message, $productid);
        $cronsetting['post_id'] = $productid;
        $payload['module'] = 'product';

        if(!empty($subscriber['userid'])){
          $sendToUsers[] = $subscriber['userid'];
        }
        else{
          $sendToDevices[] = $subscriber['tokenid'];
        }

        $wpdb->delete($wpdb->prefix.'push_notifier', array('id' => $subscriber['id']));
      }
      if(!empty($sendToUsers)){
        smpush_sendpush::SendCronPush($sendToUsers, $message, $payload, 'userid', $cronsetting);
      }
      elseif(!empty($sendToDevices)){
        smpush_sendpush::SendCronPush($sendToDevices, $message, $payload, 'tokenid', $cronsetting);
      }
    }
  }

  public static function meta_box_design($post){
    global $wpdb;
    $channels = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'push_channels ORDER BY title ASC');
    include(smpush_dir.'/pages/meta_box.php');
  }

  public static function build_meta_box(){
    add_meta_box('smpush-meta-box', 'Smart Notification', array('smpush_events', 'meta_box_design'), null, 'side', 'high');
  }
  
}