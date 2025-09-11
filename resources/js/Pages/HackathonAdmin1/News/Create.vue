<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { useForm } from '@inertiajs/vue3'


const props = defineProps({
    categories: Object
})


const form = useForm({
    title: '',
    category: '',
    content: '',
    image: null,
    is_published: false,
    post_to_twitter: false
})

const handleImageUpload = (event) => {
    form.image = event.target.files[0]
}
</script>

<template>
    <Head title="Create News" />
    
    <Default>
        <div class="container mx-auto px-4 py-8">
            
            <div class="max-w-4xl mx-auto">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Create News Article</h1>

                <form @submit.prevent="form.post(route('hackathon-admin.news.store'))"
                      class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                            <input v-model="form.title" type="text" required
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                            <select v-model="form.category" required
                                    class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="">Select category</option>
                                <option value="announcement">Announcement</option>
                                <option value="workshop">Workshop</option>
                                <option value="deadline">Deadline</option>
                                <option value="general">General</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Content</label>
                            <textarea v-model="form.content" rows="10" required
                                      class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Featured Image</label>
                            <input type="file" @change="handleImageUpload"
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" />
                        </div>

                        <div class="flex items-center space-x-6">
                            <label class="flex items-center">
                                <input v-model="form.is_published" type="checkbox" class="mr-2" />
                                <span class="text-sm text-gray-700 dark:text-gray-300">Publish immediately</span>
                            </label>
                            <label class="flex items-center">
                                <input v-model="form.post_to_twitter" type="checkbox" class="mr-2" />
                                <span class="text-sm text-gray-700 dark:text-gray-300">Post to Twitter</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a :href="route('hackathon-admin.news.index')"
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                            Create Article
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Default>
</template>
