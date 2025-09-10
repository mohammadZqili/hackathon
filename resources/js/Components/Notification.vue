<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
})

const page = usePage()
const notificationsOpen = ref(false)
const notificationRef = ref(null)
const notifications = ref([])
const loading = ref(false)
const hasMore = ref(false)
const notificationsEnabled = ref(true)

const unreadCount = computed(() =>
    notifications.value.filter(n => !n.read_at).length
)

// Fetch notifications from backend
const fetchNotifications = async () => {
    loading.value = true
    try {
        const response = await axios.get('/notifications')
        notifications.value = response.data.notifications || []
        hasMore.value = response.data.has_more || false
        notificationsEnabled.value = response.data.enabled !== false
    } catch (error) {
        console.error('Failed to fetch notifications:', error)
    } finally {
        loading.value = false
    }
}

// Poll for new notifications every 30 seconds
let pollingInterval = null
const startPolling = () => {
    pollingInterval = setInterval(() => {
        fetchNotifications()
    }, 30000) // 30 seconds
}

const stopPolling = () => {
    if (pollingInterval) {
        clearInterval(pollingInterval)
        pollingInterval = null
    }
}

const toggleNotifications = async () => {
    try {
        const response = await axios.post('/notifications/toggle')
        notificationsEnabled.value = response.data.enabled
        
        // Clear notifications if disabled
        if (!response.data.enabled) {
            notifications.value = []
            stopPolling()
        } else {
            // Re-fetch and start polling if enabled
            fetchNotifications()
            startPolling()
        }
        
        // Show feedback (you could add a toast notification here)
        console.log(response.data.message)
    } catch (error) {
        console.error('Failed to toggle notifications:', error)
    }
}

const toggleDropdown = () => {
    notificationsOpen.value = !notificationsOpen.value
}

const markAsRead = async (notificationId) => {
    try {
        await axios.post(`/notifications/${notificationId}/read`)
        const notification = notifications.value.find(n => n.id === notificationId)
        if (notification) {
            notification.read_at = new Date().toISOString()
        }
    } catch (error) {
        console.error('Failed to mark notification as read:', error)
    }
}

const markAllAsRead = async () => {
    try {
        await axios.post('/notifications/mark-all-read')
        notifications.value.forEach(n => {
            if (!n.read_at) {
                n.read_at = new Date().toISOString()
            }
        })
    } catch (error) {
        console.error('Failed to mark all notifications as read:', error)
    }
}

const deleteNotification = async (notificationId) => {
    try {
        await axios.delete(`/notifications/${notificationId}`)
        notifications.value = notifications.value.filter(n => n.id !== notificationId)
    } catch (error) {
        console.error('Failed to delete notification:', error)
    }
}

// Format notification time
const formatTime = (timestamp) => {
    if (!timestamp) return ''
    
    const date = new Date(timestamp)
    const now = new Date()
    const diff = Math.floor((now - date) / 1000) // difference in seconds
    
    if (diff < 60) return 'Just now'
    if (diff < 3600) return `${Math.floor(diff / 60)} min ago`
    if (diff < 86400) return `${Math.floor(diff / 3600)} hour${Math.floor(diff / 3600) > 1 ? 's' : ''} ago`
    if (diff < 604800) return `${Math.floor(diff / 86400)} day${Math.floor(diff / 86400) > 1 ? 's' : ''} ago`
    
    return date.toLocaleDateString()
}

// Get notification data from the notification object
const getNotificationData = (notification) => {
    const data = notification.data || {}
    return {
        title: data.title || 'Notification',
        description: data.message || data.description || '',
        priority: data.priority || 'normal',
        type: data.type || notification.type || 'info'
    }
}

const handleClickAway = (event) => {
    const notificationButton = document.querySelector('[data-notification-button]')
    const notificationDropdown = document.querySelector('[data-notification-dropdown]')

    if (!notificationButton?.contains(event.target) &&
        !notificationDropdown?.contains(event.target)) {
        notificationsOpen.value = false
    }
}

