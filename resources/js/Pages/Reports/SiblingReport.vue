<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'

const siblingData = ref([]);
const inquery_message = ref('');

const fetchSiblingStudents = async () => {
    try {
        const response = await axios.get('fetch-all-sibling-students');
        siblingData.value = response.data.siblings;
        if (response.data.siblings.length === 0) {
            inquery_message.value = 'No students with siblings found';
        } else {
            inquery_message.value = '';
        }
    } catch (error) {
        console.error('Request failed:', error);
    }
};

const fetchSiblingStudentsPdf = () => {
    window.open(route('sibling.report.all.pdf'), '_blank');
};

</script>
<template>

    <Head title="Sibling Report" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Sibling Report</h2>
        </template>

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-md-12 col-lg-12 mb-4">
                    <div class="card cstm_shadow border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="mb-3">
                                <PrimaryButton @click="fetchSiblingStudents">Fetch Siblings Report</PrimaryButton>
                            </div>

                            <div v-if="siblingData.length > 0" class="mt-4">
                                <div v-for="(group, guardianId) in siblingData" :key="guardianId" class="mb-4">
                                    <h5>Guardian: {{ group.guardian.name }} (CNIC: {{ group.guardian.cnic }})</h5>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Sr #</th>
                                                <th>Student Name</th>
                                                <th>Father Name</th>
                                                <th>Mother Name</th>
                                                <th>Class</th>
                                                <th>Section</th>
                                                <th>Gender</th>
                                                <th>Roll No</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(student, idx) in group.students" :key="student.id">
                                                <td>{{ idx + 1 }}</td>
                                                <td>{{ student.FirstName }} {{ student.LastName }}</td>
                                                <td>{{ student.FatherName }}</td>
                                                <td>{{ student?.MotherName }}</td>
                                                <td>{{ student.class?.ClassName ?? 'N/A' }}</td>
                                                <td>{{ student.section?.SectionName ?? 'N/A' }}</td>
                                                <td>{{ student.Gender }}</td>
                                                <td>{{ student.RollNumber ?? student.EnquiryRollNumber ?? 'N/A' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <button class="btn btn-primary mt-3" @click="fetchSiblingStudentsPdf">Download PDF</button>
                            </div>

                            <div v-else-if="inquery_message" class="mt-4">
                                <p class="text-danger">{{ inquery_message }}</p>
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