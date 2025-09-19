<script setup>
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL, direction, locale } = useLocalization()
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'

const props = defineProps({
    statistics: Object
})

// Extract data from statistics object
const stats = [
    { label: t('admin.dashboard.total_editions'), value: props.statistics?.total_editions || 0 },
    { label: t('admin.dashboard.total_users'), value: props.statistics?.total_users || 0 },
    { label: t('admin.dashboard.total_teams'), value: props.statistics?.total_teams || 0 },
    { label: t('admin.dashboard.total_ideas'), value: props.statistics?.total_ideas || 0 },
    { label: t('admin.dashboard.total_workshops'), value: props.statistics?.total_workshops || 0 },
]

const recentActivity = props.statistics?.recent_activities || []
const currentEdition = props.statistics?.current_edition
</script>

<template>
    <Head :title="t('admin.dashboard.title')" />

    <Default>
        <div class="container mx-auto px-4 py-8">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div v-for="stat in stats" :key="stat.label"
                     class="bg-white dark:bg-gray-800 rounded-lg shadow p-6"
                     :class="{ 'text-right': isRTL, 'text-left': !isRTL }">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ stat.label }}</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ stat.value }}</div>
                    <div v-if="stat.change" class="mt-2 text-sm"
                         :class="stat.change > 0 ? 'text-green-600' : 'text-red-600'">
                        {{ stat.change > 0 ? '+' : '' }}{{ stat.change }}%
                    </div>
                </div>
            </div>

            <!-- Current Edition Info -->
            <div v-if="currentEdition" class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 mb-8">
                <h2 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2"
                    :class="{ 'text-right': isRTL, 'text-left': !isRTL }">
                    {{ t('admin.dashboard.current_edition') }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <div class="text-sm text-blue-600 dark:text-blue-300">Name</div>
                        <div class="font-medium text-blue-900 dark:text-blue-100">{{ currentEdition.name }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-blue-600 dark:text-blue-300">Year</div>
                        <div class="font-medium text-blue-900 dark:text-blue-100">{{ currentEdition.year }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-blue-600 dark:text-blue-300">Teams</div>
                        <div class="font-medium text-blue-900 dark:text-blue-100">{{ currentEdition.teams_count }}</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h2>
                    <div class="space-y-3">
                        <div v-if="recentActivity.length === 0" class="text-sm text-gray-500 dark:text-gray-400">
                            No recent activity
                        </div>
                        <div v-for="(activity, index) in recentActivity" :key="index"
                             class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-2 h-2 mt-2 rounded-full bg-blue-500"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900 dark:text-white">{{ activity.message }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ activity.time }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
                    <div class="space-y-3">
                        <a href="/hackathon-admin/editions" class="block w-full text-left px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                            Manage Hackathon Editions
                        </a>
                        <a href="/hackathon-admin/users" class="block w-full text-left px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                            Manage Users
                        </a>
                        <a href="/hackathon-admin/settings" class="block w-full text-left px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                            System Settings
                        </a>
                        <a href="/hackathon-admin/reports" class="block w-full text-left px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                            View Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>
