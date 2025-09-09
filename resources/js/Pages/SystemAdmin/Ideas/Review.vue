<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import Default from '../../../Layouts/Default.vue'
import FormInput from '../../../Components/FormInput.vue'
import FormTextarea from '../../../Components/FormTextarea.vue'
import FormSelect from '../../../Components/FormSelect.vue'

const props = defineProps({
    idea: {
        type: Object,
        required: true
    },
    availableSupervisors: {
        type: Array,
        default: () => []
    },
    evaluationCriteria: {
        type: Array,
        default: () => [
            { name: 'Innovation', weight: 25, description: 'How innovative and creative is the solution?' },
            { name: 'Technical Feasibility', weight: 25, description: 'Is the technical approach sound and achievable?' },
            { name: 'Impact', weight: 25, description: 'What is the potential impact of this solution?' },
            { name: 'Presentation', weight: 25, description: 'How well is the idea presented and documented?' }
        ]
    }
})

// Get theme color from localStorage or default
const themeColor = ref({
    primary: '#0d9488',
    hover: '#0f766e',
    rgb: '13, 148, 136',
    gradientFrom: '#0d9488',
    gradientTo: '#14b8a6'
})

onMounted(() => {
    // Get the current theme color from CSS variables
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

    // Initialize detailed scores
    if (!reviewForm.detailed_scores || Object.keys(reviewForm.detailed_scores).length === 0) {
        const scores = {}
        evaluationCriteria.forEach(criteria => {
            scores[criteria.name] = ''
        })
        reviewForm.detailed_scores = scores
    }
})

// Computed style for dynamic theme
const themeStyles = computed(() => ({
    '--theme-primary': themeColor.value.primary,
    '--theme-hover': themeColor.value.hover,
    '--theme-rgb': themeColor.value.rgb,
    '--theme-gradient-from': themeColor.value.gradientFrom,
    '--theme-gradient-to': themeColor.value.gradientTo,
}))

// Review form
const reviewForm = useForm({
    action: '',
    feedback: props.idea.feedback || '',
    score: props.idea.score || '',
    detailed_scores: props.idea.evaluation_scores || {},
    supervisor_id: props.idea.reviewed_by || ''
})

// Review steps
const currentStep = ref(1)
const totalSteps = 4

// Step navigation
const nextStep = () => {
    if (currentStep.value < totalSteps) {
        currentStep.value++
    }
}

const previousStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--
    }
}

const goToStep = (step) => {
    currentStep.value = step
}

// Submit review
const submitReview = (action) => {
    reviewForm.action = action
    
    // Validate based on action
    if (action === 'reject' && !reviewForm.feedback.trim()) {
        alert('Feedback is required when rejecting an idea.')
        return
    }
    
    if (action === 'need_edit' && !reviewForm.feedback.trim()) {
        alert('Please provide feedback explaining what needs to be edited.')
        return
    }

    const routeMap = {
        'accept': 'system-admin.ideas.review.accept',
        'reject': 'system-admin.ideas.review.reject',
        'need_edit': 'system-admin.ideas.review.need-edit'
    }

    reviewForm.post(route(routeMap[action], props.idea.id), {
        onSuccess: () => {
            // Redirect to ideas list or show success
        }
    })
}

// Calculate total score from detailed scores
const calculateTotalScore = () => {
    const scores = Object.values(reviewForm.detailed_scores)
    if (scores.length === 0) return 0
    
    const validScores = scores.filter(score => score !== '' && !isNaN(parseFloat(score)))
    if (validScores.length === 0) return 0
    
    const sum = validScores.reduce((acc, score) => acc + parseFloat(score), 0)
    return Math.round(sum / validScores.length)
}

// Watch detailed scores and update total
const updateTotalScore = () => {
    reviewForm.score = calculateTotalScore()
}

// Get status color
const getStatusColor = (status) => {
    const colors = {
        'draft': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        'submitted': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300', 
        'under_review': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'needs_revision': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
        'accepted': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'rejected': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
    }
    return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
}

// Format status for display
const formatStatus = (status) => {
    const statusMap = {
        'draft': 'Draft',
        'submitted': 'Submitted',
        'under_review': 'Under Review',
        'needs_revision': 'Needs Revision',
        'accepted': 'Accepted',
        'rejected': 'Rejected'
    }
    return statusMap[status] || status
}

