<template>
    <Head :title="t('admin.teams.create')" />
    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white" :class="{ 'rtl': isRTL }">{{ t('admin.teams.new_team') }}</h1>
                <p class="mt-2" :style="{ color: themeColor.primary }" :class="{ 'rtl': isRTL }">
                    {{ t('admin.teams.create_description') }}
                </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Basic Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" :class="{ 'rtl': isRTL }">{{ t('admin.form.basic_information') }}</h2>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.teams.name') }} *
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
                                       class="pl-10 w-full h-12 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-opacity-50 focus:border-transparent transition-all duration-200"
                                       :class="{ 'border-red-500 focus:ring-red-500': form.errors.name }"
                                       :style="{ '--tw-ring-color': themeColor.primary }"
                                       :placeholder="t('admin.form.placeholder.enter_name')">
                            </div>
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.form.description') }}
                            </label>
                            <div class="relative">
                                <textarea v-model="form.description"
                                          id="description"
                                          rows="5"
                                          class="w-full p-4 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-opacity-50 focus:border-transparent transition-all duration-200 resize-none"
                                          :style="{ '--tw-ring-color': themeColor.primary }"
                                          :placeholder="t('admin.teams.description_placeholder')"></textarea>
                                <div class="absolute bottom-2 right-2 text-xs text-gray-400 dark:text-gray-500">
                                    {{ form.description?.length || 0 }} / 500
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="edition_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ t('admin.editions.title') }} *
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <select v-model="form.edition_id"
                                            id="edition_id"
                                            class="pl-10 w-full h-12 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-opacity-50 focus:border-transparent transition-all duration-200 appearance-none cursor-pointer"
                                            :class="{ 'border-red-500 focus:ring-red-500': form.errors.edition_id }"
                                            :style="{ '--tw-ring-color': themeColor.primary }">
                                        <option value="" disabled>{{ t('admin.editions.select_edition') }}</option>
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
                                    {{ t('admin.teams.max_members') }}
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
                                           class="pl-10 pr-20 w-full h-12 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-opacity-50 focus:border-transparent transition-all duration-200"
                                           :style="{ '--tw-ring-color': themeColor.primary }"
                                           placeholder="5">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ t('admin.teams.members') }}</span>
                                    </div>
                                </div>
                                <p v-if="form.errors.max_members" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.max_members }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Team Leader -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" :class="{ 'rtl': isRTL }">{{ t('admin.teams.leader') }}</h2>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ t('admin.teams.search_select_leader') }}
                        </label>
                        <div class="relative">
                            <input v-model="leaderSearch"
                                   @input="searchLeaders"
                                   type="text"
                                   :placeholder="t('admin.teams.search_users_placeholder')"
                                   class="w-full h-12 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors pl-10"
                                   :style="{ '--tw-ring-color': themeColor.primary }">
                            <svg class="absolute left-3 top-4 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>

                        <!-- Search Results -->
                        <div v-if="leaderSearchResults.length > 0" 
                             class="mt-2 max-h-48 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg">
                            <div v-for="user in leaderSearchResults" :key="user.id"
                                 @click="selectLeader(user)"
                                 class="px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                <div class="font-medium text-gray-900 dark:text-white">{{ user.name }}</div>
                                <div class="text-sm" :style="{ color: themeColor.primary }">{{ user.email }}</div>
                            </div>
                        </div>

                        <!-- Selected Leader -->
                        <div v-if="selectedLeader" class="mt-4 p-4 rounded-lg border-2 border-dashed" 
                             :style="{ borderColor: themeColor.primary + '40' }">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ selectedLeader.name }}</div>
                                    <div class="text-sm" :style="{ color: themeColor.primary }">{{ selectedLeader.email }}</div>
                                </div>
                                <button @click="clearLeader" type="button"
                                        class="text-red-500 hover:text-red-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Initial Members (Optional) -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" :class="{ 'rtl': isRTL }">{{ t('admin.teams.initial_members') }}</h2>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ t('admin.teams.add_member') }}
                        </label>
                        <div class="relative">
                            <input v-model="memberSearch"
                                   @input="searchMembers"
                                   type="text"
                                   :placeholder="t('admin.teams.search_users_placeholder')"
                                   class="w-full h-12 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors pl-10"
                                   :style="{ '--tw-ring-color': themeColor.primary }">
                            <svg class="absolute left-3 top-4 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>

                        <!-- Search Results -->
                        <div v-if="memberSearchResults.length > 0" 
                             class="mt-2 max-h-48 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg">
                            <div v-for="user in memberSearchResults" :key="user.id"
                                 @click="addMember(user)"
                                 class="px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                <div class="font-medium text-gray-900 dark:text-white">{{ user.name }}</div>
                                <div class="text-sm" :style="{ color: themeColor.primary }">{{ user.email }}</div>
                            </div>
                        </div>

                        <!-- Selected Members -->
                        <div v-if="form.members.length > 0" class="mt-4 space-y-2">
                            <div v-for="(member, index) in form.members" :key="member.id"
                                 class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ member.name }}</div>
                                    <div class="text-sm" :style="{ color: themeColor.primary }">{{ member.email }}</div>
                                </div>
                                <button @click="removeMember(index)" type="button"
                                        class="text-red-500 hover:text-red-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4 pt-6">
                    <Link :href="route('system-admin.teams.index')"
                          class="px-8 py-3 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium">
                        {{ t('admin.actions.cancel') }}
                    </Link>
                    <button type="submit"
                            :disabled="form.processing"
                            class="px-8 py-3 rounded-lg text-white font-medium transition-all duration-200 disabled:opacity-50 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                            :style="{
                                background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                            }">
                        {{ form.processing ? t('admin.actions.creating') : t('admin.teams.create') }}
                    </button>
                </div>
            </form>
        </div>
    </Default>
