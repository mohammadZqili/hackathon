<template>
    <div class="flex flex-row items-start justify-between flex-wrap content-start p-4 gap-x-0 gap-y-3">
        <div class="w-72 flex flex-col items-start justify-start min-w-[288px]">
            <h1 class="text-[32px] font-bold text-gray-900 dark:text-white leading-10">{{ title }}</h1>
            <p v-if="subtitle" class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ subtitle }}</p>
        </div>
        
        <div v-if="showActionButton" class="flex items-center gap-3">
            <slot name="actions">
                <button v-if="actionButtonText"
                        @click="$emit('action')"
                        class="rounded-xl h-8 overflow-hidden flex flex-row items-center justify-center py-0 px-4 min-w-[84px] max-w-[480px] text-center text-sm text-white font-medium transition-all duration-200 hover:shadow-md"
                        :style="{
                            background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                        }">
                    <div class="overflow-hidden flex flex-col items-center justify-start">
                        <div class="self-stretch relative leading-[21px] font-medium overflow-hidden text-ellipsis whitespace-nowrap">
                            {{ actionButtonText }}
                        </div>
                    </div>
                </button>
            </slot>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

defineProps({
    title: {
        type: String,
        required: true
    },
    subtitle: {
        type: String,
        default: ''
    },
    showActionButton: {
        type: Boolean,
        default: false
    },
    actionButtonText: {
        type: String,
        default: ''
    }
})

defineEmits(['action'])

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