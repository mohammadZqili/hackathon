<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, computed, watch, onMounted } from 'vue'
import Default from '../../../Layouts/Default.vue'
import PageHeader from '@/Components/Shared/PageHeader.vue'
import SearchBar from '@/Components/Shared/SearchBar.vue'
import DataTable from '@/Components/Shared/DataTable.vue'
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

// Theme colors
const themeColor = ref({
    primary: '#0d9488',
    hover: '#0f766e',
    rgb: '13, 148, 136',
    gradientFrom: '#0d9488',
    gradientTo: '#14b8a6'
})

onMounted(() => {
    const root = document.documentElement
    const primary = getComputedStyle(root).getPropertyValue('--primary-color').trim() || '#0d9488'
    const hover = getComputedStyle(root).getPropertyValue('--primary-hover').trim() || '#0f766e'
    const rgb = getComputedStyle(root).getPropertyValue('--primary-color-rgb').trim() || '13, 148, 136'
    const gradientFrom = getComputedStyle(root).getPropertyValue('--primary-gradient-from').trim() || '#0d9488'
    const gradientTo = getComputedStyle(root).getPropertyValue('--primary-gradient-to').trim() || '#14b8a6'

    themeColor.value = {
        primary: primary || themeColor.value.primary,
        hover: hover || themeColor.value.hover,
        rgb: rgb || themeColor.value.rgb,
        gradientFrom: gradientFrom || themeColor.value.gradientFrom,
        gradientTo: gradientTo || themeColor.value.gradientTo
    }
})

const themeStyles = computed(() => ({
    '--theme-primary': themeColor.value.primary,
    '--theme-hover': themeColor.value.hover,
    '--theme-rgb': themeColor.value.rgb,
    '--theme-gradient-from': themeColor.value.gradientFrom,
    '--theme-gradient-to': themeColor.value.gradientTo,
}))

const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    approved: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    active: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
}

// Table configuration for DataTable component
const columns = [
    {
        key: 'name',
        label: 'Team Name',
        width: 'w-[246px]'
    },
    {
        key: 'leader.name',
        label: 'Team Leader',
        width: 'w-[200px]',
        defaultValue: 'No Leader'
    },
    {
        key: 'track.name',
        label: 'Track',
        width: 'w-40',
        defaultValue: 'Not Assigned'
    },
    {
        key: 'members_count',
        label: 'Members',
        width: 'w-24',
        formatter: (item) => `${item.members?.length || 0}/5`
    },
    {
        key: 'status',
        label: 'Status',
        width: 'w-32'
    },
    {
        key: 'idea_status',
        label: 'Idea',
        width: 'w-32'
    },
    {
        key: 'actions',
        label: 'Actions',
        width: 'w-[180px]'
    }
]

const handleSearch = () => {
    clearTimeout(window.searchTimeout)
    window.searchTimeout = setTimeout(filterTeams, 300)
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}

const getStatusBadgeClass = (status) => {
    return statusColors[status] || statusColors.pending
}

const openCreateModal = () => {
    router.visit(route('hackathon-admin.teams.create'))
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
    <Head title="Team Management - Hackathon Admin" />

    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <PageHeader 
                title="Team Management"
                subtitle="Manage teams for current hackathon edition"
                :show-action-button="true"
                action-button-text="Create Team"
                @action="openCreateModal"
            />

            <!-- Statistics Cards -->
            <div v-if="statistics" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6 px-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Teams</p>
                            <p class="text-2xl font-bold" :style="{ color: themeColor.primary }">{{ statistics.total || 0 }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center" 
                             :style="{ backgroundColor: themeColor.primary + '20' }">
                            <svg class="w-6 h-6" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending</p>
                            <p class="text-2xl font-bold text-yellow-600">{{ statistics.pending || 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Approved</p>
                            <p class="text-2xl font-bold text-green-600">{{ statistics.approved || 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">With Ideas</p>
                            <p class="text-2xl font-bold" :style="{ color: themeColor.primary }">{{ statistics.with_ideas || 0 }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                             :style="{ backgroundColor: themeColor.primary + '20' }">
                            <svg class="w-6 h-6" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="px-4 mb-4">
                <div class="flex flex-wrap gap-4">
                    <!-- Status Filter -->
                    <div class="min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select v-model="searchForm.status" 
                                @change="filterTeams"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-primary focus:ring-primary">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="active">Active</option>
                        </select>
                    </div>

                    <!-- Track Filter -->
                    <div class="min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Track</label>
                        <select v-model="searchForm.track_id"
                                @change="filterTeams"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-primary focus:ring-primary">
                            <option value="">All Tracks</option>
                            <option v-for="track in tracks" :key="track.id" :value="track.id">
                                {{ track.name }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
            <SearchBar 
                v-model="searchForm.search"
                placeholder="Search teams by name or leader"
                @update:model-value="handleSearch"
            />

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