const handleEscapeKey = (event) => {
    if (event.key === 'Escape') {
        notificationsOpen.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickAway)
    document.addEventListener('keydown', handleEscapeKey)
    
    // Fetch notifications on component mount
    fetchNotifications()
    
    // Start polling for new notifications
    startPolling()
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickAway)
    document.removeEventListener('keydown', handleEscapeKey)
    
    // Stop polling when component unmounts
    stopPolling()
})
</script>


<template>
    <div class="relative" ref="notificationRef">
        <!-- Notification Bell Button -->
        <button type="button" data-notification-button
            class="relative p-1.5 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white focus:outline-none rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer group"
            @click="toggleDropdown" aria-label="Notifications" :aria-expanded="notificationsOpen">
            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            
            <!-- Unread Count Badge -->
            <span v-if="unreadCount > 0" 
                class="absolute -top-1 -right-1 h-5 w-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-semibold">
                {{ unreadCount > 9 ? '9+' : unreadCount }}
            </span>
            
            <span class="absolute -bottom-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-50">
                Notifications
            </span>
        </button>

        <!-- Notification Dropdown -->
        <div v-show="notificationsOpen" data-notification-dropdown
            class="absolute right-0 z-50 mt-2 w-80 origin-top-right rounded-xl bg-white dark:bg-gray-800 py-2 shadow-lg ring-1 ring-gray-300 dark:ring-gray-700 ring-opacity-5">
            <!-- Dropdown Header -->
            <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</h3>
                    <button v-if="unreadCount > 0" @click="markAllAsRead" 
                        class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                        Mark all as read
                    </button>
                </div>
                
                <!-- Notification Toggle -->
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600 dark:text-gray-400">Enable notifications</span>
                    <button @click="toggleNotifications" 
                        :class="[
                            'relative inline-flex h-5 w-9 items-center rounded-full transition-colors',
                            notificationsEnabled ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'
                        ]">
                        <span :class="[
                            'inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform',
                            notificationsEnabled ? 'translate-x-4' : 'translate-x-1'
                        ]" />
                    </button>
                </div>
            </div>

            <!-- Notification List -->
            <div class="max-h-96 overflow-y-auto">
                <div v-if="loading" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                    Loading notifications...
                </div>
                
                <div v-else-if="notifications.length === 0" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                    No notifications
                </div>

                <ul v-else>
                    <li v-for="notification in notifications" :key="notification.id"
                        class="relative group">
                        <div @click="markAsRead(notification.id)"
                            class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors"
                            :class="{
                                'bg-blue-50/50 dark:bg-blue-900/20': !notification.read_at,
                                'border-l-4': true,
                                'border-red-500': getNotificationData(notification).priority === 'critical',
                                'border-yellow-500': getNotificationData(notification).priority === 'high',
                                'border-blue-500': getNotificationData(notification).priority === 'normal',
                                'border-gray-500': getNotificationData(notification).priority === 'low'
                            }">
                            <div class="flex gap-3">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ getNotificationData(notification).title }}
                                    </h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                        {{ getNotificationData(notification).description }}
                                    </p>
                                    <time class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                        {{ formatTime(notification.created_at) }}
                                    </time>
                                </div>
                                <div class="flex items-start gap-2">
                                    <div v-if="!notification.read_at" class="w-2 h-2 mt-2 bg-blue-500 rounded-full"
                                        aria-hidden="true"></div>
                                    
                                    <!-- Delete button -->
                                    <button @click.stop="deleteNotification(notification.id)"
                                        class="opacity-0 group-hover:opacity-100 p-1 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-opacity">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                
                <!-- Load More -->
                <div v-if="hasMore && !loading" class="px-4 py-2 border-t border-gray-100 dark:border-gray-700">
                    <button @click="fetchNotifications" 
                        class="w-full text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                        Load more
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
