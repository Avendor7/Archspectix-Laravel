<template>
    <div class="input">
        <input 
            class="searchBox bg-white dark:bg-slate-800 border-arch-purple text-arch-purple placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-arch-purple/50 dark:focus:ring-arch-purple/30" 
            v-model="query" 
            :placeholder="'Search'" 
            @keydown.enter="fetchData" 
        />
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref, type Ref } from 'vue';

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
    border: 3px solid var(--color-arch-purple);
    font-size: 3rem;
    border-radius: 0.5rem;
    padding: 0.5rem 1rem;
    margin: 20px auto;
    font-family: inherit;
    outline: none;
    transition: all 0.2s ease;
}

.searchBox:focus {
    transform: scale(1.02);
    box-shadow: 0 0 0 3px var(--color-primary-shadow);
}

.input {
    text-align: center;
}
</style>
