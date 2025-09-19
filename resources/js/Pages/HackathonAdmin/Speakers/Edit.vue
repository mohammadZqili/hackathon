<template>
    <Head title="Edit Speaker" />
    <Default>
        <div class="container mx-auto px-4 py-8">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Speaker</h1>
            </div>

            <!-- Form Content -->
            <form @submit.prevent="submit" class="max-w-4xl space-y-6">
                <!-- Speaker Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Name
                    </label>
                    <input v-model="form.name"
                           type="text"
                           placeholder="Enter speaker's full name"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500"
                           required>
                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                        {{ form.errors.name }}
                    </p>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email
                    </label>
                    <input v-model="form.email"
                           type="email"
                           placeholder="speaker@example.com"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500"
                           required>
                    <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                        {{ form.errors.email }}
                    </p>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Phone
                    </label>
                    <input v-model="form.phone"
                           type="tel"
                           placeholder="+1 234 567 8900"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Title/Position -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Title/Position
                    </label>
                    <input v-model="form.title"
                           type="text"
                           placeholder="e.g., Senior Developer, CEO"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Organization -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Organization
                    </label>
                    <select v-model="form.organization_id"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                        <option value="">Select Organization</option>
                        <option v-for="org in organizations" :key="org.id" :value="org.id">
                            {{ org.name }}
                        </option>
                    </select>
                </div>

                <!-- Bio -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Biography
                    </label>
                    <textarea v-model="form.bio"
                              rows="4"
                              placeholder="Brief biography of the speaker..."
                              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500"></textarea>
                </div>

                <!-- Expertise -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Areas of Expertise
                    </label>
                    <input v-model="form.expertise"
                           type="text"
                           placeholder="e.g., AI, Machine Learning, Data Science (comma-separated)"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Separate multiple areas with commas
                    </p>
                </div>

                <!-- Social Links -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            LinkedIn
                        </label>
                        <input v-model="form.linkedin"
                               type="url"
                               placeholder="https://linkedin.com/in/username"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Twitter
                        </label>
                        <input v-model="form.twitter"
                               type="url"
                               placeholder="https://twitter.com/username"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>

                <!-- Website -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Website
                    </label>
                    <input v-model="form.website"
                           type="url"
                           placeholder="https://example.com"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 pt-6">
                    <Link :href="route('hackathon-admin.speakers.index')"
                          class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </Link>
                    <button type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 disabled:opacity-50">
                        {{ form.processing ? 'Updating...' : 'Update Speaker' }}
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
    speaker: {
        type: Object,
        required: true
    },
    organizations: {
        type: Array,
        default: () => []
    }
})

const form = useForm({
    name: props.speaker.name || '',
    email: props.speaker.email || '',
    phone: props.speaker.phone || '',
    title: props.speaker.title || '',
    organization_id: props.speaker.organization_id || '',
    bio: props.speaker.bio || '',
    expertise: props.speaker.expertise || '',
    linkedin: props.speaker.linkedin || '',
    twitter: props.speaker.twitter || '',
    website: props.speaker.website || ''
})

const submit = () => {
    form.put(route('hackathon-admin.speakers.update', props.speaker.id))
}
</script>
