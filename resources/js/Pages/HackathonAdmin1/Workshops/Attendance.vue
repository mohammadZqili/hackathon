<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    workshop: Object,
    registrations: Array,
})
</script>

<template>
    <Head :title="`Attendance: ${workshop?.title}`" />
    
    <Default>
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Workshop Attendance</h1>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registered At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attendance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="registration in registrations" :key="registration.user.id">
                            <td class="px-6 py-4 text-sm">{{ registration.user.name }}</td>
                            <td class="px-6 py-4 text-sm">{{ registration.user.email }}</td>
                            <td class="px-6 py-4 text-sm">{{ registration.registered_at }}</td>
                            <td class="px-6 py-4">
                                <CheckCircleIcon v-if="registration.attended" class="w-5 h-5 text-green-600" />
                                <XCircleIcon v-else class="w-5 h-5 text-red-600" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </Default>
</template>