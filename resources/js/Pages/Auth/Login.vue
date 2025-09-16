<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted, watch } from 'vue'
import FormInput from '../../Components/FormInput.vue'
import FormCheckbox from '../../Components/FormCheckbox.vue'
import Modal from '../../Components/Modal.vue'
import TestAccountsHelper from '../../Components/TestAccountsHelper.vue'

const page = usePage()
const { settings: { passwordlessLogin = false } = {} } = page.props

// Flash messages from OAuth
const flashError = ref(page.props.flash?.error || null)
const flashSuccess = ref(page.props.flash?.success || null)

// Watch for flash message changes
watch(() => page.props.flash, (newFlash) => {
    flashError.value = newFlash?.error || null
    flashSuccess.value = newFlash?.success || null
})

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const showMagicLinkModal = ref(false)
const magicLinkForm = useForm({
    email: ''
})

// OAuth loading state
const oauthLoading = ref(false)

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
    form.post(route('login'))
}

// Fill login form with test credentials (Development only)
const fillLoginForm = ({ email, password }) => {
    form.email = email
    form.password = password
}

const sendMagicLink = () => {
    magicLinkForm.post(route('magic.login'), {
        onFinish: () => {
            if (!magicLinkForm.hasErrors) {
                showMagicLinkModal.value = false
            }
        }
    })
}

// Handle OAuth login
const handleOAuthLogin = (provider) => {
    oauthLoading.value = true
    
    // Redirect to OAuth provider
    window.location.href = route('auth.provider', { provider: provider })
}
</script>


