<script setup>
import { Head, router } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import PageHeader from '../../../Components/Shared/PageHeader.vue'
import SearchBar from '../../../Components/Shared/SearchBar.vue'
import { ref, computed, onMounted } from 'vue'
import { CalendarIcon, ClockIcon, UserGroupIcon, AcademicCapIcon, MapPinIcon, UserIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    workshops: Object,
    myWorkshops: Array,
    search: String,
    category: String,
    categories: Array,
})

// Theme integration
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

const searchQuery = ref(props.search || '')
const selectedCategory = ref(props.category || '')

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatTime = (timeString) => {
    return new Date(`2000-01-01T${timeString}`).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    })
}

const getWorkshopStatus = (workshop) => {
    const now = new Date()
    const startTime = new Date(`${workshop.date}T${workshop.start_time}`)
    const endTime = new Date(`${workshop.date}T${workshop.end_time}`)
    
    if (now < startTime) {
        return { text: 'Upcoming', class: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' }
    } else if (now >= startTime && now <= endTime) {
        return { text: 'In Progress', class: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' }
    } else {
        return { text: 'Completed', class: 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400' }
    }
}

const getCapacityPercentage = (workshop) => {
    if (!workshop.capacity || workshop.capacity === 0) return 0
    return Math.round((workshop.registrations_count / workshop.capacity) * 100)
}

const isRegistered = (workshop) => {
    return props.myWorkshops?.some(myWorkshop => myWorkshop.id === workshop.id) || false
}

const canRegister = (workshop) => {
    const status = getWorkshopStatus(workshop)
    return status.text === 'Upcoming' && 
           !isRegistered(workshop) && 
           (workshop.registrations_count < workshop.capacity)
}

const handleSearch = (query) => {
    router.get(route('visitor.workshops.index'), { 
        search: query, 
        category: selectedCategory.value 
    }, {
        preserveState: true,
        replace: true
    })
}

const handleCategoryChange = () => {
    router.get(route('visitor.workshops.index'), { 
        search: searchQuery.value, 
        category: selectedCategory.value 
    }, {
        preserveState: true,
        replace: true
    })
}

const clearFilters = () => {
    searchQuery.value = ''
    selectedCategory.value = ''
    router.get(route('visitor.workshops.index'), {}, {
        preserveState: true,
        replace: true
    })
}
</script>

<template>
    <Head title="All Workshops" />
    
    <Default>
        <div class="max-w-7xl mx-auto" :style="themeStyles">
            <PageHeader 
                title="All Workshops" 
                description="Explore workshops designed to enhance your skills and knowledge in various fields"
                :show-action="false"
            />

            <!-- Search and Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    <div class="flex-1">
                        <SearchBar 
                            v-model="searchQuery"
                            placeholder="Search workshops by title, speaker, or description..."
                            @search="handleSearch"
                            class="w-full"
                        />
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <select 
                            v-model="selectedCategory"
                            @change="handleCategoryChange"
                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm">
                            <option value="">All Categories</option>
                            <option v-for="category in categories" :key="category" :value="category">
                                {{ category }}
                            </option>
                        </select>
                        
                        <button 
                            v-if="searchQuery || selectedCategory"
                            @click="clearFilters"
                            class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                            Clear Filters
                        </button>
                    </div>
                </div>
                
                <div v-if="searchQuery || selectedCategory" class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                    Showing {{ workshops.total || 0 }} workshops
                    <span v-if="searchQuery">for "{{ searchQuery }}"</span>
                    <span v-if="selectedCategory">in {{ selectedCategory }}</span>
                </div>
            </div>

            <!-- Workshops Grid -->
            <div v-if="workshops.data && workshops.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div v-for="workshop in workshops.data" :key="workshop.id" 
                     class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
                    <!-- Workshop Image Placeholder -->
                    <div class="h-48 bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 flex items-center justify-center">
                        <AcademicCapIcon class="w-16 h-16 text-gray-400" />
                    </div>
                    
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">{{ workshop.title }}</h3>
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                          :class="getWorkshopStatus(workshop).class">
                                        {{ getWorkshopStatus(workshop).text }}
                                    </span>
                                    <span v-if="isRegistered(workshop)" 
                                          class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                        Registered
                                    </span>
                                </div>
                            </div>
                        </div>

                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">{{ workshop.description }}</p>

                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex items-center text-gray-600 dark:text-gray-400">
                                <CalendarIcon class="w-4 h-4 mr-2" />
                                {{ formatDate(workshop.date) }}
                            </div>
                            <div class="flex items-center text-gray-600 dark:text-gray-400">
                                <ClockIcon class="w-4 h-4 mr-2" />
                                {{ formatTime(workshop.start_time) }} - {{ formatTime(workshop.end_time) }}
                            </div>
                            <div v-if="workshop.location" class="flex items-center text-gray-600 dark:text-gray-400">
                                <MapPinIcon class="w-4 h-4 mr-2" />
                                {{ workshop.location }}
                            </div>
                            <div v-if="workshop.speaker" class="flex items-center text-gray-600 dark:text-gray-400">
                                <UserIcon class="w-4 h-4 mr-2" />
                                {{ workshop.speaker.name }}
                                <span v-if="workshop.speaker.organization">({{ workshop.speaker.organization.name }})</span>
                            </div>
                        </div>

                        <!-- Capacity Progress -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400 mb-1">
                                <span>Capacity</span>
                                <span>{{ workshop.registrations_count || 0 }}/{{ workshop.capacity }}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-300" 
                                     :style="{ 
                                         backgroundColor: themeColor.primary, 
                                         width: `${getCapacityPercentage(workshop)}%` 
                                     }">
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ getCapacityPercentage(workshop) }}% full
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between">
                            <a :href="route('visitor.workshops.show', workshop.id)" 
                               class="text-sm px-3 py-2 rounded-lg border transition-colors"
                               :style="{ borderColor: themeColor.primary, color: themeColor.primary }"
                               @mouseenter="$event.target.style.backgroundColor = `rgba(${themeColor.rgb}, 0.1)`"
                               @mouseleave="$event.target.style.backgroundColor = 'transparent'">
                                View Details
                            </a>

                            <div v-if="canRegister(workshop)">
                                <a :href="route('visitor.workshops.show', workshop.id)" 
                                   class="text-sm px-4 py-2 rounded-lg text-white transition-colors"
                                   :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                                    Register Now
                                </a>
                            </div>
                            <div v-else-if="isRegistered(workshop)">
                                <span class="text-sm px-4 py-2 rounded-lg bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                    ✓ Registered
                                </span>
                            </div>
                            <div v-else-if="workshop.registrations_count >= workshop.capacity">
                                <span class="text-sm px-4 py-2 rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                    Workshop Full
                                </span>
                            </div>
                            <div v-else>
                                <span class="text-sm px-4 py-2 rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                    Registration Closed
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <AcademicCapIcon class="mx-auto h-16 w-16 text-gray-400" />
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No workshops found</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    <span v-if="searchQuery || selectedCategory">
                        Try adjusting your search criteria or filters.
                    </span>
                    <span v-else>
                        No workshops are currently available. Check back later for updates.
                    </span>
                </p>
                <button v-if="searchQuery || selectedCategory" 
                        @click="clearFilters"
                        class="mt-4 px-4 py-2 rounded-lg text-white transition-colors"
                        :style="{ background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})` }">
                    Clear Filters
                </button>
            </div>

            <!-- Pagination -->
            <div v-if="workshops.links && workshops.links.length > 3" class="mt-8">
                <nav class="flex items-center justify-between bg-white dark:bg-gray-800 px-6 py-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between flex-1 sm:hidden">
                        <a v-if="workshops.prev_page_url" 
                           :href="workshops.prev_page_url" 
                           class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Previous
                        </a>
                        <a v-if="workshops.next_page_url" 
                           :href="workshops.next_page_url" 
                           class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Next
                        </a>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Showing {{ workshops.from || 0 }} to {{ workshops.to || 0 }} of {{ workshops.total || 0 }} results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                <template v-for="(link, index) in workshops.links" :key="index">
                                    <a v-if="link.url" 
                                       :href="link.url" 
                                       class="relative inline-flex items-center px-2 py-2 text-sm font-medium border transition-colors"
                                       :class="[
                                           link.active 
                                               ? 'z-10 border-transparent text-white' 
                                               : 'border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700',
                                           index === 0 ? 'rounded-l-md' : '',
                                           index === workshops.links.length - 1 ? 'rounded-r-md' : ''
                                       ]"
                                       :style="link.active ? { backgroundColor: themeColor.primary, borderColor: themeColor.primary } : {}"
                                       v-html="link.label">
                                    </a>
                                    <span v-else 
                                          class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 border border-gray-300 dark:border-gray-600"
                                          :class="[
                                              index === 0 ? 'rounded-l-md' : '',
                                              index === workshops.links.length - 1 ? 'rounded-r-md' : ''
                                          ]"
                                          v-html="link.label">
                                    </span>
                                </template>
                            </nav>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </Default>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

input[type="text"]:focus,
select:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}
</style>