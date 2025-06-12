<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';

// Define the Question interface
interface Question {
    type: 'multiple-choice' | 'true-false' | 'checkboxes'; // Add 'checkboxes' as a valid question type
    question: string;
    options: { text: string; isCorrect: boolean }[]; // Array of options with text and isCorrect flag
    answerKey: string; // Correct answer for true/false or multiple-choice
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Questions',
        href: '/questions',
    },
];

const questions = ref<Question[]>([]); // Explicitly type the questions array
const showForm = ref(false); // Controls whether the form is shown

const addQuestion = () => {
    questions.value.push({
        type: 'multiple-choice', // Default type
        question: '',
        options: [
            { text: '', isCorrect: false },
            { text: '', isCorrect: false },
            { text: '', isCorrect: false },
            { text: '', isCorrect: false },
        ], // For multiple-choice or checkboxes questions
        answerKey: '', // For true/false or multiple-choice questions
    });
    showForm.value = true; // Show the form when a question is added
};

const submitForm = async () => {
    try {
        const formattedQuestions = questions.value.map((q) => ({
            ...q,
            options: q.type === 'multiple-choice' || q.type === 'checkboxes' ? q.options.map((opt) => opt.text) : null,
            answerKey:
                q.type === 'multiple-choice'
                    ? q.answerKey
                    : q.type === 'checkboxes'
                      ? q.options
                            .filter((opt) => opt.isCorrect)
                            .map((opt) => opt.text)
                            .join(', ')
                      : q.answerKey,
        }));

        await axios.post('/exams', {
            questions: formattedQuestions,
        });
        alert('Questions added successfully!');
        questions.value = [];
        showForm.value = false; // Hide the form after submission
    } catch (error) {
        const err = error as Error; // Explicitly cast 'error' to 'Error'
        console.error('Error details:', err.message);
        alert('Failed to add the questions.');
    }
};
</script>

<template>
    <Head title="Questions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <div class="mt-8">
                <h2 class="text-2xl font-semibold">Add Questions</h2>
                <Button v-if="!showForm" @click="addQuestion" variant="default" class="mt-4">Add Question</Button>

                <form v-if="showForm" @submit.prevent="submitForm" class="mt-6 space-y-6">
                    <div v-for="(q, index) in questions" :key="index" class="mb-6 border-b pb-6">
                        <h3 class="mb-4 text-xl font-semibold">Question {{ index + 1 }}</h3>

                        <!-- Question Type Selection -->
                        <div class="space-y-2">
                            <label for="type" class="block text-sm font-medium text-gray-700">Question Type</label>
                            <select
                                v-model="q.type"
                                class="mt-1 block h-10 w-full rounded-md border border-gray-300 px-4 py-2 shadow-sm transition-all duration-200 ease-in-out focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                            >
                                <option value="multiple-choice">Multiple Choice</option>
                                <option value="true-false">True/False</option>
                                <option value="checkboxes">Checkboxes</option>
                            </select>
                        </div>

                        <!-- Multiple Choice -->
                        <div v-if="q.type === 'multiple-choice'" class="space-y-4">
                            <label for="question" class="block text-sm font-medium text-gray-700">Question</label>
                            <Input v-model="q.question" placeholder="Enter your question" />

                            <div class="space-y-3">
                                <label class="block text-sm font-medium text-gray-700">Options</label>
                                <div v-for="(option, optIndex) in q.options" :key="optIndex" class="mt-1 space-x-3">
                                    <Input v-model="option.text" :placeholder="'Option ' + (optIndex + 1)" class="w-3/4" />
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="answerKey" class="block text-sm font-medium text-gray-700">Correct Answer</label>
                                <select
                                    v-model="q.answerKey"
                                    class="mt-1 block h-10 w-full rounded-md border border-gray-300 px-4 py-2 shadow-sm transition-all duration-200 ease-in-out focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                                >
                                    <option value="" disabled>Select the correct answer</option>
                                    <option v-for="(option, optIndex) in q.options" :key="optIndex" :value="option.text">
                                        {{ option.text }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- True/False -->
                        <div v-if="q.type === 'true-false'" class="space-y-4">
                            <label for="question" class="block text-sm font-medium text-gray-700">Question</label>
                            <Input v-model="q.question" placeholder="Enter your question" />

                            <div class="space-y-2">
                                <label for="answerKey" class="block text-sm font-medium text-gray-700">Answer</label>
                                <select
                                    v-model="q.answerKey"
                                    class="mt-1 block h-10 w-full rounded-md border border-gray-300 px-4 py-2 shadow-sm transition-all duration-200 ease-in-out focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                                >
                                    <option value="" disabled>Select the correct answer</option>
                                    <option value="true">True</option>
                                    <option value="false">False</option>
                                </select>
                            </div>
                        </div>

                        <!-- Checkboxes -->
                        <div v-if="q.type === 'checkboxes'" class="space-y-4">
                            <label for="question" class="block text-sm font-medium text-gray-700">Question</label>
                            <Input v-model="q.question" placeholder="Enter your question" />

                            <div class="space-y-3">
                                <label class="block text-sm font-medium text-gray-700">Options</label>
                                <div v-for="(option, optIndex) in q.options" :key="optIndex" class="flex items-center space-x-3">
                                    <Input v-model="option.text" :placeholder="'Option ' + (optIndex + 1)" class="w-3/4" />
                                    <Checkbox v-model="option.isCorrect" />
                                    <span class="text-sm text-gray-600">Correct</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <Button type="button" @click="addQuestion" variant="default" class="w-1/2">Add Another Question</Button>
                        <Button type="submit" variant="default" class="w-1/2">Submit All Questions</Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
