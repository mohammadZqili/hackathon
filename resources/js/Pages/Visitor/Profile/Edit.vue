<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import PageHeader from '../../../Components/Shared/PageHeader.vue'
import { ref, computed, onMounted } from 'vue'
import { UserIcon, EnvelopeIcon, PhoneIcon, BuildingOfficeIcon, AcademicCapIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    user: Object,
    team: Object,
})

// Theme integration
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

// Profile form
const form = useForm({
    name: props.user?.name || '',
    email: props.user?.email || '',
    phone: props.user?.phone || '',
    bio: props.user?.bio || '',
    organization: props.user?.organization || '',
    title: props.user?.title || '',
    university: props.user?.university || '',
    major: props.user?.major || '',
    graduation_year: props.user?.graduation_year || '',
    skills: props.user?.skills ? props.user.skills.split(',').map(s => s.trim()) : [],
    interests: props.user?.interests ? props.user.interests.split(',').map(s => s.trim()) : [],
})

const newSkill = ref('')
const newInterest = ref('')

const handleSubmit = () => {
    const formData = {
        ...form.data(),
        skills: form.skills.join(', '),
        interests: form.interests.join(', ')
    }
    
    form.transform(() => formData).patch(route('visitor.profile.update'), {
        preserveScroll: true,
    })
}

const addSkill = () => {
    if (newSkill.value.trim() && !form.skills.includes(newSkill.value.trim())) {
        form.skills.push(newSkill.value.trim())
        newSkill.value = ''
    }
}

const removeSkill = (index) => {
    form.skills.splice(index, 1)
}

const addInterest = () => {
    if (newInterest.value.trim() && !form.interests.includes(newInterest.value.trim())) {
        form.interests.push(newInterest.value.trim())
        newInterest.value = ''
    }
}

const removeInterest = (index) => {
    form.interests.splice(index, 1)
}

const actionButtons = computed(() => [
    {
        text: 'Save Changes',
        onClick: handleSubmit,
        primary: true,
        loading: form.processing
    }
])
</script>

<template>
    <Head title="Edit Profile" />
    
    <Default>
        <div class="max-w-4xl mx-auto" :style="themeStyles">
            <PageHeader 
                title="Edit Profile" 
                description="Update your personal information and preferences"
                :action-buttons="actionButtons"
            />

            <form @submit.prevent="handleSubmit" class="space-y-8">
                <!-- Basic Information -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <UserIcon class="w-5 h-5 mr-2" :style="{ color: themeColor.primary }" />
                            Basic Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Full Name *
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                    :class="{ 'border-red-300 dark:border-red-600': form.errors.name }"
                                />
                                <div v-if="form.errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Email Address *
                                </label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                    :class="{ 'border-red-300 dark:border-red-600': form.errors.email }"
                                />
                                <div v-if="form.errors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Phone Number
                                </label>
                                <input
                                    v-model="form.phone"
                                    type="tel"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                    :class="{ 'border-red-300 dark:border-red-600': form.errors.phone }"
                                />
                                <div v-if="form.errors.phone" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.phone }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Job Title
                                </label>
                                <input
                                    v-model="form.title"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                    :class="{ 'border-red-300 dark:border-red-600': form.errors.title }"
                                />
                                <div v-if="form.errors.title" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.title }}
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Organization
                                </label>
                                <input
                                    v-model="form.organization"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                    :class="{ 'border-red-300 dark:border-red-600': form.errors.organization }"
                                />
                                <div v-if="form.errors.organization" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.organization }}
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Bio
                                </label>
                                <textarea
                                    v-model="form.bio"
                                    rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                    :class="{ 'border-red-300 dark:border-red-600': form.errors.bio }"
                                    placeholder="Tell us about yourself..."
                                ></textarea>
                                <div v-if="form.errors.bio" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.bio }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <AcademicCapIcon class="w-5 h-5 mr-2" :style="{ color: themeColor.primary }" />
                            Academic Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    University
                                </label>
                                <input
                                    v-model="form.university"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                    :class="{ 'border-red-300 dark:border-red-600': form.errors.university }"
                                />
                                <div v-if="form.errors.university" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.university }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Major
                                </label>
                                <input
                                    v-model="form.major"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                    :class="{ 'border-red-300 dark:border-red-600': form.errors.major }"
                                />
                                <div v-if="form.errors.major" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.major }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Graduation Year
                                </label>
                                <input
                                    v-model="form.graduation_year"
                                    type="number"
                                    min="2020"
                                    max="2030"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                    :class="{ 'border-red-300 dark:border-red-600': form.errors.graduation_year }"
                                />
                                <div v-if="form.errors.graduation_year" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.graduation_year }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Skills & Interests -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <BuildingOfficeIcon class="w-5 h-5 mr-2" :style="{ color: themeColor.primary }" />
                            Skills & Interests
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Skills -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Skills
                            </label>
                            <div class="flex space-x-2 mb-3">
                                <input
                                    v-model="newSkill"
                                    type="text"
                                    placeholder="Add a skill"
                                    class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm"
                                    @keyup.enter="addSkill"
                                />
                                <button
                                    type="button"
                                    @click="addSkill"
                                    class="px-4 py-2 rounded-lg text-white text-sm transition-colors"
                                    :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                                    Add
                                </button>
                            </div>
                            <div v-if="form.skills.length > 0" class="flex flex-wrap gap-2">
                                <span v-for="(skill, index) in form.skills" :key="index" 
                                      class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                    {{ skill }}
                                    <button type="button" @click="removeSkill(index)" class="ml-2 hover:text-red-600">
                                        ×
                                    </button>
                                </span>
                            </div>
                        </div>

                        <!-- Interests -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Interests
                            </label>
                            <div class="flex space-x-2 mb-3">
                                <input
                                    v-model="newInterest"
                                    type="text"
                                    placeholder="Add an interest"
                                    class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm"
                                    @keyup.enter="addInterest"
                                />
                                <button
                                    type="button"
                                    @click="addInterest"
                                    class="px-4 py-2 rounded-lg text-white text-sm transition-colors"
                                    :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                                    Add
                                </button>
                            </div>
                            <div v-if="form.interests.length > 0" class="flex flex-wrap gap-2">
                                <span v-for="(interest, index) in form.interests" :key="index" 
                                      class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                    {{ interest }}
                                    <button type="button" @click="removeInterest(index)" class="ml-2 hover:text-red-600">
                                        ×
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Team Information (if applicable) -->
                <div v-if="team" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <UserIcon class="w-5 h-5 mr-2" :style="{ color: themeColor.primary }" />
                            Team Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center mr-4">
                                    <UserIcon class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ team.name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ team.members_count || 0 }} members</p>
                                    <p v-if="team.track" class="text-sm text-blue-600 dark:text-blue-400">Track: {{ team.track.name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Last updated: {{ new Date(user?.updated_at || Date.now()).toLocaleDateString() }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <a :href="route('visitor.dashboard')" 
                                   class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    Cancel
                                </a>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-4 py-2 rounded-lg text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                                    <span v-if="form.processing">Saving...</span>
                                    <span v-else>Save Changes</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </Default>
</template>

<style scoped>
input[type="text"]:focus,
input[type="email"]:focus,
input[type="tel"]:focus,
input[type="number"]:focus,
select:focus,
textarea:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>