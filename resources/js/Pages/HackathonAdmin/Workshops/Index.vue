<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, computed, watch, onMounted } from 'vue'
import Default from '@/Layouts/Default.vue'
import PageHeader from '@/Components/Shared/PageHeader.vue'
import SearchBar from '@/Components/Shared/SearchBar.vue'
import DataTable from '@/Components/Shared/DataTable.vue'

const props = defineProps({
    workshops: Object,
    statistics: Object,
    speakers: Array,
    organizations: Array,
    filters: Object,
    permissions: Object
})

const searchForm = useForm({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
    date: props.filters?.date || '',
})

// Theme colors
const themeColor = ref({
    primary: '#0d9488',
    hover: '#0f766e',
    rgb: '13, 148, 136',
    gradientFrom: '#0d9488',
    gradientTo: '#14b8a6'
})

onMounted(() => {
    const root = document.documentElement
    const primary = getComputedStyle(root).getPropertyValue('--primary-color').trim() || '#0d9488'
    const hover = getComputedStyle(root).getPropertyValue('--primary-hover').trim() || '#0f766e'
    const rgb = getComputedStyle(root).getPropertyValue('--primary-color-rgb').trim() || '13, 148, 136'
    const gradientFrom = getComputedStyle(root).getPropertyValue('--primary-gradient-from').trim() || '#0d9488'
    const gradientTo = getComputedStyle(root).getPropertyValue('--primary-gradient-to').trim() || '#14b8a6'

    themeColor.value = {
        primary: primary || themeColor.value.primary,
        hover: hover || themeColor.value.hover,
        rgb: rgb || themeColor.value.rgb,
        gradientFrom: gradientFrom || themeColor.value.gradientFrom,
        gradientTo: gradientTo || themeColor.value.gradientTo
    }
})

const themeStyles = computed(() => ({
    '--theme-primary': themeColor.value.primary,
    '--theme-hover': themeColor.value.hover,
    '--theme-rgb': themeColor.value.rgb,
    '--theme-gradient-from': themeColor.value.gradientFrom,
    '--theme-gradient-to': themeColor.value.gradientTo,
}))

// Workshop status logic
const getWorkshopStatus = (workshop) => {
    const now = new Date()
    const workshopDateTime = new Date(`${workshop.date} ${workshop.start_time}`)
    const endDateTime = new Date(`${workshop.date} ${workshop.end_time}`)
    
    if (now < workshopDateTime) {
        return { status: 'upcoming', label: 'Upcoming' }
    } else if (now >= workshopDateTime && now <= endDateTime) {
        return { status: 'ongoing', label: 'Ongoing' }
    } else {
        return { status: 'completed', label: 'Completed' }
    }
}

const getStatusBadgeClass = (status) => {
    const statusClasses = {
        upcoming: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
        ongoing: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
        completed: 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
    }
    return statusClasses[status] || statusClasses.upcoming
}

// Table configuration
const columns = [
    {
        key: 'title',
        label: 'Workshop Title',
        width: 'w-[280px]'
    },
    {
        key: 'speakers',
        label: 'Speaker(s)',
        width: 'w-[180px]'
    },
    {
        key: 'date',
        label: 'Date',
        width: 'w-[120px]',
        formatter: (item) => formatDate(item.date)
    },
    {
        key: 'time',
        label: 'Time',
        width: 'w-[140px]',
        formatter: (item) => `${item.start_time} - ${item.end_time}`
    },
    {
        key: 'location',
        label: 'Location',
        width: 'w-[150px]',
        defaultValue: 'Online'
    },
    {
        key: 'capacity',
        label: 'Capacity',
        width: 'w-[100px]',
        formatter: (item) => `${item.registered_count || 0}/${item.max_capacity}`
    },
    {
        key: 'status',
        label: 'Status',
        width: 'w-[120px]'
    },
    {
        key: 'actions',
        label: 'Actions',
        width: 'w-[200px]'
    }
]

const handleSearch = () => {
    clearTimeout(window.searchTimeout)
    window.searchTimeout = setTimeout(filterWorkshops, 300)
}

