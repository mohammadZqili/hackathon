# 🚀 ULTRA-DETAILED FRONTEND IMPLEMENTATION PLAN
## Complete Step-by-Step Guide to Finish Hackathon System Today

---

## 📌 CRITICAL INFORMATION
- **Backend Status**: 80% Complete (assuming controllers and APIs exist)
- **Frontend Status**: Basic structure exists, needs pages and components
- **Layout**: All pages use same Default.vue layout (header + sidebar)
- **Time to Complete**: 8-10 hours of focused work
- **Priority**: Frontend Vue components and pages

---

## 🎯 IMPLEMENTATION ORDER (FOLLOW EXACTLY)

### ⏰ HOUR 1-2: Authentication & Role Setup

#### Step 1.1: Update Registration Page
**File**: `resources/js/Pages/Auth/Register.vue`

```vue
<template>
    <AuthenticationCard>
        <form @submit.prevent="submit">
            <!-- Name Field -->
            <div>
                <InputLabel for="name" value="الاسم الكامل / Full Name" />
                <TextInput v-model="form.name" type="text" required />
                <InputError :message="form.errors.name" />
            </div>

            <!-- Email Field -->
            <div class="mt-4">
                <InputLabel for="email" value="البريد الإلكتروني / Email" />
                <TextInput v-model="form.email" type="email" required />
                <InputError :message="form.errors.email" />
            </div>

            <!-- Phone Field -->
            <div class="mt-4">
                <InputLabel for="phone" value="رقم الجوال / Phone" />
                <TextInput v-model="form.phone" type="text" placeholder="05XXXXXXXX" required />
                <InputError :message="form.errors.phone" />
            </div>

            <!-- National ID Field -->
            <div class="mt-4">
                <InputLabel for="national_id" value="رقم الهوية / National ID" />
                <TextInput v-model="form.national_id" type="text" maxlength="10" required />
                <InputError :message="form.errors.national_id" />
            </div>

            <!-- Birth Date Field -->
            <div class="mt-4">
                <InputLabel for="birth_date" value="تاريخ الميلاد / Birth Date" />
                <TextInput v-model="form.birth_date" type="date" required />
                <InputError :message="form.errors.birth_date" />
            </div>

            <!-- Occupation Type -->
            <div class="mt-4">
                <InputLabel value="المهنة / Occupation" />
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="radio" v-model="form.occupation" value="student" class="mr-2" />
                        <span>طالب / Student</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" v-model="form.occupation" value="employee" class="mr-2" />
                        <span>موظف / Employee</span>
                    </label>
                </div>
            </div>

            <!-- Job Title (Conditional) -->
            <div v-if="form.occupation === 'employee'" class="mt-4">
                <InputLabel for="job_title" value="المسمى الوظيفي / Job Title" />
                <TextInput v-model="form.job_title" type="text" />
                <InputError :message="form.errors.job_title" />
            </div>

            <!-- Role Selection -->
            <div class="mt-4">
                <InputLabel value="نوع التسجيل / Registration Type" />
                <select v-model="form.role" class="border-gray-300 rounded-md shadow-sm w-full">
                    <option value="">اختر / Select</option>
                    <option value="visitor">زائر (حضور الورش فقط) / Visitor</option>
                    <option value="team_leader">قائد فريق / Team Leader</option>
                    <option value="team_member">عضو فريق / Team Member</option>
                </select>
                <InputError :message="form.errors.role" />
            </div>

            <!-- Password Fields -->
            <div class="mt-4">
                <InputLabel for="password" value="كلمة المرور / Password" />
                <TextInput v-model="form.password" type="password" required />
                <InputError :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="تأكيد كلمة المرور / Confirm Password" />
                <TextInput v-model="form.password_confirmation" type="password" required />
            </div>

            <!-- Submit Button -->
            <div class="mt-6">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    تسجيل / Register
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    national_id: '',
    birth_date: '',
    occupation: 'student',
    job_title: '',
    role: '',
    password: '',
    password_confirmation: ''
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>
```

#### Step 1.2: Update Navigation Sidebar
**File**: `resources/js/Components/NavSidebarDesktop.vue`

Add role-based menu items:

