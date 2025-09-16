<template>
    <Head title="Edit Team" />
    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Team</h1>
                <p class="mt-2" :style="{ color: themeColor.primary }">
                    Update team details and manage members
                </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Basic Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Team Name *
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <input v-model="form.name"
                                       type="text"
                                       id="name"
                                       class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-opacity-50 focus:border-transparent transition-all duration-200"
                                       :class="{ 'border-red-500 focus:ring-red-500': form.errors.name }"
                                       :style="{ '--tw-ring-color': themeColor.primary }"
                                       placeholder="Enter team name">
                            </div>
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Team Description
                            </label>
                            <div class="relative">
                                <textarea v-model="form.description"
                                          id="description"
                                          rows="4"
                                          class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-opacity-50 focus:border-transparent transition-all duration-200 resize-none"
                                          :style="{ '--tw-ring-color': themeColor.primary }"
                                          placeholder="Describe your team's mission and goals..."></textarea>
                                <div class="absolute bottom-2 right-2 text-xs text-gray-400 dark:text-gray-500">
                                    {{ form.description?.length || 0 }} / 500
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="edition_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Hackathon Edition *
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <select v-model="form.edition_id"
                                            id="edition_id"
                                            class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-opacity-50 focus:border-transparent transition-all duration-200 appearance-none cursor-pointer"
                                            :class="{ 'border-red-500 focus:ring-red-500': form.errors.edition_id }"
                                            :style="{ '--tw-ring-color': themeColor.primary }">
                                        <option value="" disabled>Select Edition</option>
                                        <option v-for="edition in editions" :key="edition.id" :value="edition.id">
                                            {{ edition.name }} ({{ edition.year }})
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p v-if="form.errors.edition_id" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.edition_id }}
                                </p>
                            </div>

                            <div>
                                <label for="max_members" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Maximum Team Members
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                    </div>
                                    <input v-model="form.max_members"
                                           type="number"
                                           id="max_members"
                                           min="1"
                                           max="10"
                                           class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-opacity-50 focus:border-transparent transition-all duration-200"
                                           :style="{ '--tw-ring-color': themeColor.primary }"
                                           placeholder="5">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">members</span>
                                    </div>
                                </div>
                                <p v-if="form.errors.max_members" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.max_members }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Team Status
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <div class="w-2 h-2 rounded-full"
                                         :class="{
                                             'bg-green-500': form.status === 'active',
                                             'bg-gray-500': form.status === 'inactive',
                                             'bg-red-500': form.status === 'disqualified'
                                         }"></div>
                                </div>
                                <select v-model="form.status"
                                        id="status"
                                        class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-opacity-50 focus:border-transparent transition-all duration-200 appearance-none cursor-pointer"
                                        :style="{ '--tw-ring-color': themeColor.primary }">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="disqualified">Disqualified</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Team Members -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Team Members</h2>
                        <button @click.prevent="openAddMemberModal" type="button"
                                class="inline-flex items-center px-3 py-1.5 rounded-lg text-white text-sm font-medium transition-all duration-200"
                                :style="{
                                    background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                                }">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Add Member
                        </button>
                    </div>

                    <!-- Members List -->
                    <div v-if="team.members && team.members.length > 0" class="space-y-2">
                        <div v-for="member in team.members" :key="member.id"
                             class="flex items-center justify-between p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold"
                                     :style="{ backgroundColor: themeColor.primary }">
                                    {{ member.name.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        {{ member.name }}
                                        <span v-if="member.id === team.leader_id" 
                                              class="ml-2 text-xs px-2 py-0.5 rounded-full text-white"
                                              :style="{ backgroundColor: themeColor.primary }">
                                            Leader
                                        </span>
                                    </div>
                                    <div class="text-sm" :style="{ color: themeColor.primary }">
                                        {{ member.email }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button v-if="member.id !== team.leader_id"
                                        @click.prevent="makeLeader(member)"
                                        type="button"
                                        class="text-sm font-medium hover:underline transition-colors"
                                        :style="{ color: themeColor.primary }">
                                    Make Leader
                                </button>
                                <button @click.prevent="removeMember(member)"
                                        type="button"
                                        class="text-red-500 hover:text-red-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                        No members in this team yet. Click "Add Member" to add team members.
                    </div>
                </div>

                <!-- Team Idea (if exists) -->
                <div v-if="team.idea" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Associated Idea</h2>
                    
                    <div class="p-4 rounded-lg border-2 border-dashed" 
                         :style="{ borderColor: themeColor.primary + '40' }">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ team.idea.title }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ team.idea.description }}</p>
                                <div class="flex items-center gap-4 mt-2">
                                    <span class="text-sm" :style="{ color: themeColor.primary }">
                                        Status: {{ team.idea.status }}
                                    </span>
                                    <span class="text-sm" :style="{ color: themeColor.primary }">
                                        Track: {{ team.idea.track?.name || 'No Track' }}
                                    </span>
                                </div>
                            </div>
                            <Link :href="route('track-supervisor.ideas.edit', team.idea.id)"
                                  class="text-sm font-medium hover:underline transition-colors"
                                  :style="{ color: themeColor.primary }">
                                View Idea
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4">
                    <Link :href="route('track-supervisor.teams.index')"
                          class="px-6 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </Link>
                    <button type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 rounded-lg text-white font-medium transition-all duration-200 disabled:opacity-50"
                            :style="{
                                background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                            }">
                        {{ form.processing ? 'Updating...' : 'Update Team' }}
                    </button>
                </div>
            </form>

            <!-- Add Member Modal -->
            <AddMemberModal v-if="showAddMemberModal" 
                            :team="team"
                            :theme-color="themeColor"
                            @close="closeAddMemberModal"
                            @success="handleMemberAdded" />
        </div>
    </Default>
</template>

<script setup>
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL, direction, locale } = useLocalization()
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import Default from '../../../Layouts/Default.vue'
import AddMemberModal from './AddMemberModal.vue'

const props = defineProps({
    team: {
        type: Object,
        required: true
    },
    editions: {
        type: Array,
        default: () => []
    }
})

const form = useForm({
    name: props.team.name || '',
    description: props.team.description || '',
    edition_id: props.team.edition_id || '',
    max_members: props.team.max_members || 5,
    status: props.team.status || 'active',
    leader_id: props.team.leader_id
})

const showAddMemberModal = ref(false)

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

const openAddMemberModal = () => {
    showAddMemberModal.value = true
}

const closeAddMemberModal = () => {
    showAddMemberModal.value = false
}

const handleMemberAdded = () => {
    closeAddMemberModal()
    router.reload({ preserveScroll: true })
}

const makeLeader = (member) => {
    if (confirm(`Are you sure you want to make ${member.name} the team leader?`)) {
        form.leader_id = member.id
        form.put(route('track-supervisor.teams.update', props.team.id))
    }
}

const removeMember = (member) => {
    if (confirm(`Are you sure you want to remove ${member.name} from the team?`)) {
        router.delete(route('track-supervisor.teams.remove-member', [props.team.id, member.id]), {
            preserveScroll: true
        })
    }
}

const submit = () => {
    form.put(route('track-supervisor.teams.update', props.team.id))
}
</script>

<style scoped>
input[type="text"]:focus,
input[type="number"]:focus,
select:focus,
textarea:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>