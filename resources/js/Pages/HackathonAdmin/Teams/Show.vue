<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Default from '@/Layouts/Default.vue'
import { ArrowLeftIcon, PencilIcon, UserGroupIcon, LightBulbIcon, CalendarIcon, CheckCircleIcon, XCircleIcon, TrashIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    team: Object,
})

const showDeleteModal = ref(false)

const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    approved: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    active: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
}

const approveTeam = () => {
    router.post(route('hackathon-admin.teams.approve', props.team.id))
}

const rejectTeam = () => {
    const reason = prompt('Please provide a reason for rejection:')
    if (reason) {
        router.post(route('hackathon-admin.teams.reject', props.team.id), {
            reason: reason
        })
    }
}

const deleteTeam = () => {
    router.delete(route('hackathon-admin.teams.destroy', props.team.id), {
        onSuccess: () => {
            // Will redirect to index
        }
    })
}
</script>

<template>
    <Head :title="`Team: ${team.name}`" />
    
    <Default>
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a :href="route('hackathon-admin.teams.index')"
                           class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                            <ArrowLeftIcon class="w-5 h-5" />
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ team.name }}</h1>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Team ID: {{ team.id }} | Created: {{ new Date(team.created_at).toLocaleDateString() }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span :class="[statusColors[team.status], 'px-3 py-1 text-sm font-medium rounded-full']">
                            {{ team.status }}
                        </span>
                        <a :href="route('hackathon-admin.teams.edit', team.id)"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            <PencilIcon class="w-4 h-4 mr-2" />
                            Edit Team
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Actions for Pending Teams -->
            <div v-if="team.status === 'pending'" class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Pending Approval</h3>
                        <p class="text-sm text-yellow-600 dark:text-yellow-400 mt-1">
                            This team is waiting for approval. Review the details and take action.
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <button @click="approveTeam"
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                            <CheckCircleIcon class="w-4 h-4 mr-2" />
                            Approve
                        </button>
                        <button @click="rejectTeam"
                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                            <XCircleIcon class="w-4 h-4 mr-2" />
                            Reject
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Team Details -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Team Details</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Team Name</label>
                                <p class="mt-1 text-gray-900 dark:text-white">{{ team.name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Track</label>
                                <p class="mt-1 text-gray-900 dark:text-white">
                                    {{ team.track?.name || 'Not assigned to any track' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Hackathon Edition</label>
                                <p class="mt-1 text-gray-900 dark:text-white">
                                    {{ team.hackathon?.name }} ({{ team.hackathon?.year }})
                                </p>
                            </div>
                            <div v-if="team.rejection_reason">
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Rejection Reason</label>
                                <p class="mt-1 text-red-600 dark:text-red-400">{{ team.rejection_reason }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Team Members -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                                <UserGroupIcon class="w-5 h-5 mr-2" />
                                Team Members ({{ team.members?.length || 0 }}/5)
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Team Leader -->
                                <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ team.leader?.name }}
                                            <span class="ml-2 px-2 py-1 text-xs bg-blue-600 text-white rounded">Leader</span>
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ team.leader?.email }}</p>
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        Joined: {{ new Date(team.leader?.created_at).toLocaleDateString() }}
                                    </div>
                                </div>

                                <!-- Other Members -->
                                <div v-for="member in team.members" :key="member.id" 
                                     class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ member.name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ member.email }}</p>
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        Joined: {{ new Date(member.pivot?.created_at).toLocaleDateString() }}
                                    </div>
                                </div>

                                <!-- Empty Slots -->
                                <div v-for="i in (5 - (team.members?.length || 0) - 1)" :key="`empty-${i}`"
                                     class="flex items-center justify-center p-3 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Empty Slot</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Team Idea -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                                <LightBulbIcon class="w-5 h-5 mr-2" />
                                Team Idea
                            </h2>
                            <a v-if="team.idea" 
                               :href="route('hackathon-admin.ideas.show', team.idea.id)"
                               class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                View Full Idea ’
                            </a>
                        </div>
                        <div class="p-6">
                            <div v-if="team.idea" class="space-y-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Title</label>
                                    <p class="mt-1 text-gray-900 dark:text-white font-medium">{{ team.idea.title }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</label>
                                    <p class="mt-1 text-gray-900 dark:text-white">{{ team.idea.description }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                                    <span :class="[
                                        team.idea.status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' :
                                        team.idea.status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' :
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                                        'px-2 py-1 text-xs font-medium rounded-full'
                                    ]">
                                        {{ team.idea.status }}
                                    </span>
                                </div>
                            </div>
                            <div v-else class="text-center py-8">
                                <LightBulbIcon class="mx-auto h-12 w-12 text-gray-400" />
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No idea submitted yet</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Actions</h2>
                        </div>
                        <div class="p-6 space-y-3">
                            <a :href="route('hackathon-admin.teams.edit', team.id)"
                               class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                <PencilIcon class="w-4 h-4 mr-2" />
                                Edit Team
                            </a>
                            
                            <template v-if="team.status === 'pending'">
                                <button @click="approveTeam"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                                    <CheckCircleIcon class="w-4 h-4 mr-2" />
                                    Approve Team
                                </button>
                                <button @click="rejectTeam"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                                    <XCircleIcon class="w-4 h-4 mr-2" />
                                    Reject Team
                                </button>
                            </template>
                            
                            <button @click="showDeleteModal = true"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-600 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 font-medium rounded-lg transition-colors">
                                <TrashIcon class="w-4 h-4 mr-2" />
                                Delete Team
                            </button>
                        </div>
                    </div>

                    <!-- Activity Timeline -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Activity</h2>
                        </div>
                        <div class="p-6">
                            <div class="flow-root">
                                <ul role="list" class="-mb-8">
                                    <li>
                                        <div class="relative pb-8">
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                        <CheckCircleIcon class="h-5 w-5 text-white" />
                                                    </span>
                                                </div>
                                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                    <div>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">Team created</p>
                                                    </div>
                                                    <div class="whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                                        {{ new Date(team.created_at).toLocaleDateString() }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li v-if="team.idea">
                                        <div class="relative pb-8">
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                        <LightBulbIcon class="h-5 w-5 text-white" />
                                                    </span>
                                                </div>
                                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                    <div>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">Idea submitted</p>
                                                    </div>
                                                    <div class="whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                                        {{ new Date(team.idea.created_at).toLocaleDateString() }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDeleteModal = false"></div>
                    
                    <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            Confirm Delete
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                            Are you sure you want to delete team "{{ team.name }}"? This will also remove all team members and associated data. This action cannot be undone.
                        </p>
                        <div class="flex justify-end space-x-3">
                            <button @click="showDeleteModal = false"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg">
                                Cancel
                            </button>
                            <button @click="deleteTeam"
                                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg">
                                Delete Team
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>