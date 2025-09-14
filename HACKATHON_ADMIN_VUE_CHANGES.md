# Vue Component Updates for Edition-Based Access Control

## Components that need updating:

### 1. **Layout Component (Shared/Layout.vue or similar)**
Add edition switcher in the header/navbar:

```vue
<template>
  <div class="edition-switcher" v-if="userRole === 'hackathon_admin' && editions.length > 0">
    <select 
      v-model="currentEditionId" 
      @change="switchEdition"
      class="form-select"
    >
      <option v-for="edition in editions" :key="edition.id" :value="edition.id">
        {{ edition.name }} ({{ edition.year }})
      </option>
    </select>
  </div>
</template>

<script>
export default {
  props: {
    editions: Array,
    currentEditionId: Number,
  },
  methods: {
    switchEdition() {
      // Switch edition and reload the page
      this.$inertia.post('/hackathon-admin/switch-edition', {
        edition_id: this.currentEditionId
      }, {
        preserveScroll: true,
        preserveState: false,
      });
    }
  }
}
</script>
```

### 2. **Dashboard.vue**
```vue
<template>
  <div>
    <!-- Edition Info Banner -->
    <div class="edition-info-banner" v-if="currentEdition">
      <h3>Managing Edition: {{ currentEdition.name }} ({{ currentEdition.year }})</h3>
      <p>Registration: {{ formatDate(currentEdition.registration_start_date) }} - {{ formatDate(currentEdition.registration_end_date) }}</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
      <StatCard 
        title="Teams in Edition" 
        :value="statistics.teams" 
        icon="users"
      />
      <StatCard 
        title="Ideas in Edition" 
        :value="statistics.ideas" 
        icon="lightbulb"
      />
      <!-- ... more stats -->
    </div>

    <!-- Recent Activities -->
    <div class="recent-activities">
      <h3>Recent Ideas in {{ currentEdition?.name }}</h3>
      <!-- ... -->
    </div>
  </div>
</template>

<script>
export default {
  props: {
    statistics: Object,
    editions: Array,
    currentEditionId: Number,
    recentIdeas: Array,
  },
  computed: {
    currentEdition() {
      return this.editions.find(e => e.id === this.currentEditionId);
    }
  }
}
</script>
```

### 3. **Ideas/Index.vue**
```vue
<template>
  <div>
    <!-- Edition Filter -->
    <div class="filters">
      <label>Edition:</label>
      <select v-model="filters.edition_id" @change="applyFilters">
        <option value="">All My Editions</option>
        <option v-for="edition in editions" :key="edition.id" :value="edition.id">
          {{ edition.name }} ({{ edition.year }})
        </option>
      </select>

      <!-- Other filters -->
      <input 
        v-model="filters.search" 
        @input="applyFilters"
        placeholder="Search ideas..."
      />

      <select v-model="filters.status" @change="applyFilters">
        <option value="">All Status</option>
        <option value="draft">Draft</option>
        <option value="submitted">Submitted</option>
        <!-- ... -->
      </select>
    </div>

    <!-- Ideas Table -->
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Team</th>
          <th>Edition</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="idea in ideas.data" :key="idea.id">
          <td>{{ idea.id }}</td>
          <td>{{ idea.title }}</td>
          <td>{{ idea.team?.name }}</td>
          <td>{{ idea.edition?.name }}</td>
          <td>
            <span :class="`status-${idea.status}`">
              {{ idea.status }}
            </span>
          </td>
          <td>
            <Link :href="`/hackathon-admin/ideas/${idea.id}`">View</Link>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination -->
    <Pagination :links="ideas.links" />
  </div>
</template>

<script>
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';

export default {
  props: {
    ideas: Object,
    editions: Array,
    currentEditionId: Number,
    filters: Object,
  },
  setup(props) {
    const filters = reactive({
      edition_id: props.filters.edition_id || props.currentEditionId,
      search: props.filters.search || '',
      status: props.filters.status || '',
    });

    const applyFilters = () => {
      router.get('/hackathon-admin/ideas', filters, {
        preserveState: true,
        preserveScroll: true,
      });
    };

    return { filters, applyFilters };
  }
}
</script>
```

### 4. **Teams/Index.vue**
```vue
<template>
  <div>
    <!-- Edition Context -->
    <div class="page-header">
      <h1>Teams Management</h1>
      <p>Edition: {{ currentEdition?.name }} ({{ currentEdition?.year }})</p>
    </div>

    <!-- Filters -->
    <div class="filters">
      <select v-model="selectedEditionId" @change="filterByEdition">
        <option v-for="edition in editions" :key="edition.id" :value="edition.id">
          {{ edition.name }}
        </option>
      </select>
    </div>

    <!-- Teams List -->
    <div class="teams-grid">
      <div v-for="team in teams.data" :key="team.id" class="team-card">
        <h3>{{ team.name }}</h3>
        <p>Edition: {{ team.edition?.name }}</p>
        <p>Leader: {{ team.leader?.name }}</p>
        <p>Members: {{ team.members_count }}</p>
        <Link :href="`/hackathon-admin/teams/${team.id}`">View Details</Link>
      </div>
    </div>
  </div>
</template>
```

