<template>
    <Head :title="`Check-in: ${workshop?.title || 'Workshop'}`" />
    <Default>
        <!-- Notification Toast -->
        <Transition name="slide-fade">
            <div v-if="showNotification"
                 class="fixed top-4 right-4 z-50 min-w-[300px] max-w-[500px] p-4 rounded-lg shadow-lg transition-all"
                 :class="{
                     'bg-green-500 text-white': notificationType === 'success',
                     'bg-red-500 text-white': notificationType === 'error',
                     'bg-yellow-500 text-white': notificationType === 'warning',
                     'bg-blue-500 text-white': notificationType === 'info'
                 }">
                <div class="flex items-center">
                    <span class="flex-1">{{ notificationMessage }}</span>
                    <button @click="showNotification = false" class="ml-4 text-white/80 hover:text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </Transition>

        <!-- QR Scanner Modal -->
        <Transition name="modal">
            <div v-if="showQRScanner" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-screen items-center justify-center p-4">
                    <!-- Backdrop -->
                    <div class="fixed inset-0 bg-black bg-opacity-50" @click="closeQRScanner"></div>

                    <!-- Modal Content -->
                    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Scan QR Code
                            </h3>
                            <button @click="closeQRScanner"
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Video Container for Camera -->
                        <div class="relative bg-black rounded-lg overflow-hidden" style="min-height: 400px;">
                            <video ref="qrVideo"
                                   class="w-full h-full"
                                   style="display: block;"
                                   autoplay
                                   playsinline>
                            </video>
                            <canvas ref="qrCanvas" class="hidden"></canvas>

                            <!-- Scanning Overlay -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-64 h-64 border-2 border-white rounded-lg"
                                     style="box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5);">
                                    <div class="w-full h-full relative">
                                        <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-white"></div>
                                        <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-white"></div>
                                        <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-white"></div>
                                        <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-white"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Scanning Animation -->
                            <div v-if="isScanning" class="absolute bottom-4 left-0 right-0 text-center">
                                <div class="inline-flex items-center px-4 py-2 bg-black bg-opacity-75 rounded-lg text-white">
                                    <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Scanning for QR Code...
                                </div>
                            </div>
                        </div>

                        <!-- Manual Input and Upload Options -->
                        <div class="mt-4 space-y-4">
                            <!-- Upload Image Option -->
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    Upload an image with QR code:
                                </p>
                                <div class="flex gap-2">
                                    <input type="file"
                                           ref="qrImageUpload"
                                           @change="handleImageUpload"
                                           accept="image/*"
                                           class="hidden">
                                    <button @click="$refs.qrImageUpload.click()"
                                            class="flex-1 px-4 py-2 rounded-lg border-2 font-medium transition-all hover:shadow-md"
                                            :style="{
                                                borderColor: themeColor.primary,
                                                color: themeColor.primary,
                                                backgroundColor: `${themeColor.primary}10`
                                            }">
                                        <div class="flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <span>Choose Image</span>
                                        </div>
                                    </button>
                                </div>
                                <p v-if="uploadedFileName" class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                    Selected: {{ uploadedFileName }}
                                </p>
                            </div>

                            <!-- Manual Input Option -->
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    Or enter the code manually:
                                </p>
                                <div class="flex gap-2">
                                    <input v-model="manualQRCode"
                                           type="text"
                                           class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                           placeholder="Enter QR code"
                                           @keyup.enter="processManualQRCode">
                                    <button @click="processManualQRCode"
                                            class="px-4 py-2 rounded-lg text-white font-medium"
                                            :style="{ backgroundColor: themeColor.primary }">
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <div class="flex gap-4">
                <!-- Left Sidebar - Check-in Controls -->
                <div class="w-[360px] bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Check-In</h2>

                    <div class="space-y-4">
                        <button
                            @click="openCamera"
                            class="w-full rounded-xl h-10 overflow-hidden flex items-center justify-center px-4 gap-2 text-white font-medium transition-all hover:shadow-md"
                            :style="{
                                background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                            }">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="font-semibold">Open Camera</span>
                        </button>

                        <button
                            @click="scanBarcode"
                            class="w-full rounded-xl h-10 overflow-hidden flex items-center justify-center px-4 gap-2 text-gray-700 dark:text-gray-300 font-medium transition-all hover:shadow-md border-2"
                            :style="{
                                borderColor: themeColor.primary,
                                backgroundColor: `${themeColor.primary}10`
                            }">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 011-1h6a1 1 0 011 1v2a1 1 0 001 1h2m-6 4v6m0 0H9m3 0h3"></path>
                            </svg>
                            <span class="font-semibold">Scan Barcode</span>
                        </button>
                    </div>

                    <!-- Manual Check-in Form -->
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Manual Check-in</h3>
                        <form @submit.prevent="manualCheckin" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Email or Name
                                </label>
                                <input
                                    v-model="manualSearchQuery"
                                    type="text"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    placeholder="Enter email or name"
                                />
                            </div>
                            <button
                                type="submit"
                                class="w-full rounded-xl h-10 text-white font-medium transition-all hover:shadow-md"
                                :style="{
                                    background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                                }">
                                Search & Check-in
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right Content - Workshop Details & Attendance -->
                <div class="flex-1">
                    <!-- Workshop Header -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                            {{ workshop?.title || 'Workshop Attendance' }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400" :style="{ color: themeColor.primary }">
                            Confirm attendance for the workshop
                        </p>
                        <div class="mt-4 flex items-center gap-6 text-sm text-gray-600 dark:text-gray-400">
                            <div v-if="workshop?.speakers" class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>{{ workshop.speakers }}</span>
                            </div>
                            <div v-if="workshop?.date_time" class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>{{ workshop.date_time }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Overview Stats -->
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="bg-white dark:bg-gray-800 rounded-xl border p-6"
                            :style="{ borderColor: `${themeColor.primary}30` }">
                            <div class="text-base font-medium text-gray-700 dark:text-gray-300">Registered</div>
                            <div class="text-2xl font-bold mt-2" :style="{ color: themeColor.primary }">
                                {{ stats?.registered || 0 }}
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl border p-6"
                            :style="{ borderColor: `${themeColor.primary}30` }">
                            <div class="text-base font-medium text-gray-700 dark:text-gray-300">Attendees</div>
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400 mt-2">
                                {{ stats?.attendees || 0 }}
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl border p-6"
                            :style="{ borderColor: `${themeColor.primary}30` }">
                            <div class="text-base font-medium text-gray-700 dark:text-gray-300">Unregistered</div>
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-2">
                                {{ stats?.unregistered || 0 }}
                            </div>
                        </div>
                    </div>

                    <!-- Attendance List -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Attendance List</h2>
                        </div>

                        <!-- Table Header -->
                        <div class="grid grid-cols-2 gap-4 px-6 py-3 bg-gray-50 dark:bg-gray-700/50 text-sm font-medium text-gray-700 dark:text-gray-300">
                            <div>Visitor Name</div>
                            <div>Attendance Time/Date</div>
                        </div>

                        <!-- Table Body -->
                        <div v-if="recentCheckIns && recentCheckIns.length > 0" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <div v-for="checkin in recentCheckIns" :key="checkin.id"
                                class="grid grid-cols-2 gap-4 px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-semibold"
                                        :style="{ backgroundColor: themeColor.primary }">
                                        {{ checkin.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ checkin.name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ checkin.email }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center" :style="{ color: themeColor.primary }">
                                    {{ checkin.checkinTime }}
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="font-medium">No check-ins yet</p>
                            <p class="text-sm mt-1">Start scanning QR codes or use manual check-in</p>
                            <p class="text-xs mt-2 text-gray-400">Workshop ID: {{ workshop?.id }} | {{ recentCheckIns?.length || 0 }} check-ins loaded</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import jsQR from 'jsqr'

// Simple toast notification system
const showNotification = ref(false)
const notificationMessage = ref('')
const notificationType = ref('info')

const toast = {
    success: (message) => {
        notificationMessage.value = message
        notificationType.value = 'success'
        showNotification.value = true
        setTimeout(() => showNotification.value = false, 3000)
    },
    error: (message) => {
        notificationMessage.value = message
        notificationType.value = 'error'
        showNotification.value = true
        setTimeout(() => showNotification.value = false, 3000)
    },
    warning: (message) => {
        notificationMessage.value = message
        notificationType.value = 'warning'
        showNotification.value = true
        setTimeout(() => showNotification.value = false, 3000)
    },
    info: (message) => {
        notificationMessage.value = message
        notificationType.value = 'info'
        showNotification.value = true
        setTimeout(() => showNotification.value = false, 3000)
    }
}

const props = defineProps({
    workshop: Object,
    recentCheckIns: Array,
    stats: Object,
    workshopRegistrations: Array
})

// Theme color configuration
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

const manualSearchQuery = ref('')
const showQRScanner = ref(false)
const isScanning = ref(false)
const qrVideo = ref(null)
const qrCanvas = ref(null)
const manualQRCode = ref('')
const uploadedFileName = ref('')
const qrImageUpload = ref(null)
let mediaStream = null
let scanInterval = null

// Open camera for QR scanning
const openCamera = () => {
    showQRScanner.value = true
    isScanning.value = true
    startQRScanner()
}

// Scan barcode
const scanBarcode = () => {
    showQRScanner.value = true
    isScanning.value = true
    startQRScanner()
}

// Start QR Scanner
const startQRScanner = async () => {
    try {
        // Request camera access
        mediaStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment' }
        })

        if (qrVideo.value) {
            qrVideo.value.srcObject = mediaStream

            // Start scanning for QR codes
            qrVideo.value.addEventListener('loadedmetadata', () => {
                qrVideo.value.play()
                scanForQRCode()
            })
        }
    } catch (error) {
        console.error('Camera access denied:', error)
        toast.error('Camera access denied. Please allow camera access to scan QR codes.')
        closeQRScanner()
    }
}

// Scan for QR Code
const scanForQRCode = () => {
    if (!qrVideo.value || !qrCanvas.value) return

    const canvas = qrCanvas.value
    const video = qrVideo.value

    canvas.width = video.videoWidth
    canvas.height = video.videoHeight

    scanInterval = setInterval(() => {
        if (!video.paused && !video.ended && video.readyState === 4) {
            const context = canvas.getContext('2d')
            context.drawImage(video, 0, 0, canvas.width, canvas.height)

            // Get image data
            const imageData = context.getImageData(0, 0, canvas.width, canvas.height)

            // Try to decode QR code (using jsQR library or browser API)
            const code = scanQRFromImageData(imageData)

            if (code) {
                processQRCode(code)
                clearInterval(scanInterval)
            }
        }
    }, 300) // Scan every 300ms
}

// Scan QR from image data using jsQR library
const scanQRFromImageData = (imageData) => {
    const code = jsQR(imageData.data, imageData.width, imageData.height, {
        inversionAttempts: "dontInvert"
    })

    if (code) {
        return code.data
    }
    return null
}

// Process QR Code
const processQRCode = (code) => {
    console.log('QR Code detected:', code)
    closeQRScanner()

    // Parse QR code data if it's JSON
    let qrData = code
    try {
        const parsed = JSON.parse(code)
        if (parsed && typeof parsed === 'object') {
            qrData = parsed
            console.log('Parsed QR data:', qrData)
        }
    } catch (e) {
        // If not JSON, use the raw code
        console.log('Raw QR code (not JSON):', code)
    }

    // Prepare payload for backend
    const payload = {
        workshop_id: props.workshop?.id,
        code: code // Backend expects 'code' field
    }

    console.log('Sending check-in payload:', payload)

    // Send QR code to backend for check-in using fetch for JSON response
    fetch(route('system-admin.checkins.process-qr'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
    })
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                console.log('Backend success response:', data)
                if (data.success) {
                    toast.success(data.message || 'Check-in successful!')
                    // Reload the page to refresh the check-in list
                    // Use full page reload to ensure data consistency
                    setTimeout(() => {
                        router.reload()
                    }, 500)
                } else {
                    toast.error(data.message || 'Check-in failed')
                }
            } else {
                console.log('Backend error response:', data)
                // This handles the "Already checked in" case and other errors
                toast.warning(data.message || 'Check-in failed')

                // Still refresh the page to show current attendance data
                // This ensures already checked-in users appear in the list
                router.reload({ only: ['recentCheckIns', 'stats'] })
            }
        })
    })
    .catch(error => {
        console.log('Network error:', error)
        toast.error('Network error occurred')
    })
}

