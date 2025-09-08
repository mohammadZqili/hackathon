<template>
    <Head title="QR Scanner" />
    <Default>
        <div class="max-w-4xl mx-auto">




















































































































































































































































































































            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">QR Code Scanner</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Scan workshop attendance QR codes to mark participant attendance.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Scanner Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Scanner</h2>

                    <div class="mb-4">
                        <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            <div class="text-center">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400">QR Scanner will appear here</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <button @click="startScanner"
                                :disabled="isScanning"
                                class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-blue-300 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                            {{ isScanning ? 'Scanning...' : 'Start Scanner' }}
                        </button>

                        <button @click="stopScanner"
                                :disabled="!isScanning"
                                class="w-full bg-gray-600 hover:bg-gray-700 disabled:bg-gray-300 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                            Stop Scanner
                        </button>
                    </div>

                    <!-- Manual Input -->
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Manual Input</h3>
                        <div class="flex space-x-2">
                            <input v-model="manualCode"
                                   type="text"
                                   placeholder="Enter QR code manually"
                                   class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <button @click="processCode(manualCode)"
                                    :disabled="!manualCode"
                                    class="bg-green-600 hover:bg-green-700 disabled:bg-green-300 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                Process
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Results Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Scan Results</h2>

                    <div v-if="scanResults.length === 0" class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No scans yet</p>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="(result, index) in scanResults"
                             :key="index"
                             class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ result.participant_name || 'Unknown Participant' }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Workshop: {{ result.workshop_name || 'Unknown Workshop' }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500">
                                        {{ result.timestamp }}
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span :class="[
                                        'px-2 py-1 rounded-full text-xs font-medium',
                                        result.status === 'success'
                                            ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
                                            : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'
                                    ]">
                                        {{ result.status === 'success' ? 'Attended' : 'Error' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div v-if="scanResults.length > 0" class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Session Statistics</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ scanResults.filter(r => r.status === 'success').length }}
                                </div>
                                <div class="text-sm text-blue-800 dark:text-blue-300">Successful</div>
                            </div>
                            <div class="text-center p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                                    {{ scanResults.filter(r => r.status === 'error').length }}
                                </div>
                                <div class="text-sm text-red-800 dark:text-red-300">Errors</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'
import Default from '../../Layouts/Default.vue'

const isScanning = ref(false)
const manualCode = ref('')
const scanResults = ref([])

const startScanner = () => {
    isScanning.value = true
    // TODO: Implement actual QR scanner initialization
    console.log('Starting QR scanner...')
}

const stopScanner = () => {
    isScanning.value = false
    // TODO: Implement actual QR scanner cleanup
    console.log('Stopping QR scanner...')
}

const processCode = async (code) => {
    if (!code) return

    try {
        // TODO: Replace with actual API call
        const response = await fetch('/qr/scan-workshop', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ qr_code: code })
        })

        const result = await response.json()

        // Add result to scan results
        scanResults.value.unshift({
            participant_name: result.participant_name || 'Test Participant',
            workshop_name: result.workshop_name || 'Test Workshop',
            status: response.ok ? 'success' : 'error',
            timestamp: new Date().toLocaleString(),
            code: code
        })

        // Clear manual input
        manualCode.value = ''

    } catch (error) {
        console.error('Error processing QR code:', error)

        // Add error result
        scanResults.value.unshift({
            participant_name: 'Unknown',
            workshop_name: 'Unknown',
            status: 'error',
            timestamp: new Date().toLocaleString(),
            code: code
        })
    }
}
</script>
