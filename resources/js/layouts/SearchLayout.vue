<template>
    <Head title="Archspectix" />
    <div>
        <NavComponent />
        <div class="inputContainer">
            <SearchComponent />
            <button @click="handleOpenModal" class="settingsIcon"></button>
        </div>
        <!-- Theme Status Indicator -->
        <div class="themeStatus">
            <span class="themeIcon">{{ currentTheme === 'dark' ? '🌙' : '☀️' }}</span>
            <span class="themeText">{{ currentTheme === 'dark' ? 'Dark Mode' : 'Light Mode' }}</span>
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
import { ref, onMounted } from 'vue';

const isModalOpened = ref(false);
const currentTheme = ref<'light' | 'dark'>('light');

const closeModal = () => {
    isModalOpened.value = false;
};

const handleOpenModal = () => {
    isModalOpened.value = true;
};

// Listen for theme changes
onMounted(() => {
    const updateTheme = () => {
        currentTheme.value = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        console.log(`🎨 Theme updated to: ${currentTheme.value}`);
    };
    
    // Initial theme
    updateTheme();
    
    // Watch for theme changes
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                updateTheme();
            }
        });
    });
    
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
    
    // Also listen for custom theme events
    window.addEventListener('themeChanged', (e: any) => {
        if (e.detail && e.detail.theme) {
            currentTheme.value = e.detail.theme;
            console.log(`📡 Theme changed event received: ${e.detail.theme}`);
        }
    });
});
</script>

<style scoped>
.settingsIcon {
    padding-left: 10px;
    height: 40px;
    width: 40px;
    color: rgb(51 65 85); /* text-slate-700 */
}

.dark .settingsIcon {
    color: rgb(203 213 225); /* dark:text-slate-300 */
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

.themeStatus {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    margin: 1rem 0;
    padding: 0.5rem 1rem;
    background-color: rgb(241 245 249); /* bg-slate-100 */
    border-radius: 0.5rem;
    font-size: 0.875rem;
    color: rgb(51 65 85); /* text-slate-700 */
    transition: all 0.3s ease;
}

.dark .themeStatus {
    background-color: rgb(30 41 59); /* dark:bg-slate-800 */
    color: rgb(203 213 225); /* dark:text-slate-300 */
}

.themeIcon {
    font-size: 1.25rem;
}

.themeText {
    font-weight: 500;
}
</style>
