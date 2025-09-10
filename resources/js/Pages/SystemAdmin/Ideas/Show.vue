<script setup>
import { Head, router, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import Default from '@/Layouts/Default.vue'
import { 
    DocumentIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    idea: Object,
    reviewHistory: Array,
    scoring: Object,
})

const activeTab = ref('overview')

// Form for decision making
const decisionForm = useForm({
    status: '',
    feedback: '',
    score: null,
})

const submitDecision = (status) => {
    decisionForm.status = status
    decisionForm.post(route('system-admin.ideas.review.submit', props.idea.id), {
        onSuccess: () => {
            // Handle success
        },
    })
}

const updateScore = () => {
    if (decisionForm.score && decisionForm.score >= 0 && decisionForm.score <= 100) {
        decisionForm.post(route('system-admin.ideas.score', props.idea.id), {
            only: ['score'],
            preserveScroll: true,
        })
    }
}

const formatDateTime = (date) => {
    const d = new Date(date)
    const year = d.getFullYear()
    const month = String(d.getMonth() + 1).padStart(2, '0')
    const day = String(d.getDate()).padStart(2, '0')
    const hours = String(d.getHours()).padStart(2, '0')
    const minutes = String(d.getMinutes()).padStart(2, '0')
    return `${year}-${month}-${day} ${hours}:${minutes}`
}
</script>

