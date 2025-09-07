<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import Default from '../../../Layouts/Default.vue'
import { ChevronDownIcon, MagnifyingGlassIcon, PlusIcon, EyeIcon, PencilIcon, TrashIcon, CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    teams: Object,
    tracks: Array,
    filters: Object,
    statistics: Object,
})

const searchForm = useForm({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
    track_id: props.filters?.track_id || '',
})

const showDeleteModal = ref(false)
const teamToDelete = ref(null)

const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    approved: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    active: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
}

const filterTeams = () => {
    router.get(route('hackathon-admin.teams.index'), {
        search: searchForm.search,
        status: searchForm.status,
        track_id: searchForm.track_id,
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}

const approveTeam = (team) => {
    router.post(route('hackathon-admin.teams.approve', team.id), {}, {
        preserveScroll: true,
    })
}

const rejectTeam = (team) => {
    const reason = prompt('Please provide a reason for rejection:')
    if (reason) {
        router.post(route('hackathon-admin.teams.reject', team.id), {
            reason: reason
        }, {
            preserveScroll: true,
        })
    }
}

const deleteTeam = () => {
    if (teamToDelete.value) {
        router.delete(route('hackathon-admin.teams.destroy', teamToDelete.value.id), {
            onFinish: () => {
                showDeleteModal.value = false
                teamToDelete.value = null
            }
        })
    }
}

watch(() => searchForm.search, () => {
    clearTimeout(window.searchTimeout)
    window.searchTimeout = setTimeout(filterTeams, 300)
})
</script>

<template>
    <Head title="Team Management" />

    <Default>
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Team Management</h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Manage hackathon teams and participants
                        </p>
                    </div>
                    <a :href="route('hackathon-admin.teams.create')"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        <PlusIcon class="w-5 h-5 mr-2" />
                        Create Team
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div v-if="statistics" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Teams</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ statistics.total }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</div>
                    <div class="mt-1 text-2xl font-semibold text-yellow-600 dark:text-yellow-400">{{ statistics.pending }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Approved</div>
                    <div class="mt-1 text-2xl font-semibold text-green-600 dark:text-green-400">{{ statistics.approved }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">With Ideas</div>
                    <div class="mt-1 text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ statistics.with_ideas }}</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div class="relative">
                            <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                            <input
                                v-model="searchForm.search"
                                type="text"
                                placeholder="Search teams..."
                                class="pl-10 pr-3 py-2 w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <!-- Status Filter -->
                        <select
                            v-model="searchForm.status"
                            @change="filterTeams"
                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="active">Active</option>
                        </select>

                        <!-- Track Filter -->
                        <select
                            v-model="searchForm.track_id"
                            @change="filterTeams"
                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All Tracks</option>
                            <option v-for="track in tracks" :key="track.id" :value="track.id">
                                {{ track.name }}
                            </option>
                        </select>

                        <!-- Clear Filters -->
                        <button
                            @click="searchForm.reset(); filterTeams()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors"
                        >
                            Clear Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Teams Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Team
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Leader
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Track
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Members
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Idea
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="team in teams.data" :key="team.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ team.name }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            ID: {{ team.id }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ team.leader?.name || 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ team.leader?.email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ team.track?.name || 'Not Assigned' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ team.members?.length || 0 }} / 5
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="[statusColors[team.status], 'px-2 py-1 text-xs font-medium rounded-full']">
                                        {{ team.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="team.idea" class="text-sm text-green-600 dark:text-green-400">
                                        Submitted
                                    </span>
                                    <span v-else class="text-sm text-gray-500 dark:text-gray-400">
                                        Not Submitted
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a :href="route('hackathon-admin.teams.show', team.id)"
                                           class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            <EyeIcon class="w-5 h-5" />
                                        </a>
                                        <a :href="route('hackathon-admin.teams.edit', team.id)"
                                           class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            <PencilIcon class="w-5 h-5" />
                                        </a>

                                        <!-- Approve/Reject buttons for pending teams -->
                                        <template v-if="team.status === 'pending'">
                                            <button @click="approveTeam(team)"
                                                    class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                                <CheckCircleIcon class="w-5 h-5" />
                                            </button>
                                            <button @click="rejectTeam(team)"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                <XCircleIcon class="w-5 h-5" />
                                            </button>
                                        </template>

                                        <button @click="teamToDelete = team; showDeleteModal = true"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                            <TrashIcon class="w-5 h-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="teams.links && teams.links.length > 3" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            Showing {{ teams.from }} to {{ teams.to }} of {{ teams.total }} results
                        </div>
                        <div class="flex space-x-2">
                            <template v-for="link in teams.links" :key="link.label">
                                <a v-if="link.url"
                                   :href="link.url"
                                   :class="[
                                       'px-3 py-1 text-sm rounded',
                                       link.active
                                           ? 'bg-blue-600 text-white'
                                           : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'
                                   ]"
                                   v-html="link.label">
                                </a>
                                <span v-else
                                      class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-800 text-gray-400 rounded cursor-not-allowed"
                                      v-html="link.label">
                                </span>
                            </template>
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
                            Are you sure you want to delete team "{{ teamToDelete?.name }}"? This action cannot be undone.
                        </p>
                        <div class="flex justify-end space-x-3">
                            <button @click="showDeleteModal = false"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg">
                                Cancel
                            </button>
                            <button @click="deleteTeam"
                                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>
