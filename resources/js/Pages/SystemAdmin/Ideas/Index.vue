<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import Default from '../../../Layouts/Default.vue'

const props = defineProps({
    ideas: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    tracks: {
        type: Array,
        default: () => []
    },
    editions: {
        type: Array,
        default: () => []
    },
    stats: {
        type: Object,
        default: () => ({})
    }
})

// Get theme color from localStorage or default
const themeColor = ref({
    primary: '#0d9488',
    hover: '#0f766e',
    rgb: '13, 148, 136',
    gradientFrom: '#0d9488',
    gradientTo: '#14b8a6'
})

onMounted(() => {
    // Get the current theme color from CSS variables
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

// Computed style for dynamic theme
const themeStyles = computed(() => ({
    '--theme-primary': themeColor.value.primary,
    '--theme-hover': themeColor.value.hover,
    '--theme-rgb': themeColor.value.rgb,
    '--theme-gradient-from': themeColor.value.gradientFrom,
    '--theme-gradient-to': themeColor.value.gradientTo,
}))

// Search and filters
const searchForm = useForm({
    search: props.filters.search || '',
    status: props.filters.status || '',
    track: props.filters.track || '',
    edition: props.filters.edition || ''
})

const search = () => {
    searchForm.get(route('system-admin.ideas.index'), {
        preserveState: true,
        replace: true
    })
}

// Delete idea
const deleteIdea = (idea) => {
    if (confirm(`Are you sure you want to delete the idea "${idea.title}"? This action cannot be undone.`)) {
        useForm({}).delete(route('system-admin.ideas.destroy', idea.id), {
            onSuccess: () => {
                // Success message will be handled by flash messages
            }
        })
    }
}

// Bulk actions
const selectedIdeas = ref([])
const selectAll = ref(false)

const toggleSelectAll = () => {
    if (selectAll.value) {
        selectedIdeas.value = props.ideas.data.map(idea => idea.id)
    } else {
        selectedIdeas.value = []
    }
}

const toggleSelect = (ideaId) => {
    const index = selectedIdeas.value.indexOf(ideaId)
    if (index > -1) {
        selectedIdeas.value.splice(index, 1)
    } else {
        selectedIdeas.value.push(ideaId)
    }
    selectAll.value = selectedIdeas.value.length === props.ideas.data.length
}

// Get status color
const getStatusColor = (status) => {
    const colors = {
        'draft': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        'submitted': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300', 
        'under_review': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'needs_revision': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
        'accepted': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'rejected': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
    }
    return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
}

// Format status for display
const formatStatus = (status) => {
    const statusMap = {
        'draft': 'Draft',
        'submitted': 'Submitted',
        'under_review': 'Under Review',
        'needs_revision': 'Needs Revision',
        'accepted': 'Accepted',
        'rejected': 'Rejected'
    }
    return statusMap[status] || status
}

// Format date
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

// Get track name by ID
const getTrackName = (trackId) => {
    const track = props.tracks.find(t => t.id === trackId)
    return track ? track.name : 'Unknown Track'
}

// Get edition name by ID  
const getEditionName = (editionId) => {
    const edition = props.editions.find(e => e.id === editionId)
    return edition ? edition.name : 'Unknown Edition'
}

// Clear filters
const clearFilters = () => {
    searchForm.reset()
    search()
}
</script>

<template>
    <Head title="Ideas Management" />

    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Ideas Management</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        View and manage all submitted ideas across all hackathon editions
                    </p>
                </div>
                
                <div class="flex items-center space-x-3 mt-4 sm:mt-0">
                    <!-- Export Button -->
                    <Link :href="route('system-admin.ideas.export')"
                        class="inline-flex items-center px-4 py-2 rounded-lg font-semibold text-white transition-all duration-200 shadow-sm hover:shadow-md"
                        :style="{ 
                            background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                        }">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Ideas
                    </Link>
                </div>
            </div>

            <!-- Stats Cards -->
            <div v-if="stats" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Ideas</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total || ideas.total || 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Review</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.pending || 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Accepted</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.accepted || 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Rejected</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.rejected || 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Ideas</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input v-model="searchForm.search" @input="search" type="search" placeholder="Search by title, description, or team..." 
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[var(--theme-primary)] focus:border-transparent">
                        </div>
                    </div>
                    
                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select v-model="searchForm.status" @change="search" 
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[var(--theme-primary)] focus:border-transparent">
                            <option value="">All Statuses</option>
                            <option value="draft">Draft</option>
                            <option value="submitted">Submitted</option>
                            <option value="under_review">Under Review</option>
                            <option value="needs_revision">Needs Revision</option>
                            <option value="accepted">Accepted</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    
                    <!-- Track Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Track</label>
                        <select v-model="searchForm.track" @change="search"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[var(--theme-primary)] focus:border-transparent">
                            <option value="">All Tracks</option>
                            <option v-for="track in tracks" :key="track.id" :value="track.id">
                                {{ track.name }}
                            </option>
                        </select>
                    </div>
                    
                    <!-- Edition Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Edition</label>
                        <select v-model="searchForm.edition" @change="search"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[var(--theme-primary)] focus:border-transparent">
                            <option value="">All Editions</option>
                            <option v-for="edition in editions" :key="edition.id" :value="edition.id">
                                {{ edition.name }}
                            </option>
                        </select>
                    </div>
                    
                    <!-- Clear Filters -->
                    <div class="flex items-end">
                        <button @click="clearFilters" 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Clear Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Ideas Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <div v-if="ideas.data.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No ideas found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                </div>
                
                <div v-else>
                    <!-- Bulk Actions -->
                    <div v-if="selectedIdeas.length > 0" class="bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200 dark:border-blue-800 px-6 py-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-blue-700 dark:text-blue-300">
                                {{ selectedIdeas.length }} idea(s) selected
                            </span>
                            <div class="space-x-2">
                                <button @click="selectedIdeas = []" class="text-sm text-blue-600 hover:text-blue-800">
                                    Clear Selection
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <input type="checkbox" 
                                            v-model="selectAll"
                                            @change="toggleSelectAll"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Idea Details
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Team & Track
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status & Score
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Submitted
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="idea in ideas.data" :key="idea.id" 
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" 
                                            :value="idea.id"
                                            @change="toggleSelect(idea.id)"
                                            :checked="selectedIdeas.includes(idea.id)"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="max-w-xs">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                                                {{ idea.title || 'Untitled Idea' }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                                {{ idea.description ? idea.description.substring(0, 100) + '...' : 'No description provided' }}
                                            </div>
                                            <div v-if="idea.files && idea.files.length > 0" class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                                {{ idea.files.length }} file(s) attached
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white font-medium mb-1">
                                            {{ idea.team?.name || 'N/A' }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                                            Leader: {{ idea.team?.leader?.name || 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            Track: {{ idea.track?.name || 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ idea.team?.hackathon?.name || 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="mb-2">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                                :class="getStatusColor(idea.status)">
                                                {{ formatStatus(idea.status) }}
                                            </span>
                                        </div>
                                        <div v-if="idea.score" class="text-sm font-medium" :style="{ color: themeColor.primary }">
                                            Score: {{ idea.score }}/100
                                        </div>
                                        <div v-else class="text-sm text-gray-400">
                                            No score yet
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <div class="mb-1">
                                            {{ idea.submitted_at ? formatDate(idea.submitted_at) : formatDate(idea.created_at) }}
                                        </div>
                                        <div v-if="idea.reviewed_at" class="text-xs text-gray-400">
                                            Reviewed: {{ formatDate(idea.reviewed_at) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-3">
                                            <Link :href="route('system-admin.ideas.show', idea.id)"
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                                View
                                            </Link>
                                            <Link :href="route('system-admin.ideas.review', idea.id)"
                                                class="text-teal-600 hover:text-teal-900 dark:text-teal-400 dark:hover:text-teal-300 transition-colors">
                                                Review
                                            </Link>
                                            <button @click="deleteIdea(idea)"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="ideas.links" class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link v-if="ideas.prev_page_url" :href="ideas.prev_page_url"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    Previous
                                </Link>
                                <Link v-if="ideas.next_page_url" :href="ideas.next_page_url"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    Next
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        Showing <span class="font-medium">{{ ideas.from || 0 }}</span> to <span class="font-medium">{{ ideas.to || 0 }}</span> of <span class="font-medium">{{ ideas.total || 0 }}</span> results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                        <template v-for="link in ideas.links" :key="link.label">
                                            <Link v-if="link.url"
                                                :href="link.url"
                                                :class="[
                                                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors',
                                                    link.active
                                                        ? 'z-10 bg-blue-50 border-blue-500 text-blue-600 dark:bg-blue-900 dark:border-blue-400 dark:text-blue-300'
                                                        : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'
                                                ]"
                                                v-html="link.label">
                                            </Link>
                                            <span v-else
                                                :class="[
                                                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium cursor-default',
                                                    'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-300 dark:text-gray-500'
                                                ]"
                                                v-html="link.label">
                                            </span>
                                        </template>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

<style scoped>
/* Theme-aware input styling */
:deep(.peer:focus) {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 2px rgba(var(--theme-rgb), 0.2) !important;
}

:deep(.error) {
    border-color: #ef4444 !important;
}

.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}
</style>
