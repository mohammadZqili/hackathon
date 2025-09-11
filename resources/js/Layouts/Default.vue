<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import NavSidebarDesktop from '@/Components/NavSidebarDesktop.vue'
import NavProfile from '../Components/NavProfile.vue'
import Notification from '../Components/Notification.vue'
import FlashMessage from '../Components/FlashMessage.vue'
import Footer from '../Shared/Public/Footer.vue'
import ColorThemeSwitcher from '../Components/ColorThemeSwitcher.vue'
import Logo from '../Components/Logo.vue'
import Search from '../Components/Typesense/Search.vue'
import SystemNotice from '../Components/SystemNotice.vue'
import LanguageSwitcher from '../Components/LanguageSwitcher.vue'
import { useLocalization } from '@/composables/useLocalization'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const { t, isRTL, direction, locale } = useLocalization()
const isSidebarOpen = ref(false)
const isMobileSearchOpen = ref(false)
const noticeHeight = ref(0)

const headerHeight = computed(() => {
    return `${70 + (noticeHeight.value || 0)}px`
})

const handleNoticeHeightChange = (height) => {
    noticeHeight.value = height || 0
}

const isMobile = () => window.innerWidth < 768
const searchPlaceholder = computed(() => t('layout.search_placeholder'))

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value
    localStorage.setItem('sidebarOpen', isSidebarOpen.value.toString())
}

const closeSidebar = () => {
    isSidebarOpen.value = false
    localStorage.setItem('sidebarOpen', 'false')
}

const toggleMobileSearch = () => {
    isMobileSearchOpen.value = !isMobileSearchOpen.value
}

const closeMobileSearch = () => {
    isMobileSearchOpen.value = false
}

const handlers = {
    sidebar: (event) => {
        const elements = {
            sidebar: document.querySelector('[data-sidebar]'),
            menuButton: document.querySelector('[data-menu-button]'),
            sidebarContent: document.querySelector('[data-sidebar-content]')
        };

        if (Object.values(elements).some(el => el?.contains(event.target))) {
            return;
        }

        if (isMobile()) {
            closeSidebar();
        }
    },

    search: (event) => {
        const elements = {
            overlay: document.querySelector('[data-search-overlay]'),
            panel: document.querySelector('[data-search-panel]'),
            button: document.querySelector('[data-search-button]')
        };

        if (elements.overlay?.contains(event.target) &&
            !elements.panel?.contains(event.target) &&
            !elements.button?.contains(event.target)) {
            closeMobileSearch();
        }
    }
};

const handleClickAway = (event) => {
    handlers.sidebar(event);
    handlers.search(event);
}

const handleKeyDown = (event) => {
    if (event.key === 'Escape') {
        if (isSidebarOpen.value && isMobile()) {
            closeSidebar()
        }
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickAway)
    document.addEventListener('keydown', handleKeyDown)

    const savedState = localStorage.getItem('sidebarOpen')
    isSidebarOpen.value = savedState ? savedState === 'true' : !isMobile()
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickAway)
    document.removeEventListener('keydown', handleKeyDown)
})
</script>

