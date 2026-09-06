// Service Worker para AEGOPS PWA
// v2: se corrigieron vulnerabilidades XSS y se excluyó la API/video del caché
const CACHE_NAME = 'aegops-cache-v2';
const urlsToCache = [
  '/',
  '/index.html',
  '/css/estilos-generales.css',
  '/css/estilos-paneles.css',
  '/css/estilos-admin.css',
  '/css/fondos.css',
  '/js/lang/es.js',
  '/js/lang/en.js',
  '/js/lang/pt.js',
  '/js/lang/fr.js',
  '/js/lang/de.js',
  '/js/lang/zh.js',
  '/js/lang/ko.js',
  '/js/logic-translations.js',
  '/js/logic-main.js',
  '/js/tracking.js',
  '/foto/logo.png'
];

// Instalación del Service Worker
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Opened cache');
        return cache.addAll(urlsToCache);
      })
  );
});

// Activación del Service Worker
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            console.log('Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

// Interceptación de peticiones (Cache-first strategy)
self.addEventListener('fetch', event => {
  const url = new URL(event.request.url);

  // SEGURIDAD/PERF: Nunca cachear respuestas de la API (datos sensibles)
  if (url.pathname.includes('/api/')) {
    return;
  }

  // No cachear el video pesado (se sirve desde red)
  if (url.pathname.endsWith('.mp4')) {
    return;
  }

  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Cache hit - return cached response
        if (response) {
          return response;
        }

        // No cache hit - fetch from network
        return fetch(event.request).then(response => {
          // Cache successful GET responses
          if (response.status === 200 && event.request.method === 'GET') {
            const responseClone = response.clone();
            caches.open(CACHE_NAME)
              .then(cache => {
                cache.put(event.request, responseClone);
              });
          }
          return response;
        });
      })
      .catch(error => {
        console.error('Fetch failed:', error);
        return new Response('Offline', { status: 503, statusText: 'Service Unavailable' });
      })
  );
});

// Manejo de errores
self.addEventListener('error', event => {
  console.error('Service Worker error:', event.error);
});

// Manejo de mensajes
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});
