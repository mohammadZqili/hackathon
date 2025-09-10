<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import PageHeader from '../../../Components/Shared/PageHeader.vue'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { QrCodeIcon, CheckCircleIcon, XCircleIcon, UserIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    workshop: Object,
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

// QR Scanner state
const isScanning = ref(false)
const scannerResult = ref(null)
const scannerError = ref(null)
const videoElement = ref(null)
const stream = ref(null)
const manualCodeInput = ref('')
const showManualInput = ref(false)

// Check-in form
const checkInForm = useForm({
    qr_code: '',
    workshop_id: props.workshop?.id
})

const startScanner = async () => {
    try {
        scannerError.value = null
        
        // Request camera permission
        stream.value = await navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: 'environment' // Use back camera if available
            } 
        })
        
        if (videoElement.value) {
            videoElement.value.srcObject = stream.value
            isScanning.value = true
        }
    } catch (error) {
        scannerError.value = 'Unable to access camera. Please check permissions.'
        console.error('Camera access error:', error)
    }
}

const stopScanner = () => {
    if (stream.value) {
        stream.value.getTracks().forEach(track => track.stop())
        stream.value = null
    }
    isScanning.value = false
}

const handleManualSubmit = () => {
    if (manualCodeInput.value.trim()) {
        processCheckIn(manualCodeInput.value.trim())
        manualCodeInput.value = ''
    }
}

const processCheckIn = (qrCode) => {
    checkInForm.qr_code = qrCode
    
    checkInForm.post(route('workshop-supervisor.check-ins.store', props.workshop.id), {
        onSuccess: (page) => {
            scannerResult.value = {
                type: 'success',
                message: 'Check-in successful!',
                user: page.props.checkedInUser
            }
            
            // Clear result after 3 seconds
            setTimeout(() => {
                scannerResult.value = null
            }, 3000)
        },
        onError: (errors) => {
            scannerResult.value = {
                type: 'error',
                message: errors.qr_code || errors.message || 'Check-in failed. Please try again.',
                user: null
            }
            
            // Clear result after 5 seconds
            setTimeout(() => {
                scannerResult.value = null
            }, 5000)
        },
        preserveState: true,
        preserveScroll: true,
    })
}

// Simulate QR code detection (in real implementation, you'd use a QR code library like jsqr)
const simulateQRDetection = () => {
    // This is a placeholder - in real implementation, you'd analyze the video stream
    // and extract QR codes using a library like jsqr
    console.log('Scanning for QR codes...')
}

const formatTime = (timestamp) => {
    return new Date(timestamp).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    })
}

onMounted(() => {
    // Auto-start scanner on component mount
    startScanner()
})

onUnmounted(() => {
    stopScanner()
})
</script>

