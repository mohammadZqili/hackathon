<template>
    <Head title="Workshops Management" />
    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <div class="flex flex-row items-start justify-between flex-wrap content-start p-4 gap-x-0 gap-y-3">
                <div class="w-72 flex flex-col items-start justify-start min-w-[288px]">
                    <h1 class="text-[32px] font-bold text-gray-900 dark:text-white leading-10">Workshops</h1>
                </div>
                <button @click="handleAddAction"
                        class="rounded-xl h-8 overflow-hidden flex flex-row items-center justify-center py-0 px-4 min-w-[84px] max-w-[480px] text-center text-sm text-white font-medium transition-all duration-200 hover:shadow-md"
                        :style="{
                            background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                        }">
                    <div class="overflow-hidden flex flex-col items-center justify-start">
                        <div class="self-stretch relative leading-[21px] font-medium overflow-hidden text-ellipsis whitespace-nowrap">
                            {{ activeTab === 'workshops' ? 'Add Workshop' : activeTab === 'speakers' ? 'Add Speaker' : 'Add Organization' }}
                        </div>
                    </div>
                </button>
            </div>

            <!-- Tabs Navigation -->
            <div class="flex flex-col items-start justify-start">
                <div class="w-full border-b border-gray-200 dark:border-gray-700 flex flex-row items-start justify-start px-4 gap-8">
                    <button @click="activeTab = 'workshops'"
                            :class="[
                                'border-b-[3px] flex flex-col items-center justify-center pt-4 pb-[13px] transition-all duration-200',
                                activeTab === 'workshops' ? 'text-gray-900 dark:text-white border-solid' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 border-transparent'
                            ]"
                            :style="activeTab === 'workshops' ? { borderColor: themeColor.primary } : {}">
                        <span class="text-sm font-bold">Workshops</span>
                    </button>
                    <button @click="activeTab = 'speakers'"
                            :class="[
                                'border-b-[3px] flex flex-col items-center justify-center pt-4 pb-[13px] transition-all duration-200',
                                activeTab === 'speakers' ? 'text-gray-900 dark:text-white border-solid' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 border-transparent'
                            ]"
                            :style="activeTab === 'speakers' ? { borderColor: themeColor.primary } : {}">
                        <span class="text-sm font-bold">Speakers</span>
                    </button>
                    <button @click="activeTab = 'organizations'"
                            :class="[
                                'border-b-[3px] flex flex-col items-center justify-center pt-4 pb-[13px] transition-all duration-200',
                                activeTab === 'organizations' ? 'text-gray-900 dark:text-white border-solid' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 border-transparent'
                            ]"
                            :style="activeTab === 'organizations' ? { borderColor: themeColor.primary } : {}">
                        <span class="text-sm font-bold">Organizations</span>
                    </button>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="flex flex-col items-start justify-start py-3 px-4">
                <div class="self-stretch h-12 flex flex-col items-start justify-start min-w-[160px] max-w-2xl">
                    <div class="self-stretch flex-1 rounded-xl flex flex-row items-start justify-start">
                        <div class="self-stretch w-10 rounded-tl-xl rounded-tr-none rounded-br-none rounded-bl-xl flex items-center justify-center"
                             :style="{ backgroundColor: themeColor.primary + '20' }">
                            <svg class="w-5 h-5" :style="{ color: themeColor.primary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <div class="self-stretch flex-1 rounded-tl-none rounded-tr-xl rounded-br-xl rounded-bl-none bg-gray-100 dark:bg-gray-700 overflow-hidden flex flex-row items-center justify-start py-2 pl-2 pr-4">
                            <input v-model="searchQuery"
                                   @input="handleSearch"
                                   type="text"
                                   :placeholder="`Search ${activeTab}`"
                                   class="w-full bg-transparent text-gray-700 dark:text-gray-300 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Workshops Tab Content -->
            <div v-if="activeTab === 'workshops'" class="flex flex-col items-start justify-start py-3 px-4">
                <div class="self-stretch rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <!-- Table Header -->
                    <div class="flex flex-row items-start justify-start" :style="{ backgroundColor: themeColor.primary + '10' }">
                        <div class="flex-1 py-3 px-4">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Title</span>
                        </div>
                        <div class="w-48 py-3 px-4">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Description</span>
                        </div>
                        <div class="w-32 py-3 px-4">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Date</span>
                        </div>
                        <div class="w-36 py-3 px-4">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Speaker</span>
                        </div>
                        <div class="w-40 py-3 px-4">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Organization</span>
                        </div>
                        <div class="w-24 py-3 px-4">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Seats</span>
                        </div>
                        <div class="w-32 py-3 px-4 text-center">
                            <span class="text-sm font-medium" :style="{ color: themeColor.primary }">Actions</span>
                        </div>
                    </div>

                    <!-- Table Body -->
                    <div v-if="!workshops.data || workshops.data.length === 0" class="text-center py-12">
                        <p class="text-gray-500 dark:text-gray-400">No workshops found. Click "Add Workshop" to create your first workshop.</p>
                    </div>
                    <div v-else class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div v-for="workshop in workshops.data" :key="workshop.id"
                             class="flex flex-row hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex-1 py-4 px-4">
                                <span class="text-sm text-gray-900 dark:text-white font-medium">{{ workshop.title || 'Untitled' }}</span>
                            </div>
                            <div class="w-48 py-4 px-4">
                                <span class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ workshop.description || 'No description' }}</span>
                            </div>
                            <div class="w-32 py-4 px-4">
                                <span class="text-sm" :style="{ color: themeColor.primary }">{{ formatDate(workshop.start_date) }}</span>
                            </div>
                            <div class="w-36 py-4 px-4">
                                <span class="text-sm" :style="{ color: themeColor.primary }">{{ workshop.speakers?.[0]?.name || 'TBD' }}</span>
                            </div>
                            <div class="w-40 py-4 px-4">
                                <span class="text-sm" :style="{ color: themeColor.primary }">{{ workshop.organization?.name || 'TBD' }}</span>
                            </div>
                            <div class="w-24 py-4 px-4">
                                <div class="flex items-center gap-1">
                                    <span class="text-sm font-medium" :style="{ color: themeColor.primary }">{{ workshop.current_attendees || 0 }}</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">/{{ workshop.max_attendees || 50 }}</span>
                                </div>
                            </div>
                            <div class="w-32 py-4 px-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="viewWorkshop(workshop)"
                                            class="font-bold hover:underline transition-colors text-sm"
                                            :style="{ color: themeColor.primary }">
                                        View
                                    </button>
                                    <span :style="{ color: themeColor.primary }">|</span>
                                    <button @click="editWorkshop(workshop)"
                                            class="font-bold hover:underline transition-colors text-sm"
                                            :style="{ color: themeColor.primary }">
                                        Edit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Speakers Tab Content -->
            <div v-if="activeTab === 'speakers'" class="flex flex-col items-start justify-start py-3 px-4">
                <div class="self-stretch rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <!-- Table Header -->
                    <div class="flex flex-row items-start justify-start" :style="{ backgroundColor: themeColor.primary + '10' }">
                        <div class="w-64 py-3 px-4">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Name</span>
                        </div>
                        <div class="w-48 py-3 px-4">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</span>
                        </div>
                        <div class="w-40 py-3 px-4">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Mobile</span>
                        </div>
                        <div class="flex-1 py-3 px-4">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Bio</span>
                        </div>
                        <div class="w-32 py-3 px-4">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Organization</span>
                        </div>
                        <div class="w-32 py-3 px-4 text-center">
                            <span class="text-sm font-medium" :style="{ color: themeColor.primary }">Actions</span>
                        </div>
                    </div>

                    <!-- Table Body -->
                    <div v-if="!speakers.data || speakers.data.length === 0" class="text-center py-12">
                        <p class="text-gray-500 dark:text-gray-400">No speakers found. Click "Add Speaker" to add your first speaker.</p>
                    </div>
                    <div v-else class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div v-for="speaker in speakers.data" :key="speaker.id"
                             class="flex flex-row hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="w-64 py-4 px-4">
                                <span class="text-sm text-gray-900 dark:text-white font-medium">{{ speaker.name }}</span>
                            </div>
                            <div class="w-48 py-4 px-4">
                                <span class="text-sm" :style="{ color: themeColor.primary }">{{ speaker.email }}</span>
                            </div>
                            <div class="w-40 py-4 px-4">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ speaker.phone || 'N/A' }}</span>
                            </div>
                            <div class="flex-1 py-4 px-4">
                                <span class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ speaker.bio || 'No bio available' }}</span>
                            </div>
                            <div class="w-32 py-4 px-4">
                                <span class="text-sm" :style="{ color: themeColor.primary }">{{ speaker.organization?.name || 'N/A' }}</span>
                            </div>
                            <div class="w-32 py-4 px-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="editSpeaker(speaker)"
                                            class="font-bold hover:underline transition-colors text-sm"
                                            :style="{ color: themeColor.primary }">
                                        Edit
                                    </button>
                                    <span :style="{ color: themeColor.primary }">|</span>
                                    <button @click="deleteSpeaker(speaker)"
                                            class="font-bold hover:underline transition-colors text-sm"
                                            :style="{ color: themeColor.primary }">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Organizations Tab Content -->
            <div v-if="activeTab === 'organizations'" class="flex flex-col items-start justify-start py-3 px-4">
                <div class="self-stretch rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <!-- Table Header -->
                    <div class="flex flex-row items-start justify-start" :style="{ backgroundColor: themeColor.primary + '10' }">
                        <div class="w-64 py-3 px-4">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Name</span>
                        </div>
                        <div class="flex-1 py-3 px-4">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Description</span>
                        </div>
                        <div class="w-48 py-3 px-4">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Website</span>
                        </div>
                        <div class="w-40 py-3 px-4">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Contact</span>
                        </div>
                        <div class="w-32 py-3 px-4 text-center">
                            <span class="text-sm font-medium" :style="{ color: themeColor.primary }">Actions</span>
                        </div>
                    </div>

                    <!-- Table Body -->
                    <div v-if="!organizations.data || organizations.data.length === 0" class="text-center py-12">
                        <p class="text-gray-500 dark:text-gray-400">No organizations found. Click "Add Organization" to add your first organization.</p>
                    </div>
                    <div v-else class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div v-for="org in organizations.data" :key="org.id"
                             class="flex flex-row hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="w-64 py-4 px-4">
                                <span class="text-sm text-gray-900 dark:text-white font-medium">{{ org.name }}</span>
                            </div>
                            <div class="flex-1 py-4 px-4">
                                <span class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ org.description || 'No description' }}</span>
                            </div>
                            <div class="w-48 py-4 px-4">
                                <a :href="org.website" target="_blank" 
                                   class="text-sm hover:underline"
                                   :style="{ color: themeColor.primary }">
                                    {{ org.website || 'N/A' }}
                                </a>
                            </div>
                            <div class="w-40 py-4 px-4">
                                <span class="text-sm" :style="{ color: themeColor.primary }">{{ org.contact_email || 'N/A' }}</span>
                            </div>
                            <div class="w-32 py-4 px-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="editOrganization(org)"
                                            class="font-bold hover:underline transition-colors text-sm"
                                            :style="{ color: themeColor.primary }">
                                        Edit
                                    </button>
                                    <span :style="{ color: themeColor.primary }">|</span>
                                    <button @click="deleteOrganization(org)"
                                            class="font-bold hover:underline transition-colors text-sm"
                                            :style="{ color: themeColor.primary }">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </Default>
