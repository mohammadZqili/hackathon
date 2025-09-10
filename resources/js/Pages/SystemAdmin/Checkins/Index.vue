<template>
    <Head title="Check-ins Management" />
    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Left Panel - Check-in Actions -->
                <div class="w-full lg:w-1/3">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Check-In</h2>
                        
                        <!-- Scanner Actions -->
                        <div class="space-y-4">
                            <!-- Open Camera Button -->
                            <button
                                @click="openQRScanner"
                                class="w-full rounded-xl h-12 flex items-center justify-center gap-3 text-white font-semibold transition-all duration-300 transform hover:scale-105"
                                :style="{
                                    background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                                }"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Open QR Scanner</span>
                            </button>

                            <!-- Scan Barcode Button -->
                            <button
                                @click="openQRScanner"
                                class="w-full rounded-xl h-12 flex items-center justify-center gap-3 border-2 font-semibold transition-all duration-300 transform hover:scale-105"
                                :style="{
                                    color: themeColor.primary,
                                    borderColor: themeColor.primary,
                                    backgroundColor: themeColor.primary + '10'
                                }"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h2M8 20H6m-2-4v-2m0-2v-2m0-2V6m4-2h2M4 8h16M4 16h16" />
                                </svg>
                                <span>Scan Barcode</span>
                            </button>

                            <!-- Workshop Selection -->
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Select Workshop
                                </label>
                                <select 
                                    v-model="selectedWorkshop"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 transition-colors"
                                    :style="{
                                        '--tw-ring-color': themeColor.primary
                                    }"
                                >
                                    <option value="">Choose a workshop...</option>
                                    <option v-for="workshop in workshops" :key="workshop.id" :value="workshop.id">
                                        {{ workshop.title }}
                                    </option>
                                </select>
                            </div>

                            <!-- Manual Check-in -->
                            <div class="mt-6 pt-6 border-t dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Manual Check-in</h3>
                                <input
                                    v-model="manualCode"
                                    type="text"
                                    placeholder="Enter registration code..."
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white mb-3"
                                    @keyup.enter="checkInManual"
                                />
                                <button
                                    @click="checkInManual"
                                    class="w-full rounded-lg h-10 text-white font-medium transition-all duration-300"
                                    :style="{
                                        background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                                    }"
                                >
                                    Check In
                                </button>
                            </div>
                            <!-- QR Generator Section -->
                            <QRGenerator 
                                v-if="selectedWorkshop"
                                :theme-color="themeColor"
                                :workshop-id="selectedWorkshop"
                                :registrations="workshopRegistrations"
                            />
                        </div>
                    </div>
                </div>

                <!-- Right Panel - Attendance Overview -->
                <div class="flex-1">
                    <!-- Header -->
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Workshop Attendance</h1>
                        <p class="text-gray-600 dark:text-gray-400" :style="{ color: themeColor.primary }">
                            Confirm attendance for the workshop
                        </p>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Attendance Overview</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Registered Card -->
                            <div class="bg-white dark:bg-gray-800 rounded-xl border-2 p-6"
                                 :style="{ borderColor: themeColor.primary + '30' }">
                                <div class="text-gray-600 dark:text-gray-400 font-medium mb-2">Registered</div>
                                <div class="text-3xl font-bold" :style="{ color: themeColor.primary }">
                                    {{ statistics.registered }}
                                </div>
                            </div>

                            <!-- Attendees Card -->
                            <div class="bg-white dark:bg-gray-800 rounded-xl border-2 p-6"
                                 :style="{ borderColor: themeColor.primary + '30' }">
                                <div class="text-gray-600 dark:text-gray-400 font-medium mb-2">Attendees</div>
                                <div class="text-3xl font-bold" :style="{ color: themeColor.primary }">
                                    {{ statistics.attendees }}
                                </div>
                            </div>

                            <!-- Unregistered Card -->
                            <div class="bg-white dark:bg-gray-800 rounded-xl border-2 p-6"
                                 :style="{ borderColor: themeColor.primary + '30' }">
                                <div class="text-gray-600 dark:text-gray-400 font-medium mb-2">Unregistered</div>
                                <div class="text-3xl font-bold" :style="{ color: themeColor.primary }">
                                    {{ statistics.unregistered }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Table -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b dark:border-gray-700" 
                                        :style="{ backgroundColor: themeColor.primary + '10' }">
                                        <th class="text-left py-4 px-6 font-semibold text-gray-900 dark:text-white">
                                            Visitor Name
                                        </th>
                                        <th class="text-left py-4 px-6 font-semibold text-gray-900 dark:text-white">
                                            Workshop
                                        </th>
                                        <th class="text-left py-4 px-6 font-semibold" 
                                            :style="{ color: themeColor.primary }">
                                            Attendance Time/Date
                                        </th>
                                        <th class="text-left py-4 px-6 font-semibold text-gray-900 dark:text-white">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(attendee, index) in recentAttendees" 
                                        :key="attendee.id"
                                        class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="py-4 px-6 text-gray-900 dark:text-white">
                                            {{ attendee.name }}
                                        </td>
                                        <td class="py-4 px-6 text-gray-600 dark:text-gray-400">
                                            {{ attendee.workshop }}
                                        </td>
                                        <td class="py-4 px-6" :style="{ color: themeColor.primary }">
                                            {{ attendee.checkinTime }}
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="px-3 py-1 rounded-full text-xs font-medium text-white"
                                                  :style="{ 
                                                      backgroundColor: attendee.registered ? themeColor.primary : '#9CA3AF'
                                                  }">
                                                {{ attendee.registered ? 'Registered' : 'Walk-in' }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="px-6 py-4 flex items-center justify-between border-t dark:border-gray-700">
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Showing {{ recentAttendees.length }} of {{ totalAttendees }} attendees
                            </div>
                            <div class="flex gap-2">
                                <button 
                                    @click="previousPage"
                                    :disabled="currentPage === 1"
                                    class="px-4 py-2 rounded-lg border dark:border-gray-600 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    Previous
                                </button>
                                <button 
                                    @click="nextPage"
                                    :disabled="currentPage >= totalPages"
                                    class="px-4 py-2 rounded-lg text-white disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                                    :style="{
                                        background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                                    }">
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- QR Scanner Modal -->
            <QRScanner
                :is-open="showQRScanner"
                :workshop-id="selectedWorkshop"
                :theme-color="themeColor"
                @close="closeQRScanner"
                @scan-success="handleScanSuccess"
            />
        </div>
    </Default>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import QRScanner from '../../../Components/QRScanner.vue'
import QRGenerator from '../../../Components/QRGenerator.vue'
import axios from 'axios'

// Props
const props = defineProps({
    workshops: {
        type: Array,
        default: () => []
    },
    recentCheckIns: {
        type: Array,
        default: () => []
    },
    stats: {
        type: Object,
        default: () => ({
            registered: 0,
            attendees: 0,
            unregistered: 0
        })
    }
})

// Theme color management
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

// State
const selectedWorkshop = ref('')
const manualCode = ref('')
const showQRScanner = ref(false)
const currentPage = ref(1)
const itemsPerPage = 10

// Workshop registrations for QR generator
const workshopRegistrations = computed(() => {
    if (!selectedWorkshop.value || !allAttendees.value.length) return []
    
    // Map attendees to registration format for QR generator
    return allAttendees.value.map(attendee => ({
        id: attendee.id,
        user_name: attendee.name,
        workshop_title: attendee.workshop,
        barcode: `REG_${attendee.id}_${Date.now()}` // Generate a barcode if not available
    }))
})

// Mock data - will be replaced with real data from props
const statistics = ref(props.stats || {
    registered: 150,
    attendees: 120,
    unregistered: 30
})

const allAttendees = ref(props.recentCheckIns?.length > 0 ? props.recentCheckIns : [
    { id: 1, name: 'Liam Harper', workshop: 'AI & Machine Learning', checkinTime: '10:00 AM, July 26, 2024', registered: true },
    { id: 2, name: 'Olivia Bennett', workshop: 'AI & Machine Learning', checkinTime: '10:05 AM, July 26, 2024', registered: true },
    { id: 3, name: 'Noah Carter', workshop: 'Web Development', checkinTime: '10:10 AM, July 26, 2024', registered: true },
    { id: 4, name: 'Ava Mitchell', workshop: 'Web Development', checkinTime: '10:15 AM, July 26, 2024', registered: false },
    { id: 5, name: 'Ethan Foster', workshop: 'Mobile Apps', checkinTime: '10:20 AM, July 26, 2024', registered: true },
    { id: 6, name: 'Isabella Hayes', workshop: 'Mobile Apps', checkinTime: '10:25 AM, July 26, 2024', registered: true },
    { id: 7, name: 'Mason Reed', workshop: 'Cloud Computing', checkinTime: '10:30 AM, July 26, 2024', registered: false },
    { id: 8, name: 'Sophia Coleman', workshop: 'Cloud Computing', checkinTime: '10:35 AM, July 26, 2024', registered: true },
    { id: 9, name: 'Logan Price', workshop: 'Data Science', checkinTime: '10:40 AM, July 26, 2024', registered: true },
    { id: 10, name: 'Mia Hughes', workshop: 'Data Science', checkinTime: '10:45 AM, July 26, 2024', registered: true },
])

// Computed
const totalAttendees = computed(() => allAttendees.value.length)
const totalPages = computed(() => Math.ceil(totalAttendees / itemsPerPage))
const recentAttendees = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage
    const end = start + itemsPerPage
    return allAttendees.value.slice(start, end)
})

// Methods
const openQRScanner = () => {
    if (!selectedWorkshop.value) {
        alert('Please select a workshop first')
        return
    }
    showQRScanner.value = true
}

const closeQRScanner = () => {
    showQRScanner.value = false
}

const handleScanSuccess = (data) => {
    // Refresh the attendee list
    router.reload({ only: ['recentCheckIns', 'stats'] })
    
    // Show success message
    if (data.name) {
        alert(`Successfully checked in: ${data.name}`)
    }
}

const checkInManual = () => {
    if (!manualCode.value || !selectedWorkshop.value) {
        alert('Please enter a registration code and select a workshop')
        return
    }

    // Submit manual check-in
    router.post(route('qr.mark-attendance'), {
        code: manualCode.value,
        workshop_id: selectedWorkshop.value
    }, {
        onSuccess: () => {
            manualCode.value = ''
            // Refresh attendee list
        },
        onError: (errors) => {
            console.error('Check-in failed:', errors)
        }
    })
}

const previousPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--
    }
}

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++
    }
}
</script>

<style scoped>
input[type="text"]:focus,
select:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}

input[type="checkbox"]:checked {
    background-color: var(--theme-primary) !important;
    border-color: var(--theme-primary) !important;
}
</style>
