<form action="" method="get">
  <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>" />
  <input type="hidden" name="action" value="<?php echo (empty($_GET['action']))? 0 : $_GET['action']; ?>" />
  <input type="hidden" name="msgid" value="<?php echo (empty($_GET['msgid']))? 0 : $_GET['msgid']; ?>" />
  <div class="tablenav top">
    <div class="alignleft actions smpush-canhide">
      <select name="platform">
        <option value="0"><?php echo __('All platforms', 'smpush-plugin-lang') ?></option>
        <?php foreach(self::$platforms as $key => $platform): ?>
          <option value="<?php echo $platform; ?>" <?php if(!empty($_GET['platform']) && $_GET['platform'] == $platform){ ?>selected="selected"<?php } ?>><?php echo self::$platform_titles[$key]; ?></option>
        <?php endforeach; ?>
      </select>
      <input type="text" name="fromdate" class="smpush-datepicker" placeholder="<?php echo __('From Date', 'smpush-plugin-lang') ?>" value="<?php echo (!empty($_GET['fromdate'])) ? $_GET['fromdate'] : ''; ?>">
      <input type="text" name="todate" class="smpush-datepicker" placeholder="<?php echo __('To Date', 'smpush-plugin-lang') ?>" value="<?php echo (!empty($_GET['todate'])) ? $_GET['todate'] : ''; ?>">
      <input type="submit" id="post-query-submit" class="button" value="<?php echo __('Filter', 'smpush-plugin-lang') ?>">
    </div>
    <br class="clear">
  </div>
</form>

<div class="metabox-holder" style="display: inline-block;width:100%;">
  <div class="postbox-container" style="width:100%;">
    <div class="meta-box-sortables">
      <div class="postbox">
          <table id="smpush-quick-stats">
            <tr>
              <td>
                <img src="<?php echo smpush_imgpath; ?>/megaphone.png" />
                <p><?php echo __('Notifications', 'smpush-plugin-lang') ?></p>
                <span><?php echo self::$reports['platforms']['smsgs']?></span>
              </td>
              <td>
                <img src="<?php echo smpush_imgpath; ?>/views.png" />
                <p><?php echo __('Views', 'smpush-plugin-lang') ?></p>
                <span><?php echo self::$reports['platforms']['views']?></span>
              </td>
              <td>
                <img src="<?php echo smpush_imgpath; ?>/clicks.png" />
                <p><?php echo __('Clicks', 'smpush-plugin-lang') ?></p>
                <span><?php echo self::$reports['platforms']['clicks']?></span>
              </td>
              <td>
                <img src="<?php echo smpush_imgpath; ?>/activity.png" />
                <p><?php echo __('Click Rate', 'smpush-plugin-lang') ?></p>
                <span><?php echo (empty(self::$reports['platforms']['clicks']))? 0 : round((self::$reports['platforms']['clicks']/self::$reports['platforms']['views'])*100, 1)?>%</span>
              </td>
            </tr>
          </table>
          <br class="clear">
          <div class="inside smpush-center" style="display: inline-block;width:215px;text-align:center">
            <canvas id="smpushChartTotalMsgs" style="margin:auto"></canvas>
          </div>
          <div class="inside smpush-center" style="display: inline-block;width:215px;text-align:center">
            <canvas id="smpushChartClickRate" style="margin:auto"></canvas>
          </div>
      </div>
    </div>
  </div>
</div>

<?php if(empty($_GET['msgid'])):?>
<div class="metabox-holder" style="display: inline-block;width:100%;">
  <div class="postbox-container" style="width:100%;">
    <div class="meta-box-sortables">
      <div class="postbox">
          <table id="smpush-quick-stats">
            <tr>
              <td>
                <img src="<?php echo smpush_imgpath; ?>/down.png" />
                <p><?php echo __('Gross Subscribers', 'smpush-plugin-lang') ?></p>
                <span><?php echo self::$reports['platforms']['total']?></span>
              </td>
              <td>
                <img src="<?php echo smpush_imgpath; ?>/up.png" />
                <p><?php echo __('Active Subscribers', 'smpush-plugin-lang') ?></p>
                <span><?php echo (self::$reports['platforms']['total']-(self::$reports['platforms']['devices']-self::$reports['platforms']['total']))?></span>
              </td>
            </tr>
          </table>
          <br class="clear">
          <div class="inside smpush-center" style="display: inline-block;width:46%;text-align:center">
            <canvas id="smpushChartDStatus" style="margin:auto"></canvas>
          </div>
          <div class="inside smpush-center" style="display: inline-block;width:49%;text-align:center">
            <canvas id="smpushChartPlats" style="margin:auto"></canvas>
          </div>
      </div>
    </div>
  </div>
</div>

