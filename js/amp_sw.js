
const WorkerMessengerCommand = {
  AMP_SUBSCRIPTION_STATE: 'amp-web-push-subscription-state',
  AMP_SUBSCRIBE: 'amp-web-push-subscribe',
  AMP_UNSUBSCRIBE: 'amp-web-push-unsubscribe',
};

self.addEventListener('message', event => {
  const {command} = event.data;

  switch (command) {
    case WorkerMessengerCommand.AMP_SUBSCRIPTION_STATE:
      onMessageReceivedSubscriptionState();
      break;
    case WorkerMessengerCommand.AMP_SUBSCRIBE:
      onMessageReceivedSubscribe();
      break;
    case WorkerMessengerCommand.AMP_UNSUBSCRIBE:
      onMessageReceivedUnsubscribe();
      break;
  }
});

function onMessageReceivedSubscriptionState() {
  debugWriter("onMessageReceivedSubscriptionState");
  let retrievedPushSubscription = null;
  self.registration.pushManager
    .getSubscription()
    .then(pushSubscription => {
      retrievedPushSubscription = pushSubscription;
      if (!pushSubscription) {
        return null;
      } else {
        return self.registration.pushManager.permissionState(
          pushSubscription.options
        );
      }
    })
    .then(permissionStateOrNull => {
      if (permissionStateOrNull == null) {
        broadcastReply(WorkerMessengerCommand.AMP_SUBSCRIPTION_STATE, false);
      } else {
        debugWriter("granted");
        const isSubscribed = !!retrievedPushSubscription && permissionStateOrNull === 'granted';
        clearInterval(listenSWtimer);
        broadcastReply(WorkerMessengerCommand.AMP_SUBSCRIPTION_STATE, isSubscribed);
      }
    });
}

function endpointWorkaround(endpoint){
  let device_id = "";
  if(endpoint.indexOf("mozilla") > -1){
    device_id = endpoint.split("/")[endpoint.split("/").length-1];
  }
  else if(endpoint.indexOf("send/") > -1){
    device_id = endpoint.slice(endpoint.search("send/")+5);
  }
  debugWriter(device_id);
  return device_id;
}

function onMessageReceivedSubscribe() {
  debugWriter("onMessageReceivedSubscribe");
  listenSWtimerWork = true;
  let subsConfig = { userVisibleOnly: true };

  if(VAPID_WEB_PUSH == 1){
    const applicationServerKey = urlB64ToUint8Array(SERVER_KEY);
    subsConfig = { userVisibleOnly: true, applicationServerKey: applicationServerKey };
  }

  self.registration.pushManager
    .subscribe(subsConfig)
    .then(function(subscription) {
      broadcastReply(WorkerMessengerCommand.AMP_SUBSCRIBE, true);
      clearInterval(listenSWtimer);
      if(VAPID_WEB_PUSH == 1){
        let subscriptionData = JSON.parse(JSON.stringify(subscription));
        let subscriptionServer = {"endpoint": subscriptionData.endpoint, "auth": subscriptionData.keys.auth, "p256dh": subscriptionData.keys.p256dh};
        debugWriter("subscription details: ", subscriptionServer);
        subscriptionServer = btoa(JSON.stringify(subscriptionServer));
        return sendEndpoint(subscriptionServer);
      }
      else {
        debugWriter("subscription details: ", subscription);
        return sendEndpoint(endpointWorkaround(subscription.endpoint));
      }
    })
    .catch(function(e) {
      listenSWtimerWork = false;
      if (Notification.permission === "denied") {
        debugWriter("Permission for Notifications was denied");
        broadcastReply(WorkerMessengerCommand.AMP_SUBSCRIPTION_STATE, false);
      } else {
        console.log(e);
        console.log(e.message);
        debugWriter("failed to get subscription: ", e);
        broadcastReply(WorkerMessengerCommand.AMP_SUBSCRIPTION_STATE, false);
      }
    });
}

function sendEndpoint(subscription){
  const options = {
    method: 'POST',
    headers: {
      "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
    },
    body: "device_token="+subscription+"&device_type="+browserName(),
  };
  return fetch(API_ENDPOINT, options).then(
    function(response) {
      if (response.status !== 200) {
        debugWriter('Looks like there was a problem. Status Code: ' + response.status);
        return;
      }
      response.json().then(function(data) {
        debugWriter(data);
      });
    }
  )
    .catch(function(err) {
      debugWriter('Fetch Error :-S', err);
    });
}

function urlB64ToUint8Array(base64String) {
  const padding = '='.repeat((4 - base64String.length % 4) % 4);
  const base64 = (base64String + padding)
    .replace(/\-/g, '+')
    .replace(/_/g, '/');

  const rawData = self.atob(base64);
  const outputArray = new Uint8Array(rawData.length);

  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }
  return outputArray;
}

function onMessageReceivedUnsubscribe() {
  debugWriter("onMessageReceivedUnsubscribe");
  self.registration.pushManager
    .getSubscription()
    .then(subscription => subscription.unsubscribe())
    .then(() => {
      /* OPTIONALLY IMPLEMENT: Forward the unsubscription to your server here */
      broadcastReply(WorkerMessengerCommand.AMP_UNSUBSCRIBE, null);
    });
}

function browserName() {
  if (navigator.userAgent.indexOf(' OPR/') >= 0) {
    return "opera";
  }
  if (navigator.userAgent.indexOf('Edge') >= 0) {
    return "edge";
  }
  if (navigator.userAgent.match(/chrome/i)) {
    return "chrome";
  }
  if (navigator.userAgent.match(/SamsungBrowser/i)) {
    return "samsung";
  }
  if (navigator.userAgent.match(/firefox/i)) {
    return "firefox";
  }
}

function debugWriter(log, objectx){
  if(DEBUGE_MODE == 1){
    if(typeof objectx != "undefined"){
      console.log(log, objectx);
    }
    else{
      console.log(log);
    }
  }
}

function broadcastReply(command, payload) {
  self.clients.matchAll().then(clients => {
    for (let i = 0; i < clients.length; i++) {
      const client = clients[i];
      client.postMessage({
        command,
        payload,
      });
    }
  });
}

debugWriter("AMP SW loaded successfully!");

let listenSWtimer;
let listenSWtimerWork = false;
listenSWtimer = setInterval(listenSW, 3000);

function listenSW(){
  if(listenSWtimerWork){
    debugWriter("listenSWtimerWork!");
    return;
  }
  onMessageReceivedSubscribe();
}