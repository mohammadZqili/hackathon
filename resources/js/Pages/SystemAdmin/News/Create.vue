<script setup>
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL, direction, locale } = useLocalization()
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import Default from '../../../Layouts/Default.vue'
import RichTextEditor from '@/Components/Forms/RichTextEditor.vue'
import FilePondUploader from '@/Components/FilePondUploader.vue'

const props = defineProps({
    categories: {
        type: Array,
        default: () => []
    }
})

const page = usePage()
const csrfToken = page.props.csrf_token

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

const form = useForm({
    title: '',
    content: '',
    category_id: '',
    video_url: '',
    twitter_message: '',
    publish_to_twitter: false,
    keywords: '',
    main_image: null,
    gallery_images: []
})

const showAddCategory = ref(false)

// FilePond upload configuration
const uploadConfig = {
    process: {
        url: route('system-admin.news.upload-temp'),
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        withCredentials: true,
        onload: (response) => {
            return typeof response === 'string' ? JSON.parse(response) : response;
        }
    },
    revert: {
        url: route('system-admin.news.delete-temp'),
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        withCredentials: true
    }
}

const handleMainImageProcess = (error, file) => {
    if (error || !file) return
    
    const response = file.serverId ?
        (typeof file.serverId === 'string' ? JSON.parse(file.serverId) : file.serverId) :
        (typeof file === 'string' ? JSON.parse(file) : file)
    
    if (response?.path) {
        form.main_image = response.path
    }
}

const handleMainImageRemove = () => {
    form.main_image = null
}

const galleryFiles = ref([])

const handleGalleryProcess = (error, file) => {
    if (error || !file) return
    
    const response = file.serverId ?
        (typeof file.serverId === 'string' ? JSON.parse(file.serverId) : file.serverId) :
        (typeof file === 'string' ? JSON.parse(file) : file)
    
    if (response?.path) {
        form.gallery_images.push(response.path)
    }
}

const handleGalleryRemove = (error, file) => {
    if (!error && file.serverId) {
        const response = typeof file.serverId === 'string' ? JSON.parse(file.serverId) : file.serverId
        if (response?.path) {
            const index = form.gallery_images.indexOf(response.path)
            if (index > -1) {
                form.gallery_images.splice(index, 1)
            }
        }
    }
}

const submit = () => {
    form.post(route('system-admin.news.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Handle success
        }
    })
}
</script>

