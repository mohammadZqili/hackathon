<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { LightBulbIcon, PencilIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    ideas: Object,
    statistics: Object,
})
</script>

<template>
    <Head title="Track Ideas Review" />
    
    <Default>
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Ideas to Review</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Ideas from your assigned track requiring review</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Ideas</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ statistics?.total || 0 }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Review</div>
                    <div class="mt-1 text-2xl font-semibold text-yellow-600 dark:text-yellow-400">{{ statistics?.pending || 0 }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Reviewed</div>
                    <div class="mt-1 text-2xl font-semibold text-green-600 dark:text-green-400">{{ statistics?.reviewed || 0 }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Needs Revision</div>
                    <div class="mt-1 text-2xl font-semibold text-orange-600 dark:text-orange-400">{{ statistics?.needs_revision || 0 }}</div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Idea</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Team</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="idea in ideas?.data || []" :key="idea.id">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ idea.title }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ idea.team?.name }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                    {{ idea.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a :href="route('track-supervisor.ideas.review', idea.id)" class="text-blue-600 hover:text-blue-900">
                                    <PencilIcon class="w-5 h-5 inline" />
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </Default>
</template>