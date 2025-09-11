<template>
    <Default>
        <Head title="Tracks" />

        <div class="container mx-auto px-4 py-8">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Tracks Management</h1>
                        <p class="mt-2 text-gray-600">Manage hackathon tracks for {{ edition?.name }}</p>
                    </div>
                    <Link
                        :href="route('hackathon-admin.tracks.create')"
                        class="px-4 py-2 bg-mint-600 text-white rounded-lg hover:bg-mint-700 transition-colors"
                    >
                        <i class="fas fa-plus mr-2"></i>
                        Add Track
                    </Link>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-mint-100 rounded-lg">
                            <i class="fas fa-layer-group text-mint-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Total Tracks</p>
                            <p class="text-2xl font-bold text-gray-900">{{ tracks.total }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Total Teams</p>
                            <p class="text-2xl font-bold text-gray-900">{{ totalTeams }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <i class="fas fa-lightbulb text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Total Ideas</p>
                            <p class="text-2xl font-bold text-gray-900">{{ totalIdeas }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Active Tracks</p>
                            <p class="text-2xl font-bold text-gray-900">{{ activeTracks }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tracks Table -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Track Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Supervisor
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Teams
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ideas
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="track in tracks.data" :key="track.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ track.name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Max teams: {{ track.max_teams || 'Unlimited' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div v-if="track.supervisor" class="text-sm text-gray-900">
                                        {{ track.supervisor.name }}
                                    </div>
                                    <div v-else class="text-sm text-gray-500">
                                        Not assigned
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ track.teams_count || 0 }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ track.ideas_count || 0 }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        v-if="track.status === 'active'"
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                                    >
                                        Active
                                    </span>
                                    <span
                                        v-else
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800"
                                    >
                                        Inactive
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <Link
                                        :href="route('hackathon-admin.tracks.show', track.id)"
                                        class="text-mint-600 hover:text-mint-900 mr-3"
                                    >
                                        View
                                    </Link>
                                    <Link
                                        :href="route('hackathon-admin.tracks.edit', track.id)"
                                        class="text-blue-600 hover:text-blue-900 mr-3"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="deleteTrack(track.id)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="tracks.links && tracks.links.length > 3" class="px-6 py-4 border-t">
                    <Pagination :links="tracks.links" />
                </div>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import Default from '@/Layouts/Default.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    tracks: Object,
    edition: Object,
});

const totalTeams = computed(() => {
    return props.tracks.data?.reduce((sum, track) => sum + (track.teams_count || 0), 0) || 0;
});

const totalIdeas = computed(() => {
    return props.tracks.data?.reduce((sum, track) => sum + (track.ideas_count || 0), 0) || 0;
});

const activeTracks = computed(() => {
    return props.tracks.data?.filter(track => track.status === 'active').length || 0;
});

const deleteTrack = (id) => {
    if (confirm('Are you sure you want to delete this track?')) {
        router.delete(route('hackathon-admin.tracks.destroy', id));
    }
};
</script>