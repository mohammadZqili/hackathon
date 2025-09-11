<template>
    <Head title="Teams Management" />
    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <div class="flex flex-row items-start justify-between flex-wrap content-start p-4 gap-x-0 gap-y-3">
                <div class="w-72 flex flex-col items-start justify-start min-w-[288px]">
                    <h1 class="text-[32px] font-bold text-gray-900 dark:text-white leading-10">Teams</h1>
                </div>
                <button @click="openCreateModal"
                        class="rounded-xl h-8 overflow-hidden flex flex-row items-center justify-center py-0 px-4 min-w-[84px] max-w-[480px] text-center text-sm text-white font-medium transition-all duration-200 hover:shadow-md"
                        :style="{
                            background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                        }">
                    <div class="overflow-hidden flex flex-col items-center justify-start">
                        <div class="self-stretch relative leading-[21px] font-medium overflow-hidden text-ellipsis whitespace-nowrap">New Team</div>
                    </div>
                </button>
            </div>

            <!-- Search Bar -->
            <div class="flex flex-col items-start justify-start py-3 px-4">
                <div class="self-stretch h-12 flex flex-col items-start justify-start min-w-[160px] max-w-2xl">
                    <div class="self-stretch flex-1 rounded-xl flex flex-row items-start justify-start">
                        <div class="self-stretch w-10 rounded-tl-xl rounded-tr-none rounded-br-none rounded-bl-xl flex items-center justify-center"
                             :style="{ backgroundColor: themeColor.primary + '20' }">
                            <svg class="w-5 h-5" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <div class="self-stretch flex-1 rounded-tl-none rounded-tr-xl rounded-br-xl rounded-bl-none bg-gray-100 dark:bg-gray-700 overflow-hidden flex flex-row items-center justify-start py-2 pl-2 pr-4">
                            <input v-model="searchQuery"
                                   @input="handleSearch"
                                   type="text"
                                   placeholder="Search teams"
                                   class="w-full bg-transparent text-gray-700 dark:text-gray-300 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teams Table -->
            <div class="flex flex-col items-start justify-start py-3 px-4">
                <div class="self-stretch rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-row items-start justify-start">
                    <div class="flex-1 flex flex-col items-start justify-start">
                        <div class="self-stretch flex flex-col items-start justify-start">
                            <div class="self-stretch flex-1 bg-white dark:bg-gray-800 flex flex-row items-start justify-start">
                                <div class="self-stretch w-[246px] flex flex-col items-start justify-start py-3 px-4 box-border">
                                    <div class="self-stretch relative leading-[21px] font-medium text-sm text-gray-700 dark:text-gray-300">Team Name</div>
                                </div>
                                <div class="self-stretch w-[253px] flex flex-col items-start justify-start py-3 px-4 box-border">
                                    <div class="self-stretch relative leading-[21px] font-medium text-sm text-gray-700 dark:text-gray-300">Founding Date</div>
                                </div>
                                <div class="self-stretch w-64 flex flex-col items-start justify-start py-3 px-4 box-border">
                                    <div class="self-stretch relative leading-[21px] font-medium text-sm text-gray-700 dark:text-gray-300">Team Leader</div>
                                </div>
                                <div class="self-stretch w-[171px] flex flex-col items-start justify-start py-3 px-4 box-border">
                                    <div class="self-stretch relative leading-[21px] font-medium text-sm" :style="{ color: themeColor.primary }">Actions</div>
                                </div>
                            </div>
                        </div>
                        <div class="self-stretch flex flex-col items-start justify-start">
                            <div v-if="!teams.data || teams.data.length === 0" class="self-stretch border-t border-gray-200 dark:border-gray-700 box-border h-[72px] flex flex-row items-center justify-center">
                                <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                                    No teams found. Click "New Team" to create your first team.
                                </div>
                            </div>
                            <div v-else v-for="team in teams.data" :key="team.id"
                                 class="self-stretch border-t border-gray-200 dark:border-gray-700 box-border h-[72px] flex flex-row items-start justify-start hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="w-[246px] h-[72px] flex flex-col items-center justify-center py-2 px-4 box-border">
                                    <div class="self-stretch relative leading-[21px] text-gray-900 dark:text-white">{{ team.name }}</div>
                                </div>
                                <div class="w-[253px] h-[72px] flex flex-col items-center justify-center py-2 px-4 box-border">
                                    <div class="self-stretch relative leading-[21px]" :style="{ color: themeColor.primary }">{{ formatDate(team.created_at) }}</div>
                                </div>
                                <div class="w-64 h-[72px] flex flex-col items-center justify-center py-2 px-4 box-border">
                                    <div class="self-stretch relative leading-[21px]" :style="{ color: themeColor.primary }">{{ team.leader?.name || 'No Leader' }}</div>
                                </div>
                                <div class="w-[171px] h-[72px] flex flex-col items-center justify-center py-2 px-4 box-border">
                                    <div class="flex items-center gap-2">
                                        <button @click="() => router.visit(route('system-admin.teams.edit', team.id))"
                                                class="font-bold hover:underline transition-colors text-sm"
                                                :style="{ color: themeColor.primary }">
                                            Edit
                                        </button>
                                        <span :style="{ color: themeColor.primary }">|</span>
                                        <button @click="openAddMemberModal(team)"
                                                class="font-bold hover:underline transition-colors text-sm"
                                                :style="{ color: themeColor.primary }">
                                            Members
                                        </button>
                                        <span :style="{ color: themeColor.primary }">|</span>
                                        <button @click="deleteTeam(team)"
                                                class="font-bold hover:underline transition-colors text-sm"
                                                :style="{ color: themeColor.primary }">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="teams.links && teams.total > teams.per_page" 
                 class="px-6 py-3">
                <nav class="flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Showing {{ teams.from }} to {{ teams.to }} of {{ teams.total }} results
                    </div>
                    <div class="flex items-center gap-2">
                        <template v-for="link in teams.links" :key="link.label">
                            <Link v-if="link.url"
                                  :href="link.url"
                                  class="px-3 py-1 rounded-md text-sm font-medium transition-colors"
                                  :style="link.active ? {
                                      backgroundColor: themeColor.primary,
                                      color: 'white'
                                  } : {}"
                                  :class="!link.active ? 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 border border-gray-300 dark:border-gray-600' : ''"
                                  v-html="link.label">
                            </Link>
                            <span v-else
                                  class="px-3 py-1 rounded-md text-sm font-medium text-gray-400 dark:text-gray-500"
                                  v-html="link.label">
                            </span>
                        </template>
                    </div>
                </nav>
            </div>

            <!-- Add Member Modal -->
            <AddMemberModal v-if="showAddMemberModal" 
                            :team="selectedTeam"
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
    teams: {
        type: Object,
        required: true
    }
})

const searchQuery = ref('')
const showAddMemberModal = ref(false)
const selectedTeam = ref(null)

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

const deleteTeam = (team) => {
    if (confirm(`Are you sure you want to delete the team "${team.name}"?`)) {
        useForm({}).delete(route('system-admin.teams.destroy', team.id))
    }
}

const formatDate = (date) => {
    if (!date) return 'N/A'
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    })
}

const handleSearch = () => {
    router.get(route('system-admin.teams.index'), {
        search: searchQuery.value
    }, {
        preserveState: true,
        preserveScroll: true
    })
}

const openCreateModal = () => {
    router.visit(route('system-admin.teams.create'))
}

const openAddMemberModal = (team) => {
    selectedTeam.value = team
    showAddMemberModal.value = true
}

const closeAddMemberModal = () => {
    showAddMemberModal.value = false
    selectedTeam.value = null
}

const handleMemberAdded = () => {
    closeAddMemberModal()
    router.reload({ preserveScroll: true })
}
</script>

<style scoped>
input[type="text"]:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>