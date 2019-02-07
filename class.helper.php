<?php

class smpush_helper {
  public $ParseOutput;
  public $internalAPI;
  public $curl_status;
  public $curl_error;
  public static $returnValue;
  public static $staticResult;
  public static $paging = array(
  'stillmore' => 0,
  'perpage' => 0,
  'callpage' => 0,
  'next' => 0,
  'previous' => 0,
  'pages' => 0,
  'result' => 0
  );

  public function __construct(){}
    
  public function buildCurl($url, $ssl = false, $postfields = false) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.6 (KHTML, like Gecko) Chrome/16.0.897.0 Safari/535.6');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 40);
    curl_setopt($ch, CURLOPT_TIMEOUT, 600);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    if ($ssl !== false) {
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
      curl_setopt($ch, CURLOPT_CAINFO, smpush_dir.'/lib/cacert.pem');
    } else {
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
    if(!empty($postfields)){
      curl_setopt($ch, CURLOPT_POST, true);
      if(is_array($postfields)){
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
      }
    }
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    if(defined('WP_PROXY_HOST')){
      curl_setopt($ch, CURLOPT_PROXY, WP_PROXY_HOST);
      curl_setopt($ch, CURLOPT_PROXYPORT, WP_PROXY_PORT);
      curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
      if(defined('WP_PROXY_USERNAME')){
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME.':'.WP_PROXY_PASSWORD);
        curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_ANY);
      }
    }
    $result = curl_exec($ch);
    $this->curl_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $this->curl_error = curl_error($ch);
    curl_close($ch);
    return $result;
  }

  public static function remain_time($target_time, $nowtime=false) {
    $return = '';
    if(empty($nowtime)){
      $nowtime = current_time('timestamp');
    }
    else{
      $nowtime = strtotime($nowtime);
    }
    $diff = strtotime($target_time) - $nowtime;
    $temp = $diff/86400; // 60 sec/min*60 min/hr*24 hr/day=86400 sec/day
    $days = floor($temp);
    if($days > 0){
      $return .= $days.' '.(($days > 1)? __('days') : __('day'));
    }
    $temp = 24*($temp-$days);
    $hours = floor($temp);
    if($hours > 0){
      $return .= ' '.$hours.' '.(($hours > 1)? __('hours') : __('hour'));
    }
    $temp = 60*($temp-$hours);
    $minutes = floor($temp);
    if($minutes > 0){
      $return .= ' '.$minutes.' '.(($minutes > 1)? __('minutes') : __('minute'));
    }
    return trim($return);
  }
  
  public static function touch_time( $edit = false, $tab_index = 0, $multi = 0 ) {
    global $wp_locale;
    $tab_index_attribute = '';
    if ( (int) $tab_index > 0 )
    $tab_index_attribute = " tabindex=\"$tab_index\"";

    $time_adj = current_time('timestamp');
    $jj = ($edit) ? mysql2date( 'd', $post_date, false ) : gmdate( 'd', $time_adj );
    $mm = ($edit) ? mysql2date( 'm', $post_date, false ) : gmdate( 'm', $time_adj );
    $aa = ($edit) ? mysql2date( 'Y', $post_date, false ) : gmdate( 'Y', $time_adj );
    $hh = ($edit) ? mysql2date( 'H', $post_date, false ) : gmdate( 'H', $time_adj );
    $mn = ($edit) ? mysql2date( 'i', $post_date, false ) : gmdate( 'i', $time_adj );
    $ss = ($edit) ? mysql2date( 's', $post_date, false ) : gmdate( 's', $time_adj );

    $month = "<select " . ( $multi ? '' : 'id="mm" ' ) . "name=\"smiotime_mm\"$tab_index_attribute>\n";
    for ( $i = 1; $i < 13; $i = $i +1 ) {
    $monthnum = zeroise($i, 2);
    $month .= "\t\t\t" . '<option value="' . $monthnum . '"';
    if ( $i == $mm )
    $month .= ' selected="selected"';
    /* translators: 1: month number (01, 02, etc.), 2: month abbreviation */
    $month .= '>' . sprintf( __( '%1$s-%2$s' ), $monthnum, $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) ) . "</option>\n";
    }
    $month .= '</select>';

    $day = '<input type="text" ' . ( $multi ? '' : 'id="jj" ' ) . 'name="smiotime_jj" value="' . $jj . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" />';
    $year = '<input style="width: 41px;" type="text" ' . ( $multi ? '' : 'id="aa" ' ) . 'name="smiotime_aa" value="' . $aa . '" size="4" maxlength="4"' . $tab_index_attribute . ' autocomplete="off" />';
    $hour = '<input type="text" ' . ( $multi ? '' : 'id="hh" ' ) . 'name="smiotime_hh" value="' . $hh . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" />';
    $minute = '<input type="text" ' . ( $multi ? '' : 'id="mn" ' ) . 'name="smiotime_mn" value="' . $mn . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" />';

    echo '<div class="timestamp-wrap">';
    /* translators: 1: month, 2: day, 3: year, 4: hour, 5: minute */
    printf( __( '%1$s %2$s, %3$s @ %4$s : %5$s' ), $month, $day, $year, $hour, $minute );

    echo '</div><input type="hidden" id="ss" name="smiotime_ss" value="' . $ss . '" />';

    if ( $multi ) return;

    echo "\n\n";
    foreach ( array('mm', 'jj', 'aa', 'hh', 'mn') as $timeunit ) {
      echo '<input type="hidden" id="hidden_' . $timeunit . '" name="smiotime_hidden_' . $timeunit . '" value="' . $$timeunit . '" />' . "\n";
      $cur_timeunit = 'cur_' . $timeunit;
      echo '<input type="hidden" id="'. $cur_timeunit . '" name="smiotime_'. $cur_timeunit . '" value="' . $$cur_timeunit . '" />' . "\n";
    }
  }

  public static function freezeCache(){
    global $_wp_using_ext_object_cache;
    $_wp_using_ext_object_cache = null;
    $_SERVER['COMET_CACHE_ALLOWED'] = FALSE;
    if(! defined('DONOTCACHEPAGE')){
      define('DONOTCACHEPAGE', TRUE);
    }
    if(! defined('DONOTCACHEPAGE')){
      define('COMET_CACHE_ALLOWED', FALSE);
    }
  }
  
  public static function Paging($sql, $db){
  	if(isset($_REQUEST['perpage'])) $limit = $_REQUEST['perpage'];
  	else $limit = 20;
  	if(isset($_REQUEST['callpage'])) $currentpage = $_REQUEST['callpage'];
  	else $currentpage = 1;

    if(preg_match('/group by ([a-zA-Z0-9`*(),._\n\r]+)\s?/i', $sql, $match)){
      $cselect = 'DISTINCT('.$match[1].')';
      $countsql = preg_replace('/group by ([a-zA-Z0-9`*(),._\n\r\s]+)\s?/i', '', $sql);
    }
    else{
      $cselect = '*';
      $countsql = $sql;
    }
    $countsql = preg_replace('/select ([a-zA-Z0-9`*(),._\n\r\s]+) from/i', 'SELECT COUNT('.$cselect.') FROM', $countsql);
    $count = $db->get_var($countsql);
    if($db->num_rows > 1)
        $count = $db->num_rows;
    if($count == 0)
        return;
  	$pages = $count/$limit;
  	$pages = ceil($pages);

  	if($currentpage < $pages)
  		self::$paging['stillmore'] = 1;
  	else{
  		$currentpage = $pages;
  		self::$paging['stillmore'] = 0;
  	}
  	if($currentpage == 1){
  		self::$paging['previous'] = 0;
  		self::$paging['next'] = $currentpage+1;
  	}
  	elseif($currentpage == $pages){
  		self::$paging['previous'] = $currentpage-1;
  		self::$paging['next'] = 0;
  	}
  	else{
  		self::$paging['previous'] = $currentpage-1;
  		self::$paging['next'] = $currentpage+1;
  	}

    self::$paging['result'] = $count;
    self::$paging['pages'] = $pages;
    self::$paging['perpage'] = $limit;
    self::$paging['page'] = $currentpage;

  	if($currentpage > 0) $currentpage--;
  	$from = $currentpage*$limit;
  	return $sql." LIMIT $from,$limit";
  }

  public function output($respond, $result){
    if(!$this->ParseOutput || $this->internalAPI){
      $this->ParseOutput = true;
      if(is_array($result))
        return $result;
      else
        return array();
    }
    self::jsonPrint($respond, $result);
  }

  public static function jsonPrint($respond, $result){
    $json = array();
  	if(is_array($result)){
  		$json['respond'] = $respond;
        $json['paging'] = self::$paging;
        $json['message'] = '';
        $json['result'] = $result;
  	}
  	else{
  		$json['respond'] = $respond;
        $json['paging'] = self::$paging;
  		$json['message'] = $result;
        $json['result'] = array();
  	}
    if(self::$returnValue == 'cronjob'){
      if($respond == 0){
        smpush_cronsend::writeLog($json['message']);
        die();
      }
      else{
        return;
      }
    }
    elseif(self::$returnValue){
      self::$staticResult = array('respond' => $respond, 'result' => $result);
      return true;
    }
    header('Content-Type: application/json');
    if(!empty($_GET['callback'])){
      echo $_GET['callback'].'('.json_encode($json).')';
    }else{
      echo json_encode($json);
    }
  	die();
  }

  public function fetchPrintResult(){
    return self::$staticResult;
  }

  public static function processGDPRText($privacy, $terms, $statement){
    if(!empty($terms) || !empty($privacy)){
      $terms = '<a href="'.get_bloginfo('url').'/'.$terms.'" target="_blank">${1}</a>';
      $privacy = '<a href="'.get_bloginfo('url').'/'.$privacy.'" target="_blank">${1}</a>';
      $statement = preg_replace('/\#([a-zA-Z\s]+)\#/', $privacy, $statement, 1);
      $statement = preg_replace('/\#([a-zA-Z\s]+)\#/', $terms, $statement);
      return $statement;
    }
    else{
      return $statement;
    }
  }

  public function queryBuild($sql, $arg){
    if(isset($arg['like'])){
      foreach($arg['like'] AS $index=>$value)
        $where[] = SMPUSHTBPRE."$index LIKE '$value'";
    }
    if(isset($arg['in'])){
      foreach($arg['in'] AS $index=>$value)
        $where[] = SMPUSHTBPRE."$index IN ($value)";
    }
    if(isset($arg['notin'])){
      foreach($arg['notin'] AS $index=>$value)
        $where[] = SMPUSHTBPRE."$index NOT IN ($value)";
    }
    if(isset($arg['between'])){
      foreach($arg['between'] AS $index=>$value)
        $where[] = SMPUSHTBPRE."$index='$value' BETWEEN $value[0] AND $value[1]";
    }
    if(isset($arg['date'])){
      foreach($arg['date'] AS $tb=>$value){
        foreach($value AS $index=>$key)
            $where[] = "$key[index](".SMPUSHTBPRE."$tb)='$key[value]'";
      }
    }
    if(isset($arg['where'])){
      foreach($arg['where'] AS $index=>$value)
        $where[] = SMPUSHTBPRE."$index='$value'";
    }
    if(isset($where))
        $where = 'WHERE '.implode(' AND ', $where);
    else
        $where = '';
    if(isset($arg['orderby']))
        $order = 'ORDER BY '.SMPUSHTBPRE.$arg['orderby'].' '.$arg['order'];
    else
        $order = '';
    return str_replace(array('{where}','{order}'), array($where, $order), $sql);
  }

  public function checkReqHeader($detect){
    $return = false;
    if (!function_exists('apache_request_headers') && !function_exists('getallheaders')) {
      function apache_request_headers() {
        $arh = array();
        $rx_http = '/\AHTTP_/';
        foreach ($_SERVER as $key => $val) {
          if (preg_match($rx_http, $key)) {
            $arh_key = preg_replace($rx_http, '', $key);
            $rx_matches = array();
            $rx_matches = explode('_', $arh_key);
            if (count($rx_matches) > 0 and strlen($arh_key) > 2) {
              foreach ($rx_matches as $ak_key => $ak_val)
                $rx_matches[$ak_key] = ucfirst($ak_val);
              $arh_key = implode('-', $rx_matches);
            }
            $arh[$arh_key] = $val;
          }
        }
        return( $arh );
      }
    }
    if (function_exists('getallheaders')){
      foreach(getallheaders() as $name => $value){
        if($name == $detect){
          $return = $value;
        }
      }
    }
    elseif (function_exists('apache_request_headers')){
      foreach(apache_request_headers() as $name => $value){
        if($name == $detect){
          $return = $value;
        }
      }
    }
    if(empty($return) && !empty($_REQUEST[$name])){
      return $_REQUEST[$name];
    }
    return $return;
  }
  
  public function CheckParams($params, $or=false){
    if(! is_array($params)){
        $this->output(0, 'Parameters `'.$params.'` is required');
    }
    $indexes = '';
    foreach($params AS $param){
        if(!isset($_REQUEST[$param]) OR empty($_REQUEST[$param])){
            if($or) $indexes[] = $param;
            else $this->output(0, __('Parameter', 'smpush-plugin-lang').' `'.$param.'` '.__('is required, All required parameters are', 'smpush-plugin-lang').' `'.implode($params, '`,`').'`');
        }
        elseif($or) return;
    }
    if($or){
        $this->output(0, __('Parameters', 'smpush-plugin-lang').' `'.implode($params, '`,`').'` '.__('at least one of them is required', 'smpush-plugin-lang'));
    }
  }

  public static function cleanString($string, $processSmiley=false){
    if($processSmiley){
      $string = trim(htmlspecialchars_decode(stripslashes($string)));
      return html_entity_decode(preg_replace("/U\+([0-9A-F]{4,5})/i", "&#x\\1;", $string), ENT_NOQUOTES, 'UTF-8');
    }
    else{
      return trim(htmlspecialchars_decode(stripslashes($string)));
    }
  }
  
  public static function ShortString($string, $charcount){
    $string = strip_tags($string);
    $lenght = strlen($string);
    if($lenght > $charcount){
      $string = substr($string, 0, $charcount).'...';
      return $string;
    }
    else{
      return $string;
    }
  }
  
  public static function log($message){
    if(smpush_env_demo === true){
      return;
    }
    if(is_array($message) || is_object($message)){
      $message = json_encode($message, defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
    }
    $message = gmdate('d/m/y H:i:s').' : '.$message;
    $message .= "\n==============================================";
    $message .= "\n";
    error_log($message, 3, smpush_dir.'/cron_log.log');
  }
  
  public function getDomain($url) {
    preg_match('/[a-z0-9\-]{1,63}\.?[a-z\.]{2,6}?$/', parse_url($url, PHP_URL_HOST), $_domain_tld);
    return $_domain_tld[0];
  }
  
  function stripslashes_deep( $value ) {
    return $this->map_deep($value, 'stripslashes_from_strings_only' );
  }

  function stripslashes_from_strings_only( $value ) {
    return is_string( $value ) ? stripslashes( $value ) : $value;
  }

  function map_deep( $value, $callback ) {
    if ( is_array( $value ) ) {
      foreach ( $value as $index => $item ) {
        $value[ $index ] = $this->map_deep( $item, $callback );
      }
    } elseif ( is_object( $value ) ) {
      $object_vars = get_object_vars( $value );
      foreach ( $object_vars as $property_name => $property_value ) {
        $value->$property_name = $this->map_deep( $property_value, $callback );
      }
    } else {
      $value = call_user_func( array($this, $callback), $value );
    }
    return $value;
  }

  private function delDir($dir, $filesonly=false) {
    $structure = glob(rtrim($dir, "/").'/*');
    if(is_array($structure)){
      foreach($structure as $file){
        if (is_dir($file)){
          $this->delDir($file);
        }
        elseif (is_file($file)){
          @unlink($file);
        }
      }
    }
    if($filesonly === false){
      @rmdir($dir);
    }
  }
  
  public function buildSafariPackFile($settings) {
    $upload_dir = wp_upload_dir();
    $pack_folder = $upload_dir['basedir'].'/certifications/safari_auth';
    if (file_exists($pack_folder)) {
      $this->delDir($pack_folder);
    }
    mkdir($pack_folder);
    mkdir($pack_folder.'/icon.iconset');

    $upload_dir = wp_upload_dir();
    $safari_icon = explode('/wp-content/uploads/', $settings['safari_icon']);
    $settings['safari_icon'] = $upload_dir['basedir'].'/'.$safari_icon[1];
    
    $image = wp_get_image_editor($settings['safari_icon']);
    if(is_wp_error($image)){
      echo 'icon resize errror: '.$image->get_error_message().' @ '.$settings['safari_icon'];
      exit;
    }
    $image->resize(16, 16, true);
    $image->save($pack_folder.'/icon.iconset/icon_16x16.png');
    
    $image = wp_get_image_editor($settings['safari_icon']);
    $image->resize(32, 32, true);
    $image->save($pack_folder.'/icon.iconset/icon_16x16@2x.png');
    
    $image = wp_get_image_editor($settings['safari_icon']);
    $image->resize(32, 32, true);
    $image->save($pack_folder.'/icon.iconset/icon_32x32.png');
    
    $image = wp_get_image_editor($settings['safari_icon']);
    $image->resize(64, 64, true);
    $image->save($pack_folder.'/icon.iconset/icon_32x32@2x.png');
    
    $image = wp_get_image_editor($settings['safari_icon']);
    $image->resize(128, 128, true);
    $image->save($pack_folder.'/icon.iconset/icon_128x128.png');
    
    $image = wp_get_image_editor($settings['safari_icon']);
    $image->resize(256, 256, true);
    $image->save($pack_folder.'/icon.iconset/icon_128x128@2x.png');

    $websitejson = array(
    'websiteName' => get_bloginfo('name'),
    'websitePushID' => $settings['safari_web_id'],
    'allowedDomains' => array('http://'.parse_url(get_bloginfo('url'), PHP_URL_HOST), 'https://'.parse_url(get_bloginfo('url'), PHP_URL_HOST)),
    'urlFormatString' => 'https://'.parse_url(get_bloginfo('url'), PHP_URL_HOST).'/%@',
    'authenticationToken' => md5(time()),
    'webServiceURL' => get_bloginfo('url').'/'.$settings['push_basename'].'/safari'
    );
    $this->storelocalfile($pack_folder.'/website.json', json_encode($websitejson));

    $manifest = array();
    $raw_files = array(
    'icon.iconset/icon_16x16.png',
    'icon.iconset/icon_16x16@2x.png',
    'icon.iconset/icon_32x32.png',
    'icon.iconset/icon_32x32@2x.png',
    'icon.iconset/icon_128x128.png',
    'icon.iconset/icon_128x128@2x.png',
    'website.json'
    );
    foreach ($raw_files as $raw_file) {
      $manifest[$raw_file] = sha1($this->readlocalfile($pack_folder.'/'.$raw_file));
    }
    $this->storelocalfile($pack_folder.'/manifest.json', json_encode($manifest));

    $pkcs12 = $this->readlocalfile($settings['safari_certp12_path']);
    $certs = array();
    if (!openssl_pkcs12_read($pkcs12, $certs, $settings['safari_passphrase'])) {
      echo ('wrong safari certification password');
      exit;
    }
    $signature_path = $pack_folder.'/signature';
    // Sign the manifest.json file with the private key from the certificate
    $cert_data = openssl_x509_read($certs['cert']);
    $private_key = openssl_pkey_get_private($certs['pkey'], $settings['safari_passphrase']);
    openssl_pkcs7_sign($pack_folder.'/manifest.json', $signature_path, $cert_data, $private_key, array(), PKCS7_BINARY | PKCS7_DETACHED, smpush_dir.'/lib/AppleWWDRCA.pem');
    // Convert the signature from PEM to DER
    $signature_pem = $this->readlocalfile($signature_path);
    $matches = array();
    if (!preg_match('~Content-Disposition:[^\n]+\s*?([A-Za-z0-9+=/\r\n]+)\s*?-----~', $signature_pem, $matches)) {
      echo ('wrong safari certification type');
      exit;
    }
    $signature_der = base64_decode($matches[1]);
    $this->storelocalfile($signature_path, $signature_der);

    $upload_dir = wp_upload_dir();
    $zip_path = $upload_dir['basedir'].'/certifications/safari_pack_connection_'.get_current_blog_id().'.zip';
    @unlink($zip_path);

    if(! class_exists('ZipArchive')){
      echo 'ZipArchive is not supported';
      exit;
    }

    $zip = new ZipArchive();
    if (!$zip->open($zip_path, ZIPARCHIVE::CREATE)) {
      echo ('Could not create '.$zip_path);
      exit;
    }
    $raw_files[] = 'manifest.json';
    $raw_files[] = 'signature';
    foreach ($raw_files as $raw_file) {
      $zip->addFile($pack_folder.'/'.$raw_file, $raw_file);
    }
    $zip->close();
    $this->delDir($pack_folder);
    return $zip_path;
  }
  
  public function readlocalfile($path) {
    if(function_exists('file_get_contents')){
      $content = file_get_contents($path);
    }
    elseif(function_exists('fopen') && function_exists('stream_get_contents')){
      $handler = fopen($path, 'rb');
      $content = stream_get_contents($handler);
      fclose($handler);
    }
    elseif(function_exists('readfile')){
      $content = readfile($path);
    }
    else{
      error_log('Server closes all saving functions fopen(), readfile(), file_get_contents() !');
      return false;
    }
    return $content;
  }
  
  public function storelocalfile($path, $contents) {
    if(function_exists('fopen')){
      $handle = fopen($path, 'w');
      fwrite($handle, $contents);
      fclose($handle);
    }
    elseif(function_exists('file_put_contents')){
      file_put_contents($path, $contents);
    }
    else{
      error_log('Server closes all saving functions fopen(), file_put_contents() !');
    }
  }
  
  public static function saltHash($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }
  
  function ShortHTMLString($html, $maxLength, $isUtf8 = true){
    $printedLength = 0;
    $position = 0;
    $tags = array();
    // For UTF-8, we need to count multibyte sequences as one character.
    $re = $isUtf8 ? '{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;|[\x80-\xFF][\x80-\xBF]*}' : '{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;}';
    while($printedLength < $maxLength && preg_match($re, $html, $match, PREG_OFFSET_CAPTURE, $position)){
      list($tag, $tagPosition) = $match[0];
      // Print text leading up to the tag.
      $str = substr($html, $position, $tagPosition - $position);
      if($printedLength + strlen($str) > $maxLength){
        print(substr($str, 0, $maxLength - $printedLength));
        $printedLength = $maxLength;
        break;
      }
      print($str);
      $printedLength += strlen($str);
      if($printedLength >= $maxLength)
        break;
      if($tag[0] == '&' || ord($tag) >= 0x80){
        // Pass the entity or UTF-8 multibyte sequence through unchanged.
        print($tag);
        $printedLength++;
      }
      else{
        // Handle the tag.
        $tagName = $match[1][0];
        if($tag[1] == '/'){
          // This is a closing tag.
          $openingTag = array_pop($tags);
          assert($openingTag == $tagName); // check that tags are properly nested.
          print($tag);
        }
        else if($tag[strlen($tag) - 2] == '/'){
          // Self-closing tag.
          print($tag);
        }
        else{
          // Opening tag.
          print($tag);
          $tags[] = $tagName;
        }
      }
      // Continue after the tag.
      $position = $tagPosition + strlen($tag);
    }
    // Print any remaining text.
    if($printedLength < $maxLength && $position < strlen($html))
      print(substr($html, $position, $maxLength - $printedLength));
    // Close any open tags.
    while(!empty($tags))
      printf('</%s>', array_pop($tags));
  }
  
  function parse_signed_request($signed_request, $secret) {
    list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

    // decode the data
    $sig = $this->base64_url_decode($encoded_sig);
    $data = json_decode($this->base64_url_decode($payload), true);

    // confirm the signature
    $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
    if ($sig !== $expected_sig) {
      print('Bad Signed JSON signature!');
      return null;
    }

    return $data;
  }

  private function base64_url_decode($input) {
    return base64_decode(strtr($input, '-_', '+/'));
  }

}