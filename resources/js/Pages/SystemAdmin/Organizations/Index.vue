<template>
    <Head title="Organizations Management" />
    <Default>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Organizations Management</h1>
            <Link :href="route('system-admin.organizations.create')"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                Add Organization
            </Link>
        </div>
        
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Partner Organizations</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Manage organizations that partner with the hackathon for workshops and sponsorships
                </p>
            </div>
            
            <div class="p-6">
                <div v-if="organizations.data.length === 0" class="text-center py-8">
                    <p class="text-gray-500">No organizations found.</p>
                </div>
                
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="organization in organizations.data" :key="organization.id"
                         class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-gray-300 rounded-lg mx-auto mb-4 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm12 12H4V8h12v8zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">
                                {{ organization.name || 'Unnamed Organization' }}
                            </h3>
                            <p v-if="organization.description" class="text-sm text-gray-500 mb-4">
                                {{ organization.description.substring(0, 100) }}{{ organization.description.length > 100 ? '...' : '' }}
                            </p>
                            <div v-if="organization.website" class="mb-4">
                                <a :href="organization.website" 
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-900 text-sm">
                                    {{ organization.website }}
                                </a>
                            </div>
                            <div class="flex justify-center space-x-2">
                                <Link :href="route('system-admin.organizations.show', organization.id)"
                                      class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    View
                                </Link>
                                <Link :href="route('system-admin.organizations.edit', organization.id)"
                                      class="text-green-600 hover:text-green-900 text-sm font-medium">
                                    Edit
                                </Link>
                                <button @click="deleteOrganization(organization)"
                                        class="text-red-600 hover:text-red-900 text-sm font-medium">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pagination -->
                <div v-if="organizations.links" class="mt-6">
                    <nav class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="organizations.prev_page_url" :href="organizations.prev_page_url"
                                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </Link>
                            <Link v-if="organizations.next_page_url" :href="organizations.next_page_url"
                                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing {{ organizations.from }} to {{ organizations.to }} of {{ organizations.total }} results
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
    organizations: {
        type: Object,
        required: true
    }
})

const deleteOrganization = (organization) => {
    if (confirm(`Are you sure you want to delete the organization "${organization.name}"?`)) {
        useForm({}).delete(route('system-admin.organizations.destroy', organization.id))
    }
}
</script>
