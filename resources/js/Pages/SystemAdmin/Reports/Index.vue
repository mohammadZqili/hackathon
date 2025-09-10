<template>
    <div class="flex flex-col gap-4">
        <!-- Page Header -->
        <div class="p-4">
            <h1 class="text-[32px] font-bold text-gray-900">Reporting</h1>
            <p class="text-sm text-gray-600 mt-1">
                Overall statistics across all hackathon editions
            </p>
        </div>

        <!-- Overall Statistics Section -->
        <div class="px-4">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Overall Statistics</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-2">Participating Teams</p>
                        <p class="text-3xl font-bold text-gray-900">{{ reportSummary.total_teams || 120 }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-2">Members</p>
                        <p class="text-3xl font-bold text-gray-900">{{ reportSummary.total_users || 480 }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-2">Submitted Ideas</p>
                        <p class="text-3xl font-bold text-gray-900">{{ reportSummary.total_ideas || 360 }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-2">Workshops</p>
                        <p class="text-3xl font-bold text-gray-900">{{ reportSummary.total_workshops || 15 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edition-Specific Statistics Section -->
        <div class="px-4">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Edition-Specific Statistics</h2>
            
            <!-- Filter Controls -->
            <div class="flex flex-wrap gap-4 mb-6">
                <select v-model="selectedEdition" 
                        @change="loadEditionData"
                        class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="all">All Editions</option>
                    <option value="summer2023">Summer 2023</option>
                    <option value="winter2023">Winter 2023</option>
                    <option value="spring2024">Spring 2024</option>
                </select>
                
                <select v-model="selectedSeason"
                        @change="loadEditionData"
                        class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="all">All Seasons</option>
                    <option value="summer">Summer</option>
                    <option value="winter">Winter</option>
                    <option value="spring">Spring</option>
                    <option value="fall">Fall</option>
                </select>
                
                <select v-model="selectedYear"
                        @change="loadEditionData"
                        class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="all">All Years</option>
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>
                </select>
                
                <button @click="downloadReport"
                        class="ml-auto px-4 py-2 rounded-lg bg-emerald-100 text-emerald-700 text-sm font-medium hover:bg-emerald-200 transition-colors">
                    Download Data (Excel)
                </button>
            </div>
            
            <!-- Statistics Table -->
            <div class="rounded-xl bg-white border border-gray-200 overflow-hidden shadow-sm">
                <table class="w-full">
                    <thead class="bg-emerald-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left py-3 px-6 text-sm font-medium text-gray-700">Edition</th>
                            <th class="text-center py-3 px-6 text-sm font-medium text-gray-700">Teams</th>
                            <th class="text-center py-3 px-6 text-sm font-medium text-gray-700">Members</th>
                            <th class="text-center py-3 px-6 text-sm font-medium text-gray-700">Ideas</th>
                            <th class="text-center py-3 px-6 text-sm font-medium text-gray-700">Status</th>
                            <th class="text-center py-3 px-6 text-sm font-medium text-gray-700">Workshop Attendance</th>
                            <th class="text-center py-3 px-6 text-sm font-medium text-gray-700">Registrations</th>
                            <th class="text-center py-3 px-6 text-sm font-medium text-gray-700">Website Visitors</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="edition in editionStats" :key="edition.id" class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-6 text-sm text-gray-900">{{ edition.name }}</td>
                            <td class="py-4 px-6 text-sm text-center text-emerald-600">{{ edition.teams }}</td>
                            <td class="py-4 px-6 text-sm text-center text-emerald-600">{{ edition.members }}</td>
                            <td class="py-4 px-6 text-sm text-center text-emerald-600">{{ edition.ideas }}</td>
                            <td class="py-4 px-6 text-sm text-center">
                                <span :class="getStatusClass(edition.status)"
                                      class="px-2 py-1 rounded-full text-xs font-medium">
                                    {{ edition.status }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-sm text-center text-emerald-600">{{ edition.workshopAttendance }}%</td>
                            <td class="py-4 px-6 text-sm text-center text-emerald-600">{{ edition.registrations }}</td>
                            <td class="py-4 px-6 text-sm text-center text-emerald-600">{{ edition.visitors }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Report Links -->
        <div class="px-4 mt-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Detailed Reports</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Users Report -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-gray-900">Users Report</h3>
                    </div>
                    <p class="text-gray-600 mb-4 text-sm">
                        Detailed analysis of user registrations, roles, and activity patterns.
                    </p>
                    <Link :href="route('system-admin.reports.users')" 
                          class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium text-sm">
                        View Report
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>
                </div>

                <!-- Teams Report -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="bg-green-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-gray-900">Teams Report</h3>
                    </div>
                    <p class="text-gray-600 mb-4 text-sm">
                        Team formation statistics, member distribution, and performance metrics.
                    </p>
                    <Link :href="route('system-admin.reports.teams')" 
                          class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium text-sm">
                        View Report
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>
                </div>

                <!-- Ideas Report -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="bg-yellow-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-gray-900">Ideas Report</h3>
                    </div>
                    <p class="text-gray-600 mb-4 text-sm">
                        Idea submission patterns, review status, and scoring analytics.
                    </p>
                    <Link :href="route('system-admin.reports.ideas')" 
                          class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium text-sm">
                        View Report
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>
                </div>

                <!-- Workshops Report -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="bg-purple-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-6m-2-5h4m-4 0V9a1 1 0 011-1h2a1 1 0 011 1v7m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-gray-900">Workshops Report</h3>
                    </div>
                    <p class="text-gray-600 mb-4 text-sm">
                        Workshop attendance, engagement rates, and feedback analysis.
                    </p>
                    <Link :href="route('system-admin.reports.workshops')" 
                          class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium text-sm">
                        View Report
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>
                </div>

                <!-- System Health -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="bg-red-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-gray-900">System Health</h3>
                    </div>
                    <p class="text-gray-600 mb-4 text-sm">
                        Server performance, database status, and system monitoring.
                    </p>
                    <Link :href="route('system-admin.reports.system-health')" 
                          class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium text-sm">
                        View Report
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>
                </div>

                <!-- Edition Comparison -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="bg-indigo-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-gray-900">Edition Comparison</h3>
                    </div>
                    <p class="text-gray-600 mb-4 text-sm">
                        Compare metrics across different hackathon editions.
                    </p>
                    <Link :href="route('system-admin.reports.editions')" 
                          class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium text-sm">
                        View Report
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
    reportSummary: {
        type: Object,
        required: true
    }
})

const selectedEdition = ref('all')
const selectedSeason = ref('all')
const selectedYear = ref('all')

// Sample edition statistics data
const editionStats = ref([
    {
        id: 1,
        name: 'Summer 2023',
        teams: 40,
        members: 160,
        ideas: 120,
        status: 'Completed',
        workshopAttendance: 80,
        registrations: 200,
        visitors: 5000
    },
    {
        id: 2,
        name: 'Winter 2023',
        teams: 50,
        members: 200,
        ideas: 150,
        status: 'Completed',
        workshopAttendance: 75,
        registrations: 250,
        visitors: 6000
    },
    {
        id: 3,
        name: 'Spring 2024',
        teams: 30,
        members: 120,
        ideas: 90,
        status: 'Ongoing',
        workshopAttendance: 60,
        registrations: 150,
        visitors: 4000
    }
])

const getStatusClass = (status) => {
    const classes = {
        'Completed': 'bg-green-100 text-green-800',
        'Ongoing': 'bg-yellow-100 text-yellow-800',
        'Upcoming': 'bg-blue-100 text-blue-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const loadEditionData = () => {
    // Load filtered data based on selections
    router.get(route('system-admin.reports.index'), {
        edition: selectedEdition.value,
        season: selectedSeason.value,
        year: selectedYear.value
    }, {
        preserveState: true,
        preserveScroll: true
    })
}

const downloadReport = () => {
    // Trigger report download
    window.location.href = route('system-admin.reports.export', {
        edition: selectedEdition.value,
        season: selectedSeason.value,
        year: selectedYear.value
    })
}
</script>
