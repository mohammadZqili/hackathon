<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import Default from '../../../Layouts/Default.vue'
import { ArrowLeftIcon, UsersIcon, PlusIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    tracks: Array,
    users: Array,
    edition: Object,
})

const form = useForm({
    name: '',
    track_id: '',
    leader_id: '',
    member_ids: [],
    description: '',
    status: 'pending',
})

const submitForm = () => {
    form.post(route('hackathon-admin.teams.store'))
}

const addMember = (userId) => {
    if (!form.member_ids.includes(userId) && form.member_ids.length < 4) {
        form.member_ids.push(userId)
    }
}

const removeMember = (userId) => {
    const index = form.member_ids.indexOf(userId)
    if (index > -1) {
        form.member_ids.splice(index, 1)
    }
}

const availableUsers = computed(() => {
    return props.users.filter(user => 
        user.id !== form.leader_id && !form.member_ids.includes(user.id)
    )
})

const selectedMembers = computed(() => {
    return props.users.filter(user => form.member_ids.includes(user.id))
})
</script>

<template>
    <Head title="Create Team" />
    
    <Default>
        <div class="max-w-4xl mx-auto">
            <div class="mb-8 flex items-center space-x-4">
                <a :href="route('hackathon-admin.teams.index')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <ArrowLeftIcon class="w-5 h-5" />
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create New Team</h1>
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

                    <!-- Team Leader Selection -->
                    <div>
                        <label for="leader_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Team Leader *
                        </label>
                        <select
                            id="leader_id"
                            v-model="form.leader_id"
                            required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">Select team leader</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">
                                {{ user.name }} ({{ user.email }})
                            </option>
                        </select>
                        <div v-if="form.errors.leader_id" class="mt-1 text-sm text-red-600">{{ form.errors.leader_id }}</div>
                    </div>

                    <!-- Team Members Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Team Members (Optional)
                        </label>
                        
                        <!-- Selected Members -->
                        <div v-if="selectedMembers.length > 0" class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Selected Members:</h4>
                            <div class="space-y-2">
                                <div
                                    v-for="member in selectedMembers"
                                    :key="member.id"
                                    class="flex items-center justify-between bg-blue-50 dark:bg-blue-900/20 rounded-lg px-3 py-2"
                                >
                                    <div class="flex items-center space-x-2">
                                        <UsersIcon class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                        <span class="text-sm text-gray-900 dark:text-white">
                                            {{ member.name }} ({{ member.email }})
                                        </span>
                                    </div>
                                    <button
                                        type="button"
                                        @click="removeMember(member.id)"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                    >
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Available Users -->
                        <div v-if="availableUsers.length > 0 && selectedMembers.length < 4">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Available Users:</h4>
                            <div class="max-h-48 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-lg">
                                <div
                                    v-for="user in availableUsers"
                                    :key="user.id"
                                    class="flex items-center justify-between px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-200 dark:border-gray-600 last:border-b-0"
                                >
                                    <div class="flex items-center space-x-2">
                                        <UsersIcon class="w-4 h-4 text-gray-400" />
                                        <span class="text-sm text-gray-900 dark:text-white">
                                            {{ user.name }} ({{ user.email }})
                                        </span>
                                    </div>
                                    <button
                                        type="button"
                                        @click="addMember(user.id)"
                                        class="flex items-center space-x-1 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                    >
                                        <PlusIcon class="w-4 h-4" />
                                        <span class="text-xs">Add</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div v-if="selectedMembers.length >= 4" class="text-sm text-yellow-600 dark:text-yellow-400">
                            Maximum team size reached (4 members + 1 leader = 5 total)
                        </div>
                        
                        <div v-if="form.errors.member_ids" class="mt-1 text-sm text-red-600">{{ form.errors.member_ids }}</div>
                    </div>

                    <!-- Team Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Team Description (Optional)
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
                            Initial Status
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

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-6">
                        <a
                            :href="route('hackathon-admin.teams.index')"
                            class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors"
                        >
                            Cancel
                        </a>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white rounded-lg font-medium transition-colors"
                        >
                            <span v-if="form.processing">Creating...</span>
                            <span v-else>Create Team</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Default>
</template>
