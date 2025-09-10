<script setup>
import { Head, router, useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import Default from '@/Layouts/Default.vue'
import { 
    DocumentIcon,
    CheckIcon,
    XMarkIcon,
    ClockIcon,
    ExclamationTriangleIcon,
    StarIcon,
    UserGroupIcon,
    CalendarIcon,
    TagIcon,
    TrophyIcon,
    PencilSquareIcon,
    TrashIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    idea: Object,
    reviewHistory: Array,
    scoring: Object,
})

const activeTab = ref('overview')

// Get theme color from localStorage or default
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

// Form for decision making
const decisionForm = useForm({
    status: '',
    feedback: '',
    score: props.idea?.score || null,
})

const submitDecision = (status) => {
    decisionForm.status = status
}

const updateScore = () => {
    if (decisionForm.score && decisionForm.score >= 0 && decisionForm.score <= 100) {
        decisionForm.post(route('system-admin.ideas.score', props.idea.id), {
            only: ['score'],
            preserveScroll: true,
        })
    }
}

const submitChanges = () => {
    if (!decisionForm.status) {
        alert('Please select a decision status');
        return;
    }
    
    decisionForm.post(route('system-admin.ideas.process-review', props.idea.id), {
        onSuccess: () => {
            router.visit(route('system-admin.ideas.index'))
        },
    })
}

const deleteIdea = () => {
    if (confirm('Are you sure you want to delete this idea? This action cannot be undone.')) {
        router.delete(route('system-admin.ideas.destroy', props.idea.id))
    }
}

