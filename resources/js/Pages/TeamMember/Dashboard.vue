<template>
    <Head title="Dashboard - Team Member" />
    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-[32px] font-bold text-gray-900 dark:text-white leading-10">Dashboard</h1>
            </div>

            <!-- Summary Section -->
            <div class="mb-6">
                <h2 class="text-[22px] font-bold text-gray-900 dark:text-white leading-7 mb-5">Summary</h2>
            </div>

            <!-- Summary Cards -->
            <div class="space-y-6">
                <!-- My Team Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm flex items-start justify-between gap-6">
                    <div class="flex-1 max-w-[573px]">
                        <div class="mb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">My Team</h3>
                            <p class="text-sm leading-[21px]" :style="{ color: themeColor.primary }">
                                Manage your team members and their roles.
                            </p>
                        </div>
                        <Link :href="route('team-member.team.index')"
                              class="inline-flex items-center justify-center px-4 py-2 rounded-xl font-medium text-sm transition-colors"
                              :style="{
                                backgroundColor: themeColor.primary + '20',
                                color: themeColor.primary
                            }"
                              @mouseenter="$event.target.style.backgroundColor = themeColor.primary + '30'"
                              @mouseleave="$event.target.style.backgroundColor = themeColor.primary + '20'">
                            View Team
                        </Link>
                    </div>
                    <div class="w-[280px] h-[161px] rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700">
                        <svg class="w-full h-full" viewBox="0 0 280 161" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="280" height="161" :fill="themeColor.primary + '10'" />
                            <g opacity="0.5">
                                <circle cx="140" cy="60" r="25" :fill="themeColor.primary + '40'" />
                                <circle cx="100" cy="90" r="20" :fill="themeColor.primary + '30'" />
                                <circle cx="180" cy="90" r="20" :fill="themeColor.primary + '30'" />
                            </g>
                            <path d="M140 80C155 80 168 93 168 108V161H112V108C112 93 125 80 140 80Z" :fill="themeColor.primary + '20'" />
                            <path d="M100 105C110 105 119 114 119 124V161H81V124C81 114 90 105 100 105Z" :fill="themeColor.primary + '15'" />
                            <path d="M180 105C190 105 199 114 199 124V161H161V124C161 114 170 105 180 105Z" :fill="themeColor.primary + '15'" />
                        </svg>
                    </div>
                </div>

                <!-- Idea Status Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm flex items-start justify-between gap-6">
                    <div class="flex-1 max-w-[573px]">
                        <div class="mb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Idea Status</h3>
                            <p class="text-sm leading-[21px]" :style="{ color: themeColor.primary }">
                                Track the progress of your ideas and initiatives.
                            </p>
                        </div>
                        <Link :href="route('team-member.idea.index')"
                              class="inline-flex items-center justify-center px-4 py-2 rounded-xl font-medium text-sm transition-colors"
                              :style="{
                                backgroundColor: themeColor.primary + '20',
                                color: themeColor.primary
                            }"
                              @mouseenter="$event.target.style.backgroundColor = themeColor.primary + '30'"
                              @mouseleave="$event.target.style.backgroundColor = themeColor.primary + '20'">
                            View Ideas
                        </Link>
                    </div>
                    <div class="w-[280px] h-[161px] rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700">
                        <svg class="w-full h-full" viewBox="0 0 280 161" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="280" height="161" :fill="themeColor.primary + '10'" />
                            <circle cx="140" cy="80" r="40" :stroke="themeColor.primary + '40'" stroke-width="3" fill="none" />
                            <path d="M140 50 L150 70 L175 70 L155 85 L165 105 L140 90 L115 105 L125 85 L105 70 L130 70 Z" :fill="themeColor.primary + '30'" />
                            <rect x="60" y="120" width="160" height="8" rx="4" :fill="themeColor.primary + '20'" />
                            <rect x="60" y="120" :width="idea ? (idea.progress || 0) * 1.6 : 40" height="8" rx="4" :fill="themeColor.primary" />
                        </svg>
                    </div>
                </div>

                <!-- Upcoming Workshops Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm flex items-start justify-between gap-6">
                    <div class="flex-1 max-w-[573px]">
                        <div class="mb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Upcoming Workshops</h3>
                            <p class="text-sm leading-[21px]" :style="{ color: themeColor.primary }">
                                See the schedule for upcoming workshops and training sessions.
                            </p>
                        </div>
                        <Link :href="route('team-member.workshops.index')"
                              class="inline-flex items-center justify-center px-4 py-2 rounded-xl font-medium text-sm transition-colors"
                              :style="{
                                backgroundColor: themeColor.primary + '20',
                                color: themeColor.primary
                            }"
                              @mouseenter="$event.target.style.backgroundColor = themeColor.primary + '30'"
                              @mouseleave="$event.target.style.backgroundColor = themeColor.primary + '20'">
                            View Workshops
                        </Link>
                    </div>
                    <div class="w-[280px] h-[161px] rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700">
                        <svg class="w-full h-full" viewBox="0 0 280 161" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="280" height="161" :fill="themeColor.primary + '10'" />
                            <rect x="40" y="40" width="200" height="100" rx="8" :fill="themeColor.primary + '20'" />
                            <rect x="60" y="60" width="160" height="8" rx="4" :fill="themeColor.primary + '30'" />
                            <rect x="60" y="80" width="120" height="8" rx="4" :fill="themeColor.primary + '25'" />
                            <rect x="60" y="100" width="140" height="8" rx="4" :fill="themeColor.primary + '25'" />
                            <circle cx="240" cy="40" r="15" :fill="themeColor.primary + '40'" />
                            <text x="235" y="45" fill="white" font-size="14" font-weight="bold">{{ upcomingWorkshopsCount || '3' }}</text>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-lg" :style="{ backgroundColor: themeColor.primary + '20' }">
                            <svg class="w-6 h-6" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats?.team_name || 'No Team' }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">My Team</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-lg" :style="{ backgroundColor: themeColor.primary + '20' }">
                            <svg class="w-6 h-6" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ team?.track?.name || 'Not Selected' }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Current Track</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-lg" :style="{ backgroundColor: themeColor.primary + '20' }">
                            <svg class="w-6 h-6" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.workshops_registered || 0 }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Workshops Registered</p>
                </div>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'

const props = defineProps({
    team: Object,
    idea: Object,
    workshops: Array,
    stats: Object
})

const upcomingWorkshopsCount = computed(() => props.workshops?.length || 0)

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
