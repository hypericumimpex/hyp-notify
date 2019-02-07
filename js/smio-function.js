/*!
 * Copyright (c) 2013 Smart IO Labs
 * Project repository: http://smartiolabs.com
 * license: Is not allowed to use any part of the code.
 */
var $ = jQuery;
var smpush_currcount=0, smpush_percent=0, smpush_google_open = 1, smpush_wp_open = 1, smpush_wp10_open = 1, smpush_bb_open = 1, smpush_chrome_open = 1, smpush_safari_open = 1, smpush_firefox_open = 1, smpush_opera_open = 1, smpush_edge_open = 1, smpush_iosfcm_open = 1, smpush_samsung_open = 1, smpush_fbmsn_open = 1, smpush_fbnotify_open = 1, smpush_email_open = 1, smpush_firstrun = 1, smpush_feedback_open = 1, smpush_feedback_google = 1, smpush_feedback_iosfcm = 1, smpush_feedback_chrome = 1, smpush_feedback_safari = 1;
var smpush_pro_currcount=0, smpush_pro_percent=0, smpush_lastid=0, smpush_resum_timer;
jQuery(document).ready(function() {
  jQuery("#smpush_model_select").change(function(){
    jQuery('.smpush_apidesc').hide();
    jQuery('.smpush_method_'+jQuery(this).val()).show();
  });
  jQuery('.smpushCloseTB').click(function(){
    smpushHideTable(jQuery(this).closest("div.metabox-holder").attr("data-smpush-counter"));
  });
  jQuery('#smio-submit').click(function(){
    var form = jQuery(this).parents('form');
    if(!validateForm(form))
      return false;
  });
  jQuery('#push-token-list td span').click(function(){
    jQuery(this).attr('style', 'height:auto');
  });
  jQuery('#search-submit').click(function(){
    jQuery("#smpush-noheader-value").remove();
  });
  jQuery('#post-query-submit').click(function(event){
    jQuery("#smpush-noheader-value").remove();
  });
  jQuery('.smpush-applytoall').click(function(event){
    if(!confirm(smpush_jslang.applytoall)){
      event.preventDefault();
      return;
    }
  });
  jQuery('.smio-delete').click(function(event){
    var confirmtxt = jQuery(this).attr("data-confirm");
    if(typeof confirmtxt == "undefined"){
      confirmtxt = smpush_jslang.deleteconfirm;
    }
    if (!confirm(confirmtxt)){
      event.preventDefault();
    }
  });
  jQuery('#smpush-calculate-btn').click(function(){
    var options = {
    url:           jQuery('#smpushSendCampignForm').attr("action")+'&calculate=1&noheader=1',
    beforeSubmit:  function(){jQuery('.smpush_calculate_process').show()},
    success:       function(responseText, statusText){
      responseText = JSON.parse(responseText);
      jQuery('#smpush-calculate-span-ios').html(responseText["ios"]);
      jQuery('#smpush-calculate-span-iosfcm').html(responseText["iosfcm"]);
      jQuery('#smpush-calculate-span-android').html(responseText["android"]);
      jQuery('#smpush-calculate-span-wp').html(responseText["wp"]);
      jQuery('#smpush-calculate-span-wp10').html(responseText["wp10"]);
      jQuery('#smpush-calculate-span-bb').html(responseText["bb"]);
      jQuery('#smpush-calculate-span-chrome').html(responseText["chrome"]);
      jQuery('#smpush-calculate-span-safari').html(responseText["safari"]);
      jQuery('#smpush-calculate-span-firefox').html(responseText["firefox"]);
      jQuery('#smpush-calculate-span-opera').html(responseText["opera"]);
      jQuery('#smpush-calculate-span-edge').html(responseText["edge"]);
      jQuery('#smpush-calculate-span-samsung').html(responseText["samsung"]);
      jQuery('#smpush-calculate-span-fbmsn').html(responseText["fbmsn"]);
      jQuery('#smpush-calculate-span-fbnotify').html(responseText["fbnotify"]);
      jQuery('#smpush-calculate-span-email').html(responseText["email"]);
      jQuery('#smpush-calculate-span-ios').fadeIn();
      jQuery('#smpush-calculate-span-iosfcm').fadeIn();
      jQuery('#smpush-calculate-span-android').fadeIn();
      jQuery('#smpush-calculate-span-wp').fadeIn();
      jQuery('#smpush-calculate-span-bb').fadeIn();
      jQuery('#smpush-calculate-span-chrome').fadeIn();
      jQuery('#smpush-calculate-span-safari').fadeIn();
      jQuery('#smpush-calculate-span-firefox').fadeIn();
      jQuery('#smpush-calculate-span-opera').fadeIn();
      jQuery('#smpush-calculate-span-edge').fadeIn();
      jQuery('#smpush-calculate-span-samsung').fadeIn();
      jQuery('#smpush-calculate-span-fbmsn').fadeIn();
      jQuery('#smpush-calculate-span-fbnotify').fadeIn();
      jQuery('#smpush-calculate-span-email').fadeIn();
      jQuery('.smpush_calculate_process').hide();
    }
    };
    jQuery('#smpushSendCampignForm').ajaxSubmit(options);
  });
  jQuery('#smpush-clear-hisbtn').click(function(){
    var options = {
    url:           jQuery('#smpushSendCampignForm').attr("action")+'&clearhistory=1&noheader=1',
    beforeSubmit:  function(){jQuery('.smpush_process').show()},
    success:       function(responseText, statusText){if(responseText!=1){console.log(responseText);}else{jQuery('.smpush_process').hide();}}
    };
    jQuery('#smpushSendCampignForm').ajaxSubmit(options);
  });
  jQuery('#smpush-save-hisbtn').click(function(){
    var options = {
    url:           jQuery('#smpushSendCampignForm').attr("action")+'&savehistory=1&noheader=1',
    beforeSubmit:  function(){jQuery('.smpush_process').show()},
    success:       function(responseText, statusText){if(responseText!=1){console.log(responseText);}else{jQuery('.smpush_process').hide();}}
    };
    jQuery('#smpushSendCampignForm').ajaxSubmit(options);
  });
  jQuery('.smpush-payload').change(function(){
   if(jQuery(this).val() == "multi"){
     jQuery(".smpush-payload-normal").hide();
     jQuery(".smpush-payload-multi").show();
   }
   else{
     jQuery(".smpush-payload-multi").hide();
     jQuery(".smpush-payload-normal").show();
   }
  });
  jQuery('.and_smpush-payload').change(function(){
   if(jQuery(this).val() == "multi"){
     jQuery(".and_smpush-payload-normal").hide();
     jQuery(".and_smpush-payload-multi").show();
   }
   else{
     jQuery(".and_smpush-payload-multi").hide();
     jQuery(".and_smpush-payload-normal").show();
   }
  });
  
  jQuery('.smpushReuqestTypePicker').change(function(){
   if(jQuery(this).val() == "native"){
     jQuery(".smpush-popup-settings").hide();
     jQuery(".smpush-icon-settings").hide();
   }
   else if(jQuery(this).val() == "popup"){
	jQuery(".smpush-icon-settings").hide();
	jQuery(".smpush-popup-settings").show();
   }
   else if(jQuery(this).val() == "icon"){
     jQuery(".smpush-popup-settings").hide();
     jQuery(".smpush-icon-settings").show();
   }
  });
  
  if(jQuery('.smpush_color_picker').length > 0){
    jQuery('.smpush_color_picker').wpColorPicker();
  }
  
  if(jQuery('.smpush_emoji').length > 0){
	  jQuery(".smpush_emoji").emojioneArea({
        hideSource: true,
        saveEmojisAs: "hexa",
		dir: "ltr",
		pickerPosition: "bottom",
		searchPlaceholder: smpush_jslang.emoji_search,
        useSprite: true
      });
  }
  
  if(jQuery('.smpush_jradio').length > 0){
    jQuery(".smpush_jradio").labelauty();
  }
  
  if(jQuery('.smpush-datepicker').length > 0){
    jQuery(".smpush-datepicker").datepicker({dateFormat : 'yy-mm-dd'});
  }
  
  if(jQuery('.smpush-timepicker').length > 0){
    jQuery('.smpush-timepicker').datetimepicker({
      timeFormat: "hh:mm tt",
      dateFormat : 'yy-mm-dd'
    });
  }
  
  
  smpushAutoLoad("body");
  window.send_to_editor = function(html) {
    imgurl = jQuery('img', html).attr('src');
    jQuery('.'+smpush_upload_field).val(imgurl);
    jQuery('.'+smpush_upload_field).trigger("change");
    tb_remove();
  }
});

