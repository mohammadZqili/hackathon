<template>
    <Default>
        <Head title="Edit Track" />

        <div class="container mx-auto px-4 py-8">
            <div class="max-w-2xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Edit Track</h1>
                    <p class="mt-2 text-gray-600">Update track information for {{ edition?.name }}</p>
                </div>

                <!-- Form -->
                <div class="bg-white rounded-lg shadow p-6">
                    <form @submit.prevent="submit">
                        <div class="space-y-6">
                            <!-- Track Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Track Name *
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    id="name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-mint-500 focus:ring-mint-500"
                                    required
                                />
                                <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Description *
                                </label>
                                <textarea
                                    v-model="form.description"
                                    id="description"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-mint-500 focus:ring-mint-500"
                                    required
                                ></textarea>
                                <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.description }}
                                </div>
                            </div>

                            <!-- Max Teams -->
                            <div>
                                <label for="max_teams" class="block text-sm font-medium text-gray-700">
                                    Maximum Teams
                                </label>
                                <input
                                    v-model="form.max_teams"
                                    type="number"
                                    id="max_teams"
                                    min="1"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-mint-500 focus:ring-mint-500"
                                    placeholder="Leave empty for unlimited"
                                />
                                <div v-if="form.errors.max_teams" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.max_teams }}
                                </div>
                            </div>

                            <!-- Supervisor -->
                            <div>
                                <label for="supervisor_id" class="block text-sm font-medium text-gray-700">
                                    Track Supervisor
                                </label>
                                <select
                                    v-model="form.supervisor_id"
                                    id="supervisor_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-mint-500 focus:ring-mint-500"
                                >
                                    <option value="">Select a supervisor</option>
                                    <option v-for="supervisor in supervisors" :key="supervisor.id" :value="supervisor.id">
                                        {{ supervisor.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.supervisor_id" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.supervisor_id }}
                                </div>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">
                                    Status *
                                </label>
                                <select
                                    v-model="form.status"
                                    id="status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-mint-500 focus:ring-mint-500"
                                    required
                                >
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.status }}
                                </div>
                            </div>

                            <!-- Evaluation Criteria -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Evaluation Criteria
                                </label>
                                <div class="space-y-2">
                                    <div v-for="(criterion, index) in form.evaluation_criteria" :key="index" class="flex gap-2">
                                        <input
                                            v-model="criterion.name"
                                            type="text"
                                            placeholder="Criterion name"
                                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-mint-500 focus:ring-mint-500"
                                        />
                                        <input
                                            v-model="criterion.weight"
                                            type="number"
                                            placeholder="Weight"
                                            min="0"
                                            max="100"
                                            class="w-24 rounded-md border-gray-300 shadow-sm focus:border-mint-500 focus:ring-mint-500"
                                        />
                                        <button
                                            @click="removeCriterion(index)"
                                            type="button"
                                            class="px-3 py-2 text-red-600 hover:text-red-900"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <button
                                        @click="addCriterion"
                                        type="button"
                                        class="mt-2 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                                    >
                                        <i class="fas fa-plus mr-2"></i>
                                        Add Criterion
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="mt-8 flex justify-end space-x-4">
                            <Link
                                :href="route('hackathon-admin.tracks.index')"
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 bg-mint-600 text-white rounded-md hover:bg-mint-700 disabled:opacity-50"
                            >
                                Update Track
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Default>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Default from '@/Layouts/Default.vue';

const props = defineProps({
    track: Object,
    supervisors: Array,
    edition: Object,
});

const form = useForm({
    name: props.track.name,
    description: props.track.description,
    max_teams: props.track.max_teams,
    supervisor_id: props.track.supervisor_id || '',
    status: props.track.status,
    evaluation_criteria: props.track.evaluation_criteria || [],
});

const addCriterion = () => {
    form.evaluation_criteria.push({ name: '', weight: 25 });
};

const removeCriterion = (index) => {
    form.evaluation_criteria.splice(index, 1);
};

const submit = () => {
    form.put(route('hackathon-admin.tracks.update', props.track.id));
};
</script>