<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { ArrowLeftIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    workshop: Object,
    speakers: Array,
})

const form = useForm({
    title: props.workshop?.title || '',
    description: props.workshop?.description || '',
    speaker_id: props.workshop?.speaker_id || '',
    location: props.workshop?.location || '',
    start_time: props.workshop?.start_time || '',
    end_time: props.workshop?.end_time || '',
    max_participants: props.workshop?.max_participants || 30,
})

const submitForm = () => {
    form.put(route('hackathon-admin.workshops.update', props.workshop.id))
}
</script>

<template>
    <Head title="Edit Workshop" />
    
    <Default>
        <div class="max-w-4xl mx-auto">
            <div class="mb-8 flex items-center space-x-4">
                <a :href="route('hackathon-admin.workshops.show', workshop.id)" class="text-gray-500 hover:text-gray-700">
                    <ArrowLeftIcon class="w-5 h-5" />
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Workshop</h1>
            </div>

            <form @submit.prevent="submitForm" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                        <input v-model="form.title" type="text" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea v-model="form.description" rows="4" required
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                        <input v-model="form.location" type="text" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a :href="route('hackathon-admin.workshops.show', workshop.id)" 
                       class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg">Save Changes</button>
                </div>
            </form>
        </div>
    </Default>
</template>