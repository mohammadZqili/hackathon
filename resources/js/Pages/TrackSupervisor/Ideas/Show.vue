<script setup>
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL, direction, locale } = useLocalization()
import { Head, router, useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import Default from '@/Layouts/Default.vue'
import {
    DocumentIcon,
    PaperClipIcon,
    PaperAirplaneIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    idea: Object,
    reviewHistory: Array,
    scoring: Object,
})

// Comments form
const commentForm = useForm({
    comment: ''
})

const submitComment = () => {
    if (!commentForm.comment.trim()) return

    commentForm.post(route('track-supervisor.ideas.add-comment', props.idea.id), {
        preserveScroll: true,
        onSuccess: () => {
            commentForm.reset()
            // Reload the page to show the new comment
            router.reload({ only: ['idea'] })
        },
        onError: (errors) => {
            console.error('Error submitting comment:', errors)
        }
    })
}

const activeTab = ref('overview')

// Get theme color
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

const submitChanges = () => {
    if (!decisionForm.status) {
        alert('Please select a decision status')
        return
    }

    decisionForm.post(route('track-supervisor.ideas.process-review', props.idea.id), {
        onSuccess: () => {
            router.visit(route('track-supervisor.ideas.index'))
        },
    })
}

const formatDateTime = (date) => {
    if (!date) return 'N/A'
    const d = new Date(date)
    return d.toLocaleDateString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    }) + ' ' + d.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    })
}

// Get actual uploaded documents
const documents = computed(() => {
    return props.idea?.files || []
})

// Download document
const downloadDocument = (file) => {
    if (file.file_path) {
        // Create a download link
        const link = document.createElement('a')
        link.href = `/storage/${file.file_path}`
        link.download = file.original_name || file.file_name || 'document'
        link.target = '_blank'
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
    } else if (file.url) {
        window.open(file.url, '_blank')
    }
}
</script>

