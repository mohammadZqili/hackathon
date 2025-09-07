<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { UserGroupIcon, PencilIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    team: Object,
})
</script>

<template>
    <Head title="My Team" />
    
    <Default>
        <div class="max-w-7xl mx-auto">
            <div class="mb-8 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Team</h1>
                <a :href="route('team-leader.team.edit')" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg">
                    <PencilIcon class="w-5 h-5 mr-2" />
                    Edit Team
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Team Information</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Team Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ team?.name || 'Not Set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Track</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ team?.track?.name || 'Not Assigned' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                {{ team?.status || 'Active' }}
                            </span>
                        </dd>
                    </div>
                </dl>

                <h3 class="mt-6 text-lg font-semibold text-gray-900 dark:text-white mb-4">Team Members</h3>
                <ul class="space-y-2">
                    <li v-for="member in team?.members || []" :key="member.id" 
                        class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <span class="text-sm text-gray-900 dark:text-white">{{ member.name }}</span>
                        <span class="text-xs text-gray-500">{{ member.email }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </Default>
</template>