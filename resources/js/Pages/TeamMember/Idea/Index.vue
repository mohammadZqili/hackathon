<template>
    <Head title="Our Idea - Team Member" />
    <Default>
        <div class="w-full h-full overflow-hidden bg-gray-50 dark:bg-gray-900" :style="themeStyles">
            <!-- Our Idea Page with Overview Tab based on Figma Design -->
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
                    
                    <!-- Idea Content -->
                    <div v-if="idea" class="self-stretch flex flex-col items-start justify-start p-4 gap-6">
                        <!-- Overview Tab Content -->
                        <div class="self-stretch bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="p-6">
                                <div class="flex flex-col gap-4">
                                    <!-- Track Info -->
                                    <div class="flex items-center gap-3 pb-4 border-b border-gray-200 dark:border-gray-600">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                            :style="{ backgroundColor: `${themeColor.primary}20` }">
                                            <svg class="w-5 h-5" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Track</div>
                                            <div class="text-base font-semibold">{{ idea.track?.name || 'No track selected' }}</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Idea Title -->
                                    <div>
                                        <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Idea Title</div>
                                        <div class="text-xl font-bold text-gray-900 dark:text-white">{{ idea.title }}</div>
                                    </div>
                                    
                                    <!-- Description -->
                                    <div>
                                        <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Description</div>
                                        <div class="text-base text-gray-700 dark:text-gray-300 leading-relaxed">{{ idea.description }}</div>
                                    </div>
                                    
                                    <!-- Attachments -->
                                    <div v-if="idea.attachments?.length">
                                        <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Attachments</div>
                                        <div class="space-y-2">
                                            <div v-for="attachment in idea.attachments" :key="attachment.id"
                                                class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                </svg>
                                                <span class="text-sm text-gray-900 dark:text-white">{{ attachment.name }}</span>
                                                <a :href="attachment.url" target="_blank" 
                                                    class="text-sm hover:underline"
                                                    :style="{ color: themeColor.primary }">
                                                    Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Submission Info -->
                                    <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            Submitted on {{ formatDate(idea.created_at) }}
                                        </div>
                                    </div>
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
import { Head } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'

const props = defineProps({
    idea: Object,
    team: Object
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
</script>

<style scoped>
/* Theme styles are applied via CSS variables */
</style>