const filterWorkshops = () => {
    router.get(route('hackathon-admin.workshops.index'), {
        search: searchForm.search,
        status: searchForm.status,
        date: searchForm.date,
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}

watch(() => searchForm.status, filterWorkshops)
watch(() => searchForm.date, filterWorkshops)

const formatDate = (date) => {
    return date ? new Date(date).toLocaleDateString() : ''
}

const formatTime = (time) => {
    return time ? new Date(`2000-01-01 ${time}`).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : ''
}

const openCreateModal = () => {
    router.visit(route('hackathon-admin.workshops.create'))
}

const viewWorkshop = (workshop) => {
    router.visit(route('hackathon-admin.workshops.show', workshop.id))
}

const editWorkshop = (workshop) => {
    router.visit(route('hackathon-admin.workshops.edit', workshop.id))
}

const viewAttendance = (workshop) => {
    router.visit(route('hackathon-admin.workshops.attendance', workshop.id))
}

const generateQR = (workshop) => {
    router.post(route('hackathon-admin.workshops.generate-qr', workshop.id), {}, {
        preserveScroll: true
    })
}

const deleteWorkshop = (workshop) => {
    if (confirm('Are you sure you want to delete this workshop? This action cannot be undone.')) {
        router.delete(route('hackathon-admin.workshops.destroy', workshop.id))
    }
}
</script>

<template>
    <Head title="Workshops Management - Hackathon Admin" />

    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <PageHeader 
                title="Workshops Management"
                subtitle="Manage workshops for current hackathon edition"
                :show-action-button="permissions?.canCreate"
                action-button-text="New Workshop"
                @action="openCreateModal"
            />

            <!-- Statistics Cards -->
            <div v-if="statistics" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6 px-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Workshops</p>
                            <p class="text-2xl font-bold" :style="{ color: themeColor.primary }">{{ statistics.total || 0 }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center" 
                             :style="{ backgroundColor: themeColor.primary + '20' }">
                            <svg class="w-6 h-6" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Upcoming</p>
                            <p class="text-2xl font-bold text-blue-600">{{ statistics.upcoming || 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Ongoing</p>
                            <p class="text-2xl font-bold text-green-600">{{ statistics.ongoing || 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Registrations</p>
                            <p class="text-2xl font-bold" :style="{ color: themeColor.primary }">{{ statistics.total_registrations || 0 }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                             :style="{ backgroundColor: themeColor.primary + '20' }">
                            <svg class="w-6 h-6" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="px-4 mb-4">
                <div class="flex flex-wrap gap-4">
                    <!-- Status Filter -->
                    <div class="min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select v-model="searchForm.status" 
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-primary focus:ring-primary">
                            <option value="">All Status</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>

                    <!-- Date Filter -->
                    <div class="min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                        <input v-model="searchForm.date"
                               type="date"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
            <SearchBar 
                v-model="searchForm.search"
                placeholder="Search workshops by title, description or speaker"
                @update:model-value="handleSearch"
            />

            <!-- Workshops Table -->
            <DataTable
                :data="workshops?.data || []"
                :columns="columns"
                empty-message="No workshops found for your hackathon edition. Click 'New Workshop' to create the first workshop."
            >
                <!-- Speakers Column -->
                <template #speakers="{ item }">
                    <div v-if="item.speakers && item.speakers.length > 0">
                        <div v-for="(speaker, index) in item.speakers" :key="speaker.id" class="text-sm">
                            <span class="text-gray-900 dark:text-white">{{ speaker.name }}</span>
                            <span v-if="speaker.organization" class="text-gray-500 dark:text-gray-400 text-xs block">{{ speaker.organization.name }}</span>
                        </div>
                    </div>
                    <span v-else class="text-sm text-gray-500 dark:text-gray-400">No Speaker</span>
                </template>

                <!-- Capacity Column -->
                <template #capacity="{ item }">
                    <div class="text-sm">
                        <span class="font-medium" :style="{ color: themeColor.primary }">
                            {{ item.registered_count || 0 }}
                        </span>
                        <span class="text-gray-500 dark:text-gray-400">
                            /{{ item.max_capacity }}
                        </span>
                        <div v-if="item.max_capacity" class="mt-1">
                            <div class="w-16 bg-gray-200 rounded-full h-1">
                                <div class="h-1 rounded-full"
                                     :style="{ 
                                         backgroundColor: themeColor.primary, 
                                         width: `${Math.min(100, ((item.registered_count || 0) / item.max_capacity) * 100)}%` 
                                     }"></div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Status Column -->
                <template #status="{ item }">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="getStatusBadgeClass(getWorkshopStatus(item).status)">
                        {{ getWorkshopStatus(item).label }}
                    </span>
                </template>

                <!-- Actions Column -->
                <template #actions="{ item }">
                    <div class="flex items-center gap-2">
                        <button @click="viewWorkshop(item)"
                                class="font-bold hover:underline transition-colors text-sm"
                                :style="{ color: themeColor.primary }">
                            View
                        </button>
                        
                        <span v-if="permissions?.canEdit" :style="{ color: themeColor.primary }">|</span>
                        <button v-if="permissions?.canEdit" 
                                @click="editWorkshop(item)"
                                class="font-bold hover:underline transition-colors text-sm"
                                :style="{ color: themeColor.primary }">
                            Edit
                        </button>

                        <span :style="{ color: themeColor.primary }">|</span>
                        <button @click="viewAttendance(item)"
                                class="font-bold hover:underline transition-colors text-sm text-blue-600">
                            Attendance
                        </button>

                        <span :style="{ color: themeColor.primary }">|</span>
                        <button @click="generateQR(item)"
                                class="font-bold hover:underline transition-colors text-sm text-green-600">
                            QR Code
                        </button>

                        <span v-if="permissions?.canDelete" :style="{ color: themeColor.primary }">|</span>
                        <button v-if="permissions?.canDelete"
                                @click="deleteWorkshop(item)"
                                class="font-bold hover:underline transition-colors text-sm text-red-600">
                            Delete
                        </button>
                    </div>
                </template>
            </DataTable>

            <!-- Pagination -->
            <div v-if="workshops?.links" class="px-4 py-3">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Showing {{ workshops.from }} to {{ workshops.to }} of {{ workshops.total }} results
                    </div>
                    <div class="flex space-x-1">
                        <template v-for="(link, index) in workshops.links" :key="index">
                            <button v-if="link.url && link.label !== '&laquo; Previous' && link.label !== 'Next &raquo;'"
                                    @click="router.visit(link.url)"
                                    class="px-3 py-2 text-sm font-medium rounded-md transition-colors"
                                    :class="link.active 
                                        ? 'text-white shadow-sm' 
                                        : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                    :style="link.active ? {
                                        background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                                    } : {}">
                                {{ link.label }}
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

<style scoped>
input[type="text"]:focus,
input[type="date"]:focus,
select:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>