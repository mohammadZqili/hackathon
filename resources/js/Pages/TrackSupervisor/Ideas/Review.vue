<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Default from '../../../Layouts/Default.vue'
import { StarIcon, ArrowLeftIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    idea: Object,
    scoringCriteria: Object,
})

const form = useForm({
    status: props.idea?.status || 'under_review',
    feedback: '',
    scores: {
        innovation: 0,
        feasibility: 0,
        impact: 0,
        presentation: 0,
    },
})

const totalScore = computed(() => {
    return Object.values(form.scores).reduce((sum, score) => sum + parseInt(score || 0), 0)
})

const submitReview = () => {
    form.post(route('track-supervisor.ideas.submit-review', props.idea.id))
}
</script>

<template>
    <Head :title="`Review: ${idea?.title || 'Idea'}`" />
    
    <Default>
        <div class="max-w-5xl mx-auto">
            <div class="mb-8 flex items-center space-x-4">
                <a :href="route('track-supervisor.ideas.index')" class="text-gray-500 hover:text-gray-700">
                    <ArrowLeftIcon class="w-5 h-5" />
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Review Idea</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ idea?.title }}</p>
                </div>
            </div>

            <form @submit.prevent="submitReview" class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Idea Details</h2>
                    <p class="text-gray-700 dark:text-gray-300">{{ idea?.description }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <StarIcon class="w-5 h-5 mr-2" />
                        Scoring
                    </h2>
                    <div class="space-y-4">
                        <div v-for="(label, key) in scoringCriteria || {}" :key="key">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ label }}
                            </label>
                            <input type="range" v-model="form.scores[key]" min="0" max="25" class="w-full" />
                            <div class="text-right text-sm text-gray-500">{{ form.scores[key] }} / 25</div>
                        </div>
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between">
                                <span class="text-lg font-semibold">Total Score</span>
                                <span class="text-lg font-bold text-blue-600">{{ totalScore }} / 100</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Feedback</h2>
                    <textarea v-model="form.feedback" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg"
                              placeholder="Provide feedback for the team..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <a :href="route('track-supervisor.ideas.index')" 
                       class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg">
                        Submit Review
                    </button>
                </div>
            </form>
        </div>
    </Default>
</template>