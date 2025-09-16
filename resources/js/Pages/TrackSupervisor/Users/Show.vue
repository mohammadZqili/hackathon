<script setup>
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL, direction, locale } = useLocalization()
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'



const props = defineProps({
    user: Object, activityLog: Object
})


</script>

<template>
    <Head title="User Details" />
    
    <Default>
        <div class="container mx-auto px-4 py-8">
            
            <div class="max-w-4xl mx-auto">
                <div class="mb-6 flex justify-between items-center">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">User Details</h1>
                    <a :href="route('track-supervisor.users.edit', user.id)"
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg">Edit User</a>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm text-gray-500">Name</label>
                            <p class="text-lg font-medium">{{ user.name }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Email</label>
                            <p class="text-lg font-medium">{{ user.email }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Role</label>
                            <p class="text-lg font-medium">{{ user.role }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Status</label>
                            <span class="px-3 py-1 rounded-full text-sm"
                                  :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                {{ user.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Activity Log</h2>
                    <div class="space-y-3">
                        <div v-for="activity in activityLog" :key="activity.id"
                             class="border-l-2 border-gray-200 pl-4 py-2">
                            <p class="text-sm font-medium">{{ activity.description }}</p>
                            <p class="text-xs text-gray-500">{{ activity.created_at }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>
