<template>
    <Head title="Reports" />
    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ activeTab === 'overall' ? 'Reporting' : 'Hackathon Edition Report' }}
                </h1>
                <p class="text-sm" :style="{ color: themeColor.primary }">
                    {{ activeTab === 'overall' 
                        ? 'Overall statistics across all hackathon editions' 
                        : `Detailed report for ${selectedEditionName}` }}
                </p>
            </div>

            <!-- Tab Navigation -->
            <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button
                        @click="activeTab = 'overall'"
                        :class="[
                            'py-2 px-1 border-b-2 font-medium text-sm transition-colors',
                            activeTab === 'overall'
                                ? 'border-current'
                                : 'border-transparent hover:border-gray-300 dark:hover:border-gray-600'
                        ]"
                        :style="activeTab === 'overall' ? { 
                            color: themeColor.primary,
                            borderColor: themeColor.primary 
                        } : {
                            color: '#6b7280'
                        }">
                        Overall Reports
                    </button>
                    <button
                        @click="activeTab = 'edition'"
                        :class="[
                            'py-2 px-1 border-b-2 font-medium text-sm transition-colors',
                            activeTab === 'edition'
                                ? 'border-current'
                                : 'border-transparent hover:border-gray-300 dark:hover:border-gray-600'
                        ]"
                        :style="activeTab === 'edition' ? { 
                            color: themeColor.primary,
                            borderColor: themeColor.primary 
                        } : {
                            color: '#6b7280'
                        }">
                        Edition Report
                    </button>
                </nav>
            </div>

            <!-- Overall Reports Tab -->
            <div v-show="activeTab === 'overall'">
                <!-- Overall Statistics -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Overall Statistics</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Participating Teams -->
                        <div class="rounded-lg border border-teal-200 dark:border-gray-600 bg-white dark:bg-gray-800 p-6">
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                                Participating Teams
                            </div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ overallStats?.teams?.count || 0 }}
                            </div>
                            <div class="text-xs mt-2" :class="overallStats?.teams?.trend === 'up' ? 'text-green-600' : 'text-red-600'">
                                {{ overallStats?.teams?.trend === 'up' ? '↑' : '↓' }} {{ Math.abs(overallStats?.teams?.growth || 0) }}%
                            </div>
                        </div>

                        <!-- Members -->
                        <div class="rounded-lg border border-teal-200 dark:border-gray-600 bg-white dark:bg-gray-800 p-6">
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                                Members
                            </div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ overallStats?.members?.count || 0 }}
                            </div>
                            <div class="text-xs mt-2" :class="overallStats?.members?.trend === 'up' ? 'text-green-600' : 'text-red-600'">
                                {{ overallStats?.members?.trend === 'up' ? '↑' : '↓' }} {{ Math.abs(overallStats?.members?.growth || 0) }}%
                            </div>
                        </div>

                        <!-- Submitted Ideas -->
                        <div class="rounded-lg border border-teal-200 dark:border-gray-600 bg-white dark:bg-gray-800 p-6">
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                                Ideas
                            </div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ overallStats?.ideas?.count || 0 }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ overallStats?.ideas?.submitted || 0 }} submitted, {{ overallStats?.ideas?.draft || 0 }} draft
                            </div>
                            <div class="text-xs mt-1" :class="overallStats?.ideas?.trend === 'up' ? 'text-green-600' : 'text-red-600'">
                                {{ overallStats?.ideas?.trend === 'up' ? '↑' : '↓' }} {{ Math.abs(overallStats?.ideas?.growth || 0) }}%
                            </div>
                        </div>

                        <!-- Workshops -->
                        <div class="rounded-lg border border-teal-200 dark:border-gray-600 bg-white dark:bg-gray-800 p-6">
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                                Workshops
                            </div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ overallStats?.workshops?.count || 0 }}
                            </div>
                            <div class="text-xs mt-2" :class="overallStats?.workshops?.trend === 'up' ? 'text-green-600' : 'text-red-600'">
                                {{ overallStats?.workshops?.trend === 'up' ? '↑' : '↓' }} {{ Math.abs(overallStats?.workshops?.growth || 0) }}%
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edition Filter Buttons -->
                <div class="mb-6">
                    <div class="flex items-center gap-3 flex-wrap">
                        <button 
                            @click="filterByEdition('all')"
                            :class="[
                                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                                selectedEdition === 'all' 
                                    ? 'text-white' 
                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
                            ]"
                            :style="selectedEdition === 'all' ? {
                                background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                            } : {}">
                            All Editions
                        </button>
                        <button 
                            v-for="edition in editions"
                            :key="edition.id"
                            @click="filterByEdition(edition.id)"
                            :class="[
                                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                                selectedEdition === edition.id 
                                    ? 'text-white' 
                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
                            ]"
                            :style="selectedEdition === edition.id ? {
                                background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                            } : {}">
                            {{ edition.name }}
                        </button>
                    </div>
                </div>

                <!-- Edition-Specific Statistics Table -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Edition-Specific Statistics</h2>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" 
                                            :style="{ color: themeColor.primary }">Edition</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Teams</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Members</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ideas</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Workshops</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Participation</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="edition in editionStats" :key="edition.id" 
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                            :style="{ color: themeColor.primary }">
                                            {{ edition.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ edition.teams }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ edition.members }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ edition.ideas }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ edition.workshops }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ edition.participation_rate }}%
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="[
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                edition.status === 'active' ? 'bg-green-100 text-green-800' :
                                                edition.status === 'upcoming' ? 'bg-blue-100 text-blue-800' :
                                                'bg-gray-100 text-gray-800'
                                            ]">
                                                {{ edition.status }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Workshop Performance -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Workshop Performance</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-2xl font-bold" :style="{ color: themeColor.primary }">
                                        {{ workshopMetrics?.attendance_rate || 0 }}%
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Attendance Rate</div>
                                </div>
                                <div class="text-3xl" :style="{ color: themeColor.primary }">📊</div>
                            </div>
                            <div class="text-xs text-gray-500 mt-2">
                                {{ workshopMetrics?.total_attendances || 0 }} of {{ workshopMetrics?.total_registrations || 0 }} registered
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-2xl font-bold" :style="{ color: themeColor.primary }">
                                        {{ workshopMetrics?.total_hours || 0 }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Hours</div>
                                </div>
                                <div class="text-3xl" :style="{ color: themeColor.primary }">⏱️</div>
                            </div>
                            <div class="text-xs text-gray-500 mt-2">
                                Across {{ workshopMetrics?.total_workshops || 0 }} workshops
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-2xl font-bold" :style="{ color: themeColor.primary }">
                                        {{ workshopMetrics?.avg_satisfaction || 0 }}/5
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Satisfaction Score</div>
                                </div>
                                <div class="text-3xl" :style="{ color: themeColor.primary }">⭐</div>
                            </div>
                            <div class="text-xs text-gray-500 mt-2">
                                {{ workshopMetrics?.capacity_utilization || 0 }}% capacity utilized
                            </div>
                        </div>
                    </div>

                    <!-- Top Workshops -->
                    <div v-if="workshopMetrics?.top_workshops?.length > 0" class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Top Workshops by Attendance</h3>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <div v-for="(workshop, index) in workshopMetrics.top_workshops" :key="workshop.id" 
                                 class="flex items-center justify-between py-2" 
                                 :class="{ 'border-t border-gray-200 dark:border-gray-700': index > 0 }">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-medium" :style="{ color: themeColor.primary }">
                                        #{{ index + 1 }}
                                    </span>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ workshop.title }}</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ workshop.attendances }} attendees
                                    </span>
                                    <span class="text-sm font-medium" :style="{ color: themeColor.primary }">
                                        {{ workshop.utilization }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div v-if="recentActivity?.length > 0" class="mb-8">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Recent Activity</h2>
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <div v-for="(activity, index) in recentActivity" :key="index" 
                             class="flex items-start gap-3 py-3" 
                             :class="{ 'border-t border-gray-200 dark:border-gray-700': index > 0 }">
                            <div class="w-2 h-2 rounded-full mt-2" 
                                 :style="{ backgroundColor: themeColor.primary }"></div>
                            <div class="flex-1">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ activity.message }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ activity.user }} • {{ activity.time }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-4">
                    <button @click="generateReport" 
                            :disabled="loading"
                            class="px-6 py-2 rounded-lg text-white font-medium transition-all hover:shadow-lg disabled:opacity-50"
                            :style="{ 
                                background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                            }">
                        Generate Report
                    </button>
                    <button @click="exportToPDF"
                            :disabled="loading"
                            class="px-6 py-2 rounded-lg border font-medium transition-colors hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50"
                            :style="{ 
                                borderColor: themeColor.primary,
                                color: themeColor.primary
                            }">
                        Export to PDF
                    </button>
                    <button @click="scheduleReports"
                            :disabled="loading"
                            class="px-6 py-2 rounded-lg border font-medium transition-colors hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50"
                            :style="{ 
                                borderColor: themeColor.primary,
                                color: themeColor.primary
                            }">
                        Schedule Reports
                    </button>
                </div>
            </div>

            <!-- Edition Report Tab -->
            <div v-show="activeTab === 'edition'">
                <!-- Edition Selector -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Select Edition
                    </label>
                    <select v-model="selectedEditionForReport" 
                            @change="loadEditionReport"
                            class="w-full max-w-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option v-for="edition in editions" :key="edition.id" :value="edition.id">
                            {{ edition.name }}
                        </option>
                    </select>
                </div>

                <!-- Edition Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Teams Formed</div>
                        <div class="text-2xl font-bold" :style="{ color: themeColor.primary }">
                            {{ currentEditionStats?.teams || 0 }}
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Total Members</div>
                        <div class="text-2xl font-bold" :style="{ color: themeColor.primary }">
                            {{ currentEditionStats?.members || 0 }}
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Ideas Submitted</div>
                        <div class="text-2xl font-bold" :style="{ color: themeColor.primary }">
                            {{ currentEditionStats?.ideas || 0 }}
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Participation Rate</div>
                        <div class="text-2xl font-bold" :style="{ color: themeColor.primary }">
                            {{ currentEditionStats?.participation_rate || 0 }}%
                        </div>
                    </div>
                </div>

                <!-- Charts Placeholder -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Participation Trends</h3>
                        <div class="h-64 flex items-center justify-center text-gray-400">
                            <div class="text-center">
                                <div class="text-4xl mb-2">📈</div>
                                <div>Chart will be rendered here</div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Workshop Analytics</h3>
                        <div class="h-64 flex items-center justify-center text-gray-400">
                            <div class="text-center">
                                <div class="text-4xl mb-2">📊</div>
                                <div>Chart will be rendered here</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Download Report Button -->
                <div>
                    <button @click="downloadEditionReport"
                            :disabled="loading"
                            class="px-6 py-2 rounded-lg text-white font-medium transition-all hover:shadow-lg disabled:opacity-50"
                            :style="{ 
                                background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
                            }">
                        Download Edition Report
                    </button>
                </div>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import axios from 'axios'

