const setupCatalogPusher = () => {
    const config = window.catalogPusherConfig;
    const toastRegion = document.getElementById('catalog-toast-region');

    if (!config?.key || !config?.cluster || !toastRegion || !window.Pusher) {
        return;
    }

    window.Pusher.logToConsole = false;

    const pusher = new window.Pusher(config.key, {
        cluster: config.cluster,
    });
    let lastToastSignature = null;
    let lastToastAt = 0;

    const normalizePayload = (payload) => {
        let normalized = payload;

        if (typeof normalized === 'string') {
            try {
                normalized = JSON.parse(normalized);
            } catch {
                normalized = { message: payload };
            }
        }

        if (normalized && typeof normalized === 'object' && 'data' in normalized) {
            normalized = normalized.data;
        }

        if (typeof normalized === 'string') {
            try {
                normalized = JSON.parse(normalized);
            } catch {
                normalized = { message: normalized };
            }
        }

        if (!normalized || typeof normalized !== 'object') {
            normalized = {};
        }

        const entityLabel = normalized.entity
            ? normalized.entity.charAt(0).toUpperCase() + normalized.entity.slice(1)
            : 'Каталог';

        const actionLabel = normalized.action ?? 'обновлено';

        return {
            entity: normalized.entity ?? null,
            action: actionLabel,
            id: normalized.id ?? null,
            title: normalized.title ?? `${entityLabel}: ${actionLabel}`,
            message: normalized.message ?? 'Получено новое событие каталога.',
            url: normalized.url ?? null,
        };
    };

    const renderToast = (payload) => {
        const data = normalizePayload(payload);
        const signature = JSON.stringify(data);
        const now = Date.now();

        if (signature === lastToastSignature && now - lastToastAt < 1000) {
            return;
        }

        lastToastSignature = signature;
        lastToastAt = now;

        const toast = document.createElement('article');
        toast.className = 'pointer-events-auto translate-x-4 opacity-0 rounded-[1.5rem] border border-stone-200 bg-white/95 p-4 shadow-xl shadow-stone-300/40 backdrop-blur transition duration-300';

        const title = document.createElement('p');
        title.className = 'text-sm font-semibold text-stone-900';
        title.textContent = data.title;

        const message = document.createElement('p');
        message.className = 'mt-1 text-sm text-stone-600';
        message.textContent = data.message;

        toast.append(title, message);

        if (data.url) {
            const link = document.createElement('a');
            link.href = data.url;
            link.className = 'mt-3 inline-flex text-sm font-medium text-amber-700 hover:text-amber-800';
            link.textContent = 'Перейти';
            toast.append(link);
        }

        toastRegion.prepend(toast);

        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-4', 'opacity-0');
        });

        window.setTimeout(() => {
            toast.classList.add('translate-x-4', 'opacity-0');

            window.setTimeout(() => {
                toast.remove();
            }, 300);
        }, 5000);
    };

    const channel = pusher.subscribe(config.channel);
    channel.bind(config.event, renderToast);
    channel.bind_global((eventName, payload) => {
        if (eventName === config.event) {
            renderToast(payload);
        }
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupCatalogPusher);
} else {
    setupCatalogPusher();
}
