<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Add Member to {{ team.name }}
                    </h3>
                    <button @click="$emit('close')"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form @submit.prevent="submit" class="p-6">
                <!-- Search User -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Search User
                    </label>
                    <div class="relative">
                        <input v-model="searchQuery"
                               @input="searchUsers"
                               type="text"
                               placeholder="Type to search users..."
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors pl-10"
                               :style="{ '--tw-ring-color': themeColor.primary }">
                        <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <!-- Search Results -->
                    <div v-if="searchResults.length > 0" 
                         class="mt-2 max-h-48 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg">
                        <div v-for="user in searchResults" :key="user.id"
                             @click="selectUser(user)"
                             class="px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ user.name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</div>
                                </div>
                                <span v-if="user.team_id" 
                                      class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                    In Team
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selected User -->
                <div v-if="form.user_id" class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Selected User
                    </label>
                    <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ selectedUser.name }}</div>
                                <div class="text-sm" :style="{ color: themeColor.primary }">{{ selectedUser.email }}</div>
                            </div>
                            <button @click="clearSelection" type="button"
                                    class="text-red-500 hover:text-red-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Role Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Member Role
                    </label>
                    <select v-model="form.role"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                            :style="{ '--tw-ring-color': themeColor.primary }">
                        <option value="member">Team Member</option>
                        <option value="leader">Team Leader</option>
                        <option value="co-leader">Co-Leader</option>
                    </select>
                </div>

                <!-- Error Messages -->
                <div v-if="form.errors && Object.keys(form.errors).length > 0" 
                     class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <div v-for="(error, key) in form.errors" :key="key" class="text-sm text-red-600 dark:text-red-400">
                        {{ error }}
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4">
                    <button @click="$emit('close')" type="button"
                            class="px-6 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            :disabled="!form.user_id || form.processing"
                            class="px-6 py-2 rounded-lg text-white font-medium transition-all duration-200 disabled:opacity-50"
                            :style="{
                                background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                            }">
                        {{ form.processing ? 'Adding...' : 'Add Member' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import axios from 'axios'

const props = defineProps({
    team: {
        type: Object,
        required: true
    },
    themeColor: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['close', 'success'])

const searchQuery = ref('')
const searchResults = ref([])
const selectedUser = ref(null)
const searching = ref(false)

const form = useForm({
    user_id: null,
    team_id: props.team.id,
    role: 'member'
})

const searchUsers = async () => {
    if (searchQuery.value.length < 2) {
        searchResults.value = []
        return
    }

    searching.value = true
    try {
        const response = await axios.get(route('system-admin.users.search'), {
            params: { q: searchQuery.value }
        })
        searchResults.value = response.data.users || []
    } catch (error) {
        console.error('Error searching users:', error)
        searchResults.value = []
    } finally {
        searching.value = false
    }
}

const selectUser = (user) => {
    if (user.team_id && user.team_id !== props.team.id) {
        if (!confirm(`${user.name} is already in another team. Do you want to move them to this team?`)) {
            return
        }
    }
    
    selectedUser.value = user
    form.user_id = user.id
    searchResults.value = []
    searchQuery.value = ''
}

const clearSelection = () => {
    selectedUser.value = null
    form.user_id = null
}

const submit = () => {
    form.post(route('system-admin.teams.add-member', props.team.id), {
        onSuccess: () => {
            emit('success')
        },
        onError: (errors) => {
            console.error('Error adding member:', errors)
        }
    })
}
</script>

<style scoped>
input[type="text"]:focus,
select:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>