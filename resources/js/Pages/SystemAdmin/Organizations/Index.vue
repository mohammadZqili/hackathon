<template>
    <div class="flex flex-col gap-4">
        <!-- Page Header -->
        <div class="flex flex-row items-start justify-between flex-wrap gap-3 p-4">
            <div class="min-w-[288px]">
                <h1 class="text-[32px] font-bold text-gray-900">Workshops</h1>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="flex flex-col">
            <div class="border-b border-gray-200 flex flex-row items-start justify-start px-4 gap-8">
                <Link :href="route('system-admin.workshops.index')"
                      class="border-b-[3px] border-transparent text-gray-500 hover:text-gray-700 flex flex-col items-center justify-center pt-4 pb-[13px] transition-colors">
                    <span class="text-sm font-bold">Workshops</span>
                </Link>
                <Link :href="route('system-admin.speakers.index')"
                      class="border-b-[3px] border-transparent text-gray-500 hover:text-gray-700 flex flex-col items-center justify-center pt-4 pb-[13px] transition-colors">
                    <span class="text-sm font-bold">Speakers</span>
                </Link>
                <button class="border-b-[3px] border-emerald-500 text-gray-900 flex flex-col items-center justify-center pt-4 pb-[13px] transition-colors">
                    <span class="text-sm font-bold">Organizations</span>
                </button>
            </div>
        </div>

        <!-- Organization Management Header -->
        <div class="px-4">
            <h2 class="text-xl font-semibold text-gray-900">Organization Management</h2>
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
                       placeholder="Search organizations"
                       class="flex-1 bg-transparent px-3 py-2 text-gray-700 placeholder-gray-500 focus:outline-none">
            </div>
        </div>

        <!-- Organizations Table -->
        <div class="px-4">
            <div class="rounded-xl bg-white border border-gray-200 overflow-hidden shadow-sm">
                <!-- Table Header -->
                <div class="bg-emerald-50 flex flex-row border-b border-gray-200">
                    <div class="flex-1 py-3 px-6">
                        <span class="text-sm font-medium text-gray-700">Organization Name</span>
                    </div>
                    <div class="w-48 py-3 px-6">
                        <span class="text-sm font-medium text-gray-700">Associated Speakers</span>
                    </div>
                    <div class="w-64 py-3 px-6">
                        <span class="text-sm font-medium text-gray-700">Linked Workshops</span>
                    </div>
                    <div class="w-32 py-3 px-6 text-center">
                        <span class="text-sm font-medium text-gray-700">Actions</span>
                    </div>
                </div>

                <!-- Table Body -->
                <div v-if="!organizations.data || organizations.data.length === 0" class="text-center py-12">
                    <p class="text-gray-500">No organizations found.</p>
                </div>
                <div v-else class="divide-y divide-gray-200">
                    <div v-for="organization in organizations.data" :key="organization.id"
                         class="flex flex-row hover:bg-gray-50 transition-colors">
                        <div class="flex-1 py-4 px-6">
                            <span class="text-sm text-gray-900 font-medium">{{ organization.name || 'Unnamed Organization' }}</span>
                        </div>
                        <div class="w-48 py-4 px-6">
                            <span class="text-sm text-emerald-600">{{ getSpeakerNames(organization) }}</span>
                        </div>
                        <div class="w-64 py-4 px-6">
                            <span class="text-sm text-emerald-600">{{ getWorkshopTitles(organization) }}</span>
                        </div>
                        <div class="w-32 py-4 px-6 flex items-center justify-center gap-1">
                            <Link :href="route('system-admin.organizations.edit', organization.id)"
                                  class="text-emerald-600 hover:text-emerald-700 text-sm font-medium transition-colors">
                                Edit
                            </Link>
                            <span class="text-gray-300">|</span>
                            <button @click="deleteOrganization(organization)"
                                    class="text-red-500 hover:text-red-600 text-sm font-medium transition-colors">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Add New Organization Button -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <Link :href="route('system-admin.organizations.create')"
                          class="inline-flex items-center justify-center w-full py-2 px-4 border border-emerald-300 rounded-lg text-emerald-700 font-medium hover:bg-emerald-50 transition-colors">
                        Add New Organization
                    </Link>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="organizations.links && organizations.total > organizations.per_page" class="px-4">
            <nav class="flex items-center justify-center gap-2">
                <Link v-for="link in organizations.links" :key="link.label"
                      :href="link.url"
                      :class="link.active ? 'bg-emerald-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300'"
                      class="px-3 py-1 rounded-md text-sm font-medium transition-colors"
                      v-html="link.label">
                </Link>
            </nav>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    organizations: {
        type: Object,
        required: true
    }
})

const searchQuery = ref('')

const deleteOrganization = (organization) => {
    if (confirm(`Are you sure you want to delete the organization "${organization.name}"?`)) {
        useForm({}).delete(route('system-admin.organizations.destroy', organization.id))
    }
}

const getSpeakerNames = (organization) => {
    if (!organization.speakers || organization.speakers.length === 0) {
        return 'No speakers'
    }
    return organization.speakers.map(s => s.name).join(', ')
}

const getWorkshopTitles = (organization) => {
    if (!organization.workshops || organization.workshops.length === 0) {
        return 'No workshops'
    }
    const titles = organization.workshops.slice(0, 2).map(w => w.title)
    if (organization.workshops.length > 2) {
        titles.push(`+${organization.workshops.length - 2} more`)
    }
    return titles.join(', ')
}

const handleSearch = () => {
    router.get(route('system-admin.organizations.index'), {
        search: searchQuery.value
    }, {
        preserveState: true,
        preserveScroll: true
    })
}
</script>
