<template>
  <div v-if="!isLoading">
    <div class="container">
        <h1>{{data.results[0].Name}}</h1>
        <table class="resource">
            <tbody>
                <tr>
                    <th scope="col">Name</th>
                    <td>{{ data.results[0].Name }}</td>
                </tr>
                <tr>
                    <th>Version</th>
                    <td>{{ data.results[0].Version }}</td>
                </tr>
                <tr>
                  <th>URL</th>
                  <td><a :href="data.results[0].URL">{{ data.results[0].URL }}</a></td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td>{{data.results[0].Description}}</td>
                </tr>
                <tr>
                    <th>License</th>
                    <td><span v-for="licence in data.results[0].License">{{licence}}</span></td>
                </tr>
                <tr>
                    <th>Maintainer</th>
                    <td>{{data.results[0].Maintainer}}</td>
                </tr>
                <tr>
                    <th>Number of Votes</th>
                    <td>{{data.results[0].NumVotes}}</td>
                </tr>
                <tr>
                    <th>Last Modified</th>
                    <td>{{ formatDate(data.results[0].LastModified) }}</td>
                </tr>
                <tr>
                    <th>Out of Date</th>
                    <td>{{ formatDate(data.results[0].OutOfDate) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
      <div class="col-container">
          <div class="column">
              <h2>Dependencies</h2>
              <ul>
                  <li v-for="(value, index) in data.results[0].Depends" :key="index">{{ value }}</li>
              </ul>
          </div>

          <div class="column">
              <h2>Make Dependencies</h2>
              <ul>
                  <li v-for="(value, index) in data.results[0].MakeDepends" :key="index">{{ value }}</li>
              </ul>
          </div>
      </div>
  </div>
</template>

<script setup lang="ts">
import { onBeforeMount, ref } from 'vue';
import { useRoute } from 'vue-router';
import axios from "axios";

interface Data {
    resultcount: number,
    results: AURPackage[],
    type: string,
    version: number
}

interface AURPackage {
    Depends: string[];
    Description: string;
    FirstSubmitted: number;
    ID: number;
    Keywords: string[];
    LastModified: Date;
    License: string[];
    Maintainer: string;
    MakeDepends: string[];
    Name: string;
    NumVotes: number;
    OutOfDate: Date;
    PackageBase: string;
    PackageBaseID: number;
    Popularity: number;
    Submitter: string;
    URL: string;
    URLPath: string;
    Version: string;
}

const data = ref<Data>({
    resultcount: 0,
    results: [],
    type: '',
    version: 0,
});

const isLoading = ref(false);
const route = useRoute();
const query = String(route.params.query); // You can also use a type guard for better TypeScript support

function fetchData() {
    isLoading.value = true;
    let url = import.meta.env.VITE_API_URL+"/aur/info?value=" + query;
    axios
        .get(url)
        .then((response) => {
            data.value = response.data;
        })
        .catch((error) => {
            console.error(error);
        })
        .finally(() => {
            isLoading.value = false;
        });
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

onBeforeMount(() => {
    fetchData();
});
</script>

<style scoped>
table {
    border-collapse: collapse;
    width: 100%;
}

.resource {
    box-shadow: 0 25px 50px -12px #673ab888;
}

th,
td {
    border: 2px solid #673ab888;
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
    padding-top:20px;
    width:50%;
    justify-content: center;
    align-items: center;
}
/* dependency boxes*/

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
    border: 2px solid var(--color-primary-shadow);
    box-shadow: 0 25px 50px -12px var(--color-primary-shadow);
}

h2 {
    font-weight: bold;
    margin-bottom: 15px;
}

ul{
    padding-left:20px;
}
</style>
