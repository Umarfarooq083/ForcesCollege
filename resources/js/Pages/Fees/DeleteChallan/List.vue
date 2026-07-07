<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TableHeader from '@/Components/TableHeader.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { ref  } from 'vue';
import 'vue-multiselect/dist/vue-multiselect.min.css';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

const generatedChallans = ref();
const ChallansStatus = ref(null);
const form = useForm({
    ChallanId: '',
});

const props = defineProps({
    errors: Object
})

const FetchChallan = async () => {
    // form.reset();
     const response = await axios.get(route('challan.list.fetch', {id: form.ChallanId}));
     generatedChallans.value = response.data;
}

const deleteChallan = async (id) => {
    try {
        const response = await axios.post(
            route('challan.delete', { id }), 
            {} 
        );
        ChallansStatus.value = 'Challan deleted successfuly';
        form.reset();
        generatedChallans.value = null
        FetchChallan();
    } catch (error) {
        ChallansStatus.value = error.response.data.message;
    }
};

const columns = [
  { label: 'Active' },
  { label: 'Challan No' },
  { label: 'Class' },
  { label: 'Section' },
  { label: 'Student' },
  { label: 'Challan Month' },
  { label: 'Due Date' },
  { label: 'Status' },
  { label: 'Action' },
];

</script>
<template>
    <Head title="Challan No" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Challan
            </h2>
        </template>
       
    
         <form>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="body">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <InputLabel value="Challan No" />
                                        <TextInput  type="number" v-model="form.ChallanId" class="form-control" />
                                        <InputError class="mt-2" :message="errors.ChallanId" />
                                        <!-- <p style="color: red; font-size: 18px;" class="mt-2 text-green-600" v-if="ChallansStatus" >{{ ChallansStatus }}</p> -->
                                    </div>
                                </div>

                                 <div class="col-md-2 mt-4">
                                    <a v-if="$page.props.auth.user.user_permissions.indexOf('challan.list.fetch') > -1" class="btn" style="background-color: #59c4bc; color: #fff; margin-top: 5px;" @click="FetchChallan">Search Challan</a>
                                </div>

                            </div>
                            
                        </div>
                    </div>
                </div>
              
            </div>
        </form>
        <p style="color: red; font-size: 18px;" class="mt-2 text-green-600" v-if="ChallansStatus" >{{ ChallansStatus }}</p>
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Challan</div>
                </div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table mb-0 text-center table-hover c_list table-bordered">
                        <TableHeader :columns="columns" />
                        <tbody>
                            <tr v-if="generatedChallans">
                                <td>{{ generatedChallans.IsActive == 1 ? 'Active' : 'Inactive' }}</td>
                                <td>{{ generatedChallans.challan_no }}</td>
                                <td>{{ generatedChallans.student_rel.class.ClassName }}</td>
                                <td>{{ generatedChallans.student_rel.section.SectionName }}</td>
                                <td>{{ generatedChallans.student_rel.FirstName }}</td>
                                <td>{{ generatedChallans.ChallanMonth }}</td>
                                <td>{{ generatedChallans.DueDate }}</td>
                                <td>{{ generatedChallans.Status }}</td>
                                <td>
                                    <!-- :href="route('challan.delete',{id:generatedChallans.id})" -->
                                    <a v-if="$page.props.auth.user.user_permissions.indexOf('challan.delete') > -1"  @click="deleteChallan(generatedChallans.id)" type="button" 
                                        class="btn btn-danger btn-sm" title="Delete"><i style="color: #fff" class="fa fa-trash"></i></a>
                                </td>
                              
                            </tr>
                            <tr style="font-size: 20px; color: red;" v-else>
                                <td colspan="9" class="text-center">No Challan Found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </AuthenticatedLayout>
</template>
