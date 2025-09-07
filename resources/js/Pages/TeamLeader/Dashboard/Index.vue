<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'

const props = defineProps({
    team: Object,
    idea: Object,
    workshops: Array,
})
</script>

<template>
    <Head title="Team Leader Dashboard" />
    <Default>
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Team Leader Dashboard</h1>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Team status card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">My Team</h2>
                    <p>{{ team?.name || 'No team assigned' }}</p>
                </div>
                <!-- Idea status card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">Our Idea</h2>
                    <p>{{ idea?.title || 'No idea submitted' }}</p>
                </div>
            </div>
        </div>
    </Default>
</template>