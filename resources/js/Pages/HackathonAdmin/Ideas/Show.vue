<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Default from '../../../Layouts/Default.vue'
import { 
    ArrowLeftIcon,
    DocumentTextIcon,
    UserGroupIcon,
    StarIcon,
    ClockIcon,
    PencilIcon,
    UserPlusIcon,
    PaperClipIcon,
    ChatBubbleLeftRightIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    idea: Object,
    reviewHistory: Array,
    scoring: Object,
})

const statusColors = {
    draft: 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
    submitted: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
    under_review: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    accepted: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    needs_revision: 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
}

const scoringCriteria = [
    { key: 'innovation', label: 'Innovation & Creativity', max: 25 },
    { key: 'feasibility', label: 'Technical Feasibility', max: 25 },
    { key: 'impact', label: 'Potential Impact', max: 25 },
    { key: 'presentation', label: 'Presentation Quality', max: 25 },
]

const getScoreColor = (score, max) => {
    const percentage = (score / max) * 100
    if (percentage >= 80) return 'bg-green-500'
    if (percentage >= 60) return 'bg-yellow-500'
    return 'bg-red-500'
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>

<template>
    <Head :title="`Idea: ${idea.title}`" />

    <Default>
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a :href="route('hackathon-admin.ideas.index')"
                           class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                            <ArrowLeftIcon class="w-5 h-5" />
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ idea.title }}</h1>
                            <div class="mt-2 flex items-center space-x-4">
                                <span :class="[statusColors[idea.status], 'px-3 py-1 text-sm font-medium rounded-full']">
                                    {{ idea.status }}
                                </span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    Submitted {{ formatDate(idea.created_at) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <a :href="route('hackathon-admin.ideas.review', idea.id)"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            <PencilIcon class="w-5 h-5 mr-2" />
                            Review Idea
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Description -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Description</h2>
                        <div class="prose dark:prose-invert max-w-none">
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ idea.description }}</p>
                        </div>
                    </div>

                    <!-- Files -->
                    <div v-if="idea.files && idea.files.length > 0" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <PaperClipIcon class="w-5 h-5 mr-2" />
                            Attached Files ({{ idea.files.length }})
                        </h2>
                        <div class="space-y-2">
                            <div v-for="file in idea.files" :key="file.id" 
                                 class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center">
                                    <DocumentTextIcon class="w-5 h-5 text-gray-400 mr-3" />
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ file.filename }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ file.size_formatted }}</p>
                                    </div>
                                </div>
                                <a :href="file.download_url" 
                                   class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Scoring -->
                    <div v-if="scoring" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <StarIcon class="w-5 h-5 mr-2" />
                            Scoring Breakdown
                        </h2>
                        <div class="space-y-4">
                            <div v-for="criterion in scoringCriteria" :key="criterion.key">
                                <div class="flex justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ criterion.label }}
                                    </span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ scoring[criterion.key] || 0 }} / {{ criterion.max }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div :class="getScoreColor(scoring[criterion.key] || 0, criterion.max)"
                                         :style="`width: ${((scoring[criterion.key] || 0) / criterion.max) * 100}%`"
                                         class="h-2 rounded-full"></div>
                                </div>
                            </div>
                            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex justify-between">
                                    <span class="text-lg font-semibold text-gray-900 dark:text-white">Total Score</span>
                                    <span class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                        {{ idea.score || 0 }} / 100
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Review History -->
                    <div v-if="reviewHistory && reviewHistory.length > 0" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <ChatBubbleLeftRightIcon class="w-5 h-5 mr-2" />
                            Review History
                        </h2>
                        <div class="space-y-4">
                            <div v-for="review in reviewHistory" :key="review.id" 
                                 class="border-l-4 border-gray-200 dark:border-gray-700 pl-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-2">
                                    <span class="font-medium text-gray-900 dark:text-white">
                                    {{ review.user?.name || 'System' }}
                                    </span>
                                    <span :class="[statusColors[review.new_value] || 'bg-gray-100 text-gray-800', 'px-2 py-0.5 text-xs font-medium rounded-full']">
                                    {{ review.new_value || review.action }}
                                    </span>
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ formatDate(review.created_at) }}
                                    </span>
                                </div>
                                <p v-if="review.notes" class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ review.notes }}
                                </p>
                                <div v-if="review.metadata && review.metadata.scores" class="mt-2 flex space-x-4 text-xs">
                                    <span v-for="(score, key) in review.metadata.scores" :key="key" class="text-gray-500 dark:text-gray-400">
                                        {{ key }}: {{ score }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Team Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <UserGroupIcon class="w-5 h-5 mr-2" />
                            Team Information
                        </h2>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Team Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ idea.team?.name || 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Track</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ idea.track?.name || 'Not Assigned' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Team Leader</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ idea.team?.leader?.name || 'N/A' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Members</dt>
                                <dd class="mt-1">
                                    <ul class="text-sm text-gray-900 dark:text-white space-y-1">
                                        <li v-for="member in idea.team?.members" :key="member.id">
                                            {{ member.name }}
                                        </li>
                                    </ul>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Review Details -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Review Details</h2>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Supervisor</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ idea.supervisor?.name || 'Not Assigned' }}
                                </dd>
                            </div>
                            <div v-if="idea.reviewed_at">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Reviewed</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ formatDate(idea.reviewed_at) }}
                                </dd>
                            </div>
                            <div v-if="idea.reviewed_by">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Reviewed By</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ idea.reviewer?.name }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
                        <div class="space-y-2">
                            <a :href="route('hackathon-admin.ideas.review', idea.id)"
                               class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                <PencilIcon class="w-5 h-5 mr-2" />
                                Review This Idea
                            </a>
                            <button v-if="!idea.supervisor"
                                    @click="router.visit(route('hackathon-admin.ideas.review', idea.id))"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors">
                                <UserPlusIcon class="w-5 h-5 mr-2" />
                                Assign Supervisor
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>