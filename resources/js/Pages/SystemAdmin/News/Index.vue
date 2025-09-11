<template>
    <Head :title="t('admin.news.title')" />
    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ t('admin.news.title') }}</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Create, manage, and publish news articles for all hackathon editions
                    </p>
                </div>

                <Link :href="route('system-admin.news.create')"
                      class="inline-flex items-center px-4 py-2 rounded-lg font-semibold text-white transition-all duration-200 shadow-sm hover:shadow-md mt-4 sm:mt-0"
                      :style="{
                          background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                      }">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New Article
                </Link>
            </div>

            <!-- Tabs Navigation -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <div class="flex gap-8 px-6">
                        <Link :href="route('system-admin.news.create')"
                              class="py-4 px-1 border-b-2 transition-colors"
                              :class="false ? 'border-[var(--theme-primary)] text-[var(--theme-primary)]' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'">
                            <span class="text-sm font-medium">Add News</span>
                        </Link>
                        <button class="py-4 px-1 border-b-2 transition-colors"
                                :class="true ? 'border-[var(--theme-primary)] text-[var(--theme-primary)]' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'">
                            <span class="text-sm font-medium">All News</span>
                        </button>
                        <Link :href="route('system-admin.news.media-center')"
                              class="py-4 px-1 border-b-2 transition-colors border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                            <span class="text-sm font-medium">Media Center</span>
                        </Link>
                    </div>
                </div>
            </div>
            <!-- News Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <!-- Table Header -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Title
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Twitter
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Author
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-if="!news.data || news.data.length === 0">
                                <td colspan="5" class="text-center py-12 text-gray-500 dark:text-gray-400">
                                    No news articles found.
                                </td>
                            </tr>
                            <tr v-else v-for="article in news.data" :key="article.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ article.title || 'Untitled' }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        {{ article.content ? article.content.substring(0, 60) + '...' : '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 dark:text-gray-300">{{ formatDate(article.created_at) }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox"
                                               :checked="article.publish_to_twitter"
                                               @change="toggleTwitterPublish(article)"
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-opacity-50 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"
                                             :class="article.publish_to_twitter ? 'peer-checked:bg-[var(--theme-primary)] peer-focus:ring-[var(--theme-primary)]' : ''"></div>
                                    </label>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 dark:text-gray-300">{{ article.author?.name || 'System Admin' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <Link :href="route('system-admin.news.edit', article.id)"
                                              class="text-sm font-medium transition-colors"
                                              :style="{ color: themeColor.primary }"
                                              @mouseover="e => e.target.style.color = themeColor.hover"
                                              @mouseleave="e => e.target.style.color = themeColor.primary">
                                            Edit
                                        </Link>
                                        <button @click="deleteArticle(article)"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium transition-colors">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="news.links" class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="news.prev_page_url" :href="news.prev_page_url"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                Previous
                            </Link>
                            <Link v-if="news.next_page_url" :href="news.next_page_url"
                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                Next
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    Showing <span class="font-medium">{{ news.from || 0 }}</span> to <span class="font-medium">{{ news.to || 0 }}</span> of <span class="font-medium">{{ news.total || 0 }}</span> results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <template v-for="link in news.links" :key="link.label">
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
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import Default from '../../../Layouts/Default.vue'

const props = defineProps({
    news: {
        type: Object,
        required: true
    }
})

const activeTab = ref('all')

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
    if (!date) return 'N/A'
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    })
}

const toggleTwitterPublish = (article) => {
    useForm({
        publish_to_twitter: !article.publish_to_twitter
    }).patch(route('system-admin.news.update', article.id), {
        preserveScroll: true
    })
}

const deleteArticle = (article) => {
    if (confirm(`Are you sure you want to delete the article "${article.title}"?`)) {
        useForm({}).delete(route('system-admin.news.destroy', article.id))
    }
}
</script>

<style scoped>
/* Theme-aware input styling */
:deep(.peer:focus) {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 2px rgba(var(--theme-rgb), 0.2) !important;
}
</style>
