<script setup>
import { useLocalization } from '@/composables/useLocalization'
const { t, isRTL, direction, locale } = useLocalization()

import { Head, router, useForm, Link } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import Default from '@/Layouts/Default.vue'
import RichTextEditor from '@/Components/Forms/RichTextEditor.vue'
import {
    ArrowLeftIcon,
    DocumentIcon,
    TagIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    idea: Object,
    tracks: Array,
})

// Get theme color from localStorage or default
const themeColor = ref({
    primary: '#0d9488',
    hover: '#0f766e',
    rgb: '13, 148, 136',
    gradientFrom: '#0d9488',
    gradientTo: '#14b8a6'
})

onMounted(() => {
    const root = document.documentElement
    const primary = getComputedStyle(root).getPropertyValue('--primary-color').trim() || '#0d9488'
    const hover = getComputedStyle(root).getPropertyValue('--primary-hover').trim() || '#0f766e'
    const rgb = getComputedStyle(root).getPropertyValue('--primary-color-rgb').trim() || '13, 148, 136'
    const gradientFrom = getComputedStyle(root).getPropertyValue('--primary-gradient-from').trim() || '#0d9488'
    const gradientTo = getComputedStyle(root).getPropertyValue('--primary-gradient-to').trim() || '#14b8a6'

    themeColor.value = {
        primary: primary || themeColor.value.primary,
        hover: hover || themeColor.value.hover,
        rgb: rgb || themeColor.value.rgb,
        gradientFrom: gradientFrom || themeColor.value.gradientFrom,
        gradientTo: gradientTo || themeColor.value.gradientTo
    }
})

const themeStyles = computed(() => ({
    '--theme-primary': themeColor.value.primary,
    '--theme-hover': themeColor.value.hover,
    '--theme-rgb': themeColor.value.rgb,
    '--theme-gradient-from': themeColor.value.gradientFrom,
    '--theme-gradient-to': themeColor.value.gradientTo,
}))

// Form for editing idea
const form = useForm({
    title: props.idea.title || '',
    description: props.idea.description || '',
    problem_statement: props.idea.problem_statement || '',
    solution_approach: props.idea.solution_approach || '',
    expected_impact: props.idea.expected_impact || '',
    track_id: props.idea.track_id || '',
    status: props.idea.status || 'draft',
    technologies: props.idea.technologies || [],
})

// Technology input
const technologyInput = ref('')

const addTechnology = () => {
    if (technologyInput.value.trim() && !form.technologies.includes(technologyInput.value.trim())) {
        form.technologies.push(technologyInput.value.trim())
        technologyInput.value = ''
    }
}

const removeTechnology = (index) => {
    form.technologies.splice(index, 1)
}

const submitForm = () => {
    form.put(route('track-supervisor.ideas.update', props.idea.id), {
        preserveScroll: false,
        onSuccess: () => {
            // Redirect handled by controller
        },
    })
}

const statusOptions = [
    { value: 'draft', label: 'Draft', color: 'bg-gray-100 text-gray-800' },
    { value: 'submitted', label: 'Submitted', color: 'bg-blue-100 text-blue-800' },
    { value: 'under_review', label: 'Under Review', color: 'bg-yellow-100 text-yellow-800' },
    { value: 'needs_revision', label: 'Needs Revision', color: 'bg-orange-100 text-orange-800' },
    { value: 'accepted', label: 'Accepted', color: 'bg-green-100 text-green-800' },
    { value: 'rejected', label: 'Rejected', color: 'bg-red-100 text-red-800' },
]
</script>

