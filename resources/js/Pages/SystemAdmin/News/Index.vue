<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">News Management (All Editions)</h1>
            <Link :href="route('system-admin.news.create')"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                Create News Article
            </Link>
        </div>
        
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">All News Articles Across All Hackathon Editions</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Create, manage, and publish news articles for all hackathon editions
                </p>
            </div>
            
            <div class="p-6">
                <div v-if="news.data.length === 0" class="text-center py-8">
                    <p class="text-gray-500">No news articles found.</p>
                </div>
                
                <div v-else class="space-y-4">
                    <div v-for="article in news.data" :key="article.id"
                         class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        {{ article.title || 'Untitled Article' }}
                                    </h3>
                                    <span v-if="article.status" 
                                          class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                          :class="{
                                              'bg-green-100 text-green-800': article.status === 'published',
                                              'bg-yellow-100 text-yellow-800': article.status === 'draft',
                                              'bg-blue-100 text-blue-800': article.status === 'scheduled'
                                          }">
                                        {{ article.status }}
                                    </span>
                                </div>
                                
                                <div class="text-sm text-gray-600 mb-3">
                                    {{ article.content ? article.content.substring(0, 200) + '...' : 'No content' }}
                                </div>
                                
                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <span v-if="article.hackathon_edition">
                                        Edition: {{ article.hackathon_edition.name }}
                                    </span>
                                    <span v-if="article.created_at">
                                        Created: {{ new Date(article.created_at).toLocaleDateString() }}
                                    </span>
                                    <span v-if="article.published_at">
                                        Published: {{ new Date(article.published_at).toLocaleDateString() }}
                                    </span>
                                    <span v-if="article.views_count" class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ article.views_count }} views
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2 ml-4">
                                <Link :href="route('system-admin.news.show', article.id)"
                                      class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    View
                                </Link>
                                <Link :href="route('system-admin.news.edit', article.id)"
                                      class="text-green-600 hover:text-green-900 text-sm font-medium">
                                    Edit
                                </Link>
                                <button v-if="article.status === 'draft'"
                                        @click="publishArticle(article)"
                                        class="text-purple-600 hover:text-purple-900 text-sm font-medium">
                                    Publish
                                </button>
                                <button v-else-if="article.status === 'published'"
                                        @click="unpublishArticle(article)"
                                        class="text-orange-600 hover:text-orange-900 text-sm font-medium">
                                    Unpublish
                                </button>
                                <button @click="deleteArticle(article)"
                                        class="text-red-600 hover:text-red-900 text-sm font-medium">
                                    Delete
                                </button>
                            </div>
                        </div>
                        
                        <!-- Tags or Categories if available -->
                        <div v-if="article.tags && article.tags.length > 0" class="mt-3">
                            <div class="flex flex-wrap gap-2">
                                <span v-for="tag in article.tags" :key="tag"
                                      class="inline-flex px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">
                                    {{ tag }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pagination -->
                <div v-if="news.links" class="mt-6">
                    <nav class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="news.prev_page_url" :href="news.prev_page_url"
                                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </Link>
                            <Link v-if="news.next_page_url" :href="news.next_page_url"
                                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing {{ news.from }} to {{ news.to }} of {{ news.total }} results
                                </p>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    news: {
        type: Object,
        required: true
    }
})

const publishArticle = (article) => {
    if (confirm(`Are you sure you want to publish "${article.title}"?`)) {
        useForm({}).post(route('system-admin.news.publish', article.id))
    }
}

const unpublishArticle = (article) => {
    if (confirm(`Are you sure you want to unpublish "${article.title}"?`)) {
        useForm({}).post(route('system-admin.news.unpublish', article.id))
    }
}

const deleteArticle = (article) => {
    if (confirm(`Are you sure you want to delete the article "${article.title}"?`)) {
        useForm({}).delete(route('system-admin.news.destroy', article.id))
    }
}
</script>