// Step validation
const isStepValid = (step) => {
    switch (step) {
        case 1: return true // Overview is always valid
        case 2: return true // Evaluation can be partial
        case 3: return reviewForm.feedback.trim().length > 0
        case 4: return true // Final review
        default: return false
    }
}

// Get step status
const getStepStatus = (step) => {
    if (step < currentStep.value) return 'completed'
    if (step === currentStep.value) return 'current'
    return 'upcoming'
}

// Step titles
const stepTitles = {
    1: 'Idea Overview',
    2: 'Detailed Evaluation',
    3: 'Feedback & Comments',
    4: 'Final Decision'
}
</script>

<template>
    <Head :title="`Review Idea: ${idea.title || 'Untitled'}`" />

    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            Review Idea: {{ idea.title || 'Untitled Idea' }}
                        </h1>
                        <p class="mt-2 text-sm" :style="{ color: themeColor.primary }">
                            Submitted by {{ idea.team?.name || 'Unknown Team' }}
                        </p>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full"
                            :class="getStatusColor(idea.status)">
                            {{ formatStatus(idea.status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Progress Steps -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <template v-for="step in totalSteps" :key="step">
                        <div class="flex items-center">
                            <button @click="goToStep(step)"
                                :class="[
                                    'w-10 h-10 rounded-full flex items-center justify-center text-sm font-medium transition-colors border-2',
                                    getStepStatus(step) === 'completed' 
                                        ? 'bg-green-600 border-green-600 text-white'
                                        : getStepStatus(step) === 'current'
                                        ? 'border-[var(--theme-primary)] text-[var(--theme-primary)] bg-white dark:bg-gray-800'
                                        : 'bg-gray-200 border-gray-300 text-gray-600 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-300'
                                ]">
                                <svg v-if="getStepStatus(step) === 'completed'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span v-else>{{ step }}</span>
                            </button>
                            <div v-if="step < totalSteps" 
                                :class="[
                                    'flex-1 h-1 mx-4',
                                    getStepStatus(step + 1) === 'completed' || getStepStatus(step) === 'completed'
                                        ? 'bg-green-600'
                                        : 'bg-gray-300 dark:bg-gray-600'
                                ]">
                            </div>
                        </div>
                    </template>
                </div>
                <div class="flex justify-between mt-2">
                    <template v-for="step in totalSteps" :key="step">
                        <div class="text-sm text-center">
                            <div :class="[
                                'font-medium',
                                getStepStatus(step) === 'current' 
                                    ? 'text-[var(--theme-primary)]'
                                    : getStepStatus(step) === 'completed'
                                    ? 'text-green-600'
                                    : 'text-gray-500 dark:text-gray-400'
                            ]">
                                {{ stepTitles[step] }}
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Step Content -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8">
                
                <!-- Step 1: Idea Overview -->
                <div v-if="currentStep === 1">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Idea Overview</h2>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                        <!-- Basic Information -->
                        <div class="space-y-6">
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Team Information</div>
                                <div class="text-gray-900 dark:text-white font-medium">{{ idea.team?.name || 'N/A' }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    Leader: {{ idea.team?.leader?.name || 'N/A' }}
                                </div>
                                <div v-if="idea.team?.members?.length" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Members: {{ idea.team.members.length }} total
                                </div>
                            </div>
                            
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Track & Edition</div>
                                <div class="text-gray-900 dark:text-white font-medium">{{ idea.track?.name || 'N/A' }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ idea.team?.hackathon?.name || 'N/A' }}
                                </div>
                            </div>
                            
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Submission Date</div>
                                <div class="text-gray-900 dark:text-white font-medium">
                                    {{ idea.submitted_at ? new Date(idea.submitted_at).toLocaleDateString() : new Date(idea.created_at).toLocaleDateString() }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Current Status -->
                        <div class="space-y-6">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">Current Status</div>
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full"
                                    :class="getStatusColor(idea.status)">
                                    {{ formatStatus(idea.status) }}
                                </span>
                            </div>
                            
                            <div v-if="idea.score" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">Current Score</div>
                                <div class="text-2xl font-bold" :style="{ color: themeColor.primary }">
                                    {{ idea.score }}/100
                                </div>
                            </div>
                            
                            <div v-if="idea.reviewer" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">Assigned Reviewer</div>
                                <div class="text-gray-900 dark:text-white font-medium">{{ idea.reviewer.name }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Idea Content -->
                    <div class="space-y-6">
                        <div v-if="idea.problem_statement">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Problem Statement</h3>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ idea.problem_statement }}</p>
                        </div>
                        
                        <div v-if="idea.solution_approach">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Solution Approach</h3>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ idea.solution_approach }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Description</h3>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                {{ idea.description || 'No description provided' }}
                            </p>
                        </div>
                        
                        <div v-if="idea.expected_impact">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Expected Impact</h3>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ idea.expected_impact }}</p>
                        </div>
                        
                        <div v-if="idea.technologies && idea.technologies.length > 0">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Technologies</h3>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="tech in idea.technologies" :key="tech" 
                                    class="inline-flex px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                    {{ tech }}
                                </span>
                            </div>
                        </div>
                        
                        <div v-if="idea.files && idea.files.length > 0">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Attachments</h3>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div v-for="file in idea.files" :key="file.id" 
                                    class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ file.original_name || file.file_name || 'Unknown File' }}
                                        </div>
                                        <div v-if="file.file_size" class="text-xs text-gray-500">
                                            {{ Math.round(file.file_size / 1024) }} KB
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Detailed Evaluation -->
                <div v-if="currentStep === 2">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Detailed Evaluation</h2>
                    
                    <div class="space-y-6">
                        <div v-for="criteria in evaluationCriteria" :key="criteria.name" 
                            class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ criteria.name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        {{ criteria.description }}
                                    </p>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Weight: {{ criteria.weight }}%
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <FormInput
                                        v-model="reviewForm.detailed_scores[criteria.name]"
                                        :label="`Score for ${criteria.name} (0-100)`"
                                        type="number"
                                        :min="0"
                                        :max="100"
                                        placeholder="Enter score"
                                        @input="updateTotalScore"
                                    />
                                </div>
                                <div class="flex items-end">
                                    <div class="w-full">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Quick Score
                                        </label>
                                        <div class="flex space-x-2">
                                            <button v-for="score in [25, 50, 75, 100]" :key="score"
                                                @click="reviewForm.detailed_scores[criteria.name] = score; updateTotalScore()"
                                                class="px-3 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                {{ score }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Overall Score Summary -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Overall Score</h3>
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-3xl font-bold" :style="{ color: themeColor.primary }">
                                        {{ calculateTotalScore() }}/100
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        Calculated from {{ Object.values(reviewForm.detailed_scores).filter(s => s !== '').length }} criteria
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Manual Override</div>
                                    <FormInput
                                        v-model="reviewForm.score"
                                        type="number"
                                        :min="0"
                                        :max="100"
                                        placeholder="Override score"
                                        class="w-24"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Feedback & Comments -->
                <div v-if="currentStep === 3">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Feedback & Comments</h2>
                    
                    <div class="space-y-6">
                        <div>
                            <FormTextarea
                                v-model="reviewForm.feedback"
                                label="Detailed Feedback"
                                placeholder="Provide constructive feedback, suggestions for improvement, or reasons for your evaluation decision..."
                                :rows="8"
                                :error="reviewForm.errors.feedback"
                            />
                            <div class="mt-2 text-sm text-gray-500">
                                This feedback will be shared with the team to help them understand your evaluation.
                            </div>
                        </div>
                        
                        <!-- Supervisor Assignment -->
                        <div v-if="availableSupervisors.length > 0" class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Supervisor Assignment</h3>
                            <FormSelect
                                v-model="reviewForm.supervisor_id"
                                label="Assign Track Supervisor"
                                :options="[
                                    { value: '', label: 'Select Supervisor' },
                                    ...availableSupervisors.map(s => ({ value: s.id, label: `${s.name} - ${s.email}` }))
                                ]"
                            />
                        </div>
                        
                        <!-- Previous Feedback -->
                        <div v-if="idea.feedback" class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Previous Feedback</h3>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <p class="text-gray-700 dark:text-gray-300">{{ idea.feedback }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Final Decision -->
                <div v-if="currentStep === 4">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Final Decision</h2>
                    
                    <div class="space-y-6">
                        <!-- Review Summary -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Review Summary</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Overall Score</div>
                                    <div class="text-2xl font-bold" :style="{ color: themeColor.primary }">
                                        {{ reviewForm.score || calculateTotalScore() }}/100
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Criteria Evaluated</div>
                                    <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ Object.values(reviewForm.detailed_scores).filter(s => s !== '').length }}/{{ evaluationCriteria.length }}
                                    </div>
                                </div>
                                
                                <div v-if="reviewForm.supervisor_id">
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Assigned Supervisor</div>
                                    <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ availableSupervisors.find(s => s.id == reviewForm.supervisor_id)?.name || 'Selected' }}
                                    </div>
                                </div>
                            </div>
                            
                            <div v-if="reviewForm.feedback" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">Feedback Preview</div>
                                <p class="text-gray-700 dark:text-gray-300 line-clamp-3">{{ reviewForm.feedback }}</p>
                            </div>
                        </div>
                        
                        <!-- Final Decision Buttons -->
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Make Your Decision</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <button @click="submitReview('accept')"
                                    :disabled="reviewForm.processing"
                                    class="p-4 border-2 border-green-200 dark:border-green-700 rounded-lg hover:border-green-400 dark:hover:border-green-500 transition-colors group"
                                    :class="{ 'opacity-50 cursor-not-allowed': reviewForm.processing }">
                                    <div class="text-center">
                                        <div class="w-12 h-12 mx-auto mb-3 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center group-hover:bg-green-200 dark:group-hover:bg-green-800 transition-colors">
                                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <div class="font-semibold text-green-800 dark:text-green-300">Accept</div>
                                        <div class="text-sm text-green-600 dark:text-green-400 mt-1">Approve this idea</div>
                                    </div>
                                </button>
                                
                                <button @click="submitReview('need_edit')"
                                    :disabled="reviewForm.processing || !reviewForm.feedback.trim()"
                                    class="p-4 border-2 border-orange-200 dark:border-orange-700 rounded-lg hover:border-orange-400 dark:hover:border-orange-500 transition-colors group"
                                    :class="{ 'opacity-50 cursor-not-allowed': reviewForm.processing || !reviewForm.feedback.trim() }">
                                    <div class="text-center">
                                        <div class="w-12 h-12 mx-auto mb-3 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center group-hover:bg-orange-200 dark:group-hover:bg-orange-800 transition-colors">
                                            <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </div>
                                        <div class="font-semibold text-orange-800 dark:text-orange-300">Need Revision</div>
                                        <div class="text-sm text-orange-600 dark:text-orange-400 mt-1">Request changes</div>
                                    </div>
                                </button>
                                
                                <button @click="submitReview('reject')"
                                    :disabled="reviewForm.processing || !reviewForm.feedback.trim()"
                                    class="p-4 border-2 border-red-200 dark:border-red-700 rounded-lg hover:border-red-400 dark:hover:border-red-500 transition-colors group"
                                    :class="{ 'opacity-50 cursor-not-allowed': reviewForm.processing || !reviewForm.feedback.trim() }">
                                    <div class="text-center">
                                        <div class="w-12 h-12 mx-auto mb-3 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center group-hover:bg-red-200 dark:group-hover:bg-red-800 transition-colors">
                                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </div>
                                        <div class="font-semibold text-red-800 dark:text-red-300">Reject</div>
                                        <div class="text-sm text-red-600 dark:text-red-400 mt-1">Decline this idea</div>
                                    </div>
                                </button>
                            </div>
                            
                            <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                                Note: Reject and Need Revision require feedback to be provided.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex items-center justify-between pt-8 border-t border-gray-200 dark:border-gray-700">
                    <div>
                        <button v-if="currentStep > 1" @click="previousStep"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Previous
                        </button>
                        
                        <Link v-else :href="route('system-admin.ideas.show', idea.id)"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Back to Idea
                        </Link>
                    </div>
                    
                    <div class="text-center">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            Step {{ currentStep }} of {{ totalSteps }}
                        </span>
                    </div>
                    
                    <div>
                        <button v-if="currentStep < totalSteps" @click="nextStep"
                            class="inline-flex items-center px-4 py-2 rounded-lg font-semibold text-white transition-all duration-200 shadow-sm hover:shadow-md"
                            :style="{ 
                                background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                            }">
                            Next
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

<style scoped>
/* Theme-aware input styling */
:deep(.peer:focus) {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 2px rgba(var(--theme-rgb), 0.2) !important;
}

:deep(.error) {
    border-color: #ef4444 !important;
}

.line-clamp-3 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
}
</style>
