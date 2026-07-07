<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ref } from 'vue';
import axios from 'axios';
import Checkbox from '@/Components/Checkbox.vue';


const StudentLedgerValidationError = ref(null);
const props = defineProps({
    sections: Object,
    classes: Object,
});

const form = useForm({
    student_information:{
    ClassId: '',
    SectionId: '',
    isActive: false,
    }
});

const filteredSections = computed(() => {
    return props.sections.filter(section => section.ClassId === form.student_information.ClassId);
});

const studentInformation = () => {
    axios({
        url: route('studentinformation.fetch'),
        method: 'GET',
        params: form.student_information,
        responseType: 'blob',
    })
    .then((response) => {
        const fileURL = window.URL.createObjectURL(
            new Blob([response.data], { type: "application/pdf" })
        );
        window.open(fileURL, "_blank");
    })
    .catch((error) => {
        // StudentLedgerValidationError.value = "Please select at least Student.";
    })
    .finally(() => {
        StudentLedgerValidationError.value = null;
    });
};
</script>
<template>

    <Head title="Report List" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Reports Dashboard</h2>
        </template>

        <div class="container-fluid py-4">
            <div class="row">
                <!-- Student Detail Report -->
                <div class="col-md-12 col-lg-12 mb-4">
                    <div class="card cstm_shadow border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <h5 class="card-title mb-0">Student Information</h5>
                            </div>
                            <p class="text-muted small mb-3">
                                
                            </p>
                            <div class="row">
                                <div class="col-md-1 mt-1">
                                    <div class="form-group">
                                       <label for="active" class="fancy-checkbox element-left">	
                                            <Checkbox name="active" id="active"  v-model:checked="form.student_information.isActive" />
                                            <span class="text-dark font-weight-bold">Is Active</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.student_information.ClassId">
                                            <option value="">Select Class</option>
                                            <option v-for="classList in classes" :key="classList.id"
                                                :value="classList.id">{{
                                                    classList.ClassName }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.student_information.SectionId">
                                            <option value="">Select Section</option>
                                            <option v-for="section in filteredSections" :key="section.id"
                                                :value="section.id">{{
                                                    section?.SectionName }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-1 text-right">
                                    <button class="btn btn-primary btn-block mt-auto" @click="studentInformation">Submit
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
