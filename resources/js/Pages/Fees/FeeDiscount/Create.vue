<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import 'vue-multiselect/dist/vue-multiselect.min.css';
import Multiselect from 'vue-multiselect'
import { ref, watch, computed, watchEffect } from 'vue';
import { useToast } from 'vue-toastification';
const toast = useToast();

const min = 0;
const max = 10000000000;
const step = 1;

const props = defineProps({
    classList: Object,
    sectionList: Array,
});

const studentList = ref([]);
const optionalFeemappingListFecthed = ref([]);
const optionalFeemappingMasterListFecthed = ref([]);

const form = useForm({
    ClassId: '',
    SectionId: '',
    StudentIdArray: [],
    loadedCampusMaster: '',
    CampusFeesMasterId: '',
    TotalFee: '',
    discountAmount: 0,
    BelanceAmount: 0,
    FeeCards: [],
    selectedFeeName: '',
});

const filteredSections = computed(() => {
    return props.sectionList.filter(section => section.ClassId === form.ClassId);
});

const loadCampusMasters = [
    { id: 1, name: 'Campus Fee Master' },
    { id: 2, name: 'Optional Campus Fee Master' }
];

// Reset form values when campus master changes
watch(() => form.loadedCampusMaster, (newValue, oldValue) => {
    if (newValue === 1) {
        form.CampusFeesMasterId = [];
    } else if (newValue === 2) {
        form.CampusFeesMasterId = '';
    } else {
        form.CampusFeesMasterId = [];
    }

    // Reset all other form fields
    form.StudentIdArray = [];
    form.TotalFee = 0;
    form.discountAmount = 0;
    form.BelanceAmount = 0;
    form.FeeCards = [];

    // Clear fetched data arrays
    optionalFeemappingListFecthed.value = [];
    optionalFeemappingMasterListFecthed.value = [];
});

watch(() => form.CampusFeesMasterId, (newVal) => {
    // alert(form.loadedCampusMaster);
    if (form.loadedCampusMaster === 1 || form.loadedCampusMaster === 2 && Array.isArray(newVal)) {
        console.log(newVal);
        form.FeeCards = newVal.map(item => ({
            masterId: item.id,
            masterName: item.fee_type_rel?.FeeName || 'Unknown Fee',
            studentId: '',
            totalFee: item.Amount || 0,
            discountAmount: 0,
            balanceAmount: item.Amount || 0,
        }));

        form.TotalFee = newVal.reduce((sum, item) => sum + (item.Amount || 0), 0);
    }
});

// Watch for discount amount changes in FeeCards
watchEffect(() => {
    if (form.loadedCampusMaster === 1) {
        form.FeeCards.forEach(card => {
            if (card.discountAmount > card.totalFee) {
                alert(`Discount cannot be greater than total for ${card.masterName}`);
                card.discountAmount = 0;
            }
            card.balanceAmount = card.totalFee - card.discountAmount;
        });
    }
});

// Watch for discount amount changes in single fee mode
watchEffect(() => {
    if (form.loadedCampusMaster === 2) {
        if (form.discountAmount > form.TotalFee) {
            alert('Discount Amount cannot be greater than total amount');
            form.discountAmount = 0;
        }
        form.BelanceAmount = form.TotalFee - form.discountAmount;
    }
});

const StudentOption = computed(() => {
    return studentList.value.map(student => ({
        id: student.id,
        name: student.FirstName,
        lastName: student.LastName
    }));
});

const LoadCampusFeeMasterForm = async (masterCampusid) => {
    try {
        console.log('Loading Campus Fee Master for ID:', masterCampusid);

        if (masterCampusid == 2) {
            const response = await axios.post(route('discount.optional.fee.mappng'), {
                classId: form.ClassId
            });
            optionalFeemappingListFecthed.value = response.data;
            console.log('Optional Fee Mapping Data:', response.data);
        } else if (masterCampusid == 1) {
            const response = await axios.post(route('discount.optional.fee.mappng.master'), {
                classId: form.ClassId
            });
            optionalFeemappingMasterListFecthed.value = response.data;
            console.log('Fee Mapping Master Data:', response.data);
        }
    } catch (error) {
        console.error('Request failed:', error);
    }
};

const fetchStudents = async (event) => {
    try {
        const response = await axios.post(route('create.fetch.student'), {
            sectoinId: event,
            classId: form.ClassId
        });
        studentList.value = response.data;
    } catch (error) {
        console.error('Request failed:', error);
    }
};

function selectedCampusMaster(selectedItems) {
    // Use the v-model value if selectedItems is not provided
    const items = selectedItems || form.CampusFeesMasterId;

    if (Array.isArray(items) && items.length > 0) {
        form.TotalFee = items.reduce((sum, item) => sum + (item.Amount || 0), 0);
    } else {
        form.TotalFee = 0;
    }

    form.discountAmount = 0;
    form.BelanceAmount = form.TotalFee;
}

