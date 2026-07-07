<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';

</script>

<template>

    <div id="wrapper" class="theme-cyan">
        <div class="vertical-align-wrap text-center">
            <div class="vertical-align-middle auth-main realestate">
                <div class="auth-box d-inline-block m-0">
                    <div class="card">
                        <div class="header">
                            <div class="top d-inline-flex justify-content-center m-0">
                                <Link href="/">
                                    <img src="/assets/images/fscs-logo.png" width="100%" alt="Logo">
                                </Link>
                            </div>
                        </div>
                        <div class="body">
                            <slot />
                        </div>
                    </div>
                </div>
            </div>
        </div>    
    </div>

</template>