var smpush_upload_field;
function smpushAutoLoad(element){
  
  if(jQuery(element).find('.smpush_tabs').length > 0){
    jQuery(".smpush_tabs").tabs({active: 0}).removeClass('ui-widget').removeClass('ui-widget-content');
    jQuery(".smpush_tabs ul").removeClass('ui-widget').removeClass('ui-widget-header');
  }
  
  jQuery(element).find('.smpush_upload_file_btn').click(function() {
    smpush_upload_field = jQuery(this).attr('data-container');
    formfield = jQuery('.'+smpush_upload_field).attr('name');
    tb_show('', 'media-upload.php?type=image&TB_iframe=1');
    return false;
  });
}

function smpush_delete_service(id){
  if(!confirm(smpush_jslang.deleteconfirm)){
    return;
  }
  jQuery('.smpush_service_'+id+'_loading').show();
  jQuery.get(smpush_pageurl, {'noheader':1, 'delete': 1, 'id': id}
  ,function(data){
    jQuery('.smpush_service_'+id+'_loading').hide();
    jQuery('#smpush-service-tab-'+id).hide(600, function() {
      jQuery('#smpush-service-tab-'+id).remove("push-alternate");
    });
  });
}

function smpush_open_service(id, actiontype, action, newwidth){
  if(actiontype == 1){
    if(confirm(smpush_jslang.savechangesconfirm)){
      jQuery('#smpush_jform').ajaxSubmit();
    }
  }
  else if(actiontype == 2){
    if(typeof(newwidth) == "undefined"){
      var newwidth = 55;
    }
    jQuery(".smpush-canhide").hide();
    jQuery("#col-left").css("width", newwidth+"%");
  }
  if(typeof(newwidth) != "undefined"){
    jQuery(".smpush-canhide").hide();
    jQuery("#col-left").css("width", newwidth+"%");
  }
  jQuery(".smpush_form_ajax").show();
  jQuery('.smpush-service-tab').removeClass("push-alternate");
  jQuery('#smpush-service-tab-'+id).addClass("push-alternate");
  jQuery('.smpush_service_'+id+'_loading').show();
  jQuery.get(smpush_pageurl, {'noheader':1, 'action': action, 'id': id}
  ,function(data){
    jQuery('.smpush_form_ajax').html(data);
    var smpush_form_options = {
        beforeSubmit:  function(){jQuery('.smpush_process').show()},
        success:       function(responseText, statusText){
          if(responseText != 1){
            jQuery(".smpush_process").hide();
            alert(responseText['message']);
          }
          else{
            jQuery(".smpush_process").hide();
            jQuery(".smpush_form_ajax").fadeOut("fast", function(){
              jQuery('.smpush_form_ajax').html('');
              if(actiontype == 2 || typeof(newwidth) != "undefined"){
                jQuery("#col-left").css("width", "100%");
                jQuery(".smpush-canhide").show();
              }
              if(id != -1){
                jQuery("html, body").animate({scrollTop: jQuery('#smpush-service-tab-'+id).offset().top-100}, "slow");
              }
            });
          }
        }
    };
    jQuery('#smpush_jform').ajaxForm(smpush_form_options);
    jQuery('#smio-submit').click(function(){
      var form = jQuery(this).parents('form');
      if (!validateForm(form)) return false;
    });
    jQuery('.smpush_service_'+id+'_loading').hide();
    if(id != -1)jQuery("html, body").animate({scrollTop: 0}, "slow");
  });
}

