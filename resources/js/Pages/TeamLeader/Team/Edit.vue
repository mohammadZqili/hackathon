<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { ArrowLeftIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    team: Object,
    tracks: Array,
})

const form = useForm({
    name: props.team?.name || '',
    description: props.team?.description || '',
    track_id: props.team?.track_id || '',
})

const submitForm = () => {
    form.put(route('team-leader.team.update'))
}
</script>

<template>
    <Head title="Edit Team" />
    
    <Default>
        <div class="max-w-4xl mx-auto">
            <div class="mb-8 flex items-center space-x-4">
                <a :href="route('team-leader.team.show')" class="text-gray-500 hover:text-gray-700">
                    <ArrowLeftIcon class="w-5 h-5" />
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Team</h1>
            </div>

            <form @submit.prevent="submitForm" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Team Name
                        </label>
                        <input v-model="form.name" type="text" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Description
                        </label>
                        <textarea v-model="form.description" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Track
                        </label>
                        <select v-model="form.track_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">
                            <option value="">Select a track</option>
                            <option v-for="track in tracks" :key="track.id" :value="track.id">
                                {{ track.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a :href="route('team-leader.team.show')" 
                       class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </Default>
</template>