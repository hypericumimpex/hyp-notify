<<?php echo (empty($instance['container'])) ? 'div' : $instance['container'];?> class="smpush_desktop_widget widget <?php echo $instance['container_class'];?>">
  <<?php echo (empty($instance['head'])) ? 'h2' : $instance['head'];?> class="widget-title <?php echo $instance['head_class'];?>"><?php echo $instance['head_title'];?></<?php echo (empty($instance['head'])) ? 'h2' : $instance['head'];?>>
  <p><?php echo $instance['message'];?></p>
  <?php if($instance['show_channels'] == 1 && is_user_logged_in()):?>
  <ul>
    <?php foreach ($channels as $channel): ?>
    <li><label><input value="<?php echo $channel->id; ?>" type="checkbox" class="smpush_desktop_channels_subs" <?php if(in_array($channel->id, $subschannels)):?>checked="checked"<?php endif;?>> <?php echo $channel->title; ?></label></li>
    <?php endforeach; ?>
  </ul>
  <?php if($instance['gdpr_ver_option'] == 1):?>
  <p><?php echo $instance['gdpr_ver_text_processed']?></p>
  <?php endif;?>
  <?php if($enableSaveChannelBTN):?>
  <button class="smpush-push-subscriptions-button"><?php echo $instance['save_channels_btn']?></button>
  <?php endif;?>
  <?php endif;?>
  <button class="smpush-push-permission-button" disabled><?php echo $settings['desktop_btn_subs_text']?></button>
</<?php echo (empty($instance['container'])) ? 'div' : $instance['container'];?>>
<?php if(!empty($instance['custom_css'])):?>
<style><?php echo $instance['custom_css']?></style>
<?php endif; ?>