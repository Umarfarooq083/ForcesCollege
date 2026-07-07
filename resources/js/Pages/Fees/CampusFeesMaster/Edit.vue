<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import 'vue-multiselect/dist/vue-multiselect.min.css'

const props = defineProps({
    campus_fee_master_group: Array,
    Classes: Array,
    // fee_group_id: Number|String,
    SessionId: Number,
    fee_type_id: Number,
});

const form = useForm({
    fees_master: props.campus_fee_master_group,
    // fee_group_id: props.fee_group_id,
    fee_type_id: props.fee_type_id,
});

const submitForm = () => {
    const fee_master_data = {
        fees_master: form.fees_master,
        // fee_group_id: props.fee_group_id,
        fee_type_id: props.fee_type_id,
        session_id: props.SessionId,
    }

    form.post(route('feemaster.update'), fee_master_data, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Campus Fees Master" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Campus Fees Master</h2>
        </template>

        <form @submit.prevent="submitForm">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Edit Campus Fees Master</div>
                        <div class="body">
                            <!-- Line Items Table -->
                            <div v-if="form.fees_master.length > 0" class="mt-4 table-responsive">
                                <table class="table text-center table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fee Type</th>
                                            <th>Class</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <tr v-for="(fee_master, index) in form.fees_master" :key="index">
                                            <td>
                                                <input 
                                                    type="text"
                                                    class="text-center form-control"
                                                    v-model="fee_master.fee_master_type.FeeName" 
                                                    disabled 
                                                />
                                            </td>
                                            <td>
                                                <select class="text-center form-control" v-model="fee_master.ClassId">
                                                    <option selected value="">Select a class</option>
                                                    <option v-for="cls in props.Classes" :key="cls.id" :value="cls.id">
                                                        {{ cls.name }}
                                                    </option>
                                                </select>
                                                <InputError :message="form.errors[`fees_master.${index}.ClassId`]" />
                                            </td>

                                            <td>
                                                <input 
                                                    type="number"
                                                    class="text-center form-control"
                                                    v-model="fee_master.Amount"
                                                    required 
                                                />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Submit -->
                            <div class="mt-3 text-end">
                                <PrimaryButton type="submit" :disabled="form.processing">
                                    {{ form.processing ? 'Submitting...' : 'Update' }}
                                </PrimaryButton>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
