<template>
    <Head title="Media Center - News" />
    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">News</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Manage all media files from news articles
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
                    Add New
                </Link>
            </div>

            <!-- Tabs Navigation -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <div class="flex gap-8 px-6">
                        <Link :href="route('system-admin.news.create')"
                              class="py-4 px-1 border-b-2 transition-colors border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                            <span class="text-sm font-medium">Add News</span>
                        </Link>
                        <Link :href="route('system-admin.news.index')"
                              class="py-4 px-1 border-b-2 transition-colors border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                            <span class="text-sm font-medium">All News</span>
                        </Link>
                        <button class="py-4 px-1 border-b-2 transition-colors border-[var(--theme-primary)] text-[var(--theme-primary)]">
                            <span class="text-sm font-medium">Media Center</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Media Center Content -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <!-- Media Center Header -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Media Center</h2>

                    <!-- Filter Dropdowns -->
                    <div class="flex flex-wrap gap-3">
                        <!-- News Article Filter -->
                        <div class="relative">
                            <button @click="showArticleFilter = !showArticleFilter"
                                    class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                <span>{{ selectedArticle || 'News Article' }}</span>
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div v-if="showArticleFilter"
                                 class="absolute z-10 mt-2 w-64 rounded-lg bg-white dark:bg-gray-700 shadow-lg">
                                <div class="py-2">
                                    <button @click="selectArticle(null)"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                        All Articles
                                    </button>
                                    <button v-for="article in articles" :key="article.id"
                                            @click="selectArticle(article)"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                        {{ article.title }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Date Filter -->
                        <div class="relative">
                            <button @click="showDateFilter = !showDateFilter"
                                    class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                <span>{{ selectedDate || 'Upload Date' }}</span>
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div v-if="showDateFilter"
                                 class="absolute z-10 mt-2 w-48 rounded-lg bg-white dark:bg-gray-700 shadow-lg">
                                <div class="py-2">
                                    <button @click="selectDate('all')"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                        All Time
                                    </button>
                                    <button @click="selectDate('today')"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                        Today
                                    </button>
                                    <button @click="selectDate('week')"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                        Last 7 Days
                                    </button>
                                    <button @click="selectDate('month')"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                        Last 30 Days
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Media Type Filter -->
                        <div class="relative">
                            <button @click="showTypeFilter = !showTypeFilter"
                                    class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                <span>{{ selectedType || 'Media Type' }}</span>
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div v-if="showTypeFilter"
                                 class="absolute z-10 mt-2 w-48 rounded-lg bg-white dark:bg-gray-700 shadow-lg">
                                <div class="py-2">
                                    <button @click="selectType('all')"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                        All Types
                                    </button>
                                    <button @click="selectType('image')"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                        Images
                                    </button>
                                    <button @click="selectType('video')"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                        Videos
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Media Gallery -->
                <div v-if="loading" class="flex justify-center items-center h-64">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2"
                         :style="{ borderColor: themeColor.primary }"></div>
                </div>

                <div v-else-if="filteredMedia.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p class="mt-4 text-gray-500 dark:text-gray-400">No media files found</p>
                </div>

                <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <div v-for="media in filteredMedia" :key="media.id"
                         class="group relative cursor-pointer"
                         @click="openMediaModal(media)">
                        <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700">
                            <img v-if="media.type === 'image'"
                                 :src="media.url"
                                 :alt="media.alt"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                            <div v-else
                                 class="w-full h-full flex items-center justify-center bg-gray-200 dark:bg-gray-600">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="absolute inset-0 bg-opacity-0 group-hover:bg-opacity-50 rounded-lg transition-all duration-200 flex items-center justify-center">
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <button class="p-2 bg-white rounded-full mx-1">
                                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                <button @click.stop="deleteMedia(media)" class="p-2 bg-white rounded-full mx-1">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Media Modal -->
                <Teleport to="body">
                    <div v-if="selectedMedia"
                         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-75"
                         @click="closeMediaModal">
                        <div class="relative max-w-4xl max-h-[90vh] bg-white dark:bg-gray-800 rounded-lg overflow-hidden"
                             @click.stop>
                            <button @click="closeMediaModal"
                                    class="absolute top-4 right-4 p-2 bg-white dark:bg-gray-700 rounded-full z-10">
                                <svg class="w-6 h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <img v-if="selectedMedia.type === 'image'"
                                 :src="selectedMedia.url"
                                 :alt="selectedMedia.alt"
                                 class="w-full h-full object-contain">
                            <video v-else
                                   :src="selectedMedia.url"
                                   controls
                                   class="w-full h-full">
                            </video>
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-6">
                                <h3 class="text-white font-semibold text-lg">{{ selectedMedia.title }}</h3>
                                <p class="text-gray-300 text-sm mt-1">{{ selectedMedia.article }}</p>
                                <p class="text-gray-400 text-xs mt-2">Uploaded {{ formatDate(selectedMedia.uploadedAt) }}</p>
                            </div>
                        </div>
                    </div>
                </Teleport>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import Default from '../../../Layouts/Default.vue'

const props = defineProps({
    media: {
        type: Array,
        default: () => []
    },
    articles: {
        type: Array,
        default: () => []
    }
})

// Theme setup
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

    // Load media files
    loadMediaFiles()
})

