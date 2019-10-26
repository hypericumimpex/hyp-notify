<form action="" id="smpush_subscription_form" method="post" style="display:none">
<div id="smpush_notification_header">
  
  <div id="smpush_head_title">
    <?php echo __('Choose certain keywords, subscribe in interested channels and set categories to receive instant notifications about any new updates match your choices.', 'smpush-plugin-lang')?>
  </div>
  
  <?php if(self::$apisetting['subspage_keywords'] == 1):?>
  <h3><?php echo __('Keywords', 'smpush-plugin-lang')?> <i title="<?php echo __('Updates must contains one of your keywords to reach you', 'smpush-plugin-lang')?>"></i></h3>
  <input type="text" id="smpush_keywords" name="keywords" value="<?php echo $subscription['keywords']?>" />
  <?php endif;?>
  
  <?php if(self::$apisetting['subspage_cats_status'] == 1 && self::$apisetting['subspage_show_catimages'] == 0):?>
  <h3><?php echo __('Categories', 'smpush-plugin-lang')?></h3>
  <select id="smpush_categories" name="categories[]" multiple>
    <?php foreach($subscription['categories'] as $category):?>
    <option value="<?php echo $category['id']?>" <?php if($category['selected'] == 1):?>selected="selected"<?php endif;?>><?php echo $category['name']?></option>
    <?php endforeach;?>
  </select>
  <?php endif;?>
  
  <?php if(self::$apisetting['subspage_geo_status'] == 1):?>
  <h3><?php echo __('Location', 'smpush-plugin-lang')?> <i title="<?php echo __('Draw a circle area to send you notifications about places inside this area only', 'smpush-plugin-lang')?>"></i></h3>
  
  <div id="smio-gmap-container">
    <div id="smio_gmap_search">
      <input type="hidden" name="latitude" id="smio_latitude" value="<?php echo $subscription['latitude']?>" />
      <input type="hidden" name="longitude" id="smio_longitude" value="<?php echo $subscription['longitude']?>" />
      <input id="smio_gmap_address" class="smio_gmap_input" type="text" placeholder="<?php echo __('Put the search address then press Enter...', 'smpush-plugin-lang')?>" />
      <input name="radius" id="smio_gmap_radius" value="<?php echo $subscription['radius']?>" class="smio_gmap_input" type="number" step="1" placeholder="<?php echo __('Radius in miles', 'smpush-plugin-lang')?>" style="width:150px" />
   </div>
    <div id="smio-gmap"></div>
  </div>
  <?php endif;?>
  
  <?php if(self::$apisetting['subspage_channels'] == 1):?>
  <h3><?php echo __('Channels', 'smpush-plugin-lang')?></h3>
  <select id="smpush_channels" name="channels[]" multiple>
    <?php foreach($subscription['channels'] as $channel):?>
    <option value="<?php echo $channel['id']?>" <?php if($channel['subscribed'] == 'yes'):?>selected="selected"<?php endif;?>><?php echo $channel['title']?></option>
    <?php endforeach;?>
  </select>
  <?php endif;?>
  
  <h3><?php echo __('Receive On', 'smpush-plugin-lang')?></h3>
  <div id="smpush_receive_on">
    <?php if(self::$apisetting['subspage_plat_web'] == 1):?>
    <button type="button"><img src="<?php echo smpush_imgpath; ?>/chrome.png" /> <span><?php echo __('Web Push Notification', 'smpush-plugin-lang')?></span> <label><input type="checkbox" name="web" <?php if($subscription['web'] == 1):?>checked="checked"<?php endif;?> /></label></button>
    <?php endif;?>
    <?php if(self::$apisetting['subspage_plat_mobile'] == 1):?>
    <button type="button"><img src="<?php echo smpush_imgpath; ?>/android.png" /> <span><?php echo __('Mobile Push Notification', 'smpush-plugin-lang')?></span> <label><input type="checkbox" name="mobile" <?php if($subscription['mobile'] == 1):?>checked="checked"<?php endif;?> /></label></button>
    <?php endif;?>
    <?php if(self::$apisetting['subspage_plat_msn'] == 1):?>
    <button type="button"><img src="<?php echo smpush_imgpath; ?>/messenger_w.png" width="24px" /> <span><?php echo __('Facebook Messenger', 'smpush-plugin-lang')?></span> <label><input type="checkbox" name="msn" <?php if($subscription['msn'] == 1):?>checked="checked"<?php endif;?> /></label></button>
    <?php endif;?>
    <?php if(self::$apisetting['subspage_plat_email'] == 1):?>
    <button type="button"><img src="<?php echo smpush_imgpath; ?>/email.png" /> <span><?php echo __('Email', 'smpush-plugin-lang')?></span> <label><input type="checkbox" name="email" <?php if($subscription['email'] == 1):?>checked="checked"<?php endif;?> /></label></button>
    <?php endif;?>
  </div>
  
  <p id="smpush_web_instructions" class="smpush_install_instructions">
    <?php echo __('receive push notification messages on your web browser instantly while browsing', 'smpush-plugin-lang')?><br />
    <?php echo __('do not waste your time opening emails or sms messages', 'smpush-plugin-lang')?><br />
    <?php echo __('To subscribe click on', 'smpush-plugin-lang')?>
    <button class="smpush-push-permission-button" type="button"><?php echo __('subscribe', 'smpush-plugin-lang')?></button>
    <?php echo __('you will see permissions box click on Allow button so give us permissions to send you notification messages.', 'smpush-plugin-lang')?>
  </p>
  
  <p id="smpush_mobile_instructions" class="smpush_install_instructions">
    <?php echo __('install our official mobile app and receive push notification messages on your mobile instantly', 'smpush-plugin-lang')?><br />
    <a href="<?php echo self::$apisetting['subspage_applink_play']?>" target="_blank"><img src="<?php echo smpush_imgpath; ?>/google_play.png" /></a>
    <a href="<?php echo self::$apisetting['subspage_applink_ios']?>" target="_blank"><img src="<?php echo smpush_imgpath; ?>/appstore.png" /></a>
    <a href="<?php echo self::$apisetting['subspage_applink_wp']?>" target="_blank"><img src="<?php echo smpush_imgpath; ?>/windows_store.png" /></a>
  </p>
  
  <div id="smpush_msn_instructions" class="smpush_install_instructions">
    <?php echo __('Awesome! Give us permission to send you the notification messages to your Messenger directly. Just click on the below button!', 'smpush-plugin-lang')?><br />
    <div style="margin-top:18px" class="fb-send-to-messenger" 
      messenger_app_id="<?php echo self::$apisetting['msn_appid']?>" 
      page_id="<?php echo self::$apisetting['msn_official_fbpage_id']?>" 
      data-ref="subscribed_<?php echo get_current_user_id() ?>" color="white" cta_text="KEEP_ME_UPDATED" size="xlarge"></div>
  </div>
  
