<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';
import 'vue-multiselect/dist/vue-multiselect.min.css';
import { computed } from 'vue';


const props = defineProps({
    classList: Object,
    sectionList: Object,
    StaffList: Object,
    assignClassTeacher: Object,
});
const form = useForm({
    id: props.assignClassTeacher.id,
    ClassId: props.assignClassTeacher.ClassId,
    SectionId: props.assignClassTeacher.SectionId,
    StaffId: props.assignClassTeacher.StaffId,
});

const filteredSections = computed(() => {
    return props.sectionList.filter(section => section.ClassId === form.ClassId);
});


</script>

<template>

    <Head title="Update Assign Class Teacher" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Update Assign Class Teacher</h2>
        </template>
      <form @submit.prevent="form.put(route('assign.class.teacher.update'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Update Assign Class Teacher</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                         <InputLabel value="Class *" />
                                         <select class="form-control" v-model="form.ClassId">
                                            <option selected disabled value="">Select a Class</option>
                                            <option v-for="classList in classList" :key="classList.id" :value="classList.id">
                                                {{ classList.ClassName }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.ClassId" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Section *" />
                                        <select class="form-control" v-model="form.SectionId">
                                            <option selected disabled value="">Select a Section</option>
                                            <option v-for="sectionList in filteredSections" :key="sectionList.id" :value="sectionList.id">
                                                {{ sectionList.SectionName }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.SectionId" />
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Staff *" />
                                        <select class="form-control" v-model="form.StaffId">
                                            <option selected disabled value="">Select a Section</option>
                                            <option v-for="staff in StaffList" :key="staff.id" :value="staff.id">
                                                 {{ staff.FirstName + ' '+ staff?.LastName }} 
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.StaffId" />
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3 text-end">
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
