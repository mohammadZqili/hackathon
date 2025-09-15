# STEP 5: COMPONENT SPECIFICATIONS
## Detailed Vue Component Specifications

---

## 📋 INSTRUCTIONS
Define exact structure for each reusable component with props, emits, and template.

---

## COMPONENT TEMPLATE:
```
### Component: [Name]
**Path:** resources/js/Components/[folder]/[Name].vue
**Purpose:** [What it does]
**Used In:** [List of pages]

**Props:**
- propName: { type: Type, required: boolean, default: value }

**Emits:**
- eventName: [description of when emitted]

**Slots:**
- slotName: [what goes here]

**Data/State:**
- variableName: [type and purpose]

**Methods:**
- methodName(): [what it does]

**Template Structure:**
[Actual template code or structure]
```

---

# SHARED COMPONENTS

## 1. Status Badge Component

### Component: StatusBadge
**Path:** resources/js/Components/Shared/StatusBadge.vue
**Purpose:** Display color-coded status badges
**Used In:** All pages showing idea/team/user status

**Props:**
- status: { type: String, required: true }
- size: { type: String, default: 'md' } // sm, md, lg
- showIcon: { type: Boolean, default: true }

**Emits:**
None

**Template Structure:**
```vue
<template>
  <span :class="[
    'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium',
    statusClasses[status],
    sizeClasses[size]
  ]">
    <component :is="statusIcons[status]" v-if="showIcon" class="w-3 h-3" />
    <span>{{ statusLabels[status] }}</span>
  </span>
</template>

<script setup>
const statusClasses = {
  draft: 'bg-gray-100 text-gray-800',
  pending: 'bg-yellow-100 text-yellow-800',
  under_review: 'bg-blue-100 text-blue-800',
  needs_revision: 'bg-orange-100 text-orange-800',
  approved: 'bg-green-100 text-green-800',
  rejected: 'bg-red-100 text-red-800'
}

const statusLabels = {
  draft: 'مسودة / Draft',
  pending: 'قيد المراجعة / Pending',
  under_review: 'تحت المراجعة / Under Review',
  needs_revision: 'يحتاج تعديل / Needs Revision',
  approved: 'مقبول / Approved',
  rejected: 'مرفوض / Rejected'
}
</script>
```

---

## 2. File Upload Component

### Component: FileUploader
**Path:** resources/js/Components/Shared/FileUploader.vue
**Purpose:** Handle file uploads with progress and validation
**Used In:** Idea creation/edit pages

**Props:**
- maxFiles: { type: Number, default: 8 }
- maxSize: { type: Number, default: 15 } // in MB
- acceptedTypes: { type: Array, default: ['pdf', 'ppt', 'pptx', 'doc', 'docx'] }
- existingFiles: { type: Array, default: [] }

**Emits:**
- filesAdded: [files array]
- fileRemoved: [file id]
- error: [error message]

**Data/State:**
- uploading: Boolean
- progress: Number (0-100)
- files: Array

**Methods:**
- handleDrop(e): Process dropped files
- validateFile(file): Check size and type
- uploadFile(file): Upload with progress
- removeFile(id): Remove from list