function optionalselectedCampusMaster(selectedId) {
    form.discountAmount = 0;
    form.BelanceAmount = 0;
    const numericId = parseInt(selectedId);

    // Find selected fee with both string and numeric comparison
    const selectedFee = optionalFeemappingListFecthed.value.find(item =>
        item.id == selectedId || item.id === numericId
    );

    if (selectedFee && selectedFee.Amount) {
        form.TotalFee = selectedFee.Amount;
        form.BelanceAmount = selectedFee.Amount;
        form.selectedFeeName = selectedFee.fee_type_rel?.FeeName || "Unknown Fee";

        // ✅ Ab single select ke liye bhi FeeCards array banao
        form.FeeCards = [{
            masterId: selectedFee.id,
            masterName: selectedFee.fee_type_rel?.FeeName || "Unknown Fee",
            studentId: '',
            totalFee: selectedFee.Amount,
            discountAmount: 0,
            balanceAmount: selectedFee.Amount,
        }];

        console.log('Single select FeeCard:', form.FeeCards);
    } else {
        form.TotalFee = 0;
        form.BelanceAmount = 0;
        form.FeeCards = []; // agar fee nahi mili to array clear karo
        console.log('No fee found or Amount is missing');
    }
}

function increment() {
    if (form.discountAmount + step <= form.TotalFee) {
        form.discountAmount = parseFloat((form.discountAmount + step).toFixed(2));
    }
}

function decrement() {
    if (form.discountAmount - step >= min) {
        form.discountAmount = parseFloat((form.discountAmount - step).toFixed(2));
    }
}

function submitForm() {
    form.post(route('discount.submit'), {
        onError: (errors) => {
            if (errors.msg) {
                toast.error(errors.msg, {
                    position: "top-right",
                    autoClose: 3000,
                })
            }
        },
    })
}
</script>