// Process manual QR code input
const processManualQRCode = () => {
    if (!manualQRCode.value) {
        toast.warning('Please enter a QR code')
        return
    }

    processQRCode(manualQRCode.value)
    manualQRCode.value = ''
}

// Handle image upload for QR code scanning
const handleImageUpload = (event) => {
    const file = event.target.files[0]
    if (!file) return

    uploadedFileName.value = file.name

    // Create a canvas to process the image
    const canvas = document.createElement('canvas')
    const ctx = canvas.getContext('2d')
    const img = new Image()

    img.onload = () => {
        canvas.width = img.width
        canvas.height = img.height
        ctx.drawImage(img, 0, 0)

        // Get image data and scan for QR code
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height)
        const code = scanQRFromImageData(imageData)

        if (code) {
            processQRCode(code)
            uploadedFileName.value = ''
            // Reset file input
            if (qrImageUpload.value) {
                qrImageUpload.value.value = ''
            }
        } else {
            toast.error('No QR code found in the uploaded image')
            uploadedFileName.value = ''
            // Reset file input
            if (qrImageUpload.value) {
                qrImageUpload.value.value = ''
            }
        }
    }

    img.onerror = () => {
        toast.error('Failed to load the image')
        uploadedFileName.value = ''
        // Reset file input
        if (qrImageUpload.value) {
            qrImageUpload.value.value = ''
        }
    }

    // Read the file as data URL
    const reader = new FileReader()
    reader.onload = (e) => {
        img.src = e.target.result
    }
    reader.readAsDataURL(file)
}

