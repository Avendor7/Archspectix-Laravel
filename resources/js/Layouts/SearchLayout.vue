<template>
    <Head title="Archspectix" />
    <!-- Background glow gradient -->
    <div
        aria-hidden="true"
        class="pointer-events-none fixed inset-0 -z-10 overflow-hidden"
    >
        <!-- Top-center purple glow -->
        <div
            class="absolute left-1/2 top-[-10%] h-[40rem] w-[40rem] -translate-x-1/2 rounded-full top-gradient blur-3xl"
        ></div>

        <!-- Bottom-right pink glow -->
        <div
            class="absolute right-[-10%] bottom-[-10%] h-[35rem] w-[35rem] rounded-full bg-fuchsia-600/10 blur-3xl"
        ></div>
    </div>
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