const props = defineProps({
    overallStats: Object,
    editions: Array,
    editionStats: Array,
    workshopMetrics: Object,
    recentActivity: Array,
    selectedEditionId: [String, Number]
})

const activeTab = ref('overall')
const selectedEdition = ref(props.selectedEditionId || 'all')
const selectedEditionForReport = ref(props.editions?.[0]?.id || null)
const selectedEditionName = ref('All Editions')
const loading = ref(false)

// Current edition stats for the Edition Report tab
const currentEditionStats = computed(() => {
    const edition = props.editionStats?.find(e => e.id == selectedEditionForReport.value)
    return edition || {}
})

// Theme color configuration
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

    // Set initial selected edition name
    if (selectedEdition.value && selectedEdition.value !== 'all') {
        const edition = props.editions?.find(e => e.id == selectedEdition.value)
        if (edition) {
            selectedEditionName.value = edition.name
        }
    }
})

const themeStyles = computed(() => ({
    '--theme-primary': themeColor.value.primary,
    '--theme-hover': themeColor.value.hover,
    '--theme-rgb': themeColor.value.rgb,
    '--theme-gradient-from': themeColor.value.gradientFrom,
    '--theme-gradient-to': themeColor.value.gradientTo,
}))

const filterByEdition = (editionId) => {
    selectedEdition.value = editionId
    loading.value = true
    
    if (editionId === 'all') {
        selectedEditionName.value = 'All Editions'
        router.visit('/system-admin/reports', {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => loading.value = false
        })
    } else {
        const edition = props.editions?.find(e => e.id == editionId)
        selectedEditionName.value = edition?.name || 'Unknown Edition'
        router.visit(`/system-admin/reports?edition_id=${editionId}`, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => loading.value = false
        })
    }
}

