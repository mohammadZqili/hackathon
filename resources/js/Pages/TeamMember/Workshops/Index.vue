<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { AcademicCapIcon, ClockIcon, MapPinIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    workshops: Object,
    myRegistrations: Array,
})
</script>

<template>
    <Head title="Available Workshops" />
    
    <Default>
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Available Workshops</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="workshop in workshops?.data || []" :key="workshop.id"
                     class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-start justify-between mb-4">
                        <AcademicCapIcon class="w-8 h-8 text-blue-600" />
                        <span v-if="myRegistrations?.includes(workshop.id)"
                              class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                            Registered
                        </span>
                    </div>
                    
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ workshop.title }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ workshop.description }}</p>
                    
                    <div class="space-y-2 text-sm text-gray-500 dark:text-gray-400 mb-4">
                        <div class="flex items-center">
                            <ClockIcon class="w-4 h-4 mr-2" />
                            {{ workshop.start_time }}
                        </div>
                        <div class="flex items-center">
                            <MapPinIcon class="w-4 h-4 mr-2" />
                            {{ workshop.location }}
                        </div>
                    </div>
                    
                    <a :href="route('team-member.workshops.show', workshop.id)"
                       class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                        View Details
                    </a>
                </div>
            </div>
        </div>
    </Default>
</template>