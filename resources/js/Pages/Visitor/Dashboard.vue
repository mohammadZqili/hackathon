<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../Layouts/Default.vue'
import PageHeader from '../../Components/Shared/PageHeader.vue'
import { ref, computed, onMounted } from 'vue'
import { CalendarIcon, ClockIcon, UserGroupIcon, AcademicCapIcon, ArrowRightIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    upcomingWorkshops: Array,
    myWorkshops: Array,
    statistics: Object,
    currentEdition: Object,
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

const isRegistered = (workshop) => {
    return props.myWorkshops?.some(myWorkshop => myWorkshop.id === workshop.id) || false
}
</script>

<template>
    <Head title="Dashboard" />
    
    <Default>
        <div class="max-w-7xl mx-auto" :style="themeStyles">
            <PageHeader 
                title="Welcome to Ruman Hackathon"
                :description="`Discover upcoming workshops and enhance your skills at ${currentEdition?.name || 'the current hackathon edition'}`"
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
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Available Workshops</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics?.total_workshops || 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900/20">
                            <CalendarIcon class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">My Registrations</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics?.my_registrations || 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-green-100 dark:bg-green-900/20">
                            <ClockIcon class="w-6 h-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Upcoming Today</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics?.today_workshops || 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-purple-100 dark:bg-purple-900/20">
                            <UserGroupIcon class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Workshops Attended</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics?.attended_workshops || 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Upcoming Workshops -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Upcoming Workshops</h3>
                            <a :href="route('visitor.workshops.index')" class="text-sm" :style="{ color: themeColor.primary }">
                                View All
                            </a>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Explore workshops designed to enhance your skills and knowledge</p>
                    </div>
                    <div class="p-6">
                        <div v-if="upcomingWorkshops && upcomingWorkshops.length > 0" class="space-y-4">
                            <div v-for="workshop in upcomingWorkshops.slice(0, 3)" :key="workshop.id" 
                                 class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:border-gray-300 dark:hover:border-gray-500 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ workshop.title }}</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">{{ workshop.description }}</p>
                                        <div class="flex items-center mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            <CalendarIcon class="w-4 h-4 mr-1" />
                                            {{ formatDate(workshop.date) }} at {{ formatTime(workshop.start_time) }}
                                        </div>
                                        <div class="flex items-center mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            <UserGroupIcon class="w-4 h-4 mr-1" />
                                            {{ workshop.registrations_count || 0 }}/{{ workshop.capacity }} registered
                                        </div>
                                        <div v-if="workshop.speaker" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            Speaker: {{ workshop.speaker.name }}
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end space-y-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                              :class="getWorkshopStatus(workshop).class">
                                            {{ getWorkshopStatus(workshop).text }}
                                        </span>
                                        <a v-if="!isRegistered(workshop) && (workshop.registrations_count < workshop.capacity)"
                                           :href="route('visitor.workshops.show', workshop.id)"
                                           class="text-xs px-3 py-1 rounded-lg text-white transition-colors"
                                           :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                                            Register
                                        </a>
                                        <span v-else-if="isRegistered(workshop)" 
                                              class="text-xs px-3 py-1 rounded-lg bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                            Registered
                                        </span>
                                        <span v-else 
                                              class="text-xs px-3 py-1 rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                            Full
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <AcademicCapIcon class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No upcoming workshops</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Check back later for new workshops.</p>
                        </div>
                        
                        <div v-if="upcomingWorkshops && upcomingWorkshops.length > 3" class="mt-4 text-center">
                            <a :href="route('visitor.workshops.index')" 
                               class="inline-flex items-center text-sm font-medium" 
                               :style="{ color: themeColor.primary }">
                                View all {{ upcomingWorkshops.length }} workshops
                                <ArrowRightIcon class="w-4 h-4 ml-1" />
                            </a>
                        </div>
                    </div>
                </div>

                <!-- My Workshops -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">My Workshops</h3>
                            <a :href="route('visitor.workshops.my')" class="text-sm" :style="{ color: themeColor.primary }">
                                View All
                            </a>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Workshops you've registered for</p>
                    </div>
                    <div class="p-6">
                        <div v-if="myWorkshops && myWorkshops.length > 0" class="space-y-4">
                            <div v-for="workshop in myWorkshops.slice(0, 4)" :key="workshop.id" 
                                 class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" 
                                         :style="{ backgroundColor: `rgba(${themeColor.rgb}, 0.1)` }">
                                        <AcademicCapIcon class="w-5 h-5" :style="{ color: themeColor.primary }" />
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ workshop.title }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ formatDate(workshop.date) }} at {{ formatTime(workshop.start_time) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                          :class="getWorkshopStatus(workshop).class">
                                        {{ getWorkshopStatus(workshop).text }}
                                    </span>
                                    <a :href="route('visitor.workshops.show', workshop.id)" 
                                       class="text-xs px-2 py-1 rounded border transition-colors"
                                       :style="{ borderColor: themeColor.primary, color: themeColor.primary }"
                                       @mouseenter="$event.target.style.backgroundColor = `rgba(${themeColor.rgb}, 0.1)`"
                                       @mouseleave="$event.target.style.backgroundColor = 'transparent'">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <CalendarIcon class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No registered workshops</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start by exploring available workshops.</p>
                            <a :href="route('visitor.workshops.index')" 
                               class="mt-3 inline-flex items-center px-3 py-2 rounded-lg text-sm text-white transition-colors"
                               :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                                Browse Workshops
                            </a>
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
                        <a :href="route('visitor.workshops.index')" 
                           class="flex items-center p-4 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                            <AcademicCapIcon class="w-6 h-6 mr-3" :style="{ color: themeColor.primary }" />
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Browse All Workshops</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Discover available workshops</p>
                            </div>
                        </a>
                        
                        <a :href="route('visitor.workshops.my')" 
                           class="flex items-center p-4 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                            <CalendarIcon class="w-6 h-6 mr-3" :style="{ color: themeColor.primary }" />
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">My Workshops</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">View your registrations</p>
                            </div>
                        </a>
                        
                        <a :href="route('visitor.profile.index')"
                           class="flex items-center p-4 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                            <UserGroupIcon class="w-6 h-6 mr-3" :style="{ color: themeColor.primary }" />
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Edit Profile</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Update your information</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Hackathon Information -->
            <div v-if="currentEdition" class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 border border-blue-200 dark:border-blue-800">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ currentEdition.name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ currentEdition.description }}</p>
                        <div class="flex items-center mt-2 text-sm text-gray-500 dark:text-gray-400">
                            <CalendarIcon class="w-4 h-4 mr-2" />
                            {{ formatDate(currentEdition.hackathon_start_date) }} - {{ formatDate(currentEdition.hackathon_end_date) }}
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Registration ends:</div>
                        <div class="text-lg font-semibold" :style="{ color: themeColor.primary }">
                            {{ formatDate(currentEdition.registration_end_date) }}
                        </div>
                    </div>
                </div>
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