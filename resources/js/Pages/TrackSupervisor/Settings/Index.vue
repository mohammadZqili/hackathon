<template>
    <Head title="System Settings" />
    <Default>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" :style="themeStyles">
            <!-- Success/Error Messages -->
            <div v-if="$page.props.flash.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash.error" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ $page.props.flash.error }}
            </div>

            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Settings</h1>
            </div>

            <!-- Tabs Navigation -->
            <div class="mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button
                            v-for="tab in tabs"
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            :class="[
                                activeTab === tab.id
                                    ? 'border-b-3 font-semibold'
                                    : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600',
                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200'
                            ]"
                            :style="activeTab === tab.id ? { 
                                color: themeColor.primary, 
                                borderBottomColor: themeColor.primary,
                                borderBottomWidth: '3px'
                            } : {}"
                        >
                            {{ tab.name }}
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="mt-6">
                <!-- SMTP Tab -->
                <div v-show="activeTab === 'smtp'" class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">SMTP Settings</h2>
                        
                        <form @submit.prevent="saveSmtpSettings" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Mail Host
                                    </label>
                                    <input
                                        v-model="smtpForm.mail_host"
                                        type="text"
                                        class="w-full rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 transition-all duration-200"
                                        :style="{ '--tw-ring-color': themeColor.primary }"
                                        placeholder="smtp.gmail.com"
                                    />
                                    <div v-if="smtpForm.errors?.mail_host" class="text-red-500 text-sm mt-1">{{ smtpForm.errors.mail_host }}</div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Mail Port
                                    </label>
                                    <input
                                        v-model.number="smtpForm.mail_port"
                                        type="number"
                                        class="w-full rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 transition-all duration-200"
                                        :style="{ '--tw-ring-color': themeColor.primary }"
                                        placeholder="587"
                                    />
                                    <div v-if="smtpForm.errors?.mail_port" class="text-red-500 text-sm mt-1">{{ smtpForm.errors.mail_port }}</div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Mail Username
                                    </label>
                                    <input
                                        v-model="smtpForm.mail_username"
                                        type="text"
                                        class="w-full rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 transition-all duration-200"
                                        :style="{ '--tw-ring-color': themeColor.primary }"
                                        placeholder="your-email@gmail.com"
                                    />
                                    <div v-if="smtpForm.errors?.mail_username" class="text-red-500 text-sm mt-1">{{ smtpForm.errors.mail_username }}</div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Mail Password
                                    </label>
                                    <input
                                        v-model="smtpForm.mail_password"
                                        type="password"
                                        class="w-full rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 transition-all duration-200"
                                        :style="{ '--tw-ring-color': themeColor.primary }"
                                        placeholder="••••••••"
                                    />
                                    <div v-if="smtpForm.errors?.mail_password" class="text-red-500 text-sm mt-1">{{ smtpForm.errors.mail_password }}</div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Mail Encryption
                                    </label>
                                    <select
                                        v-model="smtpForm.mail_encryption"
                                        class="w-full rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 transition-all duration-200"
                                        :style="{ '--tw-ring-color': themeColor.primary }"
                                    >
                                        <option value="tls">TLS</option>
                                        <option value="ssl">SSL</option>
                                        <option value="">None</option>
                                    </select>
                                    <div v-if="smtpForm.errors?.mail_encryption" class="text-red-500 text-sm mt-1">{{ smtpForm.errors.mail_encryption }}</div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        From Address
                                    </label>
                                    <input
                                        v-model="smtpForm.mail_from_address"
                                        type="email"
                                        class="w-full rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 transition-all duration-200"
                                        :style="{ '--tw-ring-color': themeColor.primary }"
                                        placeholder="noreply@yourdomain.com"
                                    />
                                    <div v-if="smtpForm.errors?.mail_from_address" class="text-red-500 text-sm mt-1">{{ smtpForm.errors.mail_from_address }}</div>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="smtpForm.processing"
                                    class="px-6 py-3 rounded-xl text-white font-semibold shadow-sm hover:shadow-md transition-all duration-200 disabled:opacity-50"
                                    :style="{ 
                                        background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` 
                                    }"
                                >
                                    {{ smtpForm.processing ? 'Saving...' : 'Save SMTP Settings' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Branding Tab -->
                <div v-show="activeTab === 'branding'" class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Branding Settings</h2>
                        
                        <form @submit.prevent="saveBrandingSettings" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    App Display Name
                                </label>
                                <input
                                    v-model="brandingForm.app_name"
                                    type="text"
                                    class="w-full max-w-md rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 transition-all duration-200"
                                    :style="{ '--tw-ring-color': themeColor.primary }"
                                    placeholder="Your App Name"
                                />
                                <div v-if="brandingForm.errors?.app_name" class="text-red-500 text-sm mt-1">{{ brandingForm.errors.app_name }}</div>
                            </div>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-3xl">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Primary Color
                                    </label>
                                    <input
                                        v-model="brandingForm.primary_color"
                                        type="text"
                                        class="w-full rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 transition-all duration-200"
                                        :style="{ '--tw-ring-color': themeColor.primary }"
                                        placeholder="#007bff"
                                    />
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Secondary Color
                                    </label>
                                    <input
                                        v-model="brandingForm.secondary_color"
                                        type="text"
                                        class="w-full rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 transition-all duration-200"
                                        :style="{ '--tw-ring-color': themeColor.primary }"
                                        placeholder="#6c757d"
                                    />
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Success Color
                                    </label>
                                    <input
                                        v-model="brandingForm.success_color"
                                        type="text"
                                        class="w-full rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 transition-all duration-200"
                                        :style="{ '--tw-ring-color': themeColor.primary }"
                                        placeholder="#28a745"
                                    />
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Danger Color
                                    </label>
                                    <input
                                        v-model="brandingForm.danger_color"
                                        type="text"
                                        class="w-full rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 transition-all duration-200"
                                        :style="{ '--tw-ring-color': themeColor.primary }"
                                        placeholder="#dc3545"
                                    />
                                </div>
                            </div>
                            
                            <!-- Logo Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Upload Logo
                                </label>
                                <div class="border-2 border-dashed rounded-xl p-8 text-center transition-colors duration-200"
                                     :style="{ borderColor: themeColor.primary + '40' }">
                                    <div class="space-y-4">
                                        <div v-if="brandingForm.logo_preview" class="mb-4">
                                            <img :src="brandingForm.logo_preview" alt="Logo preview" class="mx-auto h-24 object-contain">
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Upload Logo</h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                Click or drag and drop to upload your logo
                                            </p>
                                        </div>
                                        <input
                                            type="file"
                                            @change="handleLogoUpload"
                                            accept="image/*"
                                            class="hidden"
                                            ref="logoInput"
                                        />
                                        <button
                                            type="button"
                                            @click="$refs.logoInput.click()"
                                            class="px-6 py-2 rounded-xl font-medium transition-all duration-200"
                                            :style="{ 
                                                backgroundColor: themeColor.primary + '20',
                                                color: themeColor.primary
                                            }"
                                        >
                                            Upload
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="brandingForm.processing"
                                    class="px-6 py-3 rounded-xl text-white font-semibold shadow-sm hover:shadow-md transition-all duration-200 disabled:opacity-50"
                                    :style="{ 
                                        background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` 
                                    }"
                                >
                                    {{ brandingForm.processing ? 'Saving...' : 'Save Branding Settings' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Notifications Tab -->
                <div v-show="activeTab === 'notifications'" class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Notification Settings</h2>
                        
                        <form @submit.prevent="saveNotificationSettings" class="space-y-6">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                    <div>
                                        <h3 class="font-medium text-gray-900 dark:text-white">Email Notifications</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            Send email notifications for system events
                                        </p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            v-model="notificationForm.email_enabled"
                                            class="sr-only peer"
                                        >
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"
                                             :style="notificationForm.email_enabled ? { backgroundColor: themeColor.primary } : {}">
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                    <div>
                                        <h3 class="font-medium text-gray-900 dark:text-white">Push Notifications</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            Send push notifications to mobile devices
                                        </p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            v-model="notificationForm.push_enabled"
                                            class="sr-only peer"
                                        >
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"
                                             :style="notificationForm.push_enabled ? { backgroundColor: themeColor.primary } : {}">
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                    <div>
                                        <h3 class="font-medium text-gray-900 dark:text-white">In-App Notifications</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            Show notifications within the application
                                        </p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            v-model="notificationForm.in_app_enabled"
                                            class="sr-only peer"
                                        >
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"
                                             :style="notificationForm.in_app_enabled ? { backgroundColor: themeColor.primary } : {}">
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="notificationForm.processing"
                                    class="px-6 py-3 rounded-xl text-white font-semibold shadow-sm hover:shadow-md transition-all duration-200 disabled:opacity-50"
                                    :style="{ 
                                        background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` 
                                    }"
                                >
                                    {{ notificationForm.processing ? 'Saving...' : 'Save Notification Settings' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL, direction, locale } = useLocalization()
import { Head, router, useForm } from '@inertiajs/vue3'
import { ref, onMounted, computed } from 'vue'
import Default from '../../../Layouts/Default.vue'

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({
            smtp: {},
            branding: {},
            sms: {},
            notifications: {}
        })
    }
})

// Theme color configuration
const themeColor = ref({
    primary: '#0d9488',
    hover: '#0f766e',
    rgb: '13, 148, 136',
    gradientFrom: '#0d9488',
    gradientTo: '#14b8a6'
})

onMounted(() => {
    const root = document.documentElement
    const primary = getComputedStyle(root).getPropertyValue('--primary-color').trim() || '#0d9488'
    const hover = getComputedStyle(root).getPropertyValue('--primary-hover').trim() || '#0f766e'
    const rgb = getComputedStyle(root).getPropertyValue('--primary-color-rgb').trim() || '13, 148, 136'
    const gradientFrom = getComputedStyle(root).getPropertyValue('--primary-gradient-from').trim() || '#0d9488'
    const gradientTo = getComputedStyle(root).getPropertyValue('--primary-gradient-to').trim() || '#14b8a6'

    themeColor.value = {
        primary: primary || themeColor.value.primary,
        hover: hover || themeColor.value.hover,
        rgb: rgb || themeColor.value.rgb,
        gradientFrom: gradientFrom || themeColor.value.gradientFrom,
        gradientTo: gradientTo || themeColor.value.gradientTo
    }

    console.log('Settings loaded:', props.settings)
})

const themeStyles = computed(() => ({
    '--theme-primary': themeColor.value.primary,
    '--theme-hover': themeColor.value.hover,
    '--theme-rgb': themeColor.value.rgb,
    '--theme-gradient-from': themeColor.value.gradientFrom,
    '--theme-gradient-to': themeColor.value.gradientTo,
}))

// Tabs configuration
const tabs = [
    { id: 'smtp', name: 'SMTP' },
    // { id: 'sms', name: 'SMS API' }, // Hidden for now
    { id: 'branding', name: 'Branding' },
    { id: 'notifications', name: 'Notifications' }
]

const activeTab = ref('smtp')

// Use Inertia's useForm for proper form handling
const smtpForm = useForm({
    mail_host: props.settings.smtp?.mail_host || '',
    mail_port: props.settings.smtp?.mail_port || 587,
    mail_username: props.settings.smtp?.mail_username || '',
    mail_password: props.settings.smtp?.mail_password || '',
    mail_encryption: props.settings.smtp?.mail_encryption || 'tls',
    mail_from_address: props.settings.smtp?.mail_from_address || ''
})

const brandingForm = useForm({
    app_name: props.settings.branding?.app_name || '',
    primary_color: props.settings.branding?.primary_color || '#0d9488',
    secondary_color: props.settings.branding?.secondary_color || '#14b8a6',
    success_color: props.settings.branding?.success_color || '#10b981',
    danger_color: props.settings.branding?.danger_color || '#ef4444',
    logo: null,
    logo_preview: props.settings.branding?.logo_url || null
})

// Initialize notification form with proper boolean values
const notificationForm = useForm({
    email_enabled: Boolean(props.settings.notifications?.email_enabled),
    sms_enabled: Boolean(props.settings.notifications?.sms_enabled),
    push_enabled: Boolean(props.settings.notifications?.push_enabled),
    in_app_enabled: Boolean(props.settings.notifications?.in_app_enabled)
})

// Log initial values for debugging
console.log('Initial notification settings:', {
    email_enabled: notificationForm.email_enabled,
    sms_enabled: notificationForm.sms_enabled,
    push_enabled: notificationForm.push_enabled,
    in_app_enabled: notificationForm.in_app_enabled
})

// Form submission handlers
const saveSmtpSettings = () => {
    console.log('Submitting SMTP settings:', smtpForm.data())
    smtpForm.post(route('system-admin.settings.smtp.update'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            console.log('SMTP settings saved successfully')
        },
        onError: (errors) => {
            console.error('SMTP settings error:', errors)
        }
    })
}

const saveBrandingSettings = () => {
    console.log('Submitting Branding settings:', brandingForm.data())
    
    // If there's a logo file, we need to use FormData
    if (brandingForm.logo) {
        brandingForm.post(route('system-admin.settings.branding.update'), {
            preserveScroll: true,
            preserveState: true,
            forceFormData: true,
            onSuccess: () => {
                console.log('Branding settings saved successfully')
            },
            onError: (errors) => {
                console.error('Branding settings error:', errors)
            }
        })
    } else {
        // For non-file data, regular post
        brandingForm.transform(data => ({
            ...data,
            logo: undefined,
            logo_preview: undefined
        })).post(route('system-admin.settings.branding.update'), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                console.log('Branding settings saved successfully')
            },
            onError: (errors) => {
                console.error('Branding settings error:', errors)
            }
        })
    }
}

const saveNotificationSettings = () => {
    console.log('Submitting Notification settings:', notificationForm.data())
    
    // Ensure we're sending boolean values correctly
    const data = {
        email_enabled: Boolean(notificationForm.email_enabled),
        sms_enabled: Boolean(notificationForm.sms_enabled),
        push_enabled: Boolean(notificationForm.push_enabled),
        in_app_enabled: Boolean(notificationForm.in_app_enabled)
    }
    
    console.log('Processed notification data:', data)
    
    notificationForm.transform(() => data).post(route('system-admin.settings.notifications.update'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
            console.log('Notification settings saved successfully')
            // Update form with new values from server
            if (page.props.settings?.notifications) {
                notificationForm.email_enabled = Boolean(page.props.settings.notifications.email_enabled)
                notificationForm.sms_enabled = Boolean(page.props.settings.notifications.sms_enabled)
                notificationForm.push_enabled = Boolean(page.props.settings.notifications.push_enabled)
                notificationForm.in_app_enabled = Boolean(page.props.settings.notifications.in_app_enabled)
            }
        },
        onError: (errors) => {
            console.error('Notification settings error:', errors)
        },
        onFinish: () => {
            console.log('Request finished')
        }
    })
}

// File upload handler
const handleLogoUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
        brandingForm.logo = file
        
        // Create preview
        const reader = new FileReader()
        reader.onload = (e) => {
            brandingForm.logo_preview = e.target.result
        }
        reader.readAsDataURL(file)
    }
}
</script>

<style scoped>
input[type="text"]:focus,
input[type="number"]:focus,
input[type="email"]:focus,
input[type="password"]:focus,
select:focus,
textarea:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}

input[type="checkbox"]:checked {
    background-color: var(--theme-primary) !important;
    border-color: var(--theme-primary) !important;
}
</style>
