<template>
    <!-- Only show in development environment -->
    <div v-if="isDevelopment">
        <!-- Fixed button at bottom right -->
        <button
            @click="isOpen = !isOpen"
            class="fixed bottom-4 right-4 z-50 px-4 py-2 bg-purple-600 text-white rounded-lg shadow-lg hover:bg-purple-700 transition-all duration-200 flex items-center gap-2"
            title="Test Accounts (Dev Only)"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span class="font-semibold">Test Accounts</span>
            <span class="text-xs bg-purple-800 px-1 rounded">DEV</span>
        </button>

        <!-- Modal/Panel -->
        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="transform translate-x-full opacity-0"
            enter-to-class="transform translate-x-0 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="transform translate-x-0 opacity-100"
            leave-to-class="transform translate-x-full opacity-0"
        >
            <div v-if="isOpen" class="fixed inset-y-0 right-0 z-50 w-96 bg-white dark:bg-gray-800 shadow-2xl overflow-y-auto">
                <!-- Header -->
                <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Test Accounts</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Click any account to fill login form</p>
                            <p class="text-xs text-red-500 dark:text-red-400 font-semibold">⚠️ Development Only</p>
                        </div>
                        <button
                            @click="isOpen = false"
                            class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        >
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="p-8 text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto"></div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Loading test accounts...</p>
                </div>

                <!-- Accounts List -->
                <div v-else class="p-4 space-y-4">
                    <!-- System Admin -->
                    <div v-if="accounts.system_admin?.length > 0" class="border border-red-200 dark:border-red-800 rounded-lg p-3 bg-red-50 dark:bg-red-900/20">
                        <h4 class="text-sm font-bold text-red-700 dark:text-red-400 mb-2">System Admin</h4>
                        <div class="space-y-2">
                            <AccountCard
                                v-for="admin in accounts.system_admin"
                                :key="admin.id"
                                :email="admin.email"
                                :password="admin.password"
                                :name="admin.name"
                                role="system_admin"
                                :extra="admin.extra"
                                @fill="fillCredentials"
                            />
                        </div>
                    </div>

                    <!-- Hackathon Admins -->
                    <div v-if="accounts.hackathon_admin?.length > 0" class="border border-orange-200 dark:border-orange-800 rounded-lg p-3 bg-orange-50 dark:bg-orange-900/20">
                        <h4 class="text-sm font-bold text-orange-700 dark:text-orange-400 mb-2">
                            Hackathon Admins
                            <span v-if="currentEdition" class="text-xs font-normal">({{ currentEdition.name }})</span>
                        </h4>
                        <div class="space-y-2">
                            <AccountCard
                                v-for="admin in accounts.hackathon_admin"
                                :key="admin.id"
                                :email="admin.email"
                                :password="admin.password"
                                :name="admin.name"
                                role="hackathon_admin"
                                :extra="admin.extra"
                                @fill="fillCredentials"
                            />
                        </div>
                    </div>

                    <!-- Track Supervisors -->
                    <div v-if="accounts.track_supervisor?.length > 0" class="border border-purple-200 dark:border-purple-800 rounded-lg p-3 bg-purple-50 dark:bg-purple-900/20">
                        <h4 class="text-sm font-bold text-purple-700 dark:text-purple-400 mb-2">Track Supervisors</h4>
                        <div class="space-y-2">
                            <AccountCard
                                v-for="supervisor in accounts.track_supervisor"
                                :key="supervisor.id"
                                :email="supervisor.email"
                                :password="supervisor.password"
                                :name="supervisor.name"
                                role="track_supervisor"
                                :extra="supervisor.extra"
                                @fill="fillCredentials"
                            />
                        </div>
                    </div>

                    <!-- Workshop Supervisor -->
                    <div v-if="accounts.workshop_supervisor?.length > 0" class="border border-indigo-200 dark:border-indigo-800 rounded-lg p-3 bg-indigo-50 dark:bg-indigo-900/20">
                        <h4 class="text-sm font-bold text-indigo-700 dark:text-indigo-400 mb-2">Workshop Supervisor</h4>
                        <div class="space-y-2">
                            <AccountCard
                                v-for="supervisor in accounts.workshop_supervisor"
                                :key="supervisor.id"
                                :email="supervisor.email"
                                :password="supervisor.password"
                                :name="supervisor.name"
                                role="workshop_supervisor"
                                :extra="supervisor.extra"
                                @fill="fillCredentials"
                            />
                        </div>
                    </div>

                    <!-- Team Leaders -->
                    <div v-if="accounts.team_leader?.length > 0" class="border border-blue-200 dark:border-blue-800 rounded-lg p-3 bg-blue-50 dark:bg-blue-900/20">
                        <h4 class="text-sm font-bold text-blue-700 dark:text-blue-400 mb-2">Team Leaders</h4>
                        <div class="space-y-2">
                            <AccountCard
                                v-for="leader in accounts.team_leader"
                                :key="leader.id"
                                :email="leader.email"
                                :password="leader.password"
                                :name="leader.name"
                                role="team_leader"
                                :extra="leader.extra"
                                @fill="fillCredentials"
                            />
                        </div>
                    </div>

                    <!-- Team Members -->
                    <div v-if="Object.keys(teamMembersByTeam).length > 0" class="border border-green-200 dark:border-green-800 rounded-lg p-3 bg-green-50 dark:bg-green-900/20">
                        <h4 class="text-sm font-bold text-green-700 dark:text-green-400 mb-2">Team Members</h4>

                        <div v-for="(members, teamName) in teamMembersByTeam" :key="teamName" class="mb-3 last:mb-0">
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">{{ teamName }}</p>
                            <div class="space-y-1">
                                <AccountCard
                                    v-for="member in members"
                                    :key="member.id"
                                    :email="member.email"
                                    :password="member.password"
                                    :name="member.name"
                                    role="team_member"
                                    :extra="member.extra"
                                    @fill="fillCredentials"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Visitor -->
                    <div v-if="accounts.visitor?.length > 0" class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 bg-gray-50 dark:bg-gray-900/20">
                        <h4 class="text-sm font-bold text-gray-700 dark:text-gray-400 mb-2">Visitor</h4>
                        <div class="space-y-2">
                            <AccountCard
                                v-for="visitor in accounts.visitor"
                                :key="visitor.id"
                                :email="visitor.email"
                                :password="visitor.password"
                                :name="visitor.name"
                                role="visitor"
                                :extra="visitor.extra"
                                @fill="fillCredentials"
                            />
                        </div>
                    </div>

                    <!-- No Accounts Message -->
                    <div v-if="!loading && Object.values(accounts).every(arr => !arr || arr.length === 0)" class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400">No test accounts available in the database.</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Run seeders to create test data.</p>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Toast Notification -->
        <transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="transform translate-y-full opacity-0"
            enter-to-class="transform translate-y-0 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="transform translate-y-0 opacity-100"
            leave-to-class="transform translate-y-full opacity-0"
        >
            <div v-if="showToast" class="fixed bottom-20 right-4 z-50 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Login fields filled!</span>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'

