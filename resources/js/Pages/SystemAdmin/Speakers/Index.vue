<template>
    <Head title="Speakers Management" />
    <Default>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Speakers Management</h1>
            <Link :href="route('system-admin.speakers.create')"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                Add Speaker
            </Link>
        </div>
        
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Workshop Speakers</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Manage speakers who conduct workshops during hackathon events
                </p>
            </div>
            
            <div class="p-6">
                <div v-if="speakers.data.length === 0" class="text-center py-8">
                    <p class="text-gray-500">No speakers found.</p>
                </div>
                
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="speaker in speakers.data" :key="speaker.id"
                         class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-gray-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">
                                {{ speaker.name || 'Unnamed Speaker' }}
                            </h3>
                            <p v-if="speaker.title" class="text-sm text-gray-600 mb-2">
                                {{ speaker.title }}
                            </p>
                            <p v-if="speaker.bio" class="text-sm text-gray-500 mb-4">
                                {{ speaker.bio.substring(0, 100) }}{{ speaker.bio.length > 100 ? '...' : '' }}
                            </p>
                            <div class="flex justify-center space-x-2">
                                <Link :href="route('system-admin.speakers.show', speaker.id)"
                                      class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    View
                                </Link>
                                <Link :href="route('system-admin.speakers.edit', speaker.id)"
                                      class="text-green-600 hover:text-green-900 text-sm font-medium">
                                    Edit
                                </Link>
                                <button @click="deleteSpeaker(speaker)"
                                        class="text-red-600 hover:text-red-900 text-sm font-medium">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pagination -->
                <div v-if="speakers.links" class="mt-6">
                    <nav class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="speakers.prev_page_url" :href="speakers.prev_page_url"
                                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </Link>
                            <Link v-if="speakers.next_page_url" :href="speakers.next_page_url"
                                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing {{ speakers.from }} to {{ speakers.to }} of {{ speakers.total }} results
                                </p>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'

const props = defineProps({
    speakers: {
        type: Object,
        required: true
    }
})

const deleteSpeaker = (speaker) => {
    if (confirm(`Are you sure you want to delete the speaker "${speaker.name}"?`)) {
        useForm({}).delete(route('system-admin.speakers.destroy', speaker.id))
    }
}
</script>