<template>

    <div class="min-h-screen theme-bg-base" role="document" :dir="direction">
        <!-- Sidebar Overlay -->
        <div v-if="isSidebarOpen && isMobile()" class="fixed inset-0 bg-black/30 z-30" @click.stop="closeSidebar"
            role="dialog" aria-modal="true" :aria-label="t('layout.mobile_navigation_menu')" aria-hidden="true">
        </div>

        <!-- Main Sidebar Navigation -->
        <div data-sidebar role="navigation" :aria-label="t('layout.main_sidebar')" :aria-expanded="isSidebarOpen"
            :aria-hidden="!isSidebarOpen"
            class="fixed top-6 w-80 h-[calc(100vh-48px)] transition-transform duration-200 z-40 shadow-[0px_0px_8px_rgba(0,_0,_0,_0.05)] rounded-2xl overflow-hidden"
            :class="[
                isRTL ? 'right-4' : 'left-4',
                isSidebarOpen ? 'translate-x-0' : (isRTL ? 'translate-x-full' : '-translate-x-full')
            ]">
            <NavSidebarDesktop class="h-full theme-bg-card rounded-2xl border border-gray-200/50 dark:border-gray-700/50 backdrop-blur-sm" @close="closeSidebar" />
        </div>

        <div class="flex flex-col min-h-screen">

            <!-- Header with System Notices -->
            <header role="banner" class="fixed top-6 z-40" 
                :class="[
                    isRTL ? (isSidebarOpen ? 'right-[344px] left-4' : 'right-4 left-4') : (isSidebarOpen ? 'left-[344px] right-4' : 'left-4 right-4')
                ]" 
                :style="{ transition: 'all 0.2s' }">

                <!-- System Notices -->
                <SystemNotice :notices="page.props.systemNotices" @height-change="handleNoticeHeightChange" 
                    class="mb-4" />

                <!-- Navigation -->
                <nav class="shadow-[0px_0px_8px_1px_rgba(0,_0,_0,_0.08)] rounded-2xl theme-bg-card h-16 flex items-center justify-between py-[7px] px-4 border border-gray-200/50 dark:border-gray-700/50 backdrop-blur-sm" 
                    role="navigation" :aria-label="t('layout.primary_navigation')">
                    <!-- Menu Toggle and Search -->
                    <section class="flex items-center gap-4" aria-label="Menu and search controls">
                        <button type="button" data-menu-button
                            class="rounded-lg p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-600 cursor-pointer"
                            @click="toggleSidebar" aria-label="Toggle navigation menu" :aria-expanded="isSidebarOpen">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                        
                        <!-- Desktop Search -->
                        <div class="hidden lg:flex items-center min-w-[160px] max-w-[312px]">
                            <div class="flex-1 rounded-xl flex">
                                <div class="rounded-l-xl theme-bg-accent flex items-center justify-center px-4">
                                    <svg class="w-[18px] h-[18px] text-gray-600 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <Search :isMobile="false" :placeholder="searchPlaceholder" 
                                    class="flex-1 theme-bg-accent rounded-r-xl" />
                            </div>
                        </div>
                    </section>

                    <!-- Mobile Search Controls -->
                    <section class="flex items-center gap-2 lg:hidden" aria-label="Mobile search">
                        <button type="button" data-search-button
                            class="w-7 h-7 rounded-[10px] theme-bg-accent flex items-center justify-center theme-bg-accent-hover"
                            @click="toggleMobileSearch" aria-label="Open search" :aria-expanded="isMobileSearchOpen">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </section>

                    <!-- User Controls -->
                    <section class="flex items-center gap-3" aria-label="User controls">
                        <!-- Language Switcher -->
                        <LanguageSwitcher />
                        
                        <!-- Icon Controls -->
                        <div class="flex items-center gap-2">
                            <!-- Theme Switcher -->
                            <div class="w-7 h-7 rounded-[10px] theme-bg-accent flex items-center justify-center theme-bg-accent-hover">
                                <ColorThemeSwitcher class="!p-0 !bg-transparent hover:!bg-transparent" />
                            </div>
                            
                            <!-- Notifications -->
                            <div v-if="user" class="w-7 h-7 rounded-[10px] theme-bg-accent flex items-center justify-center theme-bg-accent-hover">
                                <Notification :user="user" class="!p-0 !bg-transparent hover:!bg-transparent" />
                            </div>
                            
                            <!-- Settings -->
                            <Link v-if="user?.roles?.[0]?.name === 'System Admin'" :href="route('admin.setting.index')"
                                class="w-7 h-7 rounded-[10px] theme-bg-accent flex items-center justify-center group relative theme-bg-accent-hover cursor-pointer">
                                <span
                                    class="absolute -bottom-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-50">
                                    {{ t('admin.layout.settings_tooltip') }}
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-[18px] h-[18px] text-gray-600 dark:text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </Link>
                        </div>
                        
                        <!-- User Profile -->
                        <div v-if="user" class="flex items-center gap-3">
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-800 dark:text-white">{{ user.name }}</div>
                                <div class="text-xs text-green-600 dark:text-green-400">{{ user.roles?.[0]?.name || 'User' }}</div>
                            </div>
                            <NavProfile :user="user" />
                        </div>
                        <Link v-else href="/login" class="text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-white"
                            :aria-label="t('admin.layout.login')">
                            {{ t('admin.layout.login') }}
                        </Link>
                    </section>
                </nav>
            </header>

            <!-- Mobile Search Overlay -->
            <Search :isOpen="isMobileSearchOpen" :isMobile="true" :placeholder="searchPlaceholder"
                @close="isMobileSearchOpen = false" data-search-overlay />

            <!-- Main Content Area -->
            <main class="flex-1" role="main" 
                :class="[
                    'px-4',
                    isRTL ? (isSidebarOpen ? 'md:mr-[336px]' : 'md:mr-4') : (isSidebarOpen ? 'md:ml-[336px]' : 'md:ml-4')
                ]"
                :style="{ paddingTop: '104px', transition: 'all 0.2s' }">
                <FlashMessage />
                <article class="py-8">
                    <slot />
                </article>
            </main>

            <Footer />
        </div>
    </div>
</template>
