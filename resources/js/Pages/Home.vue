<template>
    <Layout>

    <main class="flex-col-center">

       <div class="flex-col-center">
        <color-picker :isOpen="isModalOpened"  @modal-close="closeModal"/>
       </div>
        <SearchComponent @openModal="handleOpenModal" />

    </main>
    </Layout>

</template>

<script setup lang="ts">
import Layout from '../Layouts/Layout.vue';
import SearchComponent from '../Components/SearchComponent.vue';
import {ref} from "vue";

import ColorPicker from "../Components/ColorPicker.vue";


const isModalOpened = ref(false);

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
