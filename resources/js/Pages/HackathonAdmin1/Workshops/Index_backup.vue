<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Default from '../../../Layouts/Default.vue'
import { 
    AcademicCapIcon, 
    PlusIcon, 
    MagnifyingGlassIcon, 
    ClockIcon,
    MapPinIcon,
    UsersIcon,
    CalendarIcon,
    EyeIcon,
    PencilIcon,
    TrashIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    workshops: Object,
    statistics: Object,
    speakers: Array,
    filters: Object,
})

const searchForm = useForm({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
    date: props.filters?.date || '',
})

const submitSearch = () => {
    searchForm.get(route('hackathon-admin.workshops.index'), {
        preserveState: true,
        replace: true,
    })
}

const getStatusColor = (status) => {
    const now = new Date()
    const startTime = new Date(status.start_time)
    const endTime = new Date(status.end_time)
    
    if (now < startTime) {
        return 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400'
    } else if (now >= startTime && now <= endTime) {
        return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
    } else {
        return 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
    }
}

const getStatusText = (workshop) => {
    const now = new Date()
    const startTime = new Date(workshop.start_time)
    const endTime = new Date(workshop.end_time)
    
    if (now < startTime) {
        return 'Upcoming'
    } else if (now >= startTime && now <= endTime) {
        return 'Ongoing'
    } else {
        return 'Completed'
    }
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const deleteWorkshop = (workshop) => {
    if (confirm(`Are you sure you want to delete "${workshop.title}"?`)) {
        router.delete(route('hackathon-admin.workshops.destroy', workshop.id))
    }
}
</script>

<template>
    <Head title="Workshops Management" />
    
    <Default>
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Workshops Management</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Manage workshops, speakers, and attendance tracking
                    </p>
                </div>
                <a 
                    :href="route('hackathon-admin.workshops.create')" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors"
                >
                    <PlusIcon class="w-5 h-5 mr-2" />
                    Create Workshop
                </a>
            </div>

            <!-- Statistics Cards -->
            <div v-if="statistics" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                            <AcademicCapIcon class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics.total || 0 }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Workshops</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                            <ClockIcon class="w-6 h-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics.upcoming || 0 }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Upcoming</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg">
                            <ClockIcon class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics.ongoing || 0 }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Ongoing</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                            <ClockIcon class="w-6 h-6 text-gray-600 dark:text-gray-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics.completed || 0 }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Completed</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                            <UsersIcon class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics.total_registrations || 0 }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Registrations</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
                <form @submit.prevent="submitSearch" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Search Workshops
                        </label>
                        <div class="relative">
                            <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                            <input
                                v-model="searchForm.search"
                                type="text"
                                placeholder="Search by title or description..."
                                class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            />
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status
                        </label>
                        <select
                            v-model="searchForm.status"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">All Status</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date
                        </label>
                        <input
                            v-model="searchForm.date"
                            type="date"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        />
                    </div>
                    
                    <div class="flex items-end">
                        <button
                            type="submit"
                            class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors"
                        >
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Workshops List -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        Workshops ({{ workshops?.data?.length || 0 }})
                    </h3>
                </div>

                <div v-if="workshops?.data?.length > 0" class="divide-y divide-gray-200 dark:divide-gray-700">
                    <div
                        v-for="workshop in workshops.data"
                        :key="workshop.id"
                        class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                    >
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex-1">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                                            {{ workshop.title }}
                                        </h4>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                            {{ workshop.description }}
                                        </p>
                                    </div>
                                    <span 
                                        :class="getStatusColor(workshop)"
                                        class="ml-4 px-2 py-1 text-xs font-medium rounded-full whitespace-nowrap"
                                    >
                                        {{ getStatusText(workshop) }}
                                    </span>
                                </div>
                                
                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                                        <CalendarIcon class="w-4 h-4 mr-2" />
                                        {{ formatDate(workshop.start_time) }}
                                    </div>
                                    
                                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                                        <MapPinIcon class="w-4 h-4 mr-2" />
                                        {{ workshop.location || 'TBD' }}
                                    </div>
                                    
                                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                                        <UsersIcon class="w-4 h-4 mr-2" />
                                        {{ workshop.registrations_count || 0 }} / {{ workshop.max_attendees || 'Unlimited' }}
                                    </div>
                                    
                                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                                        <AcademicCapIcon class="w-4 h-4 mr-2" />
                                        {{ workshop.speakers?.[0]?.name || 'TBD' }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="mt-4 lg:mt-0 lg:ml-6 flex items-center space-x-2">
                                <a
                                    :href="route('hackathon-admin.workshops.show', workshop.id)"
                                    class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                    title="View Details"
                                >
                                    <EyeIcon class="w-4 h-4" />
                                </a>
                                <a
                                    :href="route('hackathon-admin.workshops.edit', workshop.id)"
                                    class="p-2 text-gray-400 hover:text-green-600 dark:hover:text-green-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                    title="Edit Workshop"
                                >
                                    <PencilIcon class="w-4 h-4" />
                                </a>
                                <button
                                    @click="deleteWorkshop(workshop)"
                                    class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                    title="Delete Workshop"
                                >
                                    <TrashIcon class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="p-12 text-center">
                    <AcademicCapIcon class="mx-auto w-12 h-12 text-gray-400 dark:text-gray-600 mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No workshops found</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        {{ searchForm.search || searchForm.status || searchForm.date 
                            ? 'Try adjusting your search filters.' 
                            : 'Get started by creating your first workshop.' }}
                    </p>
                    <a
                        :href="route('hackathon-admin.workshops.create')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors"
                    >
                        <PlusIcon class="w-4 h-4 mr-2" />
                        Create Workshop
                    </a>
                </div>

                <!-- Pagination -->
                <div v-if="workshops?.links && workshops.data?.length > 0" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <nav class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <component 
                                :is="workshops.links.prev ? 'a' : 'span'" 
                                :href="workshops.links.prev"
                                :class="[
                                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium rounded-md',
                                    workshops.links.prev 
                                        ? 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600'
                                        : 'border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed dark:bg-gray-800 dark:border-gray-600 dark:text-gray-500'
                                ]"
                            >
                                Previous
                            </component>
                            <component 
                                :is="workshops.links.next ? 'a' : 'span'" 
                                :href="workshops.links.next"
                                :class="[
                                    'ml-3 relative inline-flex items-center px-4 py-2 border text-sm font-medium rounded-md',
                                    workshops.links.next 
                                        ? 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600'
                                        : 'border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed dark:bg-gray-800 dark:border-gray-600 dark:text-gray-500'
                                ]"
                            >
                                Next
                            </component>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    Showing {{ workshops.from || 0 }} to {{ workshops.to || 0 }} of {{ workshops.total || 0 }} results
                                </p>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </Default>
</template>
