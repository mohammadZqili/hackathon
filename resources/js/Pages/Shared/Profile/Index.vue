<template>
    <Head :title="`Profile - ${roleTitle}`" />
    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Profile</h1>
                <p class="text-gray-600 dark:text-gray-400">{{ roleTitle }} Profile Settings</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <form @submit.prevent="updateProfile">
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Personal Information</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                                <input v-model="form.name" type="text" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 dark:bg-gray-700 dark:text-white">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                                <input :value="profile.email" type="email" disabled
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-400">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                                <input v-model="form.phone" type="tel"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 dark:bg-gray-700 dark:text-white">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role</label>
                                <input :value="roleTitle" type="text" disabled
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-400">
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bio</label>
                            <textarea v-model="form.bio" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 dark:bg-gray-700 dark:text-white"></textarea>
                        </div>
                        
                        <div class="mt-6" v-if="role !== 'visitor'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Skills</label>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="skill in form.skills" :key="skill"
                                    class="px-3 py-1 rounded-full text-sm"
                                    :style="{ 
                                        backgroundColor: themeColor.primary + '20',
                                        color: themeColor.primary
                                    }">
                                    {{ skill }}
                                    <button type="button" @click="removeSkill(skill)" class="ml-2">&times;</button>
                                </span>
                                <input v-model="newSkill" @keyup.enter="addSkill" type="text" placeholder="Add skill..."
                                    class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-2 text-white rounded-lg transition-colors"
                            :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue'
import { Head } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'

const props = defineProps({
    profile: Object,
    role: String
})

const roleTitle = computed(() => {
    const titles = {
        'team-lead': 'Team Lead',
        'team-member': 'Team Member',
        'visitor': 'Visitor'
    }
    return titles[props.role] || 'User'
})

const form = reactive({
    name: props.profile?.name || '',
    phone: props.profile?.phone || '',
    bio: props.profile?.bio || '',
    skills: props.profile?.skills || []
})

const newSkill = ref('')

// Theme color setup
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

const addSkill = () => {
    if (newSkill.value && !form.skills.includes(newSkill.value)) {
        form.skills.push(newSkill.value)
        newSkill.value = ''
    }
}

const removeSkill = (skill) => {
    form.skills = form.skills.filter(s => s !== skill)
}

const updateProfile = () => {
    const routeName = `${props.role}.profile.update`
    router.put(route(routeName), form)
}
</script>

<style scoped>
input[type="text"]:focus,
input[type="tel"]:focus,
textarea:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>
