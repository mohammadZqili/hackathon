<template>
    <Head :title="t('admin.teams.add_member')" />
    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ t('admin.teams.add_team_member') }}
                        </h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            {{ t('admin.teams.add_member_to_team', { team: team.name }) }}
                        </p>
                    </div>
                    <Link :href="route('system-admin.teams.edit', team.id)"
                          class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ t('admin.actions.back') }}
                    </Link>
                </div>
            </div>

            <!-- Main Content Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-8 max-w-2xl mx-auto">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Team Info Display -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4 mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ t('admin.teams.adding_to_team') }}</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ team.name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ t('admin.teams.current_members') }}</p>
                                <p class="text-lg font-semibold" :style="{ color: themeColor.primary }">
                                    {{ team.members_count || team.members?.length || 0 }} / {{ team.max_members }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Full Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                            {{ t('admin.teams.full_name') }} *
                        </label>
                        <input v-model="form.name"
                               type="text"
                               id="name"
                               :placeholder="t('admin.teams.enter_members_full_name')"
                               class="w-full h-14 px-4 rounded-xl bg-gray-100 dark:bg-gray-700 border-0 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-opacity-50 transition-all duration-200"
                               :class="{ 'ring-2 ring-red-500': form.errors.name }"
                               :style="{ '--tw-ring-color': themeColor.primary }"
                               required>
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                            {{ t('admin.teams.email') }} *
                        </label>
                        <input v-model="form.email"
                               type="email"
                               id="email"
                               :placeholder="t('admin.teams.enter_members_email')"
                               class="w-full h-14 px-4 rounded-xl bg-gray-100 dark:bg-gray-700 border-0 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-opacity-50 transition-all duration-200"
                               :class="{ 'ring-2 ring-red-500': form.errors.email }"
                               :style="{ '--tw-ring-color': themeColor.primary }"
                               required>
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
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4">
                        <div class="flex items-start">
                            <input v-model="form.send_invitation"
                                   type="checkbox"
                                   id="send_invitation"
                                   class="mt-1 w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-2 focus:ring-opacity-50"
                                   :style="{
                                       '--tw-text-opacity': '1',
                                       'accent-color': themeColor.primary
                                   }">
                            <div class="ml-3">
                                <label for="send_invitation" class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ t('admin.teams.send_invitation_email') }}
                                </label>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                    {{ t('admin.teams.invitation_email_description') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <Link :href="route('system-admin.teams.edit', team.id)"
                              class="px-6 py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            {{ t('admin.actions.cancel') }}
                        </Link>
                        <button type="submit"
                                :disabled="form.processing || !form.name || !form.email"
                                class="px-6 py-3 rounded-xl text-white font-semibold transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                :style="{
                                    background: form.processing || !form.name || !form.email
                                        ? '#9ca3af'
                                        : `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                                }">
                            <span v-if="form.processing" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ t('admin.actions.sending') }}
                            </span>
                            <span v-else>
                                {{ t('admin.teams.send_invitation') }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Existing Members List -->
            <div v-if="team.members && team.members.length > 0" class="mt-8 bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 max-w-2xl mx-auto">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ t('admin.teams.current_team_members') }}
                </h2>
                <div class="space-y-2">
                    <div v-for="member in team.members" :key="member.id"
                         class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold"
                                 :style="{ backgroundColor: themeColor.primary }">
                                {{ member.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ member.name }}
                                    <span v-if="member.id === team.leader_id"
                                          class="ml-2 text-xs px-2 py-0.5 rounded-full text-white"
                                          :style="{ backgroundColor: themeColor.primary }">
                                        {{ t('admin.teams.leader') }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ member.email }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { useLocalization } from '@/composables/useLocalization'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import Default from '../../../Layouts/Default.vue'

const { t, isRTL, direction, locale } = useLocalization()

const props = defineProps({
    team: {
        type: Object,
        required: true
    }
})

const form = useForm({
    name: '',
    email: '',
    phone: '',
    team_id: props.team.id,
    send_invitation: true
})

// Get theme color from CSS variables
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

const submit = () => {
    form.post(route('system-admin.teams.add-member', props.team.id), {
        onSuccess: () => {
            // Redirect back to edit page after success
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