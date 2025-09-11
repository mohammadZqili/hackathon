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
    <Head :title="t('admin.users.title')" />
    
    <Default>
        <div class="container mx-auto px-4 py-8">
            
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white" :class="{ 'text-right': isRTL, 'text-left': !isRTL }">{{ t('admin.users.title') }}</h1>
                <a :href="route('system-admin.users.create')"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    {{ t('admin.users.add_new') }}
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex space-x-4">
                        <input type="text" :placeholder="t('admin.users.search_placeholder')"
                               class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg" :class="{ 'text-right': isRTL, 'text-left': !isRTL }" />
                        <select class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">
                            <option value="">{{ t('admin.users.all_roles') }}</option>
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
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase" :class="{ 'text-right': isRTL, 'text-left': !isRTL }">{{ t('admin.form.name') }}</th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase" :class="{ 'text-right': isRTL, 'text-left': !isRTL }">{{ t('admin.form.email') }}</th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase" :class="{ 'text-right': isRTL, 'text-left': !isRTL }">{{ t('admin.form.role') }}</th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase" :class="{ 'text-right': isRTL, 'text-left': !isRTL }">{{ t('admin.form.status') }}</th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase" :class="{ 'text-left': isRTL, 'text-right': !isRTL }">{{ t('admin.table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="user in users.data" :key="user.id">
                                <td class="px-6 py-4" :class="{ 'text-right': isRTL, 'text-left': !isRTL }">{{ user.name }}</td>
                                <td class="px-6 py-4" :class="{ 'text-right': isRTL, 'text-left': !isRTL }">{{ user.email }}</td>
                                <td class="px-6 py-4" :class="{ 'text-right': isRTL, 'text-left': !isRTL }">
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                        {{ user.role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4" :class="{ 'text-right': isRTL, 'text-left': !isRTL }">
                                    <span class="px-2 py-1 text-xs rounded-full"
                                          :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                        {{ user.is_active ? t('admin.status.active') : t('admin.status.inactive') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 space-x-2" :class="{ 'text-left': isRTL, 'text-right': !isRTL }">
                                    <a :href="route('system-admin.users.edit', user.id)"
                                       class="text-blue-600 hover:text-blue-900">{{ t('admin.actions.edit') }}</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </Default>
</template>