**Template Structure:**
```vue
<template>
  <div class="space-y-4">
    <!-- Drop Zone -->
    <div 
      @drop.prevent="handleDrop"
      @dragover.prevent
      @dragenter.prevent
      class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors"
    >
      <CloudUploadIcon class="mx-auto h-12 w-12 text-gray-400" />
      <p class="mt-2 text-sm text-gray-600">
        اسحب الملفات هنا أو انقر للاختيار
        <br>
        Drop files here or click to select
      </p>
      <input
        type="file"
        :accept="acceptedTypes.map(t => `.${t}`).join(',')"
        multiple
        @change="handleFileSelect"
        class="hidden"
        ref="fileInput"
      >
      <button
        @click="$refs.fileInput.click()"
        type="button"
        class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
      >
        اختر الملفات / Choose Files
      </button>
    </div>

    <!-- Progress Bar -->
    <div v-if="uploading" class="relative pt-1">
      <div class="flex mb-2 items-center justify-between">
        <span class="text-xs font-semibold text-blue-700">
          جاري الرفع / Uploading...
        </span>
        <span class="text-xs font-semibold text-blue-700">
          {{ progress }}%
        </span>
      </div>
      <div class="overflow-hidden h-2 text-xs flex rounded bg-blue-200">
        <div 
          :style="`width: ${progress}%`"
          class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-300"
        ></div>
      </div>
    </div>

    <!-- Files List -->
    <div v-if="files.length > 0" class="space-y-2">
      <h4 class="text-sm font-medium text-gray-700">
        الملفات المرفوعة / Uploaded Files ({{ files.length }}/{{ maxFiles }})
      </h4>
      <div v-for="file in files" :key="file.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
        <div class="flex items-center space-x-3">
          <DocumentIcon class="h-8 w-8 text-gray-400" />
          <div>
            <p class="text-sm font-medium text-gray-900">{{ file.name }}</p>
            <p class="text-xs text-gray-500">{{ formatFileSize(file.size) }}</p>
          </div>
        </div>
        <button
          @click="removeFile(file.id)"
          type="button"
          class="text-red-600 hover:text-red-800"
        >
          <TrashIcon class="h-5 w-5" />
        </button>
      </div>
    </div>

    <!-- Error Message -->
    <div v-if="errorMessage" class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
      {{ errorMessage }}
    </div>
  </div>
</template>
```

---

## 3. Data Table Component

### Component: DataTable
**Path:** resources/js/Components/Shared/DataTable.vue
**Purpose:** Reusable table with sorting, filtering, and pagination
**Used In:** All listing pages

**Props:**
- columns: { type: Array, required: true }
- data: { type: Array, required: true }
- searchable: { type: Boolean, default: true }
- perPage: { type: Number, default: 10 }
- actions: { type: Array, default: [] }

**Emits:**
- sort: [column, direction]
- search: [query]
- action: [action, row]

**Template Structure:**
```vue
<template>
  <div class="bg-white shadow-sm rounded-lg">
    <!-- Search Bar -->
    <div v-if="searchable" class="p-4 border-b">
      <input
        v-model="searchQuery"
        @input="$emit('search', searchQuery)"
        type="text"
        placeholder="بحث / Search..."
        class="w-full px-3 py-2 border rounded-md"
      >
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th
              v-for="column in columns"
              :key="column.key"
              @click="handleSort(column)"
              class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
            >
              <div class="flex items-center justify-between">
                <span>{{ column.label }}</span>
                <ChevronUpDownIcon v-if="column.sortable" class="h-4 w-4" />
              </div>
            </th>
            <th v-if="actions.length > 0" class="px-6 py-3">
              <span class="sr-only">Actions</span>
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="row in paginatedData" :key="row.id" class="hover:bg-gray-50">
            <td
              v-for="column in columns"
              :key="column.key"
              class="px-6 py-4 whitespace-nowrap text-sm"
            >
              <slot :name="`cell-${column.key}`" :value="row[column.key]" :row="row">
                {{ row[column.key] }}
              </slot>
            </td>
            <td v-if="actions.length > 0" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex items-center justify-end space-x-2">
                <button
                  v-for="action in actions"
                  :key="action.name"
                  @click="$emit('action', action.name, row)"
                  :class="action.class"
                  class="px-3 py-1 rounded-md text-sm"
                >
                  {{ action.label }}
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="px-4 py-3 border-t">
      <Pagination
        :current-page="currentPage"
        :total="filteredData.length"
        :per-page="perPage"
        @change="currentPage = $event"
      />
    </div>
  </div>
</template>
```

---

## 4. Modal Component

### Component: Modal
**Path:** resources/js/Components/Shared/Modal.vue
**Purpose:** Reusable modal dialog
**Used In:** All pages needing modals

**Props:**
- show: { type: Boolean, required: true }
- title: { type: String, default: '' }
- size: { type: String, default: 'md' } // sm, md, lg, xl
- closeable: { type: Boolean, default: true }

**Emits:**
- close: When modal should close

**Slots:**
- default: Modal body content
- footer: Modal footer actions

