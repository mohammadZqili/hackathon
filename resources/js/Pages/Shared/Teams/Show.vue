<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Team Details</h1>
            <Link :href="route('system-admin.teams.index')"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back to Teams
            </Link>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ team.name }}</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Team information and member details</p>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Team Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ team.name }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Hackathon Edition</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                            {{ team.hackathon_edition?.name || 'N/A' }}
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Team Leader</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                            {{ team.leader?.name || 'N/A' }}
                            <span v-if="team.leader?.email" class="text-gray-500">
                                ({{ team.leader.email }})
                            </span>
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                  :class="{
                                      'bg-green-100 text-green-800': team.status === 'approved',
                                      'bg-yellow-100 text-yellow-800': team.status === 'pending',
                                      'bg-red-100 text-red-800': team.status === 'rejected'
                                  }">
                                {{ team.status || 'pending' }}
                            </span>
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Team Members</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                            <div v-if="team.members && team.members.length > 0" class="space-y-2">
                                <div v-for="member in team.members" :key="member.id" 
                                     class="flex items-center justify-between p-2 bg-gray-100 rounded">
                                    <div>
                                        <span class="font-medium">{{ member.user?.name || 'N/A' }}</span>
                                        <span v-if="member.user?.email" class="text-gray-500 ml-2">
                                            ({{ member.user.email }})
                                        </span>
                                    </div>
                                    <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded">
                                        {{ member.status || 'active' }}
                                    </span>
                                </div>
                            </div>
                            <div v-else class="text-gray-500">
                                No members added yet
                            </div>
                        </dd>
                    </div>
                    <div v-if="team.idea" class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Team Idea</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                            <div class="space-y-2">
                                <div>
                                    <span class="font-medium">{{ team.idea.title || 'No title' }}</span>
                                </div>
                                <div class="text-gray-600">
                                    {{ team.idea.description ? team.idea.description.substring(0, 200) + '...' : 'No description' }}
                                </div>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                      :class="{
                                          'bg-green-100 text-green-800': team.idea.status === 'approved',
                                          'bg-yellow-100 text-yellow-800': team.idea.status === 'pending',
                                          'bg-red-100 text-red-800': team.idea.status === 'rejected',
                                          'bg-blue-100 text-blue-800': team.idea.status === 'review'
                                      }">
                                    {{ team.idea.status || 'draft' }}
                                </span>
                            </div>
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Created At</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                            {{ new Date(team.created_at).toLocaleDateString() }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    team: {
        type: Object,
        required: true
    }
})
</script>
