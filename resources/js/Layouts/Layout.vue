<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import NavComponent from '../components/NavComponent.vue';
</script>

<template>
    <Head title="Archspectix" />
    <!-- Background glow gradient -->
    <div
        aria-hidden="true"
        class="pointer-events-none fixed inset-0 -z-10 overflow-hidden"
    >
        <!-- Top-center purple glow -->
        <div
            class="absolute left-1/2 top-[-10%] h-[40rem] w-[40rem] -translate-x-1/2 rounded-full top-gradient blur-3xl"
        ></div>

        <!-- Bottom-right pink glow -->
        <div
            class="absolute right-[-10%] bottom-[-20%] h-[35rem] w-[35rem] rounded-full bg-fuchsia-600/10 blur-3xl"
        ></div>
    </div>
    <div>
        <NavComponent />
        <slot />
    </div>
</template>

<style scoped></style>
