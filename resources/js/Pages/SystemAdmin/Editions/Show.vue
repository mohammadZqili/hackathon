<script setup>
import { Head, router } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { computed } from 'vue'

const props = defineProps({
    edition: Object,
    statistics: {
        type: Object,
        default: () => ({
            total_teams: 0,
            total_ideas: 0,
            total_workshops: 0,
            total_news: 0,
            total_participants: 0,
            registration_open: false,
            idea_submission_open: false
        })
    }
})

const formatDate = (date) => {
    if (!date) return 'N/A'
    return new Date(date).toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    })
}

const statusColor = computed(() => {
    switch (props.edition?.status) {
        case 'active':
            return 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
        case 'draft':
            return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100'
        case 'completed':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100'
        case 'archived':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100'
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100'
    }
})
</script>

<template>
    <Head title="Edition Details" />
    
    <Default>
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-6xl mx-auto">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ edition.name }}</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            Year {{ edition.year }} {{ edition.theme ? `- ${edition.theme}` : '' }}
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <a :href="route('system-admin.editions.index')"
                           class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                            Back to List
                        </a>
                        <a :href="route('system-admin.editions.edit', edition.id)"
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Edit Edition
                        </a>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Teams</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics.total_teams || 0 }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Ideas</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics.total_ideas || 0 }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Workshops</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics.total_workshops || 0 }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="text-sm text-gray-500 dark:text-gray-400">News</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics.total_news || 0 }}</div>
                    </div>
                </div>

                <!-- Edition Details -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Edition Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Status and Current -->
                        <div>
                            <label class="text-sm text-gray-500 dark:text-gray-400">Status</label>
                            <div class="mt-1 flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full text-sm" :class="statusColor">
                                    {{ edition.status ? edition.status.charAt(0).toUpperCase() + edition.status.slice(1) : 'Unknown' }}
                                </span>
                                <span v-if="edition.is_current" 
                                      class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                    Current Edition
                                </span>
                            </div>
                        </div>

                        <!-- Location -->
                        <div v-if="edition.location">
                            <label class="text-sm text-gray-500 dark:text-gray-400">Location</label>
                            <p class="text-lg text-gray-900 dark:text-white">{{ edition.location }}</p>
                        </div>

                        <!-- Event Duration -->
                        <div>
                            <label class="text-sm text-gray-500 dark:text-gray-400">Event Duration</label>
                            <p class="text-lg text-gray-900 dark:text-white">
                                {{ formatDate(edition.event_start_date) }} - {{ formatDate(edition.event_end_date) }}
                            </p>
                        </div>

                        <!-- Registration Period -->
                        <div>
                            <label class="text-sm text-gray-500 dark:text-gray-400">Registration Period</label>
                            <p class="text-lg text-gray-900 dark:text-white">
                                {{ formatDate(edition.registration_start_date) }} - {{ formatDate(edition.registration_end_date) }}
                            </p>
                            <p v-if="statistics.registration_open" class="text-sm text-green-600 dark:text-green-400 mt-1">
                                ✓ Registration is currently open
                            </p>
                        </div>

                        <!-- Idea Submission Period -->
                        <div>
                            <label class="text-sm text-gray-500 dark:text-gray-400">Idea Submission Period</label>
                            <p class="text-lg text-gray-900 dark:text-white">
                                {{ formatDate(edition.idea_submission_start_date) }} - {{ formatDate(edition.idea_submission_end_date) }}
                            </p>
                            <p v-if="statistics.idea_submission_open" class="text-sm text-green-600 dark:text-green-400 mt-1">
                                ✓ Idea submission is currently open
                            </p>
                        </div>

                        <!-- Created By -->
                        <div v-if="edition.creator">
                            <label class="text-sm text-gray-500 dark:text-gray-400">Created By</label>
                            <p class="text-lg text-gray-900 dark:text-white">{{ edition.creator.name }}</p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div v-if="edition.description" class="mt-6">
                        <label class="text-sm text-gray-500 dark:text-gray-400">Description</label>
                        <p class="text-gray-900 dark:text-white mt-2 whitespace-pre-wrap">{{ edition.description }}</p>
                    </div>
                </div>

                <!-- Workshops Section -->
                <div v-if="edition.workshops && edition.workshops.length > 0" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Associated Workshops</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Date & Time
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Speaker
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Location
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="workshop in edition.workshops" :key="workshop.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ workshop.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ formatDate(workshop.date) }} {{ workshop.time }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ workshop.speaker || 'TBD' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ workshop.location || 'TBD' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- News Section -->
                <div v-if="edition.news && edition.news.length > 0" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Related News</h2>
                    <div class="space-y-4">
                        <div v-for="newsItem in edition.news" :key="newsItem.id" 
                             class="border-l-4 border-blue-500 pl-4 py-2">
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ newsItem.title }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ formatDate(newsItem.created_at) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex justify-end gap-3">
                    <button v-if="!edition.is_current && edition.status === 'active'"
                            @click="setCurrent"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        Set as Current
                    </button>
                    <button v-if="edition.status !== 'archived'"
                            @click="archiveEdition"
                            class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                        Archive Edition
                    </button>
                    <button @click="deleteEdition"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Delete Edition
                    </button>
                </div>
            </div>
        </div>
    </Default>
</template>

<script>
export default {
    methods: {
        setCurrent() {
            if (confirm('Are you sure you want to set this edition as current?')) {
                router.post(route('system-admin.editions.set-current', this.edition.id))
            }
        },
        archiveEdition() {
            if (confirm('Are you sure you want to archive this edition?')) {
                router.post(route('system-admin.editions.archive', this.edition.id))
            }
        },
        deleteEdition() {
            if (confirm('Are you sure you want to delete this edition? This action cannot be undone.')) {
                router.delete(route('system-admin.editions.destroy', this.edition.id))
            }
        }
    }
}
</script>