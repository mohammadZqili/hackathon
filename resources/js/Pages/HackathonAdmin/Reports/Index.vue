<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'
import VueApexCharts from 'vue3-apexcharts'
import { ChevronDownIcon, ArrowDownTrayIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    overallStats: Object,
    editions: Array,
    editionStats: Array,
    editionReport: Object,
    selectedEdition: Object,
    recentActivity: Object,
    selectedEditionId: [Number, String],
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

    themeColor.value = {
        primary: primary || themeColor.value.primary,
        hover: hover || themeColor.value.hover,
        rgb: rgb || themeColor.value.rgb,
        gradientFrom: primary || themeColor.value.gradientFrom,
        gradientTo: hover || themeColor.value.gradientTo
    }
})

// View state management
const activeView = ref('overall') // 'overall' or 'edition'
const selectedEditionFilter = ref('all')
const selectedSeason = ref('all')
const selectedYear = ref('all')
const selectedEditionId = ref(props.selectedEditionId || null)
const selectedEdition = ref(props.selectedEdition || null)
const editionReport = ref(props.editionReport || null)
const loadingEditionReport = ref(false)

// Filter options
const seasonOptions = ['All Editions', 'Summer 2023', 'Winter 2023', 'Spring 2024']
const yearOptions = ['All Years', '2023', '2024']

// Handle edition selection for detailed report
const viewEditionReport = (editionId) => {
    // Find the selected edition from the list
    const edition = props.editions?.find(e => e.id === editionId)
    if (edition) {
        // Set loading state and switch view
        loadingEditionReport.value = true
        selectedEditionId.value = editionId
        selectedEdition.value = edition
        activeView.value = 'edition'

        // Load the edition report data via AJAX
        router.get(route('hackathon-admin.reports.index', { edition_id: editionId }), {}, {
            preserveState: true,
            preserveScroll: true,
            only: ['editionReport', 'selectedEdition', 'selectedEditionId'],
            onSuccess: (page) => {
                // Update local state with the loaded data
                editionReport.value = page.props.editionReport
                selectedEdition.value = page.props.selectedEdition
                loadingEditionReport.value = false
            },
            onError: () => {
                loadingEditionReport.value = false
                activeView.value = 'overall'
            }
        })
    }
}

// Export data - updated to use the correct route and method
const exportData = () => {
    // Determine which data to export based on current view
    let url = route('hackathon-admin.reports.export')
    let params = {}

    if (activeView.value === 'edition' && selectedEditionId.value) {
        // Export specific edition report
        params.edition_id = selectedEditionId.value
    } else if (selectedEditionFilter.value !== 'all') {
        // Export filtered edition
        params.edition_id = selectedEditionFilter.value
    }
    // else export overall statistics (no edition_id param)

    // Create a form and submit it to trigger download
    const form = document.createElement('form')
    form.method = 'GET'
    form.action = url
    form.style.display = 'none'

    // Add parameters as hidden inputs
    Object.keys(params).forEach(key => {
        const input = document.createElement('input')
        input.type = 'hidden'
        input.name = key
        input.value = params[key]
        form.appendChild(input)
    })

    document.body.appendChild(form)
    form.submit()
    document.body.removeChild(form)
}

// Idea Status Chart Configuration
const ideaStatusChart = computed(() => {
    if (!editionReport.value?.idea_status) return null

    const categories = editionReport.value.idea_status.map(item => item.status)
    const data = editionReport.value.idea_status.map(item => item.count)

    return {
        series: [{
            name: 'Ideas',
            data: data
        }],
        options: {
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: false }
            },
            plotOptions: {
                bar: {
                    borderRadius: 8,
                    horizontal: false,
                    columnWidth: '60%',
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: categories,
                labels: {
                    style: {
                        colors: '#6B7280',
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#6B7280',
                        fontSize: '12px'
                    }
                }
            },
            fill: {
                colors: [themeColor.value.primary]
            },
            grid: {
                borderColor: '#E5E7EB',
                strokeDashArray: 4
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: (val) => `${val} ideas`
                }
            }
        }
    }
})

