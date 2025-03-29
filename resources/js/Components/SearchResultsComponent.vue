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
<!--            <tr v-for="result in data" :key="result.name">-->
<!--                <td>{{ result.source }}</td>-->
<!--                <td v-if="result.source == 'ALR'">-->
<!--                    <a href="https://google.ca">Google1</a>-->

<!--                </td>-->
<!--                <td v-else-if="result.source == 'AUR'">-->
<!--                    <a href="https://google.ca">Google2</a>-->
<!--                </td>-->
<!--                <td>{{ result.version }}</td>-->
<!--                <td>{{ result.repo }}</td>-->
<!--                <td>{{ formatDate(result.last_updated_date) }}</td>-->
<!--                <td>{{ formatDate(result.flagged_date) }}</td>-->
<!--            </tr>-->
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import {onMounted} from 'vue';

const data = defineProps({
    data: Array,
});
onMounted(()    => {
    console.log(data)
})

</script>

<style scoped>
.resource {
    border: 1px solid #000;
    box-shadow: 0 25px 50px -12px var(--color-primary-shadow);
}
</style>
