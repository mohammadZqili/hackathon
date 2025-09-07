<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'



const props = defineProps({
    edition: Object, statistics: Object
})


</script>

<template>
    <Head title="Edition Details" />
    
    <Default>
        <div class="container mx-auto px-4 py-8">
            
            <div class="max-w-6xl mx-auto">
                <div class="mb-6 flex justify-between items-center">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ edition.name }}</h1>
                    <a :href="route('system-admin.editions.edit', edition.id)"
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg">Edit Edition</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="text-sm text-gray-500">Teams</div>
                        <div class="text-2xl font-bold">{{ statistics.teams }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="text-sm text-gray-500">Participants</div>
                        <div class="text-2xl font-bold">{{ statistics.participants }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="text-sm text-gray-500">Ideas</div>
                        <div class="text-2xl font-bold">{{ statistics.ideas }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="text-sm text-gray-500">Workshops</div>
                        <div class="text-2xl font-bold">{{ statistics.workshops }}</div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Edition Details</h2>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm text-gray-500">Year</label>
                            <p class="text-lg">{{ edition.year }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Duration</label>
                            <p class="text-lg">{{ edition.start_date }} - {{ edition.end_date }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Status</label>
                            <span class="px-3 py-1 rounded-full text-sm"
                                  :class="edition.is_current ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                                {{ edition.is_current ? 'Current' : 'Past' }}
                            </span>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Description</label>
                            <p class="text-lg">{{ edition.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>
