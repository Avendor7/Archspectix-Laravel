<template>
    <SearchLayout>
        <div class="overflow-hidden rounded-2xl border border-slate-900/90 bg-slate-100 backdrop-blur-sm dark:border-slate-400 dark:bg-slate-500">
            <table>
                <thead class="border-b border-slate-700/50 bg-slate-600 text-slate-200 dark:border-slate-400 dark:bg-slate-900">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Source</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Name</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Version</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Repository</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Last Updated Date</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Flagged Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-900/30 dark:divide-slate-600 dark:bg-slate-900">
                    <tr
                        v-for="result in data"
                        :key="result.name"
                        class="transition-colors duration-150 hover:bg-slate-700/30 dark:hover:bg-slate-700"
                    >
                        <td class="px-6 py-4" v-if="result.source == 'ALR'">
                            <span
                                class="inline-flex items-center rounded-full border border-arch-blue/30 bg-arch-blue/20 px-2.5 py-0.5 text-xs font-medium text-arch-purple dark:bg-arch-blue/10"
                                >{{ result.source }}</span
                            >
                        </td>
                        <td class="px-6 py-4" v-else-if="result.source == 'AUR'">
                            <span
                                class="inline-flex items-center rounded-full border border-arch-blue/30 bg-arch-blue/30 px-2.5 py-0.5 text-xs font-medium text-arch-blue dark:bg-arch-blue/20"
                                >{{ result.source }}</span
                            >
                        </td>
                        <td class="px-6 py-4" v-if="result.source == 'ALR'">
                            <Link
                                href="/alr-details"
                                :data="{ value: result.name }"
                                class="font-medium text-arch-purple transition-colors duration-200 hover:text-arch-cyan"
                            >
                                {{ result.name }}
                            </Link>
                        </td>
                        <td class="px-6 py-4" v-else-if="result.source == 'AUR'">
                            <Link
                                href="/aur-details"
                                :data="{ value: result.name }"
                                class="font-medium text-arch-purple transition-colors duration-200 hover:text-arch-cyan"
                            >
                                {{ result.name }}
                            </Link>
                        </td>
                        <td class="px-6 py-4 font-mono text-sm text-slate-700 dark:text-slate-300">{{ result.version }}</td>
                        <td class="px-6 py-4 text-slate-700 dark:text-slate-300">{{ result.repo }}</td>
                        <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300">{{ formatDate(result.last_updated_date) }}</td>
                        <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300">{{ formatDate(result.flagged_date) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </SearchLayout>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import SearchLayout from '@/layouts/SearchLayout.vue';
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
