<template>
    <Head title="Add New Organization" />
    <Default>
        <div class="container mx-auto px-4 py-8">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Add New Organization</h1>
            </div>

            <!-- Form Content -->
            <form @submit.prevent="submit" class="max-w-6xl">
                <!-- Organization Name and Logo Upload Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Organization Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Organization Name
                        </label>
                        <input v-model="form.name"
                               type="text"
                               placeholder="Enter organization name"
                               class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white placeholder-teal-600/50 dark:placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                               required>
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Logo Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Upload Organization Logo
                        </label>
                        <div class="relative">
                            <div v-if="!previewUrl"
                                 @click="$refs.logoInput.click()"
                                 @dragover.prevent
                                 @drop.prevent="handleDrop"
                                 class="rounded-lg border-2 border-dashed border-teal-200 dark:border-gray-600 bg-teal-50/50 dark:bg-gray-800 p-8 text-center cursor-pointer hover:bg-teal-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-teal-400 dark:text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-1">
                                        Drag and drop or browse to upload an organization logo
                                    </p>
                                    <button type="button" class="px-4 py-2 bg-teal-100 dark:bg-gray-700 text-teal-700 dark:text-teal-400 rounded-lg text-sm font-medium hover:bg-teal-200 dark:hover:bg-gray-600 transition-colors">
                                        Browse
                                    </button>
                                </div>
                            </div>

                            <div v-else class="relative rounded-lg border-2 border-teal-200 dark:border-gray-600 bg-teal-50/50 dark:bg-gray-800 p-4">
                                <img :src="previewUrl" alt="Logo preview" class="w-full h-32 object-contain">
                                <button @click="removeLogo" type="button" class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <input ref="logoInput"
                                   @change="handleFileUpload"
                                   type="file"
                                   accept="image/*"
                                   class="hidden">
                        </div>
                    </div>
                </div>

                <!-- Description and Associated Speakers Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea v-model="form.description"
                                  rows="6"
                                  placeholder="Brief description about the organization..."
                                  class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white placeholder-teal-600/50 dark:placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-transparent resize-none"></textarea>
                    </div>

                    <!-- Associated Speakers -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Associated Speakers
                        </label>
                        <div class="relative">
                            <select v-model="form.speaker_ids"
                                    multiple
                                    class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent appearance-none"
                                    style="min-height: 144px;">
                                <option v-for="speaker in speakers" :key="speaker.id" :value="speaker.id">
                                    {{ speaker.name }}
                                </option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-teal-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Hold Ctrl/Cmd to select multiple speakers
                        </p>
                    </div>
                </div>

                <!-- Website and Further Information Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Website -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Website
                        </label>
                        <div class="relative">
                            <input v-model="form.website"
                                   type="url"
                                   placeholder="https://domain.com"
                                   class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white placeholder-teal-600/50 dark:placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-transparent pr-10">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-teal-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Further Information -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Further Information
                        </label>
                        <textarea v-model="form.further_info"
                                  rows="6"
                                  placeholder="Any additional information..."
                                  class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white placeholder-teal-600/50 dark:placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-transparent resize-none"></textarea>
                    </div>
                </div>

                <!-- Additional Fields Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Contact Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Contact Email
                        </label>
                        <input v-model="form.email"
                               type="email"
                               placeholder="contact@organization.com"
                               class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white placeholder-teal-600/50 dark:placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                               required>
                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Phone Number
                        </label>
                        <input v-model="form.phone"
                               type="tel"
                               placeholder="+1 234 567 8900"
                               class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white placeholder-teal-600/50 dark:placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Industry Type and Partnership Level Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Industry/Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Industry/Type
                        </label>
                        <select v-model="form.type"
                                class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                            <option value="technology">Technology</option>
                            <option value="education">Education</option>
                            <option value="finance">Finance</option>
                            <option value="healthcare">Healthcare</option>
                            <option value="manufacturing">Manufacturing</option>
                            <option value="retail">Retail</option>
                            <option value="nonprofit">Non-Profit</option>
                            <option value="government">Government</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Partnership Level -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Partnership Level
                        </label>
                        <select v-model="form.partnership_level"
                                class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                            <option value="">Select Level</option>
                            <option value="platinum">Platinum</option>
                            <option value="gold">Gold</option>
                            <option value="silver">Silver</option>
                            <option value="bronze">Bronze</option>
                            <option value="partner">Partner</option>
                            <option value="supporter">Supporter</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between">
                    <Link :href="route('hackathon-admin.organizations.index')"
                          class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </Link>
                    <button type="submit"
                            :disabled="form.processing"
                            class="px-8 py-3 bg-gradient-to-r from-teal-600 to-teal-500 text-white rounded-lg font-semibold hover:from-teal-700 hover:to-teal-600 disabled:opacity-50 transition-all shadow-lg hover:shadow-xl">
                        {{ form.processing ? 'Creating...' : 'Add Organization' }}
                    </button>
                </div>
            </form>
        </div>
    </Default>
</template>

<script setup>
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL, direction, locale } = useLocalization()
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import Default from '../../../Layouts/Default.vue'

const props = defineProps({
    speakers: {
        type: Array,
        default: () => []
    }
})

const form = useForm({
    name: '',
    email: '',
    phone: '',
    website: '',
    type: 'technology',
    description: '',
    further_info: '',
    partnership_level: '',
    speaker_ids: [],
    logo: null
})

const previewUrl = ref(null)
const logoInput = ref(null)

const handleFileUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.logo = file
        const reader = new FileReader()
        reader.onload = (e) => {
            previewUrl.value = e.target.result
        }
        reader.readAsDataURL(file)
    }
}

const handleDrop = (event) => {
    const file = event.dataTransfer.files[0]
    if (file && file.type.startsWith('image/')) {
        form.logo = file
        const reader = new FileReader()
        reader.onload = (e) => {
            previewUrl.value = e.target.result
        }
        reader.readAsDataURL(file)
    }
}

const removeLogo = () => {
    form.logo = null
    previewUrl.value = null
    if (logoInput.value) {
        logoInput.value.value = ''
    }
}

const submit = () => {
    form.post(route('hackathon-admin.organizations.store'))
}
</script>
