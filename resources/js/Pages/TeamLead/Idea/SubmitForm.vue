<template>
    <!-- Submit Idea Form based on Figma Design -->
    <div class="w-full overflow-hidden flex flex-col items-start justify-start max-w-[960px]">
        <!-- Page Header -->
        <div class="self-stretch flex flex-row items-start justify-between flex-wrap content-start p-4 text-[32px]">
            <div class="flex flex-col items-start justify-start min-w-[288px]">
                <b class="self-stretch relative leading-10 text-gray-900 dark:text-white">Submit Your Hackathon Idea</b>
            </div>
        </div>
        
        <!-- Track Selection -->
        <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border max-w-[480px]">
            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                    <div class="self-stretch relative leading-6 font-medium text-gray-900 dark:text-white">Track</div>
                </div>
                <div class="self-stretch rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 overflow-hidden flex flex-row items-center justify-between py-2 px-4 gap-0">
                    <select v-model="form.track_id" required
                        class="bg-transparent border-none outline-none flex-1"
                        :style="{ color: themeColor.primary }">
                        <option value="" disabled class="text-gray-400">Select Track</option>
                        <option v-for="track in tracks" :key="track.id" :value="track.id">
                            {{ track.name }}
                        </option>
                    </select>
                    <svg class="w-2 h-3.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Idea Title -->
        <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border max-w-[480px]">
            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                    <div class="self-stretch relative leading-6 font-medium text-gray-900 dark:text-white">Idea Title</div>
                </div>
                <div class="self-stretch rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 box-border h-14 overflow-hidden shrink-0 flex flex-row items-center justify-start p-[15px]">
                    <input v-model="form.title" type="text" placeholder="Enter Idea Title" required
                        class="w-[284px] bg-transparent border-none outline-none text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                        :style="{ color: themeColor.primary }">
                </div>
            </div>
        </div>
        
        <!-- Description -->
        <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border max-w-[480px]">
            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                    <div class="self-stretch relative leading-6 font-medium text-gray-900 dark:text-white">Description</div>
                </div>
                <div class="self-stretch flex-1 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 box-border overflow-hidden min-h-[144px] p-4">
                    <textarea v-model="form.description" placeholder="Describe your idea in detail..." required
                        class="w-full h-full bg-transparent border-none outline-none resize-none text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
                    </textarea>
                </div>
            </div>
        </div>
        
        <!-- Upload Files -->
        <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border max-w-[480px]">
            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                    <div class="self-stretch relative leading-6 font-medium text-gray-900 dark:text-white">Upload Files</div>
                </div>
                <div class="self-stretch flex-1 rounded-xl flex flex-row items-start justify-start">
                    <div class="flex-1 rounded-tl-xl rounded-tr-none rounded-br-none rounded-bl-xl bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 box-border h-14 overflow-hidden flex flex-row items-center justify-start py-[15px] pl-[15px] pr-2">
                        <input ref="fileInput" type="file" multiple @change="handleFileUpload" 
                            accept=".pdf,.ppt,.pptx,.doc,.docx" class="hidden">
                        <div class="w-96 relative leading-6 cursor-pointer text-gray-500 dark:text-gray-400"
                            @click="$refs.fileInput.click()"
                            :style="{ color: themeColor.primary }">
                            {{ uploadedFiles.length ? `${uploadedFiles.length} files selected` : 'Upload files (PDF, PowerPoint)' }}
                        </div>
                    </div>
                    <button @click="$refs.fileInput.click()"
                        class="self-stretch w-10 rounded-tl-none rounded-tr-xl rounded-br-xl rounded-bl-none bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Uploaded Files List -->
        <div v-if="uploadedFiles.length" class="self-stretch h-[47px] flex flex-col items-start justify-start pt-4 px-4 pb-2 box-border text-lg">
            <b class="self-stretch relative leading-[23px] text-gray-900 dark:text-white">Uploaded Files</b>
        </div>
        <div v-for="(file, index) in uploadedFiles" :key="index"
            class="self-stretch bg-gray-50 dark:bg-gray-800 h-14 flex flex-row items-center justify-between py-0 px-4 box-border gap-0 min-h-[56px]">
            <div class="flex-1 overflow-hidden flex flex-col items-start justify-start">
                <div class="self-stretch relative leading-6 overflow-hidden text-ellipsis whitespace-nowrap text-gray-900 dark:text-white">{{ file.name }}</div>
            </div>
            <button @click="removeFile(index)" class="w-7 h-7 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Submit Button -->
        <div class="self-stretch flex flex-row items-start justify-end py-3 px-4 text-center text-white">
            <button @click="submitForm" :disabled="processing"
                class="w-[404px] rounded-xl flex flex-row items-center justify-center py-3 px-4 box-border max-h-[40px] transition-opacity"
                :style="{ background: `linear-gradient(rgba(79, 150, 115, 0.75), rgba(79, 150, 115, 0.75)), ${themeColor.primary}` }"
                :class="{ 'opacity-50 cursor-not-allowed': processing }">
                <b class="relative leading-[14.66px]">
                    {{ processing ? 'Submitting...' : 'Submit Idea' }}
                </b>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    tracks: Array
})

const emit = defineEmits(['submitted'])

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

const form = useForm({
    track_id: '',
    title: '',
    description: '',
    files: []
})

const uploadedFiles = ref([])
const processing = ref(false)

const handleFileUpload = (event) => {
    const files = Array.from(event.target.files)
    uploadedFiles.value.push(...files)
    form.files = uploadedFiles.value
}

const removeFile = (index) => {
    uploadedFiles.value.splice(index, 1)
    form.files = uploadedFiles.value
}

const submitForm = () => {
    if (form.title && form.description && form.track_id) {
        processing.value = true
        form.post(route('team-lead.idea.store'), {
            onSuccess: () => {
                processing.value = false
                emit('submitted')
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
textarea:focus,
select:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>