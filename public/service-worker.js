self.addEventListener('push', function (event) {
    let data = event.data.json();
    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: '/logo-white.jpg',
            data: { url: data.url }
          })
    );
  });

  self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    event.waitUntil(clients.openWindow(event.notification.data.url));
  });
