<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, defineProps, ref, watch } from 'vue';

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

const props = defineProps<{
    students: Student[];
    yearLevels: YearLevel[];
    sections: Section[];
}>();

const selectedStudent = ref('');
const selectedYearLevel = ref('');
const selectedSection = ref('');

const errors = ref({
    student: '',
    yearLevel: '',
    section: '',
});

const filteredSections = computed(() => {
    const level = props.yearLevels.find((lvl) => lvl.year_level === selectedYearLevel.value);
    return level ? props.sections.filter((sec) => sec.year_level_id === level.id) : [];
});

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

function saveForm() {
    errors.value = { student: '', yearLevel: '', section: '' };

    if (!selectedStudent.value) errors.value.student = 'Please select a student.';
    if (!selectedYearLevel.value) errors.value.yearLevel = 'Please select a year level.';
    if (!selectedSection.value) errors.value.section = 'Please select a section.';

    if (!Object.values(errors.value).some(Boolean)) {
        router.post(
            '/enroll',
            {
                user_id: selectedStudent.value,
                year_level: selectedYearLevel.value,
                section: selectedSection.value,
            },
            {
                onSuccess: () => {
                    selectedStudent.value = '';
                    selectedYearLevel.value = '';
                    selectedSection.value = '';
                },
                onError: (backendErrors) => {
                    errors.value = {
                        student: backendErrors.user_id || '',
                        yearLevel: backendErrors.year_level || '',
                        section: backendErrors.section || '',
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

            <!-- Submit -->
            <button class="w-full rounded bg-blue-600 py-2 text-white hover:bg-blue-700" @click="saveForm">Save</button>
        </div>
    </AppLayout>
</template>
