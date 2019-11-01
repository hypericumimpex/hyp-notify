"use strict";

self.addEventListener("install", event => {
  event.waitUntil(self.skipWaiting());
});

self.addEventListener("notificationclick", function(event) {
  event.notification.close();
  if (typeof(event.notification.data.click) != "undefined" && event.notification.data.click != "") {
    eval(event.notification.data.click);
  }
  if (typeof(event.action) != "undefined" && event.action != "") {
    eval(event.notification.data.actions[event.action]);
    return;
  }
  if(event.notification.data.target == ""){
    return;
  }
  event.waitUntil(clients.matchAll({
    type: "window"
  }).then(function(clientList) {
    for (let i = 0; i < clientList.length; i++) {
      let client = clientList[i];
      if (client.url === event.notification.data.target && "focus" in client) {
        return client.focus();
      }
    }
    if (clients.openWindow) {
      return clients.openWindow(event.notification.data.target);
    }
  }));
});

self.addEventListener("push", function(event) {
  let payload = event.data.json().notification;
  event.waitUntil(self.registration.showNotification(payload.title, payload));
  if (typeof(payload.command) != "undefined" && payload.command != "") {
    eval(payload.command);
  }
});