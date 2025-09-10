<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Default from '@/Layouts/Default.vue'
import { 
    DocumentArrowUpIcon,
    TrashIcon,
    ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    team: Object,
    tracks: Array,
    maxFileSize: Number,
    maxFiles: Number,
    allowedFileTypes: Array,
})

const form = useForm({
    track_id: '',
    title: '',
    description: '',
    problem_statement: '',
    solution_approach: '',
    expected_impact: '',
    technologies: [''],
    files: [],
})

const fileInput = ref(null)
const uploadedFiles = ref([])

const addTechnology = () => {
    form.technologies.push('')
}

const removeTechnology = (index) => {
    form.technologies.splice(index, 1)
}

const handleFileSelect = (event) => {
    const files = Array.from(event.target.files)
    
    // Check max files
    if (uploadedFiles.value.length + files.length > props.maxFiles) {
        alert(`Maximum ${props.maxFiles} files allowed`)
        return
    }
    
    // Check file types and sizes
    for (const file of files) {
        const extension = file.name.split('.').pop().toLowerCase()
        if (!props.allowedFileTypes.includes(extension)) {
            alert(`File type .${extension} not allowed`)
            continue
        }
        
        if (file.size > props.maxFileSize) {
            alert(`File ${file.name} exceeds maximum size of 15MB`)
            continue
        }
        
        uploadedFiles.value.push(file)
    }
    
    form.files = uploadedFiles.value
    fileInput.value.value = ''
}

const removeFile = (index) => {
    uploadedFiles.value.splice(index, 1)
    form.files = uploadedFiles.value
}

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i]
}

const submit = () => {
    // Filter out empty technologies
    form.technologies = form.technologies.filter(tech => tech.trim() !== '')
    
    form.post(route('team-leader.idea.store'), {
        forceFormData: true,
        onSuccess: () => {
            // Redirect handled by controller
        },
        onError: (errors) => {
            console.error('Form errors:', errors)
        }
    })
}
</script>

<template>
    <Head title="Create Idea" />

    <Default>
        <div class="max-w-4xl mx-auto px-4 py-6">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Submit Your Idea</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Team: {{ team.name }}
                </p>
            </div>

            <!-- Warning if submission period is closing soon -->
            <div v-if="false" class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                <div class="flex">
                    <ExclamationTriangleIcon class="h-5 w-5 text-yellow-400 mr-2" />
                    <div>
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            Submission Deadline Approaching
                        </h3>
                        <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                            The idea submission period ends in 2 days. Make sure to submit your idea before the deadline.
                        </p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Track Selection -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Track Selection</h2>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Select Track <span class="text-red-500">*</span>
                        </label>
                        <select v-model="form.track_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500"
                                required>
                            <option value="">Choose a track...</option>
                            <option v-for="track in tracks" :key="track.id" :value="track.id">
                                {{ track.name }}
                            </option>
                        </select>
                        <div v-if="form.errors.track_id" class="mt-1 text-sm text-red-600">
                            {{ form.errors.track_id }}
                        </div>
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Idea Title <span class="text-red-500">*</span>
                            </label>
                            <input v-model="form.title"
                                   type="text"
                                   maxlength="255"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500"
                                   placeholder="Enter a clear and concise title for your idea"
                                   required />
                            <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                                {{ form.errors.title }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Description <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500">(100-5000 characters)</span>
                            </label>
                            <textarea v-model="form.description"
                                      rows="6"
                                      maxlength="5000"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500"
                                      placeholder="Provide a comprehensive description of your idea..."
                                      required></textarea>
                            <div class="mt-1 text-xs text-gray-500">
                                {{ form.description.length }} / 5000 characters
                            </div>
                            <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                {{ form.errors.description }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detailed Information</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Problem Statement <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500">(50-2000 characters)</span>
                            </label>
                            <textarea v-model="form.problem_statement"
                                      rows="4"
                                      maxlength="2000"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500"
                                      placeholder="What problem does your idea solve?"
                                      required></textarea>
                            <div v-if="form.errors.problem_statement" class="mt-1 text-sm text-red-600">
                                {{ form.errors.problem_statement }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Solution Approach <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500">(50-3000 characters)</span>
                            </label>
                            <textarea v-model="form.solution_approach"
                                      rows="5"
                                      maxlength="3000"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500"
                                      placeholder="How does your solution work?"
                                      required></textarea>
                            <div v-if="form.errors.solution_approach" class="mt-1 text-sm text-red-600">
                                {{ form.errors.solution_approach }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Expected Impact <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500">(50-2000 characters)</span>
                            </label>
                            <textarea v-model="form.expected_impact"
                                      rows="4"
                                      maxlength="2000"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500"
                                      placeholder="What impact will your solution have?"
                                      required></textarea>
                            <div v-if="form.errors.expected_impact" class="mt-1 text-sm text-red-600">
                                {{ form.errors.expected_impact }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Technologies -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Technologies Used</h2>
                    
                    <div class="space-y-3">
                        <div v-for="(tech, index) in form.technologies" :key="index" class="flex gap-2">
                            <input v-model="form.technologies[index]"
                                   type="text"
                                   maxlength="100"
                                   class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500"
                                   placeholder="e.g., Laravel, Vue.js, MySQL" />
                            <button v-if="form.technologies.length > 1"
                                    @click="removeTechnology(index)"
                                    type="button"
                                    class="p-2 text-red-600 hover:text-red-700">
                                <TrashIcon class="w-5 h-5" />
                            </button>
                        </div>
                        <button @click="addTechnology"
                                type="button"
                                class="text-sm text-emerald-600 hover:text-emerald-700">
                            + Add Technology
                        </button>
                    </div>
                </div>

                <!-- File Uploads -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Supporting Documents</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Upload up to {{ maxFiles }} files (PDF, PPT, PPTX, DOC, DOCX, XLS, XLSX) - Max 15MB each
                    </p>
                    
                    <div class="space-y-4">
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                            <DocumentArrowUpIcon class="mx-auto h-12 w-12 text-gray-400" />
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Click to upload or drag and drop
                            </p>
                            <input ref="fileInput"
                                   type="file"
                                   multiple
                                   :accept="allowedFileTypes.map(ext => `.${ext}`).join(',')"
                                   @change="handleFileSelect"
                                   class="hidden" />
                            <button @click="fileInput.click()"
                                    type="button"
                                    class="mt-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg">
                                Select Files
                            </button>
                        </div>

                        <!-- Uploaded Files List -->
                        <div v-if="uploadedFiles.length > 0" class="space-y-2">
                            <div v-for="(file, index) in uploadedFiles" :key="index"
                                 class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <DocumentArrowUpIcon class="w-5 h-5 text-gray-400" />
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ file.name }}</p>
                                        <p class="text-xs text-gray-500">{{ formatFileSize(file.size) }}</p>
                                    </div>
                                </div>
                                <button @click="removeFile(index)"
                                        type="button"
                                        class="text-red-600 hover:text-red-700">
                                    <TrashIcon class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3">
                    <button @click="router.visit(route('team-leader.dashboard'))"
                            type="button"
                            class="px-6 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg">
                        Cancel
                    </button>
                    <button type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg disabled:opacity-50">
                        {{ form.processing ? 'Creating...' : 'Create Idea' }}
                    </button>
                </div>
            </form>
        </div>
    </Default>
</template>
