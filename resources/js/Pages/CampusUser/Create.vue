<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import { computed } from 'vue';

const props = defineProps({
    roles: Object
});

const RolesOption = computed(() => {
    return props.roles.map(role => ({
        id: role.id,
        name: role.name
    }));
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    phone_no: '',
    address: '',
    roles_ids: [],    
});

</script>

<template>

    <Head title="Create Campus" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Campus</h2>
        </template>
       
        <form @submit.prevent="form.post(route('user.submit'))" >
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Create User</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Name" />
                                        <TextInput type="text" v-model="form.name" class="form-control" />
                                         <InputError class="mt-2" :message="form.errors.name" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel  value="Email" />
                                        <TextInput  type="email" v-model="form.email" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.email" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel  value="Password" />
                                        <TextInput  type="text" v-model="form.password" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.password" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Phone No" />
                                        <TextInput   type="text" inputmode="numeric" pattern="[0-9]*" v-model="form.phone_no" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.phone_no" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Address" />
                                        <TextInput type="text" v-model="form.address" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Roles" />
                                        <Multiselect
                                            v-model="form.roles_ids"
                                            :options="RolesOption"
                                            :multiple="true"
                                            :track-by="'id'"
                                            :label="'name'"
                                            placeholder="Select roles"
                                            />
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.roles_ids" />
                                </div>

                                 <div class="mt-4 col-md-12 text-end">
                                    <PrimaryButton>Submit</PrimaryButton>
                                </div>

                            </div>
                            
                        </div>
                    </div>
                </div>
              
            </div>
        </form>
    </AuthenticatedLayout>
</template>
