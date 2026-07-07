<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router ,Link } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import 'vue-multiselect/dist/vue-multiselect.min.css';
import Pagination from '@/Components/Pagination.vue';
import { computed,ref,watch  } from 'vue';
import debounce from 'lodash/debounce';
import Multiselect from 'vue-multiselect'
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

const studentList = ref([]);
const errors = ref({});
const isLoading = ref(false); 
const isFetchingStudents = ref(false); 

const form = useForm({
    ClassId: '',
    SectionId: '', 
    StudentId: [], 
    ChallanMonth: '', 
    ChallanMonths: [],
    CombineMonths: false,
    DueDate: '', 
    ExpiryDate: '', 
});

const challanMode = ref('single'); // single | multi

const props = defineProps({
    classAndSections: Array,
    challansList: Array,
})

const fetchStudents = async (event) => {
    if (!event) return;
    
    isFetchingStudents.value = true;
    try {
        const response = await axios.post(route('create.fetch.student'), {
            sectoinId: event,
            classId: form.ClassId
        })
        studentList.value = response.data   
        form.StudentId = [];
    } catch (error) {
        errors.value = error.response.data.errors 
    } finally {
        isFetchingStudents.value = false;
    }  
};

// const StudentOption = computed(() => {
//     return studentList.value.map(student => ({
//         id: student.id,
//         name: student?.FirstName + ' ' + student?.LastName
//     }));
// });

watch(
    () => challanMode.value,
    (mode) => {
        form.CombineMonths = mode === 'multi';
    },
    { immediate: true }
);

const StudentOption = computed(() => {
    return studentList.value.map(student => ({
        id: student.id,
        name: `${student?.FirstName ?? ''} ${student?.LastName ?? ''}`.trim()
    }));
});




const enhancedStudentOptions = computed(() => {
    if (StudentOption.value.length > 0) {
        return [
            { id: 'select-all', name: 'Select All Students', isSelectAll: true },
            ...StudentOption.value
        ];
    }
    return StudentOption.value;
});

watch(
    () => form.StudentId,
    (newSelection, oldSelection) => {
        if (!newSelection || !StudentOption.value.length) return;
        
        const currentStudentSelections = newSelection.filter(item => !item.isSelectAll);
        const hadSelectAll = oldSelection?.some(item => item.isSelectAll);
        const hasSelectAll = newSelection.some(item => item.isSelectAll);
      
        if (hasSelectAll && !hadSelectAll) {
            form.StudentId = [...StudentOption.value, { id: 'select-all', name: 'Select All Students', isSelectAll: true }];
        } 
        else if (!hasSelectAll && hadSelectAll) {
            form.StudentId = [];
        }
        else if (!hasSelectAll) {
            const allSelected = StudentOption.value.length === currentStudentSelections.length;
            
            if (allSelected && currentStudentSelections.length === StudentOption.value.length) {
                form.StudentId = [...currentStudentSelections, { id: 'select-all', name: 'Select All Students', isSelectAll: true }];
            }
        }
    },
    { deep: true }
);

watch(
  () => form.ChallanMonth,
  (newMonth) => {
    if (challanMode.value !== 'single') return;
    if (newMonth) {
      const [year, month] = newMonth.split('-').map(Number)
      let dueDateObj = new Date(year, month - 1, 10) 
      
      let dayOfWeek = dueDateObj.getDay() 

      if (dayOfWeek === 6) { 
        dueDateObj.setDate(dueDateObj.getDate() + 2) 
      } else if (dayOfWeek === 0) { 
        dueDateObj.setDate(dueDateObj.getDate() + 1)
      }
      const formatDate = (dateObj) => {
        const y = dateObj.getFullYear()
        const m = String(dateObj.getMonth() + 1).padStart(2, '0')
        const d = String(dateObj.getDate()).padStart(2, '0')
        return `${y}-${m}-${d}`
      }
      form.DueDate = formatDate(dueDateObj)
      const lastDay = new Date(year, month, 0)
      form.ExpiryDate = formatDate(lastDay)
    } else {
      form.DueDate = ''
      form.ExpiryDate = ''
    }
  }
)

