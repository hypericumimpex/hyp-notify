
jQuery(document).ready(function() {

  jQuery("#smpush_woo_waiting_button").click(function(){
    if (! smpush_browser || typeof smpush_browser === "undefined"){
      return;
    }
    if(smpush_getCookie("smpush_device_token") == ""){
      smpushIntializePopupBox();
      return;
    }
    var btn = jQuery(this);
    var oldtext = btn.html();
    jQuery(this).html(smpush_jslang.saving_text);
    jQuery.get(smpush_jslang.siteurl+"/?smpushcontrol=woo_waiting_list", { "noheader":1, "productid": btn.val(), "device_type": smpush_browser() }
      ,function(data){
        btn.html(oldtext);
        if(data["respond"] == 0){
          alert(data["message"]);
        }
      });
  });

});