```javascript
// In computed properties or setup
const menuItems = computed(() => {
    const user = usePage().props.auth.user;
    
    // Common items for all authenticated users
    const commonItems = [
        { title: 'لوحة التحكم / Dashboard', icon: 'HomeIcon', route: 'dashboard' }
    ];

    // Role-specific items
    switch(user.role) {
        case 'team_leader':
            return [
                ...commonItems,
                { title: 'فريقي / My Team', icon: 'UsersIcon', route: 'team-leader.team.show' },
                { title: 'الفكرة / Idea', icon: 'LightBulbIcon', route: 'team-leader.idea.show' },
                { title: 'الورش / Workshops', icon: 'AcademicCapIcon', route: 'workshops.index' },
                { title: 'الإشعارات / Notifications', icon: 'BellIcon', route: 'notifications.index' }
            ];
            
        case 'team_member':
            return [
                ...commonItems,
                { title: 'الفريق / Team', icon: 'UsersIcon', route: 'team-member.team.show' },
                { title: 'الفكرة / Idea', icon: 'LightBulbIcon', route: 'team-member.idea.show' },
                { title: 'الورش / Workshops', icon: 'AcademicCapIcon', route: 'workshops.index' }
            ];
            
        case 'track_supervisor':
            return [
                ...commonItems,
                { title: 'الأفكار للمراجعة / Ideas to Review', icon: 'ClipboardListIcon', route: 'track-supervisor.ideas.index' },
                { title: 'الفرق / Teams', icon: 'UsersIcon', route: 'track-supervisor.teams.index' },
                { title: 'المسار / My Track', icon: 'FlagIcon', route: 'track-supervisor.track.show' }
            ];
            
        case 'workshop_supervisor':
            return [
                ...commonItems,
                { title: 'ورشي / My Workshops', icon: 'AcademicCapIcon', route: 'workshop-supervisor.workshops.index' },
                { title: 'تسجيل الحضور / Check-in', icon: 'QrCodeIcon', route: 'workshop-supervisor.checkin' },
                { title: 'التقارير / Reports', icon: 'ChartBarIcon', route: 'workshop-supervisor.reports' }
            ];
            
        case 'hackathon_admin':
            return [
                ...commonItems,
                { title: 'نظرة عامة / Overview', icon: 'ChartPieIcon', route: 'hackathon-admin.overview' },
                { title: 'الفرق / Teams', icon: 'UsersIcon', route: 'hackathon-admin.teams.index' },
                { title: 'الأفكار / Ideas', icon: 'LightBulbIcon', route: 'hackathon-admin.ideas.index' },
                { title: 'المسارات / Tracks', icon: 'FlagIcon', route: 'hackathon-admin.tracks.index' },
                { title: 'الورش / Workshops', icon: 'AcademicCapIcon', route: 'hackathon-admin.workshops.index' },
                { title: 'الأخبار / News', icon: 'NewspaperIcon', route: 'hackathon-admin.news.index' },
                { title: 'التقارير / Reports', icon: 'DocumentReportIcon', route: 'hackathon-admin.reports' }
            ];
            
        case 'system_admin':
            return [
                ...commonItems,
                { title: 'النسخ / Editions', icon: 'CalendarIcon', route: 'system-admin.editions.index' },
                { title: 'المستخدمون / Users', icon: 'UsersIcon', route: 'system-admin.users.index' },
                { title: 'الإعدادات / Settings', icon: 'CogIcon', route: 'system-admin.settings' },
                { title: 'السجلات / Logs', icon: 'ServerIcon', route: 'system-admin.logs' }
            ];
            
        case 'visitor':
            return [
                ...commonItems,
                { title: 'الورش المتاحة / Available Workshops', icon: 'AcademicCapIcon', route: 'visitor.workshops.index' },
                { title: 'ورشي / My Workshops', icon: 'TicketIcon', route: 'visitor.my-workshops' }
            ];
            
        default:
            return commonItems;
    }
});
```

---

### ⏰ HOUR 3-4: Team Leader Pages

#### Step 2.1: Team Leader Dashboard
**File**: `resources/js/Pages/TeamLeader/Dashboard.vue`

```vue
<template>
    <AppLayout title="لوحة تحكم قائد الفريق / Team Leader Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Status Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <!-- Team Status Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <UsersIcon class="h-6 w-6 text-white" />
                            </div>
                            <div class="ml-5">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    حالة الفريق / Team Status
                                </div>
                                <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ teamStatus }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Idea Status Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <LightBulbIcon class="h-6 w-6 text-white" />
                            </div>
                            <div class="ml-5">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    حالة الفكرة / Idea Status
                                </div>
                                <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ ideaStatus }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Members Count Card -->
                    <div class="