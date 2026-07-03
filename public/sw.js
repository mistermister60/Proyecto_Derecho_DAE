// use strict;

const CACHE_NAME = "pwa-app-v1";
const OFFLINE_URL = '/offline.html';

// Assets to cache on first install
const STATIC_CACHE = [
    OFFLINE_URL,
    '/',
    '/dashboard',
    '/casos',
    '/clientes',
    '/usuarios',
    '/demandados',
    '/procuradores',
    '/agenda',
    '/css/app.css',
    '/js/app.js',
    '/manifest.json',
    '/logo.png'
];

// APIs to cache with network-first strategy
const API_CACHE = [
    '/api/casos',
    '/api/clientes',
    '/api/usuarios',
    '/api/demandados',
    '/api/procuradores',
    '/api/agenda',
    '/api/seguimientos'
];

// Install event - cache static assets
self.addEventListener("install", (event) => {
    console.log('[SW] Instalando Service Worker');

    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[SW] Cacheando assets estáticos');
                return cache.addAll(STATIC_CACHE);
            })
            .then(() => {
                console.log('[SW] Instalación completada');
                return self.skipWaiting();
            })
            .catch((error) => {
                console.error('[SW] Error en instalación:', error);
            })
    );
});

// Fetch event - implementar estrategia de cache

self.addEventListener("fetch", (event) => {
    const request = event.request;
    const url = new URL(request.url);

    // Usar strategy: network-first para navegación (navegar)
    if (request.mode === 'navigate') {
        event.respondWith(
            networkFirstNavigate(request, url)
        );
        return;
    }

    // API requests: network-first con fallback a cache
    if (url.pathname.startsWith('/api/') || url.pathname.includes('/casos') || 
        url.pathname.includes('/clientes') || url.pathname.includes('/agenda') ||
        url.pathname.includes('/seguimientos')) {
        event.respondWith(
            networkFirstAPI(request, url)
        );
        return;
    }

    // Archivos estáticos: cache-first con expiración
    event.respondWith(
        cacheFirstStatic(request, url)
    );
});

// Función para navegación con network-first
async function networkFirstNavigate(request, url) {
    try {
        const networkResponse = await fetch(request);
        
        // Si es exitoso y no es error, cachearlo
        if (networkResponse.ok && networkResponse.status < 400) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        console.log('[SW] Network failed para', url.pathname, 'usando fallback offline');
        
        const cache = await caches.open(CACHE_NAME);
        const cachedResponse = await cache.match(url.pathname);
        
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Si no hay nada cacheado, mostrar offline.html para rutas principales
        if (url.pathname === '/' || url.pathname === '/dashboard') {
            return caches.match(OFFLINE_URL);
        }
        
        // Para otras rutas, retornar HTML simple offline
        return new Response(
            `<html><body><h1>App Offline</h1><p>Conecta a internet para acceder a ${url.pathname}</p></body></html>`,
            {
                status: 200,
                statusText: 'OK',
                headers: {'Content-Type': 'text/html'}
            }
        );
    }
}

// Función para API con network-first
async function networkFirstAPI(request, url) {
    try {
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        console.log('[SW] API falló para', url.pathname, 'usando cache');
        
        const cache = await caches.open(CACHE_NAME);
        const cachedResponse = await cache.match(request);
        
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Devolver error simulado para APIs offline
        return new Response(
            JSON.stringify({
                error: 'Servicio no disponible offline',
                mensaje: 'Esta solicitud requiere conexión a internet',
                timestamp: new Date().toISOString()
            }),
            {
                status: 503,
                statusText: 'Service Unavailable',
                headers: {'Content-Type': 'application/json'}
            }
        );
    }
}

// Función para archivos estáticos con cache-first
async function cacheFirstStatic(request, url) {
    const cache = await caches.open(CACHE_NAME);
    
    // Primero intentar de cache
    const cachedResponse = await cache.match(request);
    if (cachedResponse) {
        // Verificar si está obsoleto (después de 7 días)
        const cacheDate = await cache.match(request + '.cache-date');
        if (cacheDate) {
            const cachedTime = new Date(cacheDate.url);
            const now = new Date();
            const diffDays = (now - cachedTime) / (1000 * 60 * 60 * 24);
            
            if (diffDays < 7) {
                return cachedResponse;
            }
        }
        
        // Si está obsoleto, intentar de nuevo (fallar rápido)
        try {
            return fetch(request);
        } catch (error) {
            return cachedResponse;
        }
    }
    
    // Si no hay en cache, intentar de red
    try {
        const networkResponse = await fetch(request);
        
        // Cachear la respuesta
        await cache.put(request, networkResponse.clone());
        
        // Guardar fecha de cache
        const dateResponse = new Response(new Date().toString());
        await cache.put(request + '.cache-date', dateResponse);
        
        return networkResponse;
    } catch (error) {
        console.log('[SW] Falló completamente el fetch para', url.pathname);
        return new Response('Asset not available', {status: 404, statusText: 'Not Found'});
    }
}

// Active event - limpiar caches viejos
self.addEventListener('activate', (event) => {
    console.log('[SW] Activando nuevo Service Worker');
    
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('[SW] Eliminando cache viejo:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => {
            console.log('[SW] Activación completada');
            return self.clients.claim();
        })
    );
});

// Función para verificar conexión
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'CHECK_CONNECTION') {
        const messageChannel = event.ports[0];
        
        fetch('/api/health').then((response) => {
            messageChannel.postMessage({
                online: true,
                timestamp: new Date().toISOString()
            });
        }).catch(() => {
            messageChannel.postMessage({
                online: false,
                timestamp: new Date().toISOString()
            });
        });
    }
});

console.log('[SW] Service Worker cargado correctamente');
