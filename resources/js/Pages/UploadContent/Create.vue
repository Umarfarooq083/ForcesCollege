<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';
import { ref,computed } from 'vue';
import 'vue-multiselect/dist/vue-multiselect.min.css'
import Multiselect from 'vue-multiselect'
import Checkbox from '@/Components/Checkbox.vue';   
import 'vue-multiselect/dist/vue-multiselect.min.css'
import { watch } from 'vue';

const props = defineProps({
    classesList: Object,
    uploadContentGroupList: Object,
    campusList: Object,
    region: Object,
});

// const CampusOption = computed(() => {
//     return props.campusList.map(type => ({
//         id: type.id,
//         name: type.DomainName
//     }));
// });

const CampusOption = computed(() => {
    return [
        SELECT_ALL_OPTION,
        ...props.campusList.map(type => ({
            id: type.id,
            name: type.DomainName
        }))
    ];
});

const RegionOption = computed(() => {
    return props.region.map(type => ({
         id: type.id,
         name: type.name
     }));
});



const form = useForm({
    ContentTitle: '',
    ContentType: '',
    ClassId: '',
    subjectId: '',
    termId: '',
    monthId: '',
    weekId: '',
    ContentFilePath: '',
    UploadDate: new Date().toISOString().split('T')[0],
    Description: '',
    // AvailableForAllCampuses: true,
    regionId: [],
    UploadContentGroupId: '',
    AllowedSchools: [],
});
const classSubject = ref([]);

const fetchClassSubject = async (event) => {
    try {
        const response = await axios.post('fetch-subject', {
            class_id: event
        });
        classSubject.value = response.data;
        form.subjectId = ''
    } catch (error) {
        console.error('Request failed:', error)
    }
}

const SELECT_ALL_OPTION = {
    id: 'all',
    name: 'Select All Campuses'
};

watch(
    () => form.AllowedSchools,
    (selected) => {
        if (!selected) return;
        const hasSelectAll = selected.some(opt => opt.id === 'all');
        if (hasSelectAll) {
            form.AllowedSchools = CampusOption.value.filter(
                opt => opt.id !== 'all'
            );
        }
    },
    { deep: true }
);

</script>

