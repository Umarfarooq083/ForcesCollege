<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref } from 'vue';

const props = defineProps({
    student: Object,
});

const readmittedDate = ref('');
const errors = ref({});

const validateForm = () => {
    const validationErrors = {};
    if (!readmittedDate.value) {
        validationErrors.readmittedDate = 'Re-Admission Date is required.';
    }
    errors.value = validationErrors;
    return Object.keys(validationErrors).length === 0;
};

const submitReAdmission = () => {
    if (!validateForm()) return;

    router.post(route('student.readmissionsubmit', props.student.id), {
        readmitted_date: readmittedDate.value,
    }, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Student Re-Admission" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Student Re-Admission
            </h2>
        </template>
        
        <div class="card">
            <div class="header">Re-Admit Student</div>
            <div class="body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <InputLabel value="Student Name" />
                            <input type="text" :value="`${student.FirstName} ${student.LastName}`" class="form-control" disabled />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <InputLabel value="Roll Number" />
                            <input type="text" :value="`${student.RollNumber}`" class="form-control" disabled />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <InputLabel value="Class" />
                            <input type="text" :value="`${student.class?.ClassName || 'N/A'}`" class="form-control" disabled />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <InputLabel value="Section" />
                            <input type="text" :value="`${student.section?.SectionName || 'N/A'}`" class="form-control" disabled />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <InputLabel value="Father Name" />
                            <input type="text" :value="`${student.FatherName}`" class="form-control" disabled />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <InputLabel value="Father Phone" />
                            <input type="text" :value="`${student.FatherPhone}`" class="form-control" disabled />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <InputLabel value="Withdrawal Date" />
                            <input type="text" :value="`${student.withdraw_date}`" class="form-control" disabled />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <InputLabel value="Withdrawal Reason" />
                            <input type="text" :value="`${student.withdraw_reason || 'N/A'}`" class="form-control" disabled />
                        </div>
                    </div>

                    <div class="col-md-6 form-group">
                        <label>Re-Admission Date<span class="text-danger font-12 position-absolute">★</span></label>
                        <TextInput type="date" v-model="readmittedDate" class="form-control" />
                        <InputError class="mt-2" :message="errors.readmittedDate" />
                    </div>


                </div>

                <div class="mt-4">
                    <Link style="margin-right: 6px !important" :href="route('student.readmissionlist')" class="btn btn-secondary">Cancel</Link>
                    <PrimaryButton @click="submitReAdmission">Re-Admit Student</PrimaryButton>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>