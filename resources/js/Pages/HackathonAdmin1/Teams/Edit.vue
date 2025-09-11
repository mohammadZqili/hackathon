<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { ArrowLeftIcon, UsersIcon, TrashIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    team: Object,
    tracks: Array,
})

const form = useForm({
    name: props.team?.name || '',
    track_id: props.team?.track_id || '',
    description: props.team?.description || '',
    status: props.team?.status || 'pending',
})

const submitForm = () => {
    form.put(route('hackathon-admin.teams.update', props.team.id))
}

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
        approved: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
        rejected: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
    }
    return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
}
</script>

<template>
    <Head :title="`Edit Team - ${team?.name}`" />
    
    <Default>
        <div class="max-w-4xl mx-auto">
            <div class="mb-8 flex items-center space-x-4">
                <a :href="route('hackathon-admin.teams.show', team.id)" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <ArrowLeftIcon class="w-5 h-5" />
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Team</h1>
                <span :class="getStatusColor(team?.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                    {{ team?.status }}
                </span>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Team Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Team Name *
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Enter team name"
                            />
                            <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
                        </div>

                        <!-- Track Selection -->
                        <div>
                            <label for="track_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Track *
                            </label>
                            <select
                                id="track_id"
                                v-model="form.track_id"
                                required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            >
                                <option value="">Select a track</option>
                                <option v-for="track in tracks" :key="track.id" :value="track.id">
                                    {{ track.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.track_id" class="mt-1 text-sm text-red-600">{{ form.errors.track_id }}</div>
                        </div>
                    </div>

                    <!-- Team Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Team Description
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Enter team description..."
                        ></textarea>
                        <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</div>
                    </div>

                    <!-- Status Selection -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Team Status
                        </label>
                        <select
                            id="status"
                            v-model="form.status"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                        <div v-if="form.errors.status" class="mt-1 text-sm text-red-600">{{ form.errors.status }}</div>
                    </div>

                    <!-- Team Information (Read-only) -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Team Information</h3>
                        
                        <!-- Team Leader -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Team Leader
                            </label>
                            <div class="flex items-center space-x-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg px-3 py-2">
                                <UsersIcon class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                <span class="text-sm text-gray-900 dark:text-white">
                                    {{ team?.leader?.name }} ({{ team?.leader?.email }})
                                </span>
                            </div>
                        </div>

                        <!-- Team Members -->
                        <div v-if="team?.members && team.members.length > 0">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Team Members ({{ team.members.length }})
                            </label>
                            <div class="space-y-2">
                                <div
                                    v-for="member in team.members"
                                    :key="member.id"
                                    class="flex items-center space-x-2 bg-gray-100 dark:bg-gray-600 rounded-lg px-3 py-2"
                                >
                                    <UsersIcon class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                                    <span class="text-sm text-gray-900 dark:text-white">
                                        {{ member.name }} ({{ member.email }})
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-sm text-gray-500 dark:text-gray-400">
                            No additional members yet
                        </div>

                        <!-- Current Track -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Current Track
                            </label>
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ team?.track?.name || 'No track assigned' }}
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-between pt-6">
                        <div class="flex space-x-3">
                            <a
                                :href="route('hackathon-admin.teams.show', team.id)"
                                class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors"
                            >
                                Cancel
                            </a>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white rounded-lg font-medium transition-colors"
                            >
                                <span v-if="form.processing">Updating...</span>
                                <span v-else>Update Team</span>
                            </button>
                        </div>

                        <!-- Delete Button -->
                        <button
                            type="button"
                            @click="deleteTeam"
                            class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors flex items-center space-x-2"
                        >
                            <TrashIcon class="w-4 h-4" />
                            <span>Delete Team</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Default>
</template>

<script>
export default {
    methods: {
        deleteTeam() {
            if (confirm('Are you sure you want to delete this team? This action cannot be undone.')) {
                this.$inertia.delete(route('hackathon-admin.teams.destroy', this.team.id))
            }
        }
    }
}
</script>
