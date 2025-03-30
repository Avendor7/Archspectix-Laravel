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

</style>
