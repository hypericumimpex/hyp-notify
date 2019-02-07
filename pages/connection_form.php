<form action="<?php echo $pageurl;?>&noheader=1" method="post" id="smpush_jform" class="validate">
<input type="hidden" name="id" value="<?php echo $connection['id'];?>">
<input type="hidden" name="type" value="<?php echo $connection['dbtype'];?>">
   <div id="post-body" class="metabox-holder columns-2">
      <div id="post-body-content" class="edit-form-section">
         <div id="namediv" class="stuffbox">
            <h3><label><?php echo (empty($connection['title']))? __('Add New Connection', 'smpush-plugin-lang') : $connection['title'];?></label></h3>
            <div class="inside">
               <table class="form-table">
                <tbody>
                  <tr valign="top">
                     <td class="first"><?php echo __('Title', 'smpush-plugin-lang')?></td>
                     <td><input name="title" type="text" size="40" value="<?php echo $connection['title'];?>" aria-required="true"></td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Description', 'smpush-plugin-lang')?></td>
                     <td><textarea name="description" rows="5" cols="40"><?php echo $connection['description'];?></textarea></td>
                  </tr>
                  <?php if($connection['dbtype'] == ''){?>
                  <tr valign="top">
                     <td class="first"><?php echo __('Connection Type', 'smpush-plugin-lang')?></td>
                     <td>
                     <select name="type" onchange="if(this.value=='remote'){jQuery('.smpush_select_conn').show()}else{jQuery('.smpush_select_conn').hide()}">
                        <option value="localhost"><?php echo __('Wordpress Database', 'smpush-plugin-lang')?></option>
                        <option value="remote"><?php echo __('Remote Connection', 'smpush-plugin-lang')?></option>
                     </select>
                     </td>
                  </tr>
                  <?php }$style = ($connection['dbtype'] == 'remote')?'':'style="display:none;"';?>
                  <tr valign="top" class="smpush_select_conn" <?php echo $style;?>>
                     <td class="first"><?php echo __('DB Host', 'smpush-plugin-lang')?></td>
                     <td class="form-required"><input name="dbhost" value="<?php echo $connection['dbhost'];?>" type="text" size="40" aria-required="true"></td>
                  </tr>
                  <tr valign="top" class="smpush_select_conn" <?php echo $style;?>>
                     <td class="first"><?php echo __('DB Name', 'smpush-plugin-lang')?></td>
                     <td class="form-required"><input name="dbname" value="<?php echo $connection['dbname'];?>" type="text" size="40" aria-required="true"></td>
                  </tr>
                  <tr valign="top" class="smpush_select_conn" <?php echo $style;?>>
                     <td class="first"><?php echo __('DB Username', 'smpush-plugin-lang')?></td>
                     <td class="form-required"><input name="dbuser" value="<?php echo $connection['dbuser'];?>" type="text" size="40" aria-required="true"></td>
                  </tr>
                  <tr valign="top" class="smpush_select_conn" <?php echo $style;?>>
                     <td class="first"><?php echo __('DB Password', 'smpush-plugin-lang')?></td>
                     <td><input name="dbpass" value="<?php echo $connection['dbpass'];?>" type="password" size="40"></td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Table Name', 'smpush-plugin-lang')?></td>
                     <td class="form-required">
                     <input name="tbname" value="<?php echo $connection['tbname'];?>" type="text" size="40" aria-required="true">
                     <p class="description"><?php echo __('Use', 'smpush-plugin-lang')?> {wp_prefix} <?php echo __('for the Wordpress prefix table value', 'smpush-plugin-lang')?></p>
                	 </td>
                  </tr>
                  <tr valign="top"><th colspan="2"><?php echo __('Column Names', 'smpush-plugin-lang')?></th></tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('ID', 'smpush-plugin-lang')?></td>
                     <td class="form-required">
                     <input name="id_name" value="<?php echo $connection['id_name'];?>" type="text" size="40" aria-required="true">
                     <p class="description"><?php echo __('The table primary key name', 'smpush-plugin-lang')?></p>
                	 </td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Device Token', 'smpush-plugin-lang')?></td>
                     <td class="form-required">
                     <input name="token_name" value="<?php echo $connection['token_name'];?>" type="text" size="40" aria-required="true">
                     <p class="description"><?php echo __('Table column that stores the devices token value', 'smpush-plugin-lang')?></p>
                	 </td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('MD5 Device Token', 'smpush-plugin-lang')?></td>
                     <td class="form-required">
                     <input name="md5token_name" value="<?php echo $connection['md5token_name'];?>" type="text" size="40" aria-required="true">
                     <p class="description"><?php echo __('Table column that stores the devices token value  as MD5 string for optimized queries later', 'smpush-plugin-lang')?></p>
                	 </td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Active', 'smpush-plugin-lang')?><br />(<?php echo __('optional', 'smpush-plugin-lang')?>)</td>
                     <td><input name="active_name" value="<?php echo $connection['active_name'];?>" type="text" size="40">
                     <p class="description"><?php echo __('Name of the table column that stores the devices status 1 or 0', 'smpush-plugin-lang')?></p></td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Latitude', 'smpush-plugin-lang')?><br />(<?php echo __('optional', 'smpush-plugin-lang')?>)</td>
                     <td><input name="latitude_name" value="<?php echo $connection['latitude_name'];?>" type="text" size="40">
                     <p class="description"><?php echo __('Name of the table column that stores the devices GPS location latitude point', 'smpush-plugin-lang')?></p></td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Longitude', 'smpush-plugin-lang')?><br />(<?php echo __('optional', 'smpush-plugin-lang')?>)</td>
                     <td><input name="longitude_name" value="<?php echo $connection['longitude_name'];?>" type="text" size="40">
                     <p class="description"><?php echo __('Name of the table column that stores the devices GPS location longitude point', 'smpush-plugin-lang')?></p></td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('GPS update time', 'smpush-plugin-lang')?><br />(<?php echo __('optional', 'smpush-plugin-lang')?>)</td>
                     <td><input name="gpstime_name" value="<?php echo $connection['gpstime_name'];?>" type="text" size="40">
                     <p class="description"><?php echo __('Name of the table column that stores GPS update time', 'smpush-plugin-lang')?></p></td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('GPS timeout', 'smpush-plugin-lang')?><br />(<?php echo __('optional', 'smpush-plugin-lang')?>)</td>
                     <td><input name="geotimeout_name" value="<?php echo $connection['geotimeout_name'];?>" type="text" size="40">
                     <p class="description"><?php echo __('Name of the table column that stores GPS timeout', 'smpush-plugin-lang')?></p></td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Information', 'smpush-plugin-lang')?><br />(<?php echo __('optional', 'smpush-plugin-lang')?>)</td>
                     <td><input name="info_name" value="<?php echo $connection['info_name'];?>" type="text" size="40">
                     <p class="description"><?php echo __('The table column that stores the devices information like device name, version and model', 'smpush-plugin-lang')?></p></td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Added Time', 'smpush-plugin-lang')?><br />(<?php echo __('optional', 'smpush-plugin-lang')?>)</td>
                     <td><input name="postdate" value="<?php echo $connection['postdate'];?>" type="text" size="40">
                     <p class="description"><?php echo __('The table column that stores the devices added time', 'smpush-plugin-lang')?></p></td>
                  </tr>
                  <tr valign="top">
                    <td class="first"><?php echo __('Counter', 'smpush-plugin-lang')?><br />(<?php echo __('optional', 'smpush-plugin-lang')?>)</td>
                    <td><input name="counter_name" value="<?php echo $connection['counter_name'];?>" type="text" size="40">
                      <p class="description"><?php echo __('The table column that stores the number of times device receive messages', 'smpush-plugin-lang')?></p></td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Device Type', 'smpush-plugin-lang')?></td>
                     <td class="form-required">
                     <input name="type_name" value="<?php echo $connection['type_name'];?>" type="text" size="40" aria-required="true">
                     <p class="description"><?php echo __('Name of the table column that stores the devices type', 'smpush-plugin-lang')?></p>
                	 </td>
                  </tr>
                  <tr valign="top"><th colspan="2"><?php echo __('Device Type Values', 'smpush-plugin-lang')?></th></tr>
                  <tr valign="top">
                     <td class="first">iOS</td>
                     <td class="form-required"><input name="ios_name" value="<?php echo $connection['ios_name'];?>" type="text" size="40" aria-required="true"></td>
                  </tr>
                  <tr valign="top">
                     <td class="first">iOS FCM</td>
                     <td class="form-required"><input name="iosfcm_name" value="<?php echo $connection['iosfcm_name'];?>" type="text" size="40" aria-required="true"></td>
                  </tr>
                  <tr valign="top">
                     <td class="first">Android</td>
                     <td class="form-required"><input name="android_name" value="<?php echo $connection['android_name'];?>" type="text" size="40" aria-required="true"></td>
                  </tr>
                  <tr valign="top">
                     <td class="first">Windows Phone 8</td>
                     <td class="form-required"><input name="wp_name" value="<?php echo $connection['wp_name'];?>" type="text" size="40" aria-required="true"></td>
                  </tr>
                  <tr valign="top">
                     <td class="first">Windows 10</td>
                     <td class="form-required"><input name="wp10_name" value="<?php echo $connection['wp10_name'];?>" type="text" size="40" aria-required="true"></td>
                  </tr>
                  <tr valign="top">
                     <td class="first">BlackBerry</td>
                     <td class="form-required"><input name="bb_name" value="<?php echo $connection['bb_name'];?>" type="text" size="40" aria-required="true"></td>
                  </tr>
                  <tr valign="top">
                     <td class="first">Chrome</td>
                     <td class="form-required"><input name="chrome_name" value="<?php echo $connection['chrome_name'];?>" type="text" size="40" aria-required="true"></td>
                  </tr>
                  <tr valign="top">
                     <td class="first">Safari</td>
                     <td class="form-required"><input name="safari_name" value="<?php echo $connection['safari_name'];?>" type="text" size="40" aria-required="true"></td>
                  </tr>
                  <tr valign="top">
                     <td class="first">Firefox</td>
                     <td class="form-required"><input name="firefox_name" value="<?php echo $connection['firefox_name'];?>" type="text" size="40" aria-required="true"></td>
                  </tr>
                  <tr valign="top">
                     <td class="first">Opera</td>
                     <td class="form-required"><input name="opera_name" value="<?php echo $connection['opera_name'];?>" type="text" size="40" aria-required="true"></td>
                  </tr>
                  <tr valign="top">
                     <td class="first">Edge</td>
                     <td class="form-required"><input name="edge_name" value="<?php echo $connection['edge_name'];?>" type="text" size="40" aria-required="true"></td>
                  </tr>
                  <tr valign="top">
                     <td class="first">Samsung Browser</td>
                     <td class="form-required"><input name="samsung_name" value="<?php echo $connection['samsung_name'];?>" type="text" size="40" aria-required="true"></td>
                  </tr>
                  <tr valign="top">
                     <td class="first">Facebook Messenger</td>
                     <td class="form-required"><input name="fbmsn_name" value="<?php echo $connection['fbmsn_name'];?>" type="text" size="40" aria-required="true"></td>
                  </tr>
                  <tr valign="top">
                     <td class="first">Facebook Notification</td>
                     <td class="form-required"><input name="fbnotify_name" value="<?php echo $connection['fbnotify_name'];?>" type="text" size="40" aria-required="true"></td>
                  </tr>
                  <tr valign="top">
                     <td class="first">Newsletter</td>
                     <td class="form-required"><input name="email_name" value="<?php echo $connection['email_name'];?>" type="text" size="40" aria-required="true"></td>
                  </tr>
                  <tr valign="top">
                      <td colspan="2"><input type="submit" name="submit" id="smio-submit" class="button button-primary" style="width: 120px;" value="<?php echo __('Save Changes', 'smpush-plugin-lang')?>">
                      <img src="<?php echo smpush_imgpath;?>/wpspin_light.gif" class="smpush_process" alt="" /></td>
                   </tr>
                </tbody>
              </table>
            </div>
         </div>
      </div>
   </div>
</form>