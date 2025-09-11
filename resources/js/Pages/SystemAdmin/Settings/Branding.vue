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
    site_name: props.settings?.site_name || '',
    logo: null,
    primary_color: props.settings?.primary_color || '#3B82F6',
    footer_text: props.settings?.footer_text || ''
})

const handleLogoUpload = (event) => {
    form.logo = event.target.files[0]
}
</script>

<style scoped>
input[type="text"]:focus,
input[type="file"]:focus,
textarea:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}

.rtl {
    direction: rtl;
}
</style>

<template>
    <Head :title="t('admin.settings.branding')" />
    
    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            
            <div class="max-w-2xl mx-auto" :class="{ 'rtl': isRTL }">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">{{ t('admin.settings.branding') }}</h1>

                <form @submit.prevent="form.post(route('system-admin.settings.branding.update'))"
                      class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('admin.settings.site_name') }}</label>
                            <input v-model="form.site_name" 
                                   type="text" 
                                   required
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('admin.settings.logo') }}</label>
                            <input type="file" 
                                   @change="handleLogoUpload"
                                   accept="image/*"
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:text-white" 
                                   :style="{ 'file:bg': `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('admin.settings.primary_color') }}</label>
                            <input v-model="form.primary_color" 
                                   type="color"
                                   class="mt-1 h-10 w-20 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('admin.settings.footer_text') }}</label>
                            <textarea v-model="form.footer_text" 
                                      rows="3"
                                      class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"></textarea>
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
