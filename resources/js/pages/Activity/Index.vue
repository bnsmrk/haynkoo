<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

interface YearLevel {
    id: string;
    year_level: string;
}

interface Section {
    id: string;
    section_name: string;
    year_level_id: string;
}

interface Subject {
    id: string;
    subject_name: string;
    year_level_id: string;
    section_id: string;
}

interface Activity {
    id: string;
    title: string;
    date_time: string;
    year_level_id: string;
    section_id: string;
    subject_id: string;
    yearLevel?: YearLevel;
    section?: Section;
    subject?: Subject;
}

const props = defineProps<{
    yearLevels: YearLevel[];
    sections: Section[];
    subjects: Subject[];
    activities: Activity[];
}>();

const title = ref('');
const date = ref('');
const time = ref('');
const selectedYearLevel = ref('');
const selectedSection = ref('');
const selectedSubject = ref('');
const currentActivityId = ref<string | null>(null);
const showModal = ref(false);
const showDeleteModal = ref(false);
const activityToDelete = ref<Activity | null>(null);

const errors = ref({
    title: '',
    date: '',
    time: '',
    yearLevel: '',
    section: '',
    subject: '',
});

const filteredSubjects = computed(() => {
    const yearLevel = props.yearLevels.find((y) => y.id === selectedYearLevel.value);
    const section = props.sections.find((s) => s.section_name === selectedSection.value);
    if (yearLevel && section) {
        return props.subjects.filter((sub) => sub.year_level_id === yearLevel.id && sub.section_id === section.id);
    }
    return [];
});

const filteredSections = computed(() => {
    const level = props.yearLevels.find((lvl) => lvl.id === selectedYearLevel.value);
    return level ? props.sections.filter((sec) => sec.year_level_id === level.id) : [];
});

watch(
    () => props.yearLevels,
    () => {
        selectedYearLevel.value = props.yearLevels[0]?.id || '';
    },
    { immediate: true },
);

watch(
    () => props.sections,
    () => {
        selectedSection.value = props.sections[0]?.section_name || '';
    },
    { immediate: true },
);

function validateForm() {
    errors.value = { title: '', date: '', time: '', yearLevel: '', section: '', subject: '' };
    let isValid = true;

    if (!title.value) {
        errors.value.title = 'Please enter a title.';
        isValid = false;
    }
    if (!date.value) {
        errors.value.date = 'Please select a date.';
        isValid = false;
    }
    if (!time.value) {
        errors.value.time = 'Please select a time.';
        isValid = false;
    }
    if (!selectedYearLevel.value) {
        errors.value.yearLevel = 'Please select a year level.';
        isValid = false;
    }
    if (!selectedSection.value) {
        errors.value.section = 'Please select a section.';
        isValid = false;
    }
    if (!selectedSubject.value) {
        errors.value.subject = 'Please select a subject.';
        isValid = false;
    }

    const timePattern = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/;
    if (!timePattern.test(time.value)) {
        errors.value.time = 'Time must be in HH:MM format (24-hour clock)';
        isValid = false;
    }

    return isValid;
}

function saveForm() {
    if (!validateForm()) return;

    const formData = {
        title: title.value,
        date: date.value,
        time: time.value,
        year_level_id: selectedYearLevel.value,
        section_id: props.sections.find((s) => s.section_name === selectedSection.value)?.id || '',
        subject_id: props.subjects.find((s) => s.subject_name === selectedSubject.value)?.id || '',
    };

    router.post('/activity', formData, {
        onSuccess: () => {
            resetForm();
            showModal.value = false;
        },
        onError: (backendErrors) => {
            handleBackendErrors(backendErrors);
        },
    });
}

function updateForm() {
    if (!validateForm() || !currentActivityId.value) return;

    const formData = {
        title: title.value,
        date: date.value,
        time: time.value,
        year_level_id: selectedYearLevel.value,
        section_id: props.sections.find((s) => s.section_name === selectedSection.value)?.id || '',
        subject_id: props.subjects.find((s) => s.subject_name === selectedSubject.value)?.id || '',
    };

    router.put(`/activity/${currentActivityId.value}`, formData, {
        onSuccess: () => {
            resetForm();
            showModal.value = false;
        },
        onError: (backendErrors) => {
            handleBackendErrors(backendErrors);
        },
    });
}

function resetForm() {
    title.value = '';
    date.value = '';
    time.value = '';
    selectedYearLevel.value = props.yearLevels[0]?.id || '';
    selectedSection.value = props.sections[0]?.section_name || '';
    selectedSubject.value = '';
    currentActivityId.value = null;
    errors.value = { title: '', date: '', time: '', yearLevel: '', section: '', subject: '' };
}

function handleBackendErrors(backendErrors: any) {
    errors.value = {
        title: backendErrors.title || '',
        date: backendErrors.date || '',
        time: backendErrors.time || '',
        yearLevel: backendErrors.year_level || '',
        section: backendErrors.section || '',
        subject: backendErrors.subject || '',
    };
}

function editActivity(activity: Activity) {
    currentActivityId.value = activity.id;
    title.value = activity.title;

    const [datePart, timePart] = activity.date_time.split(' ');
    date.value = datePart;

    if (timePart) {
        const [hours, minutes] = timePart.split(':');
        time.value = `${hours.padStart(2, '0')}:${minutes.padStart(2, '0')}`;
    } else {
        time.value = '00:00';
    }

    selectedYearLevel.value = activity.year_level_id;
    selectedSection.value = props.sections.find((sec) => sec.id === activity.section_id)?.section_name || '';
    selectedSubject.value = props.subjects.find((sub) => sub.id === activity.subject_id)?.subject_name || '';

    showModal.value = true;
}

