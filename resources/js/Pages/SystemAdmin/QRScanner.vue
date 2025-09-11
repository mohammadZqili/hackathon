<script setup>
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL, direction, locale } = useLocalization()
import { Head } from '@inertiajs/vue3'
import Default from '../../Layouts/Default.vue'

import { ref } from 'vue'

const props = defineProps({
    
})


const scannedData = ref(null)

const startScanning = () => {
    // Initialize QR scanner
}

const stopScanning = () => {
    // Stop QR scanner
}
</script>

<template>
    <Head :title="t('admin.qr_scanner.title')" />
    
    <Default>
        <div class="container mx-auto px-4 py-8">
            
            <div class="max-w-2xl mx-auto">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">QR Code Scanner</h1>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="aspect-video bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mb-6">
                        <div class="text-center">
                            <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 011-1h6a1 1 0 011 1v2a1 1 0 001 1h2m-4 4h.01M12 12h.01" />
                            </svg>
                            <p class="text-gray-500">Camera feed will appear here</p>
                        </div>
                    </div>

                    <div v-if="scannedData" class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <h3 class="font-semibold text-green-800 dark:text-green-400 mb-2">Scanned Successfully!</h3>
                        <pre class="text-sm">{{ scannedData }}</pre>
                    </div>

                    <div class="mt-6 flex justify-center space-x-3">
                        <button @click="startScanning"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg">
                            Start Scanning
                        </button>
                        <button @click="stopScanning"
                                class="px-6 py-2 bg-gray-600 text-white rounded-lg">
                            Stop
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>