// Registrations Trend Chart Configuration
const registrationsTrendChart = computed(() => {
    if (!editionReport.value?.registrations_trend) return null

    const categories = editionReport.value.registrations_trend.map(item => item.workshop)
    const data = editionReport.value.registrations_trend.map(item => item.registrations)

    return {
        series: [{
            name: 'Registrations',
            data: data
        }],
        options: {
            chart: {
                type: 'area',
                height: 300,
                toolbar: { show: false }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3,
                colors: [themeColor.value.primary]
            },
            xaxis: {
                categories: categories,
                labels: {
                    style: {
                        colors: '#6B7280',
                        fontSize: '11px'
                    },
                    rotate: -45,
                    rotateAlways: true
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#6B7280',
                        fontSize: '12px'
                    }
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.1,
                    stops: [0, 90, 100],
                    colorStops: [{
                        offset: 0,
                        color: themeColor.value.primary,
                        opacity: 0.4
                    }, {
                        offset: 100,
                        color: themeColor.value.primary,
                        opacity: 0.1
                    }]
                }
            },
            grid: {
                borderColor: '#E5E7EB',
                strokeDashArray: 4
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: (val) => `${val} registrations`
                }
            }
        }
    }
})

// Download Excel data
const downloadExcel = () => {
    // Use exportData function for consistency
    exportData()
}

// Back to overall view
const backToOverall = () => {
    activeView.value = 'overall'
    selectedEditionId.value = null
    selectedEdition.value = null
    // Clear the URL parameters without reloading
    window.history.replaceState({}, document.title, route('hackathon-admin.reports.index'))
}

// Initialize view on mount
onMounted(() => {
    if (props.selectedEditionId && props.editionReport) {
        selectedEditionId.value = props.selectedEditionId
        selectedEdition.value = props.selectedEdition
        editionReport.value = props.editionReport
        activeView.value = 'edition'
    }
})
</script>

