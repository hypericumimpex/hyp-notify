<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class smpush_peepso_events extends smpush_controller{
  private static $note_data;

  public function __construct(){
    parent::__construct();
  }

  public static function peepso_notification($notification){
    if(self::$apisetting['peepso_notifications'] == 0){
      return $notification;
    }

    self::$note_data = $notification;

    if(self::$note_data['not_external_id'] > 0){
      self::$note_data['post_title'] = get_the_title(self::$note_data['not_external_id']);
    }
    $PeepSoUser		= PeepSoUser::get_instance(self::$note_data['not_from_user_id']);
    $notification_args = self::notification_link(0);

    self::log($PeepSoUser->get_firstname().' '.self::$note_data['not_message'].$notification_args['message']);
    self::log($notification_args['link']);

    $message = strip_tags($PeepSoUser->get_firstname().' '.self::$note_data['not_message'].$notification_args['message']);

    $message = apply_filters('smpush_events_peepso_message', $message, self::$note_data);
    $payload = apply_filters('smpush_events_peepso_payload', self::$note_data, $message);

    $cronsetting = array();
    $cronsetting['name'] = 'PeepSo Notification';
    $cronsetting['desktop_title'] = $PeepSoUser->get_firstname();
    $cronsetting['desktop_link'] = $notification_args['link'];
    $cronsetting = apply_filters('smpush_events_peepso_settings', $cronsetting, $message, self::$note_data);
    $payload['module'] = 'peepso';

    smpush_sendpush::SendCronPush(array(0 => self::$note_data['not_external_id']), $message, $payload, 'userid', $cronsetting);

    return $notification;
  }

  private static function notification_link($echo = 1){
    //copied from peepso-core/classes/profile.php
    $link = PeepSo::get_page('activity_status') . self::$note_data['post_title'] . '/';
    $link = apply_filters('peepso_profile_notification_link', $link, self::$note_data);

    $is_a_comment = 0;
    if ('user_comment' === self::$note_data['not_type']) {
      $is_a_comment = 1;
    }

    if ('like_post' == self::$note_data['not_type']) {
      global $wpdb;
      $sql = 'SELECT COUNT(id) as `is_comment_like` FROM `' . $wpdb->prefix . 'posts` WHERE `post_type`=\'peepso-comment\' AND ID=' . self::$note_data['not_external_id'];
      $res = $wpdb->get_row($sql);

      $is_a_comment = $res->is_comment_like;
    }

    $print_link = '';
    $activity_type = array(
      'type' => 'post',
      'text' => __('post', 'peepso-core')
    );

    if ('stream_reply_comment' === self::$note_data['not_type']) {

      $activities = PeepSoActivity::get_instance();

      $not_activity = $activities->get_activity_data(self::$note_data['not_external_id'], self::$note_data['not_module_id']);
      $comment_activity = $activities->get_activity_data($not_activity->act_comment_object_id, $not_activity->act_comment_module_id);
      $post_activity = $activities->get_activity_data($comment_activity->act_comment_object_id, $comment_activity->act_comment_module_id);

      if (is_object($comment_activity) && is_object($post_activity)) {
        $parent_comment = $activities->get_activity_post($comment_activity->act_id);
        $parent_post = $activities->get_activity_post($post_activity->act_id);
        $parent_id = $parent_comment->act_external_id;

        $post_link = PeepSo::get_page('activity_status') . $parent_post->post_title . '/';
        $comment_link = $post_link . '?t=' . time() . '#comment.' . $post_activity->act_id . '.' . $parent_comment->ID . '.' . $comment_activity->act_id . '.' . $not_activity->act_external_id;

        if( 0 === intval($echo) ) {
          $hyperlink = $comment_link;
        }

        ob_start();

        echo ' ';
        $post_content = __('a comment', 'peepso-core');

        if (intval($parent_comment->post_author) === get_current_user_id()) {
          $post_content =  (self::$note_data['not_message'] != __('replied to', 'peepso-core')) ? __('on ', 'peepso-core') : '';
          $post_content .= __('your comment', 'peepso-core');
        }

        echo $post_content;

        $print_link = ob_get_clean();
      }

    } else if ('profile_like' === self::$note_data['not_type']) {

      $author = PeepSoUser::get_instance(self::$note_data['not_from_user_id']);

      $link = $author->get_profileurl();

      if( 0 === intval($echo) ) {
        $hyperlink = $link;
      }

    } else if (1 == $is_a_comment) {

      $activities = PeepSoActivity::get_instance();

      $not_activity = $activities->get_activity_data(self::$note_data['not_external_id'], self::$note_data['not_module_id']);

      $parent_activity = $activities->get_activity_data($not_activity->act_comment_object_id, $not_activity->act_comment_module_id);
      if (is_object($parent_activity)) {
        $not_post = $activities->get_activity_post($not_activity->act_id);
        $parent_post = $activities->get_activity_post($parent_activity->act_id);
        $parent_id = $parent_post->act_external_id;

        // modify the type of post (eg. post, photo, video, avatar, cover);
        $activity_type = apply_filters('peepso_notifications_activity_type', $activity_type, $parent_id, NULL);

        // check if parent post is a comment
        if($parent_post->post_type == 'peepso-comment') {
          $comment_activity = $activities->get_activity_data($not_activity->act_comment_object_id, $not_activity->act_comment_module_id);
          $post_activity = $activities->get_activity_data($comment_activity->act_comment_object_id, $comment_activity->act_comment_module_id);

          $parent_post = $activities->get_activity_post($post_activity->act_id);
          $parent_comment = $activities->get_activity_post($comment_activity->act_id);

          $parent_link = PeepSo::get_page('activity_status') . $parent_post->post_title . '/?t=' . time() . '#comment.' . $post_activity->act_id . '.' . $parent_comment->ID . '.' . $comment_activity->act_id . '.' . $not_activity->act_external_id;
        } else {
          $parent_link = PeepSo::get_page('activity_status') .  $parent_post->post_title . '/#comment.' . $parent_activity->act_id . '.' . $not_post->ID . '.' . $not_activity->act_external_id;
        }

        if( 0 === intval($echo) ) {
          $hyperlink = $parent_link;
        }

        ob_start();
        $post_content = '';
        $on = '';
        if($activity_type['type'] == 'post') {
          $on = ' ' . __('on', 'peepso-core');
          $post_content = sprintf(__('a %s', 'peepso-core'), $activity_type['text']);
        }

        /* todo : add some filter for handling notification type cover/avatar*/
        if (intval($parent_post->post_author) === get_current_user_id() || (intval($parent_post->post_author) === get_current_user_id() && in_array($activity_type['type'], array('cover','avatar')))) {
          $on = ' ' . __('on', 'peepso-core');
          $post_content = sprintf(__('your %s', 'peepso-core'), $activity_type['text']);
        }

        if(in_array($activity_type['type'], array('cover','avatar')) && (intval($parent_post->post_author) !== get_current_user_id()))
        {
          $on = ' ' . __('on', 'peepso-core');
          if(preg_match('/^[aeiou]/i', strtolower($activity_type['text']))) {
            $post_content = sprintf(__('an %s', 'peepso-core'), $activity_type['text']);
          } else {
            $post_content = sprintf(__('a %s', 'peepso-core'), $activity_type['text']);
          }
        }

        echo $on, ' ';

        echo $post_content;

        $print_link = ob_get_clean();


      }
    } else {

      if( 0 === intval($echo) ) {
        $hyperlink = $link;
      }

      if ('share' === self::$note_data['not_type']) {

        $activities = PeepSoActivity::get_instance();
        $repost = $activities->get_activity_data(self::$note_data['not_external_id'], self::$note_data['not_module_id']);
        $orig_post = $activities->get_activity_post($repost->act_repost_id);

        // modify the type of post (eg. post, photo, video, avatar, cover);
        $activity_type = apply_filters('peepso_notifications_activity_type', $activity_type, $orig_post->ID, NULL);

        ob_start();
        echo ' ' , sprintf(__('your %s', 'peepso-core'), $activity_type['text']);

        $print_link = ob_get_clean();
      }
    }

    $print_link = apply_filters('peepso_modify_link_item_notification', array($print_link, $link), self::$note_data);

    if(is_array($print_link)) {
      return ['message' => $print_link[0], 'link' => $hyperlink];
    } else {
      return ['message' => $print_link, 'link' => $hyperlink];
    }
  }

}