**Template Structure:**
```vue
<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
        <!-- Backdrop -->
        <div
          class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
          @click="closeable && $emit('close')"
        ></div>

        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
          <div
            :class="[
              'relative bg-white rounded-lg shadow-xl transition-all',
              sizeClasses[size]
            ]"
          >
            <!-- Header -->
            <div class="border-b px-6 py-4">
              <h3 class="text-lg font-semibold text-gray-900">
                {{ title }}
              </h3>
              <button
                v-if="closeable"
                @click="$emit('close')"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
              >
                <XMarkIcon class="h-6 w-6" />
              </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-4">
              <slot />
            </div>

            <!-- Footer -->
            <div v-if="$slots.footer" class="border-t px-6 py-4 bg-gray-50">
              <slot name="footer" />
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
const sizeClasses = {
  sm: 'max-w-md w-full',
  md: 'max-w-lg w-full',
  lg: 'max-w-2xl w-full',
  xl: 'max-w-4xl w-full'
}
</script>
```

---

## 5. Confirmation Dialog

### Component: ConfirmDialog
**Path:** resources/js/Components/Shared/ConfirmDialog.vue
**Purpose:** Confirmation dialog for destructive actions
**Used In:** Delete operations, status changes

**Props:**
- show: { type: Boolean, required: true }
- title: { type: String, required: true }
- message: { type: String, required: true }
- confirmText: { type: String, default: 'تأكيد / Confirm' }
- cancelText: { type: String, default: 'إلغاء / Cancel' }
- danger: { type: Boolean, default: false }

**Emits:**
- confirm: User confirmed
- cancel: User cancelled

**Template Structure:**
```vue
<template>
  <Modal :show="show" :title="title" size="sm" @close="$emit('cancel')">
    <p class="text-sm text-gray-600">{{ message }}</p>
    
    <template #footer>
      <div class="flex justify-end space-x-3">
        <button
          @click="$emit('cancel')"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
        >
          {{ cancelText }}
        </button>
        <button
          @click="$emit('confirm')"
          :class="[
            'px-4 py-2 text-sm font-medium text-white rounded-md',
            danger ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700'
          ]"
        >
          {{ confirmText }}
        </button>
      </div>
    </template>
  </Modal>
</template>
```

---

## 6. QR Code Scanner

### Component: QRScanner
**Path:** resources/js/Components/Shared/QRScanner.vue
**Purpose:** Scan QR codes using device camera
**Used In:** Workshop check-in page

**Props:**
- active: { type: Boolean, default: false }

**Emits:**
- scan: [decoded QR data]
- error: [error message]

**Methods:**
- startScanning(): Initialize camera
- stopScanning(): Stop camera
- processQR(data): Validate and emit

**Template Structure:**
```vue
<template>
  <div class="relative">
    <div v-if="active" class="relative bg-black rounded-lg overflow-hidden">
      <video
        ref="video"
        class="w-full h-64 object-cover"
        autoplay
        playsinline
      ></video>
      <div class="absolute inset-0 border-2 border-green-500 opacity-50"></div>
      <div class="absolute top-2 right-2">
        <button
          @click="stopScanning"
          class="bg-red-600 text-white p-2 rounded-full"
        >
          <XMarkIcon class="h-5 w-5" />
        </button>
      </div>
    </div>
    
    <div v-else class="bg-gray-100 rounded-lg p-12 text-center">
      <QrCodeIcon class="mx-auto h-16 w-16 text-gray-400" />
      <p class="mt-4 text-gray-600">
        انقر لبدء المسح / Click to start scanning
      </p>
      <button
        @click="startScanning"
        class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
      >
        بدء المسح / Start Scanning
      </button>
    </div>
  </div>
</template>
```

---

## 7. Statistics Card

### Component: StatCard
**Path:** resources/js/Components/Shared/StatCard.vue
**Purpose:** Display statistics with icon and trend
**Used In:** All dashboards

**Props:**
- title: { type: String, required: true }
- value: { type: [String, Number], required: true }
- icon: { type: Object, required: true }
- trend: { type: Number, default: null }
- trendLabel: { type: String, default: '' }
- color: { type: String, default: 'blue' }

