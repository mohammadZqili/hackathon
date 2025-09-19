<template>
    <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-4xl w-full p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">QR Code Scanner</h3>
                <button @click="close" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Tab Navigation -->
            <div class="flex space-x-4 mb-6 border-b dark:border-gray-700">
                <button 
                    @click="activeTab = 'camera'"
                    :class="[
                        'px-4 py-2 font-medium transition-colors',
                        activeTab === 'camera' 
                            ? 'border-b-2 text-white' 
                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                    ]"
                    :style="activeTab === 'camera' ? { 
                        borderColor: themeColor.primary,
                        color: themeColor.primary 
                    } : {}">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Camera Scan
                    </div>
                </button>
                <button 
                    @click="activeTab = 'file'"
                    :class="[
                        'px-4 py-2 font-medium transition-colors',
                        activeTab === 'file' 
                            ? 'border-b-2 text-white' 
                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                    ]"
                    :style="activeTab === 'file' ? { 
                        borderColor: themeColor.primary,
                        color: themeColor.primary 
                    } : {}">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Upload File
                    </div>
                </button>
                <button 
                    @click="activeTab = 'manual'"
                    :class="[
                        'px-4 py-2 font-medium transition-colors',
                        activeTab === 'manual' 
                            ? 'border-b-2 text-white' 
                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                    ]"
                    :style="activeTab === 'manual' ? { 
                        borderColor: themeColor.primary,
                        color: themeColor.primary 
                    } : {}">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Manual Entry
                    </div>
                </button>
            </div>

            <!-- Tab Content -->
            <div class="min-h-[400px]">
                <!-- Camera Tab -->
                <div v-if="activeTab === 'camera'" class="space-y-4">
                    <div class="relative">
                        <!-- Video element for camera feed -->
                        <div id="qr-reader" class="w-full rounded-lg overflow-hidden bg-gray-900"></div>
                        
                        <!-- Scanning overlay -->
                        <div v-if="isScanning" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="w-64 h-64 border-2 rounded-lg" 
                                 :style="{ borderColor: themeColor.primary }">
                                <div class="w-full h-1 animate-scan"
                                     :style="{ backgroundColor: themeColor.primary }"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Camera Controls -->
                    <div class="flex justify-center gap-4">
                        <button v-if="!isScanning"
                                @click="startCameraScanning"
                                class="px-6 py-3 rounded-lg text-white font-semibold transition-all duration-300 transform hover:scale-105"
                                :style="{
                                    background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                                }">
                            Start Scanning
                        </button>
                        <button v-else
                                @click="stopCameraScanning"
                                class="px-6 py-3 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700 transition-colors">
                            Stop Scanning
                        </button>
                        
                        <!-- Camera Selection -->
                        <select v-if="cameras.length > 1" 
                                v-model="selectedCameraId"
                                @change="switchCamera"
                                class="px-4 py-2 rounded-lg border dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option v-for="camera in cameras" :key="camera.id" :value="camera.id">
                                {{ camera.label || `Camera ${camera.id}` }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- File Upload Tab -->
                <div v-if="activeTab === 'file'" class="space-y-4">
                    <div class="border-2 border-dashed rounded-lg p-8 text-center transition-colors"
                         :style="{ borderColor: isDragging ? themeColor.primary : '#E5E7EB' }"
                         @drop="handleDrop"
                         @dragover.prevent="isDragging = true"
                         @dragleave="isDragging = false">
                        
                        <input type="file" 
                               ref="fileInput" 
                               @change="handleFileSelect"
                               accept="image/*"
                               class="hidden">
                        
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        
                        <p class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                            Drop your QR code image here
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            or
                        </p>
                        <button @click="fileInput?.click()"
                                class="px-6 py-2 rounded-lg text-white font-medium transition-all duration-300"
                                :style="{
                                    background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                                }">
                            Browse Files
                        </button>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">
                            Supported formats: JPG, PNG, GIF, BMP
                        </p>
                    </div>

                    <!-- Image Preview -->
                    <div v-if="selectedFile" class="mt-4">
                        <img :src="filePreview" alt="QR Code" class="max-w-full h-auto rounded-lg mx-auto">
                        <div class="mt-4 flex justify-center gap-4">
                            <button @click="scanFile"
                                    class="px-6 py-2 rounded-lg text-white font-medium transition-all duration-300"
                                    :style="{
                                        background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                                    }">
                                Scan Image
                            </button>
                            <button @click="clearFile"
                                    class="px-6 py-2 rounded-lg border dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Manual Entry Tab -->
                <div v-if="activeTab === 'manual'" class="space-y-4">
                    <div class="max-w-md mx-auto">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Enter QR Code or Barcode
                        </label>
                        <input
                            v-model="manualCode"
                            type="text"
                            placeholder="Enter code here..."
                            class="w-full px-4 py-3 rounded-lg border dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 transition-colors"
                            :style="{
                                '--tw-ring-color': themeColor.primary
                            }"
                            @keyup.enter="submitManualCode">
                        
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Enter the registration code or QR code content manually
                        </p>
                        
                        <button @click="submitManualCode"
                                :disabled="!manualCode"
                                class="w-full mt-4 px-6 py-3 rounded-lg text-white font-semibold transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                                :style="{
                                    background: manualCode 
                                        ? `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                                        : '#9CA3AF'
                                }">
                            Submit Code
                        </button>
                    </div>
                </div>
            </div>

            <!-- Result Display -->
            <div v-if="scanResult" class="mt-6 p-4 rounded-lg" 
                 :class="scanResult.success ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20'">
                <div class="flex items-start gap-3">
                    <svg v-if="scanResult.success" class="w-6 h-6 text-green-600 dark:text-green-400 mt-0.5" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg v-else class="w-6 h-6 text-red-600 dark:text-red-400 mt-0.5" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-1">
                        <h4 class="font-semibold" 
                            :class="scanResult.success ? 'text-green-800 dark:text-green-400' : 'text-red-800 dark:text-red-400'">
                            {{ scanResult.title }}
                        </h4>
                        <p class="text-sm mt-1" 
                           :class="scanResult.success ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300'">
                            {{ scanResult.message }}
                        </p>
                        <div v-if="scanResult.data" class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            <div v-if="scanResult.data.name">Name: {{ scanResult.data.name }}</div>
                            <div v-if="scanResult.data.workshop">Workshop: {{ scanResult.data.workshop }}</div>
                            <div v-if="scanResult.data.code">Code: {{ scanResult.data.code }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isProcessing" class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 rounded-2xl flex items-center justify-center">
                <div class="text-center">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-gray-200"
                         :style="{ borderTopColor: themeColor.primary }"></div>
                    <p class="mt-4 text-gray-600 dark:text-gray-400">Processing QR Code...</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { Html5Qrcode, Html5QrcodeScannerState } from 'html5-qrcode'
import QrScanner from 'qr-scanner'
import axios from 'axios'

// Configure axios to use CSRF token
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
const token = document.head.querySelector('meta[name="csrf-token"]')
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
}

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false
    },
    workshopId: {
        type: [String, Number],
        default: null
    },
    themeColor: {
        type: Object,
        default: () => ({
            primary: '#0d9488',
            hover: '#0f766e',
            rgb: '13, 148, 136',
            gradientFrom: '#0d9488',
            gradientTo: '#14b8a6'
        })
    },
    processRoute: {
        type: String,
        default: null
    }
})

const emit = defineEmits(['close', 'scan-success'])

// State
const activeTab = ref('camera')
const isScanning = ref(false)
const isProcessing = ref(false)
const scanResult = ref(null)
const manualCode = ref('')
const selectedFile = ref(null)
const filePreview = ref(null)
const isDragging = ref(false)
const cameras = ref([])
const selectedCameraId = ref(null)
const fileInput = ref(null)

let html5QrCode = null

// Watch for modal open/close
watch(() => props.isOpen, (newVal) => {
    if (!newVal) {
        // Modal is closing, cleanup
        if (isScanning.value) {
            stopCameraScanning()
        }
        // Reset to camera tab for next open
        setTimeout(() => {
            activeTab.value = 'camera'
        }, 300)
    }
})

// Camera Scanning Functions
const startCameraScanning = async () => {
    try {
        html5QrCode = new Html5Qrcode("qr-reader")
        
        // Get available cameras
        const devices = await Html5Qrcode.getCameras()
        if (devices && devices.length) {
            cameras.value = devices
            selectedCameraId.value = devices[devices.length - 1].id // Use back camera by default
        }

        const config = {
            fps: 10,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0
        }

        await html5QrCode.start(
            selectedCameraId.value || { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanError
        )

        isScanning.value = true
        scanResult.value = null
    } catch (err) {
        console.error("Failed to start scanner:", err)
        scanResult.value = {
            success: false,
            title: 'Camera Error',
            message: 'Failed to access camera. Please check permissions.'
        }
    }
}

const stopCameraScanning = async () => {
    if (html5QrCode && isScanning.value) {
        try {
            const state = html5QrCode.getState()
            // Only stop if it's actually scanning
            if (state === Html5QrcodeScannerState.SCANNING) {
                await html5QrCode.stop()
            }
            html5QrCode.clear()
        } catch (err) {
            console.warn("Scanner cleanup:", err)
        } finally {
            html5QrCode = null
            isScanning.value = false
        }
    }
}

const switchCamera = async () => {
    if (isScanning.value) {
        await stopCameraScanning()
        await startCameraScanning()
    }
}

const onScanSuccess = (decodedText, decodedResult) => {
    console.log(`QR Code detected: ${decodedText}`)
    processQRCode(decodedText)
    stopCameraScanning()
}

const onScanError = (errorMessage) => {
    // Ignore error messages while scanning
}

// File Upload Functions
const handleFileSelect = (event) => {
    const file = event.target.files[0]
    if (file) {
        processFile(file)
    }
}

const handleDrop = (event) => {
    event.preventDefault()
    isDragging.value = false
    
    const file = event.dataTransfer.files[0]
    if (file && file.type.startsWith('image/')) {
        processFile(file)
    }
}

const processFile = (file) => {
    selectedFile.value = file
    
    const reader = new FileReader()
    reader.onload = (e) => {
        filePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
}

const scanFile = async () => {
    if (!selectedFile.value) return
    
    isProcessing.value = true
    scanResult.value = null
    
    try {
        const result = await QrScanner.scanImage(selectedFile.value)
        processQRCode(result)
    } catch (error) {
        console.error('Failed to scan image:', error)
        scanResult.value = {
            success: false,
            title: 'Scan Failed',
            message: 'No QR code found in the image. Please try another image.'
        }
    } finally {
        isProcessing.value = false
    }
}

const clearFile = () => {
    selectedFile.value = null
    filePreview.value = null
    scanResult.value = null
    // Clear the file input if it exists
    if (fileInput.value && fileInput.value.value) {
        fileInput.value.value = ''
    }
}

// Manual Entry
const submitManualCode = () => {
    if (!manualCode.value) return
    processQRCode(manualCode.value)
}

// Process QR Code
const processQRCode = async (code) => {
    isProcessing.value = true

    try {
        // Determine the correct route based on props or current page context
        let processUrl = props.processRoute

        if (!processUrl) {
            // Fallback to determine route based on current URL
            const currentPath = window.location.pathname
            if (currentPath.includes('system-admin')) {
                processUrl = '/system-admin/checkins/process-qr'
            } else if (currentPath.includes('hackathon-admin')) {
                processUrl = '/hackathon-admin/checkins/process-qr'
            } else if (currentPath.includes('track-supervisor')) {
                processUrl = '/track-supervisor/checkins/process-qr'
            } else {
                // Default fallback
                processUrl = '/system-admin/checkins/process-qr'
            }
        }

        // Send to backend for processing
        const response = await axios.post(processUrl, {
            code: code,
            workshop_id: props.workshopId
        })
        
        if (response.data.success) {
            scanResult.value = {
                success: true,
                title: 'Check-in Successful!',
                message: response.data.message,
                data: response.data.data
            }
            
            emit('scan-success', response.data.data)
            
            // Clear form after successful scan
            setTimeout(() => {
                manualCode.value = ''
                clearFile()
                scanResult.value = null
            }, 3000)
        } else {
            scanResult.value = {
                success: false,
                title: 'Check-in Failed',
                message: response.data.message || 'Invalid QR code or registration not found.'
            }
        }
    } catch (error) {
        console.error('Failed to process QR code:', error)
        scanResult.value = {
            success: false,
            title: 'Error',
            message: error.response?.data?.message || 'Failed to process QR code. Please try again.'
        }
    } finally {
        isProcessing.value = false
    }
}

// Close modal
const close = () => {
    // Stop scanning if active
    if (isScanning.value) {
        stopCameraScanning()
    }
    // Clear all states
    scanResult.value = null
    manualCode.value = ''
    selectedFile.value = null
    filePreview.value = null
    activeTab.value = 'camera'
    emit('close')
}

// Cleanup on unmount
onUnmounted(() => {
    if (html5QrCode) {
        try {
            const state = html5QrCode.getState()
            if (state === Html5QrcodeScannerState.SCANNING) {
                html5QrCode.stop().catch(() => {})
            }
        } catch (err) {
            // Ignore errors during cleanup
        }
    }
})
</script>

<style scoped>
@keyframes scan {
    0% {
        transform: translateY(0);
    }
    100% {
        transform: translateY(250px);
    }
}

.animate-scan {
    animation: scan 2s linear infinite;
}

/* QR Scanner Styles */
:deep(#qr-reader) {
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
}

:deep(#qr-reader video) {
    width: 100% !important;
    height: auto !important;
    border-radius: 0.5rem;
}

:deep(#qr-reader__scan_region) {
    min-height: 300px;
}

input[type="text"]:focus,
input[type="file"]:focus {
    outline: none;
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>
