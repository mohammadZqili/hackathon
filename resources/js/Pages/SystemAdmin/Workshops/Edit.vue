<template>
    <Head :title="t('admin.workshops.edit')" />
    <Default>
        <div class="container mx-auto px-4 py-8">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ t('admin.workshops.edit') }}</h1>
            </div>

            <!-- Form Content -->
            <form @submit.prevent="submit" class="max-w-4xl space-y-6">
                <!-- Workshop Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.form.workshop_title') }}
                    </label>
                    <input v-model="form.title"
                           type="text"
                           :placeholder="t('admin.form.placeholder.enter_workshop_title')"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500"
                           required>
                    <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                        {{ form.errors.title }}
                    </p>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.form.description') }}
                    </label>
                    <textarea v-model="form.description"
                              rows="4"
                              :placeholder="t('admin.form.placeholder.workshop_description')"
                              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500"></textarea>
                </div>

                <!-- Date and Time Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ t('admin.form.date') }}
                        </label>
                        <input v-model="form.start_date"
                               type="date"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500"
                               required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ t('admin.form.time') }}
                        </label>
                        <input v-model="form.start_time"
                               type="time"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>

                <!-- Duration and Max Attendees Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ t('admin.form.duration_hours') }}
                        </label>
                        <input v-model="form.duration"
                               type="number"
                               min="0.5"
                               max="8"
                               step="0.5"
                               placeholder="2"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ t('admin.form.max_attendees') }}
                        </label>
                        <input v-model="form.max_attendees"
                               type="number"
                               min="1"
                               max="500"
                               placeholder="50"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>

                <!-- Speaker Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.workshops.speaker') }}
                    </label>
                    <select v-model="form.speaker_id"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                        <option value="">{{ t('admin.form.placeholder.select_speaker') }}</option>
                        <option v-for="speaker in speakers" :key="speaker.id" :value="speaker.id">
                            {{ speaker.name }}
                        </option>
                    </select>
                </div>

                <!-- Organization Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.form.organization') }}
                    </label>
                    <select v-model="form.organization_id"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                        <option value="">{{ t('admin.form.placeholder.select_organization') }}</option>
                        <option v-for="org in organizations" :key="org.id" :value="org.id">
                            {{ org.name }}
                        </option>
                    </select>
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.form.location') }}
                    </label>
                    <input v-model="form.location"
                           type="text"
                           :placeholder="t('admin.form.placeholder.location')"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Workshop Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.form.workshop_type') }}
                    </label>
                    <select v-model="form.type"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                        <option value="technical">{{ t('admin.form.technical') }}</option>
                        <option value="business">{{ t('admin.form.business') }}</option>
                        <option value="soft-skills">{{ t('admin.form.soft_skills') }}</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 pt-6">
                    <Link :href="route('system-admin.workshops.index')"
                          class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        {{ t('admin.actions.cancel') }}
                    </Link>
                    <button type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 disabled:opacity-50">
                        {{ form.processing ? t('admin.actions.updating') : t('admin.actions.update') + ' ' + t('admin.workshops.title') }}
                    </button>
                </div>
            </form>
        </div>
    </Default>
</template>

<script setup>
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL, direction, locale } = useLocalization()
import { Head, Link, useForm } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'

const props = defineProps({
    workshop: {
        type: Object,
        required: true
    },
    speakers: {
        type: Array,
        default: () => []
    },
    organizations: {
        type: Array,
        default: () => []
    }
})

const form = useForm({
    title: props.workshop.title || '',
    description: props.workshop.description || '',
    start_date: props.workshop.start_date || '',
    start_time: props.workshop.start_time || '',
    duration: props.workshop.duration || 2,
    max_attendees: props.workshop.max_attendees || 50,
    speaker_id: props.workshop.speaker_id || '',
    organization_id: props.workshop.organization_id || '',
    location: props.workshop.location || '',
    type: props.workshop.type || 'technical'
})

const submit = () => {
    form.put(route('system-admin.workshops.update', props.workshop.id))
}
</script>