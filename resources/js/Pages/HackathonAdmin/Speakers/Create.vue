<template>
    <Head title="Add New Speaker" />
    <Default>
        <div class="container mx-auto px-4 py-8">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Add New Speaker</h1>
            </div>

            <!-- Form Content -->
            <form @submit.prevent="submit" class="max-w-6xl">
                <!-- Speaker Name and Job Title Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Speaker Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Speaker Name
                        </label>
                        <input v-model="form.name"
                               type="text"
                               placeholder="Enter speaker name"
                               class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white placeholder-teal-600/50 dark:placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                               required>
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Job Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Job Title
                        </label>
                        <input v-model="form.title"
                               type="text"
                               placeholder="Enter job title"
                               class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white placeholder-teal-600/50 dark:placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Email and Phone Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email
                        </label>
                        <input v-model="form.email"
                               type="email"
                               placeholder="speaker@example.com"
                               class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white placeholder-teal-600/50 dark:placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                               required>
                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Phone
                        </label>
                        <input v-model="form.phone"
                               type="tel"
                               placeholder="+1 234 567 8900"
                               class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white placeholder-teal-600/50 dark:placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Workshops and Affiliated Organization Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Workshops -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Workshops
                        </label>
                        <div class="relative">
                            <select v-model="form.workshop_ids"
                                    multiple
                                    class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent appearance-none"
                                    style="min-height: 56px;">
                                <option v-for="workshop in workshops" :key="workshop.id" :value="workshop.id">
                                    {{ workshop.title }}
                                </option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-teal-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Hold Ctrl/Cmd to select multiple workshops
                        </p>
                    </div>

                    <!-- Affiliated Organization -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Affiliated Organization
                        </label>
                        <div class="relative">
                            <select v-model="form.organization_id"
                                    class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent appearance-none">
                                <option value="">Select Organization</option>
                                <option v-for="org in organizations" :key="org.id" :value="org.id">
                                    {{ org.name }}
                                </option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-teal-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Biography and Social Media Links Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Biography -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Biography
                        </label>
                        <textarea v-model="form.bio"
                                  rows="9"
                                  placeholder="Brief biography of the speaker..."
                                  class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white placeholder-teal-600/50 dark:placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-transparent resize-none"></textarea>
                    </div>

                    <!-- Social Media Links -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Social Media Links
                        </label>
                        <div class="space-y-3">
                            <!-- LinkedIn -->
                            <div class="relative">
                                <input v-model="form.linkedin"
                                       type="url"
                                       placeholder="LinkedIn profile URL"
                                       class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white placeholder-teal-600/50 dark:placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-transparent pr-10">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-teal-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Twitter -->
                            <div class="relative">
                                <input v-model="form.twitter"
                                       type="url"
                                       placeholder="Twitter/X profile URL"
                                       class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white placeholder-teal-600/50 dark:placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-transparent pr-10">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-teal-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Website -->
                            <div class="relative">
                                <input v-model="form.website"
                                       type="url"
                                       placeholder="Personal website URL"
                                       class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white placeholder-teal-600/50 dark:placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-transparent pr-10">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-teal-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Picture Upload -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Upload Profile Picture
                    </label>
                    <div class="relative">
                        <div v-if="!previewUrl"
                             @click="$refs.profileInput.click()"
                             @dragover.prevent
                             @drop.prevent="handleDrop"
                             class="rounded-lg border-2 border-dashed border-teal-200 dark:border-gray-600 bg-teal-50/50 dark:bg-gray-800 p-12 text-center cursor-pointer hover:bg-teal-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-teal-400 dark:text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Upload Profile Picture
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    Drag and drop or browse to upload a profile picture
                                </p>
                                <button type="button" class="px-4 py-2 bg-teal-100 dark:bg-gray-700 text-teal-700 dark:text-teal-400 rounded-lg text-sm font-medium hover:bg-teal-200 dark:hover:bg-gray-600 transition-colors">
                                    Browse
                                </button>
                            </div>
                        </div>

                        <div v-else class="relative rounded-lg border-2 border-teal-200 dark:border-gray-600 bg-teal-50/50 dark:bg-gray-800 p-4">
                            <img :src="previewUrl" alt="Profile preview" class="w-32 h-32 mx-auto rounded-full object-cover">
                            <button @click="removeProfile" type="button" class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <input ref="profileInput"
                               @change="handleFileUpload"
                               type="file"
                               accept="image/*"
                               class="hidden">
                    </div>
                </div>

                <!-- Expertise -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Areas of Expertise
                    </label>
                    <input v-model="form.expertise"
                           type="text"
                           placeholder="e.g., AI, Machine Learning, Data Science (comma-separated)"
                           class="w-full rounded-lg bg-teal-50 dark:bg-gray-800 border border-teal-100 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-white placeholder-teal-600/50 dark:placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Separate multiple areas with commas
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between">
                    <Link :href="route('hackathon-admin.speakers.index')"
                          class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </Link>
                    <button type="submit"
                            :disabled="form.processing"
                            class="px-8 py-3 bg-gradient-to-r from-teal-600 to-teal-500 text-white rounded-lg font-semibold hover:from-teal-700 hover:to-teal-600 disabled:opacity-50 transition-all shadow-lg hover:shadow-xl">
                        {{ form.processing ? 'Creating...' : 'Add Speaker' }}
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
    organizations: {
        type: Array,
        default: () => []
    },
    workshops: {
        type: Array,
        default: () => []
    }
})

const form = useForm({
    name: '',
    email: '',
    phone: '',
    title: '',
    organization_id: '',
    workshop_ids: [],
    bio: '',
    expertise: '',
    linkedin: '',
    twitter: '',
    website: '',
    profile_picture: null
})

const previewUrl = ref(null)
const profileInput = ref(null)

const handleFileUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.profile_picture = file
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
        form.profile_picture = file
        const reader = new FileReader()
        reader.onload = (e) => {
            previewUrl.value = e.target.result
        }
        reader.readAsDataURL(file)
    }
}

const removeProfile = () => {
    form.profile_picture = null
    previewUrl.value = null
    if (profileInput.value) {
        profileInput.value.value = ''
    }
}

const submit = () => {
    form.post(route('hackathon-admin.speakers.store'))
}
</script>
