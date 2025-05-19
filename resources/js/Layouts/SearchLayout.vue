<template>
    <Head title="Archspectix" />
    <div>
        <NavComponent />
        <div class="inputContainer">
            <SearchComponent />
            <FontAwesomeIcon @click="handleOpenModal" :icon="faGear" class="settingsIcon" />
        </div>
        <div class="flex-col-center">
            <color-picker :isOpen="isModalOpened" @modal-close="closeModal" />
        </div>
        <slot />
    </div>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import NavComponent from '../components/NavComponent.vue';
import SearchComponent from '../components/SearchComponent.vue';
import ColorPicker from '@/components/ColorPicker.vue';
import { ref } from 'vue';
import { faGear } from '@fortawesome/free-solid-svg-icons/faGear';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
const isModalOpened = ref(false);

const closeModal = () => {
    isModalOpened.value = false;
};

const handleOpenModal = () => {
    isModalOpened.value = true;
};
</script>
<style scoped>
.settingsIcon {
    padding-left: 10px;
    height: 40px;
    width: 40px;
    color: var(--color-primary);
}
.settingsIcon:hover {
    cursor: pointer;
}
.inputContainer {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 10px;
    margin-bottom: 10px;
    text-align: center;
}
</style>
