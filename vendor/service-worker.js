self.addEventListener('push', function(event) {
    const data = event.data.json();
    const title = data.title;
    const options = {
        body: data.body,
        icon: 'path/to/icon.png' // Optional: Add an icon
    };
    event.waitUntil(self.registration.showNotification(title, options));
});