<template>
    <Head :title="t('admin.news.create')" />
    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ t('admin.news.create') }}</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        {{ t('admin.news.description') }}
                    </p>
                </div>
                
                <Link :href="route('system-admin.news.index')"
                      class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors mt-4 sm:mt-0">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ t('admin.actions.back') }}
                </Link>
            </div>

            <!-- Tabs Navigation -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <div class="flex gap-8 px-6">
                        <button class="py-4 px-1 border-b-2 transition-colors border-[var(--theme-primary)] text-[var(--theme-primary)]">
                            <span class="text-sm font-medium">{{ t('admin.news.add_news') }}</span>
                        </button>
                        <Link :href="route('system-admin.news.index')"
                              class="py-4 px-1 border-b-2 transition-colors border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                            <span class="text-sm font-medium">{{ t('admin.news.all_news') }}</span>
                        </Link>
                        <button class="py-4 px-1 border-b-2 transition-colors border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                            <span class="text-sm font-medium">{{ t('admin.news.media_center') }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Add News Form -->
            <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="space-y-6">
                    <!-- News Title and Categories Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- News Title -->
                        <div class="lg:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ t('admin.news.title_column') }}</label>
                            <input v-model="form.title"
                                   type="text"
                                   id="title"
                                   :placeholder="t('admin.form.placeholder.enter_news_title')"
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[var(--theme-primary)] focus:border-transparent">
                            <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</div>
                        </div>

                        <!-- Categories -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.form.categories') }}
                                <button type="button" @click="showAddCategory = true"
                                        class="ml-2 text-xs transition-colors"
                                        :style="{ color: themeColor.primary }"
                                        @mouseover="e => e.target.style.color = themeColor.hover"
                                        @mouseleave="e => e.target.style.color = themeColor.primary">
                                    + {{ t('admin.form.add_new_category') }}
                                </button>
                            </label>
                            <select v-model="form.category_id"
                                    id="category"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[var(--theme-primary)]">
                                <option value="">{{ t('admin.form.placeholder.select_option') }}</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- News Body -->
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ t('admin.news.content') }}</label>
                        <RichTextEditor 
                            v-model="form.content"
                            :placeholder="t('admin.form.placeholder.news_content')"
                            min-height="400px"
                        />
                        <div v-if="form.errors.content" class="text-red-500 text-sm mt-1">{{ form.errors.content }}</div>
                    </div>

                    <!-- Main Image and Video Embedding -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Main Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ t('admin.form.main_image') }}</label>
                            <div class="max-w-xs">
                                <FilePondUploader 
                                    name="main_image" 
                                    label="Main Image" 
                                    :label-idle="t('admin.form.placeholder.drop_main_image')"
                                    id="main_image" 
                                    :accepted-file-types="['image/jpeg', 'image/png', 'image/webp']"
                                    :server="uploadConfig"
                                    :max-files="1"
                                    @processfile="handleMainImageProcess"
                                    @removefile="handleMainImageRemove"
                                />
                            </div>
                        </div>

                        <!-- Video Embedding -->
                        <div>
                            <label for="video_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ t('admin.form.video_embedding') }}</label>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-600 dark:text-gray-400">{{ t('admin.form.video_url') }}</label>
                                <input v-model="form.video_url"
                                       type="url"
                                       id="video_url"
                                       :placeholder="t('admin.form.placeholder.enter_video_url')"
                                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[var(--theme-primary)]">
                            </div>
                        </div>
                    </div>

                    <!-- Twitter Message and Publishing -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Twitter Message -->
                        <div>
                            <label for="twitter_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ t('admin.form.twitter_message') }}</label>
                            <textarea v-model="form.twitter_message"
                                      id="twitter_message"
                                      rows="4"
                                      maxlength="280"
                                      class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[var(--theme-primary)] resize-none"
                                      :placeholder="t('admin.form.placeholder.twitter_message')"></textarea>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ form.twitter_message?.length || 0 }}/280 characters</p>
                        </div>

                        <!-- Twitter Publishing -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ t('admin.form.twitter_publishing') }}</label>
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ t('admin.form.publish_to_twitter') }}</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input v-model="form.publish_to_twitter"
                                               type="checkbox"
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-opacity-50 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"
                                             :class="form.publish_to_twitter ? 'peer-checked:bg-[var(--theme-primary)] peer-focus:ring-[var(--theme-primary)]' : ''"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Image Gallery -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('admin.form.image_gallery') }}</label>
                        <div class="max-w-xl">
                            <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-3 bg-gray-50 dark:bg-gray-800">
                                <FilePondUploader 
                                    name="gallery_images" 
                                    label="" 
                                    :label-idle="t('admin.form.placeholder.drop_images')"
                                    id="gallery_images" 
                                    :accepted-file-types="['image/jpeg', 'image/png', 'image/webp']"
                                    :server="uploadConfig"
                                    :allow-multiple="true"
                                    :max-files="10"
                                    @processfile="handleGalleryProcess"
                                    @removefile="handleGalleryRemove"
                                />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ t('admin.form.upload_images_help') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Keywords -->
                    <div>
                        <label for="keywords" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ t('admin.form.seo_keywords') }}</label>
                        <input v-model="form.keywords"
                               type="text"
                               id="keywords"
                               :placeholder="t('admin.form.placeholder.seo_keywords')"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[var(--theme-primary)]">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ t('admin.form.keywords_help') }}</p>
                    </div>

                    <!-- Publish Button -->
                    <div class="flex justify-center pt-4">
                        <button type="submit"
                                :disabled="form.processing"
                                class="px-8 py-3 rounded-lg font-semibold text-white transition-all duration-200 shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                                :style="{ 
                                    background: form.processing ? '#9ca3af' : `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                                }">
                            {{ form.processing ? t('admin.actions.publishing') : t('admin.news.publish_article') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </Default>
</template>

<style scoped>
/* Theme-aware input styling */
:deep(.peer:focus) {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 2px rgba(var(--theme-rgb), 0.2) !important;
}

:deep(.error) {
    border-color: #ef4444 !important;
}
</style>