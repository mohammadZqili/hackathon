<template>
    <Head title="Tracks - Team Lead" />
    <Default>
        <div class="w-full h-full overflow-hidden" :style="themeStyles">
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
                
                <!-- Track Selection Section (if user doesn't have team or track) -->
                <div v-if="!userTrack && canSelectTrack" class="self-stretch flex flex-row items-start justify-end py-3 px-4 text-center text-white">
                    <div class="flex gap-4">
                        <select v-model="selectedTrackId" 
                            class="rounded-xl border border-gray-300 dark:border-gray-600 py-3 px-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                            <option value="">Select a track to join</option>
                            <option v-for="track in availableTracks" :key="track.id" :value="track.id">
                                {{ track.name }}
                            </option>
                        </select>
                        <button @click="joinTrack" :disabled="!selectedTrackId || processing"
                            class="rounded-xl flex flex-row items-center justify-center py-3 px-4 box-border max-h-[40px] transition-opacity"
                            :style="{ background: `linear-gradient(rgba(79, 150, 115, 0.75), rgba(79, 150, 115, 0.75)), ${themeColor.primary}` }"
                            :class="{ 'opacity-50 cursor-not-allowed': !selectedTrackId || processing }">
                            <b class="relative leading-[14.66px]">
                                {{ processing ? 'Joining...' : 'Join Track' }}
                            </b>
                        </button>
                    </div>
                </div>
                
                <!-- Current Track Info -->
                <div v-if="userTrack" class="self-stretch flex flex-col items-start justify-start p-4">
                    <div class="w-full bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="font-medium text-green-800 dark:text-green-200">You are registered for:</span>
                        </div>
                        <div class="text-green-700 dark:text-green-300">
                            <p class="font-semibold">{{ userTrack.name }}</p>
                            <p class="text-sm">{{ userTrack.description }}</p>
                            <p class="text-sm">Supervisor: {{ userTrack.supervisor?.name || 'TBA' }}</p>
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
import { router } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'

const props = defineProps({
    tracks: Array,
    userTrack: Object,
    team: Object,
    canSelectTrack: Boolean
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

const selectedTrackId = ref('')
const processing = ref(false)

const availableTracks = computed(() => {
    if (props.userTrack) return []
    return props.tracks || []
})

const joinTrack = () => {
    if (selectedTrackId.value) {
        processing.value = true
        router.post(route('team-lead.tracks.join'), {
            track_id: selectedTrackId.value
        }, {
            onSuccess: () => {
                processing.value = false
                selectedTrackId.value = ''
            },
            onError: () => {
                processing.value = false
            }
        })
    }
}
</script>

<style scoped>
input[type="text"]:focus,
select:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>