<template>
    <Head :title="`Idea: ${idea.title}`" />

    <Default>
        <div class="max-w-5xl mx-auto px-4 py-6" :style="themeStyles">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Idea: {{ idea.title || 'Streamlined Onboarding Process' }}
                </h1>
                <p class="text-sm mt-2" :style="{ color: themeColor.primary }">
                    Submitted by {{ idea.team?.name || 'Saleh Team' }}
                </p>
            </div>

            <!-- Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex gap-8">
                        <button
                            @click="activeTab = 'overview'"
                            :class="activeTab === 'overview' ? 'border-gray-900 dark:border-white text-gray-900 dark:text-white font-bold' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                            class="py-4 px-1 border-b-3 font-medium text-sm transition-colors"
                        >
                            Overview
                        </button>
                        <button
                            @click="activeTab = 'comments'"
                            :class="activeTab === 'comments' ? 'border-gray-900 dark:border-white text-gray-900 dark:text-white font-bold' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                            class="py-4 px-1 border-b-3 font-medium text-sm transition-colors"
                            :style="activeTab === 'comments' ? { color: themeColor.primary, borderColor: themeColor.primary } : {}"
                        >
                            Comments
                        </button>
                        <button
                            @click="activeTab = 'instructions'"
                            :class="activeTab === 'instructions' ? 'border-gray-900 dark:border-white text-gray-900 dark:text-white font-bold' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                            class="py-4 px-1 border-b-3 font-medium text-sm transition-colors"
                            :style="activeTab === 'instructions' ? { color: themeColor.primary, borderColor: themeColor.primary } : {}"
                        >
                            Instructions
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Overview Tab Content -->
            <div v-show="activeTab === 'overview'" class="space-y-8">
                <!-- Idea Details Section -->
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Idea Details</h2>

                    <!-- Details Grid -->
                    <div class="space-y-0">
                        <!-- Row 1: Team Name and Submission Date -->
                        <div class="grid grid-cols-1 md:grid-cols-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="py-5 pr-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Team Name</div>
                                <div class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ idea.team?.name || 'Marketing' }}
                                </div>
                            </div>
                            <div class="col-span-3 py-5 border-l border-gray-200 dark:border-gray-700 pl-6">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Submission Date/Time</div>
                                <div class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ formatDateTime(idea.created_at) }}
                                </div>
                            </div>
                        </div>

                        <!-- Row 2: Idea Leader and Track -->
                        <div class="grid grid-cols-1 md:grid-cols-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="py-5 pr-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Idea Leader</div>
                                <div class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ idea.team?.leader?.name || 'Sarah Chen' }}
                                </div>
                            </div>
                            <div class="col-span-3 py-5 border-l border-gray-200 dark:border-gray-700 pl-6">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Track</div>
                                <div class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ idea.track?.name || 'Marketing' }}
                                </div>
                            </div>
                        </div>

                        <!-- Row 3: Hackathon Edition -->
                        <div class="border-t border-gray-200 dark:border-gray-700 py-5">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Hackathon Edition</div>
                            <div class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                {{ idea.team?.edition?.name || 'Summer 2024' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Description</h2>
                    <p class="text-base text-gray-700 dark:text-gray-300 leading-6">
                        {{ idea.description || 'This campaign aims to leverage social media influencers and interactive content to increase brand awareness and engagement. The strategy includes a series of online contests, collaborative posts with influencers, and a user-generated content campaign.' }}
                    </p>
                </div>

                <!-- Related Documents Section -->
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Related Documents</h2>
                    <div v-if="documents.length > 0" class="space-y-0">
                        <div v-for="doc in documents" :key="doc.id"
                             @click="downloadDocument(doc)"
                             class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700/30 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-0">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                 :style="{ backgroundColor: themeColor.primary + '20' }">
                                <DocumentIcon class="w-6 h-6" :style="{ color: themeColor.primary }" />
                            </div>
                            <div class="flex-1">
                                <p class="text-base font-medium text-gray-900 dark:text-white">
                                    {{ doc.original_name || doc.file_name || 'Document' }}
                                </p>
                                <p v-if="doc.file_size" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ (doc.file_size / 1024).toFixed(2) }} KB
                                    <span v-if="doc.file_type" class="ml-2">• {{ doc.file_type.toUpperCase() }}</span>
                                </p>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Click to download
                            </div>
                        </div>
                    </div>
                    <div v-else class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <p class="text-gray-500 dark:text-gray-400 text-center">
                            No documents uploaded for this idea
                        </p>
                    </div>
                </div>

                <!-- Make Decision and Score Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6">
                    <!-- Make Decision Section -->
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Make Decision ...</h2>

                        <!-- Decision Buttons -->
                        <div class="flex gap-3 mb-4">
                            <button
                                @click="submitDecision('accepted')"
                                :class="decisionForm.status === 'accepted' ? 'text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                                :style="decisionForm.status === 'accepted' ? { backgroundColor: themeColor.primary } : {}"
                                class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all hover:opacity-90">
                                Accept
                            </button>
                            <button
                                @click="submitDecision('rejected')"
                                :class="decisionForm.status === 'rejected' ? 'bg-gray-700 dark:bg-gray-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                                class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all hover:opacity-90">
                                Reject
                            </button>
                            <button
                                @click="submitDecision('needs_revision')"
                                :class="decisionForm.status === 'needs_revision' ? 'bg-gray-700 dark:bg-gray-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                                class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all hover:opacity-90">
                                Need Edit
                            </button>
                        </div>

                        <!-- Feedback Textarea -->
                        <div>
                            <textarea
                                v-model="decisionForm.feedback"
                                placeholder="Provide feedback or required changes for the idea's acceptance"
                                class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 transition-all resize-none"
                                :style="{
                                    '--tw-ring-color': themeColor.primary,
                                    color: themeColor.primary + '90'
                                }"
                                rows="6"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Score Section -->
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Score</h2>

                        <!-- Score Input -->
                        <div class="mb-6">
                            <input
                                v-model.number="decisionForm.score"
                                type="number"
                                min="0"
                                max="100"
                                placeholder="Add Score From 100"
                                class="w-full px-4 py-3 border rounded-xl text-base bg-gray-50 dark:bg-gray-700/30 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 transition-all"
                                :style="{
                                    borderColor: themeColor.primary + '30',
                                    backgroundColor: themeColor.primary + '05',
                                    color: themeColor.primary,
                                    '--tw-ring-color': themeColor.primary
                                }"
                            />
                        </div>

                        <!-- Submit Button -->
                        <button
                            @click="submitChanges"
                            :disabled="!decisionForm.status || decisionForm.processing"
                            class="w-full px-4 py-3 text-white font-bold rounded-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            :style="{
                                background: decisionForm.status && !decisionForm.processing
                                    ? `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                                    : 'linear-gradient(135deg, #9ca3af, #6b7280)'
                            }">
                            {{ decisionForm.processing ? 'Processing...' : 'Submit Review' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Comments Tab Content -->
            <div v-show="activeTab === 'comments'" class="space-y-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Comments</h2>

                <!-- Comments Container -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <!-- Comments List -->
                    <div class="max-h-96 overflow-y-auto p-4 space-y-4">
                        <div v-if="idea.comments && idea.comments.length > 0">
                            <div v-for="comment in idea.comments" :key="comment.id" class="flex gap-3">
                                <!-- Avatar -->
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ comment.user?.name?.charAt(0).toUpperCase() || 'U' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Comment Content -->
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ comment.user?.name || 'User' }}
                                        </span>
                                        <span v-if="comment.is_supervisor"
                                              class="text-xs px-2 py-0.5 rounded-full text-white"
                                              :style="{ backgroundColor: themeColor.primary }">
                                            Supervisor
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ formatDateTime(comment.created_at) }}
                                        </span>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg px-3 py-2">
                                        <p class="text-sm text-gray-900 dark:text-white">{{ comment.comment }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                            No comments yet. Start the conversation!
                        </div>
                    </div>

                    <!-- Comment Form -->
                    <div class="border-t border-gray-200 dark:border-gray-700 p-4">
                        <form @submit.prevent="submitComment" class="flex gap-3 items-end">
                            <div class="flex-1">
                                <textarea
                                    v-model="commentForm.comment"
                                    placeholder="Type your message here..."
                                    rows="2"
                                    class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 transition-all resize-none text-sm"
                                    :style="{
                                        '--tw-ring-color': themeColor.primary
                                    }"
                                    @keydown.enter.prevent="submitComment"
                                ></textarea>
                            </div>
                            <button
                                type="submit"
                                :disabled="!commentForm.comment.trim() || commentForm.processing"
                                class="p-2 rounded-lg text-white transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                :style="{ backgroundColor: themeColor.primary }"
                            >
                                <PaperAirplaneIcon class="w-5 h-5" />
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Instructions Tab Content -->
            <div v-show="activeTab === 'instructions'" class="space-y-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Track Instructions</h2>

                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div v-if="idea.track?.instructions" class="prose dark:prose-invert max-w-none">
                        <div v-html="idea.track.instructions"></div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <p>No specific instructions have been provided for this track yet.</p>
                        <p class="text-sm mt-2">Please follow the general hackathon guidelines and feel free to ask questions in the comments.</p>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

<style scoped>
/* Focus states with theme */
textarea:focus,
input:focus {
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1);
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

/* Tab border width */
.border-b-3 {
    border-bottom-width: 3px;
}
</style>