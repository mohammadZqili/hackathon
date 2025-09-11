<script setup>
import { useLocalization } from '@/composables/useLocalization'
import { Head } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const { t, locale, isRTL } = useLocalization()

// Debug: show raw translations
const rawTranslations = computed(() => page.props.translations || {})
const translationKeys = computed(() => Object.keys(rawTranslations.value).slice(0, 20))
</script>

<template>
    <Head title="Translation Test" />
    
    <div class="p-8">
        <h1 class="text-2xl font-bold mb-4">Translation Test Page</h1>
        
        <div class="mb-4">
            <strong>Current Locale:</strong> {{ locale }}
        </div>
        
        <div class="mb-4">
            <strong>Is RTL:</strong> {{ isRTL }}
        </div>
        
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-2">Test Translations:</h2>
            <ul class="space-y-2">
                <li><strong>admin.dashboard.title:</strong> {{ t('admin.dashboard.title') }}</li>
                <li><strong>admin.ideas.title:</strong> {{ t('admin.ideas.title') }}</li>
                <li><strong>admin.teams.title:</strong> {{ t('admin.teams.title') }}</li>
                <li><strong>admin.users.title:</strong> {{ t('admin.users.title') }}</li>
                <li><strong>admin.settings.title:</strong> {{ t('admin.settings.title') }}</li>
            </ul>
        </div>
        
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-2">Available Translation Keys (first 20):</h2>
            <ul class="space-y-1 text-sm">
                <li v-for="key in translationKeys" :key="key">
                    {{ key }}: {{ rawTranslations[key] }}
                </li>
            </ul>
        </div>
        
        <div>
            <h2 class="text-xl font-semibold mb-2">Total Translations Loaded:</h2>
            <p>{{ Object.keys(rawTranslations).length }} keys</p>
        </div>
    </div>
</template>
