<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Default from '../../../Layouts/Default.vue'
import { 
    UsersIcon, 
    LightBulbIcon, 
    AcademicCapIcon, 
    NewspaperIcon,
    ClockIcon,
    CheckCircleIcon,
    XCircleIcon,
    ExclamationCircleIcon 
} from '@heroicons/vue/24/outline'

const props = defineProps({
    currentEdition: Object,
    statistics: Object,
    recentTeams: Array,
    recentIdeas: Array,
    upcomingWorkshops: Array,
    teamsByTrack: Array,
})

const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    approved: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    active: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>

<template>
    <Head title="Hackathon Admin Dashboard" />

    <Default>
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Hackathon Dashboard</h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            {{ currentEdition?.name }} - {{ currentEdition?.year }}
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <a :href="route('hackathon-admin.teams.index')" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            <UsersIcon class="w-5 h-5 mr-2" />
                            Manage Teams
                        </a>
                        <a :href="route('hackathon-admin.ideas.index')"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                            <LightBulbIcon class="w-5 h-5 mr-2" />
                            Review Ideas
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Teams Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                            <UsersIcon class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Teams</span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics?.teams?.total || 0 }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Total</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-xs">
                            <div>
                                <span class="text-yellow-600 dark:text-yellow-400 font-semibold">{{ statistics?.teams?.pending || 0 }}</span>
                                <span class="text-gray-500 dark:text-gray-400"> Pending</span>
                            </div>
                            <div>
                                <span class="text-green-600 dark:text-green-400 font-semibold">{{ statistics?.teams?.approved || 0 }}</span>
                                <span class="text-gray-500 dark:text-gray-400"> Approved</span>
                            </div>
                            <div>
                                <span class="text-red-600 dark:text-red-400 font-semibold">{{ statistics?.teams?.rejected || 0 }}</span>
                                <span class="text-gray-500 dark:text-gray-400"> Rejected</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ideas Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2 bg-indigo-100 dark:bg-indigo-900/20 rounded-lg">
                            <LightBulbIcon class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Ideas</span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics?.ideas?.total || 0 }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Total</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-yellow-600 dark:text-yellow-400 font-semibold">{{ statistics?.ideas?.pending || 0 }}</span>
                                <span class="text-gray-500 dark:text-gray-400"> Pending</span>
                            </div>
                            <div>
                                <span class="text-green-600 dark:text-green-400 font-semibold">{{ statistics?.ideas?.approved || 0 }}</span>
                                <span class="text-gray-500 dark:text-gray-400"> Approved</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Workshops Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                            <AcademicCapIcon class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Workshops</span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics?.workshops?.total || 0 }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Total</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-blue-600 dark:text-blue-400 font-semibold">{{ statistics?.workshops?.upcoming || 0 }}</span>
                                <span class="text-gray-500 dark:text-gray-400"> Upcoming</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400 font-semibold">{{ statistics?.workshops?.completed || 0 }}</span>
                                <span class="text-gray-500 dark:text-gray-400"> Completed</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tracks Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                            <NewspaperIcon class="w-6 h-6 text-green-600 dark:text-green-400" />
                        </div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Tracks</span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics?.tracks || 0 }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Active</span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Managing teams and ideas
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Recent Teams -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Teams</h3>
                            <a :href="route('hackathon-admin.teams.index')" 
                               class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                View all ’
                            </a>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div v-for="team in recentTeams" :key="team.id" class="px-6 py-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <a :href="route('hackathon-admin.teams.show', team.id)"
                                       class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ team.name }}
                                    </a>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Leader: {{ team.leader?.name || 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Track: {{ team.track?.name || 'Not Assigned' }}
                                    </div>
                                </div>
                                <span :class="[statusColors[team.status], 'px-2 py-1 text-xs font-medium rounded-full']">
                                    {{ team.status }}
                                </span>
                            </div>
                        </div>
                        <div v-if="!recentTeams || recentTeams.length === 0" class="px-6 py-8 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">No teams registered yet</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Ideas -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Ideas</h3>
                            <a :href="route('hackathon-admin.ideas.index')" 
                               class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                View all ’
                            </a>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div v-for="idea in recentIdeas" :key="idea.id" class="px-6 py-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <a :href="route('hackathon-admin.ideas.show', idea.id)"
                                       class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ idea.title }}
                                    </a>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Team: {{ idea.team?.name || 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Track: {{ idea.track?.name || 'Not Assigned' }}
                                    </div>
                                </div>
                                <span :class="[statusColors[idea.status], 'px-2 py-1 text-xs font-medium rounded-full']">
                                    {{ idea.status }}
                                </span>
                            </div>
                        </div>
                        <div v-if="!recentIdeas || recentIdeas.length === 0" class="px-6 py-8 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">No ideas submitted yet</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Workshops & Teams by Track -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Upcoming Workshops -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Upcoming Workshops</h3>
                            <a :href="route('hackathon-admin.workshops.index')" 
                               class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                Manage ’
                            </a>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div v-for="workshop in upcomingWorkshops" :key="workshop.id" class="px-6 py-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <a :href="route('hackathon-admin.workshops.show', workshop.id)"
                                       class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ workshop.title }}
                                    </a>
                                    <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <ClockIcon class="w-3 h-3 mr-1" />
                                        {{ formatDate(workshop.start_time) }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Speaker: {{ workshop.speaker?.name || 'TBD' }}
                                    </div>
                                </div>
                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 rounded-full">
                                    {{ workshop.max_participants - (workshop.registrations_count || 0) }} spots
                                </span>
                            </div>
                        </div>
                        <div v-if="!upcomingWorkshops || upcomingWorkshops.length === 0" class="px-6 py-8 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">No upcoming workshops scheduled</p>
                        </div>
                    </div>
                </div>

                <!-- Teams by Track -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Teams by Track</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div v-for="track in teamsByTrack" :key="track.id">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ track.name }}</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ track.teams_count || 0 }} teams</span>
                                </div>
                                <div class="mt-1 relative">
                                    <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200 dark:bg-gray-700">
                                        <div :style="`width: ${((track.teams_count || 0) / (statistics?.teams?.total || 1)) * 100}%`"
                                             class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-600"></div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="!teamsByTrack || teamsByTrack.length === 0" class="text-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">No tracks configured yet</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>