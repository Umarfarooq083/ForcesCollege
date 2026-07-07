<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    sections: Object,
    classes: Object,
});

const form = useForm({
    student_withdraw_report: {
        ClassId: '',
        SectionId: '',
    }
});

const loading = ref(false);

const filteredSections = computed(() => {
    return props.sections.filter(section => section.ClassId === form.student_withdraw_report.ClassId);
});

const studentWithdrawReport = () => {
    if (!form.student_withdraw_report.ClassId) {
        alert('Please select a class');
        return;
    }

    loading.value = true;
    axios({
        url: route('student.withdraw.report.fetch'),
        method: 'GET',
        params: form.student_withdraw_report,
        responseType: 'blob',
    })
    .then((response) => {
        const fileURL = window.URL.createObjectURL(
            new Blob([response.data], { type: "application/pdf" })
        );
        window.open(fileURL, "_blank");
    })
    .catch((error) => {
        console.error("PDF Error", error);
    })
    .finally(() => {
        loading.value = false;
    });
};
</script>
<template>

    <Head title="Student Withdraw Report" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Reports Dashboard</h2>
        </template>

        <div class="container-fluid py-4">
            <div class="row">
                <!-- Student Withdraw Report -->
                <div class="col-md-12 col-lg-12 mb-4">
                    <div class="card cstm_shadow border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa fa-user-times text-danger fa-2x mr-3"></i>
                                <h5 class="card-title mb-0">Student Withdraw Report</h5>
                            </div>
                            <p class="text-muted small mb-3">
                                Select a class and section to generate the student withdrawal report.
                            </p>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.student_withdraw_report.ClassId">
                                            <option value="">Select Class</option>
                                            <option v-for="classList in classes" :key="classList.id"
                                                :value="classList.id">{{
                                                    classList.ClassName }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.student_withdraw_report.SectionId">
                                            <option value="">Select Section</option>
                                            <option v-for="section in filteredSections" :key="section.id"
                                                :value="section.id">{{
                                                    section?.SectionName }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4 text-right">
                                    <button class="btn btn-primary btn-block mt-auto" :disabled="loading"
                                        @click="studentWithdrawReport">
                                        <span v-if="!loading">
                                            <i class="fa fa-chart-line mr-1"></i> Generate Report
                                        </span>
                                        <span v-else>
                                            <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.cstm_shadow {
    box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;
}
</style>