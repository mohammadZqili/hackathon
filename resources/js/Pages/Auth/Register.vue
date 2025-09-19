<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import FormInput from '../../Components/FormInput.vue'

// Get URL parameters for team invitation
const urlParams = new URLSearchParams(window.location.search)
const invitationToken = urlParams.get('invitation')
const invitationEmail = urlParams.get('email')

const form = useForm({
    name: '',
    email: invitationEmail || '', // Pre-fill email from invitation
    national_id: '',
    phone: '',
    password: '',
    password_confirmation: '',
    user_type: invitationToken ? 'team_member' : 'team_leader', // Default to team_member if invitation exists
    occupation: 'student', // Default occupation
    job_title: '',
    invitation_token: invitationToken || '', // Include invitation token
})

const { settings: { passwordlessLogin = false } = {} } = usePage().props

// Get theme color from localStorage or default
const themeColor = ref({
    primary: '#0d9488',
    hover: '#0f766e',
    rgb: '13, 148, 136',
    gradientFrom: '#0d9488',
    gradientTo: '#14b8a6'
})

onMounted(() => {
    // Get the current theme color from CSS variables
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

// Computed style for dynamic theme
const themeStyles = computed(() => ({
    '--theme-primary': themeColor.value.primary,
    '--theme-hover': themeColor.value.hover,
    '--theme-rgb': themeColor.value.rgb,
    '--theme-gradient-from': themeColor.value.gradientFrom,
    '--theme-gradient-to': themeColor.value.gradientTo,
}))

const submit = () => {
    form.post(route('register'))
}
</script>


<template>
    <Head title="Register" />

    <div class="min-h-screen flex" :style="themeStyles">
        <!-- Left Sidebar -->
        <div class="hidden lg:flex lg:w-[424px] relative flex-col justify-center items-center p-12 text-white"
            :style="{ backgroundColor: themeColor.primary }">
            <!-- Background pattern overlay -->
            <div class="absolute inset-0 opacity-10"
                style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
            </div>
            
            <div class="relative z-10 text-center">
                <h1 class="text-4xl lg:text-5xl font-bold mb-6 leading-tight">
                    Welcome Back!
                </h1>
                <p class="text-lg mb-8 opacity-90">
                    If you Already Have An Account, Please Sign in
                </p>
                <Link :href="route('login')"
                    class="inline-flex items-center justify-center px-8 py-3 border-2 border-white rounded-xl text-white font-semibold hover:bg-white transition-colors duration-200"
                    :class="{ 'hover:text-[var(--theme-primary)]': true }">
                    Login
                </Link>
            </div>
        </div>

        <!-- Right Content Area -->
        <div class="flex-1 flex items-center justify-center p-6 bg-gray-50 dark:bg-gray-900">
            <div class="w-full max-w-xl">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Register</h2>

                    <!-- Invitation Notice -->
                    <div v-if="invitationToken" class="mb-6 p-4 rounded-lg border-2 border-dashed"
                        :style="{ borderColor: themeColor.primary + '50', backgroundColor: themeColor.primary + '10' }">
                        <p class="text-sm font-medium" :style="{ color: themeColor.primary }">
                            🎉 You've been invited to join a team!
                        </p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                            Complete your registration to automatically join the team.
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Role Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Select Your Role
                            </label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="relative cursor-pointer">
                                    <input type="radio" v-model="form.user_type" value="team_leader" class="sr-only">
                                    <div class="custom-radio-option"
                                        :class="form.user_type === 'team_leader' ? 'selected' : ''">
                                        <div class="custom-radio-circle"
                                            :class="form.user_type === 'team_leader' ? 'selected' : ''">
                                            <div v-if="form.user_type === 'team_leader'" 
                                                class="custom-radio-dot"
                                                :style="{ backgroundColor: themeColor.primary }"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Team Leader</span>
                                    </div>
                                </label>

                                <label class="relative cursor-pointer">
                                    <input type="radio" v-model="form.user_type" value="team_member" class="sr-only">
                                    <div class="custom-radio-option"
                                        :class="form.user_type === 'team_member' ? 'selected' : ''">
                                        <div class="custom-radio-circle"
                                            :class="form.user_type === 'team_member' ? 'selected' : ''">
                                            <div v-if="form.user_type === 'team_member'" 
                                                class="custom-radio-dot"
                                                :style="{ backgroundColor: themeColor.primary }"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Team Member</span>
                                    </div>
                                </label>

                                <label class="relative cursor-pointer">
                                    <input type="radio" v-model="form.user_type" value="visitor" class="sr-only">
                                    <div class="custom-radio-option"
                                        :class="form.user_type === 'visitor' ? 'selected' : ''">
                                        <div class="custom-radio-circle"
                                            :class="form.user_type === 'visitor' ? 'selected' : ''">
                                            <div v-if="form.user_type === 'visitor'" 
                                                class="custom-radio-dot"
                                                :style="{ backgroundColor: themeColor.primary }"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Visitor</span>
                                    </div>
                                </label>
                            </div>
                            <div v-if="form.errors.user_type" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.user_type }}
                            </div>
                        </div>

                        <!-- Form Fields using FormInput -->
                        <div class="space-y-4">
                            <FormInput
                                v-model="form.name"
                                label="Name"
                                type="text"
                                :required="true"
                                :error="form.errors.name"
                                placeholder="Enter your full name"
                            />

                            <FormInput
                                v-model="form.email"
                                label="Email"
                                type="email"
                                :required="true"
                                :error="form.errors.email"
                                :readonly="!!invitationEmail"
                                :placeholder="invitationEmail ? 'Email from invitation' : 'Enter your email address'"
                            />

                            <FormInput
                                v-model="form.national_id"
                                label="National ID"
                                type="text"
                                :required="true"
                                :error="form.errors.national_id"
                                placeholder="Enter your national ID"
                            />

                            <FormInput
                                v-model="form.phone"
                                label="Mobile Number"
                                type="tel"
                                :required="true"
                                :error="form.errors.phone"
                                placeholder="Enter your mobile number"
                            />

                            <FormInput
                                v-model="form.password"
                                label="Password"
                                type="password"
                                :required="true"
                                :error="form.errors.password"
                                placeholder="Create a strong password"
                            />

                            <FormInput
                                v-model="form.password_confirmation"
                                label="Confirm Password"
                                type="password"
                                :required="true"
                                :error="form.errors.password_confirmation"
                                placeholder="Confirm your password"
                            />
                        </div>

                        <!-- Occupation Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Occupation
                            </label>
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <label class="relative cursor-pointer">
                                    <input type="radio" v-model="form.occupation" value="student" class="sr-only">
                                    <div class="custom-radio-option"
                                        :class="form.occupation === 'student' ? 'selected' : ''">
                                        <div class="custom-radio-circle"
                                            :class="form.occupation === 'student' ? 'selected' : ''">
                                            <div v-if="form.occupation === 'student'" 
                                                class="custom-radio-dot"
                                                :style="{ backgroundColor: themeColor.primary }"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Student</span>
                                    </div>
                                </label>

                                <label class="relative cursor-pointer">
                                    <input type="radio" v-model="form.occupation" value="employee" class="sr-only">
                                    <div class="custom-radio-option"
                                        :class="form.occupation === 'employee' ? 'selected' : ''">
                                        <div class="custom-radio-circle"
                                            :class="form.occupation === 'employee' ? 'selected' : ''">
                                            <div v-if="form.occupation === 'employee'" 
                                                class="custom-radio-dot"
                                                :style="{ backgroundColor: themeColor.primary }"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Employee</span>
                                    </div>
                                </label>
                            </div>

                            <!-- Job Title Field (conditional) -->
                            <div v-if="form.occupation === 'employee'">
                                <FormInput
                                    v-model="form.job_title"
                                    label="Job Title"
                                    type="text"
                                    :error="form.errors.job_title"
                                    placeholder="Enter your job title"
                                />
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="w-full py-3 px-4 rounded-xl font-semibold text-white transition-all duration-200 transform hover:scale-[1.02]"
                            :style="{ 
                                background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                            }"
                            :class="{ 'opacity-50 cursor-not-allowed': form.processing }">
                            {{ form.processing ? 'Creating account...' : 'Register' }}
                        </button>

                        <!-- Terms -->
                        <p class="text-xs text-center text-gray-500 dark:text-gray-400">
                            By creating an account, you agree to our
                            <a href="#" class="underline hover:text-[var(--theme-primary)]">Terms</a> and
                            <a href="#" class="underline hover:text-[var(--theme-primary)]">Privacy Policy</a>
                        </p>
                    </form>

                    <!-- Login Link for mobile -->
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 lg:hidden">
                        <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                            Already have an account?
                            <Link :href="route('login')" 
                                class="font-semibold hover:underline"
                                :style="{ color: themeColor.primary }">
                                Sign in
                            </Link>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom radio button styling */
.custom-radio-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    border: 2px solid #d1d5db;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
}

.custom-radio-option:hover {
    border-color: #9ca3af;
}

.custom-radio-option.selected {
    border-color: var(--theme-primary);
    background-color: rgba(var(--theme-rgb), 0.1);
}

.custom-radio-circle {
    width: 1rem;
    height: 1rem;
    border: 2px solid #9ca3af;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.custom-radio-circle.selected {
    border-color: var(--theme-primary);
}

.custom-radio-dot {
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 50%;
}

/* Dark mode support */
.dark .custom-radio-option {
    border-color: #4b5563;
}

.dark .custom-radio-option:hover {
    border-color: #6b7280;
}

.dark .custom-radio-option.selected {
    border-color: var(--theme-primary);
    background-color: rgba(var(--theme-rgb), 0.1);
}

.dark .custom-radio-circle {
    border-color: #6b7280;
}

.dark .custom-radio-circle.selected {
    border-color: var(--theme-primary);
}

/* FormInput theme integration */
:deep(.peer:focus) {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 2px rgba(var(--theme-rgb), 0.2) !important;
}

:deep(.error) {
    border-color: #ef4444 !important;
}

/* Screen reader only utility */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}
</style>
