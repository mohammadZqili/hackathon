<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import PageHeader from '../../../Components/Shared/PageHeader.vue'
import DataTable from '../../../Components/Shared/DataTable.vue'
import { ref, computed, onMounted } from 'vue'
import { CalendarIcon, ClockIcon, UserGroupIcon, ChartBarIcon, DocumentArrowDownIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    workshops: Array,
    statistics: Object,
    selectedPeriod: String,
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

const selectedPeriod = ref(props.selectedPeriod || 'all')

const columns = [
    {
        key: 'title',
        label: 'Workshop',
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
        key: 'registered',
        label: 'Registered',
        sortable: true,
        formatter: (value, item) => item.registrations_count || 0
    },
    {
        key: 'checked_in',
        label: 'Checked In',
        sortable: true,
        formatter: (value, item) => item.checked_in_count || 0
    },
    {
        key: 'attendance_rate',
        label: 'Attendance Rate',
        sortable: true,
        slot: 'attendance_rate'
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

const getAttendanceRate = (workshop) => {
    const registered = workshop.registrations_count || 0
    const checkedIn = workshop.checked_in_count || 0
    if (registered === 0) return 0
    return Math.round((checkedIn / registered) * 100)
}

const getWorkshopStatus = (workshop) => {
    const now = new Date()
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

const getAttendanceColor = (rate) => {
    if (rate >= 80) return 'text-green-600 dark:text-green-400'
    if (rate >= 60) return 'text-yellow-600 dark:text-yellow-400'
    return 'text-red-600 dark:text-red-400'
}

const handlePeriodChange = (period) => {
    selectedPeriod.value = period
    router.get(route('workshop-supervisor.reports.index'), { period }, {
        preserveState: true,
        replace: true
    })
}

const exportReport = () => {
    window.location.href = route('workshop-supervisor.reports.export', { period: selectedPeriod.value })
}

const actionButtons = computed(() => [
    {
        text: 'Export Report',
        icon: 'DocumentArrowDownIcon',
        onClick: exportReport,
        primary: true
    }
])
</script>

<template>
    <Head title="Attendance Reports" />
    
    <Default>
        <div class="max-w-7xl mx-auto" :style="themeStyles">
            <PageHeader 
                title="Attendance Reports" 
                description="Track and analyze workshop attendance across all your workshops"
                :action-buttons="actionButtons"
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
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics?.total_workshops || 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900/20">
                            <UserGroupIcon class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Registered</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics?.total_registered || 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-green-100 dark:bg-green-900/20">
                            <ChartBarIcon class="w-6 h-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Attended</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics?.total_attended || 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-purple-100 dark:bg-purple-900/20">
                            <ClockIcon class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Avg. Attendance</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ Math.round(statistics?.average_attendance || 0) }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Filter by Period</h3>
                        <div class="flex space-x-2">
                            <button
                                @click="handlePeriodChange('all')"
                                class="px-3 py-1 rounded-lg text-sm transition-colors"
                                :class="selectedPeriod === 'all' 
                                    ? 'text-white' 
                                    : 'border text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                :style="selectedPeriod === 'all' ? { background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` } : { borderColor: themeColor.primary }">
                                All Time
                            </button>
                            <button
                                @click="handlePeriodChange('current_month')"
                                class="px-3 py-1 rounded-lg text-sm transition-colors"
                                :class="selectedPeriod === 'current_month' 
                                    ? 'text-white' 
                                    : 'border text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                :style="selectedPeriod === 'current_month' ? { background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` } : { borderColor: themeColor.primary }">
                                This Month
                            </button>
                            <button
                                @click="handlePeriodChange('last_month')"
                                class="px-3 py-1 rounded-lg text-sm transition-colors"
                                :class="selectedPeriod === 'last_month' 
                                    ? 'text-white' 
                                    : 'border text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                :style="selectedPeriod === 'last_month' ? { background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` } : { borderColor: themeColor.primary }">
                                Last Month
                            </button>
                            <button
                                @click="handlePeriodChange('last_3_months')"
                                class="px-3 py-1 rounded-lg text-sm transition-colors"
                                :class="selectedPeriod === 'last_3_months' 
                                    ? 'text-white' 
                                    : 'border text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                :style="selectedPeriod === 'last_3_months' ? { background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` } : { borderColor: themeColor.primary }">
                                Last 3 Months
                            </button>
                        </div>
                    </div>
                    
                    <button
                        @click="exportReport"
                        class="flex items-center px-4 py-2 rounded-lg text-white transition-colors"
                        :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                        <DocumentArrowDownIcon class="w-5 h-5 mr-2" />
                        Export Report
                    </button>
                </div>
            </div>

            <!-- Reports Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <DataTable 
                    :data="workshops" 
                    :columns="columns"
                    empty-message="No workshop data available for the selected period."
                >
                    <template #attendance_rate="{ item }">
                        <div class="flex items-center space-x-3">
                            <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-300" 
                                     :style="{ 
                                         backgroundColor: themeColor.primary, 
                                         width: `${getAttendanceRate(item)}%` 
                                     }">
                                </div>
                            </div>
                            <span class="text-sm font-medium" :class="getAttendanceColor(getAttendanceRate(item))">
                                {{ getAttendanceRate(item) }}%
                            </span>
                        </div>
                    </template>
                    
                    <template #status="{ item }">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                              :class="getWorkshopStatus(item).class">
                            {{ getWorkshopStatus(item).text }}
                        </span>
                    </template>
                    
                    <template #actions="{ item }">
                        <div class="flex items-center justify-end space-x-2">
                            <a :href="route('workshop-supervisor.workshops.show', item.id)" 
                               class="text-xs px-3 py-1 rounded-lg border transition-colors"
                               :style="{ borderColor: themeColor.primary, color: themeColor.primary }"
                               @mouseenter="$event.target.style.backgroundColor = `rgba(${themeColor.rgb}, 0.1)`"
                               @mouseleave="$event.target.style.backgroundColor = 'transparent'">
                                View Details
                            </a>
                            
                            <a :href="route('workshop-supervisor.workshops.attendance', item.id)" 
                               class="text-xs px-3 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 transition-colors">
                                Full Report
                            </a>
                        </div>
                    </template>
                </DataTable>
            </div>

            <!-- Summary Charts (placeholder for future implementation) -->
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Attendance Trends</h3>
                    </div>
                    <div class="p-6">
                        <div class="h-64 flex items-center justify-center bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-center">
                                <ChartBarIcon class="mx-auto h-12 w-12 text-gray-400" />
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Chart visualization coming soon</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Top Performing Workshops</h3>
                    </div>
                    <div class="p-6">
                        <div v-if="workshops && workshops.length > 0" class="space-y-4">
                            <div v-for="workshop in workshops.slice(0, 5).sort((a, b) => getAttendanceRate(b) - getAttendanceRate(a))" 
                                 :key="workshop.id" 
                                 class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ workshop.title }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ new Date(workshop.date).toLocaleDateString() }}</p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="h-2 rounded-full transition-all duration-300" 
                                             :style="{ 
                                                 backgroundColor: themeColor.primary, 
                                                 width: `${getAttendanceRate(workshop)}%` 
                                             }">
                                        </div>
                                    </div>
                                    <span class="text-sm font-medium" :class="getAttendanceColor(getAttendanceRate(workshop))">
                                        {{ getAttendanceRate(workshop) }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <ChartBarIcon class="mx-auto h-12 w-12 text-gray-400" />
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No data available</p>
                        </div>
                    </div>
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