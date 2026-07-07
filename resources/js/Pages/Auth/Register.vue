<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form class="form-auth-small" @submit.prevent="submit">
            <div class="form-group">
                <InputLabel for="name" value="Name" class="text-left font-weight-bold w-100 mb-1"/>
                <TextInput
                    id="name"
                    type="text"
                    class="form-control"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="form-group">
                <InputLabel for="email" value="Email" class="text-left font-weight-bold w-100 mb-1"/>
                <TextInput
                    id="email"
                    type="email"
                    class="form-control"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="form-group">
                <InputLabel for="password" value="Password" class="text-left font-weight-bold w-100 mb-1"/>
                <TextInput
                    id="password"
                    type="password"
                    class="form-control"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div class="form-group">
                <InputLabel for="password_confirmation" value="Confirm Password" class="text-left font-weight-bold w-100 mb-1"/>
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="form-control"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>
            <PrimaryButton class="btn btn-primary btn-lg btn-block"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Register
            </PrimaryButton>
            <div class="bottom">
                <span class="helper-text text-dark">Already have an account? 
                    <Link :href="route('login')">Login</Link>
                </span>
            </div>
        </form>
    </GuestLayout>
</template>
