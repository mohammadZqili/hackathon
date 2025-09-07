<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Default from '../../../Layouts/Default.vue'
import { AcademicCapIcon, PlusIcon, MagnifyingGlassIcon, ClockIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    workshops: Object,
    statistics: Object,
    filters: Object,
})

const searchForm = useForm({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
})
</script>

<template>
    <Head title="Workshops Management" />
    <Default>
        <div class="max-w-7xl mx-auto">
            <div class="mb-8 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Workshops</h1>
                <a :href="route('hackathon-admin.workshops.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg">
                    <PlusIcon class="w-5 h-5 mr-2" />Create Workshop
                </a>
            </div>
            <!-- Workshop list implementation -->
        </div>
    </Default>
</template>