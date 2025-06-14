<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

interface Student {
    id: string;
    name: string;
}

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

const props = defineProps<{
    students: Student[];
    yearLevels: YearLevel[];
    sections: Section[];
    subjects: Subject[];
}>();

const selectedStudent = ref('');
const selectedYearLevel = ref('');
const selectedSection = ref('');
const selectedSubject = ref('');

const errors = ref({
    student: '',
    yearLevel: '',
    section: '',
    subject: '',
});

// Filtered subjects based on year level and section
const filteredSubjects = computed(() => {
    const yearLevel = props.yearLevels.find((y) => y.year_level === selectedYearLevel.value);
    const section = props.sections.find((s) => s.section_name === selectedSection.value);
    if (yearLevel && section) {
        return props.subjects.filter((sub) => sub.year_level_id === yearLevel.id && sub.section_id === section.id);
    }
    return [];
});

// Filtered sections based on the selected year level
const filteredSections = computed(() => {
    const level = props.yearLevels.find((lvl) => lvl.year_level === selectedYearLevel.value);
    return level ? props.sections.filter((sec) => sec.year_level_id === level.id) : [];
});

// Watchers to initialize default values from the props
watch(
    () => props.students,
    () => {
        selectedStudent.value = props.students[0]?.id || '';
    },
    { immediate: true },
);

watch(
    () => props.yearLevels,
    () => {
        selectedYearLevel.value = props.yearLevels[0]?.year_level || '';
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

// Function to save the form data
function saveForm() {
    // Clear previous errors
    errors.value = { student: '', yearLevel: '', section: '', subject: '' };

    // Validation checks
    if (!selectedStudent.value) errors.value.student = 'Please select a student.';
    if (!selectedYearLevel.value) errors.value.yearLevel = 'Please select a year level.';
    if (!selectedSection.value) errors.value.section = 'Please select a section.';
    if (!selectedSubject.value) errors.value.subject = 'Please select a subject.';

    // If there are no errors, submit the form
    if (!Object.values(errors.value).some(Boolean)) {
        router.post(
            '/enroll',
            {
                user_id: selectedStudent.value,
                year_level: selectedYearLevel.value,
                section: selectedSection.value,
                subject: selectedSubject.value,
            },
            {
                onSuccess: () => {
                    // Reset form after success
                    selectedStudent.value = '';
                    selectedYearLevel.value = '';
                    selectedSection.value = '';
                    selectedSubject.value = '';
                },
                onError: (backendErrors) => {
                    // Set errors from the backend
                    errors.value = {
                        student: backendErrors.user_id || '',
                        yearLevel: backendErrors.year_level || '',
                        section: backendErrors.section || '',
                        subject: backendErrors.subject || '',
                    };
                },
            },
        );
    }
}
</script>

<template>
    <Head title="Enroll" />
    <AppLayout :breadcrumbs="[{ title: 'Enroll', href: '/enroll' }]">
        <div class="mx-auto max-w-xl space-y-8 p-6">
            <h2 class="mb-4 text-2xl font-bold">Student Enrollment</h2>

            <!-- Student Dropdown -->
            <div>
                <label class="mb-1 block text-sm font-medium">Student</label>
                <select v-model="selectedStudent" class="w-full rounded border p-2">
                    <option disabled value="">Select a student</option>
                    <option v-for="student in props.students" :key="student.id" :value="student.id">
                        {{ student.name }}
                    </option>
                </select>
                <p v-if="errors.student" class="mt-1 text-sm text-red-600">{{ errors.student }}</p>
            </div>

            <!-- Year Level Dropdown -->
            <div>
                <label class="mb-1 block text-sm font-medium">Year Level</label>
                <select v-model="selectedYearLevel" class="w-full rounded border p-2">
                    <option disabled value="">Select a year level</option>
                    <option v-for="level in props.yearLevels" :key="level.id" :value="level.year_level">
                        {{ level.year_level }}
                    </option>
                </select>
                <p v-if="errors.yearLevel" class="mt-1 text-sm text-red-600">{{ errors.yearLevel }}</p>
            </div>

            <!-- Section Dropdown -->
            <div>
                <label class="mb-1 block text-sm font-medium">Section</label>
                <select v-model="selectedSection" class="w-full rounded border p-2">
                    <option disabled value="">Select a section</option>
                    <option v-for="section in filteredSections" :key="section.id" :value="section.section_name">
                        {{ section.section_name }}
                    </option>
                </select>
                <p v-if="errors.section" class="mt-1 text-sm text-red-600">{{ errors.section }}</p>
            </div>

            <!-- Subject Dropdown -->
            <div>
                <label class="mb-1 block text-sm font-medium">Subject</label>
                <select v-model="selectedSubject" class="w-full rounded border p-2">
                    <option disabled value="">Select a subject</option>
                    <option v-for="subject in filteredSubjects" :key="subject.id" :value="subject.subject_name">
                        {{ subject.subject_name }}
                    </option>
                </select>
                <p v-if="errors.subject" class="mt-1 text-sm text-red-600">{{ errors.subject }}</p>
            </div>

            <!-- Submit -->
            <button class="w-full rounded bg-blue-600 py-2 text-white hover:bg-blue-700" @click="saveForm">Save</button>

        </div>
    </AppLayout>
</template>
