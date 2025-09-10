<template>
    <div class="mt-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Generate Test QR Codes</h3>
        
        <div class="space-y-4">
            <!-- Generate for Registration -->
            <div v-if="registrations.length > 0" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 dark:text-white mb-3">Generate QR for Registration</h4>
                <div class="flex gap-3">
                    <select v-model="selectedRegistration" 
                            class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                        <option value="">Select a registration...</option>
                        <option v-for="reg in registrations" :key="reg.id" :value="reg.id">
                            {{ reg.user_name }} - {{ reg.workshop_title }}
                        </option>
                    </select>
                    <button @click="generateRegistrationQR"
                            :disabled="!selectedRegistration"
                            class="px-4 py-2 rounded-lg text-white font-medium transition-all"
                            :style="{
                                background: selectedRegistration 
                                    ? `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                                    : '#9CA3AF'
                            }">
                        Generate QR
                    </button>
                </div>
            </div>

            <!-- Generate Custom QR -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 dark:text-white mb-3">Generate Custom QR Code</h4>
                <div class="space-y-3">
                    <input v-model="customQRText" 
                           type="text"
                           placeholder="Enter text for QR code..."
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                    <button @click="generateCustomQR"
                            :disabled="!customQRText"
                            class="w-full px-4 py-2 rounded-lg text-white font-medium transition-all"
                            :style="{
                                background: customQRText 
                                    ? `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                                    : '#9CA3AF'
                            }">
                        Generate Custom QR
                    </button>
                </div>
            </div>

            <!-- QR Code Display -->
            <div v-if="generatedQR" class="bg-white dark:bg-gray-800 rounded-lg p-6 text-center">
                <h4 class="font-medium text-gray-900 dark:text-white mb-4">Generated QR Code</h4>
                <div class="inline-block p-4 bg-white rounded-lg">
                    <canvas ref="qrCanvas"></canvas>
                </div>
                <div class="mt-4 space-y-2">
                    <p class="text-sm text-gray-600 dark:text-gray-400">QR Content:</p>
                    <code class="block p-2 bg-gray-100 dark:bg-gray-700 rounded text-xs break-all">
                        {{ generatedQR }}
                    </code>
                    <button @click="downloadQR" 
                            class="mt-3 px-4 py-2 rounded-lg text-white font-medium"
                            :style="{
                                background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                            }">
                        Download QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import QRCode from 'qrcode'

const props = defineProps({
    themeColor: {
        type: Object,
        required: true
    },
    registrations: {
        type: Array,
        default: () => []
    },
    workshopId: {
        type: [String, Number],
        default: null
    }
})

const selectedRegistration = ref('')
const customQRText = ref('')
const generatedQR = ref('')
const qrCanvas = ref(null)

const generateRegistrationQR = async () => {
    if (!selectedRegistration.value) return
    
    const registration = props.registrations.find(r => r.id === selectedRegistration.value)
    if (registration) {
        const qrContent = `WORKSHOP_${props.workshopId}_REG_${registration.id}_CODE_${registration.barcode}`
        await generateQRCode(qrContent)
    }
}

const generateCustomQR = async () => {
    if (!customQRText.value) return
    await generateQRCode(customQRText.value)
}

const generateQRCode = async (text) => {
    generatedQR.value = text
    await nextTick()
    
    if (qrCanvas.value) {
        try {
            await QRCode.toCanvas(qrCanvas.value, text, {
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
}

const downloadQR = () => {
    if (!qrCanvas.value) return
    
    const link = document.createElement('a')
    link.download = 'qrcode.png'
    link.href = qrCanvas.value.toDataURL()
    link.click()
}
</script>
