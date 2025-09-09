<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import Default from '../../../Layouts/Default.vue'
import FormInput from '../../../Components/FormInput.vue'
import FormTextarea from '../../../Components/FormTextarea.vue'

const props = defineProps({
    idea: {
        type: Object,
        required: true
    },
    availableSupervisors: {
        type: Array,
        default: () => []
    },
    can: {
        type: Object,
        default: () => ({})
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
})

// Computed style for dynamic theme
const themeStyles = computed(() => ({
    '--theme-primary': themeColor.value.primary,
    '--theme-hover': themeColor.value.hover,
    '--theme-rgb': themeColor.value.rgb,
    '--theme-gradient-from': themeColor.value.gradientFrom,
    '--theme-gradient-to': themeColor.value.gradientTo,
}))

// Active tab
const activeTab = ref('overview')

// Review form
const reviewForm = useForm({
    feedback: props.idea.feedback || '',
    score: props.idea.score || ''
})

// Supervisor assignment form
const supervisorForm = useForm({
    supervisor_id: props.idea.reviewed_by || ''
})

// Loading states
const isProcessing = ref(false)

// Handle review decision
const makeDecision = async (action) => {
    if (isProcessing.value) return
    
    isProcessing.value = true
    
    const routeMap = {
        'accept': 'system-admin.ideas.review.accept',
        'reject': 'system-admin.ideas.review.reject', 
        'need_edit': 'system-admin.ideas.review.need-edit'
    }
    
    if (!routeMap[action]) {
        isProcessing.value = false
        return
    }
    
    if (action === 'reject' && !reviewForm.feedback.trim()) {
        alert('Feedback is required when rejecting an idea.')
        isProcessing.value = false
        return
    }
    
    if (action === 'need_edit' && !reviewForm.feedback.trim()) {
        alert('Please provide feedback explaining what needs to be edited.')
        isProcessing.value = false
        return
    }
    
    reviewForm.post(route(routeMap[action], props.idea.id), {
        onSuccess: () => {
            // Success handled by flash messages
        },
        onError: (errors) => {
            console.error('Review submission error:', errors)
        },
        onFinish: () => {
            isProcessing.value = false
        }
    })
}

// Assign supervisor
const assignSupervisor = () => {
    if (supervisorForm.processing) return
    
    supervisorForm.post(route('system-admin.ideas.assign-supervisor', props.idea.id), {
        onSuccess: () => {
            // Success handled by flash messages
        }
    })
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

// Format date
const formatDate = (date) => {
    return new Date(date).toLocaleString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    })
}

// Get file extension icon
const getFileIcon = (filename) => {
    if (!filename) return 'document'
    const ext = filename.split('.').pop()?.toLowerCase()
    if (['pdf'].includes(ext)) return 'pdf'
    if (['doc', 'docx'].includes(ext)) return 'document'
    if (['xls', 'xlsx'].includes(ext)) return 'spreadsheet'
    if (['ppt', 'pptx'].includes(ext)) return 'presentation'
    if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) return 'image'
    return 'document'
}

// Download file
const downloadFile = (file) => {
    if (file.download_url) {
        window.open(file.download_url, '_blank')
    } else {
        // Generate download URL
        window.open(route('system-admin.ideas.download-file', { idea: props.idea.id, file: file.id }), '_blank')
    }
}

// Score validation
const validateScore = (value) => {
    const num = parseInt(value)
    if (isNaN(num) || num < 0 || num > 100) {
        return false
    }
    return true
}

// Update score
const updateScore = () => {
    if (!validateScore(reviewForm.score)) {
        alert('Score must be between 0 and 100')
        return
    }
    
    reviewForm.post(route('system-admin.ideas.update-score', props.idea.id), {
        onSuccess: () => {
            // Success handled
        }
    })
}
</script>

