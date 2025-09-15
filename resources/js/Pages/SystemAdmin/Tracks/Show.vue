<template>
    <Default>
        <Head :title="track.name" />
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white" :class="{ 'rtl': isRTL }">
                            {{ track.name }}
                        </h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-400" :class="{ 'rtl': isRTL }">
                            {{ t('admin.tracks.view_details') }}
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <Link :href="route('system-admin.tracks.edit', track.id)"
                              class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            {{ t('admin.buttons.edit') }}
                        </Link>
                        <Link :href="route('system-admin.tracks.index')"
                              class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            {{ t('admin.buttons.back') }}
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Track Details -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" :class="{ 'rtl': isRTL }">
                            {{ t('admin.form.basic_information') }}
                        </h2>

                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ t('admin.tracks.name') }}
                                </dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">
                                    {{ track.name }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ t('admin.tracks.description') }}
                                </dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">
                                    {{ track.description }}
                                </dd>
                            </div>

                            <div v-if="track.hackathon">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ t('admin.hackathons.title') }}
                                </dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">
                                    {{ track.hackathon.name }}
                                </dd>
                            </div>

                            <div v-if="track.max_teams">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ t('admin.tracks.max_teams') }}
                                </dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">
                                    {{ track.max_teams }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ t('admin.form.status') }}
                                </dt>
                                <dd class="mt-1">
                                    <span v-if="track.is_active"
                                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        {{ t('admin.status.active') }}
                                    </span>
                                    <span v-else
                                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        {{ t('admin.status.inactive') }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Evaluation Criteria -->
                    <div v-if="track.evaluation_criteria && track.evaluation_criteria.length > 0"
                         class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" :class="{ 'rtl': isRTL }">
                            {{ t('admin.tracks.evaluation_criteria') }}
                        </h2>

                        <ul class="space-y-2">
                            <li v-for="(criterion, index) in track.evaluation_criteria" :key="index"
                                class="flex items-start">
                                <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" :style="{ color: themeColor.primary }" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-900 dark:text-white">{{ criterion }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Sidebar Statistics -->
                <div class="space-y-6">
                    <!-- Statistics Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" :class="{ 'rtl': isRTL }">
                            {{ t('admin.statistics.title') }}
                        </h2>

                        <dl class="space-y-4">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ t('admin.teams.total') }}
                                </dt>
                                <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ track.teams_count || 0 }}
                                </dd>
                            </div>

                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ t('admin.ideas.total') }}
                                </dt>
                                <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ track.ideas_count || 0 }}
                                </dd>
                            </div>

                            <div v-if="track.max_teams" class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex justify-between mb-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ t('admin.tracks.capacity') }}
                                    </dt>
                                    <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ Math.round((track.teams_count || 0) / track.max_teams * 100) }}%
                                    </dd>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all duration-300"
                                         :style="{
                                             width: `${Math.min(100, Math.round((track.teams_count || 0) / track.max_teams * 100))}%`,
                                             backgroundColor: themeColor.primary
                                         }"></div>
                                </div>
                            </div>
                        </dl>
                    </div>

                    <!-- Timestamps -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" :class="{ 'rtl': isRTL }">
                            {{ t('admin.timestamps.title') }}
                        </h2>

                        <dl class="space-y-3">
                            <div v-if="track.created_at">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ t('admin.timestamps.created_at') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ new Date(track.created_at).toLocaleString() }}
                                </dd>
                            </div>

                            <div v-if="track.updated_at">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ t('admin.timestamps.updated_at') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ new Date(track.updated_at).toLocaleString() }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" :class="{ 'rtl': isRTL }">
                            {{ t('admin.actions.title') }}
                        </h2>

                        <div class="space-y-3">
                            <Link :href="route('system-admin.tracks.edit', track.id)"
                                  class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-lg text-white font-medium transition-all duration-200"
                                  :style="{ backgroundColor: themeColor.primary }">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                {{ t('admin.buttons.edit') }}
                            </Link>

                            <button @click="confirmDelete"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 dark:border-red-600 rounded-lg text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                {{ t('admin.buttons.delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL: isRTLFromLocale, direction, locale } = useLocalization()

const props = defineProps({
    track: {
        type: Object,
        required: true
    }
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

// Methods
const confirmDelete = () => {
    if (confirm(t('admin.tracks.confirm_delete'))) {
        router.delete(route('system-admin.tracks.destroy', props.track.id))
    }
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
/* RTL support */
.rtl {
    direction: rtl;
    text-align: right;
}
</style>