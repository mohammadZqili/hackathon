<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { PlusIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    editions: Object,
})
</script>

<template>
    <Head title="Hackathon Editions" />
    <Default>
        <div class="max-w-7xl mx-auto">
            <div class="mb-8 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Hackathon Editions</h1>
                <a :href="route('system-admin.editions.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg">
                    <PlusIcon class="w-5 h-5 mr-2" />Create Edition
                </a>
            </div>
            <!-- Editions list -->
        </div>
    </Default>
</template>