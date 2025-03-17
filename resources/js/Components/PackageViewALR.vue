<template>
    <div v-if="!isLoading">
        <div class="container">
            <h1>{{ data.results[0].pkgname }}</h1>
            <table class="resource">
                <tbody>
                    <tr>
                        <th>Name</th>
                        <td>{{ data.results[0].pkgname }}</td>
                    </tr>
                    <tr>
                        <th>Version</th>
                        <td>{{ data.results[0].pkgver }}</td>
                    </tr>
                    <tr>
                        <th>URL</th>
                        <td><a :href="data.results[0].url">{{ data.results[0].url }}</a></td>
                    </tr>
                    <tr>
                        <th>License(s)</th>
                        <td>{{ data.results[0].licenses }}</td>
                    </tr>
                    <tr>
                        <th>Package Size</th>
                        <td>{{ formatCompressedSize }}</td>
                    </tr>
                    <tr>
                        <th>Installed Size</th>
                        <td>{{ formatInstalledSize }}</td>
                    </tr>
                    <tr>
                        <th>Build Date</th>
                        <td>{{ data.results[0].build_date }}</td>
                    </tr>
                    <tr>
                        <!-- TODO possible to link or something here? -->
                        <th>Maintainers</th>
                        <td>{{ data.results[0].maintainers }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- TODO This should probably be in components somehow. Formatting on the text also necessary -->
        <div class="col-container">
            <div class="column">
                <h2>Dependencies</h2>
                <table>
                    <tbody>
                        <tr v-for="(value, index) in data.results[0].depends" :key="index">{{ value }}</tr>
                    </tbody>
                </table>
            </div>

            <div class="column">
                <h2>Optional Dependencies</h2>
                <table>
                    <tbody>
                        <tr v-for="(value, index) in data.results[0].optdepends" :key="index">{{ value }}</tr>
                    </tbody>
                </table>
            </div>

            <div class="column">
                <h2>Required by</h2>
                <table>
                    <tbody>
                        <tr v-for="(value, index) in data.results[0].makedepends" :key="index">{{ value }}</tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</template>

<script setup lang="ts">
import { onBeforeMount, ref, computed } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";

interface PackageInfo {
    pkgname: string;
    pkgbase: string;
    repo: string;
    arch: string;
    pkgver: string;
    pkgrel: number;
    epoch: number;
    pkgdesc: string;
    url: string;
    filename: string;
    compressed_size: number;
    installed_size: number;
    build_date: Date;
    last_update: Date | null;
    flag_date: Date | null;
    maintainers: string[];
    packager: string;
    groups: string[];
    licenses: string[];
    conflicts: string[];
    provides: string[];
    replaces: string[];
    depends: Dependency[];
    optdepends: OptDependency[];
    makedepends: string[];
    checkdepends: string[];
}
interface Dependency {
    pkg: string;
    version?: string;
    operator?: string;
    arch?: string;
}
interface OptDependency {
    name: string;
    description: string;
}
interface Data{
    version: number,
    limit: number,
    valid: boolean,
    results: PackageInfo[],
    type: string,
    num_pages: number,
    page: number
}
const data = ref<Data>({
    version: 0,
    limit: 0,
    valid: false,
    results: [],
    type: '',
    num_pages:  0,
    page: 0
});

const isLoading = ref(false);
const route = useRoute();
const query = String(route.params.query); // You can also use a type guard for better TypeScript support

function fetchData() {
    isLoading.value = true;
    console.log(query);
    let url = import.meta.env.VITE_API_URL+"/alr/info?value=" + query;
    console.log(url);
    axios
        .get(url)
        .then((response) => {
            data.value = response.data;
            console.log(data.value);
        })
        .catch((error) => {
            console.error(error);
        })
        .finally(() => {
            isLoading.value = false;
        });
}
onBeforeMount(() => {
    fetchData();
});

//I don't really like this block. Some duplication but Typescript seems to not like combining things
const formatCompressedSize = computed(() => {
    return formatSize(data.value.results[0].compressed_size);
});

const formatInstalledSize = computed(() => {
    return formatSize(data.value.results[0].installed_size);
});

const formatSize = (size: number) => {
    return (size / Math.pow(2, 10)).toFixed(2) + ' KB';
};

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

@media (prefers-color-scheme: dark) {
    th {
        background-color: #010101;
    }
}

@media (prefers-color-scheme: light) {
    th {
        background-color: #CCC;
    }
}


.container {
    width: 50%;
    justify-content: center;
    align-items: center;
}

.col-container {
    margin-top: 30px;
    display: flex;
    flex-wrap: nowrap;
    gap: 10px;

}

.column {
    background-color: #010101;
    flex-basis: 33.33%;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid var(--color-primary);
    box-shadow: 0 25px 50px -12px var(--color-primary-shadow);
}

h2 {
    font-weight: bold;
    margin-bottom: 15px;
}
</style>
