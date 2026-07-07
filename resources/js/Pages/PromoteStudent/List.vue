<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import axios from 'axios';

const students_data = ref([]);
const promotion_response_data = ref(null);

const props = defineProps({
    classList: Object,
    sectionList: Object,
    activeSession: Object,
    activeSessionsList: Object,
});

const form = useForm({
    fetch_students: {
        ClassId: '',
        SectionId: '',
        SessionId: '',
        SessionName: props.activeSession?.name,
    },
    promote_student: {
        student_id: [],
        PromoteClassId: '',
        PromoteSectionId: '',
        PromoteSessionId: '',
    },
    start_promotion: []
});

const filteredSections = computed(() => {
    return props.sectionList.filter(
        section => section.ClassId === form.fetch_students.ClassId
    );
});

const filteredPromotedSections = computed(() => {
    return props.sectionList.filter(
        section => section.ClassId === form.promote_student.PromoteClassId
    );
});

const FetchStudent = async () => {
    const response = await axios.get(
        route('promotestudent.fetch', { fetch_student: form.fetch_students })
    );
    students_data.value = response.data;
};

const startStudentPromotion = async () => {
    if(form.promote_student.PromoteClassId === '' || form.promote_student.PromoteSectionId === '' || form.promote_student.PromoteSessionId === ''){
        alert('Please select all options');
        return;
    }
    // if (form.fetch_students.ClassId === form.promote_student.PromoteClassId) {
    //     alert('You can not promote student to same class');
    //     return;
    // }

    const response = await axios.get(
        route('promotestudent.list', {
            old_request_student: form.fetch_students,
            new_request_student: form.promote_student
        })
    );
    promotion_response_data.value = response.data;
    form.start_promotion = response.data.previousStudentData.map(student => ({
        student_id: student.id,
        ClassId: form.promote_student.PromoteClassId,
        SessionId: form.promote_student.PromoteSessionId,
        SectionId: form.promote_student.PromoteSectionId,
        fees: student.student_fee_discount.map(fee => ({
            fee_discount_id: fee.id,
            discount_amount: fee.DiscountAmount ?? 0,
            campus_fee_master_id: '',
        }))
    }));
};

const getSelectedFeeAmount = (sIndex, fIndex) => {
    const selectedId = form.start_promotion[sIndex]?.fees[fIndex]?.campus_fee_master_id;
    if (!selectedId || !promotion_response_data.value) return 0;
        const selectedFee = promotion_response_data.value.campusFeesMasterData
        ?.find(fee => fee.id === selectedId);
    return selectedFee ? Number(selectedFee.Amount) : 0;
};

const validateDiscount = (sIndex, fIndex) => {
    const maxAmount = getSelectedFeeAmount(sIndex, fIndex);
    let discount = Number(form.start_promotion[sIndex].fees[fIndex].discount_amount);
    if (discount > maxAmount) {
        form.start_promotion[sIndex].fees[fIndex].discount_amount = maxAmount;
        alert('Discount cannot be greater than selected Fee Amount');
    }

    if (discount < 0 || isNaN(discount)) {
        form.start_promotion[sIndex].fees[fIndex].discount_amount = 0;
    }
};

const validateCampusFeeMaster = (sIndex, fIndex) => {
    const selectedId = form.start_promotion[sIndex].fees[fIndex].campus_fee_master_id;
    if (!selectedId) return;
    const fees = form.start_promotion[sIndex].fees;
    const duplicate = fees.some((fee, index) => {
        return index !== fIndex && fee.campus_fee_master_id === selectedId;
    });
    if (duplicate) {
        alert('This Campus Fee Master is already assigned to this student.');
        form.start_promotion[sIndex].fees[fIndex].campus_fee_master_id = '';
        form.start_promotion[sIndex].fees[fIndex].discount_amount = 0;
    }
};

const submitPromotion = () => {
    form.post(route('promotestudent.store'), {
        onSuccess: () => {
            form.reset() 
        },
        onError: (errors) => {
            alert('Something went wrong')
            console.log(errors)
        }
    });
};

