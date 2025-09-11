<template>
    <Head title="Tracks - Team Member" />
    <Default>
        <div class="w-full h-full overflow-hidden bg-gray-50 dark:bg-gray-900" :style="themeStyles">
            <!-- Tracks Page based on Figma Design -->
            <div class="w-full h-[695px] overflow-hidden shrink-0 flex flex-col items-start justify-start max-w-[960px] text-[32px] text-gray-900 dark:text-white font-space-grotesk">
                <!-- Page Header -->
                <div class="self-stretch flex flex-row items-start justify-between flex-wrap content-start p-4">
                    <div class="flex flex-col items-start justify-start gap-3 min-w-[288px]">
                        <div class="w-[335px] flex flex-col items-start justify-start">
                            <b class="self-stretch relative leading-10">Tracks</b>
                        </div>
                        <div class="flex flex-col items-start justify-start text-sm"
                            :style="{ color: themeColor.primary }">
                            <div class="self-stretch relative leading-[21px]">Explore available tracks for the current hackathon edition.</div>
                        </div>
                    </div>
                </div>
                
                <!-- Tracks Table -->
                <div class="self-stretch flex flex-col items-start justify-start py-3 px-4 text-sm">
                    <div class="self-stretch rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 overflow-hidden flex flex-row items-start justify-start">
                        <div class="flex-1 flex flex-col items-start justify-start">
                            <!-- Table Header -->
                            <div class="self-stretch flex flex-col items-start justify-start">
                                <div class="self-stretch flex-1 bg-gray-100 dark:bg-gray-600 flex flex-row items-start justify-start">
                                    <div class="self-stretch w-[311px] flex flex-col items-start justify-start py-3 px-4 box-border">
                                        <div class="self-stretch relative leading-[21px] font-medium">Track Name</div>
                                    </div>
                                    <div class="self-stretch w-[310px] flex flex-col items-start justify-start py-3 px-4 box-border">
                                        <div class="self-stretch relative leading-[21px] font-medium">Description</div>
                                    </div>
                                    <div class="self-stretch w-[305px] flex flex-col items-start justify-start py-3 px-4 box-border">
                                        <div class="self-stretch relative leading-[21px] font-medium">Assigned Supervisor</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Table Body -->
                            <div class="self-stretch flex flex-col items-start justify-start"
                                :style="{ color: themeColor.primary }">
                                <div v-for="track in tracks" :key="track.id"
                                    class="self-stretch border-t border-gray-300 dark:border-gray-600 box-border h-[72px] flex flex-row items-start justify-start hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <div class="w-[311px] h-[72px] flex flex-col items-center justify-center py-2 px-4 box-border text-gray-900 dark:text-white">
                                        <div class="self-stretch relative leading-[21px]">{{ track.name }}</div>
                                    </div>
                                    <div class="w-[310px] h-[72px] flex flex-col items-center justify-center py-2 px-4 box-border">
                                        <div class="self-stretch relative leading-[21px]">{{ track.description }}</div>
                                    </div>
                                    <div class="w-[305px] h-[72px] flex flex-col items-center justify-center py-2 px-4 box-border">
                                        <div class="self-stretch relative leading-[21px]">{{ track.supervisor?.name || 'TBA' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Current Team Track Info -->
                <div v-if="teamTrack" class="self-stretch flex flex-col items-start justify-start p-4">
                    <div class="w-full bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium text-blue-800 dark:text-blue-200">Your team is registered for:</span>
                        </div>
                        <div class="text-blue-700 dark:text-blue-300">
                            <p class="font-semibold">{{ teamTrack.name }}</p>
                            <p class="text-sm">{{ teamTrack.description }}</p>
                            <p class="text-sm">Supervisor: {{ teamTrack.supervisor?.name || 'TBA' }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- No Team Track Info -->
                <div v-else-if="team && !teamTrack" class="self-stretch flex flex-col items-start justify-start p-4">
                    <div class="w-full bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <span class="font-medium text-yellow-800 dark:text-yellow-200">Track not selected</span>
                        </div>
                        <div class="text-yellow-700 dark:text-yellow-300">
                            <p class="text-sm">Your team hasn't selected a track yet. Please ask your team leader to choose one.</p>
                        </div>
                    </div>
                </div>
                
                <!-- No Team State -->
                <div v-else class="self-stretch flex flex-col items-start justify-start p-4">
                    <div class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium text-gray-700 dark:text-gray-300">Not in a team</span>
                        </div>
                        <div class="text-gray-600 dark:text-gray-400">
                            <p class="text-sm">You need to be part of a team to participate in a track.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'

const props = defineProps({
    tracks: Array,
    team: Object,
    teamTrack: Object
})

// Theme color setup
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

const themeStyles = computed(() => ({
    '--theme-primary': themeColor.value.primary,
    '--theme-hover': themeColor.value.hover,
    '--theme-rgb': themeColor.value.rgb,
    '--theme-gradient-from': themeColor.value.gradientFrom,
    '--theme-gradient-to': themeColor.value.gradientTo,
}))
</script>

<style scoped>
/* Theme styles are applied via CSS variables */
</style>