<template>
    <SearchLayout>
        <div class="bg-slate-800/30 backdrop-blur-sm rounded-2xl border border-slate-700/50 overflow-hidden">
            <table>
                <thead class="bg-slate-800/50 border-b border-slate-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-300">Source</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-300">Name</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-300">Version</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-300">Repository</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-300">Last Updated Date</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-300">Flagged Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/30">
                    <tr v-for="result in data" :key="result.name" class="hover:bg-slate-700/20 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-arch-blue/20 text-arch-blue border border-arch-blue/30">{{ result.source }}</span>
                        </td>
                        <td class="px-6 py-4" v-if="result.source == 'ALR'">
                            <Link href="/alr-details" :data="{ value: result.name }" class="text-arch-purple hover:text-arch-cyan transition-colors duration-200 font-medium">
                                {{ result.name }}
                            </Link>
                        </td>
                        <td class="px-6 py-4" v-else-if="result.source == 'AUR'">
                            <Link href="/aur-details" :data="{ value: result.name }">
                                {{ result.name }}
                            </Link>
                        </td>
                        <td class="px-6 py-4 text-slate-300 font-mono text-sm">{{ result.version }}</td>
                        <td class="px-6 py-4 text-slate-400">{{ result.repo }}</td>
                        <td class="px-6 py-4 text-slate-400 text-sm">{{ formatDate(result.last_updated_date) }}</td>
                        <td class="px-6 py-4 text-slate-400 text-sm">{{ formatDate(result.flagged_date) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </SearchLayout>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import SearchLayout from '@/Layouts/SearchLayout.vue';
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

function formatDate(timestamp: Date) {
    return timestamp
        ? new Date(timestamp).toLocaleString('en-US', {
              year: 'numeric',
              month: 'long',
              day: 'numeric',
              hour: '2-digit',
              minute: '2-digit',
          })
        : '';
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
    padding: 10px;
    text-align: left;
}

</style>
