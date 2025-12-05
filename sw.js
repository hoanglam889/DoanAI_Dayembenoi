const CACHE_NAME = 'daytre-cache-v1';
const urlsToCache = [
  '/',
  '/index.php   ',
  '/css/style.css',
  '/script.js',
  '/icon.png'
];

// Cài đặt cache
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache))
  );
});

// Fetch dữ liệu từ cache trước
self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request).then(response => response || fetch(event.request))
  );
});
