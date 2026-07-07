<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    SmsLogDetail: Object
});
</script>

<template>
    <Head title="SMS Log Detail" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                SMS Log Detail
            </h2>
        </template>

        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">SMS Log Information</div>
                </div>
            </div>

            <div class="body">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <tbody>
                            <tr>
                                <th>ID</th>
                                <td>{{ SmsLogDetail.id }}</td>
                            </tr>
                            <tr>
                                <th>Mobile No</th>
                                <td>{{ SmsLogDetail.mobileNo }}</td>
                            </tr>
                            <tr>
                                <th>Body</th>
                                <td>{{ SmsLogDetail.body }}</td>
                            </tr>
                            <tr>
                                <th>Character Length</th>
                                <td>{{ SmsLogDetail.characterLength }}</td>
                            </tr>
                            <tr>
                                <th>SMS Count</th>
                                <td>{{ SmsLogDetail.smsCount }}</td>
                            </tr>
                            <tr>
                                <th>API Type</th>
                                <td>
                                    <span class="badge bg-success" style="color:white">{{ SmsLogDetail.apItype }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>API Code</th>
                                <td>{{ SmsLogDetail.apiCode }}</td>
                            </tr>
                            <tr>
                                <th>API Response Text</th>
                                <td>{{ SmsLogDetail.apiResponseText }}</td>
                            </tr>
                            <tr>
                                <th>API Transaction ID</th>
                                <td>{{ SmsLogDetail.apiTransactionID }}</td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ new Date(SmsLogDetail.created_at).toLocaleString() }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
