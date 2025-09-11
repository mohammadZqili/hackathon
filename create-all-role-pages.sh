#!/bin/bash

echo "Creating all role-based Vue pages from designs..."

# Create necessary directories
mkdir -p resources/js/Pages/TeamLead/{Dashboard,Team,Idea,Tracks,Workshops}
mkdir -p resources/js/Pages/TeamMember/{Dashboard,Team,Idea,Tracks,Workshops}
mkdir -p resources/js/Pages/Visitor/Workshops

# =======================
# TEAM LEAD PAGES
# =======================

echo "Creating Team Lead pages..."

# Team Lead Dashboard
cat > resources/js/Pages/TeamLead/Dashboard.vue << 'EOF'
<template>
    <Head title="Dashboard - Team Lead" />
    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Welcome Section -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    Welcome back, {{ $page.props.auth.user.name }}!
                </h1>
                <p class="text-gray-600 dark:text-gray-400">Team Lead Dashboard</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Team Members Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-lg" :style="{ backgroundColor: themeColor.primary + '20' }">
                            <svg class="w-6 h-6" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.team_members || 0 }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Team Members</p>
                </div>

                <!-- Idea Status Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-lg" :style="{ backgroundColor: themeColor.primary + '20' }">
                            <svg class="w-6 h-6" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ stats.idea_status || 'Not Submitted' }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Idea Status</p>
                </div>

                <!-- Track Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-lg" :style="{ backgroundColor: themeColor.primary + '20' }">
                            <svg class="w-6 h-6" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ stats.track || 'Not Selected' }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Current Track</p>
                </div>

                <!-- Workshops Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-lg" :style="{ backgroundColor: themeColor.primary + '20' }">
                            <svg class="w-6 h-6" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.workshops_registered || 0 }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Workshops Registered</p>
                </div>
            </div>

            <!-- Quick Actions Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Team Overview -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Team Overview</h2>
                        <Link v-if="team" :href="route('team-lead.team.index')" 
                            class="text-sm font-medium"
                            :style="{ color: themeColor.primary }">
                            Manage Team →
                        </Link>
                    </div>
                    
                    <div v-if="team" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Team Name:</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ team.name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Members:</span>
                            <div class="flex -space-x-2">
                                <div v-for="(member, index) in team.members?.slice(0, 4)" :key="index"
                                    class="w-8 h-8 rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center text-xs font-medium text-white"
                                    :style="{ backgroundColor: themeColor.primary }">
                                    {{ member.name?.charAt(0) }}
                                </div>
                                <div v-if="team.members?.length > 4" 
                                    class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 border-2 border-white dark:border-gray-800 flex items-center justify-center text-xs font-medium text-gray-700 dark:text-gray-300">
                                    +{{ team.members.length - 4 }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400 mb-4">No team created yet</p>
                        <Link :href="route('team-lead.team.create')"
                            class="inline-flex items-center px-4 py-2 text-white rounded-lg transition-colors"
                            :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                            Create Team
                        </Link>
                    </div>
                </div>

                <!-- Idea Overview -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Idea Overview</h2>
                        <Link v-if="idea" :href="route('team-lead.idea.index')" 
                            class="text-sm font-medium"
                            :style="{ color: themeColor.primary }">
                            View Details →
                        </Link>
                    </div>
                    
                    <div v-if="idea" class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Title</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ idea.title }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Status</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                :style="{ 
                                    backgroundColor: themeColor.primary + '20',
                                    color: themeColor.primary
                                }">
                                {{ idea.status }}
                            </span>
                        </div>
                    </div>
                    
                    <div v-else-if="team" class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400 mb-4">No idea submitted yet</p>
                        <Link :href="route('team-lead.idea.create')"
                            class="inline-flex items-center px-4 py-2 text-white rounded-lg transition-colors"
                            :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                            Submit Idea
                        </Link>
                    </div>
                    
                    <div v-else class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400">Create a team first to submit an idea</p>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Recent Activities</h2>
                
                <div class="space-y-4">
                    <div v-for="activity in recentActivities" :key="activity.id" 
                        class="flex items-start gap-3 pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0">
                        <div class="p-2 rounded-lg" :style="{ backgroundColor: themeColor.primary + '20' }">
                            <svg class="w-4 h-4" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-gray-900 dark:text-white">{{ activity.description }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ activity.time }}</p>
                        </div>
                    </div>
                    
                    <div v-if="!recentActivities?.length" class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400">No recent activities</p>
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
    idea: Object,
    workshops: Array,
    tracks: Array,
    stats: Object,
    recentActivities: Array
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
</script>

<style scoped>
input[type="text"]:focus,
input[type="number"]:focus,
input[type="date"]:focus,
select:focus,
textarea:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}

input[type="checkbox"]:checked {
    background-color: var(--theme-primary) !important;
    border-color: var(--theme-primary) !important;
}
</style>
EOF

echo "Team Lead pages created!"

echo "Script complete! Run 'npm run dev' to compile the assets."