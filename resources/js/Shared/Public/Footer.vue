<script setup>
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const personalisation = page.props.personalisation || {}
</script>

<template>
    <footer
        class="bg-gray-100 border-t border-gray-200 text-gray-500 dark:border-t dark:border-gray-700 dark:bg-gray-800">
        <div class="mx-auto px-2 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center space-x-2 text-xs">
                    <p>Ruman Hackathon Management System</p>
                </div>
                <div class="flex items-center space-x-4 text-xs">
                    <p class="text-gray-400">{{ personalisation.copyright_text || '© ' + new Date().getFullYear() + ' Ruman. All rights reserved.' }}</p>
                </div>
            </div>
        </div>
    </footer>
</template>
