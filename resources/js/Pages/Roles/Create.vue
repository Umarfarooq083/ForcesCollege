<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { useForm } from '@inertiajs/vue3';

// campusList

const props = defineProps({
    campusList: Object
});

const form = useForm({
    name: '',
    is_super: false,
    campus_id: ''
});
</script>

<template>

    <Head title="Create Roles" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Roles
            </h2>
        </template>

        <div class="row clearfix">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <h2>Create Role</h2>
                    </div>
                    <div class="body">
                        <form id="basic-form" @submit.prevent="form.post(route('role.submit'))">
                            <div class="form-group">
                                <InputLabel for="name" value="Name" />
                                <TextInput id="name" type="text" class="form-control" v-model="form.name" 
                                    autofocus autocomplete="name" />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="form-group">
                                <InputLabel for="campus" value="Campus" />
                                <select class="form-control"  v-model="form.campus_id">
                                    <option value="">Select a campus</option>
                                    <option v-for="campus in campusList" :value="campus.id">{{ campus.SchoolName}}</option>
                                </select>
                            </div>
                            
                            
                            <div class="form-group">
                                <InputLabel for="Is Super Role" />
                                <label class="flex items-center">
                                    <Checkbox name="is_super" v-model:checked="form.is_super" />
                                    <span class="ms-2 text-sm text-gray-600">Is Super Role</span>
                                </label>
                            </div>

                            <br>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </AuthenticatedLayout>
</template>