<template>
    <Head :title="t('admin.ideas.edit_idea')" />

    <Default>
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6" :style="themeStyles">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <Link
                                :href="route('track-supervisor.ideas.index')"
                                class="mr-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                            >
                                <ArrowLeftIcon class="w-6 h-6" />
                            </Link>
                            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                                {{ t('admin.ideas.edit_idea') }}
                            </h1>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form @submit.prevent="submitForm" class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                        <!-- Title -->
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.ideas.title') }} *
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                id="title"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-theme-primary focus:ring focus:ring-theme-primary focus:ring-opacity-25"
                                :placeholder="t('admin.ideas.enter_title')"
                                required
                            />
                            <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                                {{ form.errors.title }}
                            </div>
                        </div>

                        <!-- Track -->
                        <div class="mb-6">
                            <label for="track" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.ideas.track') }} *
                            </label>
                            <select
                                v-model="form.track_id"
                                id="track"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-theme-primary focus:ring focus:ring-theme-primary focus:ring-opacity-25"
                                required
                            >
                                <option value="">{{ t('admin.ideas.select_track') }}</option>
                                <option v-for="track in tracks" :key="track.id" :value="track.id">
                                    {{ track.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.track_id" class="mt-1 text-sm text-red-600">
                                {{ form.errors.track_id }}
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mb-6">
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.ideas.status') }} *
                            </label>
                            <select
                                v-model="form.status"
                                id="status"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-theme-primary focus:ring focus:ring-theme-primary focus:ring-opacity-25"
                                required
                            >
                                <option v-for="status in statusOptions" :key="status.value" :value="status.value">
                                    {{ status.label }}
                                </option>
                            </select>
                            <div v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                                {{ form.errors.status }}
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.ideas.description') }} *
                            </label>
                            <RichTextEditor
                                v-model="form.description"
                                :placeholder="t('admin.ideas.enter_description')"
                            />
                            <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                {{ form.errors.description }}
                            </div>
                        </div>

                        <!-- Problem Statement -->
                        <div class="mb-6">
                            <label for="problem_statement" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.ideas.problem_statement') }} *
                            </label>
                            <RichTextEditor
                                v-model="form.problem_statement"
                                :placeholder="t('admin.ideas.enter_problem_statement')"
                            />
                            <div v-if="form.errors.problem_statement" class="mt-1 text-sm text-red-600">
                                {{ form.errors.problem_statement }}
                            </div>
                        </div>

                        <!-- Solution Approach -->
                        <div class="mb-6">
                            <label for="solution_approach" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.ideas.solution_approach') }} *
                            </label>
                            <RichTextEditor
                                v-model="form.solution_approach"
                                :placeholder="t('admin.ideas.enter_solution_approach')"
                            />
                            <div v-if="form.errors.solution_approach" class="mt-1 text-sm text-red-600">
                                {{ form.errors.solution_approach }}
                            </div>
                        </div>

                        <!-- Expected Impact -->
                        <div class="mb-6">
                            <label for="expected_impact" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.ideas.expected_impact') }}
                            </label>
                            <RichTextEditor
                                v-model="form.expected_impact"
                                :placeholder="t('admin.ideas.enter_expected_impact')"
                            />
                            <div v-if="form.errors.expected_impact" class="mt-1 text-sm text-red-600">
                                {{ form.errors.expected_impact }}
                            </div>
                        </div>

                        <!-- Technologies -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.ideas.technologies') }}
                            </label>
                            <div class="flex gap-2 mb-2">
                                <input
                                    v-model="technologyInput"
                                    type="text"
                                    @keyup.enter.prevent="addTechnology"
                                    class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-theme-primary focus:ring focus:ring-theme-primary focus:ring-opacity-25"
                                    :placeholder="t('admin.ideas.add_technology')"
                                />
                                <button
                                    type="button"
                                    @click="addTechnology"
                                    class="px-4 py-2 text-white rounded-md transition-colors"
                                    :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }"
                                >
                                    {{ t('admin.actions.add') }}
                                </button>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="(tech, index) in form.technologies"
                                    :key="index"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200"
                                >
                                    {{ tech }}
                                    <button
                                        type="button"
                                        @click="removeTechnology(index)"
                                        class="ml-2 text-gray-500 hover:text-red-500"
                                    >
                                        ×
                                    </button>
                                </span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end gap-4">
                            <Link
                                :href="route('track-supervisor.ideas.show', idea.id)"
                                class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
                            >
                                {{ t('admin.actions.cancel') }}
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 text-white rounded-md transition-colors disabled:opacity-50"
                                :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }"
                            >
                                {{ form.processing ? t('admin.actions.saving') : t('admin.actions.save_changes') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </Default>
</template>

<style scoped>
input[type="text"]:focus,
select:focus,
textarea:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>