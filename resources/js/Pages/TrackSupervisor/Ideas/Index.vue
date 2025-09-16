<script setup>
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL, direction, locale } = useLocalization()
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import Default from '@/Layouts/Default.vue'
import {
    MagnifyingGlassIcon,
    EyeIcon,
    PencilSquareIcon
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

// Status badge colors using design system colors
const statusColors = {
    pending_review: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
    approved: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400',
    accepted: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400',
    rejected: 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400',
    in_progress: 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
    completed: 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400',
    draft: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
    submitted: 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
    under_review: 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400',
    needs_revision: 'bg-orange-100 text-orange-700 dark:bg-orange-900/20 dark:text-orange-400',
}

// Display names for statuses
const statusDisplayNames = {
    pending_review: t('admin.ideas.pending'),
    approved: t('admin.ideas.approved'),
    rejected: t('admin.ideas.rejected'),
    in_progress: t('admin.ideas.in_progress', 'In Progress'),
    completed: t('admin.ideas.completed', 'Completed'),
    draft: t('admin.ideas.draft', 'Draft'),
    submitted: t('admin.ideas.submitted', 'Submitted'),
    under_review: t('admin.ideas.under_review', 'Under Review'),
    accepted: t('admin.ideas.accepted', 'Accepted'),
    needs_revision: t('admin.ideas.needs_revision', 'Needs Revision'),
}

const filterIdeas = () => {
    router.get(route('track-supervisor.ideas.index'), {
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
    router.get(route('track-supervisor.ideas.show', idea.id))
}

const editIdea = (idea) => {
    router.get(route('track-supervisor.ideas.review', idea.id))
}

const deleteIdea = (idea) => {
    if (confirm(t('admin.actions.confirm_delete'))) {
        router.delete(route('track-supervisor.ideas.destroy', idea.id))
    }
}
</script>

<template>
    <Head :title="t('admin.ideas.title')" />

    <Default>
        <div class="flex-1 flex flex-col">
            <div class="flex-1 p-6 overflow-auto">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ t('admin.ideas.submitted_ideas', 'Submitted Ideas') }}
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        {{ t('admin.ideas.browse_manage', 'Browse and manage all submitted ideas from various teams.') }}
                    </p>
                </div>

                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="flex items-center gap-4">
                        <div class="flex-1 max-w-2xl">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                    <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                                </div>
                                <input
                                    v-model="searchForm.search"
                                    type="text"
                                    :placeholder="t('admin.ideas.search_placeholder', 'Search ideas by title, team, or track')"
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                />
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Ideas Table -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-white dark:bg-gray-800">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Title
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Team
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Submission Date
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Track
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="idea in ideas.data" :key="idea.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ idea.title }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ idea.team?.name || 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ formatDate(idea.created_at) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="inline-flex items-center justify-center px-4 py-1.5 text-sm font-medium rounded-xl bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                            {{ idea.track?.name || 'Unassigned' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div :class="statusColors[idea.status] || statusColors.draft"
                                              class="inline-flex items-center justify-center px-4 py-1.5 text-sm font-medium rounded-xl">
                                            {{ statusDisplayNames[idea.status] || idea.status }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <button @click="viewDetails(idea)"
                                                class="text-sm font-bold text-gray-500 hover:text-emerald-600 dark:text-gray-400 dark:hover:text-emerald-400 transition-colors">
                                            View Details / Edit
                                        </button>
                                    </td>
                                </tr>

                                <!-- Empty State -->
                                <tr v-if="!ideas.data || ideas.data.length === 0">
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="text-gray-500 dark:text-gray-400">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="mt-2 text-sm">{{ t('admin.ideas.no_ideas_found', 'No ideas found') }}</p>
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
        </div>
    </Default>
</template>

<style scoped>
.theme-focus:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}

input[type="checkbox"]:checked {
    background-color: var(--theme-primary) !important;
    border-color: var(--theme-primary) !important;
}

select:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>
