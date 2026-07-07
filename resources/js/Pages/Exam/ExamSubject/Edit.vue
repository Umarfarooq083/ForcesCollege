<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { computed} from 'vue';


const props = defineProps({
    examTypes: Array,
    Classes: Object,
    Subjects: Array,
    examSubject: Object,
});

const formattedTime = computed({
  get() {
    if (!props.examSubject.Time) return ''
    return props.examSubject.Time.replace(/(AM|PM)/i, '').trim() // "16:10"
  },
  set(val) {
    props.examSubject.Time = val // update back when user edits
  }
})

const form = useForm({
    id: props.examSubject.id,
    Title: props.examSubject ? props.examSubject.Title : '',
    ExamId: props.examSubject ? props.examSubject.ExamId : '',
    ClassId: props.examSubject ? props.examSubject.ClassId : '',
    SubjectId: props.examSubject ? props.examSubject.SubjectId : '',
    Date: props.examSubject ? props.examSubject.Date : '',
    Time: formattedTime ? formattedTime : '',
    Duration: props.examSubject ? props.examSubject.Duration : '',
    CreditHours: props.examSubject ? props.examSubject.CreditHours : '',
    MarksMax: props.examSubject ? props.examSubject.MarksMax : '',
    MarksMin: props.examSubject ? props.examSubject.MarksMin : '',
    RoomNo: props.examSubject ? props.examSubject.RoomNo : '',
});



const filteredSubjects = computed(() => {
    if (!form.ClassId) return [];   // ⬅️ avoid filtering when no ClassId selected
    return props.Subjects.filter(
        subject => subject.ClassId == form.ClassId   // use == in case of string vs int
    );
});

// const preventDuplicateDate = (index, selectedDate) => {
//     const exists = form.rows.some((r, i) => i !== index && r.Date === selectedDate);
//     if (exists) {
//         alert('This date is already selected in another row.');
//         form.rows[index].Date = ''; // reset
//     }
// };

// const removeRow = (index) => {
//     form.rows.splice(index, 1);
// };
</script>

<template>
    <Head title="Exam Subject" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Exam Subject</h2>
        </template>
        
        <form @submit.prevent="form.put(route('examsubject.update'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Edit Exam Subject</div>
                        <div class="body">
                            <div class="row">
                                <!-- Title -->
                                <div class="col-md-3 mb-5">
                                    <InputLabel value="Title" /> <span class="text-danger">★</span>
                                    <TextInput type="text" v-model="form.Title" class="form-control" />
                                    <InputError class="mt-2" :message="form.errors.Title" />
                                </div>

                                <!-- Exam -->
                                <div class="col-md-3 mb-5">
                                    <InputLabel value="Exam" /> <span class="text-danger">★</span>
                                    <select v-model="form.ExamId" class="form-control">
                                        <option value="">Select Exam</option>
                                        <option v-for="type in examTypes" :key="type.id" :value="type.id">
                                            {{ type.ExamName }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.ExamId" />
                                </div>

                                <!-- Class -->
                                <div class="col-md-3 mb-5">
                                    <InputLabel value="Class" /> <span class="text-danger">★</span>
                                    <select v-model="form.ClassId" class="form-control">
                                        <option value="">Select Class</option>
                                        <option v-for="classList in Classes" :key="classList.id" :value="classList.id">
                                            {{ classList.ClassName }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.ClassId" />
                                </div>

                                 <div class="col-md-3 mb-5">
                                <InputLabel value="Subject" /><span class="text-danger">★</span>
                                <select v-model="form.SubjectId" class="form-control">
                                    <option disabled selected value="">Select Subject</option>
                                    <option v-for="subject in filteredSubjects" :key="subject.id" :value="subject.id" >
                                        {{ subject.SubjectName }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.SubjectId" />
                            </div>

                            <div class="col-md-3 mb-3">
                                <InputLabel value="Date" /><span class="text-danger">★</span>
                                <TextInput 
                                    type="date" 
                                    v-model="form.Date" 
                                    class="form-control"
                                />
                                <InputError class="mt-2" :message="form.errors.Date" />
                            </div>

                            <div class="col-md-3 mb-3">
                                <InputLabel value="Time" /><span class="text-danger">★</span>
                                <TextInput type="time" v-model="form.Time" class="form-control" />
                                 <InputError class="mt-2" :message="form.errors.Time" />
                            </div>

                           

                            <div class="col-md-3">
                                <InputLabel value="Total Marks" /><span class="text-danger">★</span>
                                <TextInput type="number" v-model="form.MarksMax" class="form-control" />
                                <InputError class="mt-2" :message="form.errors.MarksMax" />
                            </div>

                            <div class="col-md-3">
                                <InputLabel value="Passing Marks" /><span class="text-danger">★</span>
                                <TextInput type="number" v-model="form.MarksMin" class="form-control" />
                                <InputError class="mt-2" :message="form.errors.MarksMin" />
                            </div>

                             <div class="col-md-3 mt-5">
                                <InputLabel value="Duration" />
                                <TextInput type="number" v-model="form.Duration" class="form-control" />
                                 <InputError class="mt-2" :message="form.errors.Duration" />
                            </div>

                            <div class="col-md-3 mt-5">
                                <InputLabel value="Credit Hours" />
                                <TextInput type="number" v-model="form.CreditHours" class="form-control" />
                                <InputError class="mt-2" :message="form.errors.CreditHours" />
                            </div>

                            <div class="col-md-3 mt-5 ">
                                <InputLabel value="Room No" />
                                <TextInput type="number" v-model="form.RoomNo" class="form-control" />
                                <InputError class="mt-2" :message="form.errors.RoomNo" />
                            </div>
                                
                                <!-- Add Row Button -->
                                <!-- <div class="col-md-3 mt-4">
                                    <PrimaryButton type="button" @click="addRow">
                                        <i class="fa fa-plus"></i> Add Row
                                    </PrimaryButton>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>

               

                <!-- Submit -->
                <div class="col-md-12 mt-4">
                    <div class="text-end">
                        <PrimaryButton type="submit">Submit</PrimaryButton>
                    </div>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
