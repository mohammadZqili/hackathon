<template>
    <Default>
        <Head :title="t('admin.tracks.create')" />
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white" :class="{ 'rtl': isRTL }">
                            {{ t('admin.tracks.create') }}
                        </h1>
                        <p class="mt-2" :style="{ color: themeColor.primary }" :class="{ 'rtl': isRTL }">
                            {{ t('admin.tracks.create_description') }}
                        </p>
                    </div>
                    <Link :href="route('system-admin.tracks.index')"
                          class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ t('admin.buttons.back') }}
                    </Link>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Basic Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" :class="{ 'rtl': isRTL }">
                        {{ t('admin.form.basic_information') }}
                    </h2>

                    <div class="grid grid-cols-1 gap-6">
                        <!-- Track Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.tracks.name') }} *
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                                <input v-model="form.name"
                                       type="text"
                                       id="name"
                                       class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-opacity-50 focus:border-transparent transition-all duration-200"
                                       :class="{ 'border-red-500 focus:ring-red-500': form.errors.name }"
                                       :style="{ '--tw-ring-color': themeColor.primary }"
                                       :placeholder="t('admin.form.placeholder.enter_name')"
                                       required>
                            </div>
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.tracks.description') }} *
                            </label>
                            <div class="relative">
                                <textarea v-model="form.description"
                                          id="description"
                                          rows="4"
                                          class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-opacity-50 focus:border-transparent transition-all duration-200 resize-none"
                                          :class="{ 'border-red-500 focus:ring-red-500': form.errors.description }"
                                          :style="{ '--tw-ring-color': themeColor.primary }"
                                          :placeholder="t('admin.tracks.description_placeholder')"
                                          required></textarea>
                                <div class="absolute bottom-2 right-2 text-xs text-gray-400 dark:text-gray-500">
                                    {{ form.description?.length || 0 }} / 1000
                                </div>
                            </div>
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- Hackathon and Max Teams -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Hackathon -->
                            <div>
                                <label for="hackathon_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ t('admin.hackathons.title') }} *
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <select v-model="form.hackathon_id"
                                            id="hackathon_id"
                                            class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-opacity-50 focus:border-transparent transition-all duration-200 appearance-none cursor-pointer"
                                            :class="{ 'border-red-500 focus:ring-red-500': form.errors.hackathon_id }"
                                            :style="{ '--tw-ring-color': themeColor.primary }"
                                            required>
                                        <option value="" disabled>{{ t('admin.hackathons.select_hackathon') }}</option>
                                        <option v-for="hackathon in hackathons" :key="hackathon.id" :value="hackathon.id">
                                            {{ hackathon.name }}
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p v-if="form.errors.hackathon_id" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.hackathon_id }}
                                </p>
                            </div>

                            <!-- Max Teams -->
                            <div>
                                <label for="max_teams" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ t('admin.tracks.max_teams') }}
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <input v-model="form.max_teams"
                                           type="number"
                                           id="max_teams"
                                           min="1"
                                           class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-opacity-50 focus:border-transparent transition-all duration-200"
                                           :style="{ '--tw-ring-color': themeColor.primary }"
                                           :placeholder="t('admin.tracks.max_teams_placeholder')">
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.form.status') }}
                            </label>
                            <div class="flex items-center space-x-6">
                                <label class="flex items-center cursor-pointer">
                                    <input v-model="form.is_active"
                                           type="radio"
                                           :value="true"
                                           class="w-4 h-4 text-primary border-gray-300 focus:ring-2 focus:ring-primary"
                                           :style="{ color: themeColor.primary }">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ t('admin.status.active') }}
                                    </span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input v-model="form.is_active"
                                           type="radio"
                                           :value="false"
                                           class="w-4 h-4 text-primary border-gray-300 focus:ring-2 focus:ring-primary">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ t('admin.status.inactive') }}
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Assigned Supervisor -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.tracks.assigned_supervisor') }}
                            </label>
                            <select v-model="form.supervisor_id"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-opacity-50 focus:border-transparent transition-all duration-200"
                                    :style="{ '--tw-ring-color': themeColor.primary }">
                                <option value="">{{ t('admin.tracks.select_supervisor') }}</option>
                                <option v-for="supervisor in supervisors" :key="supervisor.id" :value="supervisor.id">
                                    {{ supervisor.name }} - {{ supervisor.user_type === 'system_admin' ? 'System Admin' : 'Track Supervisor' }}
                                </option>
                            </select>
                            <p v-if="form.errors.supervisor_id" class="mt-1 text-sm text-red-600">{{ form.errors.supervisor_id }}</p>
                        </div>

                        <!-- Evaluation Criteria (Optional) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.tracks.evaluation_criteria') }}
                            </label>
                            <div class="space-y-3">
                                <div v-for="(criterion, index) in form.evaluation_criteria" :key="index" class="flex items-center space-x-2">
                                    <input v-model="form.evaluation_criteria[index]"
                                           type="text"
                                           class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-opacity-50 focus:border-transparent transition-all duration-200"
                                           :style="{ '--tw-ring-color': themeColor.primary }"
                                           :placeholder="`${t('admin.tracks.criterion')} ${index + 1}`">
                                    <button @click="removeCriterion(index)"
                                            type="button"
                                            class="p-2 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                                <button @click="addCriterion"
                                        type="button"
                                        class="inline-flex items-center px-3 py-2 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-600 dark:text-gray-400 hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    {{ t('admin.tracks.add_criterion') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4">
                    <Link :href="route('system-admin.tracks.index')"
                          class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200">
                        {{ t('admin.buttons.cancel') }}
                    </Link>
                    <button type="submit"
                            :disabled="form.processing"
                            class="px-6 py-3 rounded-lg text-white font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            :style="{
                                backgroundColor: form.processing ? '#9CA3AF' : themeColor.primary,
                                ':hover': { backgroundColor: themeColor.hover }
                            }">
                        <span v-if="!form.processing" class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            {{ t('admin.buttons.create') }}
                        </span>
                        <span v-else class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ t('admin.buttons.creating') }}
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </Default>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useForm, Head, Link } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL: isRTLFromLocale, direction, locale } = useLocalization()

