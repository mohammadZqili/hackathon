<template>
    <Head title="Add Workshop" />
    <Default>
        <div class="container mx-auto px-4 py-8">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Add Workshop</h1>
            </div>

            <!-- Form Content -->
            <form @submit.prevent="submit" class="max-w-4xl space-y-6">
                <!-- Workshop Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Workshop Title
                    </label>
                    <input v-model="form.title"
                           type="text"
                           placeholder="Enter workshop title"
                           class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white placeholder-teal-600/50 dark:placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                           required>
                    <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                        {{ form.errors.title }}
                    </p>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea v-model="form.description"
                              rows="4"
                              placeholder="Describe the workshop content and objectives..."
                              class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white placeholder-teal-600/50 dark:placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-transparent resize-none"></textarea>
                </div>

                <!-- Date and Time Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date
                        </label>
                        <input v-model="form.start_date"
                               type="date"
                               class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                               required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Time
                        </label>
                        <input v-model="form.start_time"
                               type="time"
                               class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Duration and Max Attendees Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Duration (hours)
                        </label>
                        <input v-model="form.duration"
                               type="number"
                               min="0.5"
                               max="8"
                               step="0.5"
                               placeholder="2"
                               class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white placeholder-teal-600/50 dark:placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Max Attendees
                        </label>
                        <input v-model="form.max_attendees"
                               type="number"
                               min="1"
                               max="500"
                               placeholder="50"
                               class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white placeholder-teal-600/50 dark:placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Speaker Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Speaker
                    </label>
                    <select v-model="form.speaker_id"
                            class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        <option value="">Select Speaker</option>
                        <option v-for="speaker in speakers" :key="speaker.id" :value="speaker.id">
                            {{ speaker.name }}
                        </option>
                    </select>
                </div>

                <!-- Organization Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Organization
                    </label>
                    <select v-model="form.organization_id"
                            class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        <option value="">Select Organization</option>
                        <option v-for="org in organizations" :key="org.id" :value="org.id">
                            {{ org.name }}
                        </option>
                    </select>
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Location
                    </label>
                    <input v-model="form.location"
                           type="text"
                           placeholder="Room 101, Building A"
                           class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white placeholder-teal-600/50 dark:placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                </div>

                <!-- Workshop Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Workshop Type
                    </label>
                    <select v-model="form.type"
                            class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        <option value="technical">Technical</option>
                        <option value="business">Business</option>
                        <option value="soft-skills">Soft Skills</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between pt-6">
                    <Link :href="route('system-admin.workshops.index')"
                          class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </Link>
                    <button type="submit"
                            :disabled="form.processing"
                            class="px-8 py-3 bg-gradient-to-r from-teal-600 to-teal-500 text-white rounded-lg font-semibold hover:from-teal-700 hover:to-teal-600 disabled:opacity-50 transition-all shadow-lg hover:shadow-xl">
                        {{ form.processing ? 'Creating...' : 'Create Workshop' }}
                    </button>
                </div>
            </form>
        </div>
    </Default>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'

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

const form = useForm({
    title: '',
    description: '',
    start_date: '',
    start_time: '',
    duration: 2,
    max_attendees: 50,
    speaker_id: '',
    organization_id: '',
    location: '',
    type: 'technical'
})

const submit = () => {
    form.post(route('system-admin.workshops.store'))
}
</script>