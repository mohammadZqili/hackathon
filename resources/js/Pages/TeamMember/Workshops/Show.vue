<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { AcademicCapIcon, ClockIcon, MapPinIcon, UsersIcon, ArrowLeftIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    workshop: Object,
    isRegistered: Boolean,
})

const form = useForm({})

const registerForWorkshop = () => {
    form.post(route('team-member.workshops.register', props.workshop.id))
}

const unregisterFromWorkshop = () => {
    form.delete(route('team-member.workshops.unregister', props.workshop.id))
}
</script>

<template>
    <Head :title="workshop?.title || 'Workshop'" />
    
    <Default>
        <div class="max-w-4xl mx-auto">
            <div class="mb-8 flex items-center space-x-4">
                <a :href="route('team-member.workshops.index')" class="text-gray-500 hover:text-gray-700">
                    <ArrowLeftIcon class="w-5 h-5" />
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ workshop?.title }}</h1>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <p class="text-gray-700 dark:text-gray-300 mb-6">{{ workshop?.description }}</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="flex items-center text-sm">
                        <ClockIcon class="w-5 h-5 mr-2 text-gray-400" />
                        <div>
                            <div class="font-medium">Date & Time</div>
                            <div class="text-gray-600 dark:text-gray-400">{{ workshop?.start_time }}</div>
                        </div>
                    </div>
                    <div class="flex items-center text-sm">
                        <MapPinIcon class="w-5 h-5 mr-2 text-gray-400" />
                        <div>
                            <div class="font-medium">Location</div>
                            <div class="text-gray-600 dark:text-gray-400">{{ workshop?.location }}</div>
                        </div>
                    </div>
                    <div class="flex items-center text-sm">
                        <UsersIcon class="w-5 h-5 mr-2 text-gray-400" />
                        <div>
                            <div class="font-medium">Capacity</div>
                            <div class="text-gray-600 dark:text-gray-400">
                                {{ workshop?.registrations_count || 0 }} / {{ workshop?.max_participants }}
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center text-sm">
                        <AcademicCapIcon class="w-5 h-5 mr-2 text-gray-400" />
                        <div>
                            <div class="font-medium">Speaker</div>
                            <div class="text-gray-600 dark:text-gray-400">{{ workshop?.speaker?.name || 'TBD' }}</div>
                        </div>
                    </div>
                </div>

                <div v-if="workshop?.requirements" class="mb-6">
                    <h3 class="font-semibold mb-2">Requirements</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ workshop.requirements }}</p>
                </div>

                <div class="flex justify-end">
                    <button v-if="!isRegistered" @click="registerForWorkshop"
                            class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                        Register for Workshop
                    </button>
                    <button v-else @click="unregisterFromWorkshop"
                            class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                        Cancel Registration
                    </button>
                </div>
            </div>
        </div>
    </Default>
</template>