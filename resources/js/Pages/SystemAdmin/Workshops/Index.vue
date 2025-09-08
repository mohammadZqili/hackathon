<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Workshops Management (All Editions)</h1>
            <Link :href="route('system-admin.workshops.create')"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                Create Workshop
            </Link>
        </div>
        
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">All Workshops Across All Hackathon Editions</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Create, manage, and track attendance for all workshops
                </p>
            </div>
            
            <div class="p-6">
                <div v-if="workshops.data.length === 0" class="text-center py-8">
                    <p class="text-gray-500">No workshops found.</p>
                </div>
                
                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Workshop Title
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Edition
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date & Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Speakers
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Capacity
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Registrations
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="workshop in workshops.data" :key="workshop.id">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ workshop.title || 'Untitled' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ workshop.description ? workshop.description.substring(0, 100) + '...' : 'No description' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ workshop.hackathon_edition?.name || 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ workshop.start_date ? new Date(workshop.start_date).toLocaleDateString() : 'TBD' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ workshop.start_time || 'Time TBD' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ workshop.speakers?.length || 0 }} speakers
                                    </div>
                                    <div v-if="workshop.speakers && workshop.speakers.length > 0" class="text-sm text-gray-500">
                                        {{ workshop.speakers[0].name }}{{ workshop.speakers.length > 1 ? ` +${workshop.speakers.length - 1} more` : '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ workshop.max_attendees || 'Unlimited' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ workshop.registrations?.length || 0 }} registered
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ workshop.attendances_count || 0 }} attended
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <Link :href="route('system-admin.workshops.show', workshop.id)"
                                          class="text-blue-600 hover:text-blue-900 mr-3">
                                        View
                                    </Link>
                                    <Link :href="route('system-admin.workshops.edit', workshop.id)"
                                          class="text-green-600 hover:text-green-900 mr-3">
                                        Edit
                                    </Link>
                                    <Link :href="route('system-admin.workshops.attendance', workshop.id)"
                                          class="text-purple-600 hover:text-purple-900 mr-3">
                                        Attendance
                                    </Link>
                                    <button @click="deleteWorkshop(workshop)"
                                            class="text-red-600 hover:text-red-900">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div v-if="workshops.links" class="mt-6">
                    <nav class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="workshops.prev_page_url" :href="workshops.prev_page_url"
                                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </Link>
                            <Link v-if="workshops.next_page_url" :href="workshops.next_page_url"
                                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing {{ workshops.from }} to {{ workshops.to }} of {{ workshops.total }} results
                                </p>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    workshops: {
        type: Object,
        required: true
    }
})

const deleteWorkshop = (workshop) => {
    if (confirm(`Are you sure you want to delete the workshop "${workshop.title}"?`)) {
        useForm({}).delete(route('system-admin.workshops.destroy', workshop.id))
    }
}
</script>
