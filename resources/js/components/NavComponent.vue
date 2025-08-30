<template>
    <nav>
        <span class="title"><a href="/">Archspectix</a></span>
        <button class="split theme-toggle" @click="toggleTheme">{{ themeLabel }}</button>
        <a href="https://archlinux.org" class="split">Arch Linux</a>
        <a href="https://wiki.archlinux.org/title/Main_page" class="split">Arch Wiki</a>
        <a href="https://archlinux.org/packages/" class="split">Arch Package Search</a>
        <a href="https://aur.archlinux.org/packages" class="split">Arch User Repository</a>
    </nav>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';

const themeLabel = ref('Toggle Light');

function updateLabel(): void {
    const isDark = document.documentElement.classList.contains('dark');
    themeLabel.value = isDark ? 'Light Mode' : 'Dark Mode';
}

function applyTheme(theme: 'light' | 'dark'): void {
    const root = document.documentElement;
    if (theme === 'dark') {
        root.classList.add('dark');
    } else {
        root.classList.remove('dark');
    }
}

function toggleTheme(): void {
    const isDark = document.documentElement.classList.contains('dark');
    const next: 'light' | 'dark' = isDark ? 'light' : 'dark';
    try {
        localStorage.setItem('theme', next);
    } catch {}
    applyTheme(next);
    updateLabel();
}

onMounted(() => {
    updateLabel();
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

nav a.split, .theme-toggle.split {
    float: right;
}
.title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--color-primary);
    text-decoration: none;
}

.theme-toggle {
    background: transparent;
    border: 1px solid var(--color-arch-purple);
    color: var(--color-arch-purple);
    padding: 0.25rem 0.75rem;
    margin-left: 0.5rem;
    border-radius: 0.375rem;
    cursor: pointer;
}

.theme-toggle:hover {
    background: color-mix(in oklab, var(--color-arch-purple) 10%, transparent);
}
</style>