const props = defineProps({
    hackathons: Array,
    editions: Array,
    supervisors: Array
})

// Theme configuration
const themeColor = ref({
    primary: '#0d9488',
    hover: '#0f766e',
    rgb: '13, 148, 136'
})

const themeStyles = computed(() => ({
    '--primary-color': themeColor.value.primary,
    '--primary-hover': themeColor.value.hover,
    '--primary-rgb': themeColor.value.rgb
}))

// Use isRTL from localization composable
const isRTL = isRTLFromLocale

// Form setup
const form = useForm({
    name: '',
    description: '',
    hackathon_id: '',
    edition_id: null,
    max_teams: null,
    supervisor_id: null,
    evaluation_criteria: [],
    is_active: true
})

// Methods
const submit = () => {
    form.post(route('system-admin.tracks.store'))
}

const addCriterion = () => {
    form.evaluation_criteria.push('')
}

const removeCriterion = (index) => {
    form.evaluation_criteria.splice(index, 1)
}

// Dynamic theme color
onMounted(() => {
    const root = document.documentElement
    const primaryColor = getComputedStyle(root).getPropertyValue('--primary-color').trim()
    if (primaryColor) {
        themeColor.value.primary = primaryColor
        themeColor.value.hover = getComputedStyle(root).getPropertyValue('--primary-hover').trim() || '#0f766e'

        // Convert hex to RGB for opacity variations
        const hex = primaryColor.replace('#', '')
        const r = parseInt(hex.substr(0, 2), 16)
        const g = parseInt(hex.substr(2, 2), 16)
        const b = parseInt(hex.substr(4, 2), 16)
        themeColor.value.rgb = `${r}, ${g}, ${b}`
    }
})
</script>

<style scoped>
/* Custom radio button styling */
input[type="radio"]:checked {
    background-color: v-bind('themeColor.primary');
    border-color: v-bind('themeColor.primary');
}

/* Custom select dropdown arrow */
select {
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}

/* RTL support */
.rtl {
    direction: rtl;
    text-align: right;
}

.rtl select {
    background-position: left 0.5rem center;
    padding-right: 0.75rem;
    padding-left: 2.5rem;
}
</style>