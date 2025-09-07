<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { useForm } from '@inertiajs/vue3'


const props = defineProps({
    roles: Object
})


const form = useForm({
    name: '',
    email: '',
    password: '',
    role_id: ''
})
</script>

<template>
    <Head title="Create User" />
    
    <Default>
        <div class="container mx-auto px-4 py-8">
            
            <div class="max-w-2xl mx-auto">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Create New User</h1>

                <form @submit.prevent="form.post(route('system-admin.users.store'))"
                      class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                            <input v-model="form.name" type="text" required
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input v-model="form.email" type="email" required
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                            <input v-model="form.password" type="password" required
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                            <select v-model="form.role_id" required
                                    class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="">Select a role</option>
                                <option v-for="role in roles" :key="role.id" :value="role.id">
                                    {{ role.name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a :href="route('system-admin.users.index')"
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Default>
</template>
