<template>
    <Head title="Archspectix" />
    <div>
        <NavComponent />
        <div class="mt-5 mb-5">
            <SearchComponent />
        </div>
        <slot />
    </div>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import NavComponent from '../components/NavComponent.vue';
import SearchComponent from '../components/SearchComponent.vue';
</script>

<style scoped>


</style>
