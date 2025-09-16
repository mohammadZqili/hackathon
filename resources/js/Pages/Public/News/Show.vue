<template>
    <Head :title="article.title" />
    <PublicLayout>
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
            <div class="container mx-auto px-4 max-w-4xl">
                <!-- Article Header -->
                <article class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                    <!-- Featured Image -->
                    <div v-if="article.image" class="aspect-w-16 aspect-h-9">
                        <img :src="article.image" :alt="article.title" class="object-cover w-full h-64 md:h-96">
                    </div>

                    <!-- Article Content -->
                    <div class="p-6 md:p-8">
                        <!-- Title -->
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                            {{ article.title }}
                        </h1>

                        <!-- Metadata -->
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                {{ article.author }}
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ article.created_at }}
                            </div>
                            <div v-if="article.edition" class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                {{ article.edition }}
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="prose prose-lg dark:prose-invert max-w-none" v-html="article.content"></div>

                        <!-- Share Section -->
                        <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Share this article</h3>
                                <TwitterShareButton
                                    :title="article.title"
                                    :url="meta.url"
                                    :description="meta.description"
                                    :hashtags="['GuacPanel', 'Hackathon', 'News']"
                                    via="GuacPanel"
                                />
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Back Link -->
                <div class="mt-6">
                    <Link href="/news" class="inline-flex items-center text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to News
                    </Link>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/Public.vue'
import TwitterShareButton from '@/Components/TwitterShareButton.vue'

const props = defineProps({
    article: {
        type: Object,
        required: true
    },
    meta: {
        type: Object,
        required: true
    }
})
</script>