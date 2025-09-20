<template>
    <Head title="Check-ins Management" />
    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <div class="self-stretch flex flex-row items-start justify-between flex-wrap content-start p-4 text-[32px] text-gray-900 dark:text-white">
                <div class="flex flex-col items-start justify-start gap-3 min-w-[288px]">
                    <div class="w-full max-w-[896px] flex flex-col items-start justify-start">
                        <b class="self-stretch relative leading-10">Workshop Check-ins</b>
                    </div>
                    <div class="flex flex-col items-start justify-start text-sm"
                        :style="{ color: themeColor.primary }">
                        <div class="self-stretch relative leading-[21px]">
                            Select a workshop to manage check-ins and track attendance for hackathon participants.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Workshops List -->
            <div v-if="workshops && workshops.length > 0" class="space-y-4">
                <div v-for="workshop in workshops" :key="workshop.id"
                    class="self-stretch flex flex-col items-start justify-start p-4">
                    <div class="self-stretch shadow-[0px_0px_4px_rgba(0,_0,_0,_0.1)] rounded-xl bg-gray-50 dark:bg-gray-800 overflow-hidden flex flex-row items-start justify-between p-4 gap-4">
                        <div class="flex-1 flex flex-col items-start justify-start gap-4">
                            <div class="self-stretch flex flex-col items-start justify-start gap-1">
                                <div class="self-stretch flex flex-col items-start justify-start text-sm"
                                    :style="{ color: themeColor.primary }">
                                    <div class="self-stretch relative leading-[21px]">
                                        Speaker: {{ workshop.speakers || 'TBA' }} |
                                        Date: {{ formatDate(workshop.date_time || workshop.start_time) }} |
                                        Time: {{ formatTime(workshop.date_time || workshop.start_time) }}
                                    </div>
                                </div>
                                <div class="self-stretch h-5 flex flex-col items-start justify-start text-base text-gray-900 dark:text-white">
                                    <b class="self-stretch relative leading-5">{{ workshop.title }}</b>
                                </div>
                                <div class="self-stretch flex flex-col items-start justify-start text-gray-600 dark:text-gray-400">
                                    <div class="self-stretch relative leading-[21px]">{{ workshop.description || 'Workshop description' }}</div>
                                </div>

                                <!-- Workshop Stats -->
                                <div class="flex items-center gap-6 mt-2 text-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-500 dark:text-gray-400">Registered:</span>
                                        <span class="font-semibold" :style="{ color: themeColor.primary }">
                                            {{ workshop.registered_count || 0 }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-500 dark:text-gray-400">Checked-in:</span>
                                        <span class="font-semibold text-green-600 dark:text-green-400">
                                            {{ workshop.checked_in_count || 0 }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-500 dark:text-gray-400">Capacity:</span>
                                        <span class="font-semibold text-gray-700 dark:text-gray-300">
                                            {{ workshop.seats || workshop.max_attendees || 'Unlimited' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <button
                                @click="goToCheckIn(workshop.id)"
                                class="rounded-xl h-8 overflow-hidden shrink-0 flex flex-row items-center justify-center py-0 px-4 box-border min-w-[84px] max-w-[480px] text-center text-white font-medium transition-all hover:shadow-md"
                                :style="{
                                    background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                                }">
                                <div class="overflow-hidden flex flex-col items-center justify-start">
                                    <div class="self-stretch relative leading-[21px] font-medium overflow-hidden text-ellipsis whitespace-nowrap">
                                        Check-In
                                    </div>
                                </div>
                            </button>
                        </div>

                        <!-- Workshop Image/Icon -->
                        <div class="w-[200px] h-[165px] relative rounded-xl overflow-hidden flex items-center justify-center"
                            :style="{
                                background: `linear-gradient(135deg, ${themeColor.primary}20, ${themeColor.primary}10)`
                            }">
                            <svg class="w-16 h-16" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="flex flex-col items-center justify-center py-16">
                <svg class="w-24 h-24 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                    </path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No Workshops Available</h3>
                <p class="text-gray-600 dark:text-gray-400 text-center max-w-md">
                    There are no workshops scheduled at the moment. Check back later or contact the admin for more information.
                </p>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'

const props = defineProps({
    workshops: {
        type: Array,
        default: () => []
    }
})

// Theme color configuration following GuacPanel standards
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

// Navigate to specific workshop check-in page
const goToCheckIn = (workshopId) => {
    // Using workshop-attendance route as a workaround since workshop route is not registering
    router.visit(`/track-supervisor/checkins/workshop/${workshopId}`)
}

// Format date from datetime string
const formatDate = (dateTime) => {
    if (!dateTime) return 'TBA'
    const date = new Date(dateTime)
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    })
}

// Format time from datetime string
const formatTime = (dateTime) => {
    if (!dateTime) return 'TBA'
    const date = new Date(dateTime)
    return date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>

<style scoped>
input[type="text"]:focus,
input[type="number"]:focus,
input[type="date"]:focus,
select:focus,
textarea:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}

input[type="checkbox"]:checked {
    background-color: var(--theme-primary) !important;
    border-color: var(--theme-primary) !important;
}
</style>