<template>

    <Head title="Upload Content" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Upload Content</h2>
        </template>
        <form @submit.prevent="form.post(route('uploads.submit'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Upload Content</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Content Title *" />
                                        <TextInput type="text" v-model="form.ContentTitle	" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.ContentTitle	" />
                                    </div>
                                </div>

                                  <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Content Type *" />
                                        <!-- there is static content type list is also on content upload create page if you want to add new content type then add in both places -->
                                        <select class="form-control" v-model="form.ContentType">
                                            <option selected disabled value="">Select a Content Type</option>
                                            <option value="Assignments">Assignments</option>
                                            <option value="Study Material">Study Material</option>
                                            <option value="Syllabus">Syllabus</option>
                                            <option value="DLP">DLP</option>
                                            <option value="Exam Paper">Exam Paper</option>
                                            <option value="Datesheet">Datesheet</option>
                                            <option value="Wlps">Wlps</option>
                                            <option value="Scheme of Studies">Scheme of Studies</option>
                                            <option value="Vacation Work">Vacation Work</option>
                                            <option value="Checkpoint">Checkpoint</option>
                                            <option value="Worksheet">Worksheet</option>
                                            <option value="Timetable">Timetable</option>
                                            <option value="Period Allocation">Period Allocation</option>
                                            <option value="Calendars">Calendars</option>
                                            <option value="Circular">Circular</option>
                                            <option value="ICT Lesson">ICT Lesson</option>
                                            <option value="Answer Keys">Answer Keys</option>
                                            <option value="Other Download">Other Download</option>
                                        </select>
                                         <InputError class="mt-2" :message="form.errors.ContentType" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Class" />
                                        <select class="form-control" v-model="form.ClassId" @change.prevent="fetchClassSubject($event.target.value)">
                                            <option selected disabled value="">Select a Class</option>
                                            <option v-for="List in classesList" :key="List.id" :value="List.id">
                                                {{ List?.ClassName }}
                                            </option>
                                        </select>
                                         <InputError class="mt-2" :message="form.errors.ClassId" />
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Subject" />
                                        <select class="form-control" v-model="form.subjectId">
                                            <option selected disabled value="">Select a Subject</option>
                                            <option v-for="subList in classSubject" :key="subList.id" :value="subList.id">
                                                {{ subList.SubjectName }}
                                            </option>
                                        </select>
                                         <InputError class="mt-2" :message="form.errors.subjectId" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Term" />
                                        <select class="form-control" v-model="form.termId">
                                            <option selected disabled value="">Select a Term</option>
                                            <option value="Mid Term"> Mid Term</option>
                                            <option value="Final Term"> Final Term</option>
                                        </select>
                                         <InputError class="mt-2" :message="form.errors.termId" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Month" />
                                        <select class="form-control" v-model="form.monthId">
                                            <option selected disabled value="">Select a Month</option>
                                            <option value="January"> January</option>
                                            <option value="February"> February</option>
                                            <option value="March"> March</option>
                                            <option value="April"> April</option>
                                            <option value="May"> May</option>
                                            <option value="June"> June</option>
                                            <option value="July"> July</option>
                                            <option value="August"> August</option>
                                            <option value="September"> September</option>
                                            <option value="October"> October</option>
                                            <option value="November"> November</option>
                                            <option value="December"> December</option>
                                        </select>
                                         <InputError class="mt-2" :message="form.errors.monthId" />
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Week" />
                                        <select class="form-control" v-model="form.weekId">
                                            <option selected disabled value="">Select a Week</option>
                                            <option value="Week 1"> Week 1</option>
                                            <option value="Week 2"> Week 2</option>
                                            <option value="Week 3"> Week 3</option>
                                            <option value="Week 4"> Week 4</option>
                                            <option value="Week 5"> Week 5</option>
                                        </select>
                                         <InputError class="mt-2" :message="form.errors.weekId" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Upload Content Group *" />
                                        <select class="form-control" v-model="form.UploadContentGroupId">
                                            <option selected disabled value="">Select a Type</option>
                                            <option v-for="contentList in uploadContentGroupList" :key="contentList.id" :value="contentList.id">
                                                {{ contentList.name }}
                                            </option>
                                        </select>
                                         <InputError class="mt-2" :message="form.errors.UploadContentGroupId" />
                                    </div>
                                </div>
                                
                                 <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Content File" />
                                        <TextInput type="file"
                                         @input="form.ContentFilePath = $event.target.files[0]"
                                        class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.ContentFilePath	" />
                                    </div>
                                </div>

                                 <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Upload Date" />
                                        <TextInput type="date" v-model="form.UploadDate" class="form-control" readonly/>
                                        <InputError class="mt-2" :message="form.errors.UploadDate	" />
                                    </div>
                                </div>

                                  <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Region *" />
                                        <Multiselect
                                            :options="RegionOption"
                                            v-model="form.regionId"
                                            :multiple="true"
                                            :track-by="'id'"
                                            :label="'name'"
                                            placeholder="Select regions"
                                            />
                                         <InputError class="mt-2" :message="form.errors.regionId" />
                                    </div>

                                    <!-- <label class="flex items-center">
                                        <Checkbox name="AvailableForAllCampuses" v-model:checked="form.AvailableForAllCampuses" />
                                        <span class="ms-2 text-sm text-gray-600">Available For All Campuses</span>
                                    </label> -->
                                </div>

                                 <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Description" />
                                        <TextInput type="text" v-model="form.Description" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.Description	" />
                                    </div>
                                </div>
                                 <div class="col-md-6">
                                </div>

                                <div class="col-md-6" v-if="form.AvailableForAllCampuses == false">
                                    <div class="mb-3">
                                        <InputLabel value="Campus *" />
                                        <!-- <Multiselect
                                            :options="CampusOption"
                                            v-model="form.AllowedSchools"
                                            :multiple="true"
                                            :track-by="'id'"
                                            :label="'name'"
                                            placeholder="Select roles"
                                            /> -->

                                            <!-- <Multiselect
                                                :options="CampusOption"
                                                v-model="form.AllowedSchools"
                                                :multiple="true"
                                                track-by="id"
                                                label="name"
                                                placeholder="Select campuses"
                                            /> -->


                                         <!-- <InputError class="mt-2" :message="form.errors.AllowedSchools" /> -->
                                    </div>
                                </div>

                                <div class="col-md-6" v-if="form.AvailableForAllCampuses == false">
                                   
                                </div>

                                <div class="col-md-6">
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