function SMPUSH_ProccessQueue(baseurl, allcount, increration){
  if(allcount == 0){
    jQuery("#smpush_progressinfo").append("<p class='error'>"+smpush_jslang.no_tokens_msg+"</p>");
    return;
  }
  if(smpush_pro_currcount == 0){
    jQuery("#smpush_progressinfo").append("<p>"+smpush_jslang.start_queuing+" "+allcount+" "+smpush_jslang.token_in_queue+"</p>");
  }

  jQuery.getJSON(baseurl+'admin.php?page=smpush_send_notification', {'noheader':1, 'lastid':smpush_lastid, 'increration':increration}
  ,function(data){
    if(typeof(data) === "undefined" || data === null){
      jQuery("#smpush_progressinfo").append("<p class='error'>"+smpush_jslang.escaped_reconnect+"</p>");
      smpush_resum_timer = setTimeout(function(){SMPUSH_ProccessQueue(baseurl, allcount, increration)}, 2000);
      return;
    }

    if(data.respond != 0){
      smpush_pro_currcount = smpush_pro_currcount+increration;
      smpush_pro_percent = Math.floor(((smpush_pro_currcount)/allcount)*100);
      jQuery("#smpush_progressbar").progressbar("value", smpush_pro_percent);
      jQuery(".smpush_progress_label").text(smpush_pro_percent+'%');
    }

    if(data.respond == 1){
      smpush_lastid = data.message;
      SMPUSH_ProccessQueue(baseurl, allcount, increration);
    }
    else if(data.respond == -1){
      jQuery("#smpush_progressbar").progressbar("value", 100);
      jQuery(".smpush_progress_label").text(smpush_jslang.completed);
      jQuery("#smpush_progressinfo").append('<p>'+data.message+' '+smpush_jslang.message_queuing_completed+'</p>');
      SMPUSH_RunQueue(baseurl, allcount);
    }
    else if(data.respond == -2){
      jQuery("#smpush_progressbar").progressbar("value", 100);
      jQuery(".smpush_progress_label").text(smpush_jslang.completed);
      jQuery("#smpush_progressinfo").append('<p>'+data.message+' '+smpush_jslang.message_queuing_scheduling+'</p>');
      jQuery("#smpush_progressinfo").append('<p>'+smpush_jslang.completed+'...</p>');
      jQuery("#cancel_push").val(smpush_jslang.exit_and_back);
    }
    else if(data.respond == 0) jQuery("#smpush_progressinfo").append(data.message);
    else jQuery("#smpush_progressinfo").append('<p class="error">'+smpush_jslang.error_refresh+'</p>');
  }).fail(function(error) {
    console.log(error.responseText);
    jQuery("#smpush_progressinfo").append("<p class='error'>"+smpush_jslang.escaped_reconnect+"</p>");
    smpush_resum_timer = setTimeout(function(){SMPUSH_ProccessQueue(baseurl, allcount, increration)}, 2000);
  });
}

