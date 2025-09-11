<template>
    <Head title="Speakers Management" />
    <Default>
        <div class="container mx-auto px-4 py-8" :style="themeStyles">
            <!-- Page Header -->
            <div class="flex flex-row items-start justify-between flex-wrap content-start p-4 gap-x-0 gap-y-3">
                <div class="w-72 flex flex-col items-start justify-start min-w-[288px]">
                    <h1 class="text-[32px] font-bold text-gray-900 dark:text-white leading-10">Speakers</h1>
                </div>
                <Link :href="route('system-admin.speakers.create')"
                      class="rounded-xl h-8 overflow-hidden flex flex-row items-center justify-center py-0 px-4 min-w-[84px] max-w-[480px] text-center text-sm text-white font-medium transition-all duration-200 hover:shadow-md"
                      :style="{
                          background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`,
                      }">
                    <div class="overflow-hidden flex flex-col items-center justify-start">
                        <div class="self-stretch relative leading-[21px] font-medium overflow-hidden text-ellipsis whitespace-nowrap">Add Speaker</div>
                    </div>
                </Link>
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
                                   placeholder="Search speakers"
                                   class="w-full bg-transparent text-gray-700 dark:text-gray-300 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Speakers Table -->
            <div class="flex flex-col items-start justify-start py-3 px-4">
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
                        <div class="w-48 py-3 px-4">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Organization</span>
                        </div>
                        <div class="w-32 py-3 px-4">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Expertise</span>
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
                                <div class="flex items-center gap-3">
                                    <div v-if="speaker.avatar_url" class="w-10 h-10 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700">
                                        <img :src="speaker.avatar_url" :alt="speaker.name" class="w-full h-full object-cover">
                                    </div>
                                    <div v-else class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold"
                                         :style="{ backgroundColor: themeColor.primary }">
                                        {{ speaker.name ? speaker.name.charAt(0).toUpperCase() : 'S' }}
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-900 dark:text-white font-medium">{{ speaker.name || 'Unnamed' }}</div>
                                        <div v-if="speaker.title" class="text-xs text-gray-500 dark:text-gray-400">{{ speaker.title }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="w-48 py-4 px-4">
                                <span class="text-sm" :style="{ color: themeColor.primary }">{{ speaker.email || 'N/A' }}</span>
                            </div>
                            <div class="w-40 py-4 px-4">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ speaker.phone || 'N/A' }}</span>
                            </div>
                            <div class="flex-1 py-4 px-4">
                                <span class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ speaker.bio || 'No bio available' }}</span>
                            </div>
                            <div class="w-48 py-4 px-4">
                                <span class="text-sm" :style="{ color: themeColor.primary }">{{ speaker.organization?.name || 'Independent' }}</span>
                            </div>
                            <div class="w-32 py-4 px-4">
                                <div v-if="speaker.expertise" class="flex flex-wrap gap-1">
                                    <span v-for="(skill, index) in (speaker.expertise || '').split(',').slice(0, 2)" 
                                          :key="index"
                                          class="text-xs px-2 py-1 rounded-full"
                                          :style="{ 
                                              backgroundColor: themeColor.primary + '20',
                                              color: themeColor.primary
                                          }">
                                        {{ skill.trim() }}
                                    </span>
                                </div>
                                <span v-else class="text-sm text-gray-400">N/A</span>
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

            <!-- Pagination -->
            <div v-if="speakers.links && speakers.total > speakers.per_page" 
                 class="px-6 py-3">
                <nav class="flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Showing {{ speakers.from }} to {{ speakers.to }} of {{ speakers.total }} results
                    </div>
                    <div class="flex items-center gap-2">
                        <template v-for="link in speakers.links" :key="link.label">
                            <Link v-if="link.url"
                                  :href="link.url"
                                  class="px-3 py-1 rounded-md text-sm font-medium transition-colors"
                                  :style="link.active ? {
                                      backgroundColor: themeColor.primary,
                                      color: 'white'
                                  } : {}"
                                  :class="!link.active ? 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 border border-gray-300 dark:border-gray-600' : ''"
                                  v-html="link.label">
                            </Link>
                            <span v-else
                                  class="px-3 py-1 rounded-md text-sm font-medium text-gray-400 dark:text-gray-500"
                                  v-html="link.label">
                            </span>
                        </template>
                    </div>
                </nav>
            </div>

            <!-- Create/Edit Modal -->
            <SpeakerModal v-if="showModal" 
                          :speaker="selectedSpeaker"
                          :organizations="organizations"
                          :theme-color="themeColor"
                          @close="closeModal"
                          @success="handleSuccess" />
        </div>
    </Default>
</template>

<script setup>
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL, direction, locale } = useLocalization()
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import Default from '../../../Layouts/Default.vue'
import SpeakerModal from './SpeakerModal.vue'

const props = defineProps({
    speakers: {
        type: Object,
        default: () => ({ data: [] })
    },
    organizations: {
        type: Array,
        default: () => []
    }
})

const searchQuery = ref('')
const showModal = ref(false)
const selectedSpeaker = ref(null)

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

const handleSearch = () => {
    router.get(route('system-admin.speakers.index'), {
        search: searchQuery.value
    }, {
        preserveState: true,
        preserveScroll: true
    })
}

const editSpeaker = (speaker) => {
    router.visit(route('system-admin.speakers.edit', speaker.id))
}

const deleteSpeaker = (speaker) => {
    if (confirm(`Are you sure you want to delete speaker "${speaker.name}"?`)) {
        useForm({}).delete(route('system-admin.speakers.destroy', speaker.id))
    }
}

const closeModal = () => {
    showModal.value = false
    selectedSpeaker.value = null
}

const handleSuccess = () => {
    closeModal()
    router.reload({ preserveScroll: true })
}
</script>

<style scoped>
input[type="text"]:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>