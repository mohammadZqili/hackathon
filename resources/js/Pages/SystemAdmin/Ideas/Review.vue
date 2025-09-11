<script setup>
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL, direction, locale } = useLocalization()
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Default from '@/Layouts/Default.vue'
import { 
    ArrowLeftIcon,
    StarIcon,
    ArrowPathIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    idea: Object,
    supervisors: Array,
    scoringCriteria: Object,
})

const form = useForm({
    status: props.idea.status || 'pending_review',
    reviewed_by: props.idea.reviewed_by || '',
    feedback: props.idea.feedback || '',
    scores: {
        innovation: props.idea.evaluation_scores?.innovation || 0,
        feasibility: props.idea.evaluation_scores?.feasibility || 0,
        impact: props.idea.evaluation_scores?.impact || 0,
        presentation: props.idea.evaluation_scores?.presentation || 0,
    },
    notify_team: true,
})

const totalScore = computed(() => {
    return Object.values(form.scores).reduce((sum, score) => sum + parseInt(score || 0), 0)
})

const submitReview = () => {
    form.post(route('system-admin.ideas.process-review', props.idea.id), {
        preserveScroll: true,
        onSuccess: () => {
            router.visit(route('system-admin.ideas.show', props.idea.id))
        },
    })
}

const quickDecision = (status) => {
    form.status = status
    submitReview()
}

const criteriaDescriptions = {
    innovation: t('admin.ideas.criteria_innovation'),
    feasibility: t('admin.ideas.criteria_feasibility'),
    impact: t('admin.ideas.criteria_impact'),
    presentation: t('admin.ideas.criteria_presentation'),
}
</script>

<template>
    <Head :title="`${t('admin.ideas.review')}: ${idea.title}`" />

    <Default>
        <div class="max-w-[1200px] mx-auto px-4">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center mb-3">
                    <a :href="route('system-admin.ideas.show', idea.id)"
                       class="mr-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        <ArrowLeftIcon class="w-5 h-5" />
                    </a>
                    <div>
                        <h1 class="text-[32px] font-bold text-gray-900 dark:text-gray-100">
                            {{ t('admin.ideas.review') }}: {{ idea.title }}
                        </h1>
                        <p class="text-sm text-emerald-600 dark:text-emerald-400 mt-1">
                            {{ t('admin.ideas.submitted_by') }} {{ idea.team?.name || t('admin.ideas.unknown_team') }}
                        </p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submitReview" class="space-y-6">
                <!-- Main Review Section -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-[22px] font-bold text-gray-900 dark:text-gray-100 mb-6">
                        {{ t('admin.ideas.review_details') }}
                    </h2>

                    <!-- Quick Decision Buttons -->
                    <div class="mb-6">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ t('admin.ideas.quick_decision') }}</p>
                        <div class="flex flex-wrap gap-3">
                            <button type="button"
                                    @click="quickDecision('accepted')"
                                    :disabled="form.processing"
                                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-colors disabled:opacity-50">
                                {{ t('admin.ideas.accept') }}
                            </button>
                            <button type="button"
                                    @click="quickDecision('rejected')"
                                    :disabled="form.processing"
                                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-bold rounded-xl transition-colors disabled:opacity-50">
                                {{ t('admin.ideas.reject') }}
                            </button>
                            <button type="button"
                                    @click="quickDecision('needs_revision')"
                                    :disabled="form.processing"
                                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-bold rounded-xl transition-colors disabled:opacity-50">
                                {{ t('admin.ideas.need_edit') }}
                            </button>
                        </div>
                    </div>

                    <!-- Detailed Status Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ t('admin.ideas.review_status') }}
                        </label>
                        <select v-model="form.status"
                                class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="pending_review">{{ t('admin.ideas.pending') }}</option>
                            <option value="under_review">{{ t('admin.ideas.under_review') }}</option>
                            <option value="accepted">{{ t('admin.ideas.accepted') }}</option>
                            <option value="rejected">{{ t('admin.ideas.rejected') }}</option>
                            <option value="needs_revision">{{ t('admin.ideas.needs_revision') }}</option>
                            <option value="in_progress">{{ t('admin.ideas.in_progress') }}</option>
                            <option value="completed">{{ t('admin.ideas.completed') }}</option>
                        </select>
                    </div>

                    <!-- Supervisor Assignment -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ t('admin.ideas.assign_supervisor') }}
                        </label>
                        <select v-model="form.reviewed_by"
                                class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">{{ t('admin.ideas.no_supervisor') }}</option>
                            <option v-for="supervisor in supervisors" :key="supervisor.id" :value="supervisor.id">
                                {{ supervisor.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Scoring Section -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-[22px] font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center">
                        <StarIcon class="w-6 h-6 mr-2 text-emerald-600 dark:text-emerald-400" />
                        {{ t('admin.ideas.scoring_criteria') }}
                    </h2>
                    <div class="space-y-6">
                        <div v-for="(label, key) in scoringCriteria" :key="key">
                            <div class="mb-3">
                                <div class="flex justify-between items-center mb-1">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ label }}
                                    </label>
                                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                        {{ form.scores[key] }} / 25
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                    {{ criteriaDescriptions[key] }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <input type="range"
                                       v-model="form.scores[key]"
                                       min="0"
                                       max="25"
                                       class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                                       :style="`background: linear-gradient(to right, #10b981 0%, #10b981 ${(form.scores[key] / 25) * 100}%, #e5e7eb ${(form.scores[key] / 25) * 100}%, #e5e7eb 100%)`" />
                                <input type="number"
                                       v-model="form.scores[key]"
                                       min="0"
                                       max="25"
                                       class="w-16 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                            </div>
                        </div>
                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ t('admin.ideas.total_score') }}</span>
                                <span class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ totalScore }} / 100
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feedback Section -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-[22px] font-bold text-gray-900 dark:text-gray-100 mb-4">
                        {{ t('admin.ideas.feedback') }}
                    </h2>
                    <textarea v-model="form.feedback"
                              rows="6"
                              :placeholder="t('admin.ideas.feedback_placeholder')"
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent resize-none"></textarea>
                    <div class="mt-3">
                        <label class="flex items-center">
                            <input type="checkbox"
                                   v-model="form.notify_team"
                                   class="rounded border-gray-300 dark:border-gray-600 text-emerald-600 focus:ring-emerald-500" />
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                {{ t('admin.ideas.notify_team') }}
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3">
                    <a :href="route('system-admin.ideas.show', idea.id)"
                       class="px-6 py-2.5 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl font-medium transition-colors">
                        {{ t('admin.actions.cancel') }}
                    </a>
                    <button type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                        <span v-if="!form.processing">{{ t('admin.actions.submit_review') }}</span>
                        <span v-else class="flex items-center">
                            <ArrowPathIcon class="w-4 h-4 mr-2 animate-spin" />
                            {{ t('admin.actions.loading') }}
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </Default>
</template>

<style scoped>
/* Custom range slider styling */
input[type="range"] {
    -webkit-appearance: none;
    appearance: none;
    background: transparent;
    cursor: pointer;
}

input[type="range"]::-webkit-slider-track {
    background: inherit;
    height: 8px;
    border-radius: 4px;
}

input[type="range"]::-moz-range-track {
    background: inherit;
    height: 8px;
    border-radius: 4px;
}

input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    background: #10b981;
    height: 20px;
    width: 20px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
}

input[type="range"]::-moz-range-thumb {
    background: #10b981;
    height: 20px;
    width: 20px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    cursor: pointer;
}

/* Remove spinner from number input */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type="number"] {
    -moz-appearance: textfield;
}
</style>
