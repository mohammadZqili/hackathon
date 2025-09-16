<template>
    <div class="relative inline-block">
        <!-- Twitter Share Button -->
        <button
            @click="toggleShare"
            class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 transition-colors duration-200"
            :title="t('Share on Twitter')"
        >
            <!-- Twitter Icon -->
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
            </svg>
            <span>{{ t('Share') }}</span>
        </button>

        <!-- Share Options Dropdown -->
        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 transform scale-95"
            enter-to-class="opacity-100 transform scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 transform scale-100"
            leave-to-class="opacity-0 transform scale-95"
        >
            <div
                v-if="showShareOptions"
                class="absolute z-50 mt-2 w-80 rounded-lg shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5"
                :class="[isRTL ? 'left-0' : 'right-0']"
            >
                <div class="p-4 space-y-3">
                    <!-- Title -->
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ t('Share on Twitter') }}
                        </h3>
                        <button
                            @click="showShareOptions = false"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Share URL -->
                    <div class="space-y-2">
                        <label class="text-xs font-medium text-gray-700 dark:text-gray-300">
                            {{ t('Share URL') }}
                        </label>
                        <div class="flex items-center space-x-2" :class="{ 'space-x-reverse': isRTL }">
                            <input
                                :value="shareUrl"
                                readonly
                                class="flex-1 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white"
                                @focus="$event.target.select()"
                            />
                            <button
                                @click="copyToClipboard"
                                class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                            >
                                {{ copied ? t('Copied!') : t('Copy') }}
                            </button>
                        </div>
                    </div>

                    <!-- Custom Tweet Text -->
                    <div class="space-y-2">
                        <label class="text-xs font-medium text-gray-700 dark:text-gray-300">
                            {{ t('Tweet Text') }}
                        </label>
                        <textarea
                            v-model="tweetText"
                            :maxlength="maxTweetLength"
                            rows="3"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"
                            :placeholder="t('Add your comment...')"
                        ></textarea>
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>{{ tweetText.length }} / {{ maxTweetLength }}</span>
                        </div>
                    </div>

                    <!-- Share Actions -->
                    <div class="flex space-x-2" :class="{ 'space-x-reverse': isRTL }">
                        <button
                            @click="shareOnTwitter"
                            class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors"
                        >
                            {{ t('Tweet Now') }}
                        </button>
                        <button
                            @click="generateShareableLink"
                            class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                        >
                            {{ t('Generate Link') }}
                        </button>
                    </div>

                    <!-- Generated Shareable Link -->
                    <div v-if="shareableLink" class="pt-3 border-t border-gray-200 dark:border-gray-700">
                        <label class="text-xs font-medium text-gray-700 dark:text-gray-300">
                            {{ t('Shareable Link') }}
                        </label>
                        <div class="mt-1 p-2 bg-gray-50 dark:bg-gray-700 rounded text-xs text-gray-600 dark:text-gray-400 break-all">
                            {{ shareableLink }}
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useLocalization } from '@/composables/useLocalization'

const { t, isRTL } = useLocalization()

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    url: {
        type: String,
        default: null
    },
    description: {
        type: String,
        default: ''
    },
    hashtags: {
        type: Array,
        default: () => []
    },
    via: {
        type: String,
        default: ''
    }
})

const showShareOptions = ref(false)
const copied = ref(false)
const shareableLink = ref('')
const maxTweetLength = 280

// Generate the full URL for sharing
const shareUrl = computed(() => {
    if (props.url) {
        // If URL is provided, use it
        return props.url.startsWith('http') ? props.url : `${window.location.origin}${props.url}`
    }
    // Otherwise use current page URL
    return window.location.href
})

// Default tweet text
const defaultTweetText = computed(() => {
    let text = props.title
    if (props.description) {
        text += ` - ${props.description}`
    }
    return text.substring(0, maxTweetLength - shareUrl.value.length - 10) // Leave room for URL
})

const tweetText = ref(defaultTweetText.value)

// Toggle share options
const toggleShare = () => {
    showShareOptions.value = !showShareOptions.value
    if (showShareOptions.value) {
        tweetText.value = defaultTweetText.value
        shareableLink.value = ''
        copied.value = false
    }
}

// Copy URL to clipboard
const copyToClipboard = async () => {
    try {
        await navigator.clipboard.writeText(shareUrl.value)
        copied.value = true
        setTimeout(() => {
            copied.value = false
        }, 2000)
    } catch (err) {
        console.error('Failed to copy:', err)
    }
}

// Share on Twitter
const shareOnTwitter = () => {
    const params = new URLSearchParams()

    // Add tweet text
    const fullTweet = tweetText.value + '\n\n' + shareUrl.value
    params.append('text', fullTweet)

    // Add hashtags if provided
    if (props.hashtags.length > 0) {
        params.append('hashtags', props.hashtags.join(','))
    }

    // Add via parameter if provided
    if (props.via) {
        params.append('via', props.via)
    }

    // Open Twitter intent in new window
    const twitterUrl = `https://twitter.com/intent/tweet?${params.toString()}`
    window.open(twitterUrl, '_blank', 'width=550,height=420')

    showShareOptions.value = false
}

// Generate a shareable link with Twitter intent
const generateShareableLink = () => {
    const params = new URLSearchParams()

    // Add tweet text
    const fullTweet = tweetText.value + '\n\n' + shareUrl.value
    params.append('text', fullTweet)

    // Add hashtags if provided
    if (props.hashtags.length > 0) {
        params.append('hashtags', props.hashtags.join(','))
    }

    // Add via parameter if provided
    if (props.via) {
        params.append('via', props.via)
    }

    shareableLink.value = `https://twitter.com/intent/tweet?${params.toString()}`
}

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    const button = event.target.closest('button')
    const dropdown = event.target.closest('.absolute')

    if (!button && !dropdown && showShareOptions.value) {
        showShareOptions.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>