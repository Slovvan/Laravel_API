import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

declare global {
    interface Window {
        Pusher: typeof Pusher;
        Echo: InstanceType<typeof Echo>;
    }
}

console.log('[Echo] blade script loaded');

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
    wsHost: import.meta.env.VITE_PUSHER_HOST
        ? import.meta.env.VITE_PUSHER_HOST
        : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusherapp.com`,
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

const userId = document.documentElement.getAttribute('data-user-id');
if (!userId) {
    console.log('[Echo] no data-user-id found on <html>');
} else {
    console.log('[Echo] subscribing to user channel', userId);
    const channel = window.Echo.private(`user.${userId}`);
    channel
        .subscribed(() => {
            console.log('[Echo] channel subscribed');
        })
        .error((error: any) => {
            console.log('[Echo] channel error', error);
        })
        .listen('.notification.received', (event: any) => {
            console.log('[Echo] notification.received', event);
            showNotificationToast(event);
        });
}

// Connection status badge (debug)
const badge = document.createElement('div');
badge.textContent = 'Echo: connecting';
badge.style.cssText = `
    position: fixed;
    bottom: 16px;
    right: 16px;
    background: #f59e0b;
    color: #111827;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
    z-index: 9999;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
`;
document.body.appendChild(badge);

const connector = (window.Echo as any)?.connector?.pusher;
if (!connector) {
    badge.textContent = 'Echo: not connected';
    badge.style.background = '#ef4444';
    console.log('[Echo] Pusher connector not found');
} else {
    connector.connection.bind('connected', () => {
        badge.textContent = 'Echo: connected';
        badge.style.background = '#22c55e';
    });
    connector.connection.bind('disconnected', () => {
        badge.textContent = 'Echo: disconnected';
        badge.style.background = '#ef4444';
    });
    connector.connection.bind('error', (err: any) => {
        badge.textContent = 'Echo: error';
        badge.style.background = '#ef4444';
        console.log('[Echo] connection error', err);
    });
}

function showNotificationToast(notification: any) {
    const message = getNotificationMessage(notification);
    const notificationEl = document.createElement('div');
    notificationEl.className = 'notification-toast notification-toast-info';
    notificationEl.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px;">
            <div>
                <strong>Notification</strong>
                <p style="margin: 5px 0 0 0; font-size: 0.9em;">${escapeHtml(message)}</p>
            </div>
            <button class="notification-close" style="background: none; border: none; color: #111; font-size: 1.2em; cursor: pointer;">×</button>
        </div>
    `;
    notificationEl.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #f8f8f8;
        color: #111;
        padding: 15px 20px;
        border-radius: 0;
        border: 1px solid #a9a9a9;
        box-shadow: 0 2px 6px rgba(0,0,0,0.12);
        z-index: 9999;
        max-width: 400px;
        animation: slideIn 0.3s ease-out;
    `;

    document.body.appendChild(notificationEl);

    const closeBtn = notificationEl.querySelector('.notification-close') as HTMLButtonElement;
    closeBtn?.addEventListener('click', () => {
        notificationEl.style.animation = 'slideOut 0.3s ease-out forwards';
        setTimeout(() => notificationEl.remove(), 300);
    });

    setTimeout(() => {
        notificationEl.style.animation = 'slideOut 0.3s ease-out forwards';
        setTimeout(() => notificationEl.remove(), 300);
    }, 5000);
}

function getNotificationMessage(notification: any): string {
    const payload = notification?.data ?? notification?.notification?.data ?? notification;
    const type = notification?.type ?? notification?.notification?.type ?? payload?.type;

    if (type === 'comment') {
        return `${payload.commenter_name} a commenté votre article "${payload.article_title}"`;
    }

    if (payload?.commenter_name) {
        return `Nouveau commentaire par ${payload.commenter_name}`;
    }

    return 'Nouvelle notification';
}

function escapeHtml(text: string): string {
    const map: Record<string, string> = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    };
    return text.replace(/[&<>"']/g, (m) => map[m]);
}

const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;

document.head.appendChild(style);
