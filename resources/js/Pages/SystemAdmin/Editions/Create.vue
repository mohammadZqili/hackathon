<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import { ref } from 'vue'

const props = defineProps({
    users: { type: Array, default: () => [] },
    workshops: { type: Array, default: () => [] }
})

const form = useForm({
    name: '',
    year: new Date().getFullYear(),
    slug: '',
    description: '',
    theme: '',
    registration_start_date: '',
    registration_end_date: '',
    idea_submission_start_date: '',
    idea_submission_end_date: '',
    event_start_date: '',
    event_end_date: '',
    location: '',
    status: 'draft',
    is_current: false,
    created_by: ''
})

const associatedWorkshops = ref([])

const submitForm = () => {
    form.post(route('system-admin.editions.store'), {
        onSuccess: () => {
            router.visit(route('system-admin.editions.index'))
        }
    })
}

const addWorkshop = () => {
    // Placeholder for adding workshop functionality
    alert('Add workshop functionality to be implemented')
}
</script>

<template>
    <Head title="Create Edition" />
    
    <Default>
        <div class="flex-1 flex flex-col gap-4">
            <!-- Form Container -->
            <div class="w-full overflow-hidden flex flex-col items-start justify-start max-w-[960px] mx-auto">
                <!-- Header -->
                <div class="self-stretch flex flex-row items-start justify-between flex-wrap content-start p-4">
                    <div class="flex flex-col items-start justify-start gap-3 min-w-[288px]">
                        <div class="w-[421px] flex flex-col items-start justify-start">
                            <h1 class="text-[32px] leading-10 font-bold text-gray-900 dark:text-white font-space-grotesk">Add Edition Details</h1>
                        </div>
                        <div class="flex flex-col items-start justify-start text-sm text-seagreen dark:text-green-400">
                            <div class="self-stretch relative leading-[21px]">Create a new hackathon edition with all the necessary details.</div>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submitForm" class="w-full">
                    <!-- Edition Name -->
                    <div class="self-stretch flex flex-row items-end justify-start flex-wrap content-end py-3 px-4">
                        <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                            <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                                <div class="self-stretch relative leading-6 font-medium text-gray-900 dark:text-white">Edition Name</div>
                            </div>
                            <input v-model="form.name" 
                                   type="text" 
                                   required
                                   class="self-stretch rounded-xl bg-mintcream-100 dark:bg-gray-800 border-honeydew dark:border-gray-700 border-solid border-[1px] box-border h-14 overflow-hidden shrink-0 flex flex-row items-center justify-start p-[15px] text-seagreen dark:text-green-400 focus:outline-none focus:ring-2 focus:ring-seagreen dark:focus:ring-green-400"
                                   placeholder="Enter edition name" />
                            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                        </div>
                    </div>

                    <!-- Year and Hackathon Admin -->
                    <div class="flex flex-row items-start justify-start">
                        <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border max-w-[480px]">
                            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                                <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                                    <div class="self-stretch relative leading-6 font-medium text-gray-900 dark:text-white">Year</div>
                                </div>
                                <input v-model="form.year" 
                                       type="number" 
                                       required 
                                       min="2020" 
                                       max="2030"
                                       class="self-stretch rounded-xl bg-mintcream-100 dark:bg-gray-800 border-honeydew dark:border-gray-700 border-solid border-[1px] box-border h-14 overflow-hidden shrink-0 flex flex-row items-center justify-start p-[15px] text-seagreen dark:text-green-400 focus:outline-none focus:ring-2 focus:ring-seagreen dark:focus:ring-green-400" />
                                <div v-if="form.errors.year" class="text-red-500 text-sm mt-1">{{ form.errors.year }}</div>
                            </div>
                        </div>
                        <div class="w-[480px] flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border gap-4 max-w-[480px]">
                            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                                <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                                    <div class="self-stretch relative leading-6 font-medium text-gray-900 dark:text-white">Hackathon Admin</div>
                                </div>
                                <select v-model="form.created_by"
                                        class="w-full rounded-xl bg-mintcream-200 dark:bg-gray-700 h-14 overflow-hidden shrink-0 flex flex-row items-center justify-between p-4 text-seagreen dark:text-green-400 focus:outline-none focus:ring-2 focus:ring-seagreen dark:focus:ring-green-400">
                                    <option value="">Select Admin</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Registration Dates and Idea Submission Dates -->
                    <div class="flex flex-row items-start justify-start">
                        <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border gap-4 max-w-[480px]">
                            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                                <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                                    <div class="self-stretch relative leading-6 font-medium text-gray-900 dark:text-white">Registration Start Date</div>
                                </div>
                                <input v-model="form.registration_start_date" 
                                       type="date" 
                                       required
                                       class="self-stretch rounded-xl bg-mintcream-100 dark:bg-gray-800 border-honeydew dark:border-gray-700 border-solid border-[1px] box-border h-14 overflow-hidden shrink-0 flex flex-row items-center justify-start p-[15px] text-seagreen dark:text-green-400 focus:outline-none focus:ring-2 focus:ring-seagreen dark:focus:ring-green-400" />
                                <div v-if="form.errors.registration_start_date" class="text-red-500 text-sm mt-1">{{ form.errors.registration_start_date }}</div>
                            </div>
                            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                                <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                                    <div class="self-stretch relative leading-6 font-medium text-gray-900 dark:text-white">Registration End Date</div>
                                </div>
                                <input v-model="form.registration_end_date" 
                                       type="date" 
                                       required
                                       class="self-stretch rounded-xl bg-mintcream-100 dark:bg-gray-800 border-honeydew dark:border-gray-700 border-solid border-[1px] box-border h-14 overflow-hidden shrink-0 flex flex-row items-center justify-start p-[15px] text-seagreen dark:text-green-400 focus:outline-none focus:ring-2 focus:ring-seagreen dark:focus:ring-green-400" />
                                <div v-if="form.errors.registration_end_date" class="text-red-500 text-sm mt-1">{{ form.errors.registration_end_date }}</div>
                            </div>
                        </div>
                        <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border gap-4 max-w-[480px]">
                            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                                <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                                    <div class="self-stretch relative leading-6 font-medium text-gray-900 dark:text-white">Idea Submission Start Date</div>
                                </div>
                                <input v-model="form.idea_submission_start_date" 
                                       type="date" 
                                       required
                                       class="self-stretch rounded-xl bg-mintcream-100 dark:bg-gray-800 border-honeydew dark:border-gray-700 border-solid border-[1px] box-border h-14 overflow-hidden shrink-0 flex flex-row items-center justify-start p-[15px] text-seagreen dark:text-green-400 focus:outline-none focus:ring-2 focus:ring-seagreen dark:focus:ring-green-400" />
                                <div v-if="form.errors.idea_submission_start_date" class="text-red-500 text-sm mt-1">{{ form.errors.idea_submission_start_date }}</div>
                            </div>
                            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                                <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                                    <div class="self-stretch relative leading-6 font-medium text-gray-900 dark:text-white">Idea Submission End Date</div>
                                </div>
                                <input v-model="form.idea_submission_end_date" 
                                       type="date" 
                                       required
                                       class="self-stretch rounded-xl bg-mintcream-100 dark:bg-gray-800 border-honeydew dark:border-gray-700 border-solid border-[1px] box-border h-14 overflow-hidden shrink-0 flex flex-row items-center justify-start p-[15px] text-seagreen dark:text-green-400 focus:outline-none focus:ring-2 focus:ring-seagreen dark:focus:ring-green-400" />
                                <div v-if="form.errors.idea_submission_end_date" class="text-red-500 text-sm mt-1">{{ form.errors.idea_submission_end_date }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Event Dates -->
                    <div class="flex flex-row items-start justify-start">
                        <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border gap-4 max-w-[480px]">
                            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                                <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                                    <div class="self-stretch relative leading-6 font-medium text-gray-900 dark:text-white">Event Start Date</div>
                                </div>
                                <input v-model="form.event_start_date" 
                                       type="date" 
                                       required
                                       class="self-stretch rounded-xl bg-mintcream-100 dark:bg-gray-800 border-honeydew dark:border-gray-700 border-solid border-[1px] box-border h-14 overflow-hidden shrink-0 flex flex-row items-center justify-start p-[15px] text-seagreen dark:text-green-400 focus:outline-none focus:ring-2 focus:ring-seagreen dark:focus:ring-green-400" />
                                <div v-if="form.errors.event_start_date" class="text-red-500 text-sm mt-1">{{ form.errors.event_start_date }}</div>
                            </div>
                            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                                <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                                    <div class="self-stretch relative leading-6 font-medium text-gray-900 dark:text-white">Event End Date</div>
                                </div>
                                <input v-model="form.event_end_date" 
                                       type="date" 
                                       required
                                       class="self-stretch rounded-xl bg-mintcream-100 dark:bg-gray-800 border-honeydew dark:border-gray-700 border-solid border-[1px] box-border h-14 overflow-hidden shrink-0 flex flex-row items-center justify-start p-[15px] text-seagreen dark:text-green-400 focus:outline-none focus:ring-2 focus:ring-seagreen dark:focus:ring-green-400" />
                                <div v-if="form.errors.event_end_date" class="text-red-500 text-sm mt-1">{{ form.errors.event_end_date }}</div>
                            </div>
                        </div>
                        <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border gap-4 max-w-[480px]">
                            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                                <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                                    <div class="self-stretch relative leading-6 font-medium text-gray-900 dark:text-white">Location</div>
                                </div>
                                <input v-model="form.location" 
                                       type="text"
                                       class="self-stretch rounded-xl bg-mintcream-100 dark:bg-gray-800 border-honeydew dark:border-gray-700 border-solid border-[1px] box-border h-14 overflow-hidden shrink-0 flex flex-row items-center justify-start p-[15px] text-seagreen dark:text-green-400 focus:outline-none focus:ring-2 focus:ring-seagreen dark:focus:ring-green-400"
                                       placeholder="Enter location" />
                                <div v-if="form.errors.location" class="text-red-500 text-sm mt-1">{{ form.errors.location }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Description and Theme -->
                    <div class="self-stretch flex flex-row items-end justify-start flex-wrap content-end py-3 px-4">
                        <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                            <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                                <div class="self-stretch relative leading-6 font-medium text-gray-900 dark:text-white">Theme</div>
                            </div>
                            <input v-model="form.theme" 
                                   type="text"
                                   class="self-stretch rounded-xl bg-mintcream-100 dark:bg-gray-800 border-honeydew dark:border-gray-700 border-solid border-[1px] box-border h-14 overflow-hidden shrink-0 flex flex-row items-center justify-start p-[15px] text-seagreen dark:text-green-400 focus:outline-none focus:ring-2 focus:ring-seagreen dark:focus:ring-green-400"
                                   placeholder="Enter theme (optional)" />
                        </div>
                    </div>

                    <div class="self-stretch flex flex-row items-end justify-start flex-wrap content-end py-3 px-4">
                        <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                            <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                                <div class="self-stretch relative leading-6 font-medium text-gray-900 dark:text-white">Description</div>
                            </div>
                            <textarea v-model="form.description" 
                                      rows="4"
                                      class="self-stretch rounded-xl bg-mintcream-100 dark:bg-gray-800 border-honeydew dark:border-gray-700 border-solid border-[1px] box-border overflow-hidden shrink-0 flex flex-row items-start justify-start p-[15px] text-seagreen dark:text-green-400 focus:outline-none focus:ring-2 focus:ring-seagreen dark:focus:ring-green-400 resize-none"
                                      placeholder="Enter description (optional)"></textarea>
                        </div>
                    </div>

                    <!-- Status and Current Flag -->
                    <div class="flex flex-row items-start justify-start py-3 px-4 gap-8">
                        <div class="flex items-center gap-2">
                            <input v-model="form.is_current" 
                                   type="checkbox"
                                   id="is_current"
                                   class="w-5 h-5 text-seagreen bg-mintcream-100 border-honeydew rounded focus:ring-seagreen dark:focus:ring-green-400 dark:bg-gray-800 dark:border-gray-700" />
                            <label for="is_current" class="text-gray-900 dark:text-white font-medium">Set as Current Edition</label>
                        </div>
                    </div>

                    <!-- Associated Workshops -->
                    <div class="self-stretch h-[60px] flex flex-col items-start justify-start pt-5 px-4 pb-3 box-border">
                        <h2 class="self-stretch relative text-[22px] leading-7 font-bold text-gray-900 dark:text-white">Associated Workshops</h2>
                    </div>
                    
                    <div class="self-stretch flex flex-col items-start justify-start py-3 px-4 text-sm">
                        <div class="self-stretch rounded-xl bg-mintcream-100 dark:bg-gray-800 border-honeydew dark:border-gray-700 border-solid border-[1px] overflow-hidden">
                            <div class="flex-1 flex flex-col items-start justify-start">
                                <!-- Table Header -->
                                <div class="self-stretch flex-1 bg-mintcream-100 dark:bg-gray-800 flex flex-row items-start justify-start">
                                    <div class="self-stretch w-[242px] flex flex-col items-start justify-start py-3 px-4 box-border">
                                        <div class="self-stretch relative leading-[21px] font-medium text-gray-900 dark:text-white">Workshop Name</div>
                                    </div>
                                    <div class="self-stretch w-[228px] flex flex-col items-start justify-start py-3 px-4 box-border">
                                        <div class="self-stretch relative leading-[21px] font-medium text-gray-900 dark:text-white">Date</div>
                                    </div>
                                    <div class="self-stretch w-[226px] flex flex-col items-start justify-start py-3 px-4 box-border">
                                        <div class="self-stretch relative leading-[21px] font-medium text-gray-900 dark:text-white">Time</div>
                                    </div>
                                    <div class="self-stretch w-[229px] flex flex-col items-start justify-start py-3 px-4 box-border">
                                        <div class="self-stretch relative leading-[21px] font-medium text-gray-900 dark:text-white">Admin</div>
                                    </div>
                                </div>
                                
                                <!-- Table Rows -->
                                <div class="self-stretch flex flex-col items-start justify-start text-seagreen dark:text-green-400">
                                    <div v-for="(workshop, index) in associatedWorkshops" :key="index" 
                                         class="self-stretch border-gainsboro dark:border-gray-700 border-solid border-t-[1px] box-border h-[72px] flex flex-row items-start justify-start">
                                        <div class="w-[242px] h-[72px] flex flex-col items-center justify-center py-2 px-4 box-border text-gray-900 dark:text-white">
                                            <div class="self-stretch relative leading-[21px]">{{ workshop.name || `Workshop ${index + 1}` }}</div>
                                        </div>
                                        <div class="w-[228px] h-[72px] flex flex-col items-center justify-center py-2 px-4 box-border">
                                            <div class="self-stretch relative leading-[21px]">{{ workshop.date || '2024-07-15' }}</div>
                                        </div>
                                        <div class="w-[226px] h-[72px] flex flex-col items-center justify-center py-2 px-4 box-border">
                                            <div class="self-stretch relative leading-[21px]">{{ workshop.time || '10:00 AM' }}</div>
                                        </div>
                                        <div class="w-[229px] h-[72px] flex flex-col items-center justify-center py-2 px-4 box-border">
                                            <div class="self-stretch relative leading-[21px]">{{ workshop.admin || 'Admin A' }}</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Placeholder rows if no workshops -->
                                    <div v-if="associatedWorkshops.length === 0" 
                                         class="self-stretch border-gainsboro dark:border-gray-700 border-solid border-t-[1px] box-border h-[72px] flex flex-row items-center justify-center">
                                        <div class="text-gray-500 dark:text-gray-400">No workshops associated yet</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add Workshop Button -->
                    <div class="self-stretch flex flex-row items-start justify-start py-3 px-4 text-center text-sm">
                        <button type="button"
                                @click="addWorkshop"
                                class="rounded-xl bg-mintcream-200 dark:bg-gray-700 h-10 overflow-hidden flex flex-row items-center justify-center py-0 px-4 box-border min-w-[84px] max-w-[480px] hover:bg-mintcream-300 dark:hover:bg-gray-600 transition-colors">
                            <div class="overflow-hidden flex flex-col items-center justify-start">
                                <span class="self-stretch relative leading-[21px] font-bold overflow-hidden text-ellipsis whitespace-nowrap text-gray-900 dark:text-white">Add Workshop</span>
                            </div>
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <div class="self-stretch flex flex-row items-start justify-end py-3 px-4 gap-3 text-center text-sm">
                        <a :href="route('system-admin.editions.index')"
                           class="rounded-xl bg-gray-200 dark:bg-gray-700 h-10 overflow-hidden flex flex-row items-center justify-center py-0 px-6 box-border min-w-[84px] max-w-[480px] hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                            <div class="overflow-hidden flex flex-col items-center justify-start">
                                <span class="self-stretch relative leading-[21px] font-bold overflow-hidden text-ellipsis whitespace-nowrap text-gray-900 dark:text-white">Cancel</span>
                            </div>
                        </a>
                        <button type="submit"
                                :disabled="form.processing"
                                class="rounded-xl bg-mediumseagreen dark:bg-green-600 h-10 overflow-hidden flex flex-row items-center justify-center py-0 px-6 box-border min-w-[84px] max-w-[480px] hover:bg-seagreen dark:hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <div class="overflow-hidden flex flex-col items-center justify-start">
                                <span class="self-stretch relative leading-[21px] font-bold overflow-hidden text-ellipsis whitespace-nowrap text-white">{{ form.processing ? 'Creating...' : 'Create Edition' }}</span>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Default>
</template>