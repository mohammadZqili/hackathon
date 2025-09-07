<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { ArrowLeftIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    speakers: Array,
})

const form = useForm({
    title: '',
    description: '',
    speaker_id: '',
    location: '',
    start_time: '',
    end_time: '',
    max_participants: 30,
    requirements: '',
    materials_link: '',
    is_online: false,
    meeting_link: '',
})

const submitForm = () => {
    form.post(route('hackathon-admin.workshops.store'))
}
</script>

<template>
    <Head title="Create Workshop" />
    
    <Default>
        <div class="max-w-4xl mx-auto">
            <div class="mb-8 flex items-center space-x-4">
                <a :href="route('hackathon-admin.workshops.index')" class="text-gray-500 hover:text-gray-700">
                    <ArrowLeftIcon class="w-5 h-5" />
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create Workshop</h1>
            </div>

            <form @submit.prevent="submitForm" class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">Basic Information</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Title
                            </label>
                            <input v-model="form.title" type="text" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Description
                            </label>
                            <textarea v-model="form.description" rows="4" required
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Start Time
                                </label>
                                <input v-model="form.start_time" type="datetime-local" required
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    End Time
                                </label>
                                <input v-model="form.end_time" type="datetime-local" required
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Location
                            </label>
                            <input v-model="form.location" type="text" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Max Participants
                            </label>
                            <input v-model="form.max_participants" type="number" min="5" max="500" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a :href="route('hackathon-admin.workshops.index')" 
                       class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg">
                        Create Workshop
                    </button>
                </div>
            </form>
        </div>
    </Default>
</template>