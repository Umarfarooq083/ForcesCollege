<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';

const form = useForm({
    student_fee_information:{
        month:'',
    }
});

const studentFeeInformationReport = () => {
    axios({
        url: route('fee.collectionreport.fetch'),
        method: 'GET',
        params: form.student_fee_information,
        responseType: 'blob',
    })
    .then((response) => {
        const fileURL = window.URL.createObjectURL(
            new Blob([response.data], { type: "application/pdf" })
        );
        window.open(fileURL, "_blank");
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
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Reports Dashboard</h2>
        </template>

        <div class="container-fluid py-4">
            <div class="row">
                <!-- Student Detail Report -->
                <div class="col-md-12 col-lg-12 mb-4">
                    <div class="card cstm_shadow border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <h5 class="card-title mb-0">Fee Collection</h5>
                            </div>
                            <p class="text-muted small mb-3">
                                
                            </p>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="bdaymonth">Billing Month</label>
                                        <input type="month" class="form-control" id="bdaymonth" v-model="form.student_fee_information.month" name="bdaymonth">
                                    </div>
                                </div>

                                <div class="text-right" style="margin-top: 30px;">
                                    <button class="btn btn-primary btn-block mt-auto" @click="studentFeeInformationReport">Submit
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
