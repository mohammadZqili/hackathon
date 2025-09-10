<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import PageHeader from '../../../Components/Shared/PageHeader.vue'
import { ref, computed, onMounted } from 'vue'
import { CalendarIcon, ClockIcon, UserGroupIcon, QrCodeIcon, MapPinIcon, UserIcon, CheckCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    workshop: Object,
    attendees: Array,
    checkedInAttendees: Array,
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
        weekday: 'long',
        year: 'numeric',
        month: 'long',
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

const getCapacityPercentage = (workshop) => {
    if (!workshop.capacity || workshop.capacity === 0) return 0
    return Math.round((workshop.registrations_count / workshop.capacity) * 100)
}

const getAttendancePercentage = () => {
    if (!props.attendees || props.attendees.length === 0) return 0
    return Math.round((props.checkedInAttendees?.length || 0) / props.attendees.length * 100)
}

const isAttendeeCheckedIn = (attendee) => {
    return props.checkedInAttendees?.some(checkedIn => checkedIn.user_id === attendee.id) || false
}

const actionButtons = computed(() => {
    const status = getWorkshopStatus(props.workshop)
    const buttons = []
    
    if (status.text !== 'Completed') {
        buttons.push({
            text: 'QR Scanner',
            href: route('workshop-supervisor.check-ins.scanner', props.workshop.id),
            icon: 'QrCodeIcon',
            primary: true
        })
    }
    
    buttons.push({
        text: 'Attendance Report',
        href: route('workshop-supervisor.workshops.attendance', props.workshop.id),
        icon: 'UserGroupIcon',
        primary: false
    })
    
    return buttons
})
</script>

<template>
    <Head :title="`${workshop?.title} - Workshop Details`" />
    
    <Default>
        <div class="max-w-7xl mx-auto" :style="themeStyles">
            <PageHeader 
                :title="workshop?.title"
                description="Workshop details and attendance management"
                :action-buttons="actionButtons"
            />

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Workshop Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Workshop Information</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</h4>
                                    <p class="text-gray-900 dark:text-white">{{ workshop?.description || 'No description available.' }}</p>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Date & Time</h4>
                                        <div class="flex items-start space-x-3">
                                            <CalendarIcon class="w-5 h-5 mt-0.5" :style="{ color: themeColor.primary }" />
                                            <div>
                                                <p class="text-gray-900 dark:text-white font-medium">{{ formatDate(workshop?.date) }}</p>
                                                <p class="text-gray-600 dark:text-gray-400 text-sm">
                                                    {{ formatTime(workshop?.start_time) }} - {{ formatTime(workshop?.end_time) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div v-if="workshop?.location">
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Location</h4>
                                        <div class="flex items-start space-x-3">
                                            <MapPinIcon class="w-5 h-5 mt-0.5" :style="{ color: themeColor.primary }" />
                                            <p class="text-gray-900 dark:text-white">{{ workshop?.location }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Capacity</h4>
                                        <div class="flex items-center space-x-3">
                                            <UserGroupIcon class="w-5 h-5" :style="{ color: themeColor.primary }" />
                                            <div>
                                                <p class="text-gray-900 dark:text-white font-semibold">{{ workshop?.registrations_count || 0 }}/{{ workshop?.capacity }}</p>
                                                <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-1">
                                                    <div class="h-2 rounded-full transition-all duration-300" 
                                                         :style="{ 
                                                             backgroundColor: themeColor.primary, 
                                                             width: `${getCapacityPercentage(workshop)}%` 
                                                         }">
                                                    </div>
                                                </div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ getCapacityPercentage(workshop) }}% full</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Status</h4>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                              :class="getWorkshopStatus(workshop).class">
                                            {{ getWorkshopStatus(workshop).text }}
                                        </span>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Attendance</h4>
                                        <div class="flex items-center space-x-3">
                                            <CheckCircleIcon class="w-5 h-5" :style="{ color: themeColor.primary }" />
                                            <div>
                                                <p class="text-gray-900 dark:text-white font-semibold">{{ checkedInAttendees?.length || 0 }}/{{ attendees?.length || 0 }}</p>
                                                <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-1">
                                                    <div class="h-2 rounded-full transition-all duration-300" 
                                                         :style="{ 
                                                             backgroundColor: themeColor.primary, 
                                                             width: `${getAttendancePercentage()}%` 
                                                         }">
                                                    </div>
                                                </div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ getAttendancePercentage() }}% checked in</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendee List -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Registered Attendees</h3>
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ attendees?.length || 0 }} registered</span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div v-if="attendees && attendees.length > 0" class="space-y-3">
                                <div v-for="attendee in attendees" :key="attendee.id" 
                                     class="flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                            <UserIcon class="w-5 h-5 text-gray-700 dark:text-gray-300" />
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ attendee.name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ attendee.email }}</p>
                                            <p v-if="attendee.team" class="text-xs text-gray-500 dark:text-gray-400">{{ attendee.team?.name }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div v-if="isAttendeeCheckedIn(attendee)" class="flex items-center space-x-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                                Checked In
                                            </span>
                                            <CheckCircleIcon class="w-4 h-4 text-green-500" />
                                        </div>
                                        <div v-else>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400">
                                                Not Checked In
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8">
                                <UserGroupIcon class="mx-auto h-12 w-12 text-gray-400" />
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No attendees registered</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Attendees will appear here once they register for the workshop.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Stats -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Stats</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Registered</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ attendees?.length || 0 }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Checked In</span>
                                    <span class="text-sm font-semibold" :style="{ color: themeColor.primary }">{{ checkedInAttendees?.length || 0 }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Not Checked In</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ (attendees?.length || 0) - (checkedInAttendees?.length || 0) }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Attendance Rate</span>
                                    <span class="text-sm font-semibold" :style="{ color: themeColor.primary }">{{ getAttendancePercentage() }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <a v-if="getWorkshopStatus(workshop).text !== 'Completed'" 
                               :href="route('workshop-supervisor.check-ins.scanner', workshop.id)" 
                               class="flex items-center justify-center w-full px-4 py-2 rounded-lg text-white transition-colors"
                               :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                                <QrCodeIcon class="w-5 h-5 mr-2" />
                                Open QR Scanner
                            </a>
                            
                            <a :href="route('workshop-supervisor.workshops.attendance', workshop.id)" 
                               class="flex items-center justify-center w-full px-4 py-2 rounded-lg border transition-colors"
                               :style="{ borderColor: themeColor.primary, color: themeColor.primary }"
                               @mouseenter="$event.target.style.backgroundColor = `rgba(${themeColor.rgb}, 0.1)`"
                               @mouseleave="$event.target.style.backgroundColor = 'transparent'">
                                <UserGroupIcon class="w-5 h-5 mr-2" />
                                View Attendance Report
                            </a>
                            
                            <a :href="route('workshop-supervisor.workshops.index')" 
                               class="flex items-center justify-center w-full px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 transition-colors">
                                Back to My Workshops
                            </a>
                        </div>
                    </div>

                    <!-- Workshop Speaker Info -->
                    <div v-if="workshop?.speaker" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Speaker</h3>
                        </div>
                        <div class="p-6">
                            <div class="text-center">
                                <div class="w-16 h-16 mx-auto rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                    <UserIcon class="w-8 h-8 text-gray-700 dark:text-gray-300" />
                                </div>
                                <div class="mt-4">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ workshop.speaker?.name }}</p>
                                    <p v-if="workshop.speaker?.bio" class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ workshop.speaker.bio }}</p>
                                    <p v-if="workshop.speaker?.organization" class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ workshop.speaker.organization?.name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

<style scoped>
input[type="text"]:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>