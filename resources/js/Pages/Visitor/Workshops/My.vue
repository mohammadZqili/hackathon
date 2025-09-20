<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import PageHeader from '../../../Components/Shared/PageHeader.vue'
import { ref, computed, onMounted, nextTick } from 'vue'
import { CalendarIcon, ClockIcon, UserIcon, MapPinIcon } from '@heroicons/vue/24/outline'
import QRCode from 'qrcode'

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

// QR Code Modal State
const showBarcodeModal = ref(false)
const selectedWorkshop = ref(null)
const qrCanvas = ref(null)

const viewBarcode = async (workshop) => {
    selectedWorkshop.value = workshop
    showBarcodeModal.value = true

    // Generate QR code after modal is shown
    await nextTick()
    if (qrCanvas.value && workshop.barcode) {
        await generateQRCode(workshop)
    }
}

const closeBarcodeModal = () => {
    showBarcodeModal.value = false
    selectedWorkshop.value = null
}

const generateQRCode = async (workshop) => {
    if (!qrCanvas.value) return

    // Create QR content in the same format as backend
    // Format: WORKSHOP_{id}_REG_{registration_id}_CODE_{barcode}
    let qrContent = `WORKSHOP_${workshop.id}`

    if (workshop.registration_id) {
        qrContent += `_REG_${workshop.registration_id}`
    }

    if (workshop.barcode) {
        qrContent += `_CODE_${workshop.barcode}`
    }

    try {
        await QRCode.toCanvas(qrCanvas.value, qrContent, {
            width: 256,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#FFFFFF'
            }
        })
    } catch (error) {
        console.error('Failed to generate QR code:', error)
    }
}

const formatDate = (dateString) => {
    if (!dateString) return ''
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const formatTime = (timeString) => {
    if (!timeString) return ''
    // Handle both full datetime and time-only strings
    const time = timeString.includes('T') ? timeString.split('T')[1] : timeString
    return new Date(`2000-01-01T${time}`).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    })
}

const formatDateTime = (workshop) => {
    if (!workshop.date) return ''

    const dateStr = formatDate(workshop.date)
    const startTime = formatTime(workshop.start_time_formatted)
    const endTime = formatTime(workshop.end_time_formatted)

    return `Date: ${dateStr} | Time: ${startTime} - ${endTime}`
}

const getSpeakerInfo = (workshop) => {
    if (workshop.speakers && workshop.speakers.length > 0) {
        const speaker = workshop.speakers[0]
        return `Speaker: ${speaker.name || speaker.title || 'TBA'}`
    }
    return 'Speaker: TBA'
}

const getSponsorInfo = (workshop) => {
    if (workshop.organizations && workshop.organizations.length > 0) {
        const org = workshop.organizations[0]
        return `Sponsor: ${org.name || 'TBA'}`
    }
    return 'Sponsor: TBA'
}
</script>

<template>
    <Head title="My Registered Workshops" />

    <Default>
        <div class="max-w-7xl mx-auto" :style="themeStyles">
            <PageHeader
                title="My Registered Workshops"
                description="View your registered workshops and access your personal barcode for check-in at each event."
                :show-action="false"
            />

            <!-- Workshop Cards -->
            <div v-if="workshops && workshops.length > 0" class="space-y-4">
                <div v-for="workshop in workshops" :key="workshop.id"
                     class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 flex gap-4">

                    <!-- Workshop Content -->
                    <div class="flex-1 space-y-3">
                        <!-- Meta Information -->
                        <div class="text-sm" :style="{ color: themeColor.primary }">
                            {{ getSpeakerInfo(workshop) }} | {{ getSponsorInfo(workshop) }} | {{ formatDateTime(workshop) }}
                        </div>

                        <!-- Title -->
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ workshop.title }}
                        </h3>

                        <!-- Description -->
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ workshop.description || 'No description available' }}
                        </p>

                        <!-- View Barcode Button -->
                        <button @click="viewBarcode(workshop)"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                                :style="{
                                    backgroundColor: `rgba(${themeColor.rgb}, 0.1)`,
                                    color: themeColor.primary
                                }"
                                @mouseenter="$event.target.style.backgroundColor = `rgba(${themeColor.rgb}, 0.2)`"
                                @mouseleave="$event.target.style.backgroundColor = `rgba(${themeColor.rgb}, 0.1)`">
                            View Barcode
                        </button>
                    </div>

                    <!-- Workshop Image -->
                    <div class="w-64 h-40 rounded-xl overflow-hidden bg-gray-200 dark:bg-gray-700 flex-shrink-0">
                        <img v-if="workshop.thumbnail_url"
                             :src="workshop.thumbnail_url"
                             :alt="workshop.title"
                             class="w-full h-full object-cover">
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                </svg>
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

        <!-- Barcode Modal -->
        <div v-if="showBarcodeModal"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
             @click.self="closeBarcodeModal">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-md w-full">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Workshop Barcode</h3>
                    <button @click="closeBarcodeModal"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div v-if="selectedWorkshop" class="space-y-4">
                    <!-- Workshop Info -->
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">{{ selectedWorkshop.title }}</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ formatDateTime(selectedWorkshop) }}</p>
                        <p v-if="selectedWorkshop.location" class="text-sm text-gray-600 dark:text-gray-400 flex items-center mt-1">
                            <MapPinIcon class="w-4 h-4 mr-1" />
                            {{ selectedWorkshop.location }}
                        </p>
                    </div>

                    <!-- QR Code Display -->
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6 text-center">
                        <div class="bg-white p-4 rounded-lg inline-block">
                            <!-- QR Code Canvas -->
                            <canvas ref="qrCanvas"></canvas>
                        </div>

                        <!-- Barcode Text -->
                        <div class="mt-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Registration Code:</p>
                            <div class="font-mono text-lg font-bold text-gray-900 dark:text-white">
                                {{ selectedWorkshop.barcode || 'NO-CODE' }}
                            </div>
                        </div>

                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-4">
                            Show this QR code at the workshop entrance for check-in
                        </p>
                    </div>

                    <!-- Registration Status -->
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Status:</span>
                        <span class="font-medium"
                              :class="{
                                  'text-green-600 dark:text-green-400': selectedWorkshop.registration_status === 'attended',
                                  'text-blue-600 dark:text-blue-400': selectedWorkshop.registration_status === 'registered',
                                  'text-gray-600 dark:text-gray-400': selectedWorkshop.registration_status === 'cancelled'
                              }">
                            {{ selectedWorkshop.registration_status ?
                               selectedWorkshop.registration_status.charAt(0).toUpperCase() +
                               selectedWorkshop.registration_status.slice(1) :
                               'Registered' }}
                        </span>
                    </div>
                </div>

                <!-- Close Button -->
                <button @click="closeBarcodeModal"
                        class="w-full mt-6 px-4 py-2 rounded-lg text-white transition-colors"
                        :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                    Close
                </button>
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