**Template Structure:**
```vue
<template>
  <div class="bg-white overflow-hidden shadow rounded-lg">
    <div class="p-5">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <div :class="`bg-${color}-500 rounded-md p-3`">
            <component :is="icon" class="h-6 w-6 text-white" />
          </div>
        </div>
        <div class="ml-5 w-0 flex-1">
          <dl>
            <dt class="text-sm font-medium text-gray-500 truncate">
              {{ title }}
            </dt>
            <dd class="flex items-baseline">
              <div class="text-2xl font-semibold text-gray-900">
                {{ value }}
              </div>
              <div v-if="trend !== null" class="ml-2 flex items-baseline text-sm font-semibold">
                <ArrowUpIcon
                  v-if="trend > 0"
                  class="self-center flex-shrink-0 h-5 w-5 text-green-500"
                />
                <ArrowDownIcon
                  v-else
                  class="self-center flex-shrink-0 h-5 w-5 text-red-500"
                />
                <span :class="trend > 0 ? 'text-green-600' : 'text-red-600'">
                  {{ Math.abs(trend) }}%
                </span>
                <span class="text-gray-500 ml-1">{{ trendLabel }}</span>
              </div>
            </dd>
          </dl>
        </div>
      </div>
    </div>
  </div>
</template>
```

---

## 8. Notification Bell

### Component: NotificationBell
**Path:** resources/js/Components/Shared/NotificationBell.vue
**Purpose:** Show notifications with badge
**Used In:** Header/Navigation

**Props:**
- notifications: { type: Array, default: [] }
- unreadCount: { type: Number, default: 0 }

**Emits:**
- open: Open notifications panel
- markRead: [notification id]

**Template Structure:**
```vue
<template>
  <div class="relative">
    <button
      @click="showDropdown = !showDropdown"
      class="relative p-2 text-gray-600 hover:text-gray-900"
    >
      <BellIcon class="h-6 w-6" />
      <span
        v-if="unreadCount > 0"
        class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"
      >
        {{ unreadCount > 99 ? '99+' : unreadCount }}
      </span>
    </button>

    <Transition name="dropdown">
      <div
        v-if="showDropdown"
        class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg z-50"
      >
        <div class="p-4 border-b">
          <h3 class="text-lg font-semibold">الإشعارات / Notifications</h3>
        </div>
        
        <div class="max-h-96 overflow-y-auto">
          <div
            v-for="notification in notifications"
            :key="notification.id"
            @click="handleClick(notification)"
            class="p-4 hover:bg-gray-50 cursor-pointer border-b"
            :class="{ 'bg-blue-50': !notification.read }"
          >
            <div class="flex justify-between">
              <p class="text-sm font-medium text-gray-900">
                {{ notification.title }}
              </p>
              <time class="text-xs text-gray-500">
                {{ notification.time }}
              </time>
            </div>
            <p class="mt-1 text-sm text-gray-600">
              {{ notification.message }}
            </p>
          </div>
        </div>
        
        <div class="p-4 border-t">
          <Link
            href="/notifications"
            class="text-sm text-blue-600 hover:text-blue-800"
          >
            عرض الكل / View All
          </Link>
        </div>
      </div>
    </Transition>
  </div>
</template>
```

---

## 9. Team Member Card

### Component: TeamMemberCard
**Path:** resources/js/Components/Team/MemberCard.vue
**Purpose:** Display team member info with actions
**Used In:** Team management pages

**Props:**
- member: { type: Object, required: true }
- isLeader: { type: Boolean, default: false }
- canRemove: { type: Boolean, default: false }

**Emits:**
- remove: Remove member
- makeLeader: Transfer leadership

**Template Structure:**
```vue
<template>
  <div class="bg-white border rounded-lg p-4">
    <div class="flex items-center justify-between">
      <div class="flex items-center space-x-3">
        <div class="flex-shrink-0">
          <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
            <UserIcon class="h-6 w-6 text-gray-600" />
          </div>
        </div>
        <div>
          <p class="text-sm font-medium text-gray-900">
            {{ member.name }}
            <span v-if="isLeader" class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
              قائد / Leader
            </span>
          </p>
          <p class="text-sm text-gray-500">{{ member.email }}</p>
        </div>
      </div>
      
      <div v-if="canRemove && !isLeader" class="flex items-center space-x-2">
        <button
          @click="$emit('remove')"
          class="text-red-600 hover:text-red-800"
        >
          <TrashIcon class="h-5 w-5" />
        </button>
      </div>
    </div>
  </div>
</template>
```

