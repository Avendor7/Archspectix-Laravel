<template>
    <nav>
        <span class="title"><a href="/">Archspectix</a></span>
        <div class="controls split">
            <button class="theme-toggle" @click="setMode('light')" :aria-pressed="activeMode === 'light'">Light</button>
            <button class="theme-toggle" @click="setMode('dark')" :aria-pressed="activeMode === 'dark'">Dark</button>
            <button class="theme-toggle" @click="setMode('system')" :aria-pressed="activeMode === 'system'">System</button>
        </div>
        <a href="https://archlinux.org" class="split">Arch Linux</a>
        <a href="https://wiki.archlinux.org/title/Main_page" class="split">Arch Wiki</a>
        <a href="https://archlinux.org/packages/" class="split">Arch Package Search</a>
        <a href="https://aur.archlinux.org/packages" class="split">Arch User Repository</a>
    </nav>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';

type Theme = 'light' | 'dark';
type ThemeMode = Theme | 'system';

const activeMode = ref<ThemeMode>('system');

// Global theme management functions
function applyTheme(theme: Theme): void {
    const root = document.documentElement;
    if (theme === 'dark') {
        root.classList.add('dark');
        console.log('🌙 Dark theme applied');
    } else {
        root.classList.remove('dark');
        console.log('☀️ Light theme applied');
    }
}

function getSystemTheme(): Theme {
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
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

function setMode(mode: ThemeMode): void {
    console.log(`🎨 Setting theme mode to: ${mode}`);
    activeMode.value = mode;
    try {
        localStorage.setItem('themeMode', mode);
    } catch {}
    const theme: Theme = mode === 'system' ? getSystemTheme() : mode;
    applyTheme(theme);

    // Dispatch custom event for other components
    window.dispatchEvent(new CustomEvent('themeChanged', {
        detail: { mode, theme }
    }));
}

// Listen for OS preference changes when in system mode
function setupSystemThemeListener(): void {
    const mq = window.matchMedia('(prefers-color-scheme: dark)');
    if (!mq) return;

    const handler = () => {
        if (getStoredMode() === 'system') {
            console.log('🔄 OS theme preference changed, updating...');
            const theme = getSystemTheme();
            applyTheme(theme);
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

// Watch for changes in localStorage from other components
function setupStorageListener(): void {
    window.addEventListener('storage', (e) => {
        if (e.key === 'themeMode' && e.newValue) {
            const newMode = e.newValue as ThemeMode;
            if (newMode === 'light' || newMode === 'dark' || newMode === 'system') {
                console.log(`📦 Storage theme changed to: ${newMode}`);
                activeMode.value = newMode;
                setMode(newMode);
            }
        }
    });
}

onMounted(() => {
    console.log('🚀 NavComponent mounted, initializing theme...');

    // Initialize theme from stored preference
    const stored = getStoredMode();
    activeMode.value = stored;
    console.log(`📱 Initial theme mode: ${stored}`);
    setMode(stored);

    // Setup listeners
    setupSystemThemeListener();
    setupStorageListener();

    // Also listen for custom events in case other components change the theme
    window.addEventListener('themeChanged', (e: any) => {
        if (e.detail && e.detail.mode) {
            console.log(`📡 Theme changed event received: ${e.detail.mode}`);
            activeMode.value = e.detail.mode;
        }
    });
});
</script>

<style scoped>
nav a {
    display: inline-block;
    padding: 0 1rem;
    border-left: 1px solid var(--color-arch-purple);
    text-decoration: none;
    color: var(--color-arch-purple);
}

nav a:first-of-type {
    border: 0;
}

nav a:last-of-type {
    border: 0;
}

nav a.split, .theme-toggle.split, .controls.split {
    float: right;
}
.title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--color-primary);
    text-decoration: none;
}

.controls {
    display: inline-flex;
    gap: 0.25rem;
    align-items: center;
}

.theme-toggle {
    background: transparent;
    border: 1px solid var(--color-arch-purple);
    color: var(--color-arch-purple);
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.theme-toggle[aria-pressed="true"] {
    background: color-mix(in oklab, var(--color-arch-purple) 15%, transparent);
    transform: scale(1.05);
}

.theme-toggle:hover {
    background: color-mix(in oklab, var(--color-arch-purple) 10%, transparent);
    transform: scale(1.02);
}

.theme-toggle:active {
    transform: scale(0.98);
}
</style>
