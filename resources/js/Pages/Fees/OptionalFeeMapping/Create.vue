<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css';
import { computed, ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    classList: Object,
    sectionList: Array,
    feesType : Object,
    currentSession : Object,
    campusFeesMaster : Object
});
const studentList = ref([]);
const filteredSections = computed(() => {
    return props.sectionList.filter(section => section.ClassId === form.ClassId);
});

const StudentOption = computed(() => {
    return studentList.value.map(student => ({
        id: student.id,
        name: student.FirstName +' '+ student?.LastName
    }));
});

const fetchStudents = async (event) => {
    try {
        const response = await axios.post('create-fetch-student', {
            sectoinId: event,
            classId: form.ClassId
        })
        studentList.value = response.data;
        form.StudentId = [];
    } catch (error) {
        console.error('Request failed:', error)
    }
    
};

const selectedClass =  (event) => {
    form.SectionId = '';  
    form.StudentId = [];
 
};

const filteredOptionalFeeOnCampusFeesBase = computed(() => {
    return props.campusFeesMaster.filter(item => {
        return (
            item.ClassId === form.ClassId &&
            item.fee_type_rel !== null
        );
    });
});


const filteredCampusFees = computed(() => {
    return props.campusFeesMaster.filter(item => {
        return item.ClassId === form.ClassId && item.FeesTypeNId === form.FeesTypeNId;
    });
});

const form = useForm({
    ClassId: '',
    FeesTypeNId: '',
    CampusFeesMasterId: '',
    SectionId: '',
    StudentId: [],
    FromMonth: '',
    ToMonth: '',
    SessionId: props?.currentSession?.id
});

const allStudentsSelected = computed(() => {
    return StudentOption.value.length > 0 && form.StudentId.length === StudentOption.value.length;
});

const toggleSelectAllStudents = () => {
    form.StudentId = allStudentsSelected.value ? [] : [...StudentOption.value];
};

const hasStudents = computed(() => {
    return StudentOption.value.length > 0;
});

</script>

<template>

    <Head title="Create Optional Mapping" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Optional Mapping</h2>
        </template>
        <!-- {{ form.ClassId }} -->
<!-- {{ campusFeesMaster }} -->
<!-- or /  -->
<!-- {{ feesType }} -->
        <form @submit.prevent="form.post(route('optionalfee.submit'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Create Optional Mapping</div>
                        <div class="body">
                            <div class="row">
                              
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <InputLabel value="Class" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <select class="form-control" v-model="form.ClassId" @change="selectedClass($event.target.value)">
                                            <option selected disabled value="">Select a Class</option>
                                            <option v-for="list in classList" :key="list.id" :value="list.id">
                                                {{ list.ClassName }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.ClassId" />
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <InputLabel value="Session" />
                                        <input type="text" class="form-control" :value="currentSession?.start_date +' - '+ currentSession?.end_date " readonly />
                                        <InputError class="mt-2" :message="form.errors.SessionId" />
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <InputLabel value="Optional Fees Type" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <select class="form-control" v-model="form.FeesTypeNId" >
                                            <option selected disabled value="">Select Fees Type</option>
                                            <option v-for="feecycleList in filteredOptionalFeeOnCampusFeesBase" :key="feecycleList?.fee_type_rel?.id" :value="feecycleList?.fee_type_rel?.id">
                                                 {{ feecycleList?.fee_type_rel?.FeeName }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.FeesTypeNId" />
                                    </div>
                                </div>

                                <div class="col-md-4 mt-5">
                                    <div class="mb-3">
                                        <InputLabel value="Campus Fee Master" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <select class="form-control" v-model="form.CampusFeesMasterId" >
                                            <option selected disabled value="">Select a Campus Fee Master</option>
                                            <option v-for="feemsaterList in filteredCampusFees" :key="feemsaterList.id" :value="feemsaterList.id">
                                                {{feemsaterList?.fee_type_rel?.FeeName + ' - '}} {{ feemsaterList.Amount }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.CampusFeesMasterId" />
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mt-5">
                                    <div class="mb-3">
                                        <InputLabel value="Section" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <select class="form-control" v-model="form.SectionId" @change="fetchStudents($event.target.value)">
                                            <option selected disabled value="">Select a Section</option>
                                            <option v-for="secList in filteredSections" :key="secList.id" :value="secList.id">
                                                {{ secList.SectionName }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.SectionId" />
                                    </div>
                                </div>

                                <div class="col-md-4 mt-5">
                                    <div class="mb-3">
                                        <InputLabel value="Student" /> <span class="text-danger font-12 position-absolute">★</span>
 
                                        <Multiselect
                                            v-model="form.StudentId"
                                            :options="StudentOption"
                                            :multiple="true"
                                            :track-by="'id'"
                                            :label="'name'"
                                            placeholder="Select student"
                                            :close-on-select="false"
                                            :clear-on-select="false"
                                        >
                                            <template #beforeList v-if="hasStudents">
                                                <li class="multiselect__element">
                                                    <span
                                                        class="multiselect__option cursor-pointer"
                                                        @click.stop="toggleSelectAllStudents"
                                                    >
                                                        <strong>
                                                            {{ allStudentsSelected ? 'Deselect All Students' : 'Select All Students' }}
                                                        </strong>
                                                    </span>
                                                </li>
                                            </template>
                                            <template #noOptions>
                                                <div class="multiselect__option">
                                                    No students found
                                                </div>
                                            </template>
                                        </Multiselect>

                                        <div v-for="(student, index) in form.StudentId" :key="index">
                                            <span v-if="form.errors[`StudentId.${index}`]" class="text-red-500">
                                                {{ form.errors[`StudentId.${index}`] }}
                                            </span>
                                        </div>

                                        <InputError class="mt-2" :message="form.errors.StudentId" />
                                    </div>
                                </div>

                                <div class="col-md-4 mt-5">
                                    <div class="mb-3">
                                        <InputLabel value="From Month" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <TextInput type="month" v-model="form.FromMonth" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.FromMonth" />
                                    </div>
                                </div>

                                <div class="col-md-4 mt-5">
                                    <div class="mb-3">
                                        <InputLabel value="To Month" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <TextInput type="month" v-model="form.ToMonth" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.ToMonth" />
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
