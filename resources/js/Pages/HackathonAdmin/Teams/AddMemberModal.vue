<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ t('admin.teams.add_team_member') }}
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
            <form @submit.prevent="submit" class="p-6 space-y-5">
                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                        {{ t('admin.teams.full_name') }}
                    </label>
                    <input v-model="form.name"
                           type="text"
                           id="name"
                           :placeholder="t('admin.teams.enter_members_full_name')"
                           class="w-full h-14 px-4 rounded-xl bg-gray-100 dark:bg-gray-700 border-0 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-opacity-50 transition-all duration-200"
                           :class="{ 'ring-2 ring-red-500': form.errors.name }"
                           :style="{ '--tw-ring-color': themeColor.primary }">
                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                        {{ form.errors.name }}
                    </p>
                </div>

                <!-- Team Name (Read-only) -->
                <div>
                    <label for="team" class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                        {{ t('admin.teams.team_name') }}
                    </label>
                    <div class="w-full h-14 px-4 rounded-xl bg-gray-100 dark:bg-gray-700 border-0 text-gray-600 dark:text-gray-400 flex items-center">
                        {{ team.name }}
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                        {{ t('admin.teams.email') }}
                    </label>
                    <input v-model="form.email"
                           type="email"
                           id="email"
                           :placeholder="t('admin.teams.enter_members_email')"
                           class="w-full h-14 px-4 rounded-xl bg-gray-100 dark:bg-gray-700 border-0 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-opacity-50 transition-all duration-200"
                           :class="{ 'ring-2 ring-red-500': form.errors.email }"
                           :style="{ '--tw-ring-color': themeColor.primary }">
                    <p v-if="form.errors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">
                        {{ form.errors.email }}
                    </p>
                </div>

                <!-- Mobile Number -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                        {{ t('admin.teams.mobile_number') }}
                    </label>
                    <input v-model="form.phone"
                           type="tel"
                           id="phone"
                           :placeholder="t('admin.teams.enter_members_mobile_number')"
                           class="w-full h-14 px-4 rounded-xl bg-gray-100 dark:bg-gray-700 border-0 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-opacity-50 transition-all duration-200"
                           :class="{ 'ring-2 ring-red-500': form.errors.phone }"
                           :style="{ '--tw-ring-color': themeColor.primary }">
                    <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600 dark:text-red-400">
                        {{ form.errors.phone }}
                    </p>
                </div>

                <!-- Send Email Notification -->
                <div class="flex items-center">
                    <input v-model="form.send_invitation"
                           type="checkbox"
                           id="send_invitation"
                           class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-2 focus:ring-opacity-50"
                           :style="{
                               '--tw-text-opacity': '1',
                               'accent-color': themeColor.primary
                           }">
                    <label for="send_invitation" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                        {{ t('admin.teams.send_invitation_email') }}
                    </label>
                </div>

                <!-- Error Messages -->
                <div v-if="errorMessage"
                     class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <p class="text-sm text-red-600 dark:text-red-400">{{ errorMessage }}</p>
                </div>

                <!-- Success Message -->
                <div v-if="successMessage"
                     class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                    <p class="text-sm text-green-600 dark:text-green-400">{{ successMessage }}</p>
                </div>

                <!-- Actions -->
                <div class="flex justify-end pt-4">
                    <button type="submit"
                            :disabled="form.processing || !form.name || !form.email"
                            class="px-6 py-3 rounded-xl text-white font-semibold transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            :style="{
                                background: form.processing || !form.name || !form.email
                                    ? '#9ca3af'
                                    : `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                            }">
                        {{ form.processing ? t('admin.actions.sending') : t('admin.teams.send_invitation') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useLocalization } from '@/composables/useLocalization'
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const { t, isRTL, direction, locale } = useLocalization()

const props = defineProps({
    team: {
        type: Object,
        required: true
    },
    themeColor: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['close', 'success'])

const errorMessage = ref('')
const successMessage = ref('')

const form = useForm({
    name: '',
    email: '',
    phone: '',
    team_id: props.team.id,
    send_invitation: true
})

const submit = () => {
    errorMessage.value = ''
    successMessage.value = ''

    form.post(route('system-admin.teams.add-member', props.team.id), {
        onSuccess: () => {
            successMessage.value = form.send_invitation
                ? t('admin.teams.member_added_email_sent')
                : t('admin.teams.member_added_successfully')

            setTimeout(() => {
                emit('success')
            }, 1500)
        },
        onError: (errors) => {
            if (errors.email) {
                errorMessage.value = errors.email
            } else if (errors.name) {
                errorMessage.value = errors.name
            } else if (errors.phone) {
                errorMessage.value = errors.phone
            } else {
                errorMessage.value = t('admin.teams.error_adding_member')
            }
        }
    })
}
</script>

<style scoped>
input[type="text"]:focus,
input[type="email"]:focus,
input[type="tel"]:focus {
    outline: none !important;
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}

input[type="checkbox"]:checked {
    background-color: var(--theme-primary) !important;
    border-color: var(--theme-primary) !important;
}

input[type="checkbox"]:focus {
    outline: none !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>