</div>

<?php if(self::$apisetting['subspage_cats_status'] == 1 && self::$apisetting['subspage_show_catimages'] == 1):?>
<select id="smpush_categories" name="categories[]" multiple style="display:none">
  <?php foreach($subscription['categories'] as $category):?>
  <option value="<?php echo $category['id']?>" <?php if($category['selected'] == 1):?>selected="selected"<?php endif;?>><?php echo $category['name']?></option>
  <?php endforeach;?>
</select>

<div id="smpush_notification_footer">
  <h2><?php echo __('Subscribe In Categories', 'smpush-plugin-lang')?></h2>
  <?php foreach($subscription['categories'] as $category):?>
  <div class="smpushBox smpushSubsCat  <?php if($category['selected'] == 1):?>smpushBoxSelected<?php endif;?>" data-id="<?php echo $category['id']?>">
    <strong><?php echo $category['name']?></strong>
    <?php if(!empty($category['image'])):?><img src="<?php echo $category['image']; ?>" /><?php endif;?>
    <button type="button" class="smpushBox_sbtn"><span><?php echo __('Subscribe', 'smpush-plugin-lang')?></span></button>
    <button type="button" class="smpushBox_unsbtn"><img src="<?php echo smpush_imgpath; ?>/checked.png" /> <span><?php echo __('Subscribed', 'smpush-plugin-lang')?></span></button>
  </div>
  <?php endforeach;?>
</div>
<?php endif;?>

<?php if(self::$apisetting['gdpr_subs_btn'] == 1):?>
<p id="smpush_agreement_hint"><label><input type="checkbox" required /><?php echo self::$apisetting['gdpr_ver_text_processed']?></label></p>
<?php endif;?>

<div id="smpush_div_button" <?php if(self::$apisetting['gdpr_subs_btn'] == 1):?>class="smpush_div_button_gdpr_enabled"<?php endif;?>>
<button type="submit" id="smpush_save_button"><?php echo __('Save My Subscription', 'smpush-plugin-lang')?></button>
  <?php if(self::$apisetting['gdpr_subs_btn'] == 1):?>
<button type="button" id="smpush_delete_button"><?php echo __('Permanently Delete My Subscription', 'smpush-plugin-lang')?></button>
  <?php endif;?>
</div>

</form>

