<template>
    <Head :title="t('admin.editions.title')" />
    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white" :class="{ 'text-right': isRTL, 'text-left': !isRTL }">{{ t('admin.editions.title') }}</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400" :class="{ 'text-right': isRTL, 'text-left': !isRTL }">
                        {{ t('admin.editions.description', 'Manage hackathon editions and their configurations') }}
                    </p>
                </div>

                <Link :href="route('system-admin.editions.create')"
                      class="inline-flex items-center px-4 py-2 rounded-lg font-semibold text-white transition-all duration-200 shadow-sm hover:shadow-md mt-4 sm:mt-0"
                      :style="{
                          background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                      }">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ t('admin.editions.create') }}
                </Link>
            </div>

            <!-- Editions Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" :class="{ 'text-right': isRTL, 'text-left': !isRTL }">
                                    {{ t('admin.form.name') }}
                                </th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" :class="{ 'text-right': isRTL, 'text-left': !isRTL }">
                                    {{ t('admin.editions.year') }}
                                </th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" :class="{ 'text-right': isRTL, 'text-left': !isRTL }">
                                    {{ t('admin.editions.registration_dates') }}
                                </th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" :class="{ 'text-right': isRTL, 'text-left': !isRTL }">
                                    {{ t('admin.teams.title') }}
                                </th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" :class="{ 'text-right': isRTL, 'text-left': !isRTL }">
                                    {{ t('admin.editions.hackathon_admin') }}
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ t('admin.form.status') }}
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ t('admin.table.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-if="!editions.data || editions.data.length === 0">
                                <td colspan="7" class="text-center py-12 text-gray-500 dark:text-gray-400">
                                    {{ t('admin.editions.no_editions', 'No editions found. Click "Add Edition" to create your first hackathon edition.') }}
                                </td>
                            </tr>
                            <tr v-else v-for="edition in editions.data" :key="edition.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap" :class="{ 'text-right': isRTL, 'text-left': !isRTL }">
                                    <div class="text-sm font-medium" :style="{ color: themeColor.primary }">
                                        {{ edition.name }}
                                    </div>
                                    <div v-if="edition.location" class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ edition.location }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap" :class="{ 'text-right': isRTL, 'text-left': !isRTL }">
                                    <span class="text-sm text-gray-900 dark:text-gray-300">{{ edition.year }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap" :class="{ 'text-right': isRTL, 'text-left': !isRTL }">
                                    <div class="text-sm" :style="{ color: themeColor.primary }">
                                        {{ formatDate(edition.registration_start_date) }} - {{ formatDate(edition.registration_end_date) }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ t('admin.editions.event') }}: {{ formatDate(edition.hackathon_start_date) }} - {{ formatDate(edition.hackathon_end_date) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap" :class="{ 'text-right': isRTL, 'text-left': !isRTL }">
                                    <div class="flex items-center" :class="{ 'justify-end': isRTL, 'justify-start': !isRTL }">
                                        <span class="text-sm font-medium" :style="{ color: themeColor.primary }">
                                            {{ edition.teams_count || 0 }}
                                        </span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">
                                            / {{ edition.max_teams }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap" :class="{ 'text-right': isRTL, 'text-left': !isRTL }">
                                    <span class="text-sm" :style="{ color: themeColor.primary }">
                                        {{ edition.admin?.name || t('admin.common.not_assigned') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span v-if="edition.is_active"
                                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                        {{ t('admin.status.active') }}
                                    </span>
                                    <span v-else
                                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        {{ t('admin.status.inactive') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <Link :href="route('system-admin.editions.edit', edition.id)"
                                              class="text-sm font-bold transition-colors"
                                              :style="{ color: themeColor.primary }"
                                              @mouseover="e => e.target.style.color = themeColor.hover"
                                              @mouseleave="e => e.target.style.color = themeColor.primary">
                                            {{ t('admin.actions.edit') }}
                                        </Link>
                                        <span class="font-bold" :style="{ color: themeColor.primary }">|</span>
                                        <button @click="deleteEdition(edition)"
                                                class="text-sm font-bold transition-colors"
                                                :style="{ color: themeColor.primary }"
                                                @mouseover="e => e.target.style.color = themeColor.hover"
                                                @mouseleave="e => e.target.style.color = themeColor.primary">
                                            {{ t('admin.actions.delete') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="editions.links && editions.data.length > 0" 
                     class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="editions.prev_page_url" :href="editions.prev_page_url"
                                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                {{ t('messages.previous') }}
                            </Link>
                            <Link v-if="editions.next_page_url" :href="editions.next_page_url"
                                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                {{ t('messages.next') }}
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ t('admin.table.showing_results', { from: editions.from || 0, to: editions.to || 0, total: editions.total || 0 }) }}
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <template v-for="link in editions.links" :key="link.label">
                                        <Link v-if="link.url"
                                              :href="link.url"
                                              :class="[
                                                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors',
                                                  link.active
                                                      ? 'z-10 border-[var(--theme-primary)] text-white'
                                                      : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'
                                              ]"
                                              :style="link.active ? { backgroundColor: themeColor.primary } : {}"
                                              v-html="link.label">
                                        </Link>
                                        <span v-else
                                              :class="[
                                                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium cursor-default',
                                                  'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-300 dark:text-gray-500'
                                              ]"
                                              v-html="link.label">
                                        </span>
                                    </template>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL, direction, locale } = useLocalization()
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import Default from '../../../Layouts/Default.vue'

const props = defineProps({
    editions: {
        type: Object,
        required: true
    }
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

const formatDate = (date) => {
    if (!date) return t('admin.common.not_available')
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric'
    })
}

const deleteEdition = (edition) => {
    if (confirm(t('admin.editions.confirm_delete', { name: `${edition.name} ${edition.year}` }))) {
        useForm({}).delete(route('system-admin.editions.destroy', edition.id))
    }
}
</script>

<style scoped>
/* Theme-aware custom styles */
:deep(.peer:focus) {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 2px rgba(var(--theme-rgb), 0.2) !important;
}
</style>