function confirmDelete(activity: Activity) {
    activityToDelete.value = activity;
    showDeleteModal.value = true;
}

function deleteActivity() {
    if (!activityToDelete.value) return;

    router.delete(`/activity/${activityToDelete.value.id}`, {
        onSuccess: () => {
            showDeleteModal.value = false;
            activityToDelete.value = null;
        },
    });
}

function closeModal() {
    resetForm();
    showModal.value = false;
}

function closeDeleteModal() {
    showDeleteModal.value = false;
    activityToDelete.value = null;
}
</script>

<template>
    <Head title="Activity" />
    <AppLayout :breadcrumbs="[{ title: 'Activity', href: '/activity' }]">
        <div class="mx-auto max-w-7xl space-y-8 p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold">Activity</h2>
                <button
                    @click="
                        () => {
                            resetForm();
                            showModal = true;
                        }
                    "
                    class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                >
                    Create New Activity
                </button>
            </div>

            <!-- Activities Table -->
            <div class="overflow-x-auto rounded-lg bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Date & Time</th>
                            <!-- <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Year Level</th> -->
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Section</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Subject</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="activity in props.activities" :key="activity.id">
                            <td class="px-6 py-4 whitespace-nowrap">{{ activity.title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ activity.date_time }}</td>
                            <!-- <td class="px-6 py-4 whitespace-nowrap">{{ activity.yearLevel?.year_level }}</td> -->
                            <td class="px-6 py-4 whitespace-nowrap">{{ activity.section?.section_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ activity.subject?.subject_name }}</td>
                            <td class="flex space-x-2 px-6 py-4 whitespace-nowrap">
                                <button @click="editActivity(activity)" class="rounded bg-yellow-500 px-3 py-1 text-white hover:bg-yellow-600">
                                    Edit
                                </button>
                                <button @click="confirmDelete(activity)" class="rounded bg-red-500 px-3 py-1 text-white hover:bg-red-600">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Activity Form Modal -->
            <div v-if="showModal" class="bg-opacity-75 bg-gray fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="w-full max-w-md rounded-lg bg-white shadow-xl">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-medium">
                            {{ currentActivityId ? 'Edit Activity' : 'Create New Activity' }}
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium">Title</label>
                                <input v-model="title" type="text" class="w-full rounded border p-2" />
                                <p v-if="errors.title" class="mt-1 text-xs text-red-500">{{ errors.title }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="mb-1 block text-sm font-medium">Date</label>
                                    <input v-model="date" type="date" class="w-full rounded border p-2" />
                                    <p v-if="errors.date" class="mt-1 text-xs text-red-500">{{ errors.date }}</p>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium">Time</label>
                                    <input
                                        v-model="time"
                                        type="time"
                                        class="w-full rounded border p-2"
                                        step="60"
                                        placeholder="HH:MM"
                                        pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]"
                                    />
                                    <p v-if="errors.time" class="mt-1 text-xs text-red-500">{{ errors.time }}</p>
                                </div>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium">Year Level</label>
                                <select v-model="selectedYearLevel" class="w-full rounded border p-2">
                                    <option value="">Select Year Level</option>
                                    <option v-for="level in props.yearLevels" :key="level.id" :value="level.id">
                                        {{ level.year_level }}
                                    </option>
                                </select>
                                <p v-if="errors.yearLevel" class="mt-1 text-xs text-red-500">{{ errors.yearLevel }}</p>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium">Section</label>
                                <select v-model="selectedSection" class="w-full rounded border p-2">
                                    <option value="">Select Section</option>
                                    <option v-for="section in filteredSections" :key="section.id" :value="section.section_name">
                                        {{ section.section_name }}
                                    </option>
                                </select>
                                <p v-if="errors.section" class="mt-1 text-xs text-red-500">{{ errors.section }}</p>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium">Subject</label>
                                <select v-model="selectedSubject" class="w-full rounded border p-2">
                                    <option value="">Select Subject</option>
                                    <option v-for="subject in filteredSubjects" :key="subject.id" :value="subject.subject_name">
                                        {{ subject.subject_name }}
                                    </option>
                                </select>
                                <p v-if="errors.subject" class="mt-1 text-xs text-red-500">{{ errors.subject }}</p>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button @click="closeModal" class="rounded bg-gray-500 px-4 py-2 text-white hover:bg-gray-600">Cancel</button>
                            <button
                                @click="currentActivityId ? updateForm() : saveForm()"
                                class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                            >
                                {{ currentActivityId ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div v-if="showDeleteModal" class="bg-opacity-75 bg-gray fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="w-full max-w-md rounded-lg bg-white shadow-xl">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-medium">Confirm Deletion</h3>
                        <p class="mb-6">Are you sure you want to delete this activity? This action cannot be undone.</p>

                        <div class="flex justify-end space-x-3">
                            <button @click="closeDeleteModal" class="rounded bg-gray-500 px-4 py-2 text-white hover:bg-gray-600">Cancel</button>
                            <button @click="deleteActivity" class="rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