<script type="text/javascript">
window.onload = function() {
  jQuery('#smpush_channels').selectize({
    plugins: ['remove_button'],
  });
  
  <?php if(self::$apisetting['subspage_cats_status'] == 1 && self::$apisetting['subspage_show_catimages'] == 0):?>
  jQuery('#smpush_categories').selectize({
    plugins: ['remove_button'],
  });
  <?php endif;?>
  
  jQuery('#smpush_notification_header button').has('input[name="web"]').click(function() {
    var pushSupported = smpush_test_browser();
    if(! pushSupported){
      alert("Sorry your browser does not support web push notification");
      jQuery(this).find('input[name="web"]').removeAttr("checked");
      return;
    }
    jQuery(".smpush_install_instructions").hide();
    jQuery("#smpush_web_instructions").show();
    jQuery(this).find('input[name="web"]').trigger("click");
  });
  jQuery('#smpush_notification_header button').has('input[name="mobile"]').click(function() {
    jQuery(".smpush_install_instructions").hide();
    jQuery("#smpush_mobile_instructions").show();
    jQuery(this).find('input[name="mobile"]').trigger("click");
  });
  jQuery('#smpush_notification_header button').has('input[name="msn"]').click(function() {
    jQuery(".smpush_install_instructions").hide();
    jQuery("#smpush_msn_instructions").show();
    jQuery(this).find('input[name="msn"]').trigger("click");
  });
  jQuery('#smpush_notification_header button').has('input[name="email"]').click(function() {
    jQuery(".smpush_install_instructions").hide();
    jQuery(this).find('input[name="email"]').trigger("click");
  });
  jQuery("#smpush_web_instructions").show();
  
  jQuery('.smpushSubsCat').click(function() {
    var catid = jQuery(this).attr("data-id");
    if(jQuery(this).hasClass("smpushBoxSelected")){
      jQuery(this).removeClass("smpushBoxSelected");
      jQuery('#smpush_categories option[value="'+catid+'"]').removeAttr('selected');
    }
    else{
      jQuery(this).addClass("smpushBoxSelected");
      jQuery('#smpush_categories option[value="'+catid+'"]').attr('selected', 'selected');
    }
  });

  jQuery('#smpush_subscription_form').submit(function(e) {
    e.preventDefault();
    var oldtext = jQuery("#smpush_save_button").html();
    jQuery("#smpush_save_button").attr("disabled", "disabled");
    jQuery("#smpush_save_button").html("<?php echo __('Saving...', 'smpush-plugin-lang')?>");
    jQuery.post("<?php echo get_bloginfo('wpurl')?>/?smpushcontrol=save_subscription&noheader=1", jQuery("#smpush_subscription_form").serialize(), function( data ) {
      jQuery("#smpush_save_button").removeAttr("disabled");
      jQuery("#smpush_save_button").html("<?php echo __('Saved Successfully', 'smpush-plugin-lang')?>");
      setTimeout(function(){ jQuery("#smpush_save_button").html(oldtext); }, 3000);
    }).fail(function() {
      jQuery('#smpush_subscription_form').submit();
    });

    var catid = jQuery(this).attr("data-id");
    if(jQuery(this).hasClass("smpushBoxSelected")){
      jQuery(this).removeClass("smpushBoxSelected");
      jQuery('#smpush_categories option[value="'+catid+'"]').removeAttr('selected');
    }
    else{
      jQuery(this).addClass("smpushBoxSelected");
      jQuery('#smpush_categories option[value="'+catid+'"]').attr('selected', 'selected');
    }
  });
  
  jQuery('#smpush_delete_button').click(function(e) {
    if(confirm("<?php echo __('Are you sure you want to terminate your subscription permanently ?', 'smpush-plugin-lang')?>")){
      var oldtext = jQuery("#smpush_delete_button").html();
      jQuery("#smpush_delete_button").attr("disabled", "disabled");
      jQuery("#smpush_delete_button").html("<?php echo __('Processing...', 'smpush-plugin-lang')?>");
      jQuery.post("<?php echo get_bloginfo('wpurl')?>/?smpushcontrol=deletetoken&noheader=1", {"source":"delete_button"}, function( data ) {
        jQuery("#smpush_delete_button").removeAttr("disabled");
        jQuery("#smpush_delete_button").html(oldtext);
        location.reload();
      }).fail(function() {
        jQuery("#smpush_delete_button").html("<?php echo __('Error!', 'smpush-plugin-lang')?>");
      });
    }
  });
  
  jQuery('#smpush_keywords').selectize({
    plugins: ['remove_button'],
    persist: false,
    delimiter: ',',
    create: true,
    render: {
        item: function(data, escape) {
            return '<div>' + escape(data.text) + '</div>';
        }
    },
  });

};
</script>