<script setup>
import { ref, onMounted, computed, watch, onUnmounted } from 'vue';
import { useToast } from 'vue-toastification';
import axios from 'axios';
import { Link, usePage, router } from '@inertiajs/vue3';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SidebarGroup from '@/Components/SidebarGroup.vue'
import pusher from '@/Services/pusher';

const { props } = usePage();
const currentUserId = props.auth.user.id;

const isSidebarCollapsed = ref(false)
const isOffcanvasActive = ref(false)

const applyLayoutClasses = () => {
    if (typeof document === 'undefined') return
    document.body.classList.toggle('layout-fullwidth', isSidebarCollapsed.value)
    document.body.classList.toggle('offcanvas-active', isOffcanvasActive.value)
}

const toggleSidebar = (mode = 'auto') => {
    const viewportWidth = typeof window !== 'undefined' ? window.innerWidth : 0

    if (mode === 'offcanvas' || (mode === 'auto' && viewportWidth <= 1280)) {
        isOffcanvasActive.value = !isOffcanvasActive.value
    } else {
        isSidebarCollapsed.value = !isSidebarCollapsed.value
    }

    applyLayoutClasses()
}

const closeOffcanvas = () => {
    if (!isOffcanvasActive.value) return
    isOffcanvasActive.value = false
    applyLayoutClasses()
}

let resizeHandler;

let channel;
onMounted(() => {
    channel = pusher.subscribe('file-upload');
    channel.bind('App\\Events\\FileUploaded', (data) => {
        if (data.user_id === currentUserId) {
            toast.success(`${data.message}`);
        }
    });
});
onUnmounted(() => {
    if (channel) {
        pusher.unsubscribe('file-upload');
    }
});

const toast = useToast()
const page = usePage()
const campusList = ref([])
const isHeadOfficeDomain = ref()
const selectedCampus = ref(null)
const containreateOrEditWord = ref(0)
const userPermissions = page.props.auth.user.user_permissions

onMounted(async () => {
    try {
        const currentUrl = window.location.href
        const isCreate = currentUrl.includes('create')
        const isCredit = currentUrl.includes('credit')
        let isEdit = false;
        if(isCredit == false){
             isEdit = currentUrl.includes('edit')
        }
        if(isCreate || isEdit){
            containreateOrEditWord.value = 1
        }

        const fullDomain = window.location.hostname; 
        isHeadOfficeDomain.value = fullDomain.split('.')[0]; 
        const response = await axios.get('/campus-data')
        campusList.value = response.data.getCampusList
        selectedCampus.value = response.data.previousSelectedCampusId || null
    } catch (error) {
        console.error('Failed to load campus:', error)
    }
})

onMounted(() => {
    applyLayoutClasses()
    if (typeof window === 'undefined') return

    resizeHandler = () => {
        if (window.innerWidth > 1280 && isOffcanvasActive.value) {
            isOffcanvasActive.value = false
        }
        applyLayoutClasses()
    }

    window.addEventListener('resize', resizeHandler)
})

onUnmounted(() => {
    if (typeof window === 'undefined') return
    if (resizeHandler) window.removeEventListener('resize', resizeHandler)
})

const filterMenu = (items) => {
    return items.filter(item => {
        if (item.base) {
            return userPermissions.includes(item.base)
        }
        if (Array.isArray(item.route)) {
            return item.route.some(r => userPermissions.includes(r))
        }
        return userPermissions.includes(item.route)
    })
}

const academicItems = [
    { label: 'Class', base: 'class.index', route: ['class.index', 'class.create'], icon: '' },
    { label: 'Section', base: 'section.index', route: ['section.index', 'section.create'], icon: '' },
    { label: 'Subjects', base: 'subject.index', route: ['subject.index', 'subject.create', 'subject.edit'], icon: '' }
]

const filteredAcademicItems = computed(() => filterMenu(academicItems))

const CommunicationItems = [
    { label: 'SMS Credit', base: 'smscredit.index', route: ['smscredit.index','smscredit.create'], icon: '' },
    { label: 'PhoneBook Group', base: 'phonebookgroup.index', route: ['phonebookgroup.index'], icon: '' },
    { label: 'PhoneBook', base: 'phonebook.index', route: ['phonebook.index','phonebook.create', 'phonebook.edit'], icon: '' },
    { label: 'SMS Log', base: 'smslog.index', route: ['smslog.index'], icon: '' },
    { label: 'SMS', base: 'SendSMS.create', route: ['SendSMS.create'], icon: '' },
]

const filteredCommunicationItems = computed(() => filterMenu(CommunicationItems))

