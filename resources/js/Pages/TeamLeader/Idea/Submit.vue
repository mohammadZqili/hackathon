<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { CheckCircleIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    idea: Object,
})

const form = useForm({})

const submitIdea = () => {
    form.post(route('team-leader.idea.submit'), {
        onSuccess: () => {
            // Handle success
        }
    })
}
</script>

<template>
    <Head title="Submit Idea" />
    
    <Default>
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Submit Idea for Review</h1>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">{{ idea?.title }}</h2>
                <p class="text-gray-700 dark:text-gray-300">{{ idea?.description }}</p>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-6 mb-6">
                <div class="flex">
                    <ExclamationTriangleIcon class="w-6 h-6 text-yellow-400 mr-3" />
                    <div>
                        <h3 class="font-semibold text-yellow-800 dark:text-yellow-400">Important Notice</h3>
                        <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                            Once submitted, you cannot edit your idea until it's reviewed. Make sure all information is correct.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a :href="route('team-leader.idea.edit')" 
                   class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg">Go Back & Edit</a>
                <button @click="submitIdea" 
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg inline-flex items-center">
                    <CheckCircleIcon class="w-5 h-5 mr-2" />
                    Submit for Review
                </button>
            </div>
        </div>
    </Default>
</template>