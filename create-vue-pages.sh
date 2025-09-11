#!/bin/bash

echo "Creating Vue pages for all roles..."

# Create directories
mkdir -p resources/js/Pages/TeamLead/{Dashboard,Team,Idea,Tracks,Workshops}
mkdir -p resources/js/Pages/TeamMember/{Dashboard,Team,Idea,Tracks,Workshops}
mkdir -p resources/js/Pages/Visitor/Workshops
mkdir -p resources/js/Pages/Shared/Profile

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

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Team Status -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2 rounded-lg" :style="{ backgroundColor: themeColor.primary + '20' }">
                            <svg class="w-6 h-6" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.team_members || 0 }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Team Members</p>
                </div>

                <!-- Idea Status -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2 rounded-lg" :style="{ backgroundColor: themeColor.primary + '20' }">
                            <svg class="w-6 h-6" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white capitalize">{{ stats.idea_status || 'Pending' }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Idea Status</p>
                </div>

                <!-- Workshops -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2 rounded-lg" :style="{ backgroundColor: themeColor.primary + '20' }">
                            <svg class="w-6 h-6" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.workshops_registered || 0 }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Workshops Registered</p>
                </div>

                <!-- Track -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2 rounded-lg" :style="{ backgroundColor: themeColor.primary + '20' }">
                            <svg class="w-6 h-6" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ stats.track || 'Not Selected' }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Current Track</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Team Section -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">My Team</h2>
                        <Link v-if="!team" :href="route('team-lead.team.create')" 
                            class="px-4 py-2 text-white rounded-lg transition-colors text-sm"
                            :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                            Create Team
                        </Link>
                    </div>
                    
                    <div v-if="team" class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Team Name</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ team.name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Members</p>
                            <div class="flex -space-x-2 mt-2">
                                <div v-for="member in team.members?.slice(0, 5)" :key="member.id"
                                    class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 border-2 border-white dark:border-gray-800 flex items-center justify-center">
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                        {{ member.name?.charAt(0) }}
                                    </span>
                                </div>
                                <div v-if="team.members?.length > 5" 
                                    class="w-8 h-8 rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center"
                                    :style="{ backgroundColor: themeColor.primary }">
                                    <span class="text-xs font-medium text-white">+{{ team.members.length - 5 }}</span>
                                </div>
                            </div>
                        </div>
                        <Link :href="route('team-lead.team.index')" 
                            class="inline-flex items-center text-sm font-medium"
                            :style="{ color: themeColor.primary }">
                            Manage Team
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </Link>
                    </div>
                    
                    <div v-else class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400 mb-4">You haven't created a team yet</p>
                        <Link :href="route('team-lead.team.create')" 
                            class="inline-flex items-center px-4 py-2 text-white rounded-lg transition-colors"
                            :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                            Create Your Team
                        </Link>
                    </div>
                </div>

                <!-- Idea Section -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Our Idea</h2>
                        <Link v-if="team && !idea" :href="route('team-lead.idea.create')" 
                            class="px-4 py-2 text-white rounded-lg transition-colors text-sm"
                            :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                            Submit Idea
                        </Link>
                    </div>
                    
                    <div v-if="idea" class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Title</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ idea.title }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                :class="{
                                    'bg-yellow-100 text-yellow-800': idea.status === 'pending',
                                    'bg-green-100 text-green-800': idea.status === 'approved',
                                    'bg-blue-100 text-blue-800': idea.status === 'submitted',
                                    'bg-red-100 text-red-800': idea.status === 'rejected'
                                }">
                                {{ idea.status }}
                            </span>
                        </div>
                        <Link :href="route('team-lead.idea.index')" 
                            class="inline-flex items-center text-sm font-medium"
                            :style="{ color: themeColor.primary }">
                            View Details
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </Link>
                    </div>
                    
                    <div v-else-if="!team" class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400">Create a team first to submit an idea</p>
                    </div>
                    
                    <div v-else class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400 mb-4">No idea submitted yet</p>
                        <Link :href="route('team-lead.idea.create')" 
                            class="inline-flex items-center px-4 py-2 text-white rounded-lg transition-colors"
                            :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                            Submit Your Idea
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Upcoming Workshops -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Upcoming Workshops</h2>
                    <Link :href="route('team-lead.workshops.index')" 
                        class="text-sm font-medium"
                        :style="{ color: themeColor.primary }">
                        View All
                    </Link>
                </div>
                
                <div v-if="workshops?.length > 0" class="space-y-4">
                    <div v-for="workshop in workshops.slice(0, 3)" :key="workshop.id" 
                        class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ workshop.title }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ workshop.date }} | {{ workshop.time }}</p>
                        </div>
                        <button @click="registerForWorkshop(workshop.id)"
                            class="px-3 py-1 text-sm rounded-lg border transition-colors"
                            :style="{ 
                                borderColor: themeColor.primary,
                                color: themeColor.primary
                            }">
                            Register
                        </button>
                    </div>
                </div>
                
                <div v-else class="text-center py-8">
                    <p class="text-gray-500 dark:text-gray-400">No upcoming workshops</p>
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

