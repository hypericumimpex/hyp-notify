"use strict";

self.addEventListener("push", function(event) {
  let payload = JSON.parse(event.data.text());
  event.waitUntil(self.registration.showNotification(payload.title, payload));
  if (typeof(payload.command) != "undefined" && payload.command != "") {
    eval(payload.command);
  }
});

self.addEventListener("install", event => {
  event.waitUntil(self.skipWaiting());
});

self.addEventListener("notificationclick", function(event) {
  event.notification.close();
  if (typeof(event.action) != "undefined" && event.action != "") {
    eval(event.notification.data.actions[event.action]);
    return;
  }
  if(event.notification.tag == ""){
    return;
  }
  if(event.notification.data.target && event.notification.data.target == ""){
    return;
  }
  event.waitUntil(clients.matchAll({
    type: "window"
  }).then(function(clientList) {
    let targetLink = "";
    if(event.notification.data.target && event.notification.data.target != ""){
      targetLink = event.notification.data.target;
    }
    else if(event.notification.tag != ""){
      targetLink = event.notification.tag;
    }
    for (let i = 0; i < clientList.length; i++) {
      let client = clientList[i];
      if (client.url === targetLink && "focus" in client) {
        return client.focus();
      }
    }
    if (clients.openWindow) {
      return clients.openWindow(targetLink);
    }
  }));
});