<template>
    <Head title="Reports" />

    <Default>
        <div class="max-w-7xl mx-auto">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ activeView === 'overall' ? 'Reporting' : 'Hackathon Edition Report' }}
                </h1>
                <p class="text-sm mt-1" :style="{ color: themeColor.primary }">
                    {{ activeView === 'overall'
                        ? 'Overall statistics across all hackathon editions'
                        : `View detailed statistics for the ${selectedEdition?.name || 'selected'} hackathon edition.` }}
                </p>
            </div>

            <!-- Overall View -->
            <div v-if="activeView === 'overall' || !selectedEditionId">
                <!-- Overall Statistics -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-5">Overall Statistics</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="rounded-xl border p-6"
                             :style="{ borderColor: `rgba(${themeColor.rgb}, 0.2)` }">
                            <div class="text-base font-medium text-gray-600 dark:text-gray-400">
                                Participating Teams
                            </div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                                {{ overallStats?.teams || 0 }}
                            </div>
                        </div>

                        <div class="rounded-xl border p-6"
                             :style="{ borderColor: `rgba(${themeColor.rgb}, 0.2)` }">
                            <div class="text-base font-medium text-gray-600 dark:text-gray-400">
                                Members
                            </div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                                {{ overallStats?.members || 0 }}
                            </div>
                        </div>

                        <div class="rounded-xl border p-6"
                             :style="{ borderColor: `rgba(${themeColor.rgb}, 0.2)` }">
                            <div class="text-base font-medium text-gray-600 dark:text-gray-400">
                                Submitted Ideas
                            </div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                                {{ overallStats?.ideas || 0 }}
                            </div>
                        </div>

                        <div class="rounded-xl border p-6"
                             :style="{ borderColor: `rgba(${themeColor.rgb}, 0.2)` }">
                            <div class="text-base font-medium text-gray-600 dark:text-gray-400">
                                Workshops
                            </div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                                {{ overallStats?.workshops || 0 }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edition-Specific Statistics -->
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-5">Edition-Specific Statistics</h2>

                    <!-- Filters -->
                    <div class="flex flex-wrap gap-3 mb-4">
                        <!-- All Editions Dropdown -->
                        <div class="relative">
                            <button class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-colors"
                                    :style="{
                                        backgroundColor: `rgba(${themeColor.rgb}, 0.1)`,
                                        color: themeColor.primary
                                    }">
                                All Editions
                                <ChevronDownIcon class="w-5 h-5" />
                            </button>
                        </div>

                        <!-- Season Filter -->
                        <div class="relative">
                            <button class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-colors"
                                    :style="{
                                        backgroundColor: `rgba(${themeColor.rgb}, 0.1)`,
                                        color: themeColor.primary
                                    }">
                                Summer 2023
                                <ChevronDownIcon class="w-5 h-5" />
                            </button>
                        </div>

                        <!-- Year Filter -->
                        <div class="relative">
                            <button class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-colors"
                                    :style="{
                                        backgroundColor: `rgba(${themeColor.rgb}, 0.1)`,
                                        color: themeColor.primary
                                    }">
                                Winter 2023
                                <ChevronDownIcon class="w-5 h-5" />
                            </button>
                        </div>

                        <!-- Download Button -->
                        <button @click="downloadExcel"
                                class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-colors"
                                :style="{
                                    backgroundColor: `rgba(${themeColor.rgb}, 0.1)`,
                                    color: themeColor.primary
                                }">
                            Download Data (Excel)
                        </button>
                    </div>

                    <!-- Table -->
                    <div class="rounded-xl border overflow-hidden"
                         :style="{
                             borderColor: `rgba(${themeColor.rgb}, 0.2)`,
                             backgroundColor: `rgba(${themeColor.rgb}, 0.02)`
                         }">
                        <table class="w-full">
                            <thead>
                                <tr :style="{ backgroundColor: `rgba(${themeColor.rgb}, 0.05)` }">
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Edition</th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-700 dark:text-gray-300">Teams</th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-700 dark:text-gray-300">Members</th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-700 dark:text-gray-300">Ideas</th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-700 dark:text-gray-300">Status</th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-700 dark:text-gray-300">Workshop Attendance</th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-700 dark:text-gray-300">Registrations</th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-700 dark:text-gray-300">Website Visitors</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="edition in editionStats" :key="edition.id"
                                    class="border-t hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition-colors"
                                    :style="{ borderColor: '#E5E7EB' }"
                                    @click="viewEditionReport(edition.id)">
                                    <td class="px-4 py-4 text-sm text-gray-900 dark:text-white">{{ edition.name }}</td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">{{ edition.teams }}</td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">{{ edition.members }}</td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">{{ edition.ideas }}</td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex px-3 py-1 text-xs font-medium rounded-xl"
                                              :style="{
                                                  backgroundColor: `rgba(${themeColor.rgb}, 0.1)`,
                                                  color: edition.status === 'Completed' ? themeColor.primary :
                                                        edition.status === 'Ongoing' ? '#10B981' : '#6B7280'
                                              }">
                                            {{ edition.status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">{{ edition.workshop_attendance }}</td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">{{ edition.registrations }}</td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">{{ edition.website_visitors }}</td>
                                </tr>

                                <!-- Example static rows for demo - remove in production -->
                                <tr v-if="!editionStats || editionStats.length === 0"
                                    class="border-t hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition-colors"
                                    :style="{ borderColor: '#E5E7EB' }">
                                    <td class="px-4 py-4 text-sm text-gray-900 dark:text-white">Summer 2023</td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">40</td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">160</td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">120</td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex px-3 py-1 text-xs font-medium rounded-xl"
                                              :style="{
                                                  backgroundColor: `rgba(${themeColor.rgb}, 0.1)`,
                                                  color: themeColor.primary
                                              }">
                                            Completed
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">80%</td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">200</td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">5000</td>
                                </tr>

                                <tr v-if="!editionStats || editionStats.length === 0"
                                    class="border-t hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition-colors"
                                    :style="{ borderColor: '#E5E7EB' }">
                                    <td class="px-4 py-4 text-sm text-gray-900 dark:text-white">Winter 2023</td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">50</td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">200</td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">150</td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex px-3 py-1 text-xs font-medium rounded-xl"
                                              :style="{
                                                  backgroundColor: `rgba(${themeColor.rgb}, 0.1)`,
                                                  color: themeColor.primary
                                              }">
                                            Completed
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">75%</td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">250</td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">6000</td>
                                </tr>

                                <tr v-if="!editionStats || editionStats.length === 0"
                                    class="border-t hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition-colors"
                                    :style="{ borderColor: '#E5E7EB' }">
                                    <td class="px-4 py-4 text-sm text-gray-900 dark:text-white">Spring 2024</td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">30</td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">120</td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">90</td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex px-3 py-1 text-xs font-medium rounded-xl"
                                              :style="{
                                                  backgroundColor: `rgba(${themeColor.rgb}, 0.1)`,
                                                  color: '#10B981'
                                              }">
                                            Ongoing
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">60%</td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">150</td>
                                    <td class="px-4 py-4 text-sm text-center" :style="{ color: themeColor.primary }">4000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Edition Report View -->
            <div v-else-if="activeView === 'edition'">
                <!-- Back Button and Download -->
                <div class="flex justify-between items-center mb-6">
                    <button @click="backToOverall"
                            class="text-sm font-medium"
                            :style="{ color: themeColor.primary }">
                        ← Back to Overall Reports
                    </button>
                    <button @click="downloadExcel"
                            class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-colors"
                            :style="{
                                backgroundColor: `rgba(${themeColor.rgb}, 0.1)`,
                                color: themeColor.primary
                            }">
                        Download Data (Excel)
                    </button>
                </div>

                <!-- Overview Statistics -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-5">Overview</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="rounded-xl border p-6"
                             :style="{ borderColor: `rgba(${themeColor.rgb}, 0.2)` }">
                            <div class="text-base font-medium text-gray-600 dark:text-gray-400">
                                Participating Teams
                            </div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                                {{ editionReport?.overview?.teams || 0 }}
                            </div>
                        </div>

                        <div class="rounded-xl border p-6"
                             :style="{ borderColor: `rgba(${themeColor.rgb}, 0.2)` }">
                            <div class="text-base font-medium text-gray-600 dark:text-gray-400">
                                Team Members
                            </div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                                {{ editionReport?.overview?.members || 0 }}
                            </div>
                        </div>

                        <div class="rounded-xl border p-6"
                             :style="{ borderColor: `rgba(${themeColor.rgb}, 0.2)` }">
                            <div class="text-base font-medium text-gray-600 dark:text-gray-400">
                                Submitted Ideas
                            </div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                                {{ editionReport?.overview?.ideas || 0 }}
                            </div>
                        </div>

                        <div class="rounded-xl border p-6"
                             :style="{ borderColor: `rgba(${themeColor.rgb}, 0.2)` }">
                            <div class="text-base font-medium text-gray-600 dark:text-gray-400">
                                Workshop Visitors
                            </div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                                {{ editionReport?.overview?.workshops || 0 }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Idea Status Chart -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-5">Idea Status</h2>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4">
                        <div class="flex justify-center">
                            <VueApexCharts
                                v-if="ideaStatusChart"
                                type="bar"
                                height="300"
                                :options="ideaStatusChart.options"
                                :series="ideaStatusChart.series" />

                            <!-- Fallback static visualization -->
                            <div v-else class="flex items-end gap-4 h-[300px] w-full max-w-2xl">
                                <div class="flex-1 flex flex-col items-center gap-2">
                                    <div class="w-full rounded-t-lg transition-all"
                                         :style="{
                                             height: '120px',
                                             backgroundColor: themeColor.primary
                                         }"></div>
                                    <span class="text-xs text-gray-600">Submitted</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-2">
                                    <div class="w-full rounded-t-lg transition-all"
                                         :style="{
                                             height: '80px',
                                             backgroundColor: themeColor.primary
                                         }"></div>
                                    <span class="text-xs text-gray-600">In Review</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-2">
                                    <div class="w-full rounded-t-lg transition-all"
                                         :style="{
                                             height: '140px',
                                             backgroundColor: themeColor.primary
                                         }"></div>
                                    <span class="text-xs text-gray-600">Accepted</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-2">
                                    <div class="w-full rounded-t-lg transition-all"
                                         :style="{
                                             height: '60px',
                                             backgroundColor: themeColor.primary
                                         }"></div>
                                    <span class="text-xs text-gray-600">Rejected</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-2">
                                    <div class="w-full rounded-t-lg transition-all"
                                         :style="{
                                             height: '100px',
                                             backgroundColor: themeColor.primary
                                         }"></div>
                                    <span class="text-xs text-gray-600">Completed</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Workshop Statistics -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-5">Workshop Statistics</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="rounded-xl border p-6"
                             :style="{ borderColor: `rgba(${themeColor.rgb}, 0.2)` }">
                            <div class="text-base font-medium text-gray-600 dark:text-gray-400">
                                Total Workshops
                            </div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                                {{ editionReport?.workshop_stats?.total_workshops || 8 }}
                            </div>
                        </div>

                        <div class="rounded-xl border p-6"
                             :style="{ borderColor: `rgba(${themeColor.rgb}, 0.2)` }">
                            <div class="text-base font-medium text-gray-600 dark:text-gray-400">
                                Total Speakers
                            </div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                                {{ editionReport?.workshop_stats?.total_speakers || 65 }}
                            </div>
                        </div>

                        <div class="rounded-xl border p-6"
                             :style="{ borderColor: `rgba(${themeColor.rgb}, 0.2)` }">
                            <div class="text-base font-medium text-gray-600 dark:text-gray-400">
                                Average Attendance
                            </div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                                {{ editionReport?.workshop_stats?.avg_attendance || 65 }}
                            </div>
                        </div>

                        <div class="rounded-xl border p-6"
                             :style="{ borderColor: `rgba(${themeColor.rgb}, 0.2)` }">
                            <div class="text-base font-medium text-gray-600 dark:text-gray-400">
                                Total Organizations
                            </div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                                {{ editionReport?.workshop_stats?.total_organizations || 65 }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registrations Chart -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-5">Registrations</h2>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4">
                        <h3 class="text-base font-medium text-gray-700 dark:text-gray-300 mb-4">Registrations Over Workshops</h3>
                        <VueApexCharts
                            v-if="registrationsTrendChart"
                            type="area"
                            height="300"
                            :options="registrationsTrendChart.options"
                            :series="registrationsTrendChart.series" />

                        <!-- Fallback static line -->
                        <div v-else class="h-[300px] flex items-center justify-center">
                            <svg class="w-full h-full max-w-3xl" viewBox="0 0 800 300">
                                <path d="M 50 250 Q 150 200 250 180 T 450 150 T 650 100 T 750 80"
                                      :stroke="themeColor.primary"
                                      stroke-width="3"
                                      fill="none" />
                                <path d="M 50 250 Q 150 200 250 180 T 450 150 T 650 100 T 750 80 L 750 300 L 50 300 Z"
                                      :fill="themeColor.primary"
                                      fill-opacity="0.1" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Team Performance Table -->
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-5">Team Performance</h2>
                    <div class="rounded-xl border overflow-hidden"
                         :style="{
                             borderColor: `rgba(${themeColor.rgb}, 0.2)`,
                             backgroundColor: 'white'
                         }">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800">
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Team Name</th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-700 dark:text-gray-300">Members</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Idea Title</th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-700 dark:text-gray-300">Status</th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-700 dark:text-gray-300">Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="team in (editionReport?.team_performance || [])" :key="team.team_name"
                                    class="border-t hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                                    :style="{ borderColor: '#E5E7EB' }">
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ team.team_name }}</td>
                                    <td class="px-4 py-3 text-sm text-center text-gray-600 dark:text-gray-400">{{ team.members }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ team.idea_title }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded"
                                              :class="{
                                                  'bg-green-100 text-green-800': team.status === 'Accepted' || team.status === 'Completed',
                                                  'bg-yellow-100 text-yellow-800': team.status === 'In Review',
                                                  'bg-red-100 text-red-800': team.status === 'Rejected',
                                                  'bg-gray-100 text-gray-800': team.status === 'Pending' || team.status === 'Submitted'
                                              }">
                                            {{ team.status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center font-medium" :style="{ color: themeColor.primary }">
                                        {{ team.score }}
                                    </td>
                                </tr>

                                <!-- Example static rows if no data -->
                                <template v-if="!editionReport?.team_performance || editionReport.team_performance.length === 0">
                                    <tr class="border-t hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                                        :style="{ borderColor: '#E5E7EB' }">
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">Team Alpha</td>
                                        <td class="px-4 py-3 text-sm text-center text-gray-600 dark:text-gray-400">4</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">Smart City Solutions</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800">
                                                Accepted
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center font-medium" :style="{ color: themeColor.primary }">88</td>
                                    </tr>

                                    <tr class="border-t hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                                        :style="{ borderColor: '#E5E7EB' }">
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">Team Beta</td>
                                        <td class="px-4 py-3 text-sm text-center text-gray-600 dark:text-gray-400">3</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">AI-Powered Healthcare</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800">
                                                Completed
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center font-medium" :style="{ color: themeColor.primary }">92</td>
                                    </tr>

                                    <tr class="border-t hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                                        :style="{ borderColor: '#E5E7EB' }">
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">Team Gamma</td>
                                        <td class="px-4 py-3 text-sm text-center text-gray-600 dark:text-gray-400">5</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">Sustainable Energy Innovations</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded bg-yellow-100 text-yellow-800">
                                                In Review
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center font-medium" :style="{ color: themeColor.primary }">78</td>
                                    </tr>

                                    <tr class="border-t hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                                        :style="{ borderColor: '#E5E7EB' }">
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">Team Delta</td>
                                        <td class="px-4 py-3 text-sm text-center text-gray-600 dark:text-gray-400">4</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">Future of Education</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded bg-red-100 text-red-800">
                                                Rejected
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center font-medium" :style="{ color: themeColor.primary }">60</td>
                                    </tr>

                                    <tr class="border-t hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                                        :style="{ borderColor: '#E5E7EB' }">
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">Team Epsilon</td>
                                        <td class="px-4 py-3 text-sm text-center text-gray-600 dark:text-gray-400">3</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">FinTech Disruptors</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800">
                                                Accepted
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center font-medium" :style="{ color: themeColor.primary }">85</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>