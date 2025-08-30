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

function applyTheme(theme: Theme): void {
    const root = document.documentElement;
    if (theme === 'dark') {
        root.classList.add('dark');
    } else {
        root.classList.remove('dark');
    }
}

function getSystemTheme(): Theme {
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

function setMode(mode: ThemeMode): void {
    activeMode.value = mode;
    try {
        localStorage.setItem('themeMode', mode);
    } catch {}
    const theme: Theme = mode === 'system' ? getSystemTheme() : mode;
    applyTheme(theme);
}

onMounted(() => {
    try {
        const stored = localStorage.getItem('themeMode');
        if (stored === 'light' || stored === 'dark' || stored === 'system') {
            activeMode.value = stored;
        } else {
            // Backwards compatibility with previous storage key
            const legacy = localStorage.getItem('theme');
            if (legacy === 'light' || legacy === 'dark') {
                activeMode.value = legacy;
            }
        }
    } catch {}
    setMode(activeMode.value);
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
}

.theme-toggle[aria-pressed="true"] {
    background: color-mix(in oklab, var(--color-arch-purple) 15%, transparent);
}

.theme-toggle:hover {
    background: color-mix(in oklab, var(--color-arch-purple) 10%, transparent);
}
</style>
