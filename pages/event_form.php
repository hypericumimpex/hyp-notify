<form action="<?php echo $pageurl;?>&noheader=1" method="post" id="smpush_jform" class="validate">
<input type="hidden" name="id" value="<?php echo $event['id'];?>">
   <div id="post-body" class="metabox-holder columns-2">
      <div id="post-body-content" class="edit-form-section">
         <div id="namediv" class="stuffbox">
            <h3><label><?php echo (empty($event['title']))? __('Add New Event', 'smpush-plugin-lang') : $event['title'];?></label></h3>
            <div class="inside">
               <table class="form-table">
                <tbody>
                  <tr valign="top">
                     <td class="first"><?php echo __('Title', 'smpush-plugin-lang')?></td>
                     <td class="form-required">
                     <input name="title" type="text" size="60" value="<?php echo $event['title'];?>" aria-required="true">
                     </td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Event Type', 'smpush-plugin-lang')?></td>
                     <td>
                     <select name="event_type">
                        <option value="publish"><?php echo __('Published for first time', 'smpush-plugin-lang')?></option>
                        <option value="approve" <?php if($event['event_type'] == 'approve'){?>selected="selected"<?php }?>><?php echo __('Get approval to publish', 'smpush-plugin-lang')?></option>
                        <option value="update" <?php if($event['event_type'] == 'update'){?>selected="selected"<?php }?>><?php echo __('Get new changes or updates', 'smpush-plugin-lang')?></option>
                     </select>
                     </td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Post Type', 'smpush-plugin-lang')?></td>
                     <td class="form-required">
                     <select name="event_post_type" class="smpushPostType" aria-required="true">
                       <option value=""><?php echo __('Select Post Type', 'smpush-plugin-lang')?></option>
                      <?php foreach(get_post_types('', 'names') as $post_type): ?>
                        <option value="<?php echo $post_type?>" <?php if($event['post_type'] == $post_type){?>selected="selected"<?php }?>><?php echo $post_type?></option>
                      <?php endforeach; ?>
                     </select>
                     </td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Message Template', 'smpush-plugin-lang')?></td>
                     <td>
                      <select name="msg_template">
                       <option value="0"><?php echo __('Select Message Template', 'smpush-plugin-lang')?></option>
                      <?php if(!empty($templates)):foreach($templates as $template): ?>
                        <option value="<?php echo $template['id']?>" <?php if($event['msg_template'] == $template['id']){?>selected="selected"<?php }?>><?php echo $template['id'].'- '.$template['name']?></option>
                      <?php endforeach;endif; ?>
                      </select>
                     </td>
                  </tr>
                  <tr valign="top">
                    <td class="first"><?php echo __('Notification Title', 'smpush-plugin-lang')?></td>
                    <td>
                      <div id="smpush-msg-subject" class="tabs-panel">
                        <?php include('event_form_quickadd.php')?>
                        <br class="clear">
                        <input name="subject" id="smpushEventSubject" class="smpushEventMessage" value="<?php echo $event['subject'];?>" size="60" />
                      </div>
                    </td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Notification Message', 'smpush-plugin-lang')?></td>
                     <td>
                       <div class="categorydiv smpush_tabs">
                          <ul class="category-tabs">
                            <li><a href="#smpush-msg-desktop"><?php echo __('Web & Mobile', 'smpush-plugin-lang')?></a></li>
                            <li><a href="#smpush-msg-fbmsn"><?php echo __('Facebook Messenger', 'smpush-plugin-lang')?></a></li>
                            <li><a href="#smpush-msg-fbnotify"><?php echo __('Facebook Notification', 'smpush-plugin-lang')?></a></li>
                            <li><a href="#smpush-msg-email"><?php echo __('Newsletter', 'smpush-plugin-lang')?></a></li>
                          </ul>
                          <div id="smpush-msg-desktop" class="tabs-panel">
                            <table class="form-table">
                                <tr valign="top">
                                  <td>
                                    <?php include('event_form_quickadd.php')?>
                                    <br class="clear">
                                    <textarea name="message" rows="6" cols="40" style="width:95%" id="smpushEventMessageWeb" class="smpushEventMessage" aria-required="true"><?php echo $event['message'];?></textarea>
                                  </td>
                                </tr>
                            </table>
                          </div>
                          <div id="smpush-msg-fbmsn" class="tabs-panel">
                            <table class="form-table">
                                <tr valign="top">
                                  <td>
                                    <?php include('event_form_quickadd.php')?>
                                    <br class="clear">
                                    <textarea name="fbmsn_message" rows="6" cols="40" style="width:95%" id="smpushEventMessageMsn" class="smpushEventMessage" aria-required="true"><?php echo $event['fbmsn_message'];?></textarea>
                                  </td>
                                </tr>
                            </table>
                          </div>
                         <div id="smpush-msg-fbnotify" class="tabs-panel">
                            <table class="form-table">
                                <tr valign="top">
                                  <td>
                                    <?php include('event_form_quickadd.php')?>
                                    <br class="clear">
                                    <textarea name="fbnotify_message" rows="6" cols="40" style="width:95%" id="smpushEventMessageFBNotify" class="smpushEventMessage" aria-required="true"><?php echo $event['fbnotify_message'];?></textarea>
                                  </td>
                                </tr>
                            </table>
                          </div>
                         <div id="smpush-msg-email" class="tabs-panel">
                            <table class="form-table">
                                <tr valign="top">
                                  <td>
                                    <?php include('event_form_quickadd.php')?>
                                    <br class="clear">
                                    <textarea name="email_message" rows="6" cols="40" style="width:95%" id="smpushEventMessageEmail" class="smpushEventMessage" aria-required="true"><?php echo $event['email_message'];?></textarea>
                                  </td>
                                </tr>
                            </table>
                          </div>
                      </div>
                     </td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Mobile Payload', 'smpush-plugin-lang')?></td>
                     <td>
                       <input type="text" id="smpushPayloadTitle" size="15" placeholder="<?php echo __('Parameter Name', 'smpush-plugin-lang')?>" style="float:left;margin:0 5px 10px 0">
                       <select id="smpushPayload" class="smpushPostAttriSelector" style="width:150px;float:left;margin:0 5px 10px 0">
                          <option value=""><?php echo __('Choose Attribute', 'smpush-plugin-lang')?></option>
                        </select>
                       <select id="smpushPayloadFormat" style="float:left;margin:0 5px 10px 0">
                          <option value="regular"><?php echo __('Select Format', 'smpush-plugin-lang')?></option>
                          <option value="CapitalizeFirst">CapitalizeFirst</option>
                          <option value="CapitalizeAllFirst">CapitalizeAllFirst</option>
                          <option value="UPPERCASE">UPPERCASE</option>
                          <option value="lowercase">lowercase</option>
                          <option value="datetime">Date Time</option>
                          <option value="date">Date</option>
                          <option value="regular">Regular</option>
                        </select>
                       <select id="smpushPayloadFunction" class="smpushPostFunctions" style="float:left;margin:0 5px 10px 0">
                          <option value=""><?php echo __('Pass ID To Function And Get', 'smpush-plugin-lang')?></option>
                        </select>
                       <input type="text" id="smpushPayloadDefault" size="15" placeholder="<?php echo __('Default Value', 'smpush-plugin-lang')?>" style="float:left;margin:0 5px 10px 0">
                       <input type="button" class="smpushInsertPayload button button-primary" style="float:left;margin:0 5px 5px 0" value="<?php echo __('Insert', 'smpush-plugin-lang')?>">
                       <br class="clear">
                       <div id="smpushPayloadFields">
                         <?php if(!empty($event['payload_fields'])): foreach($event['payload_fields']['field'] as $key => $field): ?>
                         <p class="smpush-clear">
                          <input name="payload[field][]" value="<?php echo $event['payload_fields']['field'][$key] ?>" type="text" size="15" style="float:left;margin:0 5px">
                          <input name="payload[value][]" value="<?php echo $event['payload_fields']['value'][$key] ?>" type="text" size="30" style="float:left;margin:0 5px">
                          <input type="button" class="button button-primary" onclick="jQuery(this).closest('p').remove();" style="float:left;margin:0 5px" value="<?php echo __('Remove', 'smpush-plugin-lang')?>">
                         </p>
                         <?php endforeach; endif; ?>
                       </div>
                     </td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Ignore Case', 'smpush-plugin-lang')?></td>
                     <td>
                       <label><input name="ignore" type="checkbox" <?php if($event['ignore'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Ignore sending the message if one of its variables is empty or equal zero value .', 'smpush-plugin-lang')?></label>
                     </td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Conditions', 'smpush-plugin-lang')?></td>
                     <td class="smpushEventConditions">
                       <?php if(!empty($event['conditions'])): foreach($event['conditions']['attri'] as $key => $condition): ?>
                       <p class="smpush-clear">
                          <input name="conditions[attri][]" value="<?php echo $event['conditions']['attri'][$key] ?>" type="text" size="15" style="float:left;margin:0 5px">
                          <input name="conditions[sign][]" value="<?php echo $event['conditions']['sign'][$key] ?>" type="text" size="15" style="float:left;margin:0 5px">
                          <input name="conditions[value][]" value="<?php echo $event['conditions']['value'][$key] ?>" type="text" size="25" style="float:left;margin:0 5px">
                          <input type="button" class="button button-primary" onclick="jQuery(this).closest('p').remove();" style="float:left;margin:0 5px" value="<?php echo __('Remove', 'smpush-plugin-lang')?>">
                       </p>
                       <?php endforeach; endif; ?>
                       <div class="smpush-clear">
                          <select name="conditions[attri][]" class="smpushPostAttriSelector" style="width:150px;float:left;margin:0 5px">
                             <option value=""><?php echo __('Choose Attribute', 'smpush-plugin-lang')?></option>
                           </select>
                         <select name="conditions[sign][]" style="width:120px;float:left;margin:0 5px 5px 0" onchange="smpushUpdateValueField(this)">
                            <option data-placeholder="<?php echo __('Write Value', 'smpush-plugin-lang')?>" value=""><?php echo __('Choose Sign', 'smpush-plugin-lang')?></option>
                             <option data-placeholder="<?php echo __('e.g. 1 or Now() or Date()', 'smpush-plugin-lang')?>">></option>
                             <option data-placeholder="<?php echo __('e.g. 1 or Now() or Date()', 'smpush-plugin-lang')?>">>=</option>
                             <option data-placeholder="<?php echo __('e.g. 1 or Now() or Date()', 'smpush-plugin-lang')?>"><</option>
                             <option data-placeholder="<?php echo __('e.g. 1 or Now() or Date()', 'smpush-plugin-lang')?>"><=</option>
                             <option data-placeholder="<?php echo __('e.g. 1 or Now() or Date()', 'smpush-plugin-lang')?>">=</option>
                             <option data-placeholder="<?php echo __('e.g. 1 or Now() or Date()', 'smpush-plugin-lang')?>">NOT =</option>
                             <option data-placeholder="<?php echo __('e.g. 1 , value , value , 3', 'smpush-plugin-lang')?>">IN</option>
                             <option data-placeholder="<?php echo __('e.g. 1 , value , value , 3', 'smpush-plugin-lang')?>">NOT IN</option>
                             <option data-placeholder="<?php echo __('e.g. 1 , value , value , 3', 'smpush-plugin-lang')?>">INTERSECT</option>
                             <option data-placeholder="<?php echo __('e.g. 1 , value , value , 3', 'smpush-plugin-lang')?>">NOT INTERSECT</option>
                           </select>
                         <input name="conditions[value][]" class="smpushPostAttriSelectorValue" type="text" size="25" placeholder="<?php echo __('Write Value', 'smpush-plugin-lang')?>" style="float:left;margin:0 5px">
                          <input type="button" class="button button-primary" onclick="smpushEventAddRow(this)" style="float:left;margin:0 5px" value="<?php echo __('AND', 'smpush-plugin-lang')?>">
                          <input type="button" class="button button-primary" onclick="smpushEventDelRow(this)" style="float:left;margin:0 5px" value="<?php echo __('Remove', 'smpush-plugin-lang')?>">
                       </div>
                     </td>
                  </tr>
                  <tr valign="top">
                    <td class="first"><?php echo __('Subscription Page', 'smpush-plugin-lang')?></td>
                    <td>
                      <select name="subs_filter">
                        <option value="all"><?php echo __('Send to all', 'smpush-plugin-lang')?></option>
                        <option value="only_have" <?php if($event['subs_filter'] == 'only_have'):?>selected="selected"<?php endif;?>><?php echo __('Send to users that have a subscription only', 'smpush-plugin-lang')?></option>
                        <option value="not_have" <?php if($event['subs_filter'] == 'not_have'):?>selected="selected"<?php endif;?>><?php echo __('Send to all users except users that have a subscription', 'smpush-plugin-lang')?></option>
                      </select>
                    </td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Notify Segment', 'smpush-plugin-lang')?></td>
                     <td>
                       <select name="notify_segment" onchange="if(this.value == 'custom'){jQuery('.smpush_userid_field').show();}else{jQuery('.smpush_userid_field').hide();}">
                         <option value="all"><?php echo __('All Registered Users', 'smpush-plugin-lang')?></option>
                         <option value="post_owner" <?php if($event['notify_segment'] == 'post_owner'):?>selected="selected"<?php endif;?>><?php echo __('User that published the post', 'smpush-plugin-lang')?></option>
                         <option value="post_commenters" <?php if($event['notify_segment'] == 'post_commenters'):?>selected="selected"<?php endif;?>><?php echo __('Users add a comment in the post', 'smpush-plugin-lang')?></option>
                         <option value="comment_mentions" <?php if($event['notify_segment'] == 'comment_mentions'):?>selected="selected"<?php endif;?>><?php echo __('[bbPress] Users that mentioned in the comment', 'smpush-plugin-lang')?></option>
                         <option value="quoted_comment" <?php if($event['notify_segment'] == 'quoted_comment'):?>selected="selected"<?php endif;?>><?php echo __('[bbPress] User that has been replied on his comment', 'smpush-plugin-lang')?></option>
                         <option value="tev_attendees" <?php if($event['notify_segment'] == 'tev_attendees'):?>selected="selected"<?php endif;?>><?php echo __('[The Events Calendar] Tickets orders and attendees', 'smpush-plugin-lang')?></option>
                         <option value="custom" <?php if($event['notify_segment'] == 'custom'):?>selected="selected"<?php endif;?>><?php echo __('Specify a user ID attribute', 'smpush-plugin-lang')?></option>
                        </select>
                     </td>
                  </tr>
                  <tr valign="top" class="smpush_userid_field" <?php if($event['notify_segment'] != 'custom'):?>style="display:none"<?php endif;?>>
                     <td class="first"><?php echo __('User ID Device', 'smpush-plugin-lang')?></td>
                     <td class="form-required">
                       <input name="userid_field" value="<?php echo $event['userid_field'] ?>" type="text" id="smpushUserAttriValue" size="22">
                       <select class="smpushPostAttriSelector" style="width:150px;" onchange="jQuery('#smpushUserAttriValue').val(jQuery(this).val())">
                           <option value=""><?php echo __('Choose Attribute', 'smpush-plugin-lang')?></option>
                        </select>
                       <p class="description"><?php echo __('Select the userID attribute to send the device that is related to this userID value only .', 'smpush-plugin-lang')?></p>
                     </td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Desktop Push Link', 'smpush-plugin-lang')?></td>
                     <td>
                       <label><input name="desktop_link" type="checkbox" <?php if($event['desktop_link'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable open the post link when click on desktop push notification', 'smpush-plugin-lang')?></label>
                     </td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Newsletter', 'smpush-plugin-lang')?></td>
                     <td>
                       <label><input name="email" type="checkbox" <?php if($event['email'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Send email notifications to all WordPress users', 'smpush-plugin-lang')?></label>
                     </td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Status', 'smpush-plugin-lang')?></td>
                     <td>
                      <input name="status" type="checkbox" <?php if($event['status'] == 1) { ?>checked="checked"<?php } ?>>
                     </td>
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
<script type="text/javascript">
jQuery(document).ready(function() {
  smpushAutoLoad("#smpush_jform");
  
jQuery(".smpushInsertPayload").click(function(){
   if(jQuery("#smpushPayload").val() == "" || jQuery("#smpushPayloadTitle").val() == ""){
     return;
   }
   var title = jQuery("#smpushPayloadTitle").val();
   var insert = "{$"+jQuery("#smpushPayload").val();
   if(jQuery("#smpushPayloadFormat").val() != ""){
     insert += "|"+jQuery("#smpushPayloadFormat").val();
   }
   if(jQuery("#smpushPayloadDefault").val() != ""){
     var defValue = jQuery("#smpushPayloadDefault").val();
     defValue = defValue.replace("{", "");
     defValue = defValue.replace("}", "");
     defValue = defValue.replace("$", "");
     insert += "|"+defValue;
   }
   else{
     insert += "|null";
   }
   if(jQuery("#smpushPayloadFunction").val() != ""){
     insert += "|"+jQuery("#smpushPayloadFunction").val();
   }
   insert += "}";
   var html = '<p class="smpush-clear">';
   html += '<input name="payload[field][]" value="'+title+'" type="text" size="15" style="float:left;margin:0 5px">';
   html += '<input name="payload[value][]" value="'+insert+'" type="text" size="30" style="float:left;margin:0 5px">';
   html += '<input type="button" class="button button-primary" onclick="jQuery(this).closest(\'p\').remove();" style="float:left;margin:0 5px" value="<?php echo __('Remove', 'smpush-plugin-lang')?>">';
   html += '</p>';
   jQuery("#smpushPayloadFields").append(html);
 });
 
 jQuery(".smpushInsertAtrri").click(function(){
   var container = jQuery("#"+jQuery(this).closest("div.tabs-panel").attr("id"));
   
   if(container.find(".smpushPostAttri").val() == ""){
     return;
   }
   var insert = "{$"+container.find(".smpushPostAttri").val();
   if(container.find(".smpushPostAttriFormat").val() != ""){
     insert += "|"+container.find(".smpushPostAttriFormat").val();
   }
   if(container.find(".smpushPostAttriDefault").val() != ""){
     var defValue = container.find(".smpushPostAttriDefault").val();
     defValue = defValue.replace("{", "");
     defValue = defValue.replace("}", "");
     defValue = defValue.replace("$", "");
     insert += "|"+defValue;
   }
   else{
     insert += "|null";
   }
   if(container.find(".smpushPostAttriFunction").val() != ""){
     insert += "|"+container.find(".smpushPostAttriFunction").val();
   }
   insert += "}";
   smpushInsertAtCaret(container.find(".smpushEventMessage").attr("id"), insert);
   container.find(".smpushPostAttriFormat").val("regular");
   container.find(".smpushPostAttriFunction").val("");
   container.find(".smpushPostAttriDefault").val("");
 });
 
 jQuery(".smpushPostType").change(function(){
   if(jQuery(this).val() == ""){
     return;
   }
   jQuery(".smpush_service_-1_loading").show();
   jQuery.get("<?php echo $pageurl?>&loadAttri=1", {"noheader": 1, "smpush_post_type" : jQuery(this).val()}, function(data){
     jQuery(".smpush_service_-1_loading").hide();
     data = JSON.parse(data);
     if(data["status"] == 0){
       alert(smpush_jslang.event_no_post);
       return;
     }
     jQuery(".smpushPostFunctions").html(data["postFuncs"]);
     jQuery(".smpushPostAttriSelector").html(data["postAttrs"]);
   });
 });
 if(jQuery(".smpushPostType").val() != ""){
   jQuery(".smpushPostType").trigger("change");
 }
});
</script>