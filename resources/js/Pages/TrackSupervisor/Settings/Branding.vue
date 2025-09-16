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
    site_name: props.settings?.site_name || '',
    logo: null,
    primary_color: props.settings?.primary_color || '#3B82F6',
    footer_text: props.settings?.footer_text || ''
})

const handleLogoUpload = (event) => {
    form.logo = event.target.files[0]
}
</script>

<template>
    <Head title="Branding Settings" />
    
    <Default>
        <div class="container mx-auto px-4 py-8">
            
            <div class="max-w-2xl mx-auto">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Branding Settings</h1>

                <form @submit.prevent="form.post(route('track-supervisor.settings.branding.update'))"
                      class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Site Name</label>
                            <input v-model="form.site_name" type="text" required
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Logo</label>
                            <input type="file" @change="handleLogoUpload"
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Primary Color</label>
                            <input v-model="form.primary_color" type="color"
                                   class="mt-1 h-10 w-20" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Footer Text</label>
                            <textarea v-model="form.footer_text" rows="3"
                                      class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a :href="route('track-supervisor.settings.index')"
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
