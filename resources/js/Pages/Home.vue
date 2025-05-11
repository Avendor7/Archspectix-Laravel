<template>
    <Layout>

    <main class="flex-col-center">

       <div class="flex-col-center">
        <color-picker :isOpen="isModalOpened"  @modal-close="closeModal"/>
       </div>
        <SearchComponent @openModal="handleOpenModal" />
        <SearchResultsComponent :data="props.results" />
    </main>
    </Layout>

</template>

<script setup lang="ts">
import Layout from '../Layouts/Layout.vue';
import SearchComponent from '../components/SearchComponent.vue';
import {ref, onMounted, defineProps} from "vue";

import ColorPicker from "../components/ColorPicker.vue";
import SearchResultsComponent from "../components/SearchResultsComponent.vue";

export interface Result {
    source: string;
    name: string;
    version: string;
    repo: string;
    last_updated_date: Date;
    flagged_date: Date;
}

const isModalOpened = ref(false);

const props = defineProps<{
    results: Result[];
}>();



onMounted(()    => {
    console.log(props.results);
})
const closeModal = () => {
    isModalOpened.value = false;
};

const handleOpenModal = () => {
    isModalOpened.value = true;
};

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
