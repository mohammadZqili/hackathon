<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import PageHeader from '../../../Components/Shared/PageHeader.vue'
import { ref, computed, onMounted } from 'vue'
import { CalendarIcon, ClockIcon, UserGroupIcon, AcademicCapIcon, MapPinIcon, UserIcon, CheckCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    workshops: Array,
    statistics: Object,
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
        weekday: 'short',
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
    const startTime = new Date(`${workshop.date}T${workshop.start_time}`)
    const endTime = new Date(`${workshop.date}T${workshop.end_time}`)
    
    if (now < startTime) {
        return { 
            text: 'Upcoming', 
            class: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
            icon: CalendarIcon 
        }
    } else if (now >= startTime && now <= endTime) {
        return { 
            text: 'In Progress', 
            class: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
            icon: ClockIcon 
        }
    } else {
        return { 
            text: 'Completed', 
            class: 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
            icon: CheckCircleIcon 
        }
    }
}

const groupWorkshopsByStatus = computed(() => {
    const groups = {
        upcoming: [],
        inProgress: [],
        completed: []
    }
    
    if (props.workshops) {
        props.workshops.forEach(workshop => {
            const status = getWorkshopStatus(workshop)
            if (status.text === 'Upcoming') {
                groups.upcoming.push(workshop)
            } else if (status.text === 'In Progress') {
                groups.inProgress.push(workshop)
            } else {
                groups.completed.push(workshop)
            }
        })
    }
    
    // Sort by date
    groups.upcoming.sort((a, b) => new Date(a.date) - new Date(b.date))
    groups.inProgress.sort((a, b) => new Date(a.date) - new Date(b.date))
    groups.completed.sort((a, b) => new Date(b.date) - new Date(a.date))
    
    return groups
})

const getAttendanceStatus = (workshop) => {
    // This would be determined by actual attendance data from the backend
    // For now, we'll use the workshop status as a proxy
    const status = getWorkshopStatus(workshop)
    if (status.text === 'Completed') {
        // In a real implementation, this would check actual attendance records
        return workshop.attended ? 'attended' : 'missed'
    }
    return 'pending'
}
</script>

