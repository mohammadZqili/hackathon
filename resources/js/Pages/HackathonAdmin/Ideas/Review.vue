<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Default from '../../../Layouts/Default.vue'
import { 
    ArrowLeftIcon,
    StarIcon,
    DocumentTextIcon,
    CheckCircleIcon,
    XCircleIcon,
    ExclamationTriangleIcon,
    ArrowPathIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    idea: Object,
    supervisors: Array,
    scoringCriteria: Object,
})

const form = useForm({
    status: props.idea.status || 'pending',
    supervisor_id: props.idea.supervisor_id || '',
    feedback: props.idea.feedback || '',
    scores: {
        innovation: props.idea.scoring_data?.innovation || 0,
        feasibility: props.idea.scoring_data?.feasibility || 0,
        impact: props.idea.scoring_data?.impact || 0,
        presentation: props.idea.scoring_data?.presentation || 0,
    },
    notify_team: true,
})

const totalScore = computed(() => {
    return Object.values(form.scores).reduce((sum, score) => sum + parseInt(score || 0), 0)
})

const submitReview = () => {
    form.post(route('hackathon-admin.ideas.process-review', props.idea.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Handle success
        },
    })
}

const criteriaDescriptions = {
    innovation: 'How creative and unique is the idea? Does it present a novel solution?',
    feasibility: 'Is the idea technically achievable within the hackathon timeframe?',
    impact: 'What is the potential positive impact of this solution?',
    presentation: 'How well is the idea presented and documented?',
}
</script>

<template>
    <Head :title="`Review: ${idea.title}`" />

    <Default>
        <div class="max-w-5xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center space-x-4">
                    <a :href="route('hackathon-admin.ideas.show', idea.id)"
                       class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        <ArrowLeftIcon class="w-5 h-5" />
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Review Idea</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ idea.title }}</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submitReview" class="space-y-6">
                <!-- Status Selection -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Review Status</h2>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                        <label class="relative">
                            <input type="radio" v-model="form.status" value="pending" class="sr-only peer" />
                            <div class="px-4 py-2 border-2 rounded-lg cursor-pointer text-center transition-all
                                        peer-checked:border-yellow-500 peer-checked:bg-yellow-50 dark:peer-checked:bg-yellow-900/20
                                        border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Pending</span>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" v-model="form.status" value="under_review" class="sr-only peer" />
                            <div class="px-4 py-2 border-2 rounded-lg cursor-pointer text-center transition-all
                                        peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20
                                        border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Under Review</span>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" v-model="form.status" value="approved" class="sr-only peer" />
                            <div class="px-4 py-2 border-2 rounded-lg cursor-pointer text-center transition-all
                                        peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20
                                        border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Approved</span>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" v-model="form.status" value="rejected" class="sr-only peer" />
                            <div class="px-4 py-2 border-2 rounded-lg cursor-pointer text-center transition-all
                                        peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20
                                        border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Rejected</span>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" v-model="form.status" value="needs_revision" class="sr-only peer" />
                            <div class="px-4 py-2 border-2 rounded-lg cursor-pointer text-center transition-all
                                        peer-checked:border-orange-500 peer-checked:bg-orange-50 dark:peer-checked:bg-orange-900/20
                                        border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Needs Revision</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Supervisor Assignment -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Assign Supervisor</h2>
                    <select v-model="form.supervisor_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="">No Supervisor Assigned</option>
                        <option v-for="supervisor in supervisors" :key="supervisor.id" :value="supervisor.id">
                            {{ supervisor.name }}
                        </option>
                    </select>
                </div>

                <!-- Scoring -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <StarIcon class="w-5 h-5 mr-2" />
                        Scoring Criteria
                    </h2>
                    <div class="space-y-6">
                        <div v-for="(label, key) in scoringCriteria" :key="key">
                            <div class="mb-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ label }}
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ criteriaDescriptions[key] }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <input type="range"
                                       v-model="form.scores[key]"
                                       min="0"
                                       max="25"
                                       class="flex-1"
                                       :style="`background: linear-gradient(to right, #3B82F6 0%, #3B82F6 ${(form.scores[key] / 25) * 100}%, #E5E7EB ${(form.scores[key] / 25) * 100}%, #E5E7EB 100%)`" />
                                <input type="number"
                                       v-model="form.scores[key]"
                                       min="0"
                                       max="25"
                                       class="w-16 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                                <span class="text-sm text-gray-500 dark:text-gray-400">/ 25</span>
                            </div>
                        </div>
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-900 dark:text-white">Total Score</span>
                                <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ totalScore }} / 100
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feedback -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Feedback</h2>
                    <textarea v-model="form.feedback"
                              rows="6"
                              placeholder="Provide detailed feedback for the team..."
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        This feedback will be shared with the team if notification is enabled.
                    </p>
                </div>

                <!-- Notification -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <label class="flex items-center">
                        <input type="checkbox"
                               v-model="form.notify_team"
                               class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500" />
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            Notify team about this review
                        </span>
                    </label>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3">
                    <a :href="route('hackathon-admin.ideas.show', idea.id)"
                       class="px-6 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span v-if="!form.processing">Submit Review</span>
                        <span v-else class="flex items-center">
                            <ArrowPathIcon class="w-4 h-4 mr-2 animate-spin" />
                            Processing...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </Default>
</template>