const monthOptions = computed(() => {
    const now = new Date();
    const options = [];
    for (let i = 0; i <= 24; i++) {
        const d = new Date(now.getFullYear(), now.getMonth() + i, 1);
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const value = `${y}-${m}`;
        const label = d.toLocaleString(undefined, {
            month: 'short',
            year: 'numeric'
        });
        options.push({ value, label });
    }
    return options;
});

const addMonthTag = (newTag) => {
    if (!newTag) return;
    const value = String(newTag).trim();
    if (!/^[0-9]{4}-[0-9]{2}$/.test(value)) {
        errors.value = { ...errors.value, ChallanMonths: ['Month format should be YYYY-MM'] };
        return;
    }
    const exists = form.ChallanMonths.some(m => (m.value ?? m) === value);
    if (!exists) {
        form.ChallanMonths.push({ value, label: value });
    }
};

const submitChallan = async () => {
    isLoading.value = true;
    try {
        const filteredStudents = form.StudentId.filter(student => 
            student.id !== 'select-all' && !student.isSelectAll
        );
        // Check if any students are selected
        if (filteredStudents.length === 0) {
            errors.value = { StudentId: ['Please select at least one student'] };
            return;
        }
        const submitData = { ...form, StudentId: filteredStudents };

        if (challanMode.value === 'multi') {
            submitData.ChallanMonth = null;
            submitData.DueDate = null;
            submitData.ExpiryDate = null;
            submitData.ChallanMonths = (form.ChallanMonths || []).map(m => m.value ?? m);
        } else {
            submitData.ChallanMonths = null;
        }
        const response = await axios.post(route('challan.submit'), submitData);
        const challanIds = response.data.challan_id;
        window.open(route('challan.print', { id: challanIds.join(',') }), '_blank');
        
        errors.value = {};
        
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
        } else {
            console.log('Request failed:', error);
            errors.value = { general: ['An error occurred while generating challans'] };
        }
    } finally {
        isLoading.value = false;
    }
};

const filteredSections = computed(() => {
    return props.classAndSections.sectionList.filter(section => section.ClassId === form.ClassId);
});

const selectAll = ref(false);
const selectedChallans = ref([]);

watch(selectedChallans, (newVal) => {
  selectAll.value = newVal.length === props.challansList.data.length;
});

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedChallans.value = props.challansList.data.map(challan => challan.id);
  } else {
    selectedChallans.value = [];
  }
};

const printSelectedChallans = () => {
  if (selectedChallans.value.length === 0) {
    alert("Please select at least one challan.");
    return;
  }
  window.open(route('challan.print', { id: selectedChallans.value.join(',') }), '_blank');
};

