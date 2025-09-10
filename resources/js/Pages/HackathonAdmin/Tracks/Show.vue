<template>
    <Default>
        <Head :title="`Track: ${track.name}`" />

        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ track.name }}</h1>
                        <p class="mt-2 text-gray-600">Track details and teams</p>
                    </div>
                    <div class="flex space-x-4">
                        <Link
                            :href="route('hackathon-admin.tracks.edit', track.id)"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            <i class="fas fa-edit mr-2"></i>
                            Edit Track
                        </Link>
                        <button
                            @click="deleteTrack"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                        >
                            <i class="fas fa-trash mr-2"></i>
                            Delete Track
                        </button>
                    </div>
                </div>
            </div>

            <!-- Track Information -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Track Details -->
                <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Track Details</h2>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ track.description }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
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
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Maximum Teams</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ track.max_teams || 'Unlimited' }}
                                <span v-if="track.max_teams" class="text-gray-500">
                                    ({{ track.teams?.length || 0 }} / {{ track.max_teams }})
                                </span>
                            </dd>
                        </div>
                        <div v-if="track.evaluation_criteria && track.evaluation_criteria.length">
                            <dt class="text-sm font-medium text-gray-500">Evaluation Criteria</dt>
                            <dd class="mt-1">
                                <ul class="list-disc list-inside space-y-1">
                                    <li v-for="criterion in track.evaluation_criteria" :key="criterion.name" class="text-sm text-gray-900">
                                        {{ criterion.name }} ({{ criterion.weight }}%)
                                    </li>
                                </ul>
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Supervisor Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Supervisor</h2>
                    <div v-if="track.supervisor">
                        <div class="flex items-center mb-4">
                            <div class="h-12 w-12 rounded-full bg-mint-100 flex items-center justify-center">
                                <i class="fas fa-user text-mint-600"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">{{ track.supervisor.name }}</p>
                                <p class="text-sm text-gray-500">{{ track.supervisor.email }}</p>
                            </div>
                        </div>
                        <Link
                            :href="route('hackathon-admin.tracks.edit', track.id)"
                            class="block w-full text-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Change Supervisor
                        </Link>
                    </div>
                    <div v-else>
                        <p class="text-gray-500 mb-4">No supervisor assigned</p>
                        <Link
                            :href="route('hackathon-admin.tracks.edit', track.id)"
                            class="block w-full text-center px-4 py-2 bg-mint-600 text-white rounded-md hover:bg-mint-700"
                        >
                            Assign Supervisor
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Teams Section -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4">Teams in this Track</h2>
                    <div v-if="track.teams && track.teams.length > 0">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Team Name
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Leader
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Members
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
                                <tr v-for="team in track.teams" :key="team.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ team.name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ team.leader?.name || 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ team.members?.length || 0 }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-green-100 text-green-800': team.status === 'approved',
                                                'bg-yellow-100 text-yellow-800': team.status === 'pending',
                                                'bg-red-100 text-red-800': team.status === 'rejected'
                                            }"
                                        >
                                            {{ team.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <Link
                                            :href="route('hackathon-admin.teams.show', team.id)"
                                            class="text-mint-600 hover:text-mint-900"
                                        >
                                            View
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center py-8">
                        <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">No teams in this track yet</p>
                    </div>
                </div>
            </div>

            <!-- Ideas Section -->
            <div class="bg-white rounded-lg shadow mt-6">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4">Ideas in this Track</h2>
                    <div v-if="track.ideas && track.ideas.length > 0">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Idea Title
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Team
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Score
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="idea in track.ideas" :key="idea.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ idea.title }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ idea.team?.name || 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-green-100 text-green-800': idea.status === 'accepted',
                                                'bg-yellow-100 text-yellow-800': idea.status === 'submitted',
                                                'bg-blue-100 text-blue-800': idea.status === 'under_review',
                                                'bg-gray-100 text-gray-800': idea.status === 'draft'
                                            }"
                                        >
                                            {{ idea.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ idea.score || '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <Link
                                            :href="route('hackathon-admin.ideas.show', idea.id)"
                                            class="text-mint-600 hover:text-mint-900"
                                        >
                                            View
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center py-8">
                        <i class="fas fa-lightbulb text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">No ideas submitted for this track yet</p>
                    </div>
                </div>
            </div>
        </div>

    </Default>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import Default from '@/Layouts/Default.vue';

const props = defineProps({
    track: Object,
    edition: Object,
});

const deleteTrack = () => {
    if (confirm('Are you sure you want to delete this track? This action cannot be undone.')) {
        router.delete(route('hackathon-admin.tracks.destroy', props.track.id));
    }
};
</script>