<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'

const props = defineProps({
    team: Object,
    workshops: Array,
})
</script>

<template>
    <Head title="Team Member Dashboard" />
    <Default>
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Team Member Dashboard</h1>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">My Team Information</h2>
                <p>Team: {{ team?.name || 'Not assigned to a team' }}</p>
            </div>
        </div>
    </Default>
</template>