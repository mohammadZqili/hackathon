<template>
    <Head title="My Workshops - Visitor" />
    <Default>
        <div class="w-full h-full overflow-hidden bg-gray-50 dark:bg-gray-900" :style="themeStyles">
            <!-- My Workshops Page exactly matching Figma Design -->
            <div class="w-full overflow-hidden flex flex-col items-start justify-start max-w-[960px] text-sm font-space-grotesk">
                <!-- Page Header -->
                <div class="self-stretch flex flex-row items-start justify-between flex-wrap content-start p-4 text-[32px] text-gray-900 dark:text-white">
                    <div class="flex flex-col items-start justify-start gap-3 min-w-[288px]">
                        <div class="w-[641px] flex flex-col items-start justify-start">
                            <b class="self-stretch relative leading-10">My Registered Workshops</b>
                        </div>
                        <div class="flex flex-col items-start justify-start text-sm"
                            :style="{ color: themeColor.primary }">
                            <div class="self-stretch relative leading-[21px]">View your registered workshops and access your personal barcode for check-in at each event.</div>
                        </div>
                    </div>
                </div>
                
                <!-- My Workshops List -->
                <div v-if="myWorkshops?.length" class="self-stretch space-y-4">
                    <div v-for="workshop in myWorkshops" :key="workshop.id" 
                        class="self-stretch flex flex-col items-start justify-start p-4">
                        <div class="self-stretch shadow-[0px_0px_4px_rgba(0,_0,_0,_0.1)] rounded-xl bg-gray-50 dark:bg-gray-800 overflow-hidden flex flex-row items-start justify-between p-4 gap-0">
                            <div class="w-[587px] h-[165px] flex flex-col items-start justify-start gap-4">
                                <div class="self-stretch flex flex-col items-start justify-start gap-1">
                                    <div class="self-stretch flex flex-col items-start justify-start text-gray-600 dark:text-gray-400">
                                        <div class="self-stretch relative leading-[21px]">
                                            Speaker: {{ workshop.speaker || 'Dr. Sophia Clark' }} | 
                                            Sponsor: {{ workshop.sponsor || 'Data Insights Inc.' }} | 
                                            Date: {{ formatDate(workshop.date) || 'July 15, 2024' }} | 
                                            Time: {{ workshop.time || '10:00 AM - 12:00 PM' }}
                                        </div>
                                    </div>
                                    <div class="self-stretch h-5 flex flex-col items-start justify-start text-base text-gray-900 dark:text-white">
                                        <b class="self-stretch relative leading-5">{{ workshop.title || 'Mastering Data Analysis with Python' }}</b>
                                    </div>
                                    <div class="self-stretch flex flex-col items-start justify-start text-gray-600 dark:text-gray-400">
                                        <div class="self-stretch relative leading-[21px]">{{ workshop.description || 'Learn to analyze complex datasets using Python, covering data manipulation, visualization, and statistical analysis.' }}</div>
                                    </div>
                                </div>
                                <button @click="viewBarcode(workshop.id)" 
                                    class="rounded-xl h-8 overflow-hidden shrink-0 flex flex-row items-center justify-center py-0 px-4 box-border min-w-[84px] max-w-[480px] text-center text-gray-900 dark:text-white transition-colors bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500">
                                    <div class="overflow-hidden flex flex-col items-center justify-start">
                                        <div class="self-stretch relative leading-[21px] font-medium overflow-hidden text-ellipsis whitespace-nowrap">
                                            View Barcode
                                        </div>
                                    </div>
                                </button>
                            </div>
                            <div class="flex-1 relative rounded-xl max-w-full overflow-hidden h-[165px] bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-800 dark:to-purple-800 flex items-center justify-center">
                                <svg class="w-16 h-16 text-blue-500 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- No Workshops State -->
                <div v-else class="self-stretch flex flex-col items-center justify-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Registered Workshops</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">You haven't registered for any workshops yet.</p>
                    <Link :href="route('visitor.workshops.all')"
                        class="inline-flex items-center px-6 py-3 text-white rounded-lg transition-colors"
                        :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                        Browse All Workshops
                    </Link>
                </div>

                <!-- Barcode Modal -->
                <div v-if="showBarcodeModal" 
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" 
                    @click="showBarcodeModal = false">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-md w-full mx-4" @click.stop>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Workshop Barcode</h3>
                            <button @click="showBarcodeModal = false" 
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-48 h-48 mx-auto bg-white border-2 border-gray-300 rounded-lg flex items-center justify-center mb-4">
                                <!-- Barcode placeholder -->
                                <div class="flex space-x-1">
                                    <div v-for="i in 20" :key="i" 
                                        class="w-1 bg-black"
                                        :style="{ height: Math.random() * 40 + 20 + 'px' }">
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Show this barcode at the workshop check-in</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">Workshop ID: {{ selectedWorkshopId }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'

const props = defineProps({
    myWorkshops: Array
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

const showBarcodeModal = ref(false)
const selectedWorkshopId = ref(null)

const formatDate = (dateString) => {
    if (!dateString) return 'TBA'
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    })
}

const viewBarcode = (workshopId) => {
    selectedWorkshopId.value = workshopId
    showBarcodeModal.value = true
}
</script>

<style scoped>
/* Theme styles are applied via CSS variables */
</style>