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
    session: Object,
    zones: Array
});

const form = useForm({
    name: props.session.name,
    start_date: props.session.start_date,
    end_date: props.session.end_date,
    zoneid: props.session.zoneid,
});

</script>
<template>
    <Head title="Edit Session" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Session</h2>
        </template>
       
        <form @submit.prevent="form.put(route('lmssessions.update', session.id))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Edit Session</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <InputLabel value="Name" />
                                        <TextInput type="text" v-model="form.name" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.name" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <InputLabel value="Start Date" />
                                        <TextInput
                                            type="date"
                                            v-model="form.start_date"
                                            class="form-control"
                                        />
                                        <InputError class="mt-2" :message="form.errors.start_date" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <InputLabel value="End Date" />
                                        <TextInput
                                            type="date"
                                            v-model="form.end_date"
                                            class="form-control"
                                        />
                                        <InputError class="mt-2" :message="form.errors.end_date" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <InputLabel value="Zone" />
                                        <select v-model="form.zoneid" class="form-control">
                                            <option value="">Select Zone</option>
                                            <option v-for="zone in props.zones" :key="zone.id" :value="zone.id">
                                                {{ zone.name }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.zoneid" />
                                    </div>
                                </div>
                                <div class="col-md-12 text-end">
                                    <PrimaryButton :disabled="form.processing">Submit</PrimaryButton>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>