<template>
    <Head :title="`Idea: ${idea.title || 'Untitled'}`" />

    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ idea.title || 'Untitled Idea' }}
                        </h1>
                        <p class="mt-2 text-sm" :style="{ color: themeColor.primary }">
                            Submitted by {{ idea.team?.name || 'Unknown Team' }}
                        </p>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="flex items-center space-x-3">
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full"
                            :class="getStatusColor(idea.status)">
                            {{ formatStatus(idea.status) }}
                        </span>
                        
                        <div v-if="idea.score" class="text-lg font-bold" :style="{ color: themeColor.primary }">
                            {{ idea.score }}/100
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                <nav class="flex space-x-8">
                    <button @click="activeTab = 'overview'"
                        :class="[
                            'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                            activeTab === 'overview' 
                                ? 'border-[var(--theme-primary)] text-[var(--theme-primary)]'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
                        ]">
                        Overview
                    </button>
                    <button @click="activeTab = 'review'"
                        :class="[
                            'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                            activeTab === 'review' 
                                ? 'border-[var(--theme-primary)] text-[var(--theme-primary)]'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
                        ]">
                        Review & Scoring
                    </button>
                    <button @click="activeTab = 'history'"
                        :class="[
                            'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                            activeTab === 'history' 
                                ? 'border-[var(--theme-primary)] text-[var(--theme-primary)]'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
                        ]">
                        History
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            
            <!-- Overview Tab -->
            <div v-if="activeTab === 'overview'">
                <!-- Idea Details Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Idea Information</h2>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Team Name</div>
                                <div class="text-gray-900 dark:text-white font-medium">{{ idea.team?.name || 'N/A' }}</div>
                            </div>
                            
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Team Leader</div>
                                <div class="text-gray-900 dark:text-white font-medium">{{ idea.team?.leader?.name || 'N/A' }}</div>
                                <div v-if="idea.team?.leader?.email" class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ idea.team?.leader?.email }}
                                </div>
                            </div>
                            
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Track</div>
                                <div class="text-gray-900 dark:text-white font-medium">{{ idea.track?.name || 'N/A' }}</div>
                                <div v-if="idea.track?.description" class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ idea.track.description }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-6">
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Submission Date</div>
                                <div class="text-gray-900 dark:text-white font-medium">
                                    {{ idea.submitted_at ? formatDate(idea.submitted_at) : formatDate(idea.created_at) }}
                                </div>
                            </div>
                            
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Hackathon Edition</div>
                                <div class="text-gray-900 dark:text-white font-medium">{{ idea.team?.hackathon?.name || 'N/A' }}</div>
                                <div v-if="idea.team?.hackathon?.year" class="text-sm text-gray-500 dark:text-gray-400">
                                    Year {{ idea.team.hackathon.year }}
                                </div>
                            </div>
                            
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Reviewer</div>
                                <div class="text-gray-900 dark:text-white font-medium">
                                    {{ idea.reviewer?.name || 'Not assigned' }}
                                </div>
                                <div v-if="idea.reviewed_at" class="text-sm text-gray-500 dark:text-gray-400">
                                    Reviewed: {{ formatDate(idea.reviewed_at) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Problem Statement -->
                <div v-if="idea.problem_statement" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Problem Statement</h2>
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">{{ idea.problem_statement }}</p>
                </div>

                <!-- Solution Approach -->
                <div v-if="idea.solution_approach" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Solution Approach</h2>
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">{{ idea.solution_approach }}</p>
                </div>

                <!-- Description Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Description</h2>
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">
                        {{ idea.description || 'No description provided' }}
                    </p>
                </div>

                <!-- Expected Impact -->
                <div v-if="idea.expected_impact" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Expected Impact</h2>
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">{{ idea.expected_impact }}</p>
                </div>

                <!-- Technologies -->
                <div v-if="idea.technologies && idea.technologies.length > 0" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Technologies</h2>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="tech in idea.technologies" :key="tech" 
                            class="inline-flex px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                            {{ tech }}
                        </span>
                    </div>
                </div>

                <!-- Related Documents Section -->
                <div v-if="idea.files && idea.files.length > 0" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Related Documents</h2>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <div v-for="file in idea.files" :key="file.id" 
                            class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors group cursor-pointer"
                            @click="downloadFile(file)">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-900 dark:text-white truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {{ file.original_name || file.file_name || 'Unknown File' }}
                                </div>
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    <span v-if="file.file_size">{{ Math.round(file.file_size / 1024) }} KB</span>
                                    <span v-if="file.uploaded_at" class="ml-2">
                                        • {{ formatDate(file.uploaded_at) }}
                                    </span>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review & Scoring Tab -->
            <div v-if="activeTab === 'review'">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Decision Making -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Review Decision</h2>
                        
                        <div class="mb-6">
                            <FormTextarea
                                v-model="reviewForm.feedback"
                                label="Feedback"
                                placeholder="Provide feedback or required changes for the idea's acceptance"
                                :rows="6"
                                :error="reviewForm.errors.feedback"
                            />
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <button @click="makeDecision('accept')"
                                :disabled="isProcessing || reviewForm.processing"
                                class="px-6 py-2 rounded-lg font-semibold text-white transition-all duration-200 shadow-sm hover:shadow-md"
                                :style="{ 
                                    background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                                }"
                                :class="{ 'opacity-50 cursor-not-allowed': isProcessing || reviewForm.processing }">
                                <span v-if="isProcessing">Processing...</span>
                                <span v-else>Accept</span>
                            </button>
                            
                            <button @click="makeDecision('reject')"
                                :disabled="isProcessing || reviewForm.processing"
                                class="px-6 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors shadow-sm hover:shadow-md"
                                :class="{ 'opacity-50 cursor-not-allowed': isProcessing || reviewForm.processing }">
                                Reject
                            </button>
                            
                            <button @click="makeDecision('need_edit')"
                                :disabled="isProcessing || reviewForm.processing"
                                class="px-6 py-2 bg-orange-600 text-white rounded-lg font-semibold hover:bg-orange-700 transition-colors shadow-sm hover:shadow-md"
                                :class="{ 'opacity-50 cursor-not-allowed': isProcessing || reviewForm.processing }">
                                Need Revision
                            </button>
                        </div>

                        <div v-if="idea.feedback" class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">Current Feedback:</div>
                            <div class="text-blue-700 dark:text-blue-400 text-sm">{{ idea.feedback }}</div>
                        </div>
                    </div>

                    <!-- Score Section -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Scoring</h2>
                        
                        <div class="mb-6">
                            <FormInput
                                v-model="reviewForm.score"
                                label="Score (0-100)"
                                type="number"
                                placeholder="Enter score from 0 to 100"
                                :min="0"
                                :max="100"
                                :error="reviewForm.errors.score"
                            />
                        </div>

                        <button @click="updateScore"
                            :disabled="reviewForm.processing || !reviewForm.score"
                            class="w-full px-4 py-2 rounded-lg font-semibold text-white transition-all duration-200"
                            :style="{ 
                                background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                            }"
                            :class="{ 'opacity-50 cursor-not-allowed': reviewForm.processing || !reviewForm.score }">
                            Update Score
                        </button>

                        <div v-if="idea.score" class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Current Score</div>
                            <div class="text-3xl font-bold" :style="{ color: themeColor.primary }">
                                {{ idea.score }}/100
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Supervisor Assignment -->
                <div v-if="availableSupervisors.length > 0" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mt-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Assign Supervisor</h2>
                    
                    <div class="flex items-end gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Track Supervisor
                            </label>
                            <select v-model="supervisorForm.supervisor_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[var(--theme-primary)] focus:border-transparent">
                                <option value="">Select Supervisor</option>
                                <option v-for="supervisor in availableSupervisors" :key="supervisor.id" :value="supervisor.id">
                                    {{ supervisor.name }} - {{ supervisor.email }}
                                </option>
                            </select>
                        </div>
                        
                        <button @click="assignSupervisor"
                            :disabled="supervisorForm.processing || !supervisorForm.supervisor_id"
                            class="px-6 py-2 rounded-lg font-semibold text-white transition-colors"
                            :style="{ 
                                background: supervisorForm.supervisor_id ? `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` : '#9CA3AF'
                            }"
                            :class="{ 'cursor-not-allowed': supervisorForm.processing || !supervisorForm.supervisor_id }">
                            Assign
                        </button>
                    </div>
                </div>
            </div>

            <!-- History Tab -->
            <div v-if="activeTab === 'history'">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Review History</h2>
                    
                    <div v-if="idea.audit_logs && idea.audit_logs.length > 0" class="space-y-6">
                        <div v-for="log in idea.audit_logs" :key="log.id" 
                            class="relative pl-8 pb-6 border-l-2 border-gray-200 dark:border-gray-700 last:border-l-0 last:pb-0">
                            <!-- Timeline dot -->
                            <div class="absolute -left-2 top-0 w-4 h-4 rounded-full bg-white dark:bg-gray-800 border-2"
                                :style="{ borderColor: themeColor.primary }">
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                            {{ log.action || 'Action' }}
                                        </span>
                                        <span v-if="log.user" class="text-sm font-medium text-gray-900 dark:text-white">
                                            by {{ log.user.name }}
                                        </span>
                                    </div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ formatDate(log.created_at) }}
                                    </span>
                                </div>
                                
                                <div v-if="log.notes" class="text-gray-700 dark:text-gray-300 mb-3">
                                    {{ log.notes }}
                                </div>
                                
                                <div v-if="log.metadata" class="text-xs text-gray-500 dark:text-gray-400">
                                    <div v-if="log.metadata.score" class="mb-1">
                                        Score: {{ log.metadata.score }}/100
                                    </div>
                                    <div v-if="log.metadata.previous_status" class="mb-1">
                                        Status changed from: {{ formatStatus(log.metadata.previous_status) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No review history</h3>
                        <p class="mt-1 text-sm text-gray-500">This idea hasn't been reviewed yet.</p>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-8 flex items-center justify-between">
                <Link :href="route('system-admin.ideas.index')"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Ideas
                </Link>
                
                <!-- Additional Actions -->
                <div class="flex items-center space-x-3">
                    <Link :href="route('system-admin.ideas.review', idea.id)"
                        class="inline-flex items-center px-4 py-2 rounded-lg font-semibold text-white transition-all duration-200 shadow-sm hover:shadow-md"
                        :style="{ 
                            background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                        }">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Review Idea
                    </Link>
                    
                    <button @click="window.print()" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </button>
                    
                    <Link :href="route('system-admin.ideas.export', { id: idea.id })" 
                        class="inline-flex items-center px-4 py-2 rounded-lg font-semibold text-white transition-all duration-200 shadow-sm hover:shadow-md"
                        :style="{ 
                            background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                        }">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Details
                    </Link>
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

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }
    
    .print\:block {
        display: block !important;
    }
}
</style>
