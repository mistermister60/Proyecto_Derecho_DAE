import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Chart = Chart;
window.Swal = Swal;

// Configuración del Service Worker de PWA
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then((registration) => {
                console.log('Service Worker registrado exitosamente:', registration.scope);
                
                // Escuchar mensajes del Service Worker
                navigator.serviceWorker.addEventListener('message', (event) => {
                    if (event.data && event.data.type === 'CHECK_CONNECTION') {
                        const isOnline = navigator.onLine;
                        if (event.ports && event.ports.length > 0) {
                            event.ports[0].postMessage({
                                online: isOnline,
                                timestamp: new Date().toISOString()
                            });
                        }
                    }
                });
                
            })
            .catch((error) => {
                console.error('Error al registrar Service Worker:', error);
            });
    });
}

// Controlador de estado de red
window.addEventListener('online', () => {
    console.log('Conexión restablecida');
    // Disparar evento personalizado para recargar datos si es necesario
    window.dispatchEvent(new CustomEvent('network-restore'));
});

window.addEventListener('offline', () => {
    console.log('Sin conexión');
    window.dispatchEvent(new CustomEvent('network-down'));
});

// PWA Installation
let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
    // Prevenir el mini-infobar nativo de Chrome para usar nuestro botón personalizado
    e.preventDefault();
    deferredPrompt = e;

    // Mostrar el botón de instalación (si existe en el DOM)
    const installBtn = document.getElementById('install-pwa-button');
    if (installBtn) {
        installBtn.classList.remove('hidden');
        installBtn.classList.add('inline-flex');
        installBtn.addEventListener('click', triggerInstall);
    }
});

window.addEventListener('appinstalled', () => {
    deferredPrompt = null;
    const installBtn = document.getElementById('install-pwa-button');
    if (installBtn) {
        installBtn.classList.add('hidden');
        installBtn.classList.remove('inline-flex');
    }
});

// Función para disparar el prompt de instalación
function triggerInstall() {
    if (!deferredPrompt) return;
    deferredPrompt.prompt();
    deferredPrompt.userChoice.then((choiceResult) => {
        if (choiceResult.outcome === 'accepted') {
            console.log('Usuario aceptó la instalación de la PWA');
        } else {
            console.log('Usuario canceló la instalación de la PWA');
        }
        deferredPrompt = null;
        const installBtn = document.getElementById('install-pwa-button');
        if (installBtn) {
            installBtn.classList.add('hidden');
            installBtn.classList.remove('inline-flex');
        }
    });
}

// NOTIFICACIONES PUSH INTEGRACIÓN
async function subscribeToPushNotifications() {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        console.log('Push notifications no soportadas');
        return false;
    }

    try {
        const swRegistration = await navigator.serviceWorker.ready;
        
        // Intentar obtener suscripción actual
        let subscription = await swRegistration.pushManager.getSubscription();
        
        if (!subscription) {
            // Pedir permiso y suscribirse
            const permission = await Notification.requestPermission();
            
            if (permission !== 'granted') {
                console.log('Permiso de notificación denegado');
                return false;
            }
            
            // Obtener clave pública VAPID del servidor
            const response = await fetch('/api/notifications/vapid-public-key');
            const vapidPublicKey = await response.text();
            
            // Convertir clave de base64 a Uint8Array
            const urlB64ToUint8Array = (base64String) => {
                const padding = '='.repeat((4 - base64String.length % 4) % 4);
                const base64 = (base64String + padding)
                    .replace(/-/g, '+')
                    .replace(/_/g, '/');
                const rawData = window.atob(base64);
                const outputArray = new Uint8Array(rawData.length);
                for (let i = 0; i < rawData.length; ++i) {
                    outputArray[i] = rawData.charCodeAt(i);
                }
                return outputArray;
            };
            
            subscription = await swRegistration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlB64ToUint8Array(vapidPublicKey)
            });
            
            // Enviar suscripción al servidor
            await fetch('/api/notifications/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({
                    subscription: JSON.parse(JSON.stringify(subscription)),
                    user_id: window.userId || 1 // TODO: Reemplazar con ID real de usuario
                })
            });
        }
        
        console.log('Suscrito a notificaciones push exitosamente');
        return true;
        
    } catch (error) {
        console.error('Error suscribiéndose a notificaciones push:', error);
        return false;
    }
}

// Listener para notificaciones push recibidas
navigator.serviceWorker.addEventListener('message', async (event) => {
    if (event.data && event.data.type === 'PUSH_NOTIFICATION') {
        const notification = event.data.notification;
        
        // Mostrar notificación nativa si la aplicación no está en foreground
        if (Notification.permission === 'granted') {
            new Notification(notification.title || 'Notificación', {
                body: notification.body || 'Nueva notificación',
                icon: notification.icon || '/logo.png',
                badge: '/logo.png',
                data: notification.data,
                actions: notification.actions || []
            });
        }
        
        // Mostrar toast UI si la aplicación está en foreground
        if (window.Swal) {
            window.Swal.fire({
                title: notification.title || 'Notificación',
                text: notification.body || 'Nueva notificación',
                icon: notification.icon ? 'info' : 'info',
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                toast: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', window.Swal.stopTimer);
                    toast.addEventListener('mouseleave', window.Swal.resumeTimer);
                }
            });
        }
    }
});

// ============================================
// BÚSQUEDA GLOBAL — Componente Alpine (typeahead)
// ============================================
Alpine.data('globalSearch', () => ({
    query: '',
    results: [],
    open: false,
    loading: false,
    selectedIndex: -1,
    _debounceTimer: null,

    init() {
        this.$watch('query', () => {
            if (this._debounceTimer) clearTimeout(this._debounceTimer);
            if (this.query.length < 2) {
                this.results = [];
                this.open = false;
                return;
            }
            this._debounceTimer = setTimeout(() => this.fetchResults(), 300);
        });
    },

    async fetchResults() {
        this.loading = true;
        this.selectedIndex = -1;
        try {
            const response = await fetch(`/api/search?q=${encodeURIComponent(this.query)}`);
            const data = await response.json();
            this.results = data.results ?? [];
            this.open = this.results.length > 0;
        } catch (e) {
            this.results = [];
            this.open = false;
        } finally {
            this.loading = false;
        }
    },

    selectResult(idx) {
        if (idx >= 0 && idx < this.results.length) {
            this.open = false;
            setTimeout(() => {
                window.location.href = this.results[idx].url;
            }, 50);
        }
    },

    iconBg(type) {
        const colors = {
            Caso: '#EFF6FF',
            Cliente: '#EFF6FF',
            Demandado: '#EFF6FF',
            Procurador: '#FEF3C7',
            Audiencia: '#F3E8FF',
            Documento: '#FFFBEB',
            Entrevista: '#E0F2FE',
            Seguimiento: '#ECFDF5',
        };
        return `background:${colors[type] || '#EFF6FF'}`;
    },

    badgeStyle(type) {
        const colors = {
            Caso: 'background:#EFF6FF;color:#2563EB',
            Cliente: 'background:#F0FDF4;color:#166534',
            Demandado: 'background:#F0FDF4;color:#166534',
            Procurador: 'background:#FEF3C7;color:#92400E',
            Audiencia: 'background:#F3E8FF;color:#6B21A8',
            Documento: 'background:#FFFBEB;color:#92400E',
            Entrevista: 'background:#E0F2FE;color:#155E75',
            Seguimiento: 'background:#ECFDF5;color:#065F46',
        };
        return colors[type] || 'background:#F3F4F6;color:#374151';
    },
}));

Alpine.start();
