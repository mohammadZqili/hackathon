<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'



const props = defineProps({
    news: Object
})


const deleteNews = () => {
    if (confirm('Are you sure you want to delete this article?')) {
        // Delete logic
    }
}
</script>

<template>
    <Head title="View News" />
    
    <Default>
        <div class="container mx-auto px-4 py-8">
            
            <div class="max-w-4xl mx-auto">
                <div class="mb-6 flex justify-between items-center">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ news.title }}</h1>
                    <div class="flex space-x-3">
                        <a :href="route('hackathon-admin.news.edit', news.id)"
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg">Edit</a>
                        <button @click="deleteNews"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg">Delete</button>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="mb-4 flex items-center space-x-4 text-sm text-gray-500">
                        <span>By {{ news.author }}</span>
                        <span>{{ news.published_at }}</span>
                        <span class="px-2 py-1 rounded-full text-xs"
                              :class="news.is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                            {{ news.is_published ? 'Published' : 'Draft' }}
                        </span>
                    </div>

                    <div v-if="news.image" class="mb-6">
                        <img :src="news.image" :alt="news.title"
                             class="w-full h-64 object-cover rounded-lg" />
                    </div>

                    <div class="prose max-w-none dark:prose-invert">
                        {{ news.content }}
                    </div>

                    <div v-if="news.twitter_post_id" class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <p class="text-sm">Posted to Twitter</p>
                        <a :href="`https://twitter.com/i/web/status/${news.twitter_post_id}`"
                           target="_blank" class="text-blue-600 hover:underline">View Tweet</a>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>
