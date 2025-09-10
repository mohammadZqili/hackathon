<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import PageHeader from '../../../Components/Shared/PageHeader.vue'
import { ref, computed, onMounted } from 'vue'
import { CalendarIcon, ClockIcon, UserGroupIcon, AcademicCapIcon, MapPinIcon, UserIcon, BuildingOfficeIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    workshop: Object,
    isRegistered: Boolean,
    canRegister: Boolean,
    registrationCount: Number,
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

// Registration form
const registerForm = useForm({})
const unregisterForm = useForm({})

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
    return Math.round((props.registrationCount / workshop.capacity) * 100)
}

const handleRegister = () => {
    registerForm.post(route('visitor.workshops.register', props.workshop.id), {
        onSuccess: () => {
            // The page will be reloaded with updated data
        },
        preserveScroll: true,
    })
}

const handleUnregister = () => {
    if (confirm('Are you sure you want to unregister from this workshop?')) {
        unregisterForm.delete(route('visitor.workshops.unregister', props.workshop.id), {
            onSuccess: () => {
                // The page will be reloaded with updated data
            },
            preserveScroll: true,
        })
    }
}

const getStatusMessage = () => {
    if (props.isRegistered) {
        return { 
            message: 'You are registered for this workshop',
            type: 'success'
        }
    } else if (!props.canRegister) {
        const status = getWorkshopStatus(props.workshop)
        if (status.text === 'Completed') {
            return {
                message: 'This workshop has been completed',
                type: 'info'
            }
        } else if (props.registrationCount >= props.workshop.capacity) {
            return {
                message: 'This workshop is fully booked',
                type: 'warning'
            }
        } else if (status.text === 'In Progress') {
            return {
                message: 'This workshop is currently in progress',
                type: 'info'
            }
        }
        return {
            message: 'Registration is not available for this workshop',
            type: 'warning'
        }
    }
    return null
}

const actionButtons = computed(() => {
    const buttons = []
    
    if (props.isRegistered) {
        buttons.push({
            text: 'Unregister',
            onClick: handleUnregister,
            primary: false,
            variant: 'danger'
        })
    } else if (props.canRegister) {
        buttons.push({
            text: 'Register Now',
            onClick: handleRegister,
            primary: true,
            loading: registerForm.processing
        })
    }
    
    return buttons
})

const statusMessage = computed(() => getStatusMessage())
</script>