<template>
    <Head title="Login" />

    <div class="min-h-screen flex" :style="themeStyles">
        <!-- Left Content Area -->
        <div class="flex-1 flex items-center justify-center p-6 bg-gray-50 dark:bg-gray-900">
            <div class="w-full max-w-md">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Login</h2>

                    <!-- Flash Messages -->
                    <div v-if="flashError || flashSuccess" class="mb-6">
                        <div v-if="flashError" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg relative">
                            <span class="block sm:inline">{{ flashError }}</span>
                            <button @click="flashError = null" class="absolute top-0 right-0 px-4 py-3">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                        <div v-if="flashSuccess" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg relative">
                            <span class="block sm:inline">{{ flashSuccess }}</span>
                            <button @click="flashSuccess = null" class="absolute top-0 right-0 px-4 py-3">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Form Fields using FormInput -->
                        <div class="space-y-4">
                            <FormInput
                                v-model="form.email"
                                label="Email"
                                type="email"
                                :required="true"
                                :error="form.errors.email"
                                placeholder="Enter your email address"
                                autocomplete="email"
                            />

                            <FormInput
                                v-model="form.password"
                                label="Password"
                                type="password"
                                :required="true"
                                :error="form.errors.password"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                            />
                        </div>

                        <!-- Remember Me and Forgot Password -->
                        <div class="flex items-center justify-between">
                            <FormCheckbox 
                                v-model="form.remember" 
                                label="Remember me" 
                                name="remember" 
                                id="remember-me" 
                            />
                            <Link :href="route('password.request')" 
                                class="text-sm hover:underline"
                                :style="{ color: themeColor.primary }">
                                Forgot password?
                            </Link>
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
                            {{ form.processing ? 'Signing in...' : 'Login' }}
                        </button>

                        <!-- Social Login Section -->
                        <div class="space-y-4">
                            <div class="text-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">or continue with</span>
                            </div>
                            
                            <div class="flex justify-center space-x-4">
                                <!-- GitHub Login -->
                                <button 
                                    type="button"
                                    @click="handleOAuthLogin('github')"
                                    :disabled="oauthLoading"
                                    class="p-3 rounded-full border border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                    title="Login with GitHub">
                                    <svg class="w-6 h-6 text-gray-700 dark:text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                    </svg>
                                </button>

                                <!-- Google Login -->
                                <button 
                                    type="button"
                                    @click="handleOAuthLogin('google')"
                                    :disabled="oauthLoading"
                                    class="p-3 rounded-full border border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                    title="Login with Google">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24">
                                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Magic Link Option -->
                        <template v-if="passwordlessLogin">
                            <div class="relative">
                                <hr class="border-t border-gray-300 dark:border-gray-600">
                                <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 px-2 bg-white dark:bg-gray-800 text-gray-500">OR</span>
                            </div>

                            <button 
                                type="button" 
                                @click="showMagicLinkModal = true"
                                class="w-full flex items-center justify-center gap-2 py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>Login with magic link</span>
                            </button>
                        </template>
                    </form>

                    <!-- Register Link for mobile -->
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 lg:hidden">
                        <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                            Don't have an account?
                            <Link :href="route('register')" 
                                class="font-semibold hover:underline"
                                :style="{ color: themeColor.primary }">
                                Sign up
                            </Link>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="hidden lg:flex lg:w-[424px] relative flex-col justify-center items-center p-12 text-white"
            :style="{ backgroundColor: themeColor.primary }">
            <!-- Background pattern overlay -->
            <div class="absolute inset-0 opacity-10"
                style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
            </div>
            
            <div class="relative z-10 text-center">
                <h1 class="text-4xl lg:text-5xl font-bold mb-6 leading-tight">
                    Hello, Friend!
                </h1>
                <p class="text-lg mb-8 opacity-90">
                    Enter your personal details and start your journey with us.
                </p>
                <Link :href="route('register')"
                    class="inline-flex items-center justify-center px-8 py-3 border-2 border-white rounded-xl text-white font-semibold hover:bg-white transition-colors duration-200"
                    :class="{ 'hover:text-[var(--theme-primary)]': true }">
                    Sign Up
                </Link>
            </div>
        </div>

        <!-- Footer -->
        <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2">
            <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                @2025 RumanHack. All rights reserved.
            </p>
        </div>
    </div>

    <!-- Magic Link Modal -->
    <Modal v-if="passwordlessLogin" :show="showMagicLinkModal" @close="showMagicLinkModal = false" size="sm">
        <template #title>
            <h2>Login with Magic Link</h2>
        </template>

        <template #default>
            <form @submit.prevent="sendMagicLink" class="space-y-4">
                <FormInput 
                    v-model="magicLinkForm.email" 
                    label="Email address"
                    type="email" 
                    :required="true"
                    :error="magicLinkForm.errors.email"
                    autocomplete="email" 
                />
                <p class="text-sm text-gray-500">
                    We'll send a secure login link to your email address.
                </p>
            </form>
        </template>

        <template #footer>
            <button @click="showMagicLinkModal = false" type="button" class="mr-2 px-4 py-2 text-gray-600 dark:text-gray-300">
                Cancel
            </button>
            <button 
                @click="sendMagicLink" 
                :disabled="magicLinkForm.processing" 
                type="button" 
                class="px-4 py-2 rounded-lg font-semibold text-white"
                :style="{ 
                    background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                }"
                :class="{ 'opacity-50 cursor-not-allowed': magicLinkForm.processing }">
                {{ magicLinkForm.processing ? 'Sending...' : 'Send magic link' }}
            </button>
        </template>
    </Modal>

    <!-- Test Accounts Helper (Development Only) -->
    <TestAccountsHelper @fill-credentials="fillLoginForm" />
</template>

<style scoped>
/* FormInput theme integration */
:deep(.peer:focus) {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 2px rgba(var(--theme-rgb), 0.2) !important;
}

:deep(.error) {
    border-color: #ef4444 !important;
}

/* Custom checkbox styling */
:deep(.text-blue-600) {
    color: var(--theme-primary) !important;
}

:deep(.border-gray-300:checked) {
    background-color: var(--theme-primary) !important;
    border-color: var(--theme-primary) !important;
}
</style>
