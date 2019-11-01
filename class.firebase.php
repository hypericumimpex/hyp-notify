<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class smpush_firebase {
  private $helper;
  private $apisettings;
  private $httpClient;
  private $firebase_config;
  private $access_token;
  private $bulkLoop;
  private $bulkPayload;
  private $bulkBoundary;
  private $bulkTokenID;
  private $invalidTokens;

  public function __construct($apisettings){
    $this->apisettings = $apisettings;
    $this->helper = new smpush_helper();
    $this->gcm = new smpush_gcm($apisettings);
    $this->reset();
  }

  private function getAccessToken(){
    $this->httpClient->post('https://iid.googleapis.com/v1/web/iid', ['json' => [] ]);
  }

  private function authorize(){
    require smpush_dir.'/lib/google/autoload.php';
    $client = new Google_Client();
    $this->firebase_config = json_decode($this->apisettings['firebase_config'], true);
    $client->useApplicationDefaultCredentials();
    try {
      $client->setAuthConfig($this->apisettings['firebase_auth_file']);
      $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
      $client->setTokenCallback(function ($cacheKey, $accessToken){
        $this->access_token = $accessToken;
        if(smpush_env == 'logs'){
          $this->helper->log('Generated access token: '. $accessToken);
        }
      });
      $this->httpClient = $client->authorize();
    } catch (Exception $e) {
      $this->helper->log('Caught exception: '.  $e->getMessage());
    }
  }

  public function subscribeToTopic($topic, $token){
    $header = array(
      "Authorization: key=".$this->apisettings['chrome_apikey'],
      "Content-Length: 0",
      "Content-Type: application/json"
    );
    $response = $this->helper->buildCurl('https://iid.googleapis.com/iid/v1/'.$token.'/rel/topics/'.$topic, false, true, $header);
    if(smpush_env == 'logs'){
      $this->helper->log('Token '.$token.' subscribed to topic `'.$topic.'`: '.$response);
    }
  }

  public function unsubscribeFromTopic($topic, $token){
    $header = array(
      "Authorization: key=".$this->apisettings['chrome_apikey'],
      "Content-Type: application/json"
    );
    $params = json_encode([
      'to' => '/topics/'.$topic,
      'registration_tokens' => [$token],
    ]);
    $response = $this->helper->buildCurl('https://iid.googleapis.com/iid/v1:batchRemove', false, $params, $header);
    if(smpush_env == 'logs'){
      $this->helper->log('Token '.$token.' unsubscribed from topic `'.$topic.'`: '.$response);
    }
  }

  public function sendToTopic($topic, $token, $notification){
    $this->authorize();

    $message = [
      "message" => [
        "topic" => $topic,
        //"token" => $_REQUEST['device_token'],
        "webpush" => [
          "notification" => $notification,
        ]
      ],
    ];

    // Send the Push Notification - use $response to inspect success or errors
    $response = $this->httpClient->post('https://fcm.googleapis.com/v1/projects/'.$this->firebase_config['projectId'].'/messages:send', ['json' => $message]);
    if(smpush_env == 'logs'){
      $this->helper->log('Send to token '.$token.': ' . (string)$response->getBody());
    }
  }

  public function sendToToken($token, $payload){
    $this->authorize();

    $message = [
      "message" => [
        "token" => $token,
        "webpush" => [
          "notification" => [
            $payload
          ]
        ]
      ],
    ];

    $response = $this->httpClient->post('https://fcm.googleapis.com/v1/projects/'.$this->firebase_config['projectId'].'/messages:send', ['json' => $message]);
    if(smpush_env == 'logs'){
      $this->helper->log('Send to token '.$token.': ' . (string)$response->getBody());
    }
  }

  public function convert($token){
    $this->authorize();

    $token = json_decode($token, true);
    $params = [
      'endpoint' => $token['endpoint'],
      'keys' => [
        'auth' => $token['auth'],
        'p256dh' => $token['p256dh']
      ]
    ];

    $response = $this->httpClient->post('https://iid.googleapis.com/v1/web/iid', ['json' => $params, 'headers' => ['Crypto-Key' => 'p256ecdsa='.$this->apisettings['chrome_vapid_public']] ]);
    if(smpush_env == 'logs'){
      $this->helper->log('import token in Firebase: ' . (string)$response->getBody());
    }
    $newtoken = json_decode((string)$response->getBody(), true);
    if(!empty($newtoken['token'])){
      return $newtoken['token'];
    } elseif(!empty($newtoken['error']['code']) && $newtoken['error']['code'] == 404){
      return false;
    } else {
      return '';
    }
  }

  public function sendToGroup($groupkey){
    $message = [
      "to" => "APA91bGeW_6nGXrdYql4dXe9Kc-lDaKiOhg-8diCaghs_FBwC0nXT0mcHy0FQLn6mmhhPuYDekcAhL7O7DD95i2N5lHnyA1SELhsqx2WXAfj1tm39bGxxKw",
      "notification" => [
        "title" => "بسم الله الرحمن الرحيم",
        "body" => "This is an FCM notification message!",
        "image" => 'https://smart-local.com/demo/media/files/2/71d897658abaaf607d0f27308be9f39d.png',
        "icon" => 'https://smart-local.com/demo/media/files/2/71d897658abaaf607d0f27308be9f39d.png',
        "dir" => 'rtl',
        "data" => [
          "click" => "",
          "target" => "https://apple.com"
        ],
      ],
    ];
    $response = $this->helper->buildCurl('https://fcm.googleapis.com/fcm/send', false, json_encode($message), array("Authorization: key=".$this->apisettings['chrome_apikey'], "Content-Type: application/json"));
    if(smpush_env == 'logs'){
      $this->helper->log('Send to group '.$groupkey.': '.$response);
    }
  }

  public function reset(){
    $this->gcm->reset();
    $this->invalidTokens = [];
    $this->bulkLoop = 0;
  }

  public function collectResponse(){
    if($this->bulkLoop < 500 && $this->bulkLoop > 0){
      $this->bulkRequest();
    }
    $invGCM = $this->gcm->collectResponse();
    if(!empty($invGCM)){
      $this->invalidTokens = array_merge($invGCM, $this->invalidTokens);
    }
    return $this->invalidTokens;
  }

  public function gcm($id, $token, $type){
    $this->gcm->queue($id, $token, $type);
  }

  private function bulkRequest(){
    $this->bulkPayload .= '--'.$this->bulkBoundary.'--';

    $response = $this->helper->buildCurl('https://fcm.googleapis.com/batch', false, $this->bulkPayload, array('Content-Type: multipart/mixed; boundary="'.$this->bulkBoundary.'"'));
    //$this->helper->log($this->bulkPayload);
    //$this->helper->log($response);
    if(smpush_env == 'logs'){
      preg_match_all('/--batch_(.*)\n/', $response, $sentBoundary);
      $response = explode($sentBoundary[0][0], $response);
      if(!empty($response)){
        $index = 0;
        foreach($response as $status){
          preg_match_all('/\{(.*)\}/s', $status, $matches);
          $json = json_decode($matches[0][0], true);
          if(!empty($json)){
            if(isset($json['error']['code']) && in_array($json['error']['code'], [404, 410])){
              array_push($this->invalidTokens, $this->bulkTokenID[$index]);
            }
            $index++;
          }
        }
      }
    }
    $this->bulkLoop = 0;
  }

  public function bulkSend($id, $token, $payload){
    if($this->bulkLoop == 0){
      if(empty($this->access_token)){
        $this->authorize();
        $this->getAccessToken();
      }
      $this->bulkBoundary = md5(uniqid());
      $this->bulkPayload = '';
      $this->bulkTokenID = [];
    }
    $this->bulkLoop++;

    $this->bulkTokenID[] = $id;

    $this->bulkPayload .= '--'.$this->bulkBoundary."\n"
    .'Content-Type: application/http'."\n"
    .'Content-Transfer-Encoding: binary'."\n"
    .'Authorization: Bearer '.$this->access_token."\n\n"
    .'POST /v1/projects/'.$this->firebase_config['projectId'].'/messages:send'."\n"
    .'Content-Type: application/json'."\n"
    .'accept: application/json'."\n\n"
    .'{'
    .'"message":{'
    .'"token":"'.$token.'",'
    .'"webpush":{'
    .'"notification":'.$payload
    .'}}}'
    ."\n";

    if($this->bulkLoop == 500){
      $this->bulkRequest();
    }

  }

}