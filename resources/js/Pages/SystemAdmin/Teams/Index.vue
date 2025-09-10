<template>
    <div class="flex flex-col gap-4">
        <!-- Page Header -->
        <div class="flex flex-row items-start justify-between flex-wrap gap-3 p-4">
            <div class="min-w-[288px]">
                <h1 class="text-[32px] font-bold text-gray-900">Teams</h1>
            </div>
            <Link :href="route('system-admin.teams.create')"
                  class="rounded-xl bg-emerald-100 hover:bg-emerald-200 transition-colors h-8 flex items-center justify-center px-4 min-w-[84px] text-sm font-medium text-emerald-800">
                New Team
            </Link>
        </div>

        <!-- Search Bar -->
        <div class="px-4">
            <div class="h-12 flex flex-row rounded-xl overflow-hidden bg-emerald-50 max-w-2xl">
                <div class="w-10 flex items-center justify-center bg-emerald-100">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input v-model="searchQuery"
                       @input="handleSearch"
                       type="text"
                       placeholder="Search teams"
                       class="flex-1 bg-transparent px-3 py-2 text-gray-700 placeholder-gray-500 focus:outline-none">
            </div>
        </div>

        <!-- Teams Table -->
        <div class="px-4">
            <div class="rounded-xl bg-white border border-gray-200 overflow-hidden shadow-sm">
                <!-- Table Header -->
                <div class="bg-emerald-50 flex flex-row border-b border-gray-200">
                    <div class="flex-1 py-3 px-6">
                        <span class="text-sm font-medium text-gray-700">Team Name</span>
                    </div>
                    <div class="w-36 py-3 px-6">
                        <span class="text-sm font-medium text-gray-700">Founding Date</span>
                    </div>
                    <div class="w-44 py-3 px-6">
                        <span class="text-sm font-medium text-gray-700">Team Leader</span>
                    </div>
                    <div class="w-32 py-3 px-6 text-center">
                        <span class="text-sm font-medium text-gray-700">Actions</span>
                    </div>
                </div>

                <!-- Table Body -->
                <div v-if="!teams.data || teams.data.length === 0" class="text-center py-12">
                    <p class="text-gray-500">No teams found.</p>
                </div>
                <div v-else class="divide-y divide-gray-200">
                    <div v-for="team in teams.data" :key="team.id"
                         class="flex flex-row hover:bg-gray-50 transition-colors">
                        <div class="flex-1 py-4 px-6">
                            <span class="text-sm text-gray-900 font-medium">{{ team.name }}</span>
                        </div>
                        <div class="w-36 py-4 px-6">
                            <span class="text-sm text-emerald-600">{{ formatDate(team.created_at) }}</span>
                        </div>
                        <div class="w-44 py-4 px-6">
                            <span class="text-sm text-emerald-600">{{ team.leader?.name || 'No Leader' }}</span>
                        </div>
                        <div class="w-32 py-4 px-6 flex items-center justify-center gap-1">
                            <Link :href="route('system-admin.teams.edit', team.id)"
                                  class="text-emerald-600 hover:text-emerald-700 text-sm font-medium transition-colors">
                                Edit
                            </Link>
                            <span class="text-gray-300">|</span>
                            <button @click="deleteTeam(team)"
                                    class="text-red-500 hover:text-red-600 text-sm font-medium transition-colors">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="teams.links && teams.total > teams.per_page" class="border-t border-gray-200 px-6 py-3 bg-gray-50">
                    <nav class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ teams.from }} to {{ teams.to }} of {{ teams.total }} results
                        </div>
                        <div class="flex items-center gap-2">
                            <Link v-for="link in teams.links" :key="link.label"
                                  :href="link.url"
                                  :class="link.active ? 'bg-emerald-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300'"
                                  class="px-3 py-1 rounded-md text-sm font-medium transition-colors"
                                  v-html="link.label">
                            </Link>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    teams: {
        type: Object,
        required: true
    }
})

const searchQuery = ref('')

const deleteTeam = (team) => {
    if (confirm(`Are you sure you want to delete the team "${team.name}"?`)) {
        useForm({}).delete(route('system-admin.teams.destroy', team.id))
    }
}

const formatDate = (date) => {
    if (!date) return 'N/A'
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    })
}

const handleSearch = () => {
    router.get(route('system-admin.teams.index'), {
        search: searchQuery.value
    }, {
        preserveState: true,
        preserveScroll: true
    })
}
</script>