const emit = defineEmits(['fill-credentials'])

const page = usePage()
const isOpen = ref(false)
const showToast = ref(false)
const loading = ref(false)
// Default accounts based on seeders
const defaultAccounts = {
    system_admin: [
        { id: 1, email: 'superadmin@hackathon.com', password: 'password', name: 'Super Admin', extra: 'Full System Access' }
    ],
    hackathon_admin: [
        { id: 2, email: 'admin@hackathon.com', password: 'password', name: 'Hackathon Admin', extra: 'Edition Manager' },
        { id: 3, email: 'admin@ruman.sa', password: 'password', name: 'Admin Ruman', extra: 'Edition Manager' }
    ],
    track_supervisor: [
        { id: 4, email: 'sarah.johnson@hackathon.com', password: 'password', name: 'Sarah Johnson', extra: 'Track: Web Development' },
        { id: 5, email: 'michael.chen@hackathon.com', password: 'password', name: 'Michael Chen', extra: 'Track: Mobile Apps' }
    ],
    workshop_supervisor: [],
    team_leader: [
        { id: 6, email: 'alice@team.com', password: 'password', name: 'Alice Team Leader', extra: 'Team: InnoTech' },
        { id: 7, email: 'bob@team.com', password: 'password', name: 'Bob Team Leader', extra: 'Team: CodeCrafters' }
    ],
    team_member: [
        { id: 8, email: 'member1@team.com', password: 'password', name: 'Team Member 1', extra: 'Team: InnoTech', team_name: 'InnoTech' },
        { id: 9, email: 'member2@team.com', password: 'password', name: 'Team Member 2', extra: 'Team: InnoTech', team_name: 'InnoTech' },
        { id: 10, email: 'member3@team.com', password: 'password', name: 'Team Member 3', extra: 'Team: CodeCrafters', team_name: 'CodeCrafters' },
        { id: 11, email: 'member4@team.com', password: 'password', name: 'Team Member 4', extra: 'Team: CodeCrafters', team_name: 'CodeCrafters' },
        { id: 12, email: 'member5@team.com', password: 'password', name: 'Team Member 5', extra: 'Team: CodeCrafters', team_name: 'CodeCrafters' }
    ],
    visitor: [
        { id: 13, email: 'visitor@hackathon.com', password: 'password', name: 'Visitor User', extra: 'Workshop Access Only' }
    ]
}

