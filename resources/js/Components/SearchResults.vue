<script setup>
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
</script>

<template>

</template>

<style scoped>

</style>
