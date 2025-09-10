<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import PageHeader from '../../../Components/Shared/PageHeader.vue'
import DataTable from '../../../Components/Shared/DataTable.vue'
import SearchBar from '../../../Components/Shared/SearchBar.vue'
import { ref, computed, onMounted } from 'vue'
import { CalendarIcon, ClockIcon, UserGroupIcon, QrCodeIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    workshops: Object,
    search: String,
    statistics: Object,
})

// Theme integration
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

const searchQuery = ref(props.search || '')

const columns = [
    {
        key: 'title',
        label: 'Workshop Title',
        sortable: true,
        formatter: (value, item) => ({
            component: 'link',
            text: value,
            href: route('workshop-supervisor.workshops.show', item.id)
        })
    },
    {
        key: 'date',
        label: 'Date',
        sortable: true,
        formatter: (value) => new Date(value).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        })
    },
    {
        key: 'time',
        label: 'Time',
        sortable: false,
        formatter: (value, item) => {
            const start = new Date(`2000-01-01T${item.start_time}`).toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            })
            const end = new Date(`2000-01-01T${item.end_time}`).toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            })
            return `${start} - ${end}`
        }
    },
    {
        key: 'capacity',
        label: 'Capacity',
        sortable: true,
        formatter: (value, item) => `${item.registrations_count || 0}/${value}`
    },
    {
        key: 'status',
        label: 'Status',
        sortable: false,
        slot: 'status'
    },
    {
        key: 'actions',
        label: '',
        sortable: false,
        slot: 'actions'
    }
]

const getWorkshopStatus = (workshop) => {
    const now = new Date()
    const workshopDate = new Date(workshop.date)
    const startTime = new Date(`${workshop.date}T${workshop.start_time}`)
    const endTime = new Date(`${workshop.date}T${workshop.end_time}`)
    
    if (now < startTime) {
        return { text: 'Upcoming', class: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' }
    } else if (now >= startTime && now <= endTime) {
        return { text: 'In Progress', class: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' }
    } else {
        return { text: 'Completed', class: 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400' }
    }
}

const getCapacityPercentage = (workshop) => {
    if (!workshop.capacity || workshop.capacity === 0) return 0
    return Math.round((workshop.registrations_count / workshop.capacity) * 100)
}

const handleSearch = (query) => {
    router.get(route('workshop-supervisor.workshops.index'), { search: query }, {
        preserveState: true,
        replace: true
    })
}
</script>

<template>
    <Head title="My Workshops" />
    
    <Default>
        <div class="max-w-7xl mx-auto" :style="themeStyles">
            <PageHeader 
                title="My Workshops" 
                description="Manage your assigned workshops and track attendance"
                :show-action="false"
            />

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg" :style="{ backgroundColor: `rgba(${themeColor.rgb}, 0.1)` }">
                            <CalendarIcon class="w-6 h-6" :style="{ color: themeColor.primary }" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Workshops</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics?.total || 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900/20">
                            <ClockIcon class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Upcoming</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics?.upcoming || 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-green-100 dark:bg-green-900/20">
                            <UserGroupIcon class="w-6 h-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Attendees</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics?.total_attendees || 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-purple-100 dark:bg-purple-900/20">
                            <QrCodeIcon class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Check-ins Today</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics?.todays_checkins || 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <SearchBar 
                        v-model="searchQuery"
                        placeholder="Search workshops..."
                        @search="handleSearch"
                        class="flex-1 max-w-md"
                    />
                </div>
            </div>

            <!-- Workshops Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <DataTable 
                    :data="workshops.data" 
                    :columns="columns"
                    empty-message="No workshops assigned yet."
                >
                    <template #status="{ item }">
                        <div class="flex flex-col space-y-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                  :class="getWorkshopStatus(item).class">
                                {{ getWorkshopStatus(item).text }}
                            </span>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full transition-all duration-300" 
                                     :style="{ 
                                         backgroundColor: themeColor.primary, 
                                         width: `${getCapacityPercentage(item)}%` 
                                     }">
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ getCapacityPercentage(item) }}% full
                            </span>
                        </div>
                    </template>
                    
                    <template #actions="{ item }">
                        <div class="flex items-center justify-end space-x-2">
                            <a :href="route('workshop-supervisor.workshops.show', item.id)" 
                               class="text-xs px-3 py-1 rounded-lg border transition-colors"
                               :style="{ borderColor: themeColor.primary, color: themeColor.primary }"
                               @mouseenter="$event.target.style.backgroundColor = `rgba(${themeColor.rgb}, 0.1)`"
                               @mouseleave="$event.target.style.backgroundColor = 'transparent'">
                                View
                            </a>
                            
                            <a v-if="getWorkshopStatus(item).text !== 'Completed'" 
                               :href="route('workshop-supervisor.check-ins.scanner', item.id)" 
                               class="text-xs px-3 py-1 rounded-lg text-white transition-colors"
                               :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                                <QrCodeIcon class="w-4 h-4 inline mr-1" />
                                Scanner
                            </a>
                            
                            <a :href="route('workshop-supervisor.workshops.attendance', item.id)" 
                               class="text-xs px-3 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 transition-colors">
                                Attendance
                            </a>
                        </div>
                    </template>
                </DataTable>

                <!-- Pagination -->
                <div v-if="workshops.links && workshops.links.length > 3" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <nav class="flex items-center justify-between">
                        <div class="flex justify-between flex-1 sm:hidden">
                            <a v-if="workshops.prev_page_url" 
                               :href="workshops.prev_page_url" 
                               class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Previous
                            </a>
                            <a v-if="workshops.next_page_url" 
                               :href="workshops.next_page_url" 
                               class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Next
                            </a>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    Showing {{ workshops.from || 0 }} to {{ workshops.to || 0 }} of {{ workshops.total || 0 }} results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <template v-for="(link, index) in workshops.links" :key="index">
                                        <a v-if="link.url" 
                                           :href="link.url" 
                                           class="relative inline-flex items-center px-2 py-2 text-sm font-medium border transition-colors"
                                           :class="[
                                               link.active 
                                                   ? 'z-10 border-transparent text-white' 
                                                   : 'border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700',
                                               index === 0 ? 'rounded-l-md' : '',
                                               index === workshops.links.length - 1 ? 'rounded-r-md' : ''
                                           ]"
                                           :style="link.active ? { backgroundColor: themeColor.primary, borderColor: themeColor.primary } : {}"
                                           v-html="link.label">
                                        </a>
                                        <span v-else 
                                              class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 border border-gray-300 dark:border-gray-600"
                                              :class="[
                                                  index === 0 ? 'rounded-l-md' : '',
                                                  index === workshops.links.length - 1 ? 'rounded-r-md' : ''
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
    </Default>
</template>

<style scoped>
input[type="text"]:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>