const generateReport = async () => {
    loading.value = true
    try {
        const response = await axios.post('/system-admin/reports/generate', {
            edition_id: selectedEdition.value !== 'all' ? selectedEdition.value : null
        })
        console.log('Report generated:', response.data.message)
        alert(response.data.message)
    } catch (error) {
        console.error('Error generating report:', error)
        alert('Error generating report')
    } finally {
        loading.value = false
    }
}

const exportToPDF = async () => {
    loading.value = true
    try {
        const response = await axios.post('/system-admin/reports/export-pdf', {
            edition_id: selectedEdition.value !== 'all' ? selectedEdition.value : null
        })
        console.log('PDF export initiated:', response.data.message)
        alert(response.data.message)
    } catch (error) {
        console.error('Error exporting to PDF:', error)
        alert('Error exporting to PDF')
    } finally {
        loading.value = false
    }
}

const scheduleReports = async () => {
    loading.value = true
    try {
        const response = await axios.post('/system-admin/reports/schedule', {
            frequency: 'weekly',
            edition_id: selectedEdition.value !== 'all' ? selectedEdition.value : null
        })
        console.log('Reports scheduled:', response.data.message)
        alert(response.data.message)
    } catch (error) {
        console.error('Error scheduling reports:', error)
        alert('Error scheduling reports')
    } finally {
        loading.value = false
    }
}

const loadEditionReport = () => {
    // This would load specific edition data if needed
    console.log('Loading edition report for:', selectedEditionForReport.value)
}

const downloadEditionReport = async () => {
    loading.value = true
    try {
        const response = await axios.post('/system-admin/reports/export-pdf', {
            edition_id: selectedEditionForReport.value
        })
        console.log('Edition report download initiated:', response.data.message)
        alert(response.data.message)
    } catch (error) {
        console.error('Error downloading edition report:', error)
        alert('Error downloading edition report')
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
/* Custom focus styles for inputs */
input[type="text"]:focus,
input[type="number"]:focus,
input[type="date"]:focus,
select:focus,
textarea:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}

/* Custom checkbox styles */
input[type="checkbox"]:checked {
    background-color: var(--theme-primary) !important;
    border-color: var(--theme-primary) !important;
}

/* Custom scrollbar for tables */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: var(--theme-primary);
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: var(--theme-hover);
}
</style>