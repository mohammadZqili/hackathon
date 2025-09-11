<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'



const props = defineProps({
    news: Object, filters: Object
})


const deleteNews = (id) => {
    if (confirm('Are you sure you want to delete this news item?')) {
        // Delete logic
    }
}
</script>

<template>
    <Head title="News Management" />
    
    <Default>
        <div class="container mx-auto px-4 py-8">
            
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">News & Announcements</h1>
                <a :href="route('hackathon-admin.news.create')"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Create News
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <input type="text" placeholder="Search news..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg" />
                </div>

                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    <div v-for="item in news.data" :key="item.id" class="p-6">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ item.title }}</h3>
                                <p class="mt-1 text-gray-600 dark:text-gray-400">{{ item.excerpt }}</p>
                                <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                    <span>{{ item.author }}</span>
                                    <span>{{ item.published_at }}</span>
                                    <span class="px-2 py-1 rounded-full text-xs"
                                          :class="item.is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                                        {{ item.is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex space-x-2 ml-4">
                                <a :href="route('hackathon-admin.news.edit', item.id)"
                                   class="text-blue-600 hover:text-blue-900">Edit</a>
                                <button @click="deleteNews(item.id)"
                                        class="text-red-600 hover:text-red-900">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>
