<template>
    <div class="flex flex-col items-start justify-start py-3 px-4">
        <div class="self-stretch h-12 flex flex-col items-start justify-start min-w-[160px] max-w-2xl">
            <div class="self-stretch flex-1 rounded-xl flex flex-row items-start justify-start">
                <div class="self-stretch w-10 rounded-tl-xl rounded-tr-none rounded-br-none rounded-bl-xl flex items-center justify-center"
                     :style="{ backgroundColor: themeColor.primary + '20' }">
                    <svg class="w-5 h-5" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <div class="self-stretch flex-1 rounded-tl-none rounded-tr-xl rounded-br-xl rounded-bl-none bg-gray-100 dark:bg-gray-700 overflow-hidden flex flex-row items-center justify-start py-2 pl-2 pr-4">
                    <input :value="modelValue"
                           @input="$emit('update:modelValue', $event.target.value)"
                           type="text"
                           :placeholder="placeholder"
                           class="w-full bg-transparent text-gray-700 dark:text-gray-300 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none">
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: 'Search...'
    }
})

defineEmits(['update:modelValue'])

// Theme colors
const themeColor = ref({
    primary: '#0d9488',
    hover: '#0f766e',
    rgb: '13, 148, 136',
    gradientFrom: '#0d9488',
    gradientTo: '#14b8a6'
})

onMounted(() => {
    const root = document.documentElement
    const primary = getComputedStyle(root).getPropertyValue('--primary-color').trim() || '#0d9488'
    const hover = getComputedStyle(root).getPropertyValue('--primary-hover').trim() || '#0f766e'
    const rgb = getComputedStyle(root).getPropertyValue('--primary-color-rgb').trim() || '13, 148, 136'
    const gradientFrom = getComputedStyle(root).getPropertyValue('--primary-gradient-from').trim() || '#0d9488'
    const gradientTo = getComputedStyle(root).getPropertyValue('--primary-gradient-to').trim() || '#14b8a6'

    themeColor.value = {
        primary: primary || themeColor.value.primary,
        hover: hover || themeColor.value.hover,
        rgb: rgb || themeColor.value.rgb,
        gradientFrom: gradientFrom || themeColor.value.gradientFrom,
        gradientTo: gradientTo || themeColor.value.gradientTo
    }
})
</script>