const studentItems = [
    { label: 'Student Inquiry', base: 'inquiry.index', route: ['inquiry.index', 'inquiry.create', 'inquiry.edit', 'student.create','inquiry.detail'], icon: '' },
    { label: 'Students', base: 'student.index', route: ['student.index','student.detail'], icon: '' },
    { label: 'Student Attendance', base: 'attendance.create', route: ['attendance.create'], icon: '' },
    { label: 'Student HomeWork', base: 'homework.index', route: ['homework.index', 'homework.create', 'homework.edit', 'homework.show'], icon: '' },
    { label: 'Promote Student', base: 'promotestudent.index', route: ['promotestudent.index'], icon: '' },
    { label: 'Withdraw Student', base: 'student.withdrawlist', route: ['student.withdrawlist'], icon: '' },
    { label: 'Re-Admission', base: 'student.readmissionlist', route: ['student.readmissionlist'], icon: '' },
]
const filteredStudentItems = computed(() => filterMenu(studentItems))

const hrItems = [
    { label: 'Department', base: 'department.list', route: ['department.list', 'department.create', 'department.edit'], icon: '' },
    { label: 'Designation', base: 'designation.index', route: ['designation.index', 'designation.create', 'designation.edit'], icon: '' },
    { label: 'Staff', base: 'staff.list', route: ['staff.list', 'staff.create', 'staff.edit'], icon: '' },
    { label: 'Staff Attendance', base: 'staff.attendance.list', route: ['staff.attendance.list'], icon: '' },
    { label: 'Assign Class Teacher', base: 'assign.class.teacher.list', route: ['assign.class.teacher.list', 'assign.class.teacher.create'], icon: '' },
    { label: 'Class Timetable', base: 'classtimetable.index', route: ['classtimetable.index', 'classtimetable.create'], icon: '' },
    { label: 'Gazetted Leaves', base: 'gazettedleave.index', route: ['gazettedleave.index', 'gazettedleave.create', 'gazettedleave.edit'], icon: '' },
    { label: 'Fine Deduction', base: 'finededuction.index', route: ['finededuction.index', 'finededuction.create', 'finededuction.edit'], icon: '' },
    { label: 'Miscellaneous Payment', base: 'miscellaneouspayment.index', route: ['miscellaneouspayment.index', 'miscellaneouspayment.create', 'miscellaneouspayment.edit'], icon: '' },
    { label: 'Salary Tax', base: 'salarytax.index', route: ['salarytax.index', 'salarytax.create', 'salarytax.edit'], icon: '' },
    { label: 'Security Refund', base: 'securityrefund.index', route: ['securityrefund.index', 'securityrefund.create', 'securityrefund.edit'], icon: '' },
    { label: 'Leave Request', base: 'leave-request.index', route: ['leave-request.index'], icon: '' },
    { label: 'Security Deduction', base: 'securitydeduction.index', route: ['securitydeduction.index', 'securitydeduction.create', 'securitydeduction.submit', 'securitydeduction.edit', 'securitydeduction.update', 'securitydeduction.delete'], icon: '' },
    { label: 'Late Fine', base: 'latefine.index', route: ['latefine.index','latefine.create','latefine.submit','latefine.edit','latefine.update','latefine.delete'], icon: '' },
    { label: 'Weekly Holiday', base: 'campus-weekly-holiday.index', route: ['campus-weekly-holiday.index','campus-weekly-holiday.create','campus-weekly-holiday.submit','campus-weekly-holiday.edit','campus-weekly-holiday.update','campus-weekly-holiday.delete'], icon: '' },
    { label: 'Payroll', base: 'payrollslip.show', route: ['payrollslip.show','payrollslip.create','payrollslip.store','payrollslip.detail','payrollslip.index'], icon: '' },


]
const filteredHrItems = computed(() => filterMenu(hrItems))

const feeItems = [
    { label: 'Fees Type', base: 'fees.list', route: ['fees.list', 'fees.create', 'fees.edit'], icon: '' },
    { label: 'Campus Fees Master', base: 'feemaster.list', route: ['feemaster.list', 'feemaster.create', 'feemaster.submit'], icon: '' },
    { label: 'Optional Fees Mapping', base: 'optionalfee.list', route: ['optionalfee.list', 'optionalfee.create'], icon: '' },
    { label: 'Fee Discount', base: 'discount.list', route: ['discount.list', 'discount.create'], icon: '' },
    { label: 'Fee Collection', base: 'fee.collection.list', route: ['fee.collection.list', 'fee.collection', 'fee.challan.detail'], icon: '' },
    { label: 'Generate Challan', base: 'challan.list', route: ['challan.list'], icon: '' },
    { label: 'Fine Type', base: 'setting.index', route: ['setting.index', 'setting.create'], icon: '' },
    { label: 'Delete Challan', base: 'deletechallan.list', route: ['deletechallan.list'], icon: '' }
]
const filteredFeeItems = computed(() => filterMenu(feeItems))