<template>
    <Head :title="`Idea: ${idea.title}`" />

    <Default>
        <div class="flex-1 flex flex-col items-start justify-start gap-4">
            <!-- Main Content Area -->
            <div class="self-stretch flex flex-col items-start justify-start gap-2.5 text-gray-100 font-space-grotesk">
                <div class="w-full overflow-hidden flex flex-col items-start justify-start max-w-[960px]">
                    <!-- Header -->
                    <div class="self-stretch flex flex-row items-start justify-between flex-wrap content-start p-4 text-[32px]">
                        <div class="flex flex-col items-start justify-start gap-3 min-w-[288px]">
                            <div class="flex flex-col items-start justify-start">
                                <b class="self-stretch relative leading-10">Idea: {{ idea.title }}</b>
                            </div>
                            <div class="w-[589px] flex flex-col items-start justify-start text-sm text-seagreen">
                                <div class="self-stretch relative leading-[21px]">Submitted by {{ idea.team?.name || 'Unknown Team' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-3 text-sm">
                        <div class="self-stretch border-honeydew border-solid border-b-[1px] flex flex-row items-start justify-start py-0 px-4 gap-8">
                            <div @click="activeTab = 'overview'"
                                 :class="activeTab === 'overview' ? 'border-gainsboro-100' : 'border-transparent'"
                                 class="border-solid border-b-[3px] flex flex-col items-center justify-center pt-4 px-0 pb-[13px] cursor-pointer">
                                <div class="flex flex-col items-start justify-start">
                                    <b class="self-stretch relative leading-[21px]" 
                                       :class="activeTab === 'overview' ? 'text-gray-100' : 'text-seagreen'">Overview</b>
                                </div>
                            </div>
                            <div @click="activeTab = 'response'"
                                 :class="activeTab === 'response' ? 'border-gainsboro-100' : 'border-transparent'"
                                 class="border-solid border-b-[3px] flex flex-col items-center justify-center pt-4 px-0 pb-[13px] cursor-pointer">
                                <div class="flex flex-col items-start justify-start">
                                    <b class="self-stretch relative leading-[21px]"
                                       :class="activeTab === 'response' ? 'text-gray-100' : 'text-seagreen'">Response</b>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Overview Tab Content -->
                    <div v-show="activeTab === 'overview'">
                        <!-- Idea Details Header -->
                        <div class="self-stretch h-[60px] flex flex-col items-start justify-start pt-5 px-4 pb-3 box-border text-[22px]">
                            <b class="self-stretch relative leading-7">Idea Details</b>
                        </div>

                        <!-- Idea Details Content -->
                        <div class="self-stretch flex flex-col items-start justify-start p-4 gap-6 text-sm text-cadetblue">
                            <!-- Row 1: Team Name & Submission Date/Time -->
                            <div class="self-stretch flex-1 flex flex-row items-start justify-start gap-6">
                                <div class="self-stretch w-[186px] border-gainsboro-100 border-solid border-t-[1px] box-border flex flex-col items-start justify-start py-5 px-0">
                                    <div class="self-stretch flex-1 flex flex-row items-start justify-start">
                                        <div class="self-stretch w-[186px] flex flex-col items-start justify-start">
                                            <div class="self-stretch relative leading-[21px]">Team Name</div>
                                        </div>
                                    </div>
                                    <div class="self-stretch flex-1 flex flex-row items-start justify-start text-gray-200">
                                        <div class="self-stretch w-[186px] flex flex-col items-start justify-start">
                                            <div class="self-stretch relative leading-[21px]">{{ idea.team?.name || 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="self-stretch w-[718px] border-gainsboro-100 border-solid border-t-[1px] box-border flex flex-col items-start justify-start py-5 px-0">
                                    <div class="self-stretch flex-1 flex flex-row items-start justify-start">
                                        <div class="self-stretch w-[718px] flex flex-col items-start justify-start">
                                            <div class="self-stretch relative leading-[21px]">Submission Date/Time</div>
                                        </div>
                                    </div>
                                    <div class="self-stretch flex-1 flex flex-row items-start justify-start text-gray-200">
                                        <div class="self-stretch w-[718px] flex flex-col items-start justify-start">
                                            <div class="self-stretch relative leading-[21px]">{{ formatDateTime(idea.created_at) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2: Idea Leader & Track -->
                            <div class="self-stretch flex-1 flex flex-row items-start justify-start gap-6">
                                <div class="self-stretch w-[186px] border-gainsboro-100 border-solid border-t-[1px] box-border flex flex-col items-start justify-start py-5 px-0">
                                    <div class="self-stretch flex-1 flex flex-row items-start justify-start">
                                        <div class="self-stretch w-[186px] flex flex-col items-start justify-start">
                                            <div class="self-stretch relative leading-[21px]">Idea Leader</div>
                                        </div>
                                    </div>
                                    <div class="self-stretch flex-1 flex flex-row items-start justify-start text-gray-200">
                                        <div class="self-stretch w-[186px] flex flex-col items-start justify-start">
                                            <div class="self-stretch relative leading-[21px]">{{ idea.leader_name || 'Sarah Chen' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="self-stretch w-[718px] border-gainsboro-100 border-solid border-t-[1px] box-border flex flex-col items-start justify-start py-5 px-0">
                                    <div class="self-stretch flex-1 flex flex-row items-start justify-start">
                                        <div class="self-stretch w-[718px] flex flex-col items-start justify-start">
                                            <div class="self-stretch relative leading-[21px]">Track</div>
                                        </div>
                                    </div>
                                    <div class="self-stretch flex-1 flex flex-row items-start justify-start text-gray-200">
                                        <div class="self-stretch w-[718px] flex flex-col items-start justify-start">
                                            <div class="self-stretch relative leading-[21px]">{{ idea.track?.name || 'Unassigned' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 3: Hackathon Edition -->
                            <div class="self-stretch flex-1 flex flex-row items-start justify-start">
                                <div class="self-stretch w-[186px] border-gainsboro-100 border-solid border-t-[1px] box-border flex flex-col items-start justify-start py-5 px-0">
                                    <div class="self-stretch flex-1 flex flex-row items-start justify-start">
                                        <div class="self-stretch w-[186px] flex flex-col items-start justify-start">
                                            <div class="self-stretch relative leading-[21px]">Hackathon Edition</div>
                                        </div>
                                    </div>
                                    <div class="self-stretch flex-1 flex flex-row items-start justify-start text-gray-200">
                                        <div class="self-stretch w-[186px] flex flex-col items-start justify-start">
                                            <div class="self-stretch relative leading-[21px]">{{ idea.edition || 'Summer 2024' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description Section -->
                        <div class="self-stretch flex flex-col items-start justify-start pt-5 px-4 pb-3 text-[22px] text-gray-200">
                            <b class="self-stretch relative leading-7">Description</b>
                        </div>
                        <div class="self-stretch flex flex-col items-start justify-start pt-1 px-4 pb-3 text-gray-200">
                            <div class="self-stretch relative leading-6">{{ idea.description }}</div>
                        </div>

                        <!-- Related Documents Section -->
                        <div v-if="idea.files && idea.files.length > 0">
                            <div class="self-stretch h-[60px] flex flex-col items-start justify-start pt-5 px-4 pb-3 box-border text-[22px]">
                                <b class="self-stretch relative leading-7">Related Documents</b>
                            </div>
                            <div v-for="file in idea.files" :key="file.id">
                                <div class="self-stretch bg-mintcream-100 h-14 flex flex-row items-center justify-start py-0 px-4 box-border gap-4 min-h-[56px]">
                                    <div class="w-10 h-10 rounded-lg bg-white dark:bg-gray-700 flex items-center justify-center">
                                        <DocumentIcon class="w-6 h-6 text-gray-500 dark:text-gray-400" />
                                    </div>
                                    <div class="flex-1 overflow-hidden flex flex-col items-start justify-start">
                                        <div class="self-stretch relative leading-6 overflow-hidden text-ellipsis whitespace-nowrap">{{ file.filename }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Default Documents if no files -->
                        <div v-else>
                            <div class="self-stretch h-[60px] flex flex-col items-start justify-start pt-5 px-4 pb-3 box-border text-[22px]">
                                <b class="self-stretch relative leading-7">Related Documents</b>
                            </div>
                            <div class="self-stretch bg-mintcream-100 h-14 flex flex-row items-center justify-start py-0 px-4 box-border gap-4 min-h-[56px]">
                                <div class="w-10 h-10 rounded-lg bg-white dark:bg-gray-700 flex items-center justify-center">
                                    <DocumentIcon class="w-6 h-6 text-gray-500 dark:text-gray-400" />
                                </div>
                                <div class="flex-1 overflow-hidden flex flex-col items-start justify-start">
                                    <div class="self-stretch relative leading-6 overflow-hidden text-ellipsis whitespace-nowrap">Onboarding Checklist</div>
                                </div>
                            </div>
                            <div class="self-stretch bg-mintcream-100 h-14 flex flex-row items-center justify-start py-0 px-4 box-border gap-4 min-h-[56px]">
                                <div class="w-10 h-10 rounded-lg bg-white dark:bg-gray-700 flex items-center justify-center">
                                    <DocumentIcon class="w-6 h-6 text-gray-500 dark:text-gray-400" />
                                </div>
                                <div class="flex-1 overflow-hidden flex flex-col items-start justify-start">
                                    <div class="self-stretch relative leading-6 overflow-hidden text-ellipsis whitespace-nowrap">Training Module Outline</div>
                                </div>
                            </div>
                            <div class="self-stretch bg-mintcream-100 h-14 flex flex-row items-center justify-start py-0 px-4 box-border gap-4 min-h-[56px]">
                                <div class="w-10 h-10 rounded-lg bg-white dark:bg-gray-700 flex items-center justify-center">
                                    <DocumentIcon class="w-6 h-6 text-gray-500 dark:text-gray-400" />
                                </div>
                                <div class="flex-1 overflow-hidden flex flex-col items-start justify-start">
                                    <div class="self-stretch relative leading-6 overflow-hidden text-ellipsis whitespace-nowrap">Feedback Survey Template</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Response Tab Content -->
                    <div v-show="activeTab === 'response'" class="self-stretch p-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                                Review History
                            </h2>
                            <div v-if="reviewHistory && reviewHistory.length > 0" class="space-y-4">
                                <div v-for="review in reviewHistory" :key="review.id" 
                                     class="border-l-4 border-emerald-500 pl-4 py-2">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <span class="font-medium text-gray-900 dark:text-gray-100">
                                                {{ review.reviewer_name }}
                                            </span>
                                            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                                {{ formatDateTime(review.created_at) }}
                                            </span>
                                        </div>
                                        <span :class="[
                                            'px-2 py-1 text-xs font-medium rounded-full',
                                            review.status === 'accepted' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' :
                                            review.status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' :
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400'
                                        ]">
                                            {{ review.status }}
                                        </span>
                                    </div>
                                    <p class="text-gray-700 dark:text-gray-300">{{ review.feedback }}</p>
                                    <p v-if="review.score !== null" class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        Score: {{ review.score }}/100
                                    </p>
                                </div>
                            </div>
                            <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                                No review history available
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Decision and Score Section -->
                <div class="self-stretch flex flex-row items-start justify-start text-[22px] text-gray-200">
                    <!-- Make Decision Section -->
                    <div class="flex-1 flex flex-col items-start justify-start">
                        <div class="self-stretch flex flex-col items-start justify-start pt-5 px-4 pb-3">
                            <b class="self-stretch relative leading-7">Make Decision ... </b>
                        </div>
                        <div class="self-stretch flex flex-row items-start justify-center text-center text-sm">
                            <div class="flex-1 flex flex-row items-start justify-start flex-wrap content-start py-3 px-4 gap-3">
                                <div @click="submitDecision('accepted')"
                                     class="w-[84px] rounded-xl bg-mediumseagreen h-10 overflow-hidden shrink-0 flex flex-row items-center justify-center py-0 px-4 box-border min-w-[84px] max-w-[480px] cursor-pointer hover:opacity-90 transition-opacity">
                                    <div class="overflow-hidden flex flex-col items-center justify-start">
                                        <b class="self-stretch relative leading-[21px] overflow-hidden text-ellipsis whitespace-nowrap text-white">Accept</b>
                                    </div>
                                </div>
                                <div @click="submitDecision('rejected')"
                                     class="w-[84px] rounded-xl bg-whitesmoke h-10 overflow-hidden shrink-0 flex flex-row items-center justify-center py-0 px-4 box-border min-w-[84px] max-w-[480px] cursor-pointer hover:bg-gray-300 transition-colors">
                                    <div class="overflow-hidden flex flex-col items-center justify-start">
                                        <b class="self-stretch relative leading-[21px] overflow-hidden text-ellipsis whitespace-nowrap">Reject</b>
                                    </div>
                                </div>
                                <div @click="submitDecision('needs_revision')"
                                     class="rounded-xl bg-whitesmoke h-10 overflow-hidden flex flex-row items-center justify-center py-0 px-4 box-border min-w-[84px] max-w-[480px] cursor-pointer hover:bg-gray-300 transition-colors">
                                    <div class="overflow-hidden flex flex-col items-center justify-start">
                                        <b class="self-stretch relative leading-[21px] overflow-hidden text-ellipsis whitespace-nowrap">Need Edit</b>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border max-w-[480px] text-base">
                            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                                <div class="self-stretch flex-1 rounded-xl bg-white border-gainsboro-200 border-solid border-[1px] box-border overflow-hidden flex flex-row items-start justify-start p-[15px] min-h-[144px]">
                                    <textarea
                                        v-model="decisionForm.feedback"
                                        placeholder="Provide feedback or required changes for the idea's acceptance"
                                        class="flex-1 relative leading-6 bg-transparent border-none outline-none resize-none text-gray-600 dark:text-gray-400 placeholder:text-gray-500/75"
                                        rows="5"
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Score Section -->
                    <div class="flex-1 flex flex-col items-start justify-start">
                        <div class="self-stretch flex flex-col items-start justify-start pt-5 px-4 pb-3">
                            <b class="self-stretch relative leading-7">Score</b>
                        </div>
                        <div class="mx-4">
                            <input
                                v-model.number="decisionForm.score"
                                @blur="updateScore"
                                type="number"
                                min="0"
                                max="100"
                                placeholder="Add Score From 100"
                                class="rounded-xl bg-mintcream-100 border-honeydew border-solid border-[1px] h-14 w-full px-[15px] text-base text-seagreen placeholder:text-seagreen outline-none focus:border-seagreen"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

<style scoped>
/* Custom styles for exact matching */
.bg-mediumseagreen {
    background-color: #3cb371;
}

.bg-whitesmoke {
    background-color: #f5f5f5;
}

.bg-mintcream-100 {
    background-color: #f0fff4;
}

.bg-mintcream-200 {
    background-color: #e6f7ed;
}

.bg-mintcream-300 {
    background-color: #dcf4e6;
}

.border-honeydew {
    border-color: #f0fff0;
}

.border-gainsboro-100 {
    border-color: #dcdcdc;
}

.border-gainsboro-200 {
    border-color: #d3d3d3;
}

.text-seagreen {
    color: #2e8b57;
}

.text-cadetblue {
    color: #5f9ea0;
}

.text-gray-200 {
    color: #374151;
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
