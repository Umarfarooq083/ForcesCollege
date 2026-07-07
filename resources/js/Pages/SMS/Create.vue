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
    smsCreditCount: '',
});
</script>

<template>

    <Head title="Create Credit" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create Credit
            </h2>
        </template>

        <div class="row clearfix">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <h2>Create Credit</h2>
                    </div>
                    <div class="body">
                        <form id="basic-form" @submit.prevent="form.post(route('smscredit.submit'))">
                            <div class="form-group col-md-3">
                                <InputLabel for="credit" value="SMS Credit" />
                                <TextInput id="credit" type="number" class="form-control" v-model="form.smsCreditCount" 
                                    autofocus autocomplete="Credit" />
                                <InputError class="mt-2" :message="form.errors.smsCreditCount" />
                            </div>

                            <button type="submit" class="btn btn-primary" style="margin-left: 16px;">Submit</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </AuthenticatedLayout>
</template>
