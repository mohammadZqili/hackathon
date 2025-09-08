<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import Default from '../../../Layouts/Default.vue'
import { 
    LightBulbIcon, 
    MagnifyingGlassIcon, 
    FunnelIcon,
    EyeIcon, 
    PencilIcon, 
    UserPlusIcon,
    ArrowDownTrayIcon,
    CheckCircleIcon,
    XCircleIcon,
    ClockIcon 
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

const statusColors = {
    draft: 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
    submitted: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
    under_review: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    accepted: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    needs_revision: 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
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

const exportIdeas = () => {
    window.location.href = route('hackathon-admin.ideas.export')
}

watch(() => searchForm.search, () => {
    clearTimeout(window.searchTimeout)
    window.searchTimeout = setTimeout(filterIdeas, 300)
})

const getScoreColor = (score) => {
    if (!score) return 'text-gray-500'
    if (score >= 80) return 'text-green-600 dark:text-green-400'
    if (score >= 60) return 'text-yellow-600 dark:text-yellow-400'
    return 'text-red-600 dark:text-red-400'
}
</script>

<template>
    <Head title="Ideas Management" />

    <Default>
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Ideas Management</h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Review and manage hackathon ideas
                        </p>
                    </div>
                    <button @click="exportIdeas"
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                        <ArrowDownTrayIcon class="w-5 h-5 mr-2" />
                        Export Ideas
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div v-if="statistics" class="grid grid-cols-1 md:grid-cols-7 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ statistics.total }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Draft</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-600 dark:text-gray-400">{{ statistics.draft }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Submitted</div>
                    <div class="mt-1 text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ statistics.submitted }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Under Review</div>
                    <div class="mt-1 text-2xl font-semibold text-yellow-600 dark:text-yellow-400">{{ statistics.under_review }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Accepted</div>
                    <div class="mt-1 text-2xl font-semibold text-green-600 dark:text-green-400">{{ statistics.accepted }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Rejected</div>
                    <div class="mt-1 text-2xl font-semibold text-red-600 dark:text-red-400">{{ statistics.rejected }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Needs Revision</div>
                    <div class="mt-1 text-2xl font-semibold text-orange-600 dark:text-orange-400">{{ statistics.needs_revision }}</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <!-- Search -->
                        <div class="relative">
                            <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                            <input
                                v-model="searchForm.search"
                                type="text"
                                placeholder="Search ideas..."
                                class="pl-10 pr-3 py-2 w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <!-- Status Filter -->
                        <select
                            v-model="searchForm.status"
                            @change="filterIdeas"
                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All Status</option>
                            <option value="draft">Draft</option>
                            <option value="submitted">Submitted</option>
                            <option value="under_review">Under Review</option>
                            <option value="accepted">Accepted</option>
                            <option value="rejected">Rejected</option>
                            <option value="needs_revision">Needs Revision</option>
                        </select>

                        <!-- Track Filter -->
                        <select
                            v-model="searchForm.track_id"
                            @change="filterIdeas"
                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All Tracks</option>
                            <option v-for="track in tracks" :key="track.id" :value="track.id">
                                {{ track.name }}
                            </option>
                        </select>

                        <!-- Supervisor Filter -->
                        <select
                            v-model="searchForm.has_supervisor"
                            @change="filterIdeas"
                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All Ideas</option>
                            <option value="yes">Has Supervisor</option>
                            <option value="no">No Supervisor</option>
                        </select>

                        <!-- Clear Filters -->
                        <button
                            @click="searchForm.reset(); filterIdeas()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors"
                        >
                            Clear Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Ideas Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Idea
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Team
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Track
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Score
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Supervisor
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="idea in ideas.data" :key="idea.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ idea.title }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            Submitted: {{ new Date(idea.created_at).toLocaleDateString() }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ idea.team?.name || 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ idea.track?.name || 'Not Assigned' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="[statusColors[idea.status], 'px-2 py-1 text-xs font-medium rounded-full']">
                                        {{ idea.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="idea.score" :class="getScoreColor(idea.score)" class="text-sm font-semibold">
                                        {{ idea.score }}/100
                                    </span>
                                    <span v-else class="text-sm text-gray-400">
                                        Not Scored
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div v-if="idea.supervisor" class="text-sm text-gray-900 dark:text-white">
                                        {{ idea.supervisor.name }}
                                    </div>
                                    <span v-else class="text-sm text-gray-400">
                                        Not Assigned
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a :href="route('hackathon-admin.ideas.show', idea.id)"
                                           class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            <EyeIcon class="w-5 h-5" />
                                        </a>
                                        <a :href="route('hackathon-admin.ideas.review', idea.id)"
                                           class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            <PencilIcon class="w-5 h-5" />
                                        </a>
                                        <button v-if="!idea.supervisor"
                                                @click="$inertia.visit(route('hackathon-admin.ideas.review', idea.id))"
                                                class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300"
                                                title="Assign Supervisor">
                                            <UserPlusIcon class="w-5 h-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="ideas.links && ideas.links.length > 3" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            Showing {{ ideas.from }} to {{ ideas.to }} of {{ ideas.total }} results
                        </div>
                        <div class="flex space-x-2">
                            <template v-for="link in ideas.links" :key="link.label">
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
        </div>
    </Default>
</template>