const examItems = [
    { label: 'Exam Terms', base: 'examterm.index', route: ['examterm.index', 'examterm.create'], icon: '' },
    { label: 'Exam', base: 'examtype.index', route: ['examtype.index', 'examtype.create', 'examtype.edit'], icon: '' },
    { label: 'Exam subject', base: 'examsubject.index', route: ['examsubject.index', 'examsubject.create', 'examsubject.edit'], icon: '' },
    { label: 'Exam Student', base: 'examstudent.index', route: ['examstudent.index', 'examstudent.create', 'examstudent.edit'], icon: '' },
    { label: 'Exam Marks', base: 'marks.index', route: ['marks.index', 'marks.create', 'marks.edit'], icon: '' },
    { label: 'Exam Grade', base: 'marksgrade.list', route: ['marksgrade.list'], icon: '' },
]
const filteredExamItems = computed(() => filterMenu(examItems))

const settingsItems = [
    { label: 'Roles', base: 'role.index', route: ['role.index', 'role.create', 'role.edit', 'role.permission.assign'], icon: '' },
    { label: 'Campus', base: 'campus.index', route: ['campus.index', 'campus.create', 'campus.edit'], icon: '' },
    { label: 'Users', base: 'user.index', route: ['user.index', 'user.create', 'user.edit'], icon: '' },
    { label: 'Sessions', base: 'lmssessions.index', route: ['lmssessions.index', 'lmssessions.create', 'lmssessions.edit'], icon: '' },
    { label: 'Zones', base: 'zone.index', route: ['zone.index', 'zone.create'], icon: '' },
]
const filteredSettingsItems = computed(() => filterMenu(settingsItems))

// const changeCampus = () => {
//     if (selectedCampus.value) {
//         router.post('/switch-campus', { campus_id: selectedCampus.value })
//     }
// }

const changeCampus = () => {
    if (selectedCampus.value) {
        const isDashboard = window.location.href.includes('dashboard');
        router.post('/switch-campus', 
            { campus_id: selectedCampus.value },
            {
                onSuccess: () => {
                    if (isDashboard) {
                        window.location.reload();
                    }
                }
            }
        );
    }
}

watch(() => page.props.toast, (toastData) => {
    if (toastData?.message) {
        toast[toastData.type || 'info'](toastData.message)
    }
},
    { immediate: true }
)
</script>

