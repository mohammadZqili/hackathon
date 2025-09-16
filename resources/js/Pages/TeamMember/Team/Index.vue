<template>
    <Head title="My Team - Team Member" />
    <Default>
        <div class="w-full h-full overflow-hidden bg-gray-50 dark:bg-gray-900" :style="themeStyles">
            <!-- No Team Message -->
            <div v-if="!team" class="flex flex-col items-center justify-center h-96">
                <div class="text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Team Assigned</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ message || 'You are not part of any team yet.' }}</p>
                </div>
            </div>

            <!-- My Team View exactly matching Figma Design -->
            <div v-else class="w-full h-[760px] overflow-hidden shrink-0 flex flex-col items-start justify-start max-w-[960px] text-[32px] text-gray-900 dark:text-white">
                <!-- Team Header -->
                <div class="self-stretch flex flex-row items-start justify-between flex-wrap content-start p-4">
                    <div class="flex flex-col items-start justify-start gap-3 min-w-[288px]">
                        <div class="w-72 flex flex-col items-start justify-start">
                            <b class="self-stretch relative leading-10">
                                <span>Team </span>
                                <span :style="{ color: themeColor.primary }">{{ team.name }}</span>
                            </b>
                        </div>
                        <div class="w-72 flex flex-col items-start justify-start text-sm font-space-grotesk"
                            :style="{ color: themeColor.primary }">
                            <div class="self-stretch relative leading-[21px]">Led by {{ team.leader?.name || 'TBA' }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Members Header -->
                <div class="self-stretch flex flex-col items-start justify-start pt-4 px-4 pb-2 text-lg font-space-grotesk">
                    <b class="self-stretch relative leading-[23px]">Members</b>
                </div>
                
                <!-- Members Table -->
                <div class="self-stretch h-[353px] flex flex-col items-start justify-start py-3 px-4 box-border text-sm font-space-grotesk">
                    <div class="self-stretch flex-1 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 overflow-hidden flex flex-row items-start justify-start">
                        <div class="flex-1 h-[309px] flex flex-col items-start justify-start">
                            <!-- Table Header -->
                            <div class="self-stretch bg-white dark:bg-gray-700 border-b border-gray-300 dark:border-gray-600 flex flex-col items-start justify-start">
                                <div class="self-stretch flex-1 bg-gray-100 dark:bg-gray-600 flex flex-row items-start justify-start">
                                    <div class="self-stretch w-[210px] flex flex-col items-start justify-start py-3 px-4 box-border">
                                        <div class="self-stretch relative leading-[21px] font-medium">Name</div>
                                    </div>
                                    <div class="self-stretch w-[153px] flex flex-col items-start justify-start py-3 px-4 box-border">
                                        <div class="self-stretch relative leading-[21px] font-medium">E-mail</div>
                                    </div>
                                    <div class="self-stretch w-[177px] flex flex-col items-start justify-start py-3 px-4 box-border">
                                        <div class="self-stretch relative leading-[21px] font-medium">#Mobile No</div>
                                    </div>
                                    <div class="self-stretch w-[163px] flex flex-col items-start justify-start py-3 px-4 box-border">
                                        <div class="self-stretch relative leading-[21px] font-medium">Status</div>
                                    </div>
                                    <div class="self-stretch w-[210px] flex flex-col items-start justify-start py-3 px-4 box-border">
                                        <div class="self-stretch relative leading-[21px] font-medium">Role</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Table Body -->
                            <div class="self-stretch flex flex-col items-start justify-start">
                                <div v-for="member in team.members" :key="member.id" 
                                    class="self-stretch flex-1 bg-gray-50 dark:bg-gray-800 flex flex-row items-start justify-start border-b border-gray-200 dark:border-gray-700">
                                    <div class="self-stretch w-[210px] flex flex-col items-start justify-start py-3 px-4 box-border">
                                        <div class="self-stretch relative leading-[21px]">{{ member.name }}</div>
                                    </div>
                                    <div class="self-stretch w-[153px] flex flex-col items-start justify-start py-3 px-4 box-border">
                                        <div class="self-stretch relative leading-[21px]">{{ member.email }}</div>
                                    </div>
                                    <div class="self-stretch w-[177px] flex flex-col items-start justify-start py-3 px-4 box-border">
                                        <div class="self-stretch relative leading-[21px]">{{ member.phone || 'N/A' }}</div>
                                    </div>
                                    <div class="self-stretch w-[163px] flex flex-col items-start justify-start py-3 px-4 box-border text-center">
                                        <div class="w-full rounded-xl h-8 overflow-hidden shrink-0 flex flex-row items-center justify-center py-0 px-4 box-border min-w-[84px] max-w-[480px]"
                                            :class="member.pivot?.status === 'pending' ? 'bg-yellow-200 text-yellow-800' : 'bg-green-200 text-green-800 dark:bg-green-800 dark:text-green-200'">
                                            <div class="overflow-hidden flex flex-col items-center justify-start">
                                                <div class="self-stretch relative leading-[21px] font-medium overflow-hidden text-ellipsis whitespace-nowrap">
                                                    {{ member.pivot?.status === 'pending' ? 'Pending' : 'Active' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="self-stretch w-[210px] flex flex-col items-start justify-start py-3 px-4 box-border">
                                        <div class="self-stretch relative leading-[21px]">
                                            {{ member.id === team.leader_id ? 'Team Leader' : 'Member' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Team Details Section -->
                <div class="self-stretch flex flex-col items-start justify-start p-4">
                    <div class="self-stretch bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-2">
                                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Track</div>
                                <div class="text-base text-gray-900 dark:text-white">{{ team.track?.name || 'Not selected' }}</div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Created</div>
                                <div class="text-base text-gray-900 dark:text-white">{{ formatDate(team.created_at) }}</div>
                            </div>
                        </div>
                        <div v-if="team.track?.description" class="mt-4">
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Track Description</div>
                            <div class="text-sm text-gray-700 dark:text-gray-300">{{ team.track.description }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- No Team State -->
            <div v-else class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Not in a Team</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">You are not currently assigned to any team. Please wait for a team leader to invite you or contact the hackathon organizers.</p>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'

const props = defineProps({
    team: Object,
    message: String
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

const formatDate = (dateString) => {
    if (!dateString) return 'N/A'
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    })
}
</script>

<style scoped>
/* Theme styles are applied via CSS variables */
</style>