<template>
    <Head :title="`QR Scanner - ${workshop?.title}`" />
    
    <Default>
        <div class="max-w-6xl mx-auto" :style="themeStyles">
            <PageHeader 
                :title="`QR Scanner - ${workshop?.title}`"
                :description="`Scan QR codes to check attendees into ${workshop?.title}`"
                :show-action="false"
            />

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Scanner Section -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">QR Code Scanner</h3>
                                <div class="flex items-center space-x-3">
                                    <button
                                        @click="showManualInput = !showManualInput"
                                        class="text-sm px-3 py-1 rounded-lg border transition-colors"
                                        :style="{ borderColor: themeColor.primary, color: themeColor.primary }">
                                        Manual Entry
                                    </button>
                                    <button
                                        v-if="!isScanning"
                                        @click="startScanner"
                                        class="text-sm px-3 py-1 rounded-lg text-white transition-colors"
                                        :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                                        Start Scanner
                                    </button>
                                    <button
                                        v-else
                                        @click="stopScanner"
                                        class="text-sm px-3 py-1 rounded-lg bg-red-600 hover:bg-red-700 text-white transition-colors">
                                        Stop Scanner
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <!-- Scanner Interface -->
                            <div class="relative">
                                <div v-if="isScanning" class="relative">
                                    <video
                                        ref="videoElement"
                                        autoplay
                                        playsinline
                                        class="w-full h-80 bg-black rounded-lg object-cover">
                                    </video>
                                    
                                    <!-- Scanner Overlay -->
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="w-48 h-48 border-4 border-white rounded-lg shadow-lg relative">
                                            <div class="absolute inset-0 border-2 border-dashed" :style="{ borderColor: themeColor.primary }"></div>
                                            <div class="absolute -top-2 -left-2 w-6 h-6 border-l-4 border-t-4 border-white"></div>
                                            <div class="absolute -top-2 -right-2 w-6 h-6 border-r-4 border-t-4 border-white"></div>
                                            <div class="absolute -bottom-2 -left-2 w-6 h-6 border-l-4 border-b-4 border-white"></div>
                                            <div class="absolute -bottom-2 -right-2 w-6 h-6 border-r-4 border-b-4 border-white"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Scanner Instructions -->
                                    <div class="absolute bottom-4 left-0 right-0 text-center">
                                        <p class="text-white bg-black bg-opacity-50 px-4 py-2 rounded-lg inline-block">
                                            Position QR code within the frame
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Scanner Placeholder -->
                                <div v-else class="flex items-center justify-center h-80 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                    <div class="text-center">
                                        <QrCodeIcon class="mx-auto h-16 w-16 text-gray-400" />
                                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">QR Scanner Ready</h3>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            Click "Start Scanner" to begin scanning QR codes
                                        </p>
                                        <button
                                            @click="startScanner"
                                            class="mt-4 px-4 py-2 rounded-lg text-white transition-colors"
                                            :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                                            <QrCodeIcon class="w-5 h-5 inline mr-2" />
                                            Start Scanner
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Scanner Error -->
                                <div v-if="scannerError" class="mt-4 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                                    <div class="flex">
                                        <XCircleIcon class="h-5 w-5 text-red-400" />
                                        <div class="ml-3">
                                            <p class="text-sm text-red-800 dark:text-red-200">{{ scannerError }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Manual Input -->
                            <div v-show="showManualInput" class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Manual QR Code Entry</h4>
                                <div class="flex space-x-3">
                                    <input
                                        v-model="manualCodeInput"
                                        type="text"
                                        placeholder="Enter QR code manually..."
                                        class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm"
                                        @keyup.enter="handleManualSubmit">
                                    <button
                                        @click="handleManualSubmit"
                                        :disabled="!manualCodeInput.trim()"
                                        class="px-4 py-2 rounded-lg text-white text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                        :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                                        Submit
                                    </button>
                                </div>
                            </div>

                            <!-- Scanner Result -->
                            <div v-if="scannerResult" class="mt-6">
                                <div v-if="scannerResult.type === 'success'" class="p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                                    <div class="flex">
                                        <CheckCircleIcon class="h-5 w-5 text-green-400" />
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ scannerResult.message }}</p>
                                            <p v-if="scannerResult.user" class="text-sm text-green-600 dark:text-green-300 mt-1">
                                                Attendee: {{ scannerResult.user.name }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div v-else class="p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                                    <div class="flex">
                                        <XCircleIcon class="h-5 w-5 text-red-400" />
                                        <div class="ml-3">
                                            <p class="text-sm text-red-800 dark:text-red-200">{{ scannerResult.message }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Workshop Info & Recent Check-ins -->
                <div class="space-y-6">
                    <!-- Workshop Info -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Workshop Details</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Title</p>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ workshop?.title }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date & Time</p>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ new Date(workshop?.date).toLocaleDateString() }} 
                                        at {{ workshop?.start_time }} - {{ workshop?.end_time }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Capacity</p>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ workshop?.registrations_count || 0 }}/{{ workshop?.capacity }} registered
                                    </p>
                                    <div class="mt-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="h-2 rounded-full transition-all duration-300" 
                                             :style="{ 
                                                 backgroundColor: themeColor.primary, 
                                                 width: `${Math.min((workshop?.registrations_count || 0) / (workshop?.capacity || 1) * 100, 100)}%` 
                                             }"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Check-ins -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Check-ins</h3>
                        </div>
                        <div class="p-6">
                            <div v-if="recentCheckIns && recentCheckIns.length > 0" class="space-y-3">
                                <div v-for="checkIn in recentCheckIns" :key="checkIn.id" 
                                     class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                            <UserIcon class="w-4 h-4 text-gray-700 dark:text-gray-300" />
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ checkIn.user?.name }}</p>
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
                            <div v-else class="text-center py-6">
                                <UserIcon class="mx-auto h-10 w-10 text-gray-400" />
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No check-ins yet</p>
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