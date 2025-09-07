<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { useForm } from '@inertiajs/vue3'


const props = defineProps({
    settings: Object
})


const form = useForm({
    twitter_api_key: props.settings?.twitter_api_key || '',
    twitter_api_secret: '',
    twitter_access_token: props.settings?.twitter_access_token || '',
    twitter_access_token_secret: '',
    twitter_auto_post: props.settings?.twitter_auto_post || false
})
</script>

<template>
    <Head title="Twitter Integration" />
    
    <Default>
        <div class="container mx-auto px-4 py-8">
            
            <div class="max-w-2xl mx-auto">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Twitter Integration</h1>

                <form @submit.prevent="form.post(route('system-admin.settings.twitter.update'))"
                      class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">API Key</label>
                            <input v-model="form.twitter_api_key" type="text"
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">API Secret</label>
                            <input v-model="form.twitter_api_secret" type="password"
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Access Token</label>
                            <input v-model="form.twitter_access_token" type="text"
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Access Token Secret</label>
                            <input v-model="form.twitter_access_token_secret" type="password"
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" />
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input v-model="form.twitter_auto_post" type="checkbox" class="mr-2" />
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    Automatically post news to Twitter
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a :href="route('system-admin.settings.index')"
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Default>
</template>
