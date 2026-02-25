const CACHE_NAME = 'stocksync-v1';
const STATIC_ASSETS = [
    '/vendor/nprogress/nprogress.min.js',
    '/vendor/nprogress/nprogress.min.css',
    '/vendor/chartjs/chart.min.js',
    '/vendor/html5-qrcode/html5-qrcode.min.js',
    '/vendor/fontawesome/css/all.min.css',
];

self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS);
        })
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

self.addEventListener('fetch', (event) => {
    // Skip cross-origin requests (like fonts.googleapis.com) for now to avoid CORS issues in basic setup
    // or handle them gracefully
    if (!event.request.url.startsWith(self.location.origin)) {
        return;
    }

    const url = new URL(event.request.url);
    
    // Cache First for Static Assets (CSS, JS, Images, Fonts, Vendor libs)
    if (url.pathname.startsWith('/vendor/') || 
        url.pathname.startsWith('/css/') || 
        url.pathname.startsWith('/js/') ||
        url.pathname.startsWith('/fonts/') ||
        url.pathname.match(/\.(png|jpg|jpeg|svg|ico)$/)) {
        
        event.respondWith(
            caches.match(event.request).then((response) => {
                // Return cached response if found
                if (response) {
                    return response;
                }
                
                // Otherwise fetch from network and cache it
                return fetch(event.request).then((fetchResponse) => {
                    // Check if valid response
                    if (!fetchResponse || fetchResponse.status !== 200 || fetchResponse.type !== 'basic') {
                        return fetchResponse;
                    }

                    const responseToCache = fetchResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseToCache);
                    });

                    return fetchResponse;
                });
            })
        );
    } else {
        // Network First for HTML / Data (Dashboard, API, etc.)
        // This ensures users see fresh data when online, but can see cached version if offline
        event.respondWith(
            fetch(event.request)
                .then((response) => {
                    // Cache the fresh copy
                    const responseToCache = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseToCache);
                    });
                    return response;
                })
                .catch(() => {
                    // If offline, try to serve from cache
                    return caches.match(event.request).then((response) => {
                        if (response) {
                            return response;
                        }
                        // Optional: Return a custom offline.html here
                    });
                })
        );
    }
});