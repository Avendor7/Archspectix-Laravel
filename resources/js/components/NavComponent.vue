<template>
    <nav>
        <button>Button</button>
        <button>Button</button>
        <a href="https://archlinux.org" class="split">Arch Linux</a>
        <a href="https://wiki.archlinux.org/title/Main_page" class="split">Arch Wiki</a>
        <a href="https://archlinux.org/packages/" class="split">Arch Package Search</a>
        <a href="https://aur.archlinux.org/packages" class="split">Arch User Repository</a>
    </nav>
</template>

<script setup lang="ts"></script>

<style scoped>
nav a {
    display: inline-block;
    padding: 0 1rem;
    border-left: 1px solid var(--color-border);
    text-decoration: none;
    color: var(--color-primary);
}

nav a:first-of-type {
    border: 0;
}

nav a:last-of-type {
    border: 0;
}

nav a.split {
    float: right;
}
</style>
