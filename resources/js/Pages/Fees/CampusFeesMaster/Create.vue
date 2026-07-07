<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import { computed, ref, watch } from 'vue';

const props = defineProps({
    FeeTypes: Array,
    Classes: Object,
    Session: Object,
});

const selectedClassIds = ref([]);
const min = 0;
const step = 1;

const form = useForm({
    feetypeid: '',
    session: props.Session,
    amount: [''],
    fees_master: [],
});

watch(selectedClassIds, (newSelected) => {
    form.fees_master = newSelected.map(cls => {
        const existing = form.fees_master.find(f => f.class_id === cls.id);
        return {
            class_id: cls.id,
            class_name: cls.name,
            amount: existing?.amount ?? form.amount[0] ?? ''
        };
    });
});

const sessionRange = computed(() => {
    return form.session.start_date + '   to   ' + form.session.end_date; 
});

const submitForm = () => {
    const fee_master_data = {
        fee_type_id: form.feetypeid,
        session: form.session,
        fees_master: form.fees_master,
    }

    form.post(route('feemaster.submit'), fee_master_data, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            selectedClassIds.value = [];
        },
    });
};
</script>

<template>
    <Head title="Campus Fees Master" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Campus Fees Master</h2>
        </template>

        <form @submit.prevent="submitForm">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Campus Fees Master</div>
                        <div class="body">
                            <div class="row">

                                <!-- Fee Type -->
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Fee Types" />
                                        <select 
                                            class="form-control" 
                                            v-model="form.feetypeid"
                                        >
                                            <option selected disabled value="">Select a Fee type</option>
                                            <option v-for="FeeType in FeeTypes" :key="FeeType.id" :value="FeeType.id">
                                                {{ FeeType.name }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.feetypeid" />
                                    </div>
                                </div>
                                
                                <!-- Amount -->
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Amount" />
                                        <TextInput type="number" v-model="form.amount[0]" :min="min" :step="step" class="form-control" />
                                    </div>
                                </div>
                                
                                <!-- Class Multiselect -->
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Classes" />
                                        <Multiselect
                                            v-model="selectedClassIds"
                                            :options="Classes"
                                            :multiple="true"
                                            :track-by="'id'"
                                            :label="'name'"
                                            placeholder="Select Class"
                                            :disabled="!form.feetypeid"
                                        />
                                    </div>  
                                </div>

                                <!-- Session -->
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Session" />
                                        <TextInput 
                                            type="text" 
                                            v-model="sessionRange" 
                                            class="form-control"
                                            readonly
                                        />
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Line Items Table -->
                            <div v-if="form.fees_master.length > 0" class="mt-4 table-responsive">
                                <table class="table text-center table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Class Name</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in form.fees_master" :key="item.class_id">
                                            <td>{{ item.class_name }}
                                                <InputError :message="form.errors[`fees_master.${index}.class_id`]" />
                                            </td>
                                            
                                             <td>
                                                <input 
                                                    type="number"
                                                    class="text-center form-control"
                                                    v-model="form.fees_master[index].amount"
                                                    :min="min" :step="step"
                                                    required 
                                                />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Submit -->
                            <div class="mt-3 text-end" v-if="form.fees_master.length > 0">
                                <PrimaryButton type="submit" :disabled="form.processing">
                                    {{ form.processing ? 'Submitting...' : 'Submit' }}
                                </PrimaryButton>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
