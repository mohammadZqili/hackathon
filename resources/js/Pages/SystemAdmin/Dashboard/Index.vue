<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'



const props = defineProps({
    stats: Object, recentActivity: Object, systemHealth: Object
})


</script>

<template>
    <Head title="System Dashboard" />
    
    <Default>
        <div class="container mx-auto px-4 py-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div v-for="stat in stats" :key="stat.label" 
                     class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ stat.label }}</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ stat.value }}</div>
                    <div v-if="stat.change" class="mt-2 text-sm"
                         :class="stat.change > 0 ? 'text-green-600' : 'text-red-600'">
                        {{ stat.change > 0 ? '+' : '' }}{{ stat.change }}%
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h2>
                    <div class="space-y-3">
                        <div v-for="activity in recentActivity" :key="activity.id"
                             class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-2 h-2 mt-2 rounded-full bg-blue-500"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900 dark:text-white">{{ activity.description }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ activity.time }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">System Health</h2>
                    <div class="space-y-3">
                        <div v-for="metric in systemHealth" :key="metric.name">
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ metric.name }}</span>
                                <span class="text-sm font-medium">{{ metric.value }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full"
                                     :class="metric.value > 80 ? 'bg-green-500' : metric.value > 50 ? 'bg-yellow-500' : 'bg-red-500'"
                                     :style="`width: ${metric.value}%`"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>