<template>
    <Head :title="`${workshop?.title} - Workshop Details`" />
    
    <Default>
        <div class="max-w-6xl mx-auto" :style="themeStyles">
            <PageHeader 
                :title="workshop?.title"
                description="Workshop Details"
                :action-buttons="actionButtons"
            />

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Workshop Image Placeholder -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="h-64 bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 flex items-center justify-center">
                            <AcademicCapIcon class="w-20 h-20 text-gray-400" />
                        </div>
                    </div>

                    <!-- Workshop Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ workshop?.title }}</h1>
                                    <div class="flex items-center mt-2 space-x-3">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                              :class="getWorkshopStatus(workshop).class">
                                            {{ getWorkshopStatus(workshop).text }}
                                        </span>
                                        <span v-if="isRegistered" 
                                              class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                            ✓ Registered
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-6">
                                <!-- Description -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">About This Workshop</h3>
                                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                        {{ workshop?.description || 'No description available for this workshop.' }}
                                    </p>
                                </div>

                                <!-- Workshop Details -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Workshop Details</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-4">
                                            <div class="flex items-start space-x-3">
                                                <CalendarIcon class="w-5 h-5 mt-1" :style="{ color: themeColor.primary }" />
                                                <div>
                                                    <p class="font-medium text-gray-900 dark:text-white">Date</p>
                                                    <p class="text-gray-600 dark:text-gray-400">{{ formatDate(workshop?.date) }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-start space-x-3">
                                                <ClockIcon class="w-5 h-5 mt-1" :style="{ color: themeColor.primary }" />
                                                <div>
                                                    <p class="font-medium text-gray-900 dark:text-white">Time</p>
                                                    <p class="text-gray-600 dark:text-gray-400">
                                                        {{ formatTime(workshop?.start_time) }} - {{ formatTime(workshop?.end_time) }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div v-if="workshop?.location" class="flex items-start space-x-3">
                                                <MapPinIcon class="w-5 h-5 mt-1" :style="{ color: themeColor.primary }" />
                                                <div>
                                                    <p class="font-medium text-gray-900 dark:text-white">Location</p>
                                                    <p class="text-gray-600 dark:text-gray-400">{{ workshop?.location }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="space-y-4">
                                            <div class="flex items-start space-x-3">
                                                <UserGroupIcon class="w-5 h-5 mt-1" :style="{ color: themeColor.primary }" />
                                                <div>
                                                    <p class="font-medium text-gray-900 dark:text-white">Capacity</p>
                                                    <p class="text-gray-600 dark:text-gray-400">
                                                        {{ registrationCount || 0 }}/{{ workshop?.capacity }} registered
                                                    </p>
                                                    <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-2">
                                                        <div class="h-2 rounded-full transition-all duration-300" 
                                                             :style="{ 
                                                                 backgroundColor: themeColor.primary, 
                                                                 width: `${getCapacityPercentage(workshop)}%` 
                                                             }">
                                                        </div>
                                                    </div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        {{ getCapacityPercentage(workshop) }}% full
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Prerequisites or Requirements (if available) -->
                                <div v-if="workshop?.prerequisites || workshop?.requirements">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Prerequisites</h3>
                                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                                        <p class="text-blue-800 dark:text-blue-200">
                                            {{ workshop?.prerequisites || workshop?.requirements }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Registration Status -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Registration</h3>
                        </div>
                        <div class="p-6">
                            <!-- Status Message -->
                            <div v-if="statusMessage" class="mb-4 p-3 rounded-lg" 
                                 :class="{
                                     'bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-200': statusMessage.type === 'success',
                                     'bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200': statusMessage.type === 'info',
                                     'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-200': statusMessage.type === 'warning'
                                 }">
                                <p class="text-sm">{{ statusMessage.message }}</p>
                            </div>

                            <!-- Registration Action -->
                            <div class="space-y-3">
                                <button v-if="isRegistered" 
                                        @click="handleUnregister"
                                        :disabled="unregisterForm.processing"
                                        class="w-full px-4 py-2 rounded-lg border-2 border-red-300 text-red-700 hover:bg-red-50 dark:border-red-600 dark:text-red-400 dark:hover:bg-red-900/20 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span v-if="unregisterForm.processing">Unregistering...</span>
                                    <span v-else>Unregister from Workshop</span>
                                </button>
                                
                                <button v-else-if="canRegister" 
                                        @click="handleRegister"
                                        :disabled="registerForm.processing"
                                        class="w-full px-4 py-2 rounded-lg text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                        :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                                    <span v-if="registerForm.processing">Registering...</span>
                                    <span v-else>Register for Workshop</span>
                                </button>
                                
                                <div v-else class="text-center py-4">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Registration is not available</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Speaker Information -->
                    <div v-if="workshop?.speaker" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Speaker</h3>
                        </div>
                        <div class="p-6">
                            <div class="text-center">
                                <div class="w-16 h-16 mx-auto rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center mb-4">
                                    <UserIcon class="w-8 h-8 text-gray-700 dark:text-gray-300" />
                                </div>
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ workshop.speaker.name }}</h4>
                                <p v-if="workshop.speaker.title" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ workshop.speaker.title }}
                                </p>
                                <div v-if="workshop.speaker.organization" class="flex items-center justify-center mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    <BuildingOfficeIcon class="w-4 h-4 mr-1" />
                                    {{ workshop.speaker.organization.name }}
                                </div>
                                <p v-if="workshop.speaker.bio" class="text-sm text-gray-600 dark:text-gray-400 mt-3 text-left">
                                    {{ workshop.speaker.bio }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Workshop Organization -->
                    <div v-if="workshop?.organization" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Organized by</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-lg bg-gray-300 dark:bg-gray-600 flex items-center justify-center mr-3">
                                    <BuildingOfficeIcon class="w-6 h-6 text-gray-700 dark:text-gray-300" />
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ workshop.organization.name }}</h4>
                                    <p v-if="workshop.organization.description" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        {{ workshop.organization.description }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Back to Workshops -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="p-6">
                            <a :href="route('visitor.workshops.index')" 
                               class="w-full flex items-center justify-center px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 transition-colors">
                                ← Back to All Workshops
                            </a>
                        </div>
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