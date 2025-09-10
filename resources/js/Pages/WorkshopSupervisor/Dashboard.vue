<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Default from '../../Layouts/Default.vue'
import PageHeader from '../../Components/Shared/PageHeader.vue'
import { ref, computed, onMounted } from 'vue'
import { CalendarIcon, UserGroupIcon, QrCodeIcon, ClockIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    workshops: Object,
    statistics: Object,
    recentCheckIns: Array,
})

// Theme integration
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

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatTime = (timeString) => {
    return new Date(`2000-01-01T${timeString}`).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    })
}

const getWorkshopStatus = (workshop) => {
    const now = new Date()
    const workshopDate = new Date(workshop.date)
    const startTime = new Date(`${workshop.date}T${workshop.start_time}`)
    const endTime = new Date(`${workshop.date}T${workshop.end_time}`)
    
    if (now < startTime) {
        return { text: 'Upcoming', class: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' }
    } else if (now >= startTime && now <= endTime) {
        return { text: 'In Progress', class: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' }
    } else {
        return { text: 'Completed', class: 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400' }
    }
}

const getCapacityPercentage = (workshop) => {
    if (!workshop.capacity || workshop.capacity === 0) return 0
    return Math.round((workshop.registrations_count / workshop.capacity) * 100)
}
</script>

<template>
    <Head title="Workshop Supervisor Dashboard" />
    
    <Default>
        <div class="max-w-7xl mx-auto" :style="themeStyles">
            <PageHeader 
                title="Dashboard" 
                description="Monitor and manage your assigned workshops"
                :show-action="false"
            />

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg" :style="{ backgroundColor: `rgba(${themeColor.rgb}, 0.1)` }">
                            <CalendarIcon class="w-6 h-6" :style="{ color: themeColor.primary }" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Workshops</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics?.total_workshops || 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-green-100 dark:bg-green-900/20">
                            <ClockIcon class="w-6 h-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Today's Workshops</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics?.todays_workshops || 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900/20">
                            <UserGroupIcon class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Attendees</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics?.total_attendees || 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-purple-100 dark:bg-purple-900/20">
                            <QrCodeIcon class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Today's Check-ins</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics?.todays_checkins || 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- My Workshops -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">My Workshops</h3>
                            <a :href="route('workshop-supervisor.workshops.index')" class="text-sm" :style="{ color: themeColor.primary }">
                                View All
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        <div v-if="workshops?.data && workshops.data.length > 0" class="space-y-4">
                            <div v-for="workshop in workshops.data.slice(0, 5)" :key="workshop.id" 
                                 class="flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ workshop.title }}</h4>
                                    <div class="flex items-center mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        <CalendarIcon class="w-4 h-4 mr-1" />
                                        {{ formatDate(workshop.date) }} at {{ formatTime(workshop.start_time) }}
                                    </div>
                                    <div class="flex items-center mt-1">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                              :class="getWorkshopStatus(workshop).class">
                                            {{ getWorkshopStatus(workshop).text }}
                                        </span>
                                        <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">
                                            {{ workshop.registrations_count || 0 }}/{{ workshop.capacity }} registered
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a :href="route('workshop-supervisor.workshops.show', workshop.id)" 
                                       class="text-xs px-3 py-1 rounded-lg border transition-colors"
                                       :style="{ borderColor: themeColor.primary, color: themeColor.primary }"
                                       @mouseenter="$event.target.style.backgroundColor = `rgba(${themeColor.rgb}, 0.1)`"
                                       @mouseleave="$event.target.style.backgroundColor = 'transparent'">
                                        View
                                    </a>
                                    <a v-if="getWorkshopStatus(workshop).text === 'In Progress' || getWorkshopStatus(workshop).text === 'Upcoming'" 
                                       :href="route('workshop-supervisor.check-ins.scanner', workshop.id)" 
                                       class="text-xs px-3 py-1 rounded-lg text-white transition-colors"
                                       :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                                        QR Scanner
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <CalendarIcon class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No workshops assigned</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You don't have any workshops assigned yet.</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Check-ins -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Check-ins</h3>
                            <a :href="route('workshop-supervisor.check-ins.index')" class="text-sm" :style="{ color: themeColor.primary }">
                                View All
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        <div v-if="recentCheckIns && recentCheckIns.length > 0" class="space-y-4">
                            <div v-for="checkIn in recentCheckIns.slice(0, 8)" :key="checkIn.id" 
                                 class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                            {{ checkIn.user?.name?.charAt(0).toUpperCase() || 'U' }}
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ checkIn.user?.name || 'Unknown User' }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ checkIn.workshop?.title || 'Workshop' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatTime(checkIn.checked_in_at) }}</p>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                        Checked In
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <QrCodeIcon class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No recent check-ins</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Check-ins will appear here once workshops start.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a :href="route('workshop-supervisor.workshops.index')" 
                           class="flex items-center p-4 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                            <CalendarIcon class="w-6 h-6 mr-3" :style="{ color: themeColor.primary }" />
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">View My Workshops</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">See all assigned workshops</p>
                            </div>
                        </a>
                        
                        <a :href="route('workshop-supervisor.check-ins.index')" 
                           class="flex items-center p-4 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                            <QrCodeIcon class="w-6 h-6 mr-3" :style="{ color: themeColor.primary }" />
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">QR Code Scanner</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Scan attendee check-ins</p>
                            </div>
                        </a>
                        
                        <a :href="route('workshop-supervisor.reports.index')" 
                           class="flex items-center p-4 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                            <UserGroupIcon class="w-6 h-6 mr-3" :style="{ color: themeColor.primary }" />
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Attendance Reports</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">View workshop attendance</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

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