function SMPUSH_RunQueue(baseurl, allcount){
  jQuery.getJSON(baseurl+'admin.php?page=smpush_runqueue', {'noheader':1, 'getcount':0, 'firstrun':smpush_firstrun, 'google_notify':smpush_google_open, 'iosfcm_notify':smpush_iosfcm_open, 'wp_notify':smpush_wp_open, 'wp10_notify':smpush_wp10_open, 'bb_notify':smpush_bb_open, 'chrome_notify':smpush_chrome_open, 'safari_notify':smpush_safari_open, 'firefox_notify':smpush_firefox_open, 'opera_notify':smpush_opera_open, 'edge_notify':smpush_edge_open, 'samsung_notify':smpush_samsung_open, 'fbmsn_notify':smpush_fbmsn_open, 'fbnotify_notify':smpush_fbnotify_open, 'email_notify':smpush_email_open, 'feedback_open':smpush_feedback_open, 'feedback_google':smpush_feedback_google, 'feedback_iosfcm':smpush_feedback_iosfcm, 'feedback_chrome':smpush_feedback_chrome, 'feedback_safari':smpush_feedback_safari}
  ,function(data){
    smpush_firstrun = 0;
    if(typeof(data) === "undefined" || data === null){
      jQuery("#smpush_progressinfo").append("<p class='error'>"+smpush_jslang.escaped_reconnect+"</p>");
      smpush_resum_timer = setTimeout(function(){SMPUSH_RunQueue(baseurl, allcount)}, 3000);
      return;
    }

    if(data.respond != 0){
      if(allcount == -1){
        jQuery(".smpush_progress_label").text(smpush_jslang.start_feedback);
      }
      else{
        smpush_percent = Math.floor((smpush_currcount/allcount)*100);
        jQuery("#smpush_progressbar").progressbar("value", smpush_percent);
        jQuery(".smpush_progress_label").text(smpush_percent+'%');
        if(smpush_percent >= 100){
          jQuery(".smpush_progress_label").text(smpush_jslang.start_feedback);
        }
      }
    }

    if(data.respond == 1){
      if(data.result.all_count > 0){
        smpush_currcount = allcount-data.result.all_count;
      }
      SMPUSH_RunQueue(baseurl, allcount);
    }
    else if(data.respond == -1){
      jQuery("#smpush_progressbar").progressbar("value", 100);
      jQuery(".smpush_progress_label").text(smpush_jslang.completed);
      jQuery("#smpush_progressinfo").append(data.message);
      jQuery("#smpush_progressinfo").append('<p>'+smpush_jslang.completed+'...</p>');
      jQuery("#cancel_push").val(smpush_jslang.exit_and_back);
    }
    else if(data.respond == 2){
      if(data.result.all_count > 0){
        smpush_currcount = allcount-data.result.all_count;
      }
      if(data.result.message != ""){
        jQuery("#smpush_progressinfo").append(data.result.message);
      }
      SMPUSH_RunQueue(baseurl, allcount);
    }
    else if(data.respond == 3){
      smpush_google_open = 0;
      if(data.result.all_count > 0){
        smpush_currcount = allcount-data.result.all_count;
      }
      jQuery("#smpush_progressinfo").append(data.result.message);
      SMPUSH_RunQueue(baseurl, allcount);
    }
    else if(data.respond == "wp_server_reponse"){
      smpush_wp_open = 0;
      if(data.result.all_count > 0){
        smpush_currcount = allcount-data.result.all_count;
      }
      jQuery("#smpush_progressinfo").append(data.result.message);
      SMPUSH_RunQueue(baseurl, allcount);
    }
    else if(data.respond == "wp10_server_reponse"){
      smpush_wp10_open = 0;
      if(data.result.all_count > 0){
        smpush_currcount = allcount-data.result.all_count;
      }
      jQuery("#smpush_progressinfo").append(data.result.message);
      SMPUSH_RunQueue(baseurl, allcount);
    }
    else if(data.respond == "bb_server_reponse"){
      smpush_bb_open = 0;
      if(data.result.all_count > 0){
        smpush_currcount = allcount-data.result.all_count;
      }
      jQuery("#smpush_progressinfo").append(data.result.message);
      SMPUSH_RunQueue(baseurl, allcount);
    }
    else if(data.respond == "chrome_server_reponse"){
      smpush_chrome_open = 0;
      if(data.result.all_count > 0){
        smpush_currcount = allcount-data.result.all_count;
      }
      jQuery("#smpush_progressinfo").append(data.result.message);
      SMPUSH_RunQueue(baseurl, allcount);
    }
    else if(data.respond == "safari_server_reponse"){
      smpush_safari_open = 0;
      if(data.result.all_count > 0){
        smpush_currcount = allcount-data.result.all_count;
      }
      jQuery("#smpush_progressinfo").append(data.result.message);
      SMPUSH_RunQueue(baseurl, allcount);
    }
    else if(data.respond == "firefox_server_reponse"){
      smpush_firefox_open = 0;
      if(data.result.all_count > 0){
        smpush_currcount = allcount-data.result.all_count;
      }
      jQuery("#smpush_progressinfo").append(data.result.message);
      SMPUSH_RunQueue(baseurl, allcount);
    }
    else if(data.respond == "opera_server_reponse"){
      smpush_opera_open = 0;
      if(data.result.all_count > 0){
        smpush_currcount = allcount-data.result.all_count;
      }
      jQuery("#smpush_progressinfo").append(data.result.message);
      SMPUSH_RunQueue(baseurl, allcount);
    }
    else if(data.respond == "edge_server_reponse"){
      smpush_edge_open = 0;
      if(data.result.all_count > 0){
        smpush_currcount = allcount-data.result.all_count;
      }
      jQuery("#smpush_progressinfo").append(data.result.message);
      SMPUSH_RunQueue(baseurl, allcount);
    }
    else if(data.respond == "iosfcm_server_reponse"){
      smpush_iosfcm_open = 0;
      if(data.result.all_count > 0){
        smpush_currcount = allcount-data.result.all_count;
      }
      jQuery("#smpush_progressinfo").append(data.result.message);
      SMPUSH_RunQueue(baseurl, allcount);
    }
    else if(data.respond == "samsung_server_reponse"){
      smpush_samsung_open = 0;
      if(data.result.all_count > 0){
        smpush_currcount = allcount-data.result.all_count;
      }
      jQuery("#smpush_progressinfo").append(data.result.message);
      SMPUSH_RunQueue(baseurl, allcount);
    }
    else if(data.respond == "fbmsn_server_reponse"){
      smpush_fbmsn_open = 0;
      if(data.result.all_count > 0){
        smpush_currcount = allcount-data.result.all_count;
      }
      jQuery("#smpush_progressinfo").append(data.result.message);
      SMPUSH_RunQueue(baseurl, allcount);
    }
    else if(data.respond == "fbnotify_server_reponse"){
      smpush_fbnotify_open = 0;
      if(data.result.all_count > 0){
        smpush_currcount = allcount-data.result.all_count;
      }
      jQuery("#smpush_progressinfo").append(data.result.message);
      SMPUSH_RunQueue(baseurl, allcount);
    }
    else if(data.respond == "email_server_reponse"){
      smpush_email_open = 0;
      if(data.result.all_count > 0){
        smpush_currcount = allcount-data.result.all_count;
      }
      jQuery("#smpush_progressinfo").append(data.result.message);
      SMPUSH_RunQueue(baseurl, allcount);
    }
    else if(data.respond == 4){
      smpush_feedback_open = 0;
      jQuery("#smpush_progressinfo").append(data.message);
      SMPUSH_RunQueue(baseurl, allcount);
    }
    else if(data.respond == 5){
      smpush_feedback_google = 0;
      jQuery("#smpush_progressinfo").append(data.message);
      SMPUSH_RunQueue(baseurl, allcount);
    }
    else if(data.respond == 6){
      smpush_feedback_chrome = 0;
      jQuery("#smpush_progressinfo").append(data.message);
      SMPUSH_RunQueue(baseurl, allcount);
    }
    else if(data.respond == 7){
      smpush_feedback_safari = 0;
      jQuery("#smpush_progressinfo").append(data.message);
      SMPUSH_RunQueue(baseurl, allcount);
    }
    else if(data.respond == 8){
      smpush_feedback_iosfcm = 0;
      jQuery("#smpush_progressinfo").append(data.message);
      SMPUSH_RunQueue(baseurl, allcount);
    }
    else if(data.respond == 0) jQuery("#smpush_progressinfo").append(data.message);
    else jQuery("#smpush_progressinfo").append('<p class="error">'+smpush_jslang.error_refresh+'</p>');
  }).fail(function(error) {
    console.log(error.responseText);
    jQuery("#smpush_progressinfo").append("<p class='error'>"+smpush_jslang.escaped_reconnect+"</p>");
    smpush_resum_timer = setTimeout(function(){SMPUSH_RunQueue(baseurl, allcount)}, 3000);
  });
}

