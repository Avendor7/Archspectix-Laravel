<template>
    <div class="resource">
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
                    <Link href="/alr-details" :data="{value: result.name}">
                        {{result.name}}
                    </Link>
                </td>
                <td v-else-if="result.source == 'AUR'">
                    <Link href="/aur-details" :data="{value: result.name}">
                        {{result.name}}
                    </Link>
                </td>
                <td>{{ result.version }}</td>
                <td>{{ result.repo }}</td>
                <td>{{ formatDate(result.last_updated_date) }}</td>
                <td>{{ formatDate(result.flagged_date) }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import {onMounted} from 'vue';
import {Link, usePage} from "@inertiajs/vue3";
import { defineProps } from 'vue';
interface Result {
    source: string;
    name: string;
    version: string;
    repo: string;
    last_updated_date: Date;
    flagged_date: Date;
}

const props = defineProps<{
    data: Result[];
}>();

const { data } = props;
const page = usePage();
onMounted(()    => {
    console.log(data)
})

function formatDate(timestamp: Date) {
    return timestamp ? new Date(timestamp).toLocaleString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }) :"";
}
</script>

<style scoped>
.resource {
    border: 1px solid #000;
    box-shadow: 0 25px 50px -12px var(--color-primary-shadow);
}

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