<template>

    <Head title="Create Student discount" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Student discount</h2>
        </template>

        <form @submit.prevent="submitForm">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Create Student discount</div>
                        <div class="body">
                            <div class="row">

                                <!-- Class Selection -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Class" /> <span
                                            class="text-danger font-12 position-absolute">★</span>
                                        <select class="form-control" v-model="form.ClassId">
                                            <option selected disabled value="">Select a Class</option>
                                            <option v-for="list in classList" :key="list.id" :value="list.id">
                                                {{ list.ClassName }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.ClassId" />
                                    </div>
                                </div>

                                <!-- Section Selection -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Section" /> <span
                                            class="text-danger font-12 position-absolute">★</span>
                                        <select class="form-control" v-model="form.SectionId"
                                            @change="fetchStudents($event.target.value)">
                                            <option selected disabled value="">Select a Section</option>
                                            <option v-for="secList in filteredSections" :key="secList.id"
                                                :value="secList.id">
                                                {{ secList.SectionName }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.SectionId" />
                                    </div>
                                </div>

                                <!-- Campus Master Type Selection -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Load Campus Fee Master" /> <span
                                            class="text-danger font-12 position-absolute">★</span>
                                        <select class="form-control" v-model="form.loadedCampusMaster"
                                            @change="LoadCampusFeeMasterForm($event.target.value)">
                                            <option selected disabled value="">Select Campus Fee Master</option>
                                            <option v-for="campusMasterList in loadCampusMasters"
                                                :key="campusMasterList.id" :value="campusMasterList.id">
                                                {{ campusMasterList.name }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.loadedCampusMaster" />
                                    </div>
                                </div>

                                <!-- Campus Fee Master Selection (Multiple) - Only show when option 1 is selected -->
                                <div class="col-md-6" v-if="form.loadedCampusMaster === 1">
                                    <div class="mb-3">
                                        <InputLabel value="Campus Fee Master" /> <span
                                            class="text-danger font-12 position-absolute">★</span>
                                        <Multiselect v-model="form.CampusFeesMasterId"
                                            :options="optionalFeemappingMasterListFecthed" :multiple="true"
                                            track-by="id" :custom-label="option => option.fee_type_rel?.FeeName"
                                            placeholder="Select Campus Fee Masters"
                                            @update:modelValue="selectedCampusMaster" />
                                        <InputError class="mt-2" :message="form.errors.CampusFeesMasterId" />
                                    </div>
                                </div>
                                <!-- Optional Campus Fee Master Selection (Single) - Only show when option 2 is selected -->
                                <div class="col-md-6" v-if="form.loadedCampusMaster === 2">
                                    <div class="mb-3">
                                        <InputLabel value="Optional Campus Fee Master" /> <span
                                            class="text-danger font-12 position-absolute">★</span>
                                            
                                        <select class="form-control" v-model="form.CampusFeesMasterId"
                                            @change="optionalselectedCampusMaster($event.target.value)">
                                            <option selected disabled value="">Select an Optional Campus Fee Master
                                            </option>
                                            <option v-for="optfeeMasterData in optionalFeemappingListFecthed"
                                                :key="optfeeMasterData.id" :value="optfeeMasterData.id">
                                                {{ optfeeMasterData.fee_type_rel?.FeeName || 'Unknown Fee' }} - {{ optfeeMasterData?.Amount }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.CampusFeesMasterId" />
                                    </div>
                                </div>
                                <!-- Multiple Fee Cards - Only show when option 1 is selected and fees are selected -->
                                <div class="col-md-12" v-if="form.loadedCampusMaster === 1 && form.FeeCards.length > 0">
                                    <div v-for="(card, index) in form.FeeCards" :key="card.masterId" class="mb-3 card">
                                        <div class="card-header">
                                            <strong>{{ card.masterName }}</strong>
                                        </div>
                                        <div class="card-body row">

                                            <!-- Student Selection for each card -->
                                            <div class="col-md-6">
                                                <InputLabel value="Student" /> <span
                                                    class="text-danger font-12 position-absolute">★</span>
                                                <select class="form-control" v-model="card.studentId" required>
                                                    <option selected disabled value="">Select a Student</option>
                                                    <option v-for="student in StudentOption" :key="student.id"
                                                        :value="student.id">
                                                    {{ student.name }} {{ student?.lastName }}
                                                    </option>
                                                </select>
                                            </div>

                                            <!-- Total Fee (readonly) -->
                                            <div class="col-md-6">
                                                <InputLabel value="Total Fee" />
                                                <TextInput type="number" v-model="card.totalFee" class="form-control"
                                                    readonly />
                                            </div>

                                            <!-- Discount Amount -->
                                            <div class="col-md-6">
                                                <InputLabel value="Discount Amount" />
                                                <input type="number" class="form-control"
                                                    v-model.number="card.discountAmount" :max="card.totalFee" :min="0"
                                                    @input="card.balanceAmount = card.totalFee - card.discountAmount" />
                                            </div>

                                            <!-- Balance Amount (readonly) -->
                                            <div class="col-md-6">
                                                <InputLabel value="Balance Amount" />
                                                <TextInput type="number" v-model="card.balanceAmount"
                                                    class="form-control" readonly />
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <!-- Single Fee Form - Only show when option 2 is selected -->
                                <div class="col-md-12" v-if="form.loadedCampusMaster === 2 && form.CampusFeesMasterId">
                                    <div v-for="(card, index) in form.FeeCards" :key="card.masterId" class="mb-3 card">
                                        <div class="card-header">
                                            <strong>{{ card.masterName }}</strong>
                                        </div>
                                        <div class="card-body row">

                                            <!-- Student Selection for each card -->
                                            <div class="col-md-6">
                                                <InputLabel value="Student" /> <span
                                                    class="text-danger font-12 position-absolute">★</span>
                                                <select class="form-control" v-model="card.studentId" required>
                                                    <option selected disabled value="">Select a Student</option>
                                                    <option v-for="student in StudentOption" :key="student.id"
                                                        :value="student.id">
                                                        {{ student.name }} {{ student?.lastName }}
                                                    </option>
                                                </select>
                                            </div>

                                            <!-- Total Fee (readonly) -->
                                            <div class="col-md-6">
                                                <InputLabel value="Total Fee" />
                                                <TextInput type="number" v-model="card.totalFee" class="form-control"
                                                    readonly />
                                            </div>

                                            <!-- Discount Amount -->
                                            <div class="col-md-6">
                                                <InputLabel value="Discount Amount" />
                                                <input type="number" class="form-control"
                                                    v-model.number="card.discountAmount" :max="card.totalFee" :min="0"
                                                    @input="card.balanceAmount = card.totalFee - card.discountAmount" />
                                            </div>

                                            <!-- Balance Amount (readonly) -->
                                            <div class="col-md-6">
                                                <InputLabel value="Balance Amount" />
                                                <TextInput type="number" v-model="card.balanceAmount"
                                                    class="form-control" readonly />
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-md-12">
                                    <div class="text-end">
                                        <label class="w-100 d-inline-block">&nbsp;</label>
                                        <PrimaryButton :disabled="form.processing">
                                            <span v-if="form.processing">Processing...</span>
                                            <span v-else>Submit</span>
                                        </PrimaryButton>
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
}

.input-group-text {
    padding: 6px 12px;
    background: #f8f9fa;
    border: 1px solid #ced4da;
    border-left: 0;
    border-right: 0;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 12px 20px;
    margin-bottom: 0;
}

.btn-outline-secondary {
    border-color: #ced4da;
}

.text-center {
    text-align: center;
}
</style>