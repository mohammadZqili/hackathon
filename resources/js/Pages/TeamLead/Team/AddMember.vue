<template>
    <Head title="Add Team Member - Team Lead" />
    <Default>
        <div class="w-full h-full overflow-hidden bg-gray-50 dark:bg-gray-900" :style="themeStyles">
            <!-- Add Member Form exactly matching Figma Design -->
            <div class="w-[960px] h-[695px] overflow-hidden shrink-0 flex flex-col items-start justify-start py-5 px-0 box-border max-w-[960px] text-gray-900 dark:text-white font-space-grotesk">
                <!-- Page Header -->
                <div class="self-stretch flex flex-row items-start justify-between flex-wrap content-start p-4 text-[32px]">
                    <div class="w-72 flex flex-col items-start justify-start min-w-[288px]">
                        <b class="self-stretch relative leading-10">Add Team Member</b>
                    </div>
                </div>
                
                <!-- Full Name Field -->
                <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border max-w-[480px] text-base">
                    <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                        <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                            <div class="self-stretch relative leading-6 font-medium">Full Name</div>
                        </div>
                        <div class="self-stretch rounded-xl bg-gray-100 dark:bg-gray-700 h-14 overflow-hidden shrink-0 flex flex-row items-center justify-start p-4 box-border text-gray-500 dark:text-gray-400">
                            <input v-model="form.name" type="text" placeholder="Enter member's full name" required
                                class="w-[382px] relative leading-6 whitespace-pre-wrap inline-block shrink-0 bg-transparent border-none outline-none text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
                        </div>
                    </div>
                </div>
                
                <!-- Email Field -->
                <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border max-w-[480px] text-base">
                    <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                        <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                            <div class="self-stretch relative leading-6 font-medium">Email</div>
                        </div>
                        <div class="self-stretch rounded-xl bg-gray-100 dark:bg-gray-700 h-14 overflow-hidden shrink-0 flex flex-row items-center justify-start p-4 box-border text-gray-500 dark:text-gray-400">
                            <input v-model="form.email" type="email" placeholder="Enter member's email" required
                                class="w-[393px] relative leading-6 whitespace-pre-wrap inline-block shrink-0 bg-transparent border-none outline-none text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
                        </div>
                    </div>
                </div>
                
                <!-- Mobile Number Field -->
                <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border max-w-[480px] text-base">
                    <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                        <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                            <div class="self-stretch relative leading-6 font-medium">Mobile Number</div>
                        </div>
                        <div class="self-stretch rounded-xl bg-gray-100 dark:bg-gray-700 h-14 overflow-hidden shrink-0 flex flex-row items-center justify-start p-4 box-border text-gray-500 dark:text-gray-400">
                            <input v-model="form.phone" type="tel" placeholder="Enter member's mobile Number"
                                class="w-[393px] relative leading-6 whitespace-pre-wrap inline-block shrink-0 bg-transparent border-none outline-none text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
                        </div>
                    </div>
                </div>
                
                <!-- Send Invitation Button -->
                <div class="self-stretch flex flex-row items-start justify-end py-3 px-4 text-center text-white">
                    <button @click="submitForm" :disabled="processing"
                        class="rounded-xl flex flex-row items-center justify-center py-3 px-4 box-border max-h-[40px] transition-opacity"
                        :style="{ background: `linear-gradient(rgba(79, 150, 115, 0.75), rgba(79, 150, 115, 0.75)), ${themeColor.primary}` }"
                        :class="{ 'opacity-50 cursor-not-allowed': processing }">
                        <b class="relative leading-[14.66px]">
                            {{ processing ? 'Sending...' : 'Send Invitation' }}
                        </b>
                    </button>
                </div>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import { router, useForm } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'

const props = defineProps({
    team: Object
})

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

const form = useForm({
    name: '',
    email: '',
    phone: ''
})

const processing = ref(false)

const submitForm = () => {
    if (form.name && form.email) {
        processing.value = true
        form.post(route('team-lead.team.add-member.store'), {
            onSuccess: () => {
                processing.value = false
                form.reset()
            },
            onError: () => {
                processing.value = false
            }
        })
    }
}
</script>

<style scoped>
input[type="text"]:focus,
input[type="email"]:focus,
input[type="tel"]:focus,
select:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>