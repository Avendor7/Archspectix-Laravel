<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import NavComponent from "../Components/NavComponent.vue";
</script>

<template>
    <Head title="Archspectix" />
    <div>
        <NavComponent />
        <slot />
    </div>


</template>

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
