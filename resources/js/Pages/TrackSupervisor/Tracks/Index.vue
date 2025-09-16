<template>
    <Default>
        <Head title="Tracks" />
        <div class="w-full relative h-full overflow-hidden text-left text-sm">
            <!-- Header Section -->
            <div class="flex flex-row items-start justify-between flex-wrap p-4">
                <div class="flex flex-col items-start justify-start gap-3 min-w-[288px]">
                    <div class="flex flex-col items-start justify-start">
                        <h1 class="text-[32px] font-bold leading-10">My Assigned Tracks</h1>
                    </div>
                    <div class="flex flex-col items-start justify-start text-sm" :style="{ color: themeColor.primary }">
                        <div class="relative leading-[21px]">Manage teams, ideas, and workshops for your assigned tracks.</div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-row items-center justify-end gap-3">
                    <select
                        v-model="filters.edition_id"
                        @change="applyFilters"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2"
                        :style="{ '--tw-ring-color': themeColor.primary }"
                    >
                        <option value="">All Editions</option>
                        <option v-for="edition in editions" :key="edition.id" :value="edition.id">
                            {{ edition.name }}
                        </option>
                    </select>

                    <select
                        v-model="filters.status"
                        @change="applyFilters"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2"
                        :style="{ '--tw-ring-color': themeColor.primary }"
                    >
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>

                    <input
                        v-model="filters.search"
                        @input="debounceSearch"
                        type="text"
                        placeholder="Search tracks..."
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2"
                        :style="{ '--tw-ring-color': themeColor.primary }"
                    />
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 px-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-4 border" :style="{ borderColor: `rgba(${themeColor.rgb}, 0.1)` }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500">Total Tracks</p>
                            <p class="text-2xl font-bold">{{ statistics.total }}</p>
                        </div>
                        <div class="p-2 rounded-lg" :style="{ backgroundColor: `rgba(${themeColor.rgb}, 0.1)` }">
                            <svg class="w-5 h-5" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4 border" :style="{ borderColor: `rgba(${themeColor.rgb}, 0.1)` }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500">Active</p>
                            <p class="text-2xl font-bold">{{ statistics.active }}</p>
                        </div>
                        <div class="p-2 rounded-lg bg-green-100">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4 border" :style="{ borderColor: `rgba(${themeColor.rgb}, 0.1)` }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500">Inactive</p>
                            <p class="text-2xl font-bold">{{ statistics.inactive }}</p>
                        </div>
                        <div class="p-2 rounded-lg bg-gray-100">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4 border" :style="{ borderColor: `rgba(${themeColor.rgb}, 0.1)` }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500">With Supervisor</p>
                            <p class="text-2xl font-bold">{{ statistics.with_supervisor }}</p>
                        </div>
                        <div class="p-2 rounded-lg" :style="{ backgroundColor: `rgba(${themeColor.rgb}, 0.1)` }">
                            <svg class="w-5 h-5" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4 border" :style="{ borderColor: `rgba(${themeColor.rgb}, 0.1)` }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500">Total Teams</p>
                            <p class="text-2xl font-bold">{{ statistics.total_teams }}</p>
                        </div>
                        <div class="p-2 rounded-lg bg-blue-100">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4 border" :style="{ borderColor: `rgba(${themeColor.rgb}, 0.1)` }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500">Total Ideas</p>
                            <p class="text-2xl font-bold">{{ statistics.total_ideas }}</p>
                        </div>
                        <div class="p-2 rounded-lg bg-purple-100">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tracks Table -->
            <div class="px-4">
                <div class="rounded-xl border overflow-hidden" :style="{ borderColor: `rgba(${themeColor.rgb}, 0.1)`, backgroundColor: `rgba(${themeColor.rgb}, 0.02)` }">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr :style="{ backgroundColor: `rgba(${themeColor.rgb}, 0.05)` }">
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Track Name</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Edition</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Description</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Assigned Supervisor</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Teams</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Ideas</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="track in tracks.data" :key="track.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4 text-sm text-gray-900">{{ track.name }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-600">
                                        <span v-if="track.edition">{{ track.edition.name }}</span>
                                        <span v-else class="text-gray-400">N/A</span>
                                    </td>
                                    <td class="px-4 py-4 text-sm" :style="{ color: themeColor.primary }">
                                        <div class="max-w-xs truncate">{{ track.description }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-sm" :style="{ color: themeColor.primary }">
                                        <span v-if="track.supervisors && track.supervisors.length > 0">
                                            {{ track.supervisors.map(s => s.name).join(', ') }}
                                        </span>
                                        <span v-else class="text-gray-400">Not Assigned</span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-900">{{ track.teams_count || 0 }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-900">{{ track.ideas_count || 0 }}</td>
                                    <td class="px-4 py-4 text-sm">
                                        <span v-if="track.is_active" class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                                        <span v-else class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                    </td>
                                    <td class="px-4 py-4 text-sm">
                                        <div class="flex items-center gap-2">
                                            <Link
                                                :href="route('track-supervisor.tracks.show', track.id)"
                                                class="text-blue-600 hover:text-blue-800"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </Link>
                                            <Link
                                                :href="route('track-supervisor.tracks.edit', track.id)"
                                                :style="{ color: themeColor.primary }"
                                                class="hover:opacity-75"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="tracks.links && tracks.links.length > 3" class="mt-4">
                    <Pagination :links="tracks.links" />
                </div>
            </div>

            <!-- Note: Track supervisors cannot create tracks -->
            <div v-if="message" class="px-4 py-3">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-yellow-800">
                    {{ message }}
                </div>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL, direction, locale } = useLocalization()
import { ref, computed, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Default from '@/Layouts/Default.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    tracks: Object,
    editions: Array,
    statistics: Object,
    filters: Object,
    message: String,
});

const filters = ref({
    edition_id: props.filters?.edition_id || '',
    status: props.filters?.status || '',
    search: props.filters?.search || '',
});

const themeColor = ref({
    primary: '#0d9488',
    hover: '#0f766e',
    rgb: '13, 148, 136',
    gradientFrom: '#0d9488',
    gradientTo: '#14b8a6'
});

onMounted(() => {
    const root = document.documentElement;
    const primary = getComputedStyle(root).getPropertyValue('--primary-color').trim() || '#0d9488';
    const hover = getComputedStyle(root).getPropertyValue('--primary-hover').trim() || '#0f766e';
    const rgb = getComputedStyle(root).getPropertyValue('--primary-color-rgb').trim() || '13, 148, 136';
    const gradientFrom = getComputedStyle(root).getPropertyValue('--primary-gradient-from').trim() || '#0d9488';
    const gradientTo = getComputedStyle(root).getPropertyValue('--primary-gradient-to').trim() || '#14b8a6';

    themeColor.value = {
        primary: primary || themeColor.value.primary,
        hover: hover || themeColor.value.hover,
        rgb: rgb || themeColor.value.rgb,
        gradientFrom: gradientFrom || themeColor.value.gradientFrom,
        gradientTo: gradientTo || themeColor.value.gradientTo
    };
});

let searchTimeout = null;

const debounceSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
};

const applyFilters = () => {
    router.get(route('track-supervisor.tracks.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>