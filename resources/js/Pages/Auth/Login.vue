<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>
        <form class="form-auth-small" @submit.prevent="submit">
            <div class="form-group">
                <InputLabel for="email" value="Email" class="text-left font-weight-bold w-100 mb-1"/>
                <TextInput id="email" type="email" class="form-control" v-model="form.email" required autofocus autocomplete="username"/>
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div class="form-group">
                <InputLabel for="password" value="Password" class="text-left font-weight-bold w-100 mb-1"/>
                <TextInput id="password" type="password" class="form-control" v-model="form.password" required autocomplete="current-password"/>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div class="form-group clearfix">
                <label for="remember" class="fancy-checkbox element-left">	
                    <Checkbox name="remember" id="remember" v-model:checked="form.remember" />
                    <span class="text-dark font-weight-bold">Remember me</span>
                </label>
            </div>
            <PrimaryButton class="btn btn-primary btn-lg btn-block"
                :class="{ '': form.processing }"
                :disabled="form.processing">
                LOGIN
            </PrimaryButton>
            <div class="bottom">
                <span class="helper-text">
                    <i class="fa fa-lock me-1"></i> 
                    <Link v-if="canResetPassword" :href="route('password.request')">Forgot password?</Link>
                </span>
            </div>
        </form>
    </GuestLayout>
</template>
<style>
    .btn.btn-primary {
        background-color: #1f4020;
        border-color: #1f4020;
    }
    .form-control:focus {
        border-color: #1f4020;
    }
    .fancy-checkbox input[type="checkbox"]:checked + span:before {
        color: #1f4020;
        border-color: #1f4020;
    }
</style>