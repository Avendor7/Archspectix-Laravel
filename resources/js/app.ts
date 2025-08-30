import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Theme management with support for 'system' mode
type Theme = 'light' | 'dark';
type ThemeMode = Theme | 'system';

function applyTheme(theme: Theme): void {
    const root = document.documentElement;
    if (theme === 'dark') {
        root.classList.add('dark');
    } else {
        root.classList.remove('dark');
    }
}

function getMediaQuery(): MediaQueryList | null {
    if (typeof window === 'undefined' || !('matchMedia' in window)) {
        return null;
    }
    return window.matchMedia('(prefers-color-scheme: dark)');
}

function getStoredMode(): ThemeMode {
    try {
        const storedMode = localStorage.getItem('themeMode');
        if (storedMode === 'light' || storedMode === 'dark' || storedMode === 'system') {
            return storedMode;
        }
        // Backwards compatibility with previous 'theme' storage
        const legacy = localStorage.getItem('theme');
        if (legacy === 'light' || legacy === 'dark') {
            return legacy;
        }
    } catch {}
    return 'system';
}

function getSystemTheme(): Theme {
    const mq = getMediaQuery();
    return mq && mq.matches ? 'dark' : 'light';
}

function applyCurrentMode(): void {
    const mode = getStoredMode();
    const theme: Theme = mode === 'system' ? getSystemTheme() : mode;
    applyTheme(theme);
}

function initTheme(): void {
    // Apply initial theme ASAP
    applyCurrentMode();

    // Re-apply on OS preference change, but only when in 'system' mode
    const mq = getMediaQuery();
    if (!mq) {
        return;
    }
    const handler = () => {
        if (getStoredMode() === 'system') {
            applyCurrentMode();
        }
    };
    // Modern browsers
    if ('addEventListener' in mq) {
        mq.addEventListener('change', handler as EventListener);
    } else if ('addListener' in mq) {
        // Older Safari
        // @ts-expect-error: addListener exists on older Safari versions
        mq.addListener(handler);
    }
}

initTheme();

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
