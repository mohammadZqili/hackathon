<template>
    <Head :title="t('admin.workshops.create')" />
    <Default>
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6" :style="themeStyles">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ t('admin.workshops.add_new') }}
                    </h1>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Workshop Name and Description Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Workshop Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.workshops.name') }}
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                :placeholder="t('admin.workshops.enter_name')"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-theme-primary focus:ring focus:ring-theme-primary focus:ring-opacity-25"
                                :style="{ backgroundColor: `rgba(${themeColor.rgb}, 0.05)` }"
                                required
                            />
                            <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.workshops.description') }}
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                :placeholder="t('admin.workshops.enter_description')"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-theme-primary focus:ring focus:ring-theme-primary focus:ring-opacity-25 resize-none"
                                :style="{ backgroundColor: `rgba(${themeColor.rgb}, 0.05)` }"
                            ></textarea>
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                {{ form.errors.description }}
                            </p>
                        </div>
                    </div>

                    <!-- Speaker and Organization Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Speaker -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.workshops.speaker') }}
                            </label>
                            <select
                                v-model="form.speaker_id"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-theme-primary focus:ring focus:ring-theme-primary focus:ring-opacity-25"
                                :style="{ backgroundColor: `rgba(${themeColor.rgb}, 0.05)` }"
                            >
                                <option value="">{{ t('admin.workshops.select_speaker') }}</option>
                                <option v-for="speaker in speakers" :key="speaker.id" :value="speaker.id">
                                    {{ speaker.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.speaker_ids" class="mt-1 text-sm text-red-600">
                                {{ form.errors.speaker_ids }}
                            </p>
                        </div>

                        <!-- Organizing Entity -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.workshops.organizing_entity') }}
                            </label>
                            <select
                                v-model="form.organization_id"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-theme-primary focus:ring focus:ring-theme-primary focus:ring-opacity-25"
                                :style="{ backgroundColor: `rgba(${themeColor.rgb}, 0.05)` }"
                            >
                                <option value="">{{ t('admin.workshops.select_organization') }}</option>
                                <option v-for="org in organizations" :key="org.id" :value="org.id">
                                    {{ org.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.organization_ids" class="mt-1 text-sm text-red-600">
                                {{ form.errors.organization_ids }}
                            </p>
                        </div>
                    </div>

                    <!-- Date and Time Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.workshops.date') }}
                            </label>
                            <input
                                v-model="form.workshop_date"
                                type="date"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-theme-primary focus:ring focus:ring-theme-primary focus:ring-opacity-25"
                                :style="{ backgroundColor: `rgba(${themeColor.rgb}, 0.05)` }"
                                required
                            />
                            <p v-if="form.errors.start_time" class="mt-1 text-sm text-red-600">
                                {{ form.errors.start_time }}
                            </p>
                        </div>

                        <!-- Time -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.workshops.start_time') || 'Start Time' }}
                            </label>
                            <input
                                v-model="form.start_time_input"
                                type="time"
                                :placeholder="t('admin.workshops.start_time')"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-theme-primary focus:ring focus:ring-theme-primary focus:ring-opacity-25"
                                :style="{ backgroundColor: `rgba(${themeColor.rgb}, 0.05)` }"
                                required
                            />
                            <p v-if="form.errors.start_time" class="mt-1 text-sm text-red-600">
                                {{ form.errors.start_time }}
                            </p>
                        </div>
                    </div>

                    <!-- Duration Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.workshops.duration') || 'Duration (hours)' }}
                            </label>
                            <input
                                v-model="form.duration"
                                type="number"
                                min="0.5"
                                max="8"
                                step="0.5"
                                placeholder="2"
                                required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-theme-primary focus:ring focus:ring-theme-primary focus:ring-opacity-25"
                                :style="{ backgroundColor: `rgba(${themeColor.rgb}, 0.05)` }"
                            />
                            <p v-if="form.errors.duration" class="mt-1 text-sm text-red-600">
                                {{ form.errors.duration }}
                            </p>
                        </div>
                        <div></div>
                    </div>

                    <!-- Location Row with Format Toggle -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ t('admin.workshops.location') }}
                        </label>

                        <!-- Format Toggle Buttons -->
                        <div class="flex gap-2 mb-3">
                            <button
                                type="button"
                                @click="form.format = 'offline'"
                                :class="[
                                    'px-4 py-2 rounded-lg font-medium transition-colors',
                                    form.format === 'offline'
                                        ? 'text-white'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
                                ]"
                                :style="form.format === 'offline' ? { backgroundColor: themeColor.primary } : {}"
                            >
                                {{ t('admin.workshops.in_person') }}
                            </button>
                            <button
                                type="button"
                                @click="form.format = 'online'"
                                :class="[
                                    'px-4 py-2 rounded-lg font-medium transition-colors',
                                    form.format === 'online'
                                        ? 'text-white'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
                                ]"
                                :style="form.format === 'online' ? { backgroundColor: themeColor.primary } : {}"
                            >
                                {{ t('admin.workshops.remote') }}
                            </button>
                        </div>

                        <input
                            v-model="form.location"
                            type="text"
                            :placeholder="form.format === 'online' ? t('admin.workshops.enter_remote_link') : t('admin.workshops.enter_location')"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-theme-primary focus:ring focus:ring-theme-primary focus:ring-opacity-25"
                            :style="{ backgroundColor: `rgba(${themeColor.rgb}, 0.05)` }"
                        />
                        <p v-if="form.errors.location" class="mt-1 text-sm text-red-600">
                            {{ form.errors.location }}
                        </p>
                    </div>

                    <!-- Remote Link and Seat Capacity Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Remote Link (only shown if format is online) -->
                        <div v-if="form.format === 'online'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.workshops.remote_link') }}
                            </label>
                            <input
                                v-model="form.remote_link"
                                type="url"
                                :placeholder="t('admin.workshops.enter_remote_link')"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-theme-primary focus:ring focus:ring-theme-primary focus:ring-opacity-25"
                                :style="{ backgroundColor: `rgba(${themeColor.rgb}, 0.05)` }"
                            />
                        </div>

                        <!-- Seat Capacity -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('admin.workshops.seat_capacity') }}
                            </label>
                            <input
                                v-model="form.max_attendees"
                                type="number"
                                min="1"
                                :placeholder="t('admin.workshops.enter_seat_capacity')"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-theme-primary focus:ring focus:ring-theme-primary focus:ring-opacity-25"
                                :style="{ backgroundColor: `rgba(${themeColor.rgb}, 0.05)` }"
                            />
                            <p v-if="form.errors.max_attendees" class="mt-1 text-sm text-red-600">
                                {{ form.errors.max_attendees }}
                            </p>
                        </div>
                    </div>

                    <!-- Workshop Type (Hidden but required) -->
                    <input type="hidden" v-model="form.type" />

                    <!-- Submit Button -->
                    <div class="flex justify-center pt-6">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full max-w-md px-6 py-3 text-white font-semibold rounded-lg shadow-lg transition-all disabled:opacity-50"
                            :style="{
                                background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                            }"
                        >
                            {{ form.processing ? t('admin.actions.creating') : t('admin.workshops.add_workshop') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { useLocalization } from '@/composables/useLocalization'
const { t, isRTL, direction, locale } = useLocalization()

import { Head, Link, useForm } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
    speakers: {
        type: Array,
        default: () => []
    },
    organizations: {
        type: Array,
        default: () => []
    }
})

// Theme colors
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

const form = useForm({
    title: '',
    description: '',
    workshop_date: '',
    start_time_input: '',
    duration: 2, // Default 2 hours
    start_time: '', // Will be computed from date+time
    max_attendees: 50,
    speaker_id: '',
    organization_id: '',
    location: '',
    remote_link: '',
    type: 'workshop',
    format: 'offline'
})

const submit = () => {
    // Combine date and time for start_time
    if (form.workshop_date && form.start_time_input) {
        form.start_time = `${form.workshop_date} ${form.start_time_input}:00`;
    }

    // Transform speaker_id and organization_id to arrays for the controller
    // Also include duration for backend calculation
    form.transform((data) => ({
        ...data,
        duration: data.duration, // Send duration to backend
        speaker_ids: data.speaker_id ? [data.speaker_id] : [],
        organization_ids: data.organization_id ? [data.organization_id] : []
    })).post(route('system-admin.workshops.store'))
}
</script>

<style scoped>
input[type="text"]:focus,
input[type="number"]:focus,
input[type="date"]:focus,
input[type="time"]:focus,
input[type="url"]:focus,
select:focus,
textarea:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>