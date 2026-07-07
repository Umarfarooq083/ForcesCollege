<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';
import 'vue-multiselect/dist/vue-multiselect.min.css'


const props = defineProps({
    subjectData: Object,
    classesList: Object,
});
const form = useForm({
    id: props.subjectData.id,
    SubjectName: props.subjectData.SubjectName,
    SubjectType: props.subjectData.SubjectType,
    SubjectCode: props.subjectData.SubjectCode,
    ClassId: props.subjectData.ClassId,
});

const SubjectTypeList = [
    { id: 'Practical', name: 'Practical' },
    { id: 'Theory', name: 'Theory' },
];

</script>

<template>

    <Head title="Student Inquiry" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Campus</h2>
        </template>
 
        <form @submit.prevent="form.put(route('subject.update'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Create Sbject</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Subject Name *" />
                                        <TextInput type="text" v-model="form.SubjectName" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.SubjectName" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Subject Type *" />
                                        <select class="form-control" v-model="form.SubjectType">
                                            <option selected disabled value="">Select a Type</option>
                                            <option v-for="typeList in SubjectTypeList" :key="typeList.id" :value="typeList.id">
                                                {{ typeList.name }}
                                            </option>
                                        </select>
                                         <InputError class="mt-2" :message="form.errors.SubjectType" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Subject Code *" />
                                         <TextInput type="text" v-model="form.SubjectCode" class="form-control" />
                                         <InputError class="mt-2" :message="form.errors.SubjectCode" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Class *" />
                                        <select class="form-control" v-model="form.ClassId">
                                            <option selected disabled value="">Select a Class</option>
                                            <option v-for="List in classesList" :key="List.id" :value="List.id">
                                                {{ List.ClassName }}
                                            </option>
                                        </select>
                                         <InputError class="mt-2" :message="form.errors.ClassId" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    
                                </div>

                                <div class="col-md-6">
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