<template>
    <div id="wrapper" class="theme-cyan">

        <nav class="navbar navbar-fixed-top border-bottom">
            <div class="container-fluid">
                <div class="navbar-brand">
                    <button type="button" class="btn-toggle-offcanvas" @click="toggleSidebar('offcanvas')">
                        <i class="fa fa-bars"></i>
                    </button>
                    <button type="button" class="btn-toggle-fullwidth" @click="toggleSidebar('fullwidth')">
                        <i class="fa fa-bars"></i>
                    </button>
                    <Link style="margin-left: 20px;" href="#">FSCS</Link>
                </div>
                <div class="col-md-9">

                </div>
                <div class="col-md-2" v-if="isHeadOfficeDomain === 'headoffice'">
                    <InputLabel for="Switch-Campus" value="Switch Campus" />
                    <select class="form-control" id="Switch-Campus" name="Switch-Campus" v-model="selectedCampus" @change="changeCampus">
                        <option disabled value="">Select Campus</option>
                        <option :disabled="containreateOrEditWord == 1" v-for="campus in campusList" :key="campus.id" :value="campus.id">
                            {{ campus.DomainName }}
                        </option>
                    </select>
                </div>
                <div class="navbar-right">
                    <div id="navbar-menu">
                        <ul class="nav navbar-nav">
                            <li>
                                <!-- <Link style="background-color: #fcfcfc; border: none;" :href="route('logout')" method="post" class="icon-menu"><i class="fa fa-power-off"></i></Link> -->
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- main left menu -->
        <div id="left-sidebar" class="sidebar" :class="{ open: isOffcanvasActive }">
            <button type="button" class="btn-toggle-offcanvas" @click="closeOffcanvas">
                <i class="fa fa-arrow-left"></i>
            </button>
            <div class="sidebar-scroll">
                <div class="user-account">
                    <img src="/assets/images/Logo.jpeg" class="rounded-circle user-photo" alt="User Profile Picture">
                    <div class="dropdown">
                        <span>Welcome,</span>
                        <a href="javascript:void(0);" class="dropdown-toggle user-name" data-toggle="dropdown">
                            <strong class="text-capitalize">{{ $page.props.auth.user.name }}</strong>
                        </a>
                        <ul class="p-2 dropdown-menu dropdown-menu-right account cstm_account">
                            <DropdownLink :href="route('profile.edit')">
                                <i class="fa fa-user"></i>
                                <span> Profile</span>
                            </DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">
                                <i class="fa fa-power-off"></i>
                                <span> Log Out</span>
                            </DropdownLink>
                        </ul>
                    </div>
                    <hr>
                </div>

                <div class="tab-content padding-0">
                    <nav id="left-sidebar-nav" class="sidebar-nav">
                        <ul class="metismenu">
                            <li :class="{ active: route().current('dashboard') }">
                                <ResponsiveNavLink :href="route('dashboard')"
                                    :class="{ active: route().current('dashboard') }">
                                    <i class="fa fa-dashboard"></i>
                                    <span> Dashboard</span>
                                </ResponsiveNavLink>
                            </li>
                            <SidebarGroup v-if="filteredHrItems.length" label="Human Resources" icon="fa fa-sitemap"
                                :items="filteredHrItems" />

                            <SidebarGroup v-if="filteredAcademicItems.length" label="Academic" icon="fa fa-university"
                                :items="filteredAcademicItems" />

                            <SidebarGroup v-if="filteredStudentItems.length" label="Students" icon="fa fa-users"
                                :items="filteredStudentItems" />

                            <SidebarGroup v-if="filteredFeeItems.length" label="Fees" icon="fa fa-money"
                                :items="filteredFeeItems" />

                            <SidebarGroup v-if="filteredExamItems.length" label="Exam" icon="fa fa-graduation-cap"
                                :items="filteredExamItems" />
                            
                            <SidebarGroup v-if="filteredCommunicationItems.length" label="Communication" icon="fa fa-book"
                                :items="filteredCommunicationItems" />

                            <SidebarGroup v-if="isHeadOfficeDomain === 'headoffice'" label="Downloads"
                                icon="fa fa-sitemap" :items="[
                                    { label: 'Content Group', route: ['content.index', 'content.create', 'content.edit'], icon: '' },
                                    { label: 'Content Approvals', route: ['uploads.approval'], icon: '' },
                                    { label: 'Content Upload Specialist', route: ['uploads.specialistlist'], icon: '' },
                                    { label: 'Upload Content', route: ['uploads.index', 'uploads.create'], icon: '' },
                                    { label: 'Downloaded Logs', route: ['downloaded.logs'], icon: '' },
                                ]" />
                                
                            <SidebarGroup v-if="isHeadOfficeDomain !== 'headoffice'" label="Downloads"
                                icon="fa fa-sitemap" :items="[
                                    { label: 'Upload Content', route: ['uploads.index', 'uploads.create'], icon: '' },
                                ]" />

                            <SidebarGroup v-if="filteredSettingsItems.length" label="System Settings" icon="fa fa-cogs"
                                :items="filteredSettingsItems" />
                           
                            <SidebarGroup label="Reports" icon="fa fa-line-chart ftlayer"
                                :items="[
                                    { label: 'Result Sheet', route: ['result.sheet','result.card'], icon: '' },
                                    { label: 'Master Report', route: ['masterreport.list'], icon: '' },
                                    { label: 'Student Information', route: ['student.information'], icon: '' },
                                    { label: 'Monthly Fee Report', route: ['fee.collectionreport'], icon: '' },
                                    { label: 'Fee Collection Summary', route: ['fee.collectionsummary'], icon: '' },
                                    { label: 'Daily Fee Collection', route: ['fee.dailycollection'], icon: '' },
                                    { label: 'Student Balance', route: ['student.feebalance'], icon: '' },
                                    { label: 'Sibling Report', route: ['sibling.report'], icon: '' },
                                ]"
                            />

                            <!-- v-if="props.auth.user.email == 'r.umar083@gmail.com'" -->
                            <!-- <SidebarGroup v-if="props.auth.user.email == 'r.umar083@gmail.com'"  label="Import Forces Data" icon="fa fa-download"
                                :items="[
                                    { label: 'Import Api Data', route: ['get.all.api.list'], icon: '' },
                                ]"
                            /> -->
  
                        </ul>
                    </nav>
                </div>
            </div>
        </div>


        <header class="bg-white" v-if="$slots.header">
            <div class="">
                <slot name="header" />
            </div>
        </header>
        <!-- mani page content body part -->
        <div id="main-content">
            <main>
                <slot />
            </main>
        </div>

    </div>
</template>