// Close QR Scanner
const closeQRScanner = () => {
    showQRScanner.value = false
    isScanning.value = false

    // Stop camera stream
    if (mediaStream) {
        mediaStream.getTracks().forEach(track => track.stop())
        mediaStream = null
    }

    // Clear scan interval
    if (scanInterval) {
        clearInterval(scanInterval)
        scanInterval = null
    }

    // Reset video
    if (qrVideo.value) {
        qrVideo.value.srcObject = null
    }

    // Reset upload state
    uploadedFileName.value = ''
    manualQRCode.value = ''
    if (qrImageUpload.value) {
        qrImageUpload.value.value = ''
    }
}

// Manual check-in
const manualCheckin = () => {
    if (!manualSearchQuery.value) {
        toast.warning('Please enter an email or name to search')
        return
    }

    router.post(route('system-admin.checkins.mark-attendance'), {
        workshop_id: props.workshop?.id,
        search_query: manualSearchQuery.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Check-in successful!')
            manualSearchQuery.value = ''
            router.reload({ only: ['recentCheckIns', 'stats'] })
        },
        onError: (errors) => {
            toast.error(errors.message || 'Failed to check-in')
        }
    })
}

// Cleanup on component unmount
onBeforeUnmount(() => {
    closeQRScanner()
})
</script>

<style scoped>
input[type="text"]:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}

/* Toast notification transitions */
.slide-fade-enter-active {
    transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
    transition: all 0.2s ease-in;
}

.slide-fade-enter-from {
    transform: translateX(20px);
    opacity: 0;
}

.slide-fade-leave-to {
    transform: translateX(20px);
    opacity: 0;
}

/* Modal transitions */
.modal-enter-active, .modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from, .modal-leave-to {
    opacity: 0;
}

.modal-enter-active .relative,
.modal-leave-active .relative {
    transition: transform 0.3s ease;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
    transform: scale(0.95);
}
</style>