### 5. **Tracks/Index.vue**
```vue
<template>
  <div>
    <!-- Edition-specific header -->
    <div class="header">
      <h1>Tracks for {{ currentEdition?.name }}</h1>
      <Link 
        :href="`/hackathon-admin/tracks/create?edition_id=${currentEditionId}`"
        class="btn btn-primary"
      >
        Create Track
      </Link>
    </div>

    <!-- Statistics filtered by edition -->
    <div class="stats">
      <div>Total Tracks in Edition: {{ statistics.total }}</div>
      <div>Active: {{ statistics.active }}</div>
      <div>With Teams: {{ statistics.with_teams }}</div>
    </div>

    <!-- Tracks Table -->
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Edition</th>
          <th>Teams</th>
          <th>Ideas</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="track in tracks.data" :key="track.id">
          <td>{{ track.name }}</td>
          <td>{{ track.edition?.name }}</td>
          <td>{{ track.teams_count }}</td>
          <td>{{ track.ideas_count }}</td>
          <td>
            <span :class="track.is_active ? 'active' : 'inactive'">
              {{ track.is_active ? 'Active' : 'Inactive' }}
            </span>
          </td>
          <td>
            <Link :href="`/hackathon-admin/tracks/${track.id}/edit`">Edit</Link>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
```

### 6. **Workshops/Index.vue**
```vue
<template>
  <div>
    <!-- Edition Context -->
    <div class="edition-context">
      <h2>Workshops for {{ currentEdition?.name }}</h2>
      <p>Showing workshops for edition: {{ currentEdition?.year }}</p>
    </div>

    <!-- Create button with edition context -->
    <Link 
      :href="`/hackathon-admin/workshops/create?edition_id=${currentEditionId}`"
      class="btn btn-primary"
    >
      Create Workshop for {{ currentEdition?.name }}
    </Link>

    <!-- Workshops List -->
    <div v-for="workshop in workshops.data" :key="workshop.id">
      <h3>{{ workshop.title }}</h3>
      <p>Edition: {{ workshop.edition?.name }}</p>
      <p>Date: {{ formatDate(workshop.date) }}</p>
      <p>Registrations: {{ workshop.registrations_count }}</p>
    </div>
  </div>
</template>
```

### 7. **Reports/Index.vue**
```vue
<template>
  <div>
    <!-- Edition Selector for Reports -->
    <div class="report-filters">
      <h2>Reports for Edition</h2>
      <select v-model="selectedEditionId" @change="loadReports">
        <option value="">All My Editions</option>
        <option v-for="edition in editions" :key="edition.id" :value="edition.id">
          {{ edition.name }} ({{ edition.year }})
        </option>
      </select>
    </div>

    <!-- Report Cards -->
    <div class="reports-grid">
      <div class="report-card">
        <h3>Teams Report</h3>
        <p>Total Teams: {{ reports.teams_count }}</p>
        <p>Edition: {{ currentEdition?.name }}</p>
        <button @click="exportReport('teams')">Export</button>
      </div>

      <div class="report-card">
        <h3>Ideas Report</h3>
        <p>Total Ideas: {{ reports.ideas_count }}</p>
        <p>By Status:</p>
        <ul>
          <li v-for="(count, status) in reports.ideas_by_status" :key="status">
            {{ status }}: {{ count }}
          </li>
        </ul>
        <button @click="exportReport('ideas')">Export</button>
      </div>
    </div>

    <!-- Charts filtered by edition -->
    <div class="charts">
      <IdeasByTrackChart :data="chartData.ideasByTrack" :edition="currentEdition" />
      <TeamsTimelineChart :data="chartData.teamsTimeline" :edition="currentEdition" />
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    exportReport(type) {
      window.location.href = `/hackathon-admin/reports/export/${type}?edition_id=${this.selectedEditionId}`;
    }
  }
}
</script>
```

### 8. **Create/Edit Forms**
All create and edit forms should include edition context:

```vue
<!-- Teams/Create.vue -->
<template>
  <form @submit.prevent="createTeam">
    <div class="form-group">
      <label>Edition</label>
      <select v-model="form.edition_id" required>
        <option v-for="edition in editions" :key="edition.id" :value="edition.id">
          {{ edition.name }} ({{ edition.year }})
        </option>
      </select>
    </div>
    
    <!-- Other form fields -->
    <div class="form-group">
      <label>Team Name</label>
      <input v-model="form.name" required />
    </div>

    <button type="submit">Create Team</button>
  </form>
</template>

<script>
export default {
  props: {
    editions: Array,
    currentEditionId: Number,
  },
  data() {
    return {
      form: {
        edition_id: this.currentEditionId,
        name: '',
        // ... other fields
      }
    };
  }
}
</script>
```

## Common Patterns to Apply:

1. **Always show edition context** in page headers
2. **Pre-select current edition** in forms
3. **Include edition in listings** for clarity
4. **Filter all data** by accessible editions
5. **Show edition info** in cards and tables
6. **Pass edition_id** in create/edit links
7. **Display edition-specific statistics**
8. **Export data** filtered by edition
