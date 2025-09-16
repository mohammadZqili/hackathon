<template>
    <Head title="Our Idea - Team Lead" />
    <Default>
        <div class="w-full h-full overflow-hidden" :style="themeStyles">
            <!-- Our Idea Page with Tabbed Interface based on Figma Design -->
            <div class="self-stretch flex flex-col items-start justify-start text-gray-900 dark:text-white font-space-grotesk">
                <div class="w-full overflow-hidden flex flex-col items-start justify-start max-w-[960px]">
                    <!-- Page Header -->
                    <div class="self-stretch flex flex-row items-start justify-between flex-wrap content-start p-4 text-[32px]">
                        <div class="flex flex-col items-start justify-start gap-3 min-w-[288px]">
                            <div class="flex flex-col items-start justify-start">
                                <b class="self-stretch relative leading-10">
                                    {{ idea ? `Idea: ${idea.title}` : 'Submit Your Hackathon Idea' }}
                                </b>
                            </div>
                            <div v-if="idea" class="w-[589px] flex flex-col items-start justify-start text-sm"
                                :style="{ color: themeColor.primary }">
                                <div class="self-stretch relative leading-[21px]">Submitted by {{ idea.user?.name || $page.props.auth.user.name }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab Navigation -->
                    <div v-if="idea" class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-3 text-sm"
                        :style="{ color: themeColor.primary }">
                        <div class="self-stretch border-b border-gray-300 dark:border-gray-600 flex flex-row items-start justify-start py-0 px-4 gap-8">
                            <button @click="activeTab = 'overview'"
                                class="border-b-[3px] flex flex-col items-center justify-center pt-4 px-0 pb-[13px] transition-colors"
                                :class="activeTab === 'overview' ? 'border-gray-400' : 'border-transparent'"
                                :style="activeTab === 'overview' ? { borderColor: themeColor.primary } : {}">
                                <div class="flex flex-col items-start justify-start">
                                    <b class="self-stretch relative leading-[21px]"
                                        :style="activeTab === 'overview' ? { color: themeColor.primary } : { color: 'inherit' }">
                                        Overview
                                    </b>
                                </div>
                            </button>
                            <button @click="activeTab = 'comments'"
                                class="border-b-[3px] flex flex-col items-center justify-center pt-4 px-0 pb-[13px] transition-colors"
                                :class="activeTab === 'comments' ? 'border-gray-400' : 'border-transparent'"
                                :style="activeTab === 'comments' ? { borderColor: themeColor.primary } : {}">
                                <div class="flex flex-col items-start justify-start">
                                    <b class="self-stretch relative leading-[21px]"
                                        :style="activeTab === 'comments' ? { color: themeColor.primary } : { color: 'inherit' }">
                                        Comments
                                    </b>
                                </div>
                            </button>
                            <button @click="activeTab = 'instructions'"
                                class="border-b-[3px] flex flex-col items-center justify-center pt-4 px-0 pb-[13px] transition-colors"
                                :class="activeTab === 'instructions' ? 'border-gray-400' : 'border-transparent'"
                                :style="activeTab === 'instructions' ? { borderColor: themeColor.primary } : {}">
                                <div class="flex flex-col items-start justify-start">
                                    <b class="self-stretch relative leading-[21px]"
                                        :style="activeTab === 'instructions' ? { color: themeColor.primary } : { color: 'inherit' }">
                                        Instructions
                                    </b>
                                </div>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Tab Content -->
                    <div v-if="idea" class="self-stretch flex-1 min-h-0">
                        <!-- Overview Tab -->
                        <div v-if="activeTab === 'overview'" class="self-stretch h-full">
                            <div class="self-stretch h-[60px] flex flex-col items-start justify-start pt-5 px-4 pb-3 box-border text-[22px]">
                                <b class="self-stretch relative leading-7">Idea Details</b>
                            </div>
                            <div class="self-stretch flex flex-col items-start justify-start p-4 text-sm"
                                :style="{ color: themeColor.primary }">
                                <div class="self-stretch flex-1 flex flex-row items-start justify-start">
                                    <div class="self-stretch w-[928px] border-t border-gray-300 dark:border-gray-600 box-border flex flex-col items-start justify-start py-5 px-0">
                                        <div class="self-stretch flex flex-row items-start justify-start">
                                            <div class="w-[186px] flex flex-col items-start justify-start">
                                                <div class="self-stretch relative leading-[21px]">Description</div>
                                            </div>
                                        </div>
                                        <div class="self-stretch flex flex-row items-start justify-start text-gray-900 dark:text-white">
                                            <div class="w-[928px] flex flex-col items-start justify-start">
                                                <div class="self-stretch relative leading-[21px]">{{ idea.description || 'No description provided.' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="self-stretch h-[60px] flex flex-col items-start justify-start pt-5 px-4 pb-3 box-border text-[22px]">
                                <b class="self-stretch relative leading-7">Current Stage</b>
                            </div>
                            <div class="self-stretch flex flex-col items-start justify-start p-4 gap-3">
                                <div class="self-stretch flex flex-row items-start justify-between">
                                    <div class="flex flex-col items-start justify-start">
                                        <div class="self-stretch relative leading-6 font-medium capitalize">{{ idea.status || 'Draft' }}</div>
                                    </div>
                                </div>
                                <div class="self-stretch rounded bg-gray-200 dark:bg-gray-700 flex flex-col items-start justify-start">
                                    <div class="rounded h-2"
                                        :style="{ 
                                            backgroundColor: themeColor.primary,
                                            width: getProgressWidth(idea.status)
                                        }" />
                                </div>
                                <div class="self-stretch flex flex-col items-start justify-start text-sm"
                                    :style="{ color: themeColor.primary }">
                                    <div class="self-stretch relative leading-[21px]">{{ getProgressText(idea.status) }}</div>
                                </div>
                            </div>
                            
                            <!-- Files Section -->
                            <div v-if="idea.files?.length" class="self-stretch h-[60px] flex flex-col items-start justify-start pt-5 px-4 pb-3 box-border text-[22px]">
                                <b class="self-stretch relative leading-7">Related Documents</b>
                            </div>
                            <div v-for="file in idea.files" :key="file.id"
                                class="self-stretch bg-gray-50 dark:bg-gray-800 h-14 flex flex-row items-center justify-start py-0 px-4 box-border gap-4 min-h-[56px]">
                                <div class="w-10 rounded-lg h-10 bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 overflow-hidden flex flex-col items-start justify-start">
                                    <div class="self-stretch relative leading-6 overflow-hidden text-ellipsis whitespace-nowrap">{{ file.name }}</div>
                                </div>
                            </div>
                            
                            <!-- Edit Button -->
                            <div class="self-stretch flex flex-row items-start justify-end py-3 px-4 text-center text-white">
                                <Link :href="route('team-lead.idea.edit-my')"
                                    class="w-52 rounded-xl flex flex-row items-center justify-center py-3 px-4 box-border max-h-[40px]"
                                    :style="{ background: `linear-gradient(rgba(79, 150, 115, 0.75), rgba(79, 150, 115, 0.75)), ${themeColor.primary}` }">
                                    <b class="relative leading-[14.66px]">Edit Idea</b>
                                </Link>
                            </div>
                        </div>
                        
                        <!-- Comments Tab -->
                        <div v-if="activeTab === 'comments'" class="self-stretch h-full overflow-y-auto">
                            <!-- Supervisor Feedback -->
                            <div class="self-stretch h-[60px] flex flex-row items-start justify-start pt-5 px-4 pb-3 box-border text-[22px]">
                                <b class="relative leading-7">Supervisor Feedback</b>
                            </div>
                            
                            <!-- Comments List -->
                            <div v-for="comment in idea.comments" :key="comment.id" 
                                class="self-stretch flex flex-row items-start justify-start p-4 gap-3">
                                <div class="w-10 relative rounded-[20px] h-10 overflow-hidden shrink-0 bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ comment.user?.name?.charAt(0) || 'U' }}
                                    </span>
                                </div>
                                <div class="flex-1 h-[147px] flex flex-col items-start justify-start">
                                    <div class="self-stretch flex flex-row items-start justify-start gap-3">
                                        <div class="flex flex-col items-start justify-start">
                                            <b class="self-stretch relative leading-[21px]">{{ comment.user?.role || 'Supervisor' }}: {{ comment.user?.name }}</b>
                                        </div>
                                        <div class="flex flex-col items-start justify-start text-gray-500 dark:text-gray-400">
                                            <div class="self-stretch relative leading-[21px]">{{ formatDate(comment.created_at) }}</div>
                                        </div>
                                    </div>
                                    <div class="self-stretch flex flex-col items-start justify-start">
                                        <div class="self-stretch relative leading-[21px]">{{ comment.comment }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Reply Form -->
                            <div class="self-stretch relative h-[250px] bg-gray-100 dark:bg-gray-800 rounded-lg mx-4 mb-4">
                                <div class="absolute top-[206px] left-[13.92px] rounded-lg flex flex-col items-center justify-center py-2 px-4 box-border text-center text-white"
                                    :style="{ backgroundColor: themeColor.primary }">
                                    <button @click="submitReply" class="relative leading-4 font-medium">Reply</button>
                                </div>
                                <div class="absolute w-[6.63%] top-[84%] left-[11.75%] text-base leading-6 cursor-pointer"
                                    @click="cancelReply">Cancel</div>
                                <div class="absolute top-[52px] left-[13.92px] right-[13.92px] h-[142px]">
                                    <textarea v-model="replyText" placeholder="What are your thoughts?"
                                        class="w-full h-full rounded-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 p-4 resize-none outline-none">
                                    </textarea>
                                </div>
                                <div class="absolute top-[12px] left-[13.92px] w-[125.3px] h-6 flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                            {{ $page.props.auth.user.name?.charAt(0) }}
                                        </span>
                                    </div>
                                    <div class="flex flex-col">
                                        <div class="text-sm font-semibold">{{ $page.props.auth.user.name }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Instructions Tab -->
                        <div v-if="activeTab === 'instructions'" class="self-stretch h-full flex flex-col items-start justify-start py-3 px-4 box-border text-gray-900 dark:text-white">
                            <div class="self-stretch h-[60px] flex flex-row items-start justify-start pt-5 px-4 pb-3 box-border text-[22px]">
                                <b class="relative leading-7">Supervisor Feedback</b>
                            </div>
                            
                            <!-- Instructions Content -->
                            <div class="self-stretch flex-1 bg-white dark:bg-gray-800 rounded-lg p-6">
                                <div class="space-y-4">
                                    <div v-if="idea.instructions">
                                        <h3 class="text-lg font-semibold mb-2">Project Instructions</h3>
                                        <div class="text-gray-600 dark:text-gray-400" v-html="idea.instructions"></div>
                                    </div>
                                    <div v-else class="text-center py-8">
                                        <p class="text-gray-500 dark:text-gray-400">No instructions available yet.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submit Idea Form (when no idea exists) -->
                    <div v-else class="self-stretch flex flex-col items-start justify-start">
                        <!-- Submit Form content goes here - based on Submit_idea_tab design -->
                        <SubmitIdeaForm :tracks="tracks" @submitted="handleIdeaSubmitted" />
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'
import SubmitIdeaForm from './SubmitForm.vue'

const props = defineProps({
    idea: Object,
    tracks: Array
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

const activeTab = ref('overview')
const replyText = ref('')

const getProgressWidth = (status) => {
    const statusProgress = {
        'draft': '20%',
        'submitted': '40%',
        'under_review': '60%',
        'approved': '80%',
        'completed': '100%'
    }
    return statusProgress[status] || '20%'
}

const getProgressText = (status) => {
    const statusText = {
        'draft': '20% Complete - Draft Stage',
        'submitted': '40% Complete - Under Review',
        'under_review': '60% Complete - Being Evaluated',
        'approved': '80% Complete - Approved',
        'completed': '100% Complete - Finished'
    }
    return statusText[status] || '20% Complete'
}

const formatDate = (dateString) => {
    const date = new Date(dateString)
    const now = new Date()
    const diffInDays = Math.floor((now - date) / (1000 * 60 * 60 * 24))
    
    if (diffInDays === 0) return 'Today'
    if (diffInDays === 1) return '1 day ago'
    if (diffInDays < 7) return `${diffInDays} days ago`
    return date.toLocaleDateString()
}

const submitReply = () => {
    if (replyText.value.trim()) {
        router.post(route('team-lead.idea.comment', props.idea.id), {
            comment: replyText.value
        }, {
            onSuccess: () => {
                replyText.value = ''
            }
        })
    }
}

const cancelReply = () => {
    replyText.value = ''
}

const handleIdeaSubmitted = () => {
    router.visit(route('team-lead.idea.index'))
}
</script>

<style scoped>
input[type="text"]:focus,
textarea:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>