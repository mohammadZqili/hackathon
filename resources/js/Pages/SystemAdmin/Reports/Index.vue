<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'



const props = defineProps({
    reports: Object
})


const availableReports = [
    { id: 'users', name: 'User Report', description: 'Complete user list with roles' },
    { id: 'activity', name: 'Activity Report', description: 'System activity log' },
    { id: 'performance', name: 'Performance Report', description: 'System performance metrics' }
]

const generateReport = (reportId) => {
    // Generate report logic
}
</script>

<template>
    <Head title="System Reports" />
    
    <Default>
        <div class="container mx-auto px-4 py-8">
            
            <div class="max-w-6xl mx-auto">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">System Reports</h1>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="report in availableReports" :key="report.id"
                         class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-2">{{ report.name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ report.description }}</p>
                        <button @click="generateReport(report.id)"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">
                            Generate Report
                        </button>
                    </div>
                </div>

                <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold">Recent Reports</h2>
                    </div>
                    <div class="p-6">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 uppercase">
                                    <th class="pb-3">Report</th>
                                    <th class="pb-3">Generated</th>
                                    <th class="pb-3">Size</th>
                                    <th class="pb-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="report in reports" :key="report.id">
                                    <td class="py-3">{{ report.name }}</td>
                                    <td class="py-3">{{ report.created_at }}</td>
                                    <td class="py-3">{{ report.size }}</td>
                                    <td class="py-3 text-right">
                                        <a :href="report.download_url"
                                           class="text-blue-600 hover:text-blue-900">Download</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>
