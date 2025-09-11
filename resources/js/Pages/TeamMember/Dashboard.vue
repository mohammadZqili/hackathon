<template>
    <Head title="Team Member Dashboard" />
    <Default>
        <div class="w-full h-full overflow-hidden bg-gray-50 dark:bg-gray-900" :style="themeStyles">
            <!-- Team Member Dashboard exactly matching Figma Design -->
            <div class="w-full overflow-hidden flex flex-col items-start justify-start max-w-[960px] text-sm font-space-grotesk">
                <!-- Page Header -->
                <div class="self-stretch flex flex-row items-start justify-between flex-wrap content-start p-4 text-[32px] text-gray-900 dark:text-white">
                    <div class="flex flex-col items-start justify-start gap-3 min-w-[288px]">
                        <div class="w-[896px] flex flex-col items-start justify-start">
                            <b class="self-stretch relative leading-10">Team Member Dashboard</b>
                        </div>
                        <div class="flex flex-col items-start justify-start text-sm"
                            :style="{ color: themeColor.primary }">
                            <div class="self-stretch relative leading-[21px]">Welcome to your team member dashboard. Stay updated with your team activities and upcoming workshops.</div>
                        </div>
                    </div>
                </div>
                
                <!-- Team Information Card -->
                <div v-if="team" class="self-stretch flex flex-col items-start justify-start p-4">
                    <div class="self-stretch shadow-[0px_0px_4px_rgba(0,_0,_0,_0.1)] rounded-xl bg-white dark:bg-gray-800 overflow-hidden flex flex-col items-start justify-start p-4 gap-4">
                        <div class="self-stretch flex flex-row items-center justify-between">
                            <div class="flex flex-col items-start justify-start">
                                <div class="text-lg font-semibold text-gray-900 dark:text-white">My Team</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Current team information</div>
                            </div>
                        </div>
                        <div class="self-stretch flex flex-col items-start justify-start gap-2">
                            <div class="flex items-center gap-3">
                                <span class="text-base font-medium text-gray-900 dark:text-white">Team Name:</span>
                                <span class="text-base" :style="{ color: themeColor.primary }">{{ team.name }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-base font-medium text-gray-900 dark:text-white">Team Lead:</span>
                                <span class="text-base text-gray-700 dark:text-gray-300">{{ team.leader?.name || 'TBA' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-base font-medium text-gray-900 dark:text-white">Track:</span>
                                <span class="text-base text-gray-700 dark:text-gray-300">{{ team.track?.name || 'Not selected' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-base font-medium text-gray-900 dark:text-white">Members:</span>
                                <span class="text-base text-gray-700 dark:text-gray-300">{{ team.members?.length || 0 }} members</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- No Team State -->
                <div v-else class="self-stretch flex flex-col items-center justify-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Not in a Team</h3>
                    <p class="text-gray-600 dark:text-gray-400">You are not currently assigned to any team. Please wait for a team leader to invite you.</p>
                </div>
                
                <!-- Quick Actions -->
                <div class="self-stretch flex flex-col items-start justify-start p-4">
                    <div class="self-stretch flex flex-col items-start justify-start gap-4">
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</div>
                        <div class="self-stretch grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- View Team -->
                            <Link v-if="team" :href="route('team-member.team.index')"
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-700 group">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                        :style="{ backgroundColor: `${themeColor.primary}20` }">
                                        <svg class="w-5 h-5" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="text-base font-medium text-gray-900 dark:text-white group-hover:text-gray-700 dark:group-hover:text-gray-200">View My Team</div>
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">See team members and details</div>
                            </Link>
                            
                            <!-- View Idea -->
                            <Link v-if="team?.idea" :href="route('team-member.idea.index')"
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-700 group">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                        :style="{ backgroundColor: `${themeColor.primary}20` }">
                                        <svg class="w-5 h-5" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                        </svg>
                                    </div>
                                    <div class="text-base font-medium text-gray-900 dark:text-white group-hover:text-gray-700 dark:group-hover:text-gray-200">Our Idea</div>
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">View team's submitted idea</div>
                            </Link>
                            
                            <!-- Browse Tracks -->
                            <Link :href="route('team-member.tracks.index')"
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-700 group">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                        :style="{ backgroundColor: `${themeColor.primary}20` }">
                                        <svg class="w-5 h-5" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                    </div>
                                    <div class="text-base font-medium text-gray-900 dark:text-white group-hover:text-gray-700 dark:group-hover:text-gray-200">Browse Tracks</div>
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Explore available tracks</div>
                            </Link>
                            
                            <!-- Workshops -->
                            <Link :href="route('team-member.workshops.index')"
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-700 group">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                        :style="{ backgroundColor: `${themeColor.primary}20` }">
                                        <svg class="w-5 h-5" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <div class="text-base font-medium text-gray-900 dark:text-white group-hover:text-gray-700 dark:group-hover:text-gray-200">Workshops</div>
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Join upcoming workshops</div>
                            </Link>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div v-if="recentActivity?.length" class="self-stretch flex flex-col items-start justify-start p-4">
                    <div class="self-stretch flex flex-col items-start justify-start gap-4">
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activity</div>
                        <div class="self-stretch space-y-2">
                            <div v-for="activity in recentActivity" :key="activity.id"
                                class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="w-2 h-2 rounded-full" :style="{ backgroundColor: themeColor.primary }"></div>
                                <div class="flex-1">
                                    <div class="text-sm text-gray-900 dark:text-white">{{ activity.message }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(activity.created_at) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'

const props = defineProps({
    team: Object,
    recentActivity: Array,
    upcomingWorkshops: Array
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
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    })
}
</script>

<style scoped>
/* Theme styles are applied via CSS variables */
</style>