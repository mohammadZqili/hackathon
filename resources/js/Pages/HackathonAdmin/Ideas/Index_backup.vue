<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, computed, watch, onMounted } from 'vue'
import Default from '@/Layouts/Default.vue'
import PageHeader from '@/Components/Shared/PageHeader.vue'
import SearchBar from '@/Components/Shared/SearchBar.vue'
import DataTable from '@/Components/Shared/DataTable.vue'
import { 
    MagnifyingGlassIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    ideas: Object,
    statistics: Object,
    tracks: Array,
    supervisors: Array,
    filters: Object,
})

const searchForm = useForm({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
    track_id: props.filters?.track_id || '',
    has_supervisor: props.filters?.has_supervisor || '',
})

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

// Status badge colors using GuacPanel theme
const statusColors = {
    pending_review: 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800',
    approved: 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800',
    accepted: 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800',
    rejected: 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800',
    in_progress: 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-900/20 dark:text-indigo-400 dark:border-indigo-800',
    completed: 'bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-900/20 dark:text-gray-400 dark:border-gray-800',
    draft: 'bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-900/20 dark:text-gray-400 dark:border-gray-800',
    submitted: 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800',
    under_review: 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800',
    needs_revision: 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-900/20 dark:text-orange-400 dark:border-orange-800',
}

// Display names for statuses
const statusDisplayNames = {
    pending_review: 'Pending Review',
    approved: 'Approved',
    rejected: 'Rejected',
    in_progress: 'In Progress',
    completed: 'Completed',
    draft: 'Draft',
    submitted: 'Submitted',
    under_review: 'Under Review',
    accepted: 'Accepted',
    needs_revision: 'Needs Revision',
}

// Table configuration for DataTable component
const columns = [
    {
        key: 'title',
        label: 'Idea Title',
        width: 'w-[300px]'
    },
    {
        key: 'team.name',
        label: 'Team',
        width: 'w-[180px]',
        defaultValue: 'No Team'
    },
    {
        key: 'track.name',
        label: 'Track',
        width: 'w-[150px]',
        defaultValue: 'No Track'
    },
    {
        key: 'supervisor.name',
        label: 'Supervisor',
        width: 'w-[150px]',
        defaultValue: 'Not Assigned'
    },
    {
        key: 'status',
        label: 'Status',
        width: 'w-[120px]'
    },
    {
        key: 'score',
        label: 'Score',
        width: 'w-[80px]',
        formatter: (item) => item.total_score ? `${item.total_score}/100` : 'Not Rated'
    },
    {
        key: 'submitted_at',
        label: 'Submitted',
        width: 'w-[120px]',
        formatter: (item) => item.submitted_at ? formatDate(item.submitted_at) : 'Draft'
    },
    {
        key: 'actions',
        label: 'Actions',
        width: 'w-[200px]'
    }
]

const handleSearch = () => {
    clearTimeout(window.searchTimeout)
    window.searchTimeout = setTimeout(filterIdeas, 300)
}

const getStatusBadgeClass = (status) => {
    return statusColors[status] || statusColors.draft
}

const filterIdeas = () => {
    router.get(route('hackathon-admin.ideas.index'), {
        search: searchForm.search,
        status: searchForm.status,
        track_id: searchForm.track_id,
        has_supervisor: searchForm.has_supervisor,
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}

watch(() => searchForm.search, () => {
    clearTimeout(window.searchTimeout)
    window.searchTimeout = setTimeout(filterIdeas, 300)
})

const formatDate = (date) => {
    return new Date(date).toISOString().split('T')[0]
}

const viewDetails = (idea) => {
    router.get(route('hackathon-admin.ideas.show', idea.id))
}

const editIdea = (idea) => {
    router.get(route('hackathon-admin.ideas.review', idea.id))
}
</script>

<template>
    <Head title="Ideas Management - Hackathon Admin" />

    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <PageHeader 
                title="Ideas Management"
                subtitle="Review and manage ideas for current hackathon edition"
            />

            <!-- Statistics Cards -->
            <div v-if="statistics" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ statistics.total || 0 }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Draft</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-600 dark:text-gray-300">{{ statistics.draft || 0 }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Submitted</div>
                    <div class="mt-1 text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ statistics.submitted || 0 }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Under Review</div>
                    <div class="mt-1 text-2xl font-semibold text-amber-600 dark:text-amber-400">{{ statistics.under_review || 0 }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Accepted</div>
                    <div class="mt-1 text-2xl font-semibold text-emerald-600 dark:text-emerald-400">{{ statistics.accepted || 0 }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rejected</div>
                    <div class="mt-1 text-2xl font-semibold text-red-600 dark:text-red-400">{{ statistics.rejected || 0 }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Needs Revision</div>
                    <div class="mt-1 text-2xl font-semibold text-orange-600 dark:text-orange-400">{{ statistics.needs_revision || 0 }}</div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search Bar -->
                    <div class="md:col-span-2">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                            </div>
                            <input
                                v-model="searchForm.search"
                                type="text"
                                placeholder="Search ideas by title, team, or description..."
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                            />
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <select v-model="searchForm.status"
                                @change="filterIdeas"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">All Statuses</option>
                            <option value="draft">Draft</option>
                            <option value="submitted">Submitted</option>
                            <option value="under_review">Under Review</option>
                            <option value="accepted">Accepted</option>
                            <option value="rejected">Rejected</option>
                            <option value="needs_revision">Needs Revision</option>
                        </select>
                    </div>

                    <!-- Track Filter -->
                    <div>
                        <select v-model="searchForm.track_id"
                                @change="filterIdeas"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">All Tracks</option>
                            <option v-for="track in tracks" :key="track.id" :value="track.id">
                                {{ track.name }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Ideas Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Title
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Team
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Submission Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Track
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Score
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="idea in ideas.data" :key="idea.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ idea.title }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ idea.team?.name || 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ formatDate(idea.created_at) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        {{ idea.track?.name || 'Unassigned' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="statusColors[idea.status] || statusColors.draft"
                                          class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border">
                                        {{ statusDisplayNames[idea.status] || idea.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        <span v-if="idea.score" class="font-semibold text-emerald-600 dark:text-emerald-400">
                                            {{ idea.score }}/100
                                        </span>
                                        <span v-else class="text-gray-400">-</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button @click="viewDetails(idea)" 
                                            class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 mr-3">
                                        View
                                    </button>
                                    <button @click="editIdea(idea)"
                                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                                        Review
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Empty State -->
                            <tr v-if="!ideas.data || ideas.data.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="text-gray-500 dark:text-gray-400">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="mt-2 text-sm">No ideas found</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="ideas.links && ideas.links.length > 3" class="mt-6 flex items-center justify-between">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Showing {{ ideas.from }} to {{ ideas.to }} of {{ ideas.total }} results
                </div>
                <div class="flex space-x-2">
                    <template v-for="link in ideas.links" :key="link.label">
                        <button
                            v-if="link.url"
                            @click="router.get(link.url)"
                            :disabled="!link.url"
                            v-html="link.label"
                            :class="[
                                'px-3 py-1 text-sm rounded-lg transition-colors',
                                link.active 
                                    ? 'bg-emerald-600 text-white hover:bg-emerald-700' 
                                    : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600'
                            ]"
                        />
                    </template>
                </div>
            </div>
        </div>
    </Default>
</template>
