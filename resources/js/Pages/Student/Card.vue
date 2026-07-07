<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, Head } from '@inertiajs/vue3';
import 'vue-multiselect/dist/vue-multiselect.min.css'


const props = defineProps({
    student: Object,
});

</script>

<template>

    <Head title="Student Inquiry" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Student ID Card</h2>
        </template>
        <div class="container text-center">
            <div class="wrap">
                <div>
                    <a 
                        v-if="$page.props.auth.user.user_permissions.indexOf('student.card.pdf') > -1"
                        :href="route('student.card.pdf', {id:student.id})"
                        class="btn btn-success btn-sm"
                        title="Student Card" target="_blank" rel="noopener noreferrer">
                        Student Card <i class="fa fa-print"></i>
                    </a>
                </div>
                <div class="card_wrapper">
                    <div class="card">
                        <div class="card-header cd_h_wrapper">
                            <div class="cd_h">
                                <div class="cdh_logo">
                                    <img src="/assets/images/Logo.jpeg" width="40px" height="40px"
                                        alt="Forces School & College System">
                                </div>
                                <div class="cdh_txt">FORCES SCHOOL & COLLEGE SYSTEM</div>
                            </div>
                        </div>
                        <div class="card-body cd_b_wrapper">
                            <div class="cd_b">
                                <div class="cdb_details">
                                    <div class="mb-2 name">{{ student?.FirstName }} {{ student?.LastName }}</div>
                                    <div class="std_details">
                                        <div class="std_heading">Roll No:</div>
                                        <div class="std_txt">{{ student?.RollNumber }}</div>
                                    </div>
                                    <div class="std_details">
                                        <div class="std_heading">Class:</div>
                                        <div class="std_txt">{{ student?.class?.ClassName }}</div>
                                    </div>
                                    <div class="std_details">
                                        <div class="std_heading">Section:</div>
                                        <div class="std_txt">{{ student?.section?.SectionName }}</div>
                                    </div>
                                </div>
                                <div class="cdb_img">
                                    <div class="{{imagespaceclass}}">
                                        <img  :src=" student?.StudentPhotoPath ? `/storage/${student?.StudentPhotoPath}` : '/assets/images/staff_profile.jpg'"  width="40px" height="40px">
                                    </div>
                                    <div class="ppl_sign">
                                        <div class="">Principal:</div>
                                        <div class="border_bottom"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card_wrapper1 break-before">
                    <div class="card">
                        <div class="card-body cd_b_wrapper">
                            <div class="cd_b">
                                <div class="cdb_details">
                                    <div class="std_details">
                                        <div class="std_heading">Father name:</div>
                                        <div class="std_txt">{{ student?.FatherName }}</div>
                                    </div>
                                    <div class="std_details">
                                        <div class="std_heading">Address:</div>
                                        <div class="std_txt">{{ student?.Is_Guardian == 1 ? student?.GuardianAddress : student?.CurrentAddress }}</div>
                                    </div>
                                    <div class="std_details">
                                        <div class="std_heading">Contact#:</div> 
                                        <div class="std_txt">{{ student?.FatherPhone }}</div>
                                    </div>
                                    <div class="std_details">
                                        <div class="std_heading">Emergancy#:</div>
                                        <div class="std_txt">{{ student?.Is_Guardian == 1 ? student?.GuardianPhone : student?.FatherPhone }}</div>
                                    </div>
                                    <div class="std_details">
                                        <div class="std_heading">Date of Birth:</div>
                                        <div class="std_txt">{{ student?.DateOfBirth }}</div>
                                    </div>
                                    <div class="std_details">
                                        <div class="std_heading">BloodGroup:</div>
                                        <div class="std_txt">{{ student?.BloodGroup }}</div>
                                    </div>
                                    <div class="std_details">
                                        <div class="std_heading">Issue Date:</div>
                                        <div class="std_txt">{{ new Date(student?.created_at).toLocaleDateString() }}</div>
                                    </div>
                                    <div class="std_details">
                                        <div class="std_heading">Expiry Date:</div>
                                        <div class="std_txt">{{ student?.ExpireDate }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
body {
    -webkit-print-color-adjust: exact;
}

.main_wrap {
    width: 100%;
    display: inline-flex;
    flex-direction: column;
    gap: 50px;
    justify-content: center;
    -webkit-print-color-adjust: exact;
}

.card_wrapper,
.card_wrapper1 {
    width: 100%;
    max-width: 500px;
    height: 300px;
    overflow: hidden;
    background: #fff;
    border: 3px solid #58666e;
    border-bottom: 5px solid #279121;
    -webkit-print-color-adjust: exact;
}

.card_wrapper1 {
    border-bottom: 3px solid #58666e;
}


.wrap .card,
.wrap .card-header,
.wrap .card-body {
    border-radius: 0;
    border: 0;
}

.cd_h_wrapper {
    padding: 5px 10px;
    background: #7266ba;
    border-bottom: 6px solid #279121 !important;
}

.cd_h {
    display: inline-flex;
    justify-content: flex-start;
    align-items: center;
    width: 100%;
    gap: 10px;
    color: #fff;
    font-weight: 600;
    font-size: 24px;
}

.cd_b_wrapper {
    color: #58666e;
    padding: 25px 5px;
}

.cd_b .name {
    width: 100%;
    font-size: 25px;
    font-weight: bold;
    text-transform: uppercase;
}

.cd_b {
    text-align: left;
    width: 100%;
    display: inline-flex;
    align-items: flex-start;
}


.cdb_details {
    width: 70%;
}

.card_wrapper1 .cdb_details {
    width: 100%;
}

.cdb_img {
    width: 30%;
}

.img_space {
    border: 1px solid gray;
    min-height: 130px;
    width: 100%;
}

.img_space_wd {
    min-height: 130px;
    width: 100%;
}

.std_details {
    display: inline-flex;
    width: 100%;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.std_details:last-child {
    margin-bottom: 0;
}

.std_details .std_heading {
    width: 30%;
    font-weight: bold;
}

.card_wrapper1 .std_details .std_heading {
    width: 50%;
    font-weight: bold;
}

.std_details .std_txt {
    text-align: left;
    width: 70%;
}

.card_wrapper1 .std_details .std_txt {
    text-align: left;
    width: 60%;
}

.ppl_sign {
    width: 100%;
    display: inline-flex;
    justify-content: flex-end;
    align-items: baseline;
    gap: 3px;
    font-size: 8px;
    margin-top: 2.2rem;
}

.ppl_sign div:first-child {
    width: 33%;
    text-align: left;
}

.ppl_sign .border_bottom {
    width: 67%;
    text-align: left;
    border-bottom: 1px dashed #58666e;
}

.break-before {
    page-break-before: always;
}

.card_wrapper1 {
    margin-top: 50px;
}

.wrap {
    width: 100%;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

@media print {
    .wrap {
        display: unset !important;
        -webkit-print-color-adjust: exact;
    }

    .card_wrapper,
    .card_wrapper1 {
        margin-left: 245px;
        -webkit-print-color-adjust: exact;
    }

    .card_wrapper1 {
        margin-top: 0px !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>
