<template>
    <SearchLayout>
        <p v-if="error" role="status" class="text-arch-purple">{{ error }}</p>
        <div v-else-if="package">
            <h1 class="text-5xl font-bold mb-6 pb-4 text-arch-purple">{{ package.name }}</h1>
            <table class="resource bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-200">
                <tbody>
                    <tr>
                        <th>Version</th>
                        <td>{{ package.version }}</td>
                    </tr>
                    <tr>
                        <th>Repository</th>
                        <td>Omarchy stable / x86_64</td>
                    </tr>
                    <tr>
                        <th>Architecture</th>
                        <td>{{ package.arch }}</td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td>{{ package.description }}</td>
                    </tr>
                    <tr>
                        <th>URL</th>
                        <td><a :href="package.url" class="text-arch-purple">{{ package.url }}</a></td>
                    </tr>
                    <tr>
                        <th>License(s)</th>
                        <td>{{ package.licenses.join(', ') }}</td>
                    </tr>
                    <tr>
                        <th>Package Size</th>
                        <td>{{ formatSize(package.compressed_size) }}</td>
                    </tr>
                    <tr>
                        <th>Installed Size</th>
                        <td>{{ formatSize(package.installed_size) }}</td>
                    </tr>
                    <tr>
                        <th>Build Date</th>
                        <td>{{ package.build_date ? new Date(package.build_date).toLocaleString('en-US') : '' }}</td>
                    </tr>
                    <tr>
                        <th>Packager</th>
                        <td>{{ package.packager }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="mt-8 grid gap-3 md:grid-cols-3">
                <div class="rounded-xl border border-arch-purple p-5">
                    <h2 class="font-bold mb-3">Dependencies</h2>
                    <ul>
                        <li v-for="dependency in package.depends" :key="dependency">{{ dependency }}</li>
                    </ul>
                </div>
                <div class="rounded-xl border border-arch-purple p-5">
                    <h2 class="font-bold mb-3">Optional Dependencies</h2>
                    <ul>
                        <li v-for="dependency in package.optdepends" :key="dependency">{{ dependency }}</li>
                    </ul>
                </div>
                <div class="rounded-xl border border-arch-purple p-5">
                    <h2 class="font-bold mb-3">Make Dependencies</h2>
                    <ul>
                        <li v-for="dependency in package.makedepends" :key="dependency">{{ dependency }}</li>
                    </ul>
                </div>
            </div>
        </div>
        <p v-else>No Omarchy package found.</p>
    </SearchLayout>
</template>

<script setup lang="ts">
import SearchLayout from '@/layouts/SearchLayout.vue';

interface OmarchyPackage {
    name: string;
    version: string;
    description: string;
    arch: string;
    url: string;
    licenses: string[];
    compressed_size: number;
    installed_size: number;
    build_date: string | null;
    packager: string;
    depends: string[];
    optdepends: string[];
    makedepends: string[];
}

defineProps<{
    package: OmarchyPackage | null;
    error: string | null;
}>();

function formatSize(size: number) {
    return (size / 1024).toFixed(2) + ' KB';
}
</script>

<style scoped>
table {
    border-collapse: collapse;
    width: 100%;
}

.resource {
    box-shadow: 0 25px 50px -12px var(--color-primary-shadow);
}

th,
td {
    border: 1px solid var(--color-primary);
    padding: 10px;
    text-align: left;
}

th {
    width: 30%;
    white-space: nowrap;
}

td {
    overflow-wrap: anywhere;
}
</style>
