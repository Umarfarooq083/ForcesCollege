<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { computed, ref, watch } from 'vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
    ExamTerms: Array,
    FeeTypes: Array,
    Students: Array,
    Classes: Object,
    Session: Object,
});

const form = useForm({
    examtermid: '',
    session: props.Session,
    examid: '',
    classid: '',
    studentid: '',
    allterms: false,
});

const examtypes = ref(Array.isArray(props.FeeTypes) ? props.FeeTypes : []);
const classes = ref(Array.isArray(props.Classes) ? props.Classes : []);
const students = ref(Array.isArray(props.Students) ? props.Students : []);

// Watch ExamTermId  → fetch exams from server
watch(() => form.examtermid, async (newVal) => {
    if (newVal) {
        try {
            const res = await axios.get(route('getexam.types', { examtermid: newVal}));
            examtypes.value = res.data;
        } catch (error) {
            console.error('Error fetching exam:', error);
            toast.error('Failed to fetch Exams');
        }
    } else {
        examtypes.value = [];
    }
});

// Watch ExamId  → fetch classes from server
watch(() => form.examid, async (newVal) => {
    if (newVal) {
        try {
            const res = await axios.get(route('marks.data', { ExamId: newVal}));
            classes.value = res.data;
        } catch (error) {
            console.error('Error fetching classes:', error);
            toast.error('Failed to fetch classes');
        }
    } else {
        examtypes.value = [];
    }
});

// Watch ClassId → fetch subjects from server
watch(() => form.classid, async (newVal) => {
    if (newVal && form.examid) {
        try {
            const res = await axios.get(route('get.exam.students', {
                ExamId: form.examid,
                ClassId: newVal
            }));
            students.value = res.data;
        } catch (error) {
            console.error('Error fetching subjects:', error);
            toast.error('Failed to fetch Students');
        }
    } else {
        students.value = [];
    }
});

const sessionRange = computed(() => {
    return form.session?.start_date + '   to   ' + form.session?.end_date; 
});

// Computed property to check if form is ready for submission
const isFormReady = computed(() => {
    return form.examid && form.classid && form.examtermid && form.studentid;
});

// const submitForm = () => {
//     if (!isFormReady.value) {
//         toast.error('Please fill all required fields');
//         return;
//     }
    
//     form.get(route('result.card'), {
//         preserveScroll: true,
//         onSuccess: () => {
//             form.reset();
//         },
//     });
// };

// const submitForm = () => {
//     if (!isFormReady.value) {
//         toast.error('Please fill all required fields');
//         return;
//     }

//     axios.get(route('result.card'), {
//         params: {
//             examtermid: form.examtermid,
//             examid: form.examid,
//             classid: form.classid,
//             studentid: form.studentid,
//             session: form.session,
//             allterms: form.allterms,
//         },
//         responseType: 'blob', // PDF / print ke liye
//         headers: {
//             Accept: 'application/pdf',
//         }
//     })
//     .then((response) => {
//         const blob = new Blob([response.data], {
//             type: 'application/pdf',
//         });

//         const url = window.URL.createObjectURL(blob);
//         window.open(url, '_blank');

//         // cleanup
//         setTimeout(() => window.URL.revokeObjectURL(url), 1000);
//     })
//     .catch((error) => {
//         console.error('Result Card Error:', error);
//         toast.error('Failed to generate Result Card');
//     });
// };


const submitForm = () => {
    if (!isFormReady.value) {
        toast.error('Please fill all required fields');
        return;
    }

    // Direct blade view open karo
    const url = route('result.card', {
        examtermid: form.examtermid,
        examid: form.examid,
        classid: form.classid,
        studentid: form.studentid,
        session: form.session,
        allterms: form.allterms ? 1 : 0,
    });

    window.open(url, '_blank');
};



</script>

<template>
    <Head title="Result Sheet" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Result Sheet</h2>
        </template>
        <form @submit.prevent="submitForm">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                       
                        <div class="header d-flex align-items-center justify-content-between">
                            <!-- Left group -->
                            <div class="d-flex align-items-center">
                                <span class="me-2">Term Wise Result Sheet</span>
                                <span class="custom-control custom-switch">
                                    <input type="checkbox" v-model="form.allterms" class="custom-control-input" id="resultSwitch">
                                    <label class="custom-control-label cstm_switch" for="resultSwitch"></label>
                                </span>
                            </div>

                            <!-- Right side text -->
                            <span>Result Sheet</span>
                        </div>
                        <div class="body">
                            <div class="row">
                                <!-- Fee Type -->
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <InputLabel value="Exam Terms" />
                                        <select 
                                            class="form-control" 
                                            v-model="form.examtermid"
                                        >
                                            <option selected disabled value="">Select Exam Term</option>
                                            <option v-for="ExamTerm in props.ExamTerms" :key="ExamTerm.id" :value="ExamTerm.id">
                                                {{ ExamTerm?.ExamTermName }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.examtermid" />
                                    </div>
                                </div>
                                
                                <!-- Amount -->
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <InputLabel value="Exam" />
                                        <select 
                                            class="form-control" 
                                            v-model="form.examid"
                                        >
                                            <option selected disabled value="">Select Exam</option>
                                            <option v-for="examtype in examtypes" :key="examtype.id" :value="examtype.id">
                                                {{ examtype?.ExamName }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.examid" />
                                    </div>
                                </div>
                                
                                <!-- Class Multiselect -->
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <InputLabel value="Class" />
                                        <select 
                                            class="form-control" 
                                            v-model="form.classid"
                                        >
                                            <option selected disabled value="">Select a class</option>
                                             <option v-for="examclasss in classes" :key="examclasss.id" :value="examclasss.id">
                                                {{ examclasss?.ClassName }}
                                            </option>
                                        </select>
                                          <InputError class="mt-2" :message="form.errors.examid" />
                                    </div>  
                                </div>
                                
                                <!-- Class Multiselect -->
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <InputLabel value="Student" />
                                        <select 
                                            class="form-control" 
                                            v-model="form.studentid"
                                        >
                                            <option selected disabled value="">Select a Fee type</option>
                                            <option v-for="student in students" :key="student.id" :value="student.id">
                                                {{ student?.FirstName }} {{ student?.LastName }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.studentid" />
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

                                <!-- Add Row Button -->
                                <div class="col-md-2">
                                    <PrimaryButton type="submit" :disabled="!isFormReady">
                                        <i class="fa fa-plus"></i> Get Result Card
                                    </PrimaryButton>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