const search = ref('');
watch(search, debounce((value) => {
  router.get(route('challan.list'), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 800));
</script>

<template>
    <Head title="Challan List" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Zone
            </h2>
        </template>

         <form @submit.prevent="submitChallan">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Generate Challan </div>
                        <div class="body">
                            <!-- Global Loading Overlay -->
                            <div v-if="isLoading" class="loading-overlay">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden"></span>
                                </div>
                                <p class="mt-2">Generating Challans...</p>
                            </div>

                            <div class="row">

                                <div class="col-xl-3 col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Class" />
                                         <select class="form-control" v-model="form.ClassId" :disabled="isLoading">
                                            <option selected disabled value="">Select a Class</option>
                                            <option v-for="list in classAndSections.classList" :key="list.id" :value="list.id">
                                                {{ list.ClassName }}
                                            </option>
                                        </select>
                                    <InputError v-if="errors.ClassId" class="mt-2" :message="errors.ClassId[0]" />
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <InputLabel  value="Section" />
                                        <div class="position-relative">
                                            <select class="form-control" v-model="form.SectionId" 
                                                @change="fetchStudents($event.target.value)" 
                                                :disabled="isLoading">
                                                <option selected disabled value="">Select a Section</option>
                                                <option v-for="secList in filteredSections" :key="secList.id" :value="secList.id">
                                                    {{ secList.SectionName }}
                                                </option>
                                            </select>
                                            <div v-if="isFetchingStudents" class="position-absolute top-50 end-0 translate-middle-y me-2">
                                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                        </div>
                                    <InputError v-if="errors.SectionId" class="mt-2" :message="errors.SectionId[0]" />
                                    </div>
                                </div>

                                <div class="col-xl-4 col-lg-6 col-md-8">
                                    <div class="mb-3">
                                        <InputLabel value="Student" />
                                        <Multiselect
                                            v-model="form.StudentId"
                                            :options="enhancedStudentOptions"
                                            :multiple="true"
                                            :select-label="''"
                                            :deselectLabel="''"
                                            :track-by="'id'"
                                            :label="'name'"
                                            placeholder="Select Student"
                                            :close-on-select="false"
                                            :disabled="isLoading || isFetchingStudents"
                                            :class="{ 'opacity-50': isFetchingStudents }"
                                        >
                                            <template #option="{ option }">
                                                <div v-if="option.isSelectAll" class="multiselect-option select-all-option">
                                                    <strong>{{ option.name }}</strong>
                                                </div>
                                                <div v-else>
                                                    {{ option.name }}
                                                </div>
                                            </template>
                                            <template #noResult>
                                                <div v-if="isFetchingStudents" class="text-center py-2">
                                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                                </div>
                                                <div v-else>
                                                    No students found
                                                </div>
                                            </template>
                                        </Multiselect>
                                    </div>
                                    <InputError v-if="errors.StudentId" class="mt-2" :message="errors.StudentId[0]" />
                                </div>

                                <div class="col-xl-2 col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <!-- <InputLabel value="Billing Month" /> -->
                                        <div class="d-flex gap-2 align-items-center mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" id="mode-single" value="single" v-model="challanMode" :disabled="isLoading">
                                                <label class="form-check-label" for="mode-single">Single Or</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" id="mode-multi" value="multi" v-model="challanMode" :disabled="isLoading">
                                                <label class="form-check-label" for="mode-multi">Multiple Challans</label>
                                            </div>
                                        </div>

                                        <TextInput v-if="challanMode === 'single'" type="month" v-model="form.ChallanMonth" class="form-control" :disabled="isLoading" />
                                        <Multiselect
                                            v-else
                                            v-model="form.ChallanMonths"
                                            :options="monthOptions"
                                            :multiple="true"
                                            :taggable="true"
                                            tag-placeholder="Add month (YYYY-MM)"
                                            placeholder="Select Months"
                                            :close-on-select="false"
                                            :track-by="'value'"
                                            :label="'label'"
                                            @tag="addMonthTag"
                                            :disabled="isLoading"
                                        />
                                        <div v-if="challanMode === 'multi'" class="form-check mt-2">
                                            <label class="form-check-label" for="combine-months">
                                                Challan will be Combined into single challan (Fee cycle)
                                            </label>
                                        </div>

                                        <InputError v-if="errors.ChallanMonth && challanMode === 'single'" class="mt-2" :message="errors.ChallanMonth[0]" />
                                        <InputError v-if="errors.ChallanMonths && challanMode === 'multi'" class="mt-2" :message="errors.ChallanMonths[0]" />
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <InputLabel  value="Due date" />
                                        <TextInput v-if="challanMode === 'single'" type="date" v-model="form.DueDate" class="form-control" readonly :disabled="isLoading"/>
                                        <div v-else class="form-control bg-light text-muted">Auto (per month)</div>
                                        <InputError v-if="errors.DueDate && challanMode === 'single'" class="mt-2" :message="errors.DueDate[0]" />
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <InputLabel  value="Expiry Date" />
                                        <TextInput v-if="challanMode === 'single'" type="date" v-model="form.ExpiryDate" class="form-control" readonly :disabled="isLoading"/>
                                        <div v-else class="form-control bg-light text-muted">Auto (per month)</div>
                                        <InputError v-if="errors.ExpiryDate && challanMode === 'single'" class="mt-2" :message="errors.ExpiryDate[0]" />
                                    </div>
                                </div>

                                <div class="col-xl-2 col-lg-4 col-md-6">
                                     <label class="w-100">&nbsp</label>
                                    <PrimaryButton 
                                        v-if="$page.props.auth.user.user_permissions.indexOf('challan.submit') > -1"
                                        :disabled="isLoading"
                                    >
                                        <span v-if="isLoading" class="d-inline-flex align-items-center">
                                            <div class="spinner-border spinner-border-sm me-2" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            Generating...
                                        </span>
                                        <span v-else>
                                            Generate Challan
                                        </span>
                                    </PrimaryButton>
                                </div>

                            </div>
                            
                        </div>
                    </div>
                </div>
              
            </div>
        </form>

        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center flex-wrap">
                    <div class="font-weight-bold">Generated Challan List</div>
                    <div>
                        <input type="text" class="form-control form-control-sm" v-model="search"
                            placeholder="Search by Class, Section, Student, Challan No ..." style="width: 350px;"/>
                    </div>

                    <PrimaryButton 
                        @click="printSelectedChallans">
                        Print Selected Challans
                    </PrimaryButton>
                </div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table mb-0 table-hover c_list table-bordered">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select_all" v-model="selectAll" @change="toggleSelectAll"/><label for="select_all" class="ms-2">Challan No</label></th>
                                <th>Student</th>
                                <th>Class</th>
                                <th>Section</th>
                                <th>Challan Month</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Expiry Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="challanData in challansList.data">
                                <td><input type="checkbox" :value="challanData.id" v-model="selectedChallans" :id="'challan-' + challanData.id"/> <label :for="'challan-' + challanData.id" class="ms-2">{{ challanData.challan_no }}</label></td>
                                <td><strong>{{ `${challanData?.student_rel?.FirstName ?? ''} ${challanData?.student_rel?.LastName ?? ''}`.trim() }}</strong></td>
                                <td><strong>{{ challanData?.class_rel?.ClassName }}</strong></td>
                                <td>{{ challanData?.section_rel?.SectionName }}</td>
                                <td>{{ challanData.ChallanMonth }}</td>
                                <td>{{ challanData.DueDate }}</td>
                                <td>{{ challanData.Status }}</td>
                                <td>{{ challanData.ExpiryDate }}</td>   
                                <td>
                                    <div class="dropdown">
                                    <button style="font-size: 19px; color: #58666e;" class="btn btn-link" type="button" id="actionMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-bars"></i>
                                    </button>

                                    <div class="dropdown-menu" aria-labelledby="actionMenu">
                                       <strong><a  as="a" class="dropdown-item" target="_blank" :href="route('challan.print', { id: challanData.id })" >
                                           <i class="fa fa-print"></i> Print Challan</a></strong>
                                        <hr v-if="challanData.Status === 'Paid'">
                                        <!-- v-if="$page.props.auth.user.user_permissions.indexOf('challan.markasunpaid') > -1"  -->
                                        <strong v-if="challanData.Status == 'Paid'">
                                            <Link class="dropdown-item" method="post" :href="route('challan.markasunpaid', { id: challanData.id })" >
                                           <i class="fa fa-envelope" aria-hidden="true"></i> Mark As Unpaid</Link></strong>
                                           <!-- <i class="fa fa-envelope-open" aria-hidden="true"></i> -->
                                           
                                    </div>
                                    </div>
                                </td>   
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="challansList.links" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.multiselect__option {
    white-space: unset !important;
}
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.opacity-50 {
    opacity: 0.5;
}

.position-relative {
    position: relative;
}

.position-absolute {
    position: absolute;
}

.top-50 {
    top: 50%;
}

.end-0 {
    right: 0;
}

.translate-middle-y {
    transform: translateY(-50%);
}

.me-2 {
    margin-right: 0.5rem;
}

</style>