const accounts = ref(defaultAccounts)
const currentEdition = ref(null)

// Check if we're in development environment
const isDevelopment = computed(() => {
    // Check multiple indicators for development environment
    return (
        page.props.app?.debug === true ||
        page.props.app?.env === 'local' ||
        window.location.hostname === 'localhost' ||
        window.location.hostname === '127.0.0.1' ||
        import.meta.env.DEV === true
    )
})

// Fetch test accounts from database
const fetchTestAccounts = async () => {
    if (!isDevelopment.value) return

    loading.value = true
    try {
        const response = await axios.get('/api/test-accounts')
        if (response.data && response.data.accounts) {
            // Merge fetched accounts with defaults, preferring fetched if available
            Object.keys(response.data.accounts).forEach(role => {
                if (response.data.accounts[role] && response.data.accounts[role].length > 0) {
                    accounts.value[role] = response.data.accounts[role]
                }
            })
            currentEdition.value = response.data.current_edition
        }
    } catch (error) {
        console.error('Failed to fetch test accounts:', error)
        // Keep using default accounts on error
    } finally {
        loading.value = false
    }
}

// Group team members by team
const teamMembersByTeam = computed(() => {
    const grouped = {}
    accounts.value.team_member?.forEach(member => {
        const teamName = member.team_name || 'Unknown Team'
        if (!grouped[teamName]) {
            grouped[teamName] = []
        }
        grouped[teamName].push(member)
    })
    return grouped
})

onMounted(() => {
    // Use default accounts immediately, then try to fetch real ones
    accounts.value = defaultAccounts

    if (isDevelopment.value) {
        // Try to fetch real accounts from database (optional enhancement)
        // fetchTestAccounts()
    }
})

const fillCredentials = ({ email, password }) => {
    // Emit event to fill the form
    emit('fill-credentials', { email, password })

    // Show toast
    showToast.value = true
    setTimeout(() => {
        showToast.value = false
    }, 2000)

    // Close the panel
    isOpen.value = false
}

// Account Card Component (inline)
const AccountCard = {
    props: ['email', 'password', 'name', 'role', 'extra'],
    emits: ['fill'],
    template: `
        <div class="bg-white dark:bg-gray-800 rounded p-2 text-xs border border-gray-200 dark:border-gray-700 hover:border-purple-400 dark:hover:border-purple-600 transition-colors cursor-pointer"
             @click="$emit('fill', { email, password })">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="font-semibold text-gray-900 dark:text-white">{{ name }}</div>
                    <div class="text-gray-600 dark:text-gray-400 mt-1">
                        <div class="text-xs">{{ email }}</div>
                        <div class="flex items-center gap-1 text-xs">
                            <span>Pass:</span>
                            <span class="font-mono">{{ password }}</span>
                        </div>
                        <div v-if="extra" class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ extra }}</div>
                    </div>
                </div>
                <div class="ml-2 p-1.5 text-purple-600 dark:text-purple-400">
                    <svg class="w-4 h-4"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                </div>
            </div>
        </div>
    `
}
</script>

<style scoped>
/* Add any additional styles if needed */
</style>