<template>
    <div class="input">
        <input class="searchBox" type="query" v-model="query" :placeholder="'SearchComponent'" @keydown.enter="fetchData" />
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref, type Ref } from 'vue';

export interface Result {
    source: string;
    name: string;
    version: string;
    repo: string;
    last_updated_date: Date;
    flagged_date: Date;
}

const query: Ref<string> = ref('');
const isLoading: Ref<boolean> = ref(false);

function fetchData() {
    if (!query.value.trim()) {
        return;
    }

    isLoading.value = true;

    router.get(
        '/search',
        { value: query.value },
        {
            onFinish: () => {
                isLoading.value = false;
            },
        },
    );
}
</script>

<style scoped>
.searchBox {
    border: 3px solid var(--color-primary);
    font-size: 3rem;
    border-radius: 0.5rem;
    padding: 0.5rem 1rem;
    margin: 20px auto;
    font-family: inherit;
    outline: none;
    box-shadow: 0 20px 20px -20px var(--color-primary-shadow);
    background: var(--color-background);
    color: var(--color-text);
}

.input {
    text-align: center;
}
</style>
