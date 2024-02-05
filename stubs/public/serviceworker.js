/*
serviceworker.js - a simple service worker for PWA

service workers can be used for:
- caching assets
- intercepting network requests
- push notifications
- background sync
- geofencing
- etc.
*/

const staticCacheName = `pwa-v${new Date().getTime()}`;
const filesToCache = [
    '/offline',
    '/css/app.css',
    '/js/app.js',
    '/images/icons/icon-72x72.png',
    '/images/icons/icon-96x96.png',
    '/images/icons/icon-128x128.png',
    '/images/icons/icon-144x144.png',
    '/images/icons/icon-152x152.png',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-384x384.png',
    '/images/icons/icon-512x512.png'
];

// push notifications
/*
to do a push notification with a service worker, you need to:
- register the service worker
- request permission for push notifications
- get the push subscription
- send the push subscription to the server
- send a push notification from the server
- handle the push notification in the service worker
- display the push notification
 */
self.addEventListener('push', function (event) {
    console.log('push serviceworker.js', event);
    const options = {
        body: event.data.text(),
        icon: '/images/icons/icon-192x192.png',
        badge: '/images/icons/icon-72x72.png'
    };
    event.waitUntil(self.registration.showNotification('Push Notification', options));
});

// Cache on install
self.addEventListener('install', (event) => {
    // console.log('install serviceworker.js', event);
    // this.skipWaiting();
    // event.waitUntil(
    //     caches.open(staticCacheName)
    //         .then(cache => {
    //           return cache ? cache.addAll(filesToCache) : null;
    //         })
    // )
});

// Clear cache on activate
self.addEventListener('activate', (event) => {
    // console.log('activate serviceworker.js', event);

    // event.waitUntil(
    //     caches.keys().then(cacheNames => {
    //         return Promise.all(
    //             cacheNames
    //                 .filter(cacheName => (cacheName.startsWith("pwa-")))
    //                 .filter(cacheName => (cacheName !== staticCacheName))
    //                 .map(cacheName => caches.delete(cacheName))
    //         );
    //     })
    // );
});

// Serve from Cache
self.addEventListener('fetch', (event) => {
    // console.log('fetch serviceworker.js', event);

    // event.respondWith(
    //     caches.match(event.request)
    //         .then(response => {
    //             return response || fetch(event.request);
    //         })
    //         .catch(() => {
    //             return caches.match('offline');
    //         })
    // )
});


self.addEventListener('message', (event) => {
    // console.log('message serviceworker.js', event);
});

