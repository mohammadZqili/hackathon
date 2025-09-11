<template>
    <div class="relative">
        <!-- Language Switcher Button -->
        <button
            @click="isOpen = !isOpen"
            class="flex items-center gap-1 px-2 py-1 rounded-lg transition-colors text-sm"
            :class="[
                isRTL ? 'flex-row-reverse' : '',
                'hover:bg-gray-100 dark:hover:bg-gray-700'
            ]"
            :style="{ 
                backgroundColor: isOpen ? themeColor.primary + '10' : '',
                color: isOpen ? themeColor.primary : ''
            }"
            :title="`Switch language (${currentLanguage.native})`"
        >
            <span class="text-base">{{ currentLanguage.flag }}</span>
            <span class="font-medium hidden sm:inline">{{ currentLanguage.code.toUpperCase() }}</span>
            <svg 
                class="w-3 h-3 transition-transform"
                :class="[isOpen ? 'rotate-180' : '', flipIcon]"
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <!-- Language Dropdown -->
        <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-if="isOpen"
                @click.outside="isOpen = false"
                class="absolute mt-2 w-48 rounded-lg bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 z-50"
                :class="isRTL ? 'left-0' : 'right-0'"
            >
                <div class="py-1">
                    <button
                        v-for="(lang, code) in languages"
                        :key="code"
                        @click="handleSwitchLanguage(code)"
                        class="w-full px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        :class="[
                            locale === code ? 'bg-gray-50 dark:bg-gray-700' : '',
                            lang.direction === 'rtl' ? 'text-right flex flex-row-reverse' : 'text-left flex'
                        ]"
                        :style="{ 
                            color: locale === code ? themeColor.primary : ''
                        }"
                    >
                        <span class="text-lg">{{ lang.flag }}</span>
                        <span class="mx-3 flex-1">{{ lang.native }}</span>
                        <svg 
                            v-if="locale === code"
                            class="w-5 h-5"
                            fill="currentColor" 
                            viewBox="0 0 20 20"
                        >
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useLocalization } from '@/composables/useLocalization'

const { locale, isRTL, languages, currentLanguage, switchLanguage, flipIcon } = useLocalization()

const isOpen = ref(false)

// Theme color setup
const themeColor = ref({
    primary: '#0d9488',
    hover: '#0f766e',
    rgb: '13, 148, 136',
})

onMounted(() => {
    const root = document.documentElement
    const primary = getComputedStyle(root).getPropertyValue('--primary-color').trim() || '#0d9488'
    const hover = getComputedStyle(root).getPropertyValue('--primary-hover').trim() || '#0f766e'
    const rgb = getComputedStyle(root).getPropertyValue('--primary-color-rgb').trim() || '13, 148, 136'

    themeColor.value = {
        primary: primary || themeColor.value.primary,
        hover: hover || themeColor.value.hover,
        rgb: rgb || themeColor.value.rgb,
    }
})

const handleSwitchLanguage = async (code) => {
    await switchLanguage(code)
    isOpen.value = false
}
</script>