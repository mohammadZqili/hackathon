<script setup>
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'
import Default from '../../../Layouts/Default.vue'
import { AcademicCapIcon, ClockIcon, MapPinIcon, UsersIcon, QrCodeIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    workshop: Object,
    registrations: Array,
})
</script>

<template>
    <Head :title="`Workshop: ${workshop?.title || 'Workshop'}`" />
    
    <Default>
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ workshop?.title || 'Workshop Details' }}</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Track Supervisor View</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Workshop Information</h2>
                        <p class="text-gray-700 dark:text-gray-300">{{ workshop?.description }}</p>
                        
                        <div class="mt-6 space-y-3">
                            <div class="flex items-center text-sm">
                                <ClockIcon class="w-5 h-5 mr-2 text-gray-400" />
                                <span>{{ workshop?.start_time }} - {{ workshop?.end_time }}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <MapPinIcon class="w-5 h-5 mr-2 text-gray-400" />
                                <span>{{ workshop?.location || 'TBD' }}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <UsersIcon class="w-5 h-5 mr-2 text-gray-400" />
                                <span>{{ registrations?.length || 0 }} / {{ workshop?.max_participants }} registered</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">QR Scanner</h2>
                        <button class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center justify-center">
                            <QrCodeIcon class="w-5 h-5 mr-2" />
                            Scan Attendance
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>