<div class="wrap">
   <div id="smpush-icon-chanmanage" class="icon32"><br></div>
   <h2><?php echo __('Migrate From OneSignal', 'smpush-plugin-lang')?></h2>
   <div id="col-container">
     <div id="col-right" class="smpushFormAjaxResult" style="width: 100%">
       <form action="<?php echo $pageurl;?>" method="post" class="validate">
           <div id="post-body" class="metabox-holder columns-2">
              <div id="post-body-content" class="edit-form-section">
                 <div id="namediv" class="stuffbox">
                    <h3><label><?php echo __('Generating CSV subscribers link', 'smpush-plugin-lang');?></label></h3>
                    <div class="inside">
                       <table class="form-table">
                        <tbody>
                          <tr valign="top" class="form-required">
                             <td class="first"><?php echo __('App ID', 'smpush-plugin-lang')?></td>
                             <td>
                              <input name="appid" type="text" size="60">
                             </td>
                          </tr>
                          <tr valign="top" class="form-required">
                             <td class="first"><?php echo __('Secret Key', 'smpush-plugin-lang')?></td>
                             <td>
                              <input name="seckey" type="text" size="60">
                               <p class="description"><?php echo __('For how to get this stuff', 'smpush-plugin-lang')?> <a href="https://documentation.onesignal.com/docs/accounts-and-keys#section-keys-ids" target="_blank"><?php echo __('click here', 'smpush-plugin-lang')?></a></p>
                             </td>
                          </tr>
                          <tr valign="top">
                            <td colspan="2"><input type="submit" name="submit" class="button button-primary" style="width: 120px;" value="<?php echo __('Generate Link', 'smpush-plugin-lang')?>"></td>
                         </tr>
                        </tbody>
                      </table>
                    </div>
                 </div>
              </div>
           </div>
        </form>
     </div>
     <div id="col-right" class="smpushFormAjaxResult" style="width: 100%">
       <form action="<?php echo $pageurl;?>" method="post" class="validate">
         <div id="post-body" class="metabox-holder columns-2">
           <div id="post-body-content" class="edit-form-section">
             <div id="namediv" class="stuffbox">
               <h3><label><?php echo __('Import OneSignal CSV subscribers', 'smpush-plugin-lang');?></label></h3>
               <div class="inside">
                 <table class="form-table">
                   <tbody>
                   <tr valign="top" class="form-required">
                     <td class="first"><?php echo __('OneSignal CSV Link', 'smpush-plugin-lang')?></td>
                     <td>
                       <input name="csvlink" type="link" size="60">
                       <p class="description"><code><?php echo __('Note that OneSignal takes some time to proccess your CSV file after generating your CSV link.', 'smpush-plugin-lang')?></code></p>
                       <p class="description"><code><?php echo __('You need to get your app VAPID keys from OneSignal and update them in the plugin settings page.', 'smpush-plugin-lang')?></code></p>
                       <p class="description"><code><?php echo __('Do not forget to enable OneSignal payload compliant option in the plugin settings page.', 'smpush-plugin-lang')?></code></p>
                     </td>
                   </tr>
                   <tr valign="top">
                     <td class="first"><?php echo __('Prevent Duplicates', 'smpush-plugin-lang')?></td>
                     <td>
                       <label><input name="no_duplicates" type="checkbox"> <?php echo __('Verify each subscriber if it is already exist or not [will take more time and needs more CPU]', 'smpush-plugin-lang')?></label>
                     </td>
                   </tr>
                   <tr valign="top">
                     <td colspan="2"><input type="submit" name="submit" class="button button-primary" style="width: 140px;" value="<?php echo __('Import Subscribers', 'smpush-plugin-lang')?>"></td>
                   </tr>
                   </tbody>
                 </table>
               </div>
             </div>
           </div>
         </div>
       </form>
     </div>
   </div>
</div>
<script type="text/javascript">
var smpush_pageurl = '<?php echo $pageurl;?>';
jQuery(document).ready(function() {
});
</script>