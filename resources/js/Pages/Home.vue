<template>
    <Layout>

    <main class="flex-col-center">


        <color-picker :isOpen="isModalOpened" @modal-close="closeModal"/>
        <Search />
        <div v-if="hasLoaded" class="resource">
            <table>
                <thead>
                <tr>
                    <th>Source</th>
                    <th>Name</th>
                    <th>Version</th>
                    <th>Repository</th>
                    <th>Last Updated Date</th>
                    <th>Flagged Date</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="result in data" :key="result.name">
                    <td>{{ result.source }}</td>
                    <td v-if="result.source == 'ALR'">
                        <a href="https://google.ca">Google1</a>

                    </td>
                    <td v-else="result.source == 'AUR'">
                        <a href="https://google.ca">Google2</a>
                    </td>
                    <td>{{ result.version }}</td>
                    <td>{{ result.repo }}</td>
                    <td>{{ formatDate(result.last_updated_date) }}</td>
                    <td>{{ formatDate(result.flagged_date) }}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div v-else-if="isLoading">Loading...</div>
        <div v-else></div>
    </main>
    </Layout>

</template>

<script setup lang="ts">
import Layout from '../Layouts/Layout.vue';
import Search from './Search.vue';
import {ref, onMounted} from "vue";

import axios from "axios";
import ColorPicker from "../Components/ColorPicker.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import { faGear } from '@fortawesome/free-solid-svg-icons/faGear'

const data = ref<Result[]>([]);
import { router } from '@inertiajs/vue3'

const query = ref("");
const isLoading = ref(false);
const hasLoaded = ref(false);
const isFocusing = ref(false);


interface Result {
    source: string;
    name: string;
    version: string;
    repo: string;
    last_updated_date: Date;
    flagged_date: Date;
}




function formatDate(timestamp: Date) {
    return timestamp ? new Date(timestamp).toLocaleString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }) :"";
}

const isModalOpened = ref(false);
const openModal = () => {
    isModalOpened.value = true;
};
const closeModal = () => {
    isModalOpened.value = false;
};

onMounted(() => {
    // if (route.query.q) {
        //query.value = route.query.q as string;
    //     fetchData();
    // }
});

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
.resource {
    border: 1px solid #000;
    box-shadow: 0 25px 50px -12px var(--color-primary-shadow);
}

</style>
