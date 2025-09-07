<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { useForm } from '@inertiajs/vue3'


const props = defineProps({
    
})


const form = useForm({
    name: '',
    year: new Date().getFullYear(),
    start_date: '',
    end_date: '',
    description: '',
    is_current: false
})
</script>

<template>
    <Head title="Create Edition" />
    
    <Default>
        <div class="container mx-auto px-4 py-8">
            
            <div class="max-w-2xl mx-auto">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Create Hackathon Edition</h1>

                <form @submit.prevent="form.post(route('system-admin.editions.store'))"
                      class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                            <input v-model="form.name" type="text" required
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Year</label>
                            <input v-model="form.year" type="number" required min="2024" max="2030"
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                                <input v-model="form.start_date" type="date" required
                                       class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                                <input v-model="form.end_date" type="date" required
                                       class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                            <textarea v-model="form.description" rows="4"
                                      class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input v-model="form.is_current" type="checkbox" class="mr-2" />
                                <span class="text-sm text-gray-700 dark:text-gray-300">Set as current edition</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a :href="route('system-admin.editions.index')"
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                            Create Edition
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Default>
</template>
