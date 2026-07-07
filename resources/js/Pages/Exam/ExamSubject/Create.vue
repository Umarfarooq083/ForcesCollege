<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { computed } from 'vue';

const props = defineProps({
    examTypes: Array,
    Classes: Object,
    Subjects: Array,
});

const form = useForm({
    Title: '',
    ExamId: '',
    ClassId: '',
    rows: [] 
});

const addRow = () => {
    form.rows.push({
        SubjectId: '',
        Date: '',
        Time: '',
        Duration: '',
        CreditHours: '',
        MarksMax: '',
        MarksMin: '',
        RoomNo: '',
    });
};

const filteredSubjects = computed(() => {
    if (!form.ClassId) return [];   // ⬅️ avoid filtering when no ClassId selected
    return props.Subjects.filter(
        subject => subject.ClassId == form.ClassId   // use == in case of string vs int
    );
});

const preventDuplicateDate = (index, selectedDate) => {
    const exists = form.rows.some((r, i) => i !== index && r.Date === selectedDate);
    if (exists) {
        alert('This date is already selected in another row.');
        form.rows[index].Date = ''; // reset
    }
};

const removeRow = (index) => {
    form.rows.splice(index, 1);
};
</script>

<template>
    <Head title="New Exam Subject" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">New Exam Subject</h2>
        </template>

        <form @submit.prevent="form.post(route('examsubject.submit'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">New Exam Subject</div>
                        <div class="body">
                            <div class="row">
                                <!-- Title -->
                                <div class="mb-5 col-md-3">
                                    <InputLabel value="Title" /> <span class="text-danger">★</span>
                                    <TextInput type="text" v-model="form.Title" class="form-control" />
                                    <InputError class="mt-2" :message="form.errors.Title" />
                                </div>

                                <!-- Exam -->
                                <div class="mb-5 col-md-3">
                                    <InputLabel value="Exam" /> <span class="text-danger">★</span>
                                    <select v-model="form.ExamId" class="form-control">
                                        <option value="">Select Exam</option>
                                        <option v-for="type in examTypes" :key="type.id" :value="type.id">
                                            {{ type.ExamName }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.ExamId" />
                                </div>

                                <!-- Class -->
                                <div class="mb-5 col-md-3">
                                    <InputLabel value="Class" /> <span class="text-danger">★</span>
                                    <select v-model="form.ClassId" class="form-control">
                                        <option value="">Select Class</option>
                                        <option v-for="classList in Classes" :key="classList.id" :value="classList.id">
                                            {{ classList.ClassName }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.ClassId" />
                                </div>

                                <!-- Add Row Button -->
                                <div class="mt-4 col-md-3">
                                    <PrimaryButton type="button" @click="addRow">
                                        <i class="fa fa-plus"></i> Add Row
                                    </PrimaryButton>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 col-md-12" v-if="form.rows.length > 0"  v-for="(row, index) in form.rows" :key="index">
                    <div class="p-3 shadow-sm card" >
                        <div class="row">
                            
                            <div class="mb-5 col-md-3">
                                <InputLabel value="Subject " /> <span class="text-danger">★</span>
                                <select v-model="row.SubjectId" class="form-control">
                                    <option value="">Select Subject</option>
                                    <option v-for="subject in filteredSubjects" :key="subject.id" :value="subject.id"
                                        :disabled="form.rows.some((sel_row, i) => i !== index && sel_row.SubjectId === subject.id)" >
                                        {{ subject.SubjectName }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors[`rows.${index}.SubjectId`]" />
                            </div>

                            <div class="mb-3 col-md-3">
                                <InputLabel value="Date" /> <span class="text-danger">★</span>
                                <TextInput 
                                    type="date" 
                                    v-model="row.Date" 
                                    class="form-control"
                                    
                                />
                                <!-- @change="preventDuplicateDate(index, row.Date)" -->
                                <InputError class="mt-2" :message="form.errors[`rows.${index}.Date`]" />
                            </div>

                            <div class="mb-3 col-md-3">
                                <InputLabel value="Time" /> <span class="text-danger">★</span>
                                <TextInput type="time" v-model="row.Time" class="form-control" />
                                 <InputError class="mt-2" :message="form.errors[`rows.${index}.Time`]" />
                            </div>

                            <div class="mb-3 col-md-3">
                                <InputLabel value="Total Marks" /> <span class="text-danger">★</span>
                                <TextInput type="number" v-model="row.MarksMax" class="form-control" />
                                <InputError class="mt-2" :message="form.errors[`rows.${index}.MarksMax`]" />
                            </div>

                            <div class="mb-3 col-md-3">
                                <InputLabel value="Passing Marks" /> <span class="text-danger">★</span>
                                <TextInput type="number" v-model="row.MarksMin" class="form-control" />
                                <InputError class="mt-2" :message="form.errors[`rows.${index}.MarksMin`]" />
                            </div>

                            <div class="mb-3 col-md-3">
                                <InputLabel value="Credit Hours" />
                                <TextInput type="number" v-model="row.CreditHours" class="form-control" />
                                <InputError class="mt-2" :message="form.errors[`rows.${index}.CreditHours`]" />
                            </div>

                            <div class="mb-3 col-md-3">
                                <InputLabel value="Duration" />
                                <TextInput type="number" v-model="row.Duration" class="form-control" />
                                 <InputError class="mt-2" :message="form.errors[`rows.${index}.Duration`]" />
                            </div>
                            
                            <div class="mb-3 col-md-3">
                                <InputLabel value="Room No" />
                                <TextInput type="number" v-model="row.RoomNo" class="form-control" />
                                <InputError class="mt-2" :message="form.errors[`rows.${index}.RoomNo`]" />
                            </div>

                            <div class="mt-4 col-md-12 align-items-end text-end">
                                <button type="button" class="mt-2 btn btn-danger btn-sm" @click="removeRow(index)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="mt-4 col-md-12">
                    <div class="text-end">
                        <PrimaryButton type="submit">Submit</PrimaryButton>
                    </div>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