const props = defineProps({
    team: Object,
    idea: Object,
    workshops: Array,
    tracks: Array,
    stats: Object
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

const registerForWorkshop = (workshopId) => {
    router.post(route('team-lead.workshops.register', workshopId))
}
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

# Create other Team Lead pages
echo "Creating Team Lead Team Index page..."
cat > resources/js/Pages/TeamLead/Team/Index.vue << 'EOF'
<template>
    <Head title="My Team" />
    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">My Team</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage your hackathon team</p>
            </div>

            <div v-if="team" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Team Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Team Name</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ team.name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Track</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ team.track?.name || 'Not Selected' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Description</p>
                            <p class="text-gray-900 dark:text-white">{{ team.description || 'No description' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Created</p>
                            <p class="text-gray-900 dark:text-white">{{ new Date(team.created_at).toLocaleDateString() }}</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Team Members</h3>
                        <button @click="showAddMemberModal = true"
                            class="px-4 py-2 text-white rounded-lg transition-colors text-sm"
                            :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                            Add Member
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Name</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Email</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Role</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="member in team.members" :key="member.id" class="border-b border-gray-100 dark:border-gray-700">
                                    <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">{{ member.name }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ member.email }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ member.pivot?.role || 'Member' }}</td>
                                    <td class="py-3 px-4">
                                        <button @click="removeMember(member.id)"
                                            class="text-red-600 hover:text-red-800 text-sm">
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div v-else class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No Team Yet</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Create your team to start collaborating</p>
                <Link :href="route('team-lead.team.create')"
                    class="inline-flex items-center px-6 py-3 text-white rounded-lg transition-colors"
                    :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                    Create Team
                </Link>
            </div>

            <!-- Add Member Modal -->
            <div v-if="showAddMemberModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Add Team Member</h3>
                    <form @submit.prevent="addMember">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                            <input v-model="memberForm.email" type="email" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role</label>
                            <select v-model="memberForm.role"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 dark:bg-gray-700 dark:text-white">
                                <option value="developer">Developer</option>
                                <option value="designer">Designer</option>
                                <option value="marketer">Marketer</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showAddMemberModal = false"
                                class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-white rounded-lg"
                                :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                                Add Member
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'

const props = defineProps({
    team: Object,
    canCreateTeam: Boolean
})

const showAddMemberModal = ref(false)
const memberForm = reactive({
    email: '',
    role: 'developer'
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

const addMember = () => {
    router.post(route('team-lead.team.add-member'), memberForm, {
        onSuccess: () => {
            showAddMemberModal.value = false
            memberForm.email = ''
            memberForm.role = 'developer'
        }
    })
}

const removeMember = (memberId) => {
    if (confirm('Are you sure you want to remove this member?')) {
        router.delete(route('team-lead.team.remove-member', memberId))
    }
}
</script>

<style scoped>
input[type="text"]:focus,
input[type="email"]:focus,
select:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>
EOF

# Create shared Profile component
echo "Creating shared Profile component..."
cat > resources/js/Pages/Shared/Profile/Index.vue << 'EOF'
<template>
    <Head :title="`Profile - ${roleTitle}`" />
    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Profile</h1>
                <p class="text-gray-600 dark:text-gray-400">{{ roleTitle }} Profile Settings</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <form @submit.prevent="updateProfile">
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Personal Information</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                                <input v-model="form.name" type="text" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 dark:bg-gray-700 dark:text-white">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                                <input :value="profile.email" type="email" disabled
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-400">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                                <input v-model="form.phone" type="tel"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 dark:bg-gray-700 dark:text-white">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role</label>
                                <input :value="roleTitle" type="text" disabled
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-400">
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bio</label>
                            <textarea v-model="form.bio" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 dark:bg-gray-700 dark:text-white"></textarea>
                        </div>
                        
                        <div class="mt-6" v-if="role !== 'visitor'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Skills</label>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="skill in form.skills" :key="skill"
                                    class="px-3 py-1 rounded-full text-sm"
                                    :style="{ 
                                        backgroundColor: themeColor.primary + '20',
                                        color: themeColor.primary
                                    }">
                                    {{ skill }}
                                    <button type="button" @click="removeSkill(skill)" class="ml-2">&times;</button>
                                </span>
                                <input v-model="newSkill" @keyup.enter="addSkill" type="text" placeholder="Add skill..."
                                    class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-2 text-white rounded-lg transition-colors"
                            :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue'
import { Head } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'

const props = defineProps({
    profile: Object,
    role: String
})

const roleTitle = computed(() => {
    const titles = {
        'team-lead': 'Team Lead',
        'team-member': 'Team Member',
        'visitor': 'Visitor'
    }
    return titles[props.role] || 'User'
})

const form = reactive({
    name: props.profile?.name || '',
    phone: props.profile?.phone || '',
    bio: props.profile?.bio || '',
    skills: props.profile?.skills || []
})

const newSkill = ref('')

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

const addSkill = () => {
    if (newSkill.value && !form.skills.includes(newSkill.value)) {
        form.skills.push(newSkill.value)
        newSkill.value = ''
    }
}

const removeSkill = (skill) => {
    form.skills = form.skills.filter(s => s !== skill)
}

const updateProfile = () => {
    const routeName = `${props.role}.profile.update`
    router.put(route(routeName), form)
}
</script>

<style scoped>
input[type="text"]:focus,
input[type="tel"]:focus,
textarea:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>
EOF

echo "Vue pages creation completed!"
echo "Run 'npm run dev' to compile the assets"