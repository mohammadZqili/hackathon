<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Default from '@/Layouts/Default.vue'
import { 
    PencilIcon,
    PaperAirplaneIcon,
    ArrowPathIcon,
    DocumentArrowDownIcon,
    CheckCircleIcon,
    XCircleIcon,
    ClockIcon,
    ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    idea: Object,
    team: Object,
    reviewHistory: Array,
    canEdit: Boolean,
    canSubmit: Boolean,
})

const activeTab = ref('details')

const statusColors = {
    draft: 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
    submitted: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
    under_review: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    accepted: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    needs_revision: 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
}

const statusIcons = {
    draft: null,
    submitted: ClockIcon,
    under_review: ClockIcon,
    accepted: CheckCircleIcon,
    rejected: XCircleIcon,
    needs_revision: ExclamationTriangleIcon,
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

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i]
}

const submitIdea = () => {
    if (!confirm('Are you sure you want to submit this idea for review? Once submitted, you cannot edit it unless requested by the reviewer.')) {
        return
    }
    
    router.post(route('team-leader.idea.submit'))
}

const withdrawIdea = () => {
    if (!confirm('Are you sure you want to withdraw this idea? You will be able to edit and resubmit it.')) {
        return
    }
    
    router.post(route('team-leader.idea.withdraw'))
}

const editIdea = () => {
    router.visit(route('team-leader.idea.edit'))
}

const downloadFile = (file) => {
    window.open(`/storage/${file.path}`, '_blank')
}
</script>

<template>
    <Head :title="`Idea: ${idea.title}`" />

    <Default>
        <div class="max-w-6xl mx-auto px-4 py-6">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ idea.title }}</h1>
                        <div class="mt-2 flex items-center space-x-4">
                            <span :class="[statusColors[idea.status], 'px-3 py-1 text-sm font-medium rounded-full flex items-center']">
                                <component v-if="statusIcons[idea.status]" :is="statusIcons[idea.status]" class="w-4 h-4 mr-1" />
                                {{ idea.status.replace('_', ' ').charAt(0).toUpperCase() + idea.status.slice(1).replace('_', ' ') }}
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                Track: {{ idea.track?.name || 'Unassigned' }}
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                Team: {{ team.name }}
                            </span>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <button v-if="canEdit"
                                @click="editIdea"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">
                            <PencilIcon class="w-5 h-5 mr-2" />
                            Edit Idea
                        </button>
                        <button v-if="canSubmit"
                                @click="submitIdea"
                                class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg">
                            <PaperAirplaneIcon class="w-5 h-5 mr-2" />
                            Submit for Review
                        </button>
                        <button v-if="idea.status === 'submitted' || idea.status === 'under_review'"
                                @click="withdrawIdea"
                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                            <ArrowPathIcon class="w-5 h-5 mr-2" />
                            Withdraw
                        </button>
                    </div>
                </div>
            </div>

            <!-- Feedback Alert (if rejected or needs revision) -->
            <div v-if="idea.status === 'rejected' || idea.status === 'needs_revision'" 
                 class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4">
                <div class="flex">
                    <ExclamationTriangleIcon class="h-5 w-5 text-red-400 mr-2 flex-shrink-0" />
                    <div>
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                            {{ idea.status === 'rejected' ? 'Idea Rejected' : 'Revision Required' }}
                        </h3>
                        <p v-if="idea.feedback" class="mt-1 text-sm text-red-700 dark:text-red-300">
                            {{ idea.feedback }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                <nav class="flex space-x-8">
                    <button @click="activeTab = 'details'"
                            :class="[
                                'py-2 px-1 border-b-2 font-medium text-sm',
                                activeTab === 'details' 
                                    ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' 
                                    : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'
                            ]">
                        Idea Details
                    </button>
                    <button @click="activeTab = 'files'"
                            :class="[
                                'py-2 px-1 border-b-2 font-medium text-sm',
                                activeTab === 'files' 
                                    ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' 
                                    : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'
                            ]">
                        Files ({{ idea.files?.length || 0 }})
                    </button>
                    <button @click="activeTab = 'history'"
                            :class="[
                                'py-2 px-1 border-b-2 font-medium text-sm',
                                activeTab === 'history' 
                                    ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' 
                                    : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'
                            ]">
                        Review History
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div v-show="activeTab === 'details'" class="space-y-6">
                <!-- Description -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Description</h2>
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ idea.description }}</p>
                </div>

                <!-- Problem Statement -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Problem Statement</h2>
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ idea.problem_statement }}</p>
                </div>

                <!-- Solution Approach -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Solution Approach</h2>
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ idea.solution_approach }}</p>
                </div>

                <!-- Expected Impact -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Expected Impact</h2>
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ idea.expected_impact }}</p>
                </div>

                <!-- Technologies -->
                <div v-if="idea.technologies && idea.technologies.length > 0" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Technologies Used</h2>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="tech in idea.technologies" :key="tech"
                              class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/20 text-emerald-800 dark:text-emerald-300 rounded-full text-sm">
                            {{ tech }}
                        </span>
                    </div>
                </div>

                <!-- Submission Info -->
                <div v-if="idea.submitted_at" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Submission Information</h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Submitted At</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatDate(idea.submitted_at) }}</dd>
                        </div>
                        <div v-if="idea.reviewed_at">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Reviewed At</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatDate(idea.reviewed_at) }}</dd>
                        </div>
                        <div v-if="idea.reviewer">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Reviewed By</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ idea.reviewer.name }}</dd>
                        </div>
                        <div v-if="idea.score">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Score</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ idea.score }} / 100</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Files Tab -->
            <div v-show="activeTab === 'files'" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Uploaded Files</h2>
                <div v-if="idea.files && idea.files.length > 0" class="space-y-3">
                    <div v-for="file in idea.files" :key="file.id"
                         @click="downloadFile(file)"
                         class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                        <div class="flex items-center space-x-3">
                            <DocumentArrowDownIcon class="w-6 h-6 text-gray-400" />
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ file.filename }}</p>
                                <p class="text-xs text-gray-500">{{ formatFileSize(file.size) }}</p>
                            </div>
                        </div>
                        <span class="text-sm text-emerald-600 hover:text-emerald-700">Download</span>
                    </div>
                </div>
                <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                    No files uploaded yet
                </div>
            </div>

            <!-- History Tab -->
            <div v-show="activeTab === 'history'" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Review History</h2>
                <div v-if="reviewHistory && reviewHistory.length > 0" class="space-y-4">
                    <div v-for="review in reviewHistory" :key="review.id" 
                         class="border-l-4 border-emerald-500 pl-4 py-2">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ review.reviewer_name }}
                                </span>
                                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                    {{ formatDate(review.created_at) }}
                                </span>
                            </div>
                            <span :class="[statusColors[review.status], 'px-2 py-1 text-xs font-medium rounded-full']">
                                {{ review.status }}
                            </span>
                        </div>
                        <p v-if="review.feedback" class="text-gray-700 dark:text-gray-300">{{ review.feedback }}</p>
                    </div>
                </div>
                <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                    No review history available
                </div>
            </div>
        </div>
    </Default>
</template>
