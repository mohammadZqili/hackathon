<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const page = usePage();

const navigationSections = ref([
    {
        items: [
            {
                name: 'Home',
                href: route('home'),
                icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />'
            },
            {
                name: 'Login',
                href: route('login'),
                icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />'
            },
            {
                type: 'divider'
            },
            {
                name: 'Terms',
                href: route('terms'),
                icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />'
            },
        ]
    },
]);

const isActive = (href) => {
    try {
        const path = new URL(href).pathname;
        return page.url === path;
    } catch (e) {
        return page.url === href;
    }
};
</script>

<template>
    <aside data-sidebar-content
        class="h-full flex flex-col bg-white dark:bg-gray-800 shadow-lg transition-all duration-300 ease-in-out">
        <nav class="flex-1 overflow-y-auto py-2 px-2" aria-labelledby="nav-heading">
            <ul class="space-y-1">
                <template v-for="(section, sectionIndex) in navigationSections" :key="sectionIndex">
                    <li v-if="sectionIndex > 0" class="my-1.5 px-2" role="separator">
                        <div class="h-px w-full bg-gray-100 dark:bg-gray-700"></div>
                    </li>
                    <template v-for="(item, itemIndex) in section.items" :key="itemIndex">
                        <li v-if="item.type === 'divider'" class="my-1.5 px-2" role="separator">
                            <div class="h-px w-full bg-gray-100 dark:bg-gray-700"></div>
                        </li>
                        <li v-else>
                            <Link :href="item.href" :class="{
                                'flex items-center px-2.5 py-2 rounded-lg transition-all duration-200 ease-in-out': true,
                                'text-teal-600 dark:text-teal-400': isActive(item.href),
                                'text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700/50': !isActive(item.href)
                            }">
                            <svg class="w-[18px] h-[18px] mr-2.5 transition-colors duration-200" :class="{
                                'text-teal-600 dark:text-teal-400': isActive(item.href),
                                'text-gray-400 dark:text-gray-500': !isActive(item.href)
                            }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true" v-html="item.icon"></svg>
                            <span class="text-sm font-medium">{{ item.name }}</span>
                            </Link>
                        </li>
                    </template>
                </template>
            </ul>
        </nav>
    </aside>
</template>
