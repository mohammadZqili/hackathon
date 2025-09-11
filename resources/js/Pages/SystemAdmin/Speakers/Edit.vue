<template>
    <Head :title="t('admin.speakers.edit')" />
    <Default>
        <div class="container mx-auto px-4 py-8">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ t('admin.speakers.edit') }}</h1>
            </div>

            <!-- Form Content -->
            <form @submit.prevent="submit" class="max-w-4xl space-y-6">
                <!-- Speaker Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.speakers.name') }}
                    </label>
                    <input v-model="form.name"
                           type="text"
                           :placeholder="t('admin.form.placeholder.enter_name')"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500"
                           required>
                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                        {{ form.errors.name }}
                    </p>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.form.email') }}
                    </label>
                    <input v-model="form.email"
                           type="email"
                           :placeholder="t('admin.form.placeholder.enter_email')"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500"
                           required>
                    <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                        {{ form.errors.email }}
                    </p>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.form.phone') }}
                    </label>
                    <input v-model="form.phone"
                           type="tel"
                           :placeholder="t('admin.form.placeholder.enter_phone')"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Title/Position -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.speakers.title_position') }}
                    </label>
                    <input v-model="form.title"
                           type="text"
                           :placeholder="t('admin.form.placeholder.enter_title')"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Organization -->
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

                <!-- Bio -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.speakers.bio') }}
                    </label>
                    <textarea v-model="form.bio"
                              rows="4"
                              :placeholder="t('admin.form.placeholder.speaker_bio')"
                              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500"></textarea>
                </div>

                <!-- Expertise -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.speakers.expertise') }}
                    </label>
                    <input v-model="form.expertise"
                           type="text"
                           :placeholder="t('admin.form.placeholder.expertise')"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ t('admin.form.keywords_help') }}
                    </p>
                </div>

                <!-- Social Links -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ t('admin.speakers.linkedin') }}
                        </label>
                        <input v-model="form.linkedin"
                               type="url"
                               :placeholder="t('admin.form.placeholder.linkedin')"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ t('admin.speakers.twitter') }}
                        </label>
                        <input v-model="form.twitter"
                               type="url"
                               :placeholder="t('admin.form.placeholder.twitter')"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>

                <!-- Website -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.form.website') }}
                    </label>
                    <input v-model="form.website"
                           type="url"
                           :placeholder="t('admin.form.placeholder.website')"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 pt-6">
                    <Link :href="route('system-admin.speakers.index')"
                          class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        {{ t('admin.actions.cancel') }}
                    </Link>
                    <button type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 disabled:opacity-50">
                        {{ form.processing ? t('admin.actions.updating') : t('admin.actions.update') + ' ' + t('admin.speakers.name') }}
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
    form.put(route('system-admin.speakers.update', props.speaker.id))
}
</script>