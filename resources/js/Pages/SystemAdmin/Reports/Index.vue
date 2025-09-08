<template>
    <Head title="Reports & Analytics" />
    <Default>
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Reports & Analytics</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Comprehensive reporting and analytics for all hackathon data.
                </p>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Users</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ reportSummary.total_users }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="bg-green-100 dark:bg-green-900 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Teams</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ reportSummary.total_teams }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Ideas</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ reportSummary.total_ideas }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-6m-2-5h4m-4 0V9a1 1 0 011-1h2a1 1 0 011 1v7m-4 0h4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Workshops</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ reportSummary.total_workshops }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Categories -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Users Report -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-gray-900 dark:text-white">Users Report</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Detailed analysis of user registrations, roles, and activity patterns.
                    </p>
                    <a :href="route('system-admin.reports.users')" 
                       class="inline-flex items-center text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        View Users Report
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <!-- Teams Report -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="bg-green-100 dark:bg-green-900 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-gray-900 dark:text-white">Teams Report</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Team formation statistics, member distribution, and performance metrics.
                    </p>
                    <a :href="route('system-admin.reports.teams')" 
                       class="inline-flex items-center text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">
                        View Teams Report
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <!-- Ideas Report -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-gray-900 dark:text-white">Ideas Report</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Idea submission patterns, review status, and scoring analytics.
                    </p>
                    <a :href="route('system-admin.reports.ideas')" 
                       class="inline-flex items-center text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300">
                        View Ideas Report
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <!-- Workshops Report -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-6m-2-5h4m-4 0V9a1 1 0 011-1h2a1 1 0 011 1v7m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-gray-900 dark:text-white">Workshops Report</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Workshop attendance, engagement rates, and feedback analysis.
                    </p>
                    <a :href="route('system-admin.reports.workshops')" 
                       class="inline-flex items-center text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300">
                        View Workshops Report
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <!-- System Health -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="bg-red-100 dark:bg-red-900 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-gray-900 dark:text-white">System Health</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Server performance, database status, and system monitoring.
                    </p>
                    <a :href="route('system-admin.reports.system-health')" 
                       class="inline-flex items-center text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                        View System Health
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <!-- Current Edition Summary -->
                <div v-if="reportSummary.current_edition" class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="bg-indigo-100 dark:bg-indigo-900 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-gray-900 dark:text-white">Current Edition</h3>
                    </div>
                    <div class="space-y-2">
                        <p class="text-gray-900 dark:text-white font-medium">{{ reportSummary.current_edition.name }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Year: {{ reportSummary.current_edition.year }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Status: {{ reportSummary.current_edition.status }}</p>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'

const props = defineProps({
    reportSummary: {
        type: Object,
        required: true
    }
})
</script>
