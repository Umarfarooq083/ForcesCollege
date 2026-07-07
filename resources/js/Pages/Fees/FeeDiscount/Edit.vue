<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import 'vue-multiselect/dist/vue-multiselect.min.css';
import { watchEffect } from 'vue';

const min = 0;
const max = 10000000000;
const step = 1;

const props = defineProps({
    studentFeeDiscount: Object,
});

const loadCampusMasters = [
    {id: 1, name: 'Campus Fee Master'},
    {id: 2, name: 'Optional Campus Fee Master'}
];
const matchedCampus = loadCampusMasters.find(
    (item) => item.id === props.studentFeeDiscount.loadedCampusMaster
);

const form = useForm({
  id:props.studentFeeDiscount.id,
  loadedCampusMaster: matchedCampus ? matchedCampus.name : '',
  TotalFee: props.studentFeeDiscount.TotalFee,
  discountAmount:  props.studentFeeDiscount.DiscountAmount,
  BelanceAmount:  0,
});

watchEffect(() => {
  const discount = parseFloat(form.discountAmount) || 0;
  const total = parseFloat(form.TotalFee) || 0;

  if (discount > total) {
    alert('Discount Amount cannot be greater than total amount');
    form.discountAmount = 0;
  }

  form.BelanceAmount = total - discount;
});

function increment() {
  const current = parseFloat(form.discountAmount) || 0;
  if (current + step <= max) {
    form.discountAmount = parseFloat((current + step).toFixed(2));
  }
}

function decrement() {
  const current = parseFloat(form.discountAmount) || 0;
  if (current - step >= min) {
    form.discountAmount = parseFloat((current - step).toFixed(2));
  }
}
</script>

<template>

    <Head title="Edit Student Discount" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Student Discount</h2>
        </template>
      
        <form @submit.prevent="form.put(route('discount.update'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Edit Student Discount</div>
                        <div class="body">
                            <div class="row">
                              
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Class" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <select class="form-control" readonly>
                                            <option selected disabled value="">{{ studentFeeDiscount?.class_rel?.ClassName }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Section" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <select class="form-control" readonly>
                                            <option selected disabled value="">{{ studentFeeDiscount?.section_rel?.SectionName }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Load Campus Fee Master" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <select class="form-control" readonly>
                                            <option  selected disabled  >
                                                {{ form.loadedCampusMaster}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                               
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Campus Fee Master" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <select class="form-control" readonly >
                                            <option selected disabled value="">{{ studentFeeDiscount?.campus_fees_master_rel?.fee_type_rel?.FeeName }}</option>
                                        </select>
                                    </div>
                                </div>
 
                                <div class="col-md-3 mt-4" >
                                    <div class="mb-3">
                                        <InputLabel value="Students" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <select class="form-control" readonly >
                                            <option selected disabled value="">{{ studentFeeDiscount?.student_rel?.FirstName }} {{ studentFeeDiscount?.student_rel?.LastName }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3 mt-4">
                                    <div class="mb-3">
                                        <InputLabel value="Total Fee" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <TextInput type="number" v-model="form.TotalFee" class="form-control" readonly/>
                                    </div>
                                </div>

                               <div class="col-md-3 mt-4">
                                    <InputLabel value="Discount Amount"  /> <span class="text-danger font-12 position-absolute">★</span>
                                    <div class="col-md-12  input-group" style="padding: 0px;">
                                        <span><button class="btn btn-default" type="button" @click="decrement">-</button></span>
                                        <input type="number" class="form-control " v-model.number="form.discountAmount" :min="min" :max="max" :step="step" required />
                                        <span class="input-group-addon">PKR</span>
                                        <span>
                                        <button class="btn btn-default" type="button" @click="increment">+</button>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-md-3 mt-4">
                                    <div class="mb-3">
                                        <InputLabel value="Balance Fee" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <TextInput type="number" v-model="form.BelanceAmount"  class="form-control" readonly/>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="text-end">
                                        <label class="w-100 d-inline-block">&nbsp;</label>
                                        <PrimaryButton>Submit</PrimaryButton>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
<style scoped>
.input-group {
  display: flex;
  align-items: center;
  gap: 4px;
}

.input-group-addon {
  padding: 6px 10px;
  background: #f1f1f1;
}
</style>