</template>

<script setup>
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL, direction, locale } = useLocalization()
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import Default from '../../../Layouts/Default.vue'

const props = defineProps({
    workshops: {
        type: Object,
        default: () => ({ data: [] })
    },
    speakers: {
        type: Object,
        default: () => ({ data: [] })
    },
    organizations: {
        type: Object,
        default: () => ({ data: [] })
    }
})

const activeTab = ref('workshops')
const searchQuery = ref('')

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

const formatDate = (date) => {
    if (!date) return 'TBD'
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    })
}

const handleSearch = () => {
    router.get(route('system-admin.workshops.index'), {
        search: searchQuery.value,
        tab: activeTab.value
    }, {
        preserveState: true,
        preserveScroll: true
    })
}

const handleAddAction = () => {
    if (activeTab.value === 'workshops') {
        router.visit(route('system-admin.workshops.create'))
    } else if (activeTab.value === 'speakers') {
        router.visit(route('system-admin.speakers.create'))
    } else if (activeTab.value === 'organizations') {
        router.visit(route('system-admin.organizations.create'))
    }
}

const viewWorkshop = (workshop) => {
    router.visit(route('system-admin.workshops.show', workshop.id))
}

const editWorkshop = (workshop) => {
    router.visit(route('system-admin.workshops.edit', workshop.id))
}

const editSpeaker = (speaker) => {
    router.visit(route('system-admin.speakers.edit', speaker.id))
}

const deleteSpeaker = (speaker) => {
    if (confirm(`Are you sure you want to delete speaker "${speaker.name}"?`)) {
        useForm({}).delete(route('system-admin.speakers.destroy', speaker.id))
    }
}

const editOrganization = (org) => {
    router.visit(route('system-admin.organizations.edit', org.id))
}

const deleteOrganization = (org) => {
    if (confirm(`Are you sure you want to delete organization "${org.name}"?`)) {
        useForm({}).delete(route('system-admin.organizations.destroy', org.id))
    }
}

</script>

<style scoped>
input[type="text"]:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>