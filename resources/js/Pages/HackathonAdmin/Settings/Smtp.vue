<script setup>
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL, direction, locale } = useLocalization()
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { useForm } from '@inertiajs/vue3'


const props = defineProps({
    settings: Object
})


const form = useForm({
    smtp_host: props.settings?.smtp_host || '',
    smtp_port: props.settings?.smtp_port || 587,
    smtp_username: props.settings?.smtp_username || '',
    smtp_password: '',
    smtp_encryption: props.settings?.smtp_encryption || 'tls',
    smtp_from_address: props.settings?.smtp_from_address || '',
    smtp_from_name: props.settings?.smtp_from_name || ''
})

const testConnection = () => {
    form.post(route('system-admin.settings.smtp.test'))
}
</script>

<template>
    <Head title="SMTP Settings" />
    
    <Default>
        <div class="container mx-auto px-4 py-8">
            
            <div class="max-w-2xl mx-auto">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">SMTP Configuration</h1>

                <form @submit.prevent="form.post(route('system-admin.settings.smtp.update'))"
                      class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">SMTP Host</label>
                            <input v-model="form.smtp_host" type="text" required
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">SMTP Port</label>
                            <input v-model="form.smtp_port" type="number" required
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                            <input v-model="form.smtp_username" type="text"
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                            <input v-model="form.smtp_password" type="password"
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Encryption</label>
                            <select v-model="form.smtp_encryption"
                                    class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="">None</option>
                                <option value="tls">TLS</option>
                                <option value="ssl">SSL</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">From Address</label>
                            <input v-model="form.smtp_from_address" type="email" required
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">From Name</label>
                            <input v-model="form.smtp_from_name" type="text" required
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-between">
                        <button type="button" @click="testConnection"
                                class="px-4 py-2 bg-gray-600 text-white rounded-lg">
                            Test Connection
                        </button>
                        <div class="space-x-3">
                            <a :href="route('system-admin.settings.index')"
                               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Cancel</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                                Save Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </Default>
</template>
