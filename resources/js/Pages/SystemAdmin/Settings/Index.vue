<template>
    <Head title="System Settings" />
    <Default>
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">System Settings</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Configure system-wide settings and integrations.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- SMTP Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 7.89a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-gray-900 dark:text-white">SMTP Configuration</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Configure email server settings for notifications.</p>
                    <a :href="route('system-admin.settings.smtp')" 
                       class="inline-flex items-center text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        Configure SMTP
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <!-- Branding Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z" />
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-gray-900 dark:text-white">Branding</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Customize logos, colors and visual identity.</p>
                    <a :href="route('system-admin.settings.branding')" 
                       class="inline-flex items-center text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300">
                        Configure Branding
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <!-- Twitter Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="bg-cyan-100 dark:bg-cyan-900 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-gray-900 dark:text-white">Twitter Integration</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Connect Twitter for auto-posting news.</p>
                    <a :href="route('system-admin.settings.twitter')" 
                       class="inline-flex items-center text-cyan-600 hover:text-cyan-800 dark:text-cyan-400 dark:hover:text-cyan-300">
                        Configure Twitter
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <!-- SMS Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="bg-green-100 dark:bg-green-900 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-gray-900 dark:text-white">SMS Configuration</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Configure SMS provider for notifications.</p>
                    <a :href="route('system-admin.settings.sms')" 
                       class="inline-flex items-center text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">
                        Configure SMS
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Settings Summary -->
            <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Settings Overview</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ settings.smtp?.mail_host ? '✓' : '✗' }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">SMTP Configured</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ settings.branding?.app_name ? '✓' : '✗' }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Branding Set</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-cyan-600 dark:text-cyan-400">
                            {{ settings.twitter?.twitter_api_key ? '✓' : '✗' }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Twitter Connected</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ settings.sms?.sms_api_key ? '✓' : '✗' }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">SMS Configured</div>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({
            smtp: {},
            branding: {},
            twitter: {},
            sms: {}
        })
    }
})
</script>