const themeStyles = computed(() => ({
    '--theme-primary': themeColor.value.primary,
    '--theme-hover': themeColor.value.hover,
    '--theme-rgb': themeColor.value.rgb,
    '--theme-gradient-from': themeColor.value.gradientFrom,
    '--theme-gradient-to': themeColor.value.gradientTo,
}))

// Filter states
const showArticleFilter = ref(false)
const showDateFilter = ref(false)
const showTypeFilter = ref(false)

const selectedArticle = ref(null)
const selectedDate = ref(null)
const selectedType = ref(null)

const loading = ref(false)
const mediaFiles = ref([])
const selectedMedia = ref(null)

// Load media files from props
const loadMediaFiles = async () => {
    loading.value = true
    try {
        // Use the real media data passed from the backend
        mediaFiles.value = props.media || []
    } catch (error) {
        console.error('Failed to load media:', error)
    } finally {
        loading.value = false
    }
}

// Filtered media based on selections
const filteredMedia = computed(() => {
    let filtered = [...mediaFiles.value]

    // Filter by article
    if (selectedArticle.value) {
        filtered = filtered.filter(m => m.articleId === selectedArticle.value.id)
    }

    // Filter by date
    if (selectedDate.value && selectedDate.value !== 'all') {
        const now = new Date()
        const filterDate = new Date()

        switch (selectedDate.value) {
            case 'today':
                filterDate.setDate(now.getDate() - 1)
                break
            case 'week':
                filterDate.setDate(now.getDate() - 7)
                break
            case 'month':
                filterDate.setDate(now.getDate() - 30)
                break
        }

        filtered = filtered.filter(m => new Date(m.uploadedAt) >= filterDate)
    }

    // Filter by type
    if (selectedType.value && selectedType.value !== 'all') {
        filtered = filtered.filter(m => m.type === selectedType.value)
    }

    return filtered
})

// Filter functions
const selectArticle = (article) => {
    selectedArticle.value = article
    showArticleFilter.value = false
}

const selectDate = (date) => {
    selectedDate.value = date
    showDateFilter.value = false
}

const selectType = (type) => {
    selectedType.value = type
    showTypeFilter.value = false
}

// Media modal
const openMediaModal = (media) => {
    selectedMedia.value = media
}

const closeMediaModal = () => {
    selectedMedia.value = null
}

// Delete media
const deleteMedia = async (media) => {
    if (confirm(`Are you sure you want to delete this ${media.type}?`)) {
        try {
            // Make API call to delete the media
            const response = await fetch(route('system-admin.news.media.delete', media.id), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                }
            })
            
            if (response.ok) {
                // Remove from local state
                mediaFiles.value = mediaFiles.value.filter(m => m.id !== media.id)
            } else {
                console.error('Failed to delete media')
                alert('Failed to delete media file. Please try again.')
            }
        } catch (error) {
            console.error('Error deleting media:', error)
            alert('An error occurred while deleting the media file.')
        }
    }
}

// Format date
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}
</script>

<style scoped>
/* Custom scrollbar for media gallery */
:deep(.media-gallery::-webkit-scrollbar) {
    width: 8px;
    height: 8px;
}

:deep(.media-gallery::-webkit-scrollbar-track) {
    background: #f1f1f1;
    border-radius: 4px;
}

:deep(.media-gallery::-webkit-scrollbar-thumb) {
    background: #888;
    border-radius: 4px;
}

:deep(.media-gallery::-webkit-scrollbar-thumb:hover) {
    background: #555;
}

/* Dark mode scrollbar */
:deep(.dark .media-gallery::-webkit-scrollbar-track) {
    background: #374151;
}

:deep(.dark .media-gallery::-webkit-scrollbar-thumb) {
    background: #6b7280;
}

:deep(.dark .media-gallery::-webkit-scrollbar-thumb:hover) {
    background: #9ca3af;
}
</style>
