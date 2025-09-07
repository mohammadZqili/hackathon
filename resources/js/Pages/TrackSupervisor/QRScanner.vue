<script setup>
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'
import Default from '../../Layouts/Default.vue'
import { QrCodeIcon, CameraIcon } from '@heroicons/vue/24/outline'

const scanning = ref(false)
const result = ref(null)
</script>

<template>
    <Head title="QR Scanner" />
    
    <Default>
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Workshop QR Scanner</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Scan QR codes to mark workshop attendance</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="text-center">
                    <QrCodeIcon class="w-24 h-24 mx-auto text-gray-400 mb-4" />
                    <button @click="scanning = !scanning" 
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg inline-flex items-center">
                        <CameraIcon class="w-5 h-5 mr-2" />
                        {{ scanning ? 'Stop Scanning' : 'Start Scanning' }}
                    </button>
                </div>
                
                <div v-if="result" class="mt-6 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <p class="text-green-800 dark:text-green-400">Attendance marked successfully!</p>
                </div>
            </div>
        </div>
    </Default>
</template>