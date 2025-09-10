<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import Default from '@/Layouts/Default.vue'
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

// Status badge colors matching Figma design
const statusColors = {
    pending_review: 'bg-amber-50 text-amber-700 border-amber-200',
    approved: 'bg-emerald-50 text-emerald-700 border-emerald-200',
    rejected: 'bg-red-50 text-red-700 border-red-200',
    in_progress: 'bg-blue-50 text-blue-700 border-blue-200',
    completed: 'bg-gray-50 text-gray-700 border-gray-200',
    // Legacy statuses mapping
    draft: 'bg-gray-50 text-gray-700 border-gray-200',
    submitted: 'bg-amber-50 text-amber-700 border-amber-200',
    under_review: 'bg-amber-50 text-amber-700 border-amber-200',
    accepted: 'bg-emerald-50 text-emerald-700 border-emerald-200',
    needs_revision: 'bg-orange-50 text-orange-700 border-orange-200',
}

// Display names for statuses
const statusDisplayNames = {
    pending_review: 'Pending Review',
    approved: 'Approved',
    rejected: 'Rejected',
    in_progress: 'In Progress',
    completed: 'Completed',
    // Legacy statuses
    draft: 'Draft',
    submitted: 'Pending Review',
    under_review: 'Pending Review',
    accepted: 'Approved',
    needs_revision: 'Needs Edit',
}

const filterIdeas = () => {
    router.get(route('system-admin.ideas.index'), {
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
    return new Date(date).toISOString().split('T')[0] // Returns YYYY-MM-DD format
}

const viewDetails = (idea) => {
    router.get(route('system-admin.ideas.show', idea.id))
}

const editIdea = (idea) => {
    router.get(route('system-admin.ideas.review', idea.id))
}
</script>

<template>
    <Head title="Submitted Ideas" />

    <Default>
        <div class="max-w-[1200px] mx-auto px-4">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-[32px] font-bold text-gray-900 dark:text-gray-100 mb-2">
                    Submitted Ideas
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Browse and manage all submitted ideas from various teams.
                </p>
            </div>

            <!-- Search Bar -->
            <div class="mb-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                    </div>
                    <input
                        v-model="searchForm.search"
                        type="text"
                        placeholder="Search ideas by title, team, or track"
                        class="block w-full pl-10 pr-3 py-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent sm:text-base"
                    />
                </div>
            </div>

            <!-- Ideas Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-white dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Title
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Team
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Submission Date
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Track
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Status
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="idea in ideas.data" :key="idea.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-4 py-5 text-sm text-gray-900 dark:text-gray-100">
                                    <div class="max-w-[200px]">
                                        <p class="font-medium line-clamp-2">{{ idea.title }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-5 text-sm text-gray-600 dark:text-gray-400">
                                    <div class="max-w-[150px]">
                                        <p class="line-clamp-2">{{ idea.team?.name || 'N/A' }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-5 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                    {{ formatDate(idea.created_at) }}
                                </td>
                                <td class="px-4 py-5 text-sm">
                                    <div class="inline-flex items-center justify-center px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-gray-700">
                                        <span class="text-gray-700 dark:text-gray-300 font-medium">
                                            {{ idea.track?.name || 'Unassigned' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-5 text-sm">
                                    <div class="inline-flex items-center justify-center px-3 py-1.5 rounded-xl border"
                                         :class="statusColors[idea.status] || statusColors.pending_review">
                                        <span class="font-medium">
                                            {{ statusDisplayNames[idea.status] || idea.status }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-5 text-sm">
                                    <button @click="viewDetails(idea)" 
                                            class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-bold transition-colors">
                                        View Details
                                    </button>
                                    <span class="mx-1 text-gray-400">/</span>
                                    <button @click="editIdea(idea)"
                                            class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-bold transition-colors">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Empty State -->
                            <tr v-if="!ideas.data || ideas.data.length === 0">
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No ideas found
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
                                    ? 'bg-emerald-600 text-white' 
                                    : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600'
                            ]"
                        />
                    </template>
                </div>
            </div>
        </div>
    </Default>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
