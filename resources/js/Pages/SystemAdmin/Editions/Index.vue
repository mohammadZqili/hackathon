<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { PlusIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    editions: Object,
})

const setCurrent = (editionId) => {
    if (confirm('Are you sure you want to set this edition as current?')) {
        useForm({}).post(route('system-admin.editions.set-current', editionId))
    }
}

const archiveEdition = (editionId) => {
    if (confirm('Are you sure you want to archive this edition?')) {
        useForm({}).post(route('system-admin.editions.archive', editionId))
    }
}
</script>

<template>
    <Head title="Hackathon Editions" />
    <Default>
        <div class="max-w-7xl mx-auto">
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Hackathon Editions</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Manage different editions and years of hackathon events.
                    </p>
                </div>
                <a :href="route('system-admin.editions.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <PlusIcon class="w-5 h-5 mr-2" />Create Edition
                </a>
            </div>

            <!-- Editions Grid -->
            <div v-if="editions.data.length === 0" class="text-center py-12">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No editions found</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Get started by creating your first hackathon edition.</p>
                <a :href="route('system-admin.editions.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <PlusIcon class="w-5 h-5 mr-2" />Create First Edition
                </a>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="edition in editions.data" :key="edition.id" 
                     class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                    
                    <!-- Edition Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                {{ edition.name }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Year {{ edition.year }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span v-if="edition.is_current" 
                                  class="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 text-xs font-medium rounded-full">
                                Current
                            </span>
                            <span :class="[
                                'px-2 py-1 text-xs font-medium rounded-full',
                                edition.status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' :
                                edition.status === 'draft' ? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100' :
                                edition.status === 'completed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' :
                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100'
                            ]">
                                {{ edition.status.charAt(0).toUpperCase() + edition.status.slice(1) }}
                            </span>
                        </div>
                    </div>

                    <!-- Edition Details -->
                    <div class="space-y-3 mb-6">
                        <div v-if="edition.theme" class="flex items-center text-sm">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z" />
                            </svg>
                            <span class="text-gray-600 dark:text-gray-400">{{ edition.theme }}</span>
                        </div>
                        
                        <div v-if="edition.location" class="flex items-center text-sm">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-gray-600 dark:text-gray-400">{{ edition.location }}</span>
                        </div>

                        <div class="flex items-center text-sm">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span class="text-gray-600 dark:text-gray-400">
                                {{ new Date(edition.event_start_date).toLocaleDateString() }} - 
                                {{ new Date(edition.event_end_date).toLocaleDateString() }}
                            </span>
                        </div>
                    </div>

                    <!-- Edition Description -->
                    <div v-if="edition.description" class="mb-6">
                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3">
                            {{ edition.description }}
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex space-x-2">
                            <a :href="route('system-admin.editions.show', edition.id)" 
                               class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                View
                            </a>
                            <a :href="route('system-admin.editions.edit', edition.id)" 
                               class="text-sm text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 font-medium">
                                Edit
                            </a>
                        </div>
                        <div class="flex space-x-2">
                            <button v-if="!edition.is_current && edition.status === 'active'" 
                                    @click="setCurrent(edition.id)"
                                    class="text-sm text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 font-medium">
                                Set Current
                            </button>
                            <button v-if="edition.status !== 'archived'" 
                                    @click="archiveEdition(edition.id)"
                                    class="text-sm text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300 font-medium">
                                Archive
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="editions.links && editions.data.length > 0" class="mt-8">
                <nav class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <a v-if="editions.prev_page_url" :href="editions.prev_page_url"
                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Previous
                        </a>
                        <a v-if="editions.next_page_url" :href="editions.next_page_url"
                           class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Next
                        </a>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Showing {{ editions.from }} to {{ editions.to }} of {{ editions.total }} results
                            </p>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </Default>
</template>