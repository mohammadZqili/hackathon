<template>
    <Head title="Workshops - Team Lead" />
    <Default>
        <div class="w-full h-full overflow-hidden" :style="themeStyles">
            <!-- Workshops Page based on Figma Design -->
            <div class="w-full overflow-hidden flex flex-col items-start justify-start max-w-[960px] text-sm font-space-grotesk">
                <!-- Page Header -->
                <div class="self-stretch flex flex-row items-start justify-between flex-wrap content-start p-4 text-[32px] text-gray-900 dark:text-white">
                    <div class="flex flex-col items-start justify-start gap-3 min-w-[288px]">
                        <div class="w-[896px] flex flex-col items-start justify-start">
                            <b class="self-stretch relative leading-10">Upcoming Workshops</b>
                        </div>
                        <div class="flex flex-col items-start justify-start text-sm"
                            :style="{ color: themeColor.primary }">
                            <div class="self-stretch relative leading-[21px]">Explore our upcoming workshops designed to enhance your skills and knowledge in various fields. Register now to secure your spot!</div>
                        </div>
                    </div>
                </div>
                
                <!-- Workshops List -->
                <div v-if="workshops?.length" class="self-stretch space-y-4">
                    <div v-for="workshop in workshops" :key="workshop.id" 
                        class="self-stretch flex flex-col items-start justify-start p-4">
                        <div class="self-stretch shadow-[0px_0px_4px_rgba(0,_0,_0,_0.1)] rounded-xl bg-gray-50 dark:bg-gray-800 overflow-hidden flex flex-row items-start justify-between p-4 gap-0">
                            <div class="w-[587px] h-[165px] flex flex-col items-start justify-start gap-4">
                                <div class="self-stretch flex flex-col items-start justify-start gap-1">
                                    <div class="self-stretch flex flex-col items-start justify-start text-gray-600 dark:text-gray-400">
                                        <div class="self-stretch relative leading-[21px]">
                                            Speaker: {{ workshop.speaker || 'TBA' }} | 
                                            Sponsor: {{ workshop.sponsor || 'TBA' }} | 
                                            Date: {{ formatDate(workshop.date) }} | 
                                            Time: {{ workshop.time || 'TBA' }}
                                        </div>
                                    </div>
                                    <div class="self-stretch h-5 flex flex-col items-start justify-start text-base text-gray-900 dark:text-white">
                                        <b class="self-stretch relative leading-5">{{ workshop.title }}</b>
                                    </div>
                                    <div class="self-stretch flex flex-col items-start justify-start text-gray-600 dark:text-gray-400">
                                        <div class="self-stretch relative leading-[21px]">{{ workshop.description }}</div>
                                    </div>
                                </div>
                                <button @click="registerForWorkshop(workshop.id)" 
                                    :disabled="isRegistered(workshop.id) || processing === workshop.id"
                                    class="rounded-xl h-8 overflow-hidden shrink-0 flex flex-row items-center justify-center py-0 px-4 box-border min-w-[84px] max-w-[480px] text-center text-gray-900 dark:text-white transition-colors"
                                    :class="{
                                        'bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500': !isRegistered(workshop.id),
                                        'bg-green-200 dark:bg-green-800 text-green-800 dark:text-green-200 cursor-not-allowed': isRegistered(workshop.id),
                                        'opacity-50 cursor-not-allowed': processing === workshop.id
                                    }">
                                    <div class="overflow-hidden flex flex-col items-center justify-start">
                                        <div class="self-stretch relative leading-[21px] font-medium overflow-hidden text-ellipsis whitespace-nowrap">
                                            {{ processing === workshop.id ? 'Registering...' : (isRegistered(workshop.id) ? 'Registered' : 'Register') }}
                                        </div>
                                    </div>
                                </button>
                            </div>
                            <div class="flex-1 relative rounded-xl max-w-full overflow-hidden h-[165px] bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-800 dark:to-purple-800 flex items-center justify-center">
                                <svg class="w-16 h-16 text-blue-500 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- No Workshops State -->
                <div v-else class="self-stretch flex flex-col items-center justify-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Workshops Available</h3>
                    <p class="text-gray-600 dark:text-gray-400">Check back later for upcoming workshops.</p>
                </div>
                
                <!-- My Registrations Section -->
                <div v-if="registrations?.length" class="self-stretch mt-8">
                    <div class="self-stretch flex flex-col items-start justify-start p-4 text-[22px]">
                        <b class="self-stretch relative leading-7 text-gray-900 dark:text-white">My Registrations</b>
                    </div>
                    <div class="self-stretch space-y-2 px-4">
                        <div v-for="registration in registrations" :key="registration.id"
                            class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg">
                            <div>
                                <h4 class="font-semibold text-green-800 dark:text-green-200">{{ registration.workshop?.title }}</h4>
                                <p class="text-sm text-green-600 dark:text-green-400">
                                    {{ formatDate(registration.workshop?.date) }} at {{ registration.workshop?.time }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 text-xs rounded-full bg-green-200 dark:bg-green-800 text-green-800 dark:text-green-200">
                                    {{ registration.status }}
                                </span>
                                <button @click="cancelRegistration(registration.id)"
                                    class="text-red-600 hover:text-red-800 text-sm">
                                    Cancel
                                </button>
                            </div>
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
    workshops: Array,
    registrations: Array
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

const processing = ref(null)

const formatDate = (dateString) => {
    if (!dateString) return 'TBA'
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    })
}

const isRegistered = (workshopId) => {
    return props.registrations?.some(reg => reg.workshop_id === workshopId)
}

const registerForWorkshop = (workshopId) => {
    if (!isRegistered(workshopId)) {
        processing.value = workshopId
        router.post(route('team-lead.workshops.register', workshopId), {}, {
            onSuccess: () => {
                processing.value = null
            },
            onError: () => {
                processing.value = null
            }
        })
    }
}

const cancelRegistration = (registrationId) => {
    if (confirm('Are you sure you want to cancel this registration?')) {
        router.delete(route('team-lead.workshops.unregister', registrationId))
    }
}
</script>

<style scoped>
/* No additional styles needed - using theme variables */
</style>