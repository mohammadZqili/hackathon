<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'

defineProps({
    user: Object,
    message: String,
})
</script>

<template>
    <Head title="Test Page" />
    
    <Default>
        <div class="max-w-7xl mx-auto p-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">HackathonAdmin Test Page</h1>
                
                <div class="space-y-2">
                    <p class="text-gray-700 dark:text-gray-300">
                        <strong>Message:</strong> {{ message }}
                    </p>
                    <p class="text-gray-700 dark:text-gray-300">
                        <strong>User:</strong> {{ user?.email }}
                    </p>
                    <p class="text-gray-700 dark:text-gray-300">
                        <strong>Name:</strong> {{ user?.name }}
                    </p>
                </div>
                
                <div class="mt-6 space-y-2">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Links</h2>
                    <div class="flex space-x-4">
                        <a href="/hackathon-admin/dashboard" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                        <a href="/hackathon-admin/teams" class="text-blue-600 hover:text-blue-800">Teams</a>
                        <a href="/hackathon-admin/ideas" class="text-blue-600 hover:text-blue-800">Ideas</a>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>