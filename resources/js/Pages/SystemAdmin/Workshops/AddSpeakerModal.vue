<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ t('admin.speakers.create') }}
                    </h3>
                    <button @click="$emit('close')"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form @submit.prevent="submit" class="p-6 space-y-6">
                <!-- Speaker Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.form.name') }} *
                    </label>
                    <input v-model="form.name"
                           type="text"
                           :placeholder="t('admin.form.placeholder.enter_name')"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                           :style="{ '--tw-ring-color': themeColor.primary }"
                           required>
                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                        {{ form.errors.name }}
                    </p>
                </div>

                <!-- Email and Phone -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ t('admin.form.email') }} *
                        </label>
                        <input v-model="form.email"
                               type="email"
                               :placeholder="t('admin.form.placeholder.enter_email')"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                               :style="{ '--tw-ring-color': themeColor.primary }"
                               required>
                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">
                            {{ form.errors.email }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ t('admin.form.phone') }}
                        </label>
                        <input v-model="form.phone"
                               type="tel"
                               :placeholder="t('admin.form.placeholder.enter_phone')"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                               :style="{ '--tw-ring-color': themeColor.primary }">
                    </div>
                </div>

                <!-- Title and Organization -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ t('admin.speakers.title_position') }}
                        </label>
                        <input v-model="form.title"
                               type="text"
                               :placeholder="t('admin.form.placeholder.enter_title')"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                               :style="{ '--tw-ring-color': themeColor.primary }">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ t('admin.form.organization') }}
                        </label>
                        <select v-model="form.organization_id"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                                :style="{ '--tw-ring-color': themeColor.primary }">
                            <option value="">{{ t('admin.form.placeholder.select_organization') }}</option>
                            <option v-for="org in organizations" :key="org.id" :value="org.id">
                                {{ org.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Bio -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.speakers.bio') }}
                    </label>
                    <textarea v-model="form.bio"
                              rows="4"
                              :placeholder="t('admin.form.placeholder.speaker_bio')"
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors resize-none"
                              :style="{ '--tw-ring-color': themeColor.primary }"></textarea>
                </div>

                <!-- Social Links -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.speakers.social_media') }}
                    </label>
                    <div class="space-y-2">
                        <input v-model="form.linkedin"
                               type="url"
                               :placeholder="t('admin.form.placeholder.linkedin')"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                               :style="{ '--tw-ring-color': themeColor.primary }">
                        <input v-model="form.twitter"
                               type="url"
                               :placeholder="t('admin.form.placeholder.twitter')"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                               :style="{ '--tw-ring-color': themeColor.primary }">
                        <input v-model="form.website"
                               type="url"
                               :placeholder="t('admin.form.placeholder.website')"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                               :style="{ '--tw-ring-color': themeColor.primary }">
                    </div>
                </div>

                <!-- Expertise Areas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('admin.speakers.expertise') }}
                    </label>
                    <input v-model="form.expertise"
                           type="text"
                           :placeholder="t('admin.form.placeholder.expertise')"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                           :style="{ '--tw-ring-color': themeColor.primary }">
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4 pt-4">
                    <button @click="$emit('close')" type="button"
                            class="px-6 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        {{ t('admin.actions.cancel') }}
                    </button>
                    <button type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 rounded-lg text-white font-medium transition-all duration-200 disabled:opacity-50"
                            :style="{
                                background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                            }">
                        {{ form.processing ? t('admin.actions.adding') : t('admin.speakers.create') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL, direction, locale } = useLocalization()
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    themeColor: {
        type: Object,
        required: true
    },
    organizations: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits(['close', 'success'])

const form = useForm({
    name: '',
    email: '',
    phone: '',
    title: '',
    organization_id: '',
    bio: '',
    linkedin: '',
    twitter: '',
    website: '',
    expertise: ''
})

const submit = () => {
    form.post(route('system-admin.speakers.store'), {
        onSuccess: () => {
            emit('success')
        },
        onError: (errors) => {
            console.error('Error adding speaker:', errors)
        }
    })
}
</script>

<style scoped>
input[type="text"]:focus,
input[type="email"]:focus,
input[type="tel"]:focus,
input[type="url"]:focus,
select:focus,
textarea:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>