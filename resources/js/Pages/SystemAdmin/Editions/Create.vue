<template>
    <Head title="Add Edition" />
    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Add Edition Details</h1>
                <p class="mt-2" :style="{ color: themeColor.primary }">
                    Create a new hackathon edition with all the necessary details.
                </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Edition Name -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Edition Name
                            </label>
                            <input v-model="form.name"
                                   type="text"
                                   id="name"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                                   :class="{ 'border-red-500': form.errors.name }"
                                   :style="{ '--tw-ring-color': themeColor.primary }"
                                   placeholder="Enter hackathon name">
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Year
                                </label>
                                <input v-model="form.year"
                                       type="number"
                                       id="year"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                                       :class="{ 'border-red-500': form.errors.year }"
                                       :style="{ '--tw-ring-color': themeColor.primary }"
                                       placeholder="2024">
                                <p v-if="form.errors.year" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.year }}
                                </p>
                            </div>

                            <div>
                                <label for="admin_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Hackathon Admin
                                </label>
                                <select v-model="form.admin_id"
                                        id="admin_id"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                                        :style="{ '--tw-ring-color': themeColor.primary }">
                                    <option value="">Select Admin</option>
                                    <option v-for="admin in admins" :key="admin.id" :value="admin.id">
                                        {{ admin.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.admin_id" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.admin_id }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Location
                            </label>
                            <input v-model="form.location"
                                   type="text"
                                   id="location"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                                   :style="{ '--tw-ring-color': themeColor.primary }"
                                   placeholder="Enter hackathon location">
                        </div>
                    </div>
                </div>

                <!-- Date Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Date Settings</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="registration_start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Registration Start Date
                            </label>
                            <input v-model="form.registration_start_date"
                                   type="date"
                                   id="registration_start_date"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                                   :class="{ 'border-red-500': form.errors.registration_start_date }"
                                   :style="{ '--tw-ring-color': themeColor.primary }">
                            <p v-if="form.errors.registration_start_date" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.registration_start_date }}
                            </p>
                        </div>

                        <div>
                            <label for="registration_end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Registration End Date
                            </label>
                            <input v-model="form.registration_end_date"
                                   type="date"
                                   id="registration_end_date"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                                   :class="{ 'border-red-500': form.errors.registration_end_date }"
                                   :style="{ '--tw-ring-color': themeColor.primary }">
                            <p v-if="form.errors.registration_end_date" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.registration_end_date }}
                            </p>
                        </div>

                        <div>
                            <label for="hackathon_start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Hackathon Start Date
                            </label>
                            <input v-model="form.hackathon_start_date"
                                   type="date"
                                   id="hackathon_start_date"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                                   :class="{ 'border-red-500': form.errors.hackathon_start_date }"
                                   :style="{ '--tw-ring-color': themeColor.primary }">
                            <p v-if="form.errors.hackathon_start_date" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.hackathon_start_date }}
                            </p>
                        </div>

                        <div>
                            <label for="hackathon_end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Hackathon End Date
                            </label>
                            <input v-model="form.hackathon_end_date"
                                   type="date"
                                   id="hackathon_end_date"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                                   :class="{ 'border-red-500': form.errors.hackathon_end_date }"
                                   :style="{ '--tw-ring-color': themeColor.primary }">
                            <p v-if="form.errors.hackathon_end_date" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.hackathon_end_date }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Configuration -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Configuration</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="max_teams" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Maximum Teams
                            </label>
                            <input v-model="form.max_teams"
                                   type="number"
                                   id="max_teams"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                                   :class="{ 'border-red-500': form.errors.max_teams }"
                                   :style="{ '--tw-ring-color': themeColor.primary }"
                                   placeholder="100">
                            <p v-if="form.errors.max_teams" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.max_teams }}
                            </p>
                        </div>

                        <div>
                            <label for="max_team_members" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Maximum Team Members
                            </label>
                            <input v-model="form.max_team_members"
                                   type="number"
                                   id="max_team_members"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                                   :class="{ 'border-red-500': form.errors.max_team_members }"
                                   :style="{ '--tw-ring-color': themeColor.primary }"
                                   placeholder="5">
                            <p v-if="form.errors.max_team_members" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.max_team_members }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea v-model="form.description"
                                  id="description"
                                  rows="4"
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                                  :style="{ '--tw-ring-color': themeColor.primary }"
                                  placeholder="Enter edition description..."></textarea>
                    </div>

                    <div class="mt-6">
                        <label class="flex items-center">
                            <input v-model="form.is_active"
                                   type="checkbox"
                                   class="rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-2 focus:ring-opacity-50"
                                   :style="{ '--tw-text-opacity': 1, color: themeColor.primary }">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                Set as active edition
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4">
                    <Link :href="route('system-admin.editions.index')"
                          class="px-6 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </Link>
                    <button type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 rounded-lg text-white font-medium transition-all duration-200 disabled:opacity-50"
                            :style="{
                                background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                            }">
                        {{ form.processing ? 'Creating...' : 'Create Edition' }}
                    </button>
                </div>
            </form>
        </div>
    </Default>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import Default from '../../../Layouts/Default.vue'

const props = defineProps({
    admins: {
        type: Array,
        default: () => []
    }
})

const form = useForm({
    name: '',
    year: new Date().getFullYear(),
    registration_start_date: '',
    registration_end_date: '',
    hackathon_start_date: '',
    hackathon_end_date: '',
    admin_id: '',
    description: '',
    location: '',
    max_teams: 100,
    max_team_members: 5,
    is_active: false
})

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

const submit = () => {
    form.post(route('system-admin.editions.store'))
}
</script>

<style scoped>
/* Theme-aware custom styles */
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