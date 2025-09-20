<template>
    <Head title="Our Idea - Team Member" />
    <Default>
        <div class="w-full h-full overflow-hidden bg-gray-50 dark:bg-gray-900" :style="themeStyles">
            <!-- Our Idea Page with Tabs based on Figma Design -->
            <div class="self-stretch flex flex-col items-start justify-start text-gray-900 dark:text-white font-space-grotesk">
                <div class="w-full overflow-hidden flex flex-col items-start justify-start max-w-[960px]">
                    <!-- Page Header -->
                    <div class="self-stretch flex flex-row items-start justify-between flex-wrap content-start p-4 text-[32px]">
                        <div class="flex flex-col items-start justify-start gap-3 min-w-[288px]">
                            <div class="flex flex-col items-start justify-start">
                                <b class="self-stretch relative leading-10">
                                    {{ idea ? `Idea: ${idea.title}` : 'No Idea Submitted Yet' }}
                                </b>
                            </div>
                            <div v-if="idea" class="w-[589px] flex flex-col items-start justify-start text-sm"
                                :style="{ color: themeColor.primary }">
                                <div class="self-stretch relative leading-[21px]">Submitted by {{ idea.user?.name || team?.leader?.name }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs Navigation -->
                    <div v-if="idea" class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-3 text-sm">
                        <div class="self-stretch border-b border-gray-200 dark:border-gray-600 flex flex-row items-start justify-start py-0 px-4 gap-8">
                            <button @click="activeTab = 'overview'"
                                class="flex flex-col items-center justify-center pt-4 px-0 pb-[13px] border-b-[3px] transition-colors duration-200"
                                :class="activeTab === 'overview' ? 'border-current' : 'border-transparent'">
                                <div class="flex flex-col items-start justify-start">
                                    <b class="self-stretch relative leading-[21px]"
                                        :class="activeTab === 'overview' ? 'text-current' : 'text-gray-500 dark:text-gray-400'"
                                        :style="activeTab === 'overview' ? { color: themeColor.primary } : {}">
                                        Overview
                                    </b>
                                </div>
                            </button>
                            <button @click="activeTab = 'comments'"
                                class="flex flex-col items-center justify-center pt-4 px-0 pb-[13px] border-b-[3px] transition-colors duration-200"
                                :class="activeTab === 'comments' ? 'border-current' : 'border-transparent'">
                                <div class="flex flex-col items-start justify-start">
                                    <b class="self-stretch relative leading-[21px]"
                                        :class="activeTab === 'comments' ? 'text-current' : 'text-gray-500 dark:text-gray-400'"
                                        :style="activeTab === 'comments' ? { color: themeColor.primary } : {}">
                                        Comments
                                    </b>
                                </div>
                            </button>
                            <button @click="activeTab = 'instructions'"
                                class="flex flex-col items-center justify-center pt-4 px-0 pb-[13px] border-b-[3px] transition-colors duration-200"
                                :class="activeTab === 'instructions' ? 'border-current' : 'border-transparent'">
                                <div class="flex flex-col items-start justify-start">
                                    <b class="self-stretch relative leading-[21px]"
                                        :class="activeTab === 'instructions' ? 'text-current' : 'text-gray-500 dark:text-gray-400'"
                                        :style="activeTab === 'instructions' ? { color: themeColor.primary } : {}">
                                        Instructions
                                    </b>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div v-if="idea" class="self-stretch flex flex-col items-start justify-start p-4 gap-6">

                        <!-- Overview Tab -->
                        <div v-if="activeTab === 'overview'" class="self-stretch flex flex-col gap-6">
                            <!-- Idea Details Section -->
                            <div class="self-stretch h-[60px] flex flex-col items-start justify-start pt-5 px-0 pb-3 text-[22px]">
                                <b class="self-stretch relative leading-7">Idea Details</b>
                            </div>

                            <div class="self-stretch flex flex-col items-start justify-start gap-6 text-sm">
                                <div class="self-stretch flex-1 flex flex-row items-start justify-start gap-6">
                                    <!-- Description -->
                                    <div class="self-stretch w-[186px] border-t border-gray-200 dark:border-gray-600 flex flex-col items-start justify-start py-5 px-0"
                                        :style="{ color: themeColor.primary }">
                                        <div class="self-stretch flex flex-row items-start justify-start">
                                            <div class="w-[186px] flex flex-col items-start justify-start">
                                                <div class="self-stretch relative leading-[21px]">Description</div>
                                            </div>
                                        </div>
                                        <div class="self-stretch flex flex-row items-start justify-start text-gray-700 dark:text-gray-300">
                                            <div class="w-[186px] flex flex-col items-start justify-start">
                                                <div class="self-stretch relative leading-[21px]">{{ idea.description || 'No description provided' }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Problem -->
                                    <div class="self-stretch flex-1 border-t border-gray-200 dark:border-gray-600 flex flex-col items-start justify-start py-5 px-0"
                                        :style="{ color: themeColor.primary }">
                                        <div class="self-stretch flex flex-row items-start justify-start">
                                            <div class="flex-1 flex flex-col items-start justify-start">
                                                <div class="self-stretch relative leading-[21px]">Problem</div>
                                            </div>
                                        </div>
                                        <div class="self-stretch flex flex-row items-start justify-start text-gray-700 dark:text-gray-300">
                                            <div class="flex-1 flex flex-col items-start justify-start">
                                                <div class="self-stretch relative leading-[21px]">{{ idea.problem || 'Problem statement not provided' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Solution -->
                                <div class="self-stretch flex-1 flex flex-row items-start justify-start">
                                    <div class="self-stretch w-[186px] border-t border-gray-200 dark:border-gray-600 flex flex-col items-start justify-start py-5 px-0"
                                        :style="{ color: themeColor.primary }">
                                        <div class="self-stretch flex flex-row items-start justify-start">
                                            <div class="w-[186px] flex flex-col items-start justify-start">
                                                <div class="self-stretch relative leading-[21px]">Solution</div>
                                            </div>
                                        </div>
                                        <div class="self-stretch flex flex-row items-start justify-start text-gray-700 dark:text-gray-300">
                                            <div class="w-[186px] flex flex-col items-start justify-start">
                                                <div class="self-stretch relative leading-[21px]">{{ idea.solution || 'Solution not provided' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Current Stage -->
                            <div class="self-stretch h-[60px] flex flex-col items-start justify-start pt-5 px-0 pb-3 text-[22px]">
                                <b class="self-stretch relative leading-7">Current Stage</b>
                            </div>

                            <div class="self-stretch flex flex-col items-start justify-start gap-3">
                                <div class="self-stretch flex flex-row items-start justify-between">
                                    <div class="flex flex-col items-start justify-start">
                                        <div class="self-stretch relative leading-6 font-medium">{{ idea.status || 'Implementation' }}</div>
                                    </div>
                                </div>
                                <div class="self-stretch rounded bg-gray-200 dark:bg-gray-700 flex flex-col items-start justify-start h-2">
                                    <div class="rounded h-2"
                                        :style="{ backgroundColor: themeColor.primary, width: getProgressWidth() }" />
                                </div>
                                <div class="self-stretch flex flex-col items-start justify-start text-sm"
                                    :style="{ color: themeColor.primary }">
                                    <div class="self-stretch relative leading-[21px]">{{ getProgressPercentage() }}% Complete</div>
                                </div>
                            </div>

                            <!-- Files/Attachments -->
                            <div v-if="idea.files?.length" class="self-stretch">
                                <div class="self-stretch h-[60px] flex flex-col items-start justify-start pt-5 px-0 pb-3 text-[22px]">
                                    <b class="self-stretch relative leading-7">Related Documents</b>
                                </div>

                                <div class="space-y-2">
                                    <a v-for="file in idea.files" :key="file.id"
                                        :href="route('team-member.idea.download-file', { idea: idea.id, file: file.id })"
                                        class="self-stretch bg-gray-50 dark:bg-gray-800 h-14 flex flex-row items-center justify-start py-0 px-4 gap-4 min-h-[56px] hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                        <svg class="w-10 h-10 rounded-lg p-2 bg-gray-100 dark:bg-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div class="flex-1 overflow-hidden flex flex-col items-start justify-start">
                                            <div class="self-stretch relative leading-6 overflow-hidden text-ellipsis whitespace-nowrap">{{ file.original_name || file.name }}</div>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Comments Tab - Team Chat -->
                        <div v-if="activeTab === 'comments'" class="self-stretch flex flex-col gap-6">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Team Chat</h3>
                            <!-- Team Comments List -->
                            <div class="self-stretch flex flex-col gap-4">
                                <div v-if="teamComments?.length" class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ teamComments.length }} message{{ teamComments.length !== 1 ? 's' : '' }}
                                </div>

                                <div v-if="teamComments?.length" class="space-y-6">
                                    <div v-for="comment in teamComments" :key="comment.id"
                                        class="bg-white dark:bg-gray-800 rounded-lg p-6">
                                        <!-- Comment Header -->
                                        <div class="flex items-center gap-3 mb-4">
                                            <img class="w-10 h-10 rounded-full object-cover"
                                                :src="comment.user?.avatar || '/default-avatar.png'"
                                                :alt="comment.user?.name || 'User'" />
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2">
                                                    <div class="text-sm font-semibold">{{ comment.user?.name || 'Anonymous' }}</div>
                                                    <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300">
                                                        {{ comment.user?.user_type === 'team_leader' ? 'Team Leader' : 'Team Member' }}
                                                    </span>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(comment.created_at) }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Comment Content -->
                                        <div class="text-base leading-6 text-gray-700 dark:text-gray-300">
                                            {{ comment.comment }}
                                        </div>
                                    </div>
                                </div>

                                <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    No team messages yet. Start the conversation!
                                </div>
                            </div>

                            <!-- Add Comment Form -->
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                                <div class="flex items-start gap-3 mb-4">
                                    <img class="w-10 h-10 rounded-full object-cover"
                                        :src="$page.props.auth.user.avatar || '/default-avatar.png'"
                                        :alt="$page.props.auth.user.name" />
                                    <div class="flex-1">
                                        <div class="text-sm font-semibold">{{ $page.props.auth.user.name }}</div>
                                    </div>
                                </div>

                                <form @submit.prevent="submitComment" class="space-y-4">
                                    <textarea v-model="commentForm.comment"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white p-4 min-h-[100px]"
                                        placeholder="Share your thoughts with the team..."
                                        required></textarea>

                                    <div class="flex justify-between items-center">
                                        <button type="button" @click="cancelComment" class="text-gray-600 dark:text-gray-400">
                                            Cancel
                                        </button>
                                        <button type="submit"
                                            class="rounded-lg px-4 py-2 text-white font-medium"
                                            :style="{ backgroundColor: themeColor.primary }"
                                            :disabled="!commentForm.comment.trim()">
                                            Send Message
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Instructions Tab -->
                        <div v-if="activeTab === 'instructions'" class="self-stretch flex flex-col gap-6 overflow-y-auto">
                            <!-- Active Instructions Section -->
                            <div class="self-stretch">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Active Instructions</h3>

                                <div v-if="instructions && instructions.length > 0" class="space-y-4">
                                    <div v-for="instruction in instructions" :key="instruction.id"
                                        class="bg-white dark:bg-gray-800 rounded-lg p-6 border-l-4"
                                        :class="{
                                            'border-orange-500': instruction.user_role === 'supervisor',
                                            'border-blue-500': instruction.user_role === 'team_leader'
                                        }">
                                        <div class="flex items-start gap-4">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                                :class="{
                                                    'bg-orange-100 dark:bg-orange-900': instruction.user_role === 'supervisor',
                                                    'bg-blue-100 dark:bg-blue-900': instruction.user_role === 'team_leader'
                                                }">
                                                <span class="text-sm font-medium"
                                                    :class="{
                                                        'text-orange-600 dark:text-orange-300': instruction.user_role === 'supervisor',
                                                        'text-blue-600 dark:text-blue-300': instruction.user_role === 'team_leader'
                                                    }">
                                                    {{ instruction.user?.name?.charAt(0) || (instruction.user_role === 'supervisor' ? 'S' : 'L') }}
                                                </span>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between mb-2">
                                                    <div>
                                                        <span class="font-semibold text-gray-900 dark:text-white">{{ instruction.user?.name || 'Unknown' }}</span>
                                                        <span class="ml-2 text-xs px-2 py-1 rounded-full"
                                                            :class="{
                                                                'bg-orange-100 dark:bg-orange-900 text-orange-600 dark:text-orange-300': instruction.user_role === 'supervisor',
                                                                'bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300': instruction.user_role === 'team_leader'
                                                            }">
                                                            {{ instruction.user_role === 'supervisor' ? 'Track Supervisor' : 'Team Leader' }}
                                                        </span>
                                                    </div>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ formatDate(instruction.created_at) }}
                                                    </span>
                                                </div>
                                                <div class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                                                    {{ instruction.instruction_text }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div v-else class="bg-gray-50 dark:bg-gray-800 rounded-lg p-8 text-center">
                                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-gray-500 dark:text-gray-400">No instructions available yet.</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Instructions from supervisors or team leaders will appear here.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No Idea State -->
                    <div v-else class="self-stretch flex flex-col items-center justify-center py-12">
                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Idea Submitted</h3>
                        <p class="text-gray-600 dark:text-gray-400">Your team hasn't submitted an idea yet. Please wait for your team leader to submit one.</p>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'

const props = defineProps({
    idea: Object,
    team: Object,
    teamComments: {
        type: Array,
        default: () => []
    },
    supervisorFeedback: {
        type: Array,
        default: () => []
    },
    instructions: {
        type: Array,
        default: () => []
    }
})

// Tab management
const activeTab = ref('overview')

// Comment form
const commentForm = useForm({
    comment: ''
})

// Theme color setup
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

const formatDate = (dateString) => {
    if (!dateString) return 'N/A'
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

// Progress calculation
const getProgressPercentage = () => {
    if (!props.idea) return 0

    // You can customize this logic based on your idea progress tracking
    const status = props.idea.status?.toLowerCase()
    switch (status) {
        case 'draft': return 10
        case 'submitted': return 30
        case 'under_review': return 50
        case 'approved': return 70
        case 'implementation': return 60
        case 'completed': return 100
        default: return 60
    }
}

const getProgressWidth = () => {
    return `${getProgressPercentage()}%`
}

// Comment functions
const submitComment = () => {
    if (!commentForm.comment.trim()) return

    commentForm.post(route('team-member.idea.comment', props.idea.id), {
        onSuccess: () => {
            commentForm.reset()
        },
        preserveScroll: true
    })
}

const cancelComment = () => {
    commentForm.reset()
}
</script>

<style scoped>
/* Theme styles are applied via CSS variables */
</style>