<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'

const props = defineProps({
    track: Object,
    statistics: Object,
    pendingIdeas: Array,
})
</script>

<template>
    <Head title="Track Supervisor Dashboard" />
    <Default>
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Track Supervisor Dashboard</h1>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">My Track</h2>
                <p>{{ track?.name || 'No track assigned' }}</p>
                <p class="mt-2">Ideas to Review: {{ pendingIdeas?.length || 0 }}</p>
            </div>
        </div>
    </Default>
</template>