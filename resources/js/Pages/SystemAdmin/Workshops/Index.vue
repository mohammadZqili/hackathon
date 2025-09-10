<template>
    <div class="flex flex-col gap-4">
        <!-- Page Header -->
        <div class="flex flex-row items-start justify-between flex-wrap gap-3 p-4">
            <div class="min-w-[288px]">
                <h1 class="text-[32px] font-bold text-gray-900">Workshops</h1>
            </div>
            <Link :href="route('system-admin.workshops.create')"
                  class="rounded-xl bg-emerald-100 hover:bg-emerald-200 transition-colors h-8 flex items-center justify-center px-4 min-w-[84px] text-sm font-medium text-emerald-800">
                Add Workshop
            </Link>
        </div>

        <!-- Tabs Navigation -->
        <div class="flex flex-col">
            <div class="border-b border-gray-200 flex flex-row items-start justify-start px-4 gap-8">
                <button @click="activeTab = 'workshops'"
                        :class="activeTab === 'workshops' ? 'border-emerald-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="border-b-[3px] flex flex-col items-center justify-center pt-4 pb-[13px] transition-colors">
                    <span class="text-sm font-bold">Workshops</span>
                </button>
                <button @click="activeTab = 'speakers'"
                        :class="activeTab === 'speakers' ? 'border-emerald-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="border-b-[3px] flex flex-col items-center justify-center pt-4 pb-[13px] transition-colors">
                    <span class="text-sm font-bold">Speakers</span>
                </button>
                <button @click="activeTab = 'organizations'"
                        :class="activeTab === 'organizations' ? 'border-emerald-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="border-b-[3px] flex flex-col items-center justify-center pt-4 pb-[13px] transition-colors">
                    <span class="text-sm font-bold">Organizations</span>
                </button>
            </div>
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
                       placeholder="Search workshops"
                       class="flex-1 bg-transparent px-3 py-2 text-gray-700 placeholder-gray-500 focus:outline-none">
            </div>
        </div>

        <!-- Workshops Content -->
        <div v-if="activeTab === 'workshops'" class="px-4">
            <div class="rounded-xl bg-white border border-gray-200 overflow-hidden">
                <!-- Table Header -->
                <div class="bg-emerald-50 flex flex-row border-b border-gray-200">
                    <div class="flex-1 py-3 px-4">
                        <span class="text-sm font-medium text-gray-700">Title</span>
                    </div>
                    <div class="w-48 py-3 px-4">
                        <span class="text-sm font-medium text-gray-700">Description</span>
                    </div>
                    <div class="w-32 py-3 px-4">
                        <span class="text-sm font-medium text-gray-700">Date</span>
                    </div>
                    <div class="w-36 py-3 px-4">
                        <span class="text-sm font-medium text-gray-700">Speaker</span>
                    </div>
                    <div class="w-40 py-3 px-4">
                        <span class="text-sm font-medium text-gray-700">Organization</span>
                    </div>
                    <div class="w-24 py-3 px-4">
                        <span class="text-sm font-medium text-gray-700">Seats</span>
                    </div>
                    <div class="w-32 py-3 px-4 text-center">
                        <span class="text-sm font-medium text-gray-700">Actions</span>
                    </div>
                </div>

                <!-- Table Body -->
                <div v-if="!workshops.data || workshops.data.length === 0" class="text-center py-12">
                    <p class="text-gray-500">No workshops found.</p>
                </div>
                <div v-else class="divide-y divide-gray-200">
                    <div v-for="workshop in workshops.data" :key="workshop.id"
                         class="flex flex-row hover:bg-gray-50 transition-colors">
                        <div class="flex-1 py-4 px-4">
                            <span class="text-sm text-gray-900 font-medium">{{ workshop.title || 'Untitled' }}</span>
                        </div>
                        <div class="w-48 py-4 px-4">
                            <span class="text-sm text-gray-600 line-clamp-2">{{ workshop.description || 'No description' }}</span>
                        </div>
                        <div class="w-32 py-4 px-4">
                            <span class="text-sm text-emerald-600">{{ formatDate(workshop.start_date) }}</span>
                        </div>
                        <div class="w-36 py-4 px-4">
                            <span class="text-sm text-emerald-600">{{ workshop.speakers?.[0]?.name || 'TBD' }}</span>
                        </div>
                        <div class="w-40 py-4 px-4">
                            <span class="text-sm text-emerald-600">{{ workshop.organization?.name || 'TBD' }}</span>
                        </div>
                        <div class="w-24 py-4 px-4">
                            <span class="text-sm text-emerald-600">{{ workshop.max_attendees || '∞' }}</span>
                        </div>
                        <div class="w-32 py-4 px-4 flex items-center justify-center gap-2">
                            <Link :href="route('system-admin.workshops.edit', workshop.id)"
                                  class="text-emerald-600 hover:text-emerald-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </Link>
                            <button @click="deleteWorkshop(workshop)"
                                    class="text-red-500 hover:text-red-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="workshops.links && workshops.total > workshops.per_page" class="mt-6 flex justify-center">
                <nav class="flex items-center gap-2">
                    <Link v-for="link in workshops.links" :key="link.label"
                          :href="link.url"
                          :class="link.active ? 'bg-emerald-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                          class="px-3 py-1 rounded-md text-sm font-medium transition-colors"
                          v-html="link.label">
                    </Link>
                </nav>
            </div>
        </div>

        <!-- Speakers Tab Content -->
        <div v-if="activeTab === 'speakers'" class="px-4">
            <div class="rounded-xl bg-white border border-gray-200 p-6">
                <p class="text-gray-500 text-center">Speakers management coming soon...</p>
            </div>
        </div>

        <!-- Organizations Tab Content -->
        <div v-if="activeTab === 'organizations'" class="px-4">
            <div class="rounded-xl bg-white border border-gray-200 p-6">
                <p class="text-gray-500 text-center">Organizations management coming soon...</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    workshops: {
        type: Object,
        required: true
    }
})

const activeTab = ref('workshops')
const searchQuery = ref('')

const deleteWorkshop = (workshop) => {
    if (confirm(`Are you sure you want to delete the workshop "${workshop.title}"?`)) {
        useForm({}).delete(route('system-admin.workshops.destroy', workshop.id))
    }
}

const formatDate = (date) => {
    if (!date) return 'TBD'
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const handleSearch = () => {
    // Implement search functionality
    router.get(route('system-admin.workshops.index'), {
        search: searchQuery.value
    }, {
        preserveState: true,
        preserveScroll: true
    })
}
</script>
