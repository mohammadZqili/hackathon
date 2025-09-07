<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { AcademicCapIcon, UsersIcon, QrCodeIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    workshop: Object,
    attendanceStats: Object,
})
</script>

<template>
    <Head :title="workshop?.title || 'Workshop'" />
    
    <Default>
        <div class="max-w-7xl mx-auto">
            <div class="mb-8 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ workshop?.title }}</h1>
                <div class="flex space-x-3">
                    <a :href="route('hackathon-admin.workshops.edit', workshop.id)"
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg">Edit</a>
                    <a :href="route('hackathon-admin.workshops.attendance', workshop.id)"
                       class="px-4 py-2 bg-green-600 text-white rounded-lg">Attendance</a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">Workshop Details</h2>
                    <p class="text-gray-700 dark:text-gray-300 mb-6">{{ workshop?.description }}</p>
                    
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Location</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ workshop?.location }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Time</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ workshop?.start_time }} - {{ workshop?.end_time }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">Statistics</h2>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Registered</dt>
                            <dd class="mt-1 text-2xl font-semibold">{{ attendanceStats?.registered || 0 }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Attended</dt>
                            <dd class="mt-1 text-2xl font-semibold">{{ attendanceStats?.attended || 0 }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Attendance Rate</dt>
                            <dd class="mt-1 text-2xl font-semibold">{{ attendanceStats?.attendance_rate || 0 }}%</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </Default>
</template>