<script setup>
import { useLocalization } from '@/composables/useLocalization'
import { Head } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'

const { t, isRTL, direction, locale } = useLocalization()

const props = defineProps({
    settings: Object
})

// Theme color implementation
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
})

const themeStyles = computed(() => ({
    '--theme-primary': themeColor.value.primary,
    '--theme-hover': themeColor.value.hover,
    '--theme-rgb': themeColor.value.rgb,
    '--theme-gradient-from': themeColor.value.gradientFrom,
    '--theme-gradient-to': themeColor.value.gradientTo,
}))

const form = useForm({
    twitter_api_key: props.settings?.twitter_api_key || '',
    twitter_api_secret: '',
    twitter_access_token: props.settings?.twitter_access_token || '',
    twitter_access_token_secret: '',
    twitter_auto_post: props.settings?.twitter_auto_post || false
})
</script>

<style scoped>
input[type="text"]:focus,
input[type="password"]:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}

input[type="checkbox"]:checked {
    background-color: var(--theme-primary) !important;
    border-color: var(--theme-primary) !important;
}

.rtl {
    direction: rtl;
}
</style>

<template>
    <Head :title="t('admin.settings.twitter_integration')" />
    
    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            
            <div class="max-w-2xl mx-auto" :class="{ 'rtl': isRTL }">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">{{ t('admin.settings.twitter_integration') }}</h1>

                <form @submit.prevent="form.post(route('system-admin.settings.twitter.update'))"
                      class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('admin.settings.twitter_api_key') }}</label>
                            <input v-model="form.twitter_api_key" 
                                   type="text"
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('admin.settings.twitter_api_secret') }}</label>
                            <input v-model="form.twitter_api_secret" 
                                   type="password"
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('admin.settings.twitter_access_token') }}</label>
                            <input v-model="form.twitter_access_token" 
                                   type="text"
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('admin.settings.twitter_access_token_secret') }}</label>
                            <input v-model="form.twitter_access_token_secret" 
                                   type="password"
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                        </div>

                        <div>
                            <label class="flex items-center" :class="{ 'flex-row-reverse justify-end': isRTL }">
                                <input v-model="form.twitter_auto_post" 
                                       type="checkbox" 
                                       :class="isRTL ? 'ml-2' : 'mr-2'" />
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ t('admin.settings.twitter_auto_post') }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3" :class="{ 'flex-row-reverse space-x-reverse': isRTL }">
                        <a :href="route('system-admin.settings.index')"
                           class="px-4 py-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors">
                            {{ t('admin.actions.cancel') }}
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 text-white rounded-lg transition-colors"
                                :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }"
                                :disabled="form.processing">
                            {{ form.processing ? t('admin.actions.saving') : t('admin.settings.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Default>
</template>
