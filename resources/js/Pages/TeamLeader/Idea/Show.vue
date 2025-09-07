<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { LightBulbIcon, PencilIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    idea: Object,
})
</script>

<template>
    <Head title="Our Idea" />
    
    <Default>
        <div class="max-w-7xl mx-auto">
            <div class="mb-8 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Our Idea</h1>
                <a v-if="idea && idea.status === 'draft'" 
                   :href="route('team-leader.idea.edit')" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg">
                    <PencilIcon class="w-5 h-5 mr-2" />
                    Edit Idea
                </a>
            </div>

            <div v-if="idea" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">{{ idea.title }}</h2>
                <div class="mb-4">
                    <span class="px-3 py-1 text-sm font-medium rounded-full"
                          :class="idea.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                        {{ idea.status }}
                    </span>
                </div>
                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ idea.description }}</p>
                
                <div v-if="idea.feedback" class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <h3 class="font-semibold mb-2">Reviewer Feedback</h3>
                    <p class="text-sm">{{ idea.feedback }}</p>
                </div>
            </div>

            <div v-else class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                <LightBulbIcon class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No Idea Submitted Yet</h2>
                <p class="text-gray-500 mb-6">Start by creating your team's innovative idea</p>
                <a :href="route('team-leader.idea.create')" 
                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg">
                    Create Idea
                </a>
            </div>
        </div>
    </Default>
</template>