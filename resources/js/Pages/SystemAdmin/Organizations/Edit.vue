<template>
    <Head title="Edit Organization" />
    <Default>
        <div class="container mx-auto px-4 py-8">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Organization</h1>
            </div>

            <!-- Form Content -->
            <form @submit.prevent="submit" class="max-w-4xl space-y-6">
                <!-- Organization Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Organization Name
                    </label>
                    <input v-model="form.name"
                           type="text"
                           placeholder="Enter organization name"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500"
                           required>
                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                        {{ form.errors.name }}
                    </p>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Contact Email
                    </label>
                    <input v-model="form.email"
                           type="email"
                           placeholder="contact@organization.com"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500"
                           required>
                    <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                        {{ form.errors.email }}
                    </p>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Phone Number
                    </label>
                    <input v-model="form.phone"
                           type="tel"
                           placeholder="+1 234 567 8900"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Website -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Website
                    </label>
                    <input v-model="form.website"
                           type="url"
                           placeholder="https://organization.com"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Industry/Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Industry/Type
                    </label>
                    <select v-model="form.type"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                        <option value="technology">Technology</option>
                        <option value="education">Education</option>
                        <option value="finance">Finance</option>
                        <option value="healthcare">Healthcare</option>
                        <option value="manufacturing">Manufacturing</option>
                        <option value="retail">Retail</option>
                        <option value="nonprofit">Non-Profit</option>
                        <option value="government">Government</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea v-model="form.description"
                              rows="4"
                              placeholder="Brief description about the organization..."
                              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500"></textarea>
                </div>

                <!-- Associated Speakers -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Associated Speakers
                    </label>
                    <select v-model="form.speaker_ids"
                            multiple
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500"
                            style="min-height: 100px;">
                        <option v-for="speaker in speakers" :key="speaker.id" :value="speaker.id">
                            {{ speaker.name }}
                        </option>
                    </select>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Hold Ctrl/Cmd to select multiple speakers
                    </p>
                </div>

                <!-- Partnership Level -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Partnership Level
                    </label>
                    <select v-model="form.partnership_level"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                        <option value="">Select Level</option>
                        <option value="platinum">Platinum</option>
                        <option value="gold">Gold</option>
                        <option value="silver">Silver</option>
                        <option value="bronze">Bronze</option>
                        <option value="partner">Partner</option>
                        <option value="supporter">Supporter</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 pt-6">
                    <Link :href="route('system-admin.organizations.index')"
                          class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </Link>
                    <button type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 disabled:opacity-50">
                        {{ form.processing ? 'Updating...' : 'Update Organization' }}
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
    organization: {
        type: Object,
        required: true
    },
    speakers: {
        type: Array,
        default: () => []
    }
})

const form = useForm({
    name: props.organization.name || '',
    email: props.organization.email || '',
    phone: props.organization.phone || '',
    website: props.organization.website || '',
    type: props.organization.type || 'technology',
    description: props.organization.description || '',
    partnership_level: props.organization.partnership_level || '',
    speaker_ids: props.organization.speakers ? props.organization.speakers.map(s => s.id) : []
})

const submit = () => {
    form.put(route('system-admin.organizations.update', props.organization.id))
}
</script>