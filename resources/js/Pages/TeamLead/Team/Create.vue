<template>
    <Head title="Create Team - Team Lead" />
    <Default>
        <div class="w-full h-full overflow-hidden bg-gray-50 dark:bg-gray-900" :style="themeStyles">
            <!-- Create Team Form exactly matching Figma Design -->
            <div class="w-full bg-gray-50 dark:bg-gray-900 overflow-hidden flex flex-col items-start justify-start min-h-[800px]">
                <div class="self-stretch flex-1 flex flex-row items-start justify-center py-5 px-40">
                    <div class="w-[960px] h-[760px] overflow-hidden shrink-0 flex flex-col items-start justify-start py-5 px-0 box-border max-w-[960px]">
                        <!-- Page Header -->
                        <div class="self-stretch flex flex-row items-start justify-between flex-wrap content-start p-4 text-[32px] text-gray-900 dark:text-white">
                            <div class="w-72 flex flex-col items-start justify-start min-w-[288px]">
                                <b class="self-stretch relative leading-10">Create Team</b>
                            </div>
                        </div>
                        
                        <!-- Team Name Field -->
                        <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border max-w-[480px] text-base"
                            :style="{ color: themeColor.primary }">
                            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                                <div class="self-stretch rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 box-border h-14 overflow-hidden shrink-0 flex flex-row items-center justify-start p-[15px]">
                                    <input v-model="form.name" type="text" placeholder="Team Name" required
                                        class="w-[190px] relative leading-6 whitespace-pre-wrap inline-block shrink-0 bg-transparent border-none outline-none text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                                        :style="{ color: themeColor.primary }">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hackathon Edition Field -->
                        <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border max-w-[480px] text-base text-gray-900 dark:text-white">
                            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                                <div class="self-stretch rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 box-border h-14 overflow-hidden shrink-0 flex flex-row items-center justify-between relative gap-0">
                                    <select v-model="form.edition_id" required
                                        class="w-[394px] relative leading-6 whitespace-pre-wrap inline-block shrink-0 bg-transparent border-none outline-none text-gray-900 dark:text-white appearance-none px-4">
                                        <option value="" disabled class="text-gray-400">Hackathon Edition</option>
                                        <option v-for="edition in editions" :key="edition.id" :value="edition.id">
                                            {{ edition.name }}
                                        </option>
                                    </select>
                                    <svg class="w-[11px] absolute right-4 h-[21px] object-contain pointer-events-none text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Invite Members Field -->
                        <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border max-w-[480px] text-base"
                            :style="{ color: themeColor.primary }">
                            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                                <div class="self-stretch rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 box-border h-14 overflow-hidden shrink-0 flex flex-row items-center justify-start p-[15px]">
                                    <input v-model="newMemberEmail" type="email" placeholder="Invite Members (Email)"
                                        class="w-[395px] relative leading-6 whitespace-pre-wrap inline-block shrink-0 bg-transparent border-none outline-none text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                                        :style="{ color: themeColor.primary }"
                                        @keydown.enter.prevent="addMemberEmail">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Invited Members List Header -->
                        <div class="self-stretch h-[47px] flex flex-col items-start justify-start pt-4 px-4 pb-2 box-border text-lg text-gray-900 dark:text-white">
                            <b class="self-stretch relative leading-[23px]">Invited Members</b>
                        </div>
                        
                        <!-- Invited Members List -->
                        <div v-for="(email, index) in form.member_emails" :key="index"
                            class="self-stretch bg-gray-50 dark:bg-gray-800 h-14 flex flex-row items-center justify-between py-0 px-4 box-border gap-0 min-h-[56px]">
                            <div class="flex-1 overflow-hidden flex flex-col items-start justify-start">
                                <div class="self-stretch relative leading-6 overflow-hidden text-ellipsis whitespace-nowrap text-gray-900 dark:text-white">{{ email }}</div>
                            </div>
                            <button @click="removeMemberEmail(index)" class="w-7 h-7 flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Create Team Button -->
                        <div class="self-stretch flex flex-row items-start justify-start py-3 px-4 text-center text-sm text-white">
                            <button @click="submitForm" :disabled="processing"
                                class="w-[480px] rounded-xl h-10 overflow-hidden flex flex-row items-center justify-center py-0 px-4 box-border min-w-[84px] max-w-[480px] transition-opacity"
                                :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }"
                                :class="{ 'opacity-50 cursor-not-allowed': processing }">
                                <div class="overflow-hidden flex flex-col items-center justify-start">
                                    <b class="self-stretch relative leading-[21px] overflow-hidden text-ellipsis whitespace-nowrap">
                                        {{ processing ? 'Creating...' : 'Create Team' }}
                                    </b>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue'
import { Head } from '@inertiajs/vue3'
import { router, useForm } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'

const props = defineProps({
    editions: Array,
    tracks: Array
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
    edition_id: '',
    track_id: '',
    member_emails: []
})

const newMemberEmail = ref('')
const processing = ref(false)

const addMemberEmail = () => {
    if (newMemberEmail.value && newMemberEmail.value.includes('@')) {
        if (!form.member_emails.includes(newMemberEmail.value)) {
            form.member_emails.push(newMemberEmail.value)
            newMemberEmail.value = ''
        }
    }
}

const removeMemberEmail = (index) => {
    form.member_emails.splice(index, 1)
}

const submitForm = () => {
    if (form.name && form.edition_id) {
        processing.value = true
        form.post(route('team-lead.team.store'), {
            onSuccess: () => {
                processing.value = false
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
select:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>