<template>
    <SearchLayout>
        <main class="flex-col-center"></main>
    </SearchLayout>
</template>

<script setup lang="ts">
import SearchLayout from '@/Layouts/SearchLayout.vue';
</script>

<style scoped>
table {
    border-collapse: collapse;
    width: 100%;
}
th,
td {
    border: 1px solid var(--color-primary);
    padding: 10px;
    text-align: left;
}

th {
    background-color: #010101;
}
</style>
