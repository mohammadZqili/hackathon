<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Add New Workshop
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
                <!-- Workshop Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Workshop Title *
                    </label>
                    <input v-model="form.title"
                           type="text"
                           placeholder="Enter workshop title"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                           :style="{ '--tw-ring-color': themeColor.primary }"
                           required>
                    <p v-if="form.errors.title" class="mt-1 text-sm text-red-600 dark:text-red-400">
                        {{ form.errors.title }}
                    </p>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea v-model="form.description"
                              rows="3"
                              placeholder="Describe the workshop content and objectives..."
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors resize-none"
                              :style="{ '--tw-ring-color': themeColor.primary }"></textarea>
                </div>

                <!-- Date and Time -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date *
                        </label>
                        <input v-model="form.start_date"
                               type="date"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                               :style="{ '--tw-ring-color': themeColor.primary }"
                               required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Time
                        </label>
                        <input v-model="form.start_time"
                               type="time"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                               :style="{ '--tw-ring-color': themeColor.primary }">
                    </div>
                </div>

                <!-- Duration and Max Attendees -->
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
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                               :style="{ '--tw-ring-color': themeColor.primary }">
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
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                               :style="{ '--tw-ring-color': themeColor.primary }">
                    </div>
                </div>

                <!-- Speaker Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Speaker
                    </label>
                    <select v-model="form.speaker_id"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                            :style="{ '--tw-ring-color': themeColor.primary }">
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
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                            :style="{ '--tw-ring-color': themeColor.primary }">
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
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-opacity-50 transition-colors"
                           :style="{ '--tw-ring-color': themeColor.primary }">
                </div>

                <!-- Workshop Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Workshop Type
                    </label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input v-model="form.type"
                                   type="radio"
                                   value="technical"
                                   class="mr-2"
                                   :style="{ accentColor: themeColor.primary }">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Technical</span>
                        </label>
                        <label class="flex items-center">
                            <input v-model="form.type"
                                   type="radio"
                                   value="business"
                                   class="mr-2"
                                   :style="{ accentColor: themeColor.primary }">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Business</span>
                        </label>
                        <label class="flex items-center">
                            <input v-model="form.type"
                                   type="radio"
                                   value="soft-skills"
                                   class="mr-2"
                                   :style="{ accentColor: themeColor.primary }">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Soft Skills</span>
                        </label>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4 pt-4">
                    <button @click="$emit('close')" type="button"
                            class="px-6 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 rounded-lg text-white font-medium transition-all duration-200 disabled:opacity-50"
                            :style="{
                                background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                            }">
                        {{ form.processing ? 'Adding...' : 'Add Workshop' }}
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
    speakers: {
        type: Array,
        default: () => []
    },
    organizations: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits(['close', 'success'])

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
    form.post(route('track-supervisor.workshops.store'), {
        onSuccess: () => {
            emit('success')
        },
        onError: (errors) => {
            console.error('Error adding workshop:', errors)
        }
    })
}
</script>

<style scoped>
input[type="text"]:focus,
input[type="number"]:focus,
input[type="date"]:focus,
input[type="time"]:focus,
select:focus,
textarea:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>