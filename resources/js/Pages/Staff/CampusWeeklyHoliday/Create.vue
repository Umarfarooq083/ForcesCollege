<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    campusList: Array,
});

const weekendOptions = [
    { value: 'saturday', label: 'Saturday' },
    { value: 'sunday', label: 'Sunday' },
    { value: 'both', label: 'Saturday & Sunday' },
    { value: 'none', label: 'No Weekend' },
];

const form = useForm({
    campus_id: '',
    weekend_day: 'both',
    is_active: true,
});
</script>

<template>
    <Head title="Create Campus Weekly Holiday" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Campus Weekly Holiday</h2>
        </template>

        <form @submit.prevent="form.post(route('campus-weekly-holiday.submit'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Create Campus Weekly Holiday Configuration</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Campus *" />
                                        <select class="form-control" v-model="form.campus_id">
                                            <option selected disabled value="">Select a Campus</option>
                                            <option v-for="campus in campusList" :key="campus.id" :value="campus.id">
                                                {{ campus.SchoolName }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.campus_id" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Weekend Day *" />
                                        <select class="form-control" v-model="form.weekend_day">
                                            <option v-for="option in weekendOptions" :key="option.value" :value="option.value">
                                                {{ option.label }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.weekend_day" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" v-model="form.is_active" id="isActive">
                                            <label class="form-check-label" for="isActive">
                                                Active
                                            </label>
                                        </div>
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