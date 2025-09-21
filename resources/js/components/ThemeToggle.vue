<template>
    <div class="theme-toggle" @keydown.enter.prevent="toggleOpen">
        <button class="icon-btn" :aria-expanded="open" aria-haspopup="menu" @click="toggleOpen" aria-label="Theme" ref="btn">
            <span v-if="mode === 'system'">🖥️</span>
            <span v-else-if="mode === 'dark'">🌙</span>
            <span v-else>☀️</span>
        </button>

        <ul v-if="open" role="menu" class="theme-menu" @keydown.esc="close" @focusout="onFocusOut">
            <li role="menuitem">
                <button @click="setMode('light')">Light</button>
            </li>
            <li role="menuitem">
                <button @click="setMode('dark')">Dark</button>
            </li>
            <li role="menuitem">
                <button @click="setMode('system')">System</button>
            </li>
        </ul>
    </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, onBeforeUnmount, nextTick } from 'vue';

type Mode = 'light' | 'dark' | 'system';
const STORAGE_KEY = 'site-theme';

const mode = ref<Mode>((localStorage.getItem(STORAGE_KEY) as Mode) ?? 'system');
const open = ref(false);
const btn = ref<HTMLButtonElement | null>(null);

const applyTheme = (m: Mode) => {
    const root = document.documentElement;
    if (m === 'system') {
        const dark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        root.classList.toggle('dark', dark);
        root.classList.toggle('light', !dark);
    } else {
        root.classList.toggle('dark', m === 'dark');
        root.classList.toggle('light', m === 'light');
    }
};

const mq = window.matchMedia('(prefers-color-scheme: dark)');
const systemListener = () => {
    if (mode.value === 'system') applyTheme('system');
};

onMounted(() => {
    applyTheme(mode.value);
    mq.addEventListener('change', systemListener);
});

onBeforeUnmount(() => {
    mq.removeEventListener('change', systemListener);
});

watch(mode, (m) => {
    localStorage.setItem(STORAGE_KEY, m);
    applyTheme(m);
    open.value = false;
});

function setMode(m: Mode) {
    mode.value = m;
}

function toggleOpen() {
    open.value = !open.value;
    if (open.value) nextTick(() => btn.value?.focus());
}

function close() {
    open.value = false;
}

function onFocusOut(e: FocusEvent) {
    const related = e.relatedTarget as Node | null;
    if (!related || !(btn.value?.contains(related) || (e.target as Node).contains(related))) {
        close();
    }
}
</script>

<style scoped>
.theme-toggle {
    position: relative;
    display: inline-block;
}
.icon-btn {
    background: transparent;
    border: none;
    font-size: 1.2rem;
}
.theme-menu {
    position: absolute;
    right: 0;
    margin-top: 8px;
    border-radius: 6px;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.4);
    padding: 6px;
    min-width: 120px;
}
.theme-menu button {
    width: 100%;
    text-align: left;
    background: none;
    border: none;
    padding: 8px;
}
.theme-menu button:focus {
    outline: 2px solid #8b5cf6;
}
</style>