function smpushEventDelRow(button) {
  if(jQuery(".smpushEventConditions div").length == 1){
    return;
  }
  jQuery(button).closest("div").remove();
}

function smpushUpdateValueField(select) {
  var value = jQuery(select).find(':selected').attr('data-placeholder');
  jQuery(select).closest("div").find(".smpushPostAttriSelectorValue").attr("placeholder", value);
}

function smpushEventAddRow(button) {
  var newRow = "<div class='smpush-clear'>"+jQuery(button).closest("div").html()+"</div>";
  jQuery(".smpushEventConditions").append(newRow);
  jQuery(".smpushEventConditions div:last").find("select").val("");
  jQuery(".smpushEventConditions div:last").find("input[type='text']").val("");
}

function smpushInsertAtCaret(areaId, text) {
  var txtarea = document.getElementById(areaId);
  var scrollPos = txtarea.scrollTop;
  var strPos = 0;
  var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ? "ff" : (document.selection ? "ie" : false ) );
  if (br == "ie") { 
      txtarea.focus();
      var range = document.selection.createRange();
      range.moveStart ('character', -txtarea.value.length);
      strPos = range.text.length;
  }
  else if (br == "ff") strPos = txtarea.selectionStart;

  var front = (txtarea.value).substring(0,strPos);  
  var back = (txtarea.value).substring(strPos,txtarea.value.length); 
  txtarea.value=front+text+back;
  strPos = strPos + text.length;
  if (br == "ie") { 
      txtarea.focus();
      var range = document.selection.createRange();
      range.moveStart ('character', -txtarea.value.length);
      range.moveStart ('character', strPos);
      range.moveEnd ('character', 0);
      range.select();
  }
  else if (br == "ff") {
      txtarea.selectionStart = strPos;
      txtarea.selectionEnd = strPos;
      txtarea.focus();
  }
  txtarea.scrollTop = scrollPos;
}

