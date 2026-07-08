<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';


</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-weight-bold text-dark">Dashboard</h2>
        </template>
        <div class="row">
          <h3 class="ml-5">Dashboard</h3>

        </div>
    </AuthenticatedLayout>
</template>


<style scoped>
.card {
    height: 100%;
}
</style>