<template>
    <Head title="My Workshops" />
    
    <Default>
        <div class="max-w-7xl mx-auto" :style="themeStyles">
            <PageHeader 
                title="My Workshops" 
                description="View and manage your workshop registrations"
                :show-action="false"
            />

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg" :style="{ backgroundColor: `rgba(${themeColor.rgb}, 0.1)` }">
                            <AcademicCapIcon class="w-6 h-6" :style="{ color: themeColor.primary }" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Registered</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics?.total_registered || 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900/20">
                            <CalendarIcon class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Upcoming</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ groupWorkshopsByStatus.upcoming.length }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-green-100 dark:bg-green-900/20">
                            <CheckCircleIcon class="w-6 h-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Attended</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics?.attended || 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-purple-100 dark:bg-purple-900/20">
                            <ClockIcon class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">In Progress</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ groupWorkshopsByStatus.inProgress.length }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="workshops && workshops.length > 0" class="space-y-8">
                <!-- Upcoming Workshops -->
                <div v-if="groupWorkshopsByStatus.upcoming.length > 0">
                    <div class="flex items-center mb-4">
                        <CalendarIcon class="w-5 h-5 mr-2" :style="{ color: themeColor.primary }" />
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Upcoming Workshops</h2>
                        <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">({{ groupWorkshopsByStatus.upcoming.length }})</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="workshop in groupWorkshopsByStatus.upcoming" :key="workshop.id" 
                             class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <!-- Workshop Image Placeholder -->
                            <div class="h-40 bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 flex items-center justify-center">
                                <AcademicCapIcon class="w-12 h-12 text-gray-400" />
                            </div>
                            
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-3">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white line-clamp-2">{{ workshop.title }}</h3>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ml-2"
                                          :class="getWorkshopStatus(workshop).class">
                                        {{ getWorkshopStatus(workshop).text }}
                                    </span>
                                </div>

                                <div class="space-y-2 mb-4 text-sm">
                                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                                        <CalendarIcon class="w-4 h-4 mr-2" />
                                        {{ formatDate(workshop.date) }}
                                    </div>
                                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                                        <ClockIcon class="w-4 h-4 mr-2" />
                                        {{ formatTime(workshop.start_time) }} - {{ formatTime(workshop.end_time) }}
                                    </div>
                                    <div v-if="workshop.location" class="flex items-center text-gray-600 dark:text-gray-400">
                                        <MapPinIcon class="w-4 h-4 mr-2" />
                                        {{ workshop.location }}
                                    </div>
                                    <div v-if="workshop.speaker" class="flex items-center text-gray-600 dark:text-gray-400">
                                        <UserIcon class="w-4 h-4 mr-2" />
                                        {{ workshop.speaker.name }}
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <a :href="route('visitor.workshops.show', workshop.id)" 
                                       class="text-sm px-3 py-2 rounded-lg border transition-colors"
                                       :style="{ borderColor: themeColor.primary, color: themeColor.primary }"
                                       @mouseenter="$event.target.style.backgroundColor = `rgba(${themeColor.rgb}, 0.1)`"
                                       @mouseleave="$event.target.style.backgroundColor = 'transparent'">
                                        View Details
                                    </a>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ workshop.registrations_count || 0 }}/{{ workshop.capacity }} registered
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- In Progress Workshops -->
                <div v-if="groupWorkshopsByStatus.inProgress.length > 0">
                    <div class="flex items-center mb-4">
                        <ClockIcon class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" />
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">In Progress</h2>
                        <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">({{ groupWorkshopsByStatus.inProgress.length }})</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="workshop in groupWorkshopsByStatus.inProgress" :key="workshop.id" 
                             class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-green-200 dark:border-green-700 overflow-hidden">
                            <!-- Workshop Image Placeholder with green tint -->
                            <div class="h-40 bg-gradient-to-br from-green-100 to-emerald-100 dark:from-green-900/20 dark:to-emerald-900/20 flex items-center justify-center">
                                <AcademicCapIcon class="w-12 h-12 text-green-600 dark:text-green-400" />
                            </div>
                            
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-3">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white line-clamp-2">{{ workshop.title }}</h3>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ml-2"
                                          :class="getWorkshopStatus(workshop).class">
                                        {{ getWorkshopStatus(workshop).text }}
                                    </span>
                                </div>

                                <div class="space-y-2 mb-4 text-sm">
                                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                                        <CalendarIcon class="w-4 h-4 mr-2" />
                                        {{ formatDate(workshop.date) }}
                                    </div>
                                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                                        <ClockIcon class="w-4 h-4 mr-2" />
                                        {{ formatTime(workshop.start_time) }} - {{ formatTime(workshop.end_time) }}
                                    </div>
                                    <div v-if="workshop.location" class="flex items-center text-gray-600 dark:text-gray-400">
                                        <MapPinIcon class="w-4 h-4 mr-2" />
                                        {{ workshop.location }}
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <a :href="route('visitor.workshops.show', workshop.id)" 
                                       class="text-sm px-3 py-2 rounded-lg text-white transition-colors"
                                       :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                                        Join Now
                                    </a>
                                    <div class="text-xs text-green-600 dark:text-green-400 font-medium">
                                        Currently Active
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Completed Workshops -->
                <div v-if="groupWorkshopsByStatus.completed.length > 0">
                    <div class="flex items-center mb-4">
                        <CheckCircleIcon class="w-5 h-5 mr-2 text-gray-600 dark:text-gray-400" />
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Completed</h2>
                        <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">({{ groupWorkshopsByStatus.completed.length }})</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="workshop in groupWorkshopsByStatus.completed" :key="workshop.id" 
                             class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden opacity-90">
                            <!-- Workshop Image Placeholder with grayscale -->
                            <div class="h-40 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-900/20 dark:to-gray-800/20 flex items-center justify-center">
                                <AcademicCapIcon class="w-12 h-12 text-gray-400" />
                            </div>
                            
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-3">
                                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 line-clamp-2">{{ workshop.title }}</h3>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ml-2"
                                          :class="getWorkshopStatus(workshop).class">
                                        {{ getWorkshopStatus(workshop).text }}
                                    </span>
                                </div>

                                <div class="space-y-2 mb-4 text-sm">
                                    <div class="flex items-center text-gray-500 dark:text-gray-500">
                                        <CalendarIcon class="w-4 h-4 mr-2" />
                                        {{ formatDate(workshop.date) }}
                                    </div>
                                    <div class="flex items-center text-gray-500 dark:text-gray-500">
                                        <ClockIcon class="w-4 h-4 mr-2" />
                                        {{ formatTime(workshop.start_time) }} - {{ formatTime(workshop.end_time) }}
                                    </div>
                                    <div v-if="workshop.speaker" class="flex items-center text-gray-500 dark:text-gray-500">
                                        <UserIcon class="w-4 h-4 mr-2" />
                                        {{ workshop.speaker.name }}
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <a :href="route('visitor.workshops.show', workshop.id)" 
                                       class="text-sm px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 transition-colors">
                                        View Details
                                    </a>
                                    <div class="text-xs">
                                        <span v-if="getAttendanceStatus(workshop) === 'attended'" 
                                              class="text-green-600 dark:text-green-400 font-medium">
                                            ✓ Attended
                                        </span>
                                        <span v-else class="text-gray-500 dark:text-gray-500">
                                            Not Attended
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <AcademicCapIcon class="mx-auto h-16 w-16 text-gray-400" />
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No workshops registered</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    You haven't registered for any workshops yet. Explore available workshops to get started.
                </p>
                <a :href="route('visitor.workshops.index')" 
                   class="mt-4 inline-flex items-center px-4 py-2 rounded-lg text-white transition-colors"
                   :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                    Browse Workshops
                </a>
            </div>
        </div>
    </Default>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

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