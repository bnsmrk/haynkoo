<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Material', href: '/materials' }];

// Page props
const { props } = usePage() as unknown as {
    props: {
        yearLevels: { id: number; year_level: string }[];
        sections: { id: number; section_name: string; year_level_id: number }[];
        subjects: { id: number; subject_name: string; year_level_id: number; section_id: number }[];
    };
};

// Form state
const form = useForm({
    year_level_id: '',
    section_id: '',
    subject_id: '',
    material_type: '',
    file: null as File | null,
});

// File change handler
function handleFileChange(event: Event) {
    const input = event.target as HTMLInputElement;
    if (input.files?.length) {
        form.file = input.files[0];
    }
}

// Submit
function submit() {
    form.post('/materials', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('file');
        },
    });
}

// Filtered dropdown options
const filteredSections = computed(() => props.sections.filter((s) => s.year_level_id === Number(form.year_level_id)));

const filteredSubjects = computed(() => {
    if (!form.year_level_id || !form.section_id) return [];
    return props.subjects.filter((s) => s.year_level_id === Number(form.year_level_id) && s.section_id === Number(form.section_id));
});
</script>

<template>
    <Head title="Upload Material" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6 md:p-10">
            <Card class="mx-auto w-full max-w-4xl shadow-xl">
                <CardHeader class="border-b p-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Upload New Material</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Please select the correct year level, section, and subject before uploading.
                    </p>
                </CardHeader>

                <CardContent class="p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Year Level -->
                            <div>
                                <Label for="year_level">Year Level</Label>
                                <select
                                    id="year_level"
                                    v-model="form.year_level_id"
                                    required
                                    class="mt-2 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus:border-transparent focus:ring-2 focus:ring-primary focus:outline-none dark:border-gray-700 dark:bg-sidebar dark:text-white"
                                >
                                    <option disabled value="">Select Year Level</option>
                                    <option v-for="lvl in props.yearLevels" :key="lvl.id" :value="lvl.id">
                                        {{ lvl.year_level }}
                                    </option>
                                </select>
                            </div>

                            <!-- Section -->
                            <div>
                                <Label for="section">Section</Label>
                                <select
                                    id="section"
                                    v-model="form.section_id"
                                    required
                                    class="mt-2 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus:border-transparent focus:ring-2 focus:ring-primary focus:outline-none dark:border-gray-700 dark:bg-sidebar dark:text-white"
                                >
                                    <option disabled value="">Select Section</option>
                                    <option v-for="sect in filteredSections" :key="sect.id" :value="sect.id">
                                        {{ sect.section_name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Subject -->
                            <div>
                                <Label for="subject">Subject</Label>
                                <select
                                    id="subject"
                                    v-model="form.subject_id"
                                    :disabled="!form.section_id"
                                    required
                                    class="mt-2 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus:border-transparent focus:ring-2 focus:ring-primary focus:outline-none disabled:opacity-50 dark:border-gray-700 dark:bg-sidebar dark:text-white"
                                >
                                    <option disabled value="">Select Subject</option>
                                    <option v-for="subj in filteredSubjects" :key="subj.id" :value="subj.id">
                                        {{ subj.subject_name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Material Type -->
                            <div>
                                <Label for="material_type">Material Type</Label>
                                <select
                                    id="material_type"
                                    v-model="form.material_type"
                                    required
                                    class="mt-2 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus:border-transparent focus:ring-2 focus:ring-primary focus:outline-none dark:border-gray-700 dark:bg-sidebar dark:text-white"
                                >
                                    <option disabled value="">Select Type</option>
                                    <option value="learning_material">Learning Material</option>
                                    <option value="lesson_plan">Lesson Plan</option>
                                </select>
                            </div>
                        </div>

                        <!-- File Upload -->
                        <div>
                            <Label for="fileUpload">Upload File</Label>
                            <input
                                id="fileUpload"
                                type="file"
                                @change="handleFileChange"
                                accept=".pdf,.doc,.docx,.ppt,.pptx"
                                required
                                class="mt-2 block w-full text-sm text-gray-900 file:mr-4 file:rounded-md file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-white hover:file:bg-blue-700 dark:text-white dark:file:bg-blue-500 dark:file:hover:bg-blue-600"
                            />
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Accepted: PDF, DOC(X), PPT(X)</p>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <Button type="submit" class="w-full md:w-auto">Upload Material</Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
