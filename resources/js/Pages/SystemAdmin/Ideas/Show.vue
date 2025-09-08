<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Idea Details</h1>
            <Link :href="route('system-admin.ideas.index')"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back to Ideas
            </Link>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ idea.title || 'Untitled Idea' }}</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Detailed idea information and review history</p>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Title</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ idea.title || 'No title provided' }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                            <div class="whitespace-pre-wrap">{{ idea.description || 'No description provided' }}</div>
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Team</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                            <div>
                                <span class="font-medium">{{ idea.team?.name || 'N/A' }}</span>
                                <span v-if="idea.team?.leader" class="text-gray-500 ml-2">
                                    (Leader: {{ idea.team.leader.name }})
                                </span>
                            </div>
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Track</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ idea.track?.name || 'N/A' }}</dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Hackathon Edition</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ idea.hackathon_edition?.name || 'N/A' }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                  :class="{
                                      'bg-green-100 text-green-800': idea.status === 'approved',
                                      'bg-yellow-100 text-yellow-800': idea.status === 'pending',
                                      'bg-red-100 text-red-800': idea.status === 'rejected',
                                      'bg-blue-100 text-blue-800': idea.status === 'review',
                                      'bg-gray-100 text-gray-800': idea.status === 'draft'
                                  }">
                                {{ idea.status || 'draft' }}
                            </span>
                        </dd>
                    </div>
                    <div v-if="idea.files && idea.files.length > 0" class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Attached Files</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                            <div class="space-y-2">
                                <div v-for="file in idea.files" :key="file.id" 
                                     class="flex items-center justify-between p-2 bg-gray-100 rounded">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="font-medium">{{ file.original_name || file.file_name || 'Unknown File' }}</span>
                                        <span v-if="file.file_size" class="text-gray-500 ml-2">
                                            ({{ Math.round(file.file_size / 1024) }} KB)
                                        </span>
                                    </div>
                                    <a :href="file.download_url || '#'" 
                                       class="text-blue-600 hover:text-blue-900 text-sm">
                                        Download
                                    </a>
                                </div>
                            </div>
                        </dd>
                    </div>
                    <div v-if="idea.audit_logs && idea.audit_logs.length > 0" class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Review History</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                            <div class="space-y-3">
                                <div v-for="log in idea.audit_logs" :key="log.id" 
                                     class="border-l-4 border-gray-200 pl-4">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium">{{ log.action || 'Action' }}</span>
                                        <span class="text-gray-500 text-xs">
                                            {{ new Date(log.created_at).toLocaleString() }}
                                        </span>
                                    </div>
                                    <div v-if="log.user" class="text-gray-600 text-sm">
                                        by {{ log.user.name }}
                                    </div>
                                    <div v-if="log.notes" class="text-gray-700 mt-1">
                                        {{ log.notes }}
                                    </div>
                                </div>
                            </div>
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Submitted At</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                            {{ idea.created_at ? new Date(idea.created_at).toLocaleString() : 'N/A' }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                            {{ idea.updated_at ? new Date(idea.updated_at).toLocaleString() : 'N/A' }}
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
    idea: {
        type: Object,
        required: true
    }
})
</script>
