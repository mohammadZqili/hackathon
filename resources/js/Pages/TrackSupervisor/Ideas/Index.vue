<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, computed, watch, onMounted } from 'vue'
import Default from '@/Layouts/Default.vue'
import PageHeader from '@/Components/Shared/PageHeader.vue'
import SearchBar from '@/Components/Shared/SearchBar.vue'
import DataTable from '@/Components/Shared/DataTable.vue'

const props = defineProps({
    ideas: Object,
    statistics: Object,
    supervisedTracks: Array,
    filters: Object,
    permissions: Object
})

const searchForm = useForm({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
    track_id: props.filters?.track_id || '',
    priority: props.filters?.priority || '',
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

// Status badge colors for review status
const statusColors = {
    pending_review: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
    under_review: 'bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-400',
    reviewed: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400',
    needs_revision: 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
    approved: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    submitted: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
    draft: 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
}

// Display names for statuses
const statusDisplayNames = {
    pending_review: 'Pending Review',
    under_review: 'Under Review',
    reviewed: 'Reviewed',
    needs_revision: 'Needs Revision',
    approved: 'Approved',
    rejected: 'Rejected',
    submitted: 'Submitted',
    draft: 'Draft'
}

// Table configuration
const columns = [
    {
        key: 'title',
        label: 'Idea Title',
        width: 'w-[250px]'
    },
    {
        key: 'team.name',
        label: 'Team',
        width: 'w-[150px]',
        defaultValue: 'No Team'
    },
    {
        key: 'track.name',
        label: 'Track',
        width: 'w-[130px]',
        defaultValue: 'No Track'
    },
    {
        key: 'submitted_at',
        label: 'Submitted',
        width: 'w-[100px]',
        formatter: (item) => item.submitted_at ? formatDate(item.submitted_at) : 'Draft'
    },
    {
        key: 'status',
        label: 'Status',
        width: 'w-[120px]'
    },
    {
        key: 'my_score',
        label: 'My Score',
        width: 'w-[90px]'
    },
    {
        key: 'actions',
        label: 'Actions',
        width: 'w-[180px]'
    }
]

const handleSearch = () => {
    clearTimeout(window.searchTimeout)
    window.searchTimeout = setTimeout(filterIdeas, 300)
}

const filterIdeas = () => {
    router.get(route('track-supervisor.ideas.index'), {
        search: searchForm.search,
        status: searchForm.status,
        track_id: searchForm.track_id,
        priority: searchForm.priority,
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}

watch(() => searchForm.status, filterIdeas)
watch(() => searchForm.track_id, filterIdeas)
watch(() => searchForm.priority, filterIdeas)

const formatDate = (date) => {
    return date ? new Date(date).toLocaleDateString() : ''
}

const getStatusBadgeClass = (status) => {
    return statusColors[status] || statusColors.pending_review
}

const viewIdea = (idea) => {
    router.visit(route('track-supervisor.ideas.show', idea.id))
}

const reviewIdea = (idea) => {
    router.visit(route('track-supervisor.ideas.review', idea.id))
}
</script>

<template>
    <Head title="Ideas Review - Track Supervisor" />

    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <PageHeader 
                title="Ideas Review"
                subtitle="Review and evaluate ideas in your assigned tracks"
            />

            <!-- Supervised Tracks Info -->
            <div v-if="supervisedTracks && supervisedTracks.length > 0" class="px-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Your Supervised Tracks</h3>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="track in supervisedTracks" :key="track.id" 
                              class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                              :style="{ 
                                  backgroundColor: themeColor.primary + '20', 
                                  color: themeColor.primary 
                              }">
                            {{ track.name }}
                            <span class="ml-2 text-xs bg-white dark:bg-gray-700 px-2 py-1 rounded-full">
                                {{ track.ideas_count || 0 }} ideas
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div v-if="statistics" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6 px-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Ideas</p>
                            <p class="text-2xl font-bold" :style="{ color: themeColor.primary }">{{ statistics.total || 0 }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center" 
                             :style="{ backgroundColor: themeColor.primary + '20' }">
                            <svg class="w-6 h-6" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Review</p>
                            <p class="text-2xl font-bold text-blue-600">{{ statistics.pending || statistics.pending_review || 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Reviewed</p>
                            <p class="text-2xl font-bold text-green-600">{{ statistics.reviewed || 0 }}</p>
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
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Need Revision</p>
                            <p class="text-2xl font-bold text-orange-600">{{ statistics.needs_revision || 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Review Status</label>
                        <select v-model="searchForm.status" 
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-primary focus:ring-primary">
                            <option value="">All Statuses</option>
                            <option value="pending_review">Pending Review</option>
                            <option value="under_review">Under Review</option>
                            <option value="reviewed">Reviewed</option>
                            <option value="needs_revision">Needs Revision</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <!-- Track Filter -->
                    <div v-if="supervisedTracks && supervisedTracks.length > 1" class="min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Track</label>
                        <select v-model="searchForm.track_id"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-primary focus:ring-primary">
                            <option value="">All My Tracks</option>
                            <option v-for="track in supervisedTracks" :key="track.id" :value="track.id">
                                {{ track.name }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
            <SearchBar 
                v-model="searchForm.search"
                placeholder="Search ideas by title, description or team name"
                @update:model-value="handleSearch"
            />

            <!-- Ideas Table -->
            <DataTable
                :data="ideas?.data || []"
                :columns="columns"
                empty-message="No ideas found in your supervised tracks."
            >
                <!-- Status Column -->
                <template #status="{ item }">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="getStatusBadgeClass(item.review_status || item.status)">
                        {{ statusDisplayNames[item.review_status || item.status] || item.status }}
                    </span>
                </template>

                <!-- My Score Column -->
                <template #my_score="{ item }">
                    <div v-if="item.my_review">
                        <span class="text-sm font-medium" :style="{ color: themeColor.primary }">
                            {{ item.my_review.total_score }}/100
                        </span>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ formatDate(item.my_review.reviewed_at) }}
                        </div>
                    </div>
                    <span v-else class="text-sm text-gray-500 dark:text-gray-400">
                        Not Reviewed
                    </span>
                </template>

                <!-- Actions Column -->
                <template #actions="{ item }">
                    <div class="flex items-center gap-2">
                        <button @click="viewIdea(item)"
                                class="font-bold hover:underline transition-colors text-sm"
                                :style="{ color: themeColor.primary }">
                            View
                        </button>
                        
                        <span :style="{ color: themeColor.primary }">|</span>
                        <button @click="reviewIdea(item)"
                                class="font-bold hover:underline transition-colors text-sm"
                                :style="{ color: themeColor.primary }">
                            {{ item.my_review ? 'Edit Review' : 'Review' }}
                        </button>
                    </div>
                </template>
            </DataTable>

            <!-- Pagination -->
            <div v-if="ideas?.links" class="px-4 py-3">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Showing {{ ideas.from }} to {{ ideas.to }} of {{ ideas.total }} results
                    </div>
                    <div class="flex space-x-1">
                        <template v-for="(link, index) in ideas.links" :key="index">
                            <button v-if="link.url && link.label !== '&laquo; Previous' && link.label !== 'Next &raquo;'"
                                    @click="router.visit(link.url)"
                                    class="px-3 py-2 text-sm font-medium rounded-md transition-colors"
                                    :class="link.active 
                                        ? 'text-white shadow-sm' 
                                        : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                    :style="link.active ? {
                                        background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                                    } : {}">
                                {{ link.label }}
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

<style scoped>
input[type="text"]:focus,
select:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>