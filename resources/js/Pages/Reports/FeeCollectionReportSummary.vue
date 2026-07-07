<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import axios from 'axios';

const form = ref({
    month: ''
})

const reportHtml = ref('')
const loading = ref(false)

const studentFeeInformationReport = async () => {
    if (!form.value.month) {
        alert('Please select billing month')
        return
    }

    loading.value = true

    try {
        const response = await axios.get(
            route('fee.collectionsummary.fetch'),
            {
                params: {
                    month: form.value.month
                }
            }
        )

        reportHtml.value = response.data
        openModal()
    } catch (error) {
        console.error('Report Error:', error)
        alert('Failed to load report')
    } finally {
        loading.value = false
    }
}
const openModal = () => {
    $('#feeReportModal').modal('show')
}

const closeModal = () => {
    $('#feeReportModal').modal('hide')
}

const printReport = () => {
    const printWindow = window.open(
        '',
        'printWindow',
        'width=1000,height=700,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes'
    )

    if (!printWindow) {
        alert('Popup blocked. Please allow popups for this site.')
        return
    }

    printWindow.document.open()
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print Report</title>
            <meta charset="utf-8" />
            <style>
                body { margin: 0; font-family: Arial, sans-serif; }
            </style>
        </head>
        <body>
            ${reportHtml.value}

            <script>
                window.onafterprint = function () {
                    window.close();
                };
                window.onload = function () {
                    window.focus();
                    window.print();
                };
            <\/script>
        </body>
        </html>
    `)
    printWindow.document.close()
}


</script>
<template>

    <Head title="Report List" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Reports Dashboard</h2>
        </template>

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-md-12 col-lg-12 mb-4">
                    <div class="card cstm_shadow border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <h5 class="card-title mb-0">Fee Collection Summary</h5>
                            </div>
                            <p class="text-muted small mb-3">
                                
                            </p>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="bdaymonth">Billing Month</label>
                                        <input type="month" class="form-control" v-model="form.month"/>
                                        <!-- <input type="month" class="form-control" id="bdaymonth" v-model="form.student_fee_information.month" name="bdaymonth"> -->
                                    </div>
                                </div>

                                <div class="text-right" style="margin-top: 30px;">
                                    <button
                                        class="btn btn-primary w-100"
                                        @click="studentFeeInformationReport"
                                        :disabled="loading"
                                    >
                                        {{ loading ? 'Loading...' : 'Submit' }}
                                    </button>
                                    <!-- <button class="btn btn-primary btn-block mt-auto" @click="studentFeeInformationReport">Submit</button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="feeReportModal" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xxl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Fee Collection Report</h5>
                        <button type="button" class="close" @click="closeModal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body" v-html="reportHtml"></div>
                    <div class="modal-footer">
                        <button class="btn btn-success" @click="printReport">Print</button>
                        <button type="button" class="btn btn-danger" @click="closeModal">Close</button>
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