const selectAll = computed({
    get() {
        return students_data.value.length > 0 &&
            form.promote_student.student_id.length === students_data.value.length;
    },
    set(value) {
        if (value) {
            form.promote_student.student_id = students_data.value.map(s => s.id);
        } else {
            form.promote_student.student_id = [];
        }
    }
});

</script>

<template>

    <Head title="Student" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Student
            </h2>
        </template>
       
        <div class="row">
            <div :class="form.promote_student.student_id.length > 0
                ? 'col-md-6'
                : 'col-md-12'">
                <div class="card">
                    <div class="header">
                        <div class="w-100 d-inline-flex justify-content-between align-items-center">
                            <div class="font-weight-bold">Get Student</div>
                        </div>
                    </div>
                    <div class="body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <InputLabel value="Class" />
                                    <select class="form-control" v-model="form.fetch_students.ClassId">
                                        <option selected disabled value="">Select a Class</option>
                                        <option v-for="list in classList" :key="list.id" :value="list.id">
                                            {{ list.ClassName }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <InputLabel value="Section" />
                                    <div class="position-relative">
                                        <select class="form-control" v-model="form.fetch_students.SectionId">
                                            <option selected disabled value="">Select a Section</option>
                                            <option v-for="secList in filteredSections" :key="secList.id"
                                                :value="secList.id">
                                                {{ secList.SectionName }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <InputLabel value="Section" />
                                    <div class="position-relative">
                                        <select class="form-control" v-model="form.fetch_students.SessionId">
                                            <option selected value="">Select a Session</option>
                                            <option v-for="sessionList in activeSessionsList" :key="sessionList.id"
                                                :value="sessionList.id">
                                                {{ sessionList.name }} ( {{ sessionList.start_date }} - {{
                                                    sessionList.end_date }} )
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <PrimaryButton @click="FetchStudent">Get Student</PrimaryButton>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div v-if="form.promote_student.student_id.length > 0" class="col-md-6">
                <div class="card">
                    <div class="header">
                        <div class="w-100 d-inline-flex justify-content-between align-items-center">
                            <div class="font-weight-bold">Start Promoting</div>
                        </div>
                    </div>
                    <div class="body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <InputLabel value="Class" />
                                    <select class="form-control" v-model="form.promote_student.PromoteClassId" required>
                                        <option selected disabled value="">Select a Class</option>
                                        <option v-for="list in classList" :key="list.id" :value="list.id">
                                            {{ list.ClassName }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <InputLabel value="Section" />
                                    <div class="position-relative">
                                        <select class="form-control" v-model="form.promote_student.PromoteSectionId" required>
                                            <option selected disabled value="">Select a Section</option>
                                            <option v-for="secList in filteredPromotedSections" :key="secList.id"
                                                :value="secList.id">
                                                {{ secList.SectionName }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <InputLabel value="Session" />
                                    <div class="position-relative">
                                        <select class="form-control" v-model="form.promote_student.PromoteSessionId" required>
                                            <option selected value="">Select a Session</option>
                                            <option v-for="sessionList in activeSessionsList" :key="sessionList.id"
                                                :value="sessionList.id">
                                                {{ sessionList.name }} ( {{ sessionList.start_date }} - {{
                                                    sessionList.end_date }} )
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <PrimaryButton @click="startStudentPromotion">Start Promoting</PrimaryButton>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- promotion code start from here  -->
            <div class="col-md-12" v-if="form.start_promotion.length">
                <form @submit.prevent="submitPromotion">
                    <div class="card">
                        <div class="header">
                            <div class="font-weight-bold">Promote Student</div>
                        </div>

                        <div class="body">
                            <div v-for="(promotionlist, sIndex) in promotion_response_data.previousStudentData"
                                :key="promotionlist.id" class="mb-4 p-3 border rounded">
                                <!-- Student Info -->
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label>Student Name</label>
                                        <input type="text" class="form-control"
                                            :value="promotionlist.FirstName + ' ' + promotionlist.LastName" readonly>
                                    </div>

                                    <div class="col-md-3">
                                        <label>Roll Number</label>
                                        <input type="text" class="form-control" :value="promotionlist.RollNumber"
                                            readonly>
                                    </div>
                                </div>

                                <!-- Fee Rows -->
                                <div class="row" v-for="(discountdetail, fIndex) in promotionlist.student_fee_discount"
                                    :key="discountdetail.id">

                                    <div class="col-md-3">
                                        <label>Fee Type</label>
                                        <input type="text" class="form-control"
                                            :value="discountdetail?.campus_fees_master_rel?.fee_type_rel?.FeeName"
                                            readonly>
                                    </div>

                                    <div class="col-md-2">
                                        <label>Total Fee</label>
                                        <input type="number" class="form-control"
                                            :value="discountdetail.campus_fees_master_rel.Amount" readonly>
                                    </div>

                                    <div class="col-md-3">
                                        <label>Assign Campus Fee Master</label>
                                        <select class="form-control"
                                            v-model="form.start_promotion[sIndex].fees[fIndex].campus_fee_master_id"
                                            @change="validateCampusFeeMaster(sIndex, fIndex)">

                                            <option disabled value="">Select Fee</option>

                                            <option
                                                v-for="promotionMasterlist in promotion_response_data.campusFeesMasterData"
                                                :key="promotionMasterlist.id" :value="promotionMasterlist.id">

                                                {{ promotionMasterlist?.fee_type_rel?.FeeName }}
                                                - {{ promotionMasterlist?.Amount }}

                                            </option>
                                        </select>

                                    </div>

                                    <div class="col-md-2">
                                        <label> Discount</label>
                                        <input type="number" class="form-control"
                                            :max="getSelectedFeeAmount(sIndex, fIndex)" min="0"
                                            :disabled="!form.start_promotion[sIndex].fees[fIndex].campus_fee_master_id"
                                            v-model="form.start_promotion[sIndex].fees[fIndex].discount_amount"
                                            @input="validateDiscount(sIndex, fIndex)">

                                        <small class="text-danger"
                                            v-if="form.start_promotion[sIndex].fees[fIndex].discount_amount > getSelectedFeeAmount(sIndex, fIndex)">
                                            Discount cannot exceed selected fee amount
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                Submit Promotion
                            </button>

                        </div>
                    </div>
                </form>
            </div>
            <!-- promotion code end here  -->

            <div class="col-md-12">
                <div class="card" v-if="!promotion_response_data?.campusFeesMasterData">
                    <div class="header">
                        <div class="w-100 d-inline-flex justify-content-between align-items-center">
                            <div class="font-weight-bold">Student Data</div>
                        </div>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table mb-0 table-hover c_list table-bordered">
                               <div v-if="students_data.length > 0" class="ml-2 mt-1 mb-2 w-100 d-inline-flex justify-content-between align-items-center">
                                    <!-- <input type="checkbox" v-model="selectAll" /> -->
                                    <label class="fancy-checkbox">
                                        <input type="checkbox"
                                            v-model="selectAll" />
                                        <span class="text-dark font-weight-bold">Check All</span>
                                    </label>
                                </div>

                                <tbody v-if="students_data.length > 0">
                                    <tr v-for="list in students_data">
                                        <td>
                                            <label class="fancy-checkbox">
                                                <input :value="list.id" type="checkbox"
                                                    v-model="form.promote_student.student_id" />
                                                <span class="text-dark font-weight-bold"></span>
                                            </label>
                                        </td>

                                        <td>{{ list.RollNumber }}</td>
                                        <td>{{ list?.FirstName }} {{ list?.LastName }}</td>
                                        <td>
                                            <table class="table mb-0 table-hover c_list table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Fee Type</th>
                                                        <th>Amount</th>
                                                        <th>Discount</th>
                                                        <th>Payable</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="detail in list.student_fee_discount">
                                                        <td> {{ detail?.campus_fees_master_rel?.fee_type_rel?.FeeName }}
                                                        </td>
                                                        <td> {{ detail.TotalFee }} </td>
                                                        <td> {{ detail.DiscountAmount }} </td>
                                                        <td> {{ detail.BalanceFeeAfterDiscount }} </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="4" class="text-center">No data available</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </AuthenticatedLayout>
</template>
