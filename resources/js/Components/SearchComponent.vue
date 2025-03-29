<template>
    <div class="inputContainer">
    <input class="searchBox" type="query" v-model="query"
           :placeholder="'SearchComponent'"
           @keydown.enter="fetchData"/>
    <FontAwesomeIcon @click="openModal" :icon="faGear"
                     class="settingsIcon"/>
    </div>
</template>

<script setup lang="ts">
import { faGear } from "@fortawesome/free-solid-svg-icons/faGear";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { router } from "@inertiajs/vue3";
import { ref, type Ref } from "vue";

export interface Result {
    source: string;
    name: string;
    version: string;
    repo: string;
    last_updated_date: Date;
    flagged_date: Date;
}

const query: Ref<string> = ref("");
const isLoading: Ref<boolean> = ref(false);


// Define the emit for openModal
const emit = defineEmits(['openModal']);

function fetchData() {
    if (!query.value.trim()) {
        return;
    }

    isLoading.value = true;

    router.get('/search',
        { value: query.value },
        {
        onFinish: () => {
            isLoading.value = false;
            },
}
    );
}

const openModal = () => {
  emit('openModal');
};
</script>

<style scoped>
.searchBox {
    border: 3px solid var(--color-primary);
    font-size: 3rem;
    border-radius: .5rem;
    padding: .5rem 1rem;
    margin: 20px auto;
    font-family: inherit;
    outline: none;
    box-shadow: 0 20px 20px -20px var(--color-primary-shadow);
    background: var(--color-background);
    color: var(--color-text);
}

.inputContainer {
    text-align: center;
}
.settingsIcon{
    padding-left:10px;
    height:40px;
    width:40px;
    color: var(--color-primary);
}
.settingsIcon:hover{
    cursor: pointer;
}
</style>