const formatDateTime = (date) => {
    if (!date) return 'N/A'
    const d = new Date(date)
    return d.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

// Status gradient colors
const statusGradients = {
    accepted: 'from-green-400 to-green-600',
    rejected: 'from-red-400 to-red-600',
    needs_revision: 'from-amber-400 to-amber-600',
    pending_review: 'from-blue-400 to-blue-600',
    under_review: 'from-indigo-400 to-indigo-600',
    submitted: 'from-blue-400 to-blue-600',
    draft: 'from-gray-400 to-gray-600',
    in_progress: 'from-purple-400 to-purple-600',
    completed: 'from-teal-400 to-teal-600',
}
</script>

<template>
    <Head :title="`System Admin - Idea: ${idea.title}`" />

    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Enhanced Header with Gradient -->
            <div class="mb-8">
                <div class="bg-gradient-to-r rounded-xl p-6 shadow-lg"
                     :style="{
                         background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                     }">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-sm text-white/80 mb-1">SYSTEM ADMIN VIEW</div>
                            <h1 class="text-3xl font-bold text-white mb-2">
                                {{ idea.title }}
                            </h1>
                            <div class="flex items-center gap-4 text-white/90">
                                <span class="flex items-center">
                                    <UserGroupIcon class="w-5 h-5 mr-2" />
                                    {{ idea.team?.name || 'Unknown Team' }}
                                </span>
                                <span class="flex items-center">
                                    <CalendarIcon class="w-5 h-5 mr-2" />
                                    {{ formatDateTime(idea.created_at) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="deleteIdea"
                                    class="bg-red-500/20 hover:bg-red-500/30 text-white px-4 py-2 rounded-lg transition-all flex items-center">
                                <TrashIcon class="w-5 h-5 mr-2" />
                                Delete
                            </button>
                            <span :class="`bg-gradient-to-r ${statusGradients[idea.status] || 'from-gray-400 to-gray-600'} px-4 py-2 text-white font-semibold rounded-full shadow-md`">
                                {{ idea.status?.replace('_', ' ').toUpperCase() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs with Theme Color -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="flex -mb-px">
                        <button @click="activeTab = 'overview'"
                                :class="activeTab === 'overview' ? 'border-b-2' : 'border-transparent'"
                                :style="activeTab === 'overview' ? { borderColor: themeColor.primary, color: themeColor.primary } : {}"
                                class="px-6 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                            <div class="flex items-center">
                                <DocumentIcon class="w-5 h-5 mr-2" />
                                Overview
                            </div>
                        </button>
                        <button @click="activeTab = 'response'"
                                :class="activeTab === 'response' ? 'border-b-2' : 'border-transparent'"
                                :style="activeTab === 'response' ? { borderColor: themeColor.primary, color: themeColor.primary } : {}"
                                class="px-6 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                            <div class="flex items-center">
                                <ClockIcon class="w-5 h-5 mr-2" />
                                Response History
                            </div>
                        </button>
                        <button @click="activeTab = 'admin'"
                                :class="activeTab === 'admin' ? 'border-b-2' : 'border-transparent'"
                                :style="activeTab === 'admin' ? { borderColor: themeColor.primary, color: themeColor.primary } : {}"
                                class="px-6 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                            <div class="flex items-center">
                                <PencilSquareIcon class="w-5 h-5 mr-2" />
                                Admin Actions
                            </div>
                        </button>
                    </nav>
                </div>

                <!-- Overview Tab Content -->
                <div v-show="activeTab === 'overview'" class="p-6">
                    <!-- Quick Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-gradient-to-br p-4 rounded-lg text-white"
                             :style="{
                                 background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                             }">
                            <TagIcon class="w-8 h-8 mb-2 opacity-80" />
                            <div class="text-sm opacity-90">Track</div>
                            <div class="text-lg font-bold">{{ idea.track?.name || 'Unassigned' }}</div>
                        </div>
                        <div class="bg-gradient-to-br from-purple-400 to-purple-600 p-4 rounded-lg text-white">
                            <UserGroupIcon class="w-8 h-8 mb-2 opacity-80" />
                            <div class="text-sm opacity-90">Team Members</div>
                            <div class="text-lg font-bold">{{ idea.team?.members?.length || 0 }}</div>
                        </div>
                        <div class="bg-gradient-to-br from-orange-400 to-orange-600 p-4 rounded-lg text-white">
                            <DocumentIcon class="w-8 h-8 mb-2 opacity-80" />
                            <div class="text-sm opacity-90">Documents</div>
                            <div class="text-lg font-bold">{{ idea.files?.length || 0 }}</div>
                        </div>
                        <div class="bg-gradient-to-br from-blue-400 to-blue-600 p-4 rounded-lg text-white">
                            <TrophyIcon class="w-8 h-8 mb-2 opacity-80" />
                            <div class="text-sm opacity-90">Score</div>
                            <div class="text-lg font-bold">{{ idea.score || 0 }}/100</div>
                        </div>
                    </div>

                    <!-- Idea Details -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-3" :style="{ color: themeColor.primary }">
                                Description
                            </h3>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                    {{ idea.description }}
                                </p>
                            </div>
                        </div>

                        <div v-if="idea.problem_statement">
                            <h3 class="text-lg font-semibold mb-3" :style="{ color: themeColor.primary }">
                                Problem Statement
                            </h3>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                    {{ idea.problem_statement }}
                                </p>
                            </div>
                        </div>

                        <div v-if="idea.solution_approach">
                            <h3 class="text-lg font-semibold mb-3" :style="{ color: themeColor.primary }">
                                Solution Approach
                            </h3>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                    {{ idea.solution_approach }}
                                </p>
                            </div>
                        </div>

                        <!-- Related Documents with Enhanced Style -->
                        <div v-if="idea.files && idea.files.length > 0">
                            <h3 class="text-lg font-semibold mb-3" :style="{ color: themeColor.primary }">
                                Related Documents
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div v-for="file in idea.files" :key="file.id"
                                     class="flex items-center p-3 rounded-lg cursor-pointer transition-all hover:shadow-md"
                                     :style="{
                                         backgroundColor: `rgba(${themeColor.rgb}, 0.05)`,
                                         border: `1px solid rgba(${themeColor.rgb}, 0.2)`
                                     }"
                                     @mouseover="e => e.currentTarget.style.backgroundColor = `rgba(${themeColor.rgb}, 0.1)`"
                                     @mouseleave="e => e.currentTarget.style.backgroundColor = `rgba(${themeColor.rgb}, 0.05)`">
                                    <div class="p-2 rounded-lg mr-3"
                                         :style="{ backgroundColor: `rgba(${themeColor.rgb}, 0.1)` }">
                                        <DocumentIcon class="w-6 h-6" :style="{ color: themeColor.primary }" />
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ file.filename }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Click to download</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Response Tab Content -->
                <div v-show="activeTab === 'response'" class="p-6">
                    <h3 class="text-lg font-semibold mb-4" :style="{ color: themeColor.primary }">
                        Review History
                    </h3>
                    <div v-if="reviewHistory && reviewHistory.length > 0" class="space-y-4">
                        <div v-for="review in reviewHistory" :key="review.id" 
                             class="border-l-4 pl-4 py-3 rounded-r-lg"
                             :style="{ 
                                 borderLeftColor: themeColor.primary,
                                 backgroundColor: `rgba(${themeColor.rgb}, 0.02)`
                             }">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ review.reviewer_name }}
                                    </span>
                                    <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                        {{ formatDateTime(review.created_at) }}
                                    </span>
                                </div>
                                <span :class="`bg-gradient-to-r ${statusGradients[review.status] || 'from-gray-400 to-gray-600'} px-3 py-1 text-xs font-medium text-white rounded-full`">
                                    {{ review.status }}
                                </span>
                            </div>
                            <p class="text-gray-700 dark:text-gray-300">{{ review.feedback }}</p>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                        No review history available
                    </div>
                </div>

                <!-- Admin Tab Content -->
                <div v-show="activeTab === 'admin'" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Make Decision Section -->
                        <div>
                            <h3 class="text-xl font-bold mb-4 flex items-center" :style="{ color: themeColor.primary }">
                                <PencilSquareIcon class="w-6 h-6 mr-2" />
                                Make Decision
                            </h3>
                            <div class="flex flex-wrap gap-3 mb-4">
                                <button @click="submitDecision('accepted')"
                                        :class="decisionForm.status === 'accepted' ? 'ring-2 ring-offset-2' : ''"
                                        :style="decisionForm.status === 'accepted' ? { 
                                            background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                                            ringColor: themeColor.primary
                                        } : {}"
                                        class="px-4 py-2 font-bold rounded-lg transition-all text-white bg-gradient-to-r from-green-400 to-green-600 hover:from-green-500 hover:to-green-700">
                                    <CheckIcon class="w-5 h-5 inline mr-2" />
                                    Accept
                                </button>
                                <button @click="submitDecision('rejected')"
                                        :class="decisionForm.status === 'rejected' ? 'ring-2 ring-offset-2 ring-red-500' : ''"
                                        class="px-4 py-2 font-bold rounded-lg transition-all text-white bg-gradient-to-r from-red-400 to-red-600 hover:from-red-500 hover:to-red-700">
                                    <XMarkIcon class="w-5 h-5 inline mr-2" />
                                    Reject
                                </button>
                                <button @click="submitDecision('needs_revision')"
                                        :class="decisionForm.status === 'needs_revision' ? 'ring-2 ring-offset-2 ring-amber-500' : ''"
                                        class="px-4 py-2 font-bold rounded-lg transition-all text-white bg-gradient-to-r from-amber-400 to-amber-600 hover:from-amber-500 hover:to-amber-700">
                                    <ExclamationTriangleIcon class="w-5 h-5 inline mr-2" />
                                    Need Edit
                                </button>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Feedback & Comments
                                </label>
                                <textarea
                                    v-model="decisionForm.feedback"
                                    placeholder="Provide feedback or required changes"
                                    class="w-full px-4 py-3 border rounded-lg bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 transition-all resize-none"
                                    :style="{
                                        borderColor: `rgba(${themeColor.rgb}, 0.3)`,
                                        '--tw-ring-color': themeColor.primary
                                    }"
                                    rows="5"
                                ></textarea>
                            </div>
                        </div>

                        <!-- Score Section -->
                        <div>
                            <h3 class="text-xl font-bold mb-4 flex items-center" :style="{ color: themeColor.primary }">
                                <StarIcon class="w-6 h-6 mr-2" />
                                Score
                            </h3>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Overall Score (0-100)
                                </label>
                                <div class="relative">
                                    <input
                                        v-model.number="decisionForm.score"
                                        @blur="updateScore"
                                        type="number"
                                        min="0"
                                        max="100"
                                        placeholder="Enter score"
                                        class="w-full px-4 py-3 pr-16 border rounded-lg text-2xl font-bold text-center transition-all"
                                        :style="{
                                            borderColor: `rgba(${themeColor.rgb}, 0.3)`,
                                            backgroundColor: `rgba(${themeColor.rgb}, 0.05)`,
                                            color: themeColor.primary,
                                            '--tw-ring-color': themeColor.primary
                                        }"
                                    />
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 text-2xl font-bold"
                                         :style="{ color: themeColor.primary }">
                                        /100
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Score Progress Bar -->
                            <div class="mb-6">
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                    <div class="h-3 rounded-full transition-all duration-300"
                                         :style="{
                                             width: `${decisionForm.score || 0}%`,
                                             background: `linear-gradient(90deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                                         }"></div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button @click="submitChanges"
                                    :disabled="!decisionForm.status || decisionForm.processing"
                                    class="w-full px-6 py-3 text-white font-bold rounded-lg transition-all shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
                                    :style="{
                                        background: decisionForm.status && !decisionForm.processing 
                                            ? `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                                            : 'linear-gradient(135deg, #9ca3af, #6b7280)'
                                    }">
                                <span v-if="!decisionForm.processing">
                                    Submit Review
                                </span>
                                <span v-else class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

<style scoped>
/* Remove spinner from number input */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type="number"] {
    -moz-appearance: textfield;
}

/* Focus states */
textarea:focus,
input:focus {
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1);
}
</style>