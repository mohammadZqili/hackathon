<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'



const props = defineProps({
    settings: Object
})


</script>

<template>
    <Head title="System Settings" />
    
    <Default>
        <div class="container mx-auto px-4 py-8">
            
            <div class="max-w-6xl mx-auto">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">System Settings</h1>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <a :href="route('system-admin.settings.smtp')"
                       class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition">
                        <div class="text-blue-600 mb-4">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">SMTP Configuration</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Configure email settings</p>
                    </a>

                    <a :href="route('system-admin.settings.twitter')"
                       class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition">
                        <div class="text-blue-400 mb-4">
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">Twitter Integration</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Connect Twitter account</p>
                    </a>

                    <a :href="route('system-admin.settings.branding')"
                       class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition">
                        <div class="text-purple-600 mb-4">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">Branding</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Customize appearance</p>
                    </a>
                </div>
            </div>
        </div>
    </Default>
</template>
