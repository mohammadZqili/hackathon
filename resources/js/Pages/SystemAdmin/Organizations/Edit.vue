<template>
    <Head :title="t('admin.organizations.edit')" />
    <Default>
        <div class="container mx-auto px-4 py-8">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ t('admin.organizations.edit') }}</h1>
            </div>

            <!-- Form Content -->
            <form @submit.prevent="submit" class="max-w-4xl space-y-6">
                <!-- Organization Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.organizations.name') }}
                    </label>
                    <input v-model="form.name"
                           type="text"
                           :placeholder="t('admin.form.placeholder.enter_organization_name')"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500"
                           required>
                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                        {{ form.errors.name }}
                    </p>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.organizations.email') }}
                    </label>
                    <input v-model="form.email"
                           type="email"
                           :placeholder="t('admin.form.placeholder.contact_email')"
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

                <!-- Industry/Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.organizations.type') }}
                    </label>
                    <select v-model="form.type"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                        <option value="technology">{{ t('admin.form.technology') }}</option>
                        <option value="education">{{ t('admin.form.education') }}</option>
                        <option value="finance">{{ t('admin.form.finance') }}</option>
                        <option value="healthcare">{{ t('admin.form.healthcare') }}</option>
                        <option value="manufacturing">{{ t('admin.form.manufacturing') }}</option>
                        <option value="retail">{{ t('admin.form.retail') }}</option>
                        <option value="nonprofit">{{ t('admin.form.nonprofit') }}</option>
                        <option value="government">{{ t('admin.form.government') }}</option>
                        <option value="other">{{ t('admin.form.other') }}</option>
                    </select>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.form.description') }}
                    </label>
                    <textarea v-model="form.description"
                              rows="4"
                              :placeholder="t('admin.form.placeholder.organization_description')"
                              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500"></textarea>
                </div>

                <!-- Associated Speakers -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.form.associated_speakers') }}
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
                        {{ t('admin.form.multiple_select_help') }}
                    </p>
                </div>

                <!-- Partnership Level -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.organizations.partnership_level') }}
                    </label>
                    <select v-model="form.partnership_level"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-2 focus:ring-teal-500">
                        <option value="">{{ t('admin.form.placeholder.select_level') }}</option>
                        <option value="platinum">{{ t('admin.form.platinum') }}</option>
                        <option value="gold">{{ t('admin.organizations.gold') }}</option>
                        <option value="silver">{{ t('admin.organizations.silver') }}</option>
                        <option value="bronze">{{ t('admin.organizations.bronze') }}</option>
                        <option value="partner">{{ t('admin.organizations.partner') }}</option>
                        <option value="supporter">{{ t('admin.form.supporter') }}</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 pt-6">
                    <Link :href="route('system-admin.organizations.index')"
                          class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        {{ t('admin.actions.cancel') }}
                    </Link>
                    <button type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 disabled:opacity-50">
                        {{ form.processing ? t('admin.actions.updating') : t('admin.actions.update') + ' ' + t('admin.organizations.title') }}
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