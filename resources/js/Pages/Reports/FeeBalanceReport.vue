<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ref } from 'vue';

const loadingAdmission = ref(false);

const props = defineProps({
    sections: Object,
    classes: Object,
});

const form = useForm({
    student_fee_balance:{
        ClassId: '',
        SectionId: '',
    }
});

const filteredSectionsForUnpaidFee = computed(() => {
    return props.sections.filter(section => section.ClassId === form.student_fee_balance.ClassId);
});

const studentFeeInformationReport = () => {
    loadingAdmission.value = true;
    axios({
        url: route('student.feebalance.fetch'),
        method: 'GET',
        params: form.student_fee_balance,
        responseType: 'blob',
    })
    .then((response) => {
        const fileURL = window.URL.createObjectURL(
            new Blob([response.data], { type: "application/pdf" })
        );
        window.open(fileURL, "_blank");
    }).finally(() => {
        loadingAdmission.value = false;
    })
    .catch((error) => {
        console.error("PDF Error ", error);
    })
};
</script>
<template>

    <Head title="Report List" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Student Balance</h2>
        </template>

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-md-12 col-lg-12 mb-4">
                    <div class="card cstm_shadow border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <h5 class="card-title mb-0">Student Balance</h5>
                            </div>
                            <p class="text-muted small mb-3">
                                
                            </p>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="bdaymonth">Class</label>
                                        <select class="form-control" v-model="form.student_fee_balance.ClassId">
                                            <option value="">Select Class</option>
                                            <option v-for="classList in classes" :key="classList.id"
                                                :value="classList.id">{{
                                                    classList.ClassName }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="bdaymonth">Section</label>
                                          <select class="form-control" v-model="form.student_fee_balance.SectionId">
                                            <option value="">Select Section</option>
                                            <option v-for="section in filteredSectionsForUnpaidFee" :key="section.id"
                                                :value="section.id">{{ section?.SectionName }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="text-right" style="margin-top: 30px;">
                                    <button class="btn btn-primary btn-block mt-auto" :disabled="loadingAdmission"
                                        @click="studentFeeInformationReport">
                                        <span v-if="!loadingAdmission">
                                            <i class="fa fa-chart-line mr-1"></i> Student Balance
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
