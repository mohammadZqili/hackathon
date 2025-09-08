<template>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Teams Management (All Editions)</h1>
        
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">All Teams Across All Hackathon Editions</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Manage and monitor all teams across all hackathon editions
                </p>
            </div>
            
            <div class="p-6">
                <div v-if="teams.data.length === 0" class="text-center py-8">
                    <p class="text-gray-500">No teams found.</p>
                </div>
                
                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Team Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Edition
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Leader
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Members
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="team in teams.data" :key="team.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ team.name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ team.hackathon_edition?.name || 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ team.leader?.name || 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ team.members?.length || 0 }} members
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                          :class="{
                                              'bg-green-100 text-green-800': team.status === 'approved',
                                              'bg-yellow-100 text-yellow-800': team.status === 'pending',
                                              'bg-red-100 text-red-800': team.status === 'rejected'
                                          }">
                                        {{ team.status || 'pending' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <Link :href="route('system-admin.teams.show', team.id)"
                                          class="text-blue-600 hover:text-blue-900 mr-3">
                                        View
                                    </Link>
                                    <button @click="deleteTeam(team)"
                                            class="text-red-600 hover:text-red-900">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div v-if="teams.links" class="mt-6">
                    <nav class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="teams.prev_page_url" :href="teams.prev_page_url"
                                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </Link>
                            <Link v-if="teams.next_page_url" :href="teams.next_page_url"
                                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing {{ teams.from }} to {{ teams.to }} of {{ teams.total }} results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <template v-for="link in teams.links" :key="link.label">
                                        <Link v-if="link.url"
                                              :href="link.url"
                                              :class="[
                                                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                                  link.active
                                                      ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                                              ]"
                                              v-html="link.label">
                                        </Link>
                                        <span v-else
                                              :class="[
                                                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium cursor-default',
                                                  'bg-white border-gray-300 text-gray-300'
                                              ]"
                                              v-html="link.label">
                                        </span>
                                    </template>
                                </nav>
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
    teams: {
        type: Object,
        required: true
    }
})

const deleteTeam = (team) => {
    if (confirm(`Are you sure you want to delete the team "${team.name}"?`)) {
        useForm({}).delete(route('system-admin.teams.destroy', team.id))
    }
}
</script>
