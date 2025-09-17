<script setup>
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL, direction, locale } = useLocalization()
import { Head, Link } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { ref, computed } from 'vue'

const props = defineProps({
    statistics: Object,
    recentActivities: Array,
    chartData: Object,
    editions: Array,
    filters: Object
})

// Extract data from statistics object with track-specific stats
const stats = computed(() => [
    {
        label: 'Teams in Tracks',
        value: props.statistics?.teams?.total || 0,
        change: props.statistics?.teams?.growth || null,
        icon: 'users',
        color: 'blue'
    },
    {
        label: 'Ideas Submitted',
        value: props.statistics?.ideas?.submitted || 0,
        total: props.statistics?.ideas?.total || 0,
        icon: 'lightbulb',
        color: 'green'
    },
    {
        label: 'Workshops',
        value: props.statistics?.workshops?.upcoming || 0,
        total: props.statistics?.workshops?.total || 0,
        icon: 'calendar',
        color: 'purple'
    },
    {
        label: 'Active Members',
        value: props.statistics?.users?.active || 0,
        total: props.statistics?.users?.total || 0,
        icon: 'user-group',
        color: 'orange'
    },
])

// Track information
const trackInfo = computed(() => ({
    supervised: props.statistics?.tracks?.supervised || 0,
    names: props.statistics?.tracks?.names || 'No tracks assigned',
    teams: props.statistics?.tracks?.teams_in_tracks || 0
}))
</script>

<template>
    <Head title="Track Supervisor Dashboard" />

    <Default>
        <div class="container mx-auto px-4 py-8">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Track Supervisor Dashboard</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Monitor and manage your assigned tracks</p>
            </div>

            <!-- Track Info Banner -->
            <div class="bg-gradient-to-r from-teal-500 to-teal-600 rounded-lg p-6 mb-8 text-white">
                <h2 class="text-xl font-semibold mb-2">Your Supervised Tracks</h2>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-teal-50 text-sm">Tracks: {{ trackInfo.supervised }}</p>
                        <p class="text-lg font-medium mt-1">{{ trackInfo.names }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold">{{ trackInfo.teams }}</p>
                        <p class="text-teal-50 text-sm">Total Teams</p>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div v-for="stat in stats" :key="stat.label"
                     class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ stat.label }}</div>
                            <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ stat.value }}</div>
                            <div v-if="stat.total" class="mt-1 text-sm text-gray-500">
                                of {{ stat.total }} total
                            </div>
                        </div>
                        <div class="p-2 rounded-lg"
                             :class="{
                                 'bg-blue-100 text-blue-600': stat.color === 'blue',
                                 'bg-green-100 text-green-600': stat.color === 'green',
                                 'bg-purple-100 text-purple-600': stat.color === 'purple',
                                 'bg-orange-100 text-orange-600': stat.color === 'orange'
                             }">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path v-if="stat.icon === 'users'" d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                <path v-else-if="stat.icon === 'lightbulb'" d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6z"/>
                                <path v-else-if="stat.icon === 'calendar'" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1z"/>
                                <path v-else d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Activities -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h2>
                    <div class="space-y-3">
                        <div v-if="!recentActivities || recentActivities.length === 0"
                             class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">
                            No recent activity in your tracks
                        </div>
                        <div v-else v-for="activity in recentActivities" :key="activity.id"
                             class="flex items-start space-x-3 pb-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                                 :class="{
                                     'bg-green-100 text-green-600': activity.color === 'green',
                                     'bg-blue-100 text-blue-600': activity.color === 'blue',
                                     'bg-yellow-100 text-yellow-600': activity.color === 'yellow'
                                 }">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path v-if="activity.icon === 'users'" d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path v-else d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ activity.title }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ activity.description }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                    {{ activity.timestamp ? new Date(activity.timestamp).toLocaleString() : '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
                    <div class="space-y-3">
                        <Link :href="route('track-supervisor.teams.index')"
                              class="flex items-center justify-between px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-teal-50 dark:hover:bg-teal-900/20 rounded-lg transition-colors">
                            <span>Manage Teams</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </Link>
                        <Link :href="route('track-supervisor.ideas.index')"
                              class="flex items-center justify-between px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-teal-50 dark:hover:bg-teal-900/20 rounded-lg transition-colors">
                            <span>Review Ideas</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </Link>
                        <Link :href="route('track-supervisor.workshops.index')"
                              class="flex items-center justify-between px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-teal-50 dark:hover:bg-teal-900/20 rounded-lg transition-colors">
                            <span>Manage Workshops</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </Link>
                        <Link :href="route('track-supervisor.checkins.index')"
                              class="flex items-center justify-between px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-teal-50 dark:hover:bg-teal-900/20 rounded-lg transition-colors">
                            <span>Workshop Check-ins</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </Link>
                        <Link :href="route('track-supervisor.reports.index')"
                              class="flex items-center justify-between px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-teal-50 dark:hover:bg-teal-900/20 rounded-lg transition-colors">
                            <span>View Reports</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>