<div class="metabox-holder" style="display: inline-block;width:49%;">
  <div class="postbox-container" style="width:100%;">
    <div class="meta-box-sortables">
      <div class="postbox">
        <h3><label><?php echo __('Devices', 'smpush-plugin-lang') ?></label></h3>
        <div class="inside smpush-center" style="width:95%;text-align:center">
          <canvas id="smpushChartDevices" style="margin:auto"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<?php endif;?>

<div class="metabox-holder" style="display: inline-block;width:49%;">
  <div class="postbox-container" style="width:100%;">
    <div class="meta-box-sortables">
      <div class="postbox">
        <h3><label><?php echo __('Engagement', 'smpush-plugin-lang') ?></label></h3>
        <div class="inside smpush-center" style="width:95%;text-align:center">
          <canvas id="smpushChartEMsgs" style="margin:auto"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="metabox-holder" style="display: inline-block;width:49%;">
  <div class="postbox-container" style="width:100%;">
    <div class="meta-box-sortables">
      <div class="postbox">
        <h3><label><?php echo __('Total Messages', 'smpush-plugin-lang') ?></label></h3>
        <div class="inside smpush-center" style="width:95%;text-align:center">
          <canvas id="smpushChartTMsgs" style="margin:auto"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="metabox-holder" style="display: inline-block;width:49%;">
  <div class="postbox-container" style="width:100%;">
    <div class="meta-box-sortables">
      <div class="postbox">
        <h3><label><?php echo __('Normal Messages', 'smpush-plugin-lang') ?></label></h3>
        <div class="inside smpush-center" style="width:95%;text-align:center">
          <canvas id="smpushChartNMsgs" style="margin:auto"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="metabox-holder" style="display: inline-block;width:49%;">
  <div class="postbox-container" style="width:100%;">
    <div class="meta-box-sortables">
      <div class="postbox">
        <h3><label><?php echo __('Schedule Messages', 'smpush-plugin-lang') ?></label></h3>
        <div class="inside smpush-center" style="width:95%;text-align:center">
          <canvas id="smpushChartSMsgs" style="margin:auto"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="metabox-holder" style="display: inline-block;width:49%;">
  <div class="postbox-container" style="width:100%;">
    <div class="meta-box-sortables">
      <div class="postbox">
        <h3><label><?php echo __('Geo-Fence Messages', 'smpush-plugin-lang') ?></label></h3>
        <div class="inside smpush-center" style="width:95%;text-align:center">
          <canvas id="smpushChartGMsgs" style="margin:auto"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  jQuery(document).ready(function () {
    var smpush_chart_options = {
      scales: {
        xAxes: [{
            type: 'time',
            position: 'bottom',
            time: {
              unit: 'day',
              displayFormats: {
                'day': 'DD',
              }
            }
          }]
      }
    };
    var smpush_success_style = {
      borderColor: "#75d491",
      backgroundColor: "#75d491",
      fill: false,
    };
    var smpush_fail_style = {
      borderColor: "#FF6384",
      backgroundColor: "#FF6384",
      fill: false,
    };

    new Chart.Line(jQuery("#smpushChartNMsgs"), {
      data: {
        datasets: [{borderColor: "#75d491",      backgroundColor: "#75d491",      fill: false,
            label: '<?php echo __('Delivered Messages', 'smpush-plugin-lang') ?>',
            data: [
              <?php
              for($i = $stat_date['start']; $i <= $stat_date['end']; $i = $i + 86400){
                $today = date('Y-m-d', $i);
                if(!empty(self::$reports[$today]['smsg'])){
                  echo '{x: "'.date('M d Y', $i).'", y: '.self::$reports[$today]['smsg'].'},';
                }
                else{
                  echo '{x: "'.date('M d Y', $i).'", y: 0},';
                }
              }
              ?>
            ]
          }, { borderColor: "#FF6384",      backgroundColor: "#FF6384",      fill: false,
            label: '<?php echo __('Failed Messages', 'smpush-plugin-lang') ?>',
            data: [
              <?php
              for($i = $stat_date['start']; $i <= $stat_date['end']; $i = $i + 86400){
                $today = date('Y-m-d', $i);
                if(!empty(self::$reports[$today]['fmsg'])){
                  echo '{x: "'.date('M d Y', $i).'", y: '.self::$reports[$today]['fmsg'].'},';
                }
                else{
                  echo '{x: "'.date('M d Y', $i).'", y: 0},';
                }
              }
              ?>
            ]
          }]
      },
      options: smpush_chart_options
    });

    new Chart.Line(jQuery("#smpushChartSMsgs"), {
      data: {
        datasets: [{borderColor: "#75d491",      backgroundColor: "#75d491",      fill: false,
            label: '<?php echo __('Delivered Messages', 'smpush-plugin-lang') ?>',
            data: [
              <?php
              for($i = $stat_date['start']; $i <= $stat_date['end']; $i = $i + 86400){
                $today = date('Y-m-d', $i);
                if(!empty(self::$reports[$today]['sschmsg'])){
                  echo '{x: "'.date('M d Y', $i).'", y: '.self::$reports[$today]['sschmsg'].'},';
                }
                else{
                  echo '{x: "'.date('M d Y', $i).'", y: 0},';
                }
              }
              ?>
            ]
          }, { borderColor: "#FF6384",      backgroundColor: "#FF6384",      fill: false,
            label: '<?php echo __('Failed Messages', 'smpush-plugin-lang') ?>',
            data: [
              <?php
              for($i = $stat_date['start']; $i <= $stat_date['end']; $i = $i + 86400){
                $today = date('Y-m-d', $i);
                if(!empty(self::$reports[$today]['fschmsg'])){
                  echo '{x: "'.date('M d Y', $i).'", y: '.self::$reports[$today]['fschmsg'].'},';
                }
                else{
                  echo '{x: "'.date('M d Y', $i).'", y: 0},';
                }
              }
              ?>
            ]
          }]
      },
      options: smpush_chart_options
    });
    
    new Chart.Line(jQuery("#smpushChartGMsgs"), {
      data: {
        datasets: [{borderColor: "#75d491",      backgroundColor: "#75d491",      fill: false,
            label: '<?php echo __('Delivered Messages', 'smpush-plugin-lang') ?>',
            data: [
              <?php
              for($i = $stat_date['start']; $i <= $stat_date['end']; $i = $i + 86400){
                $today = date('Y-m-d', $i);
                if(!empty(self::$reports[$today]['sgeomsg'])){
                  echo '{x: "'.date('M d Y', $i).'", y: '.self::$reports[$today]['sgeomsg'].'},';
                }
                else{
                  echo '{x: "'.date('M d Y', $i).'", y: 0},';
                }
              }
              ?>
            ]
          }, { borderColor: "#FF6384",      backgroundColor: "#FF6384",      fill: false,
            label: '<?php echo __('Failed Messages', 'smpush-plugin-lang') ?>',
            data: [
              <?php
              for($i = $stat_date['start']; $i <= $stat_date['end']; $i = $i + 86400){
                $today = date('Y-m-d', $i);
                if(!empty(self::$reports[$today]['fgeomsg'])){
                  echo '{x: "'.date('M d Y', $i).'", y: '.self::$reports[$today]['fgeomsg'].'},';
                }
                else{
                  echo '{x: "'.date('M d Y', $i).'", y: 0},';
                }
              }
              ?>
            ]
          }]
      },
      options: smpush_chart_options
    });
              
    new Chart.Line(jQuery("#smpushChartEMsgs"), {
      data: {
        datasets: [{borderColor: "#75d491",      backgroundColor: "#75d491",      fill: false,
            label: '<?php echo __('Views', 'smpush-plugin-lang') ?>',
            data: [
              <?php
              for($i = $stat_date['start']; $i <= $stat_date['end']; $i = $i + 86400){
                $today = date('Y-m-d', $i);
                if(!empty(self::$reports[$today]['views'])){
                  echo '{x: "'.date('M d Y', $i).'", y: '.self::$reports[$today]['views'].'},';
                }
                else{
                  echo '{x: "'.date('M d Y', $i).'", y: 0},';
                }
              }
              ?>
            ]
          }, { borderColor: "#FF6384",      backgroundColor: "#FF6384",      fill: false,
            label: '<?php echo __('Clicks', 'smpush-plugin-lang') ?>',
            data: [
              <?php
              for($i = $stat_date['start']; $i <= $stat_date['end']; $i = $i + 86400){
                $today = date('Y-m-d', $i);
                if(!empty(self::$reports[$today]['clicks'])){
                  echo '{x: "'.date('M d Y', $i).'", y: '.self::$reports[$today]['clicks'].'},';
                }
                else{
                  echo '{x: "'.date('M d Y', $i).'", y: 0},';
                }
              }
              ?>
            ]
          }]
      },
      options: smpush_chart_options
    });
    
    new Chart.Line(jQuery("#smpushChartTMsgs"), {
      data: {
        datasets: [{borderColor: "#75d491",      backgroundColor: "#75d491",      fill: false,
            label: '<?php echo __('Delivered Messages', 'smpush-plugin-lang') ?>',
            data: [
              <?php
              for($i = $stat_date['start']; $i <= $stat_date['end']; $i = $i + 86400){
                $today = date('Y-m-d', $i);
                if(!empty(self::$reports[$today]['totals'])){
                  echo '{x: "'.date('M d Y', $i).'", y: '.self::$reports[$today]['totals'].'},';
                }
                else{
                  echo '{x: "'.date('M d Y', $i).'", y: 0},';
                }
              }
              ?>
            ]
          }, { borderColor: "#FF6384",      backgroundColor: "#FF6384",      fill: false,
            label: '<?php echo __('Failed Messages', 'smpush-plugin-lang') ?>',
            data: [
              <?php
              for($i = $stat_date['start']; $i <= $stat_date['end']; $i = $i + 86400){
                $today = date('Y-m-d', $i);
                if(!empty(self::$reports[$today]['totalf'])){
                  echo '{x: "'.date('M d Y', $i).'", y: '.self::$reports[$today]['totalf'].'},';
                }
                else{
                  echo '{x: "'.date('M d Y', $i).'", y: 0},';
                }
              }
              ?>
            ]
          }]
      },
      options: smpush_chart_options
    });
    
    <?php if(empty($_GET['msgid'])):?>
    new Chart.Line(jQuery("#smpushChartDevices"), {
      data: {
        datasets: [{
            borderColor: "#75d491",
            backgroundColor: "#75d491",
            fill: false,
            label: '<?php echo __('Active', 'smpush-plugin-lang') ?>',
            data: [
              <?php
              for($i = $stat_date['start']; $i <= $stat_date['end']; $i = $i + 86400){
                $today = date('Y-m-d', $i);
                if(!empty(self::$reports[$today]['newdevice'])){
                  echo '{x: "'.date('M d Y', $i).'", y: '.self::$reports[$today]['newdevice'].'},';
                }
                else{
                  echo '{x: "'.date('M d Y', $i).'", y: 0},';
                }
              }
              ?>
            ]
          }, {
            fill: false,
            borderColor: "#CCC",
            backgroundColor: "#CCC",
            label: '<?php echo __('Dead', 'smpush-plugin-lang') ?>',
            data: [
              <?php
              for($i = $stat_date['start']; $i <= $stat_date['end']; $i = $i + 86400){
                $today = date('Y-m-d', $i);
                if(!empty(self::$reports[$today]['invdevice'])){
                  echo '{x: "'.date('M d Y', $i).'", y: '.self::$reports[$today]['invdevice'].'},';
                }
                else{
                  echo '{x: "'.date('M d Y', $i).'", y: 0},';
                }
              }
              ?>
            ]
          }]
      },
      options: smpush_chart_options
    });
    
    new Chart(jQuery("#smpushChartDStatus"), {
      type: 'doughnut',
      data:{
      labels: ['<?php echo __('Active', 'smpush-plugin-lang') ?>','<?php echo __('Dead', 'smpush-plugin-lang') ?>'],
      datasets: [{
        backgroundColor: ["#75d491","#CCC"],
        data: [<?php echo self::$reports['platforms']['total']?>,<?php echo (self::$reports['platforms']['devices']-self::$reports['platforms']['total'])?>]
      }]
      }
    });
    
    new Chart(jQuery("#smpushChartPlats"), {
      type: 'doughnut',
      data:{
      labels: [<?php foreach(self::$platform_titles as $platform){echo '"'.$platform.'",';}?>],
      datasets: [{
        backgroundColor: ["#20B2AA","#b0e0e6","#a52a2a","#ffdab9","#FF00FF","#ADFF2F","#D2691E","#708090","#61ee9c","#7d3a11","#36ec23","#2e91fe","#c250c6","#053f5e","#889088"],
        data: [<?php foreach(self::$platforms as $platform){echo (empty(self::$reports['platforms'][$platform]))? '0,' : '"'.self::$reports['platforms'][$platform].'",';}?>]
      }]
      },
      options: {legend: {position:'right'}}
    });
    <?php endif;?>

    new Chart(jQuery("#smpushChartTotalMsgs"), {
      type: 'doughnut',
      data:{
      labels: ['<?php echo __('Delivered', 'smpush-plugin-lang') ?>','<?php echo __('Failed', 'smpush-plugin-lang') ?>'],
      datasets: [{
        backgroundColor: ["#75d491","#FF6384"],
        data: [<?php echo self::$reports['platforms']['smsgs']?>,<?php echo self::$reports['platforms']['fmsgs']?>]
      }]
      }
    });
    
    new Chart(jQuery("#smpushChartClickRate"), {
      type: 'doughnut',
      data:{
      labels: ['<?php echo __('Views', 'smpush-plugin-lang') ?>','<?php echo __('Clicks', 'smpush-plugin-lang') ?>'],
      datasets: [{
        backgroundColor: ["#75d491","#FF6384"],
        data: [<?php echo self::$reports['platforms']['views']?>,<?php echo self::$reports['platforms']['clicks']?>]
      }]
      }
    });

  });
</script>