function smpush_back_setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function smpush_back_getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(";");
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==" "){
          c = c.substring(1);
        }
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}

function smpushHideTable(tbnum){
  jQuery(".metabox-holder[data-smpush-counter='"+tbnum+"']").hide();
  var smpushTablesHistory = smpush_back_getCookie("smpushTablesHistory");
  smpushTablesHistory += ","+tbnum;
  smpush_back_setCookie("smpushTablesHistory", smpushTablesHistory, 30);
}

function smpushResetHistoryTables(){
  jQuery(".metabox-holder").show();
  smpush_back_setCookie("smpushTablesHistory", "", -1);
}

function smpushHideHistoryTables(){
  var smpushTablesHistory = smpush_back_getCookie("smpushTablesHistory");
  if(smpushTablesHistory != ""){
    smpushTablesHistory = smpushTablesHistory.split(",");
    for(var i=0;i<=smpushTablesHistory.length;i++){
      jQuery(".metabox-holder[data-smpush-counter='"+smpushTablesHistory[i]+"']").hide();
    }
  }
}

function smpushProcessSmilies(message){
	message = message.replace(/U\+([0-9A-F]{4,5})/ig, function (match, code) {
		var codes = code.split('-').map(function(value, index) {
			return parseInt(value, 16);
		});
		return String.fromCodePoint.apply(null, codes);
	});
	return message;
}

function smpushPayToSelector(value){
  if(value == 1){
    jQuery(".paytoreadOptions2").hide();
    jQuery(".paytoreadOptions1").show();
  }
  else if(value == 2){
    jQuery(".paytoreadOptions1").hide();
    jQuery(".paytoreadOptions2").show();
  }
  else{
    jQuery(".paytoreadOptions2").hide();
    jQuery(".paytoreadOptions1").hide();
  }
}
