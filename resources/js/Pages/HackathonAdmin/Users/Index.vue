<script setup>
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL, direction, locale } = useLocalization()
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'



const props = defineProps({
    users: Object, filters: Object, roles: Object
})


</script>

<template>
    <Head title="User Management" />

    <Default>
        <div class="container mx-auto px-4 py-8">

            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Users</h1>
                <a :href="route('hackathon-admin.users.create')"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Add New User
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex space-x-4">
                        <input type="text" placeholder="Search users..."
                               class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg" />
                        <select class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">
                            <option value="">All Roles</option>
                            <option v-for="role in roles" :key="role.id" :value="role.id">
                                {{ role.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="user in users.data" :key="user.id">
                                <td class="px-6 py-4">{{ user.name }}</td>
                                <td class="px-6 py-4">{{ user.email }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                        {{ user.role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full"
                                          :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                        {{ user.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a :href="route('hackathon-admin.users.edit', user.id)"
                                       class="text-blue-600 hover:text-blue-900">Edit</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </Default>
</template>
