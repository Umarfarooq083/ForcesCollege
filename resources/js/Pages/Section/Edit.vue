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
    sectionData: Object,
});

const form = useForm({
    id: props.sectionData.id,
    SectionName: props.sectionData.SectionName,
});


</script>

<template>

    <Head title="Student Inquiry" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Update Section</h2>
        </template>
 
        <form @submit.prevent="form.put(route('section.update'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Update Section</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Section Name *" />
                                        <TextInput type="text" v-model="form.SectionName"  class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.SectionName" />
                                    </div>
                                </div>
                         
                                <div class="col-md-6">
                                    <div class="mb-3 text-end">
                                        <label class="w-100 d-inline-block">&nbsp;</label>
                                        <PrimaryButton>Update</PrimaryButton>
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
