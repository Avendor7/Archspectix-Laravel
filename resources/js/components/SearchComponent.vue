<template>
    <div class="input">
        <input class="searchBox" v-model="query" :placeholder="'Search'" @keydown.enter="fetchData" />
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
    color: var(--color-arch-purple);
}

.input {
    text-align: center;
}
</style>