---

## 10. Countdown Timer

### Component: CountdownTimer
**Path:** resources/js/Components/Shared/CountdownTimer.vue
**Purpose:** Show countdown to deadline
**Used In:** Dashboards, deadline displays

**Props:**
- deadline: { type: String, required: true } // ISO date
- label: { type: String, default: '' }
- showSeconds: { type: Boolean, default: false }

**Data/State:**
- days: Number
- hours: Number
- minutes: Number
- seconds: Number
- expired: Boolean

**Methods:**
- updateCountdown(): Calculate remaining time
- formatNumber(n): Add leading zero

**Template Structure:**
```vue
<template>
  <div class="text-center">
    <p v-if="label" class="text-sm text-gray-600 mb-2">{{ label }}</p>
    
    <div v-if="!expired" class="flex justify-center space-x-4">
      <div class="bg-gray-100 rounded-lg p-3">
        <div class="text-2xl font-bold text-gray-900">{{ formatNumber(days) }}</div>
        <div class="text-xs text-gray-600">أيام / Days</div>
      </div>
      <div class="bg-gray-100 rounded-lg p-3">
        <div class="text-2xl font-bold text-gray-900">{{ formatNumber(hours) }}</div>
        <div class="text-xs text-gray-600">ساعات / Hours</div>
      </div>
      <div class="bg-gray-100 rounded-lg p-3">
        <div class="text-2xl font-bold text-gray-900">{{ formatNumber(minutes) }}</div>
        <div class="text-xs text-gray-600">دقائق / Minutes</div>
      </div>
      <div v-if="showSeconds" class="bg-gray-100 rounded-lg p-3">
        <div class="text-2xl font-bold text-gray-900">{{ formatNumber(seconds) }}</div>
        <div class="text-xs text-gray-600">ثواني / Seconds</div>
      </div>
    </div>
    
    <div v-else class="text-red-600 font-semibold">
      انتهى الوقت / Time Expired
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps(['deadline', 'label', 'showSeconds'])

const days = ref(0)
const hours = ref(0)
const minutes = ref(0)
const seconds = ref(0)
const expired = ref(false)

let interval = null

const updateCountdown = () => {
  const now = new Date().getTime()
  const deadlineTime = new Date(props.deadline).getTime()
  const distance = deadlineTime - now
  
  if (distance < 0) {
    expired.value = true
    clearInterval(interval)
    return
  }
  
  days.value = Math.floor(distance / (1000 * 60 * 60 * 24))
  hours.value = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
  minutes.value = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))
  seconds.value = Math.floor((distance % (1000 * 60)) / 1000)
}

const formatNumber = (n) => n.toString().padStart(2, '0')

onMounted(() => {
  updateCountdown()
  interval = setInterval(updateCountdown, 1000)
})

onUnmounted(() => {
  if (interval) clearInterval(interval)
})
</script>
```

---

## COMPONENT SPECIFICATIONS COMPLETE CHECKLIST
- ☐ StatusBadge component defined
- ☐ FileUploader component defined
- ☐ DataTable component defined
- ☐ Modal component defined
- ☐ ConfirmDialog component defined
- ☐ QRScanner component defined
- ☐ StatCard component defined
- ☐ NotificationBell component defined
- ☐ TeamMemberCard component defined
- ☐ CountdownTimer component defined
- ☐ All props specified
- ☐ All emits documented
- ☐ Template structures provided

---

## ADDITIONAL COMPONENTS NEEDED
Based on the pages, these components are also needed:
1. RichTextEditor - For idea descriptions
2. LanguageToggle - For Arabic/English switch
3. ThemeToggle - For dark mode
4. Pagination - For data tables
5. SearchBar - For filtering
6. DateRangePicker - For reports
7. Chart components - For statistics
8. IdeaCard - For idea listings
9. WorkshopCard - For workshop displays
10. InviteModal - For team invitations

---

## NOTES
[Add any component-specific implementation notes]
