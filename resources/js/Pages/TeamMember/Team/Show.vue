<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { UserGroupIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    team: Object,
})
</script>

<template>
    <Head title="My Team" />
    
    <Default>
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">My Team</h1>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <UserGroupIcon class="w-5 h-5 mr-2" />
                    Team Information
                </h2>
                
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Team Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ team?.name || 'Not Assigned' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Team Leader</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ team?.leader?.name || 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Track</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ team?.track?.name || 'Not Assigned' }}</dd>
                    </div>
                </dl>

                <h3 class="mt-6 text-lg font-semibold text-gray-900 dark:text-white mb-3">Team Members</h3>
                <ul class="space-y-2">
                    <li v-for="member in team?.members || []" :key="member.id" 
                        class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-sm text-gray-900 dark:text-white">{{ member.name }}</div>
                        <div class="text-xs text-gray-500">{{ member.email }}</div>
                    </li>
                </ul>
            </div>
        </div>
    </Default>
</template>