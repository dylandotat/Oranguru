const CACHE_VERSION = "frogtab-v1";

const PRECACHE_URLS = [
  "/",
  "/style.css",
  "/simple.min.css",
  "/simple-customizations.css",
  "/main.js",
  "/help.html",
  "/week.html",
  "/achievements.html",
  "/send.html",
  "/icon-normal.html",
  "/icon-notify.html",
  "/manifest.webmanifest",
  "/favicons/icon-16.png",
  "/favicons/icon-32.png",
  "/favicons/icon-16-notify.png",
  "/favicons/icon-32-notify.png",
  "/favicons/icon-180-apple.png",
  "/favicons/icon-192.png",
  "/favicons/icon-512.png"
];

self.addEventListener("install", event => {
  event.waitUntil(
    caches.open(CACHE_VERSION).then(cache => cache.addAll(PRECACHE_URLS))
  );
  self.skipWaiting();
});

self.addEventListener("activate", event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.filter(k => k !== CACHE_VERSION).map(k => caches.delete(k)))
    )
  );
  self.clients.claim();
});

self.addEventListener("fetch", event => {
  const url = new URL(event.request.url);

  // Let API/server calls go straight to network
  if (url.pathname.startsWith("/open/") || url.pathname.startsWith("/service/")) {
    return;
  }

  // Network-first for all requests, fall back to cache for offline support
  event.respondWith(
    fetch(event.request)
      .then(response => {
        const clone = response.clone();
        caches.open(CACHE_VERSION).then(cache => cache.put(event.request, clone));
        return response;
      })
      .catch(() => caches.match(event.request, { ignoreSearch: true }))
  );
});
