self.addEventListener('push', function (event) {
    if (!event.data) {
        return;
    }

    var data = '';
    // if data is string
    if (typeof event.data === 'string') {
        data = event.data;
    } else {
        data = event.data.json();
    }

    const title = data.title || 'Notification';
    const options = {
        body: data.body || '',
        icon: data.icon || '/images/logo.jpg',
        data: data.data || {}
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    let url = '/';
    if (event.notification.data && event.notification.data.url) {
        url = event.notification.data.url;
    }

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            for (const client of clientList) {
                if (client.url.includes(url) && 'focus' in client) {
                    return client.focus();
                }
            }

            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});