</template>

<script setup>
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL, direction, locale } = useLocalization()
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import Default from '../../../Layouts/Default.vue'
import axios from 'axios'

const props = defineProps({
    editions: {
        type: Array,
        default: () => []
    }
})

const form = useForm({
    name: '',
    description: '',
    edition_id: '',
    leader_id: null,
    max_members: 5,
    members: []
})

const leaderSearch = ref('')
const leaderSearchResults = ref([])
const selectedLeader = ref(null)

const memberSearch = ref('')
const memberSearchResults = ref([])

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

const searchLeaders = async () => {
    if (leaderSearch.value.length < 2) {
        leaderSearchResults.value = []
        return
    }

    try {
        const response = await axios.get(route('system-admin.users.search'), {
            params: { q: leaderSearch.value }
        })
        leaderSearchResults.value = response.data.users || []
    } catch (error) {
        console.error('Error searching leaders:', error)
        leaderSearchResults.value = []
    }
}

const selectLeader = (user) => {
    selectedLeader.value = user
    form.leader_id = user.id
    leaderSearchResults.value = []
    leaderSearch.value = ''
}

const clearLeader = () => {
    selectedLeader.value = null
    form.leader_id = null
}

const searchMembers = async () => {
    if (memberSearch.value.length < 2) {
        memberSearchResults.value = []
        return
    }

    try {
        const response = await axios.get(route('system-admin.users.search'), {
            params: { q: memberSearch.value }
        })
        // Filter out already selected members and the leader
        memberSearchResults.value = (response.data.users || []).filter(user => 
            !form.members.find(m => m.id === user.id) && 
            user.id !== form.leader_id
        )
    } catch (error) {
        console.error('Error searching members:', error)
        memberSearchResults.value = []
    }
}

const addMember = (user) => {
    if (!form.members.find(m => m.id === user.id)) {
        form.members.push(user)
    }
    memberSearchResults.value = []
    memberSearch.value = ''
}

const removeMember = (index) => {
    form.members.splice(index, 1)
}

const submit = () => {
    // Transform members array to just IDs for submission
    const data = {
        ...form.data(),
        member_ids: form.members.map(m => m.id)
    }
    
    form.transform(() => data).post(route('system-admin.teams.store'))
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