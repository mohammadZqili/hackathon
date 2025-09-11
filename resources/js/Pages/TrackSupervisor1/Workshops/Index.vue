<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { AcademicCapIcon, EyeIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    workshops: Object,
})
</script>

<template>
    <Head title="Track Workshops" />
    
    <Default>
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Track Workshops</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Workshops related to your assigned track</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Registrations</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="workshop in workshops?.data || []" :key="workshop.id">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ workshop.title }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ workshop.start_time }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ workshop.registrations_count || 0 }} / {{ workshop.max_participants }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a :href="route('track-supervisor.workshops.show', workshop.id)" class="text-blue-600 hover:text-blue-900">
                                    <EyeIcon class="w-5 h-5 inline" />
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </Default>
</template>