<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    campus: Object,
    zones: Object,
    regions: Object,
});

const form = useForm({
    id: props.campus.id,
    SessionId:props.campus.SessionId,
    zoneid:props.campus.zoneid,
    regionid: props.campus.regionid,
    OwnerName: props.campus.OwnerName,
    SchoolName: props.campus.SchoolName,
    Address: props.campus.Address,
    PhoneNo: props.campus.PhoneNo,
    OfficePhone: props.campus.OfficePhone,
    MobileNo: props.campus.MobileNo,
    Area: props.campus.Area,
    Rooms: props.campus.Rooms,
    City: props.campus.City,
    TotalFaculty:props.campus.TotalFaculty,
    Rental: props.campus.Rental,
    ContractDuration: props.campus.ContractDuration,
    Comments: props.campus.Comments,
    Other: props.campus.Other,
    AgreementPath: props.campus.AgreementPath,
    URL: props.campus.URL,
    Code: props.campus.Code,
    AccountNo: props.campus.AccountNo,
    AccountTitle: props.campus.AccountTitle,
    bankName: props.campus.bankName,
    BranchCode: props.campus.BranchCode,
    IsAvailableForMobApp: props.campus.IsAvailableForMobApp,
    SortOrder: props.campus.SortOrder,
});
</script>

<template>

    <Head title="Update Campus" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Update Campus</h2>
        </template>
        
        <form @submit.prevent="form.put(route('campus.update'))" >
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="header">General Information</div>
                        <div class="body">
                            <div class="row">
                               <TextInput type="hidden" v-model="form.id" class="form-control" />
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <InputLabel for="OwnerName" value="Owner Name" />
                                        <TextInput id="OwnerName" type="text" v-model="form.OwnerName" class="form-control" />
                                         <InputError class="mt-2" :message="form.errors.OwnerName" />
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <InputLabel for="SchoolName" value="School Name" />
                                        <TextInput id="SchoolName" type="text" v-model="form.SchoolName" class="form-control" />
                                         <InputError class="mt-2" :message="form.errors.SchoolName" />
                                    </div>
                                </div>
                               
                             
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="Code" value="Campus Code" />
                                        <TextInput id="Code" type="text" v-model="form.Code" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="Zone" value="Zone" />
                                        <select class="form-control" v-model="form.zoneid">
                                            <option selected disabled value="">Select a Zone</option>
                                            <option v-for="zone in zones" :key="zone.id" :value="zone.id">
                                                {{ zone.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="Region" value="Region" />
                                        <select class="form-control" v-model="form.regionid">
                                            <option selected disabled value="">Select a Region</option>
                                            <option v-for="region in regions" :key="region.id" :value="region.id">
                                                {{ region.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="header">Contact Information</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="Address" value="Address" />
                                        <TextInput id="Address" type="text" v-model="form.Address" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.Address" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="City" value="City" />
                                        <TextInput id="City" type="text" v-model="form.City" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.City" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="PhoneNo" value="Phone No" />
                                        <TextInput id="PhoneNo" type="text" inputmode="numeric" pattern="[0-9]*" v-model="form.PhoneNo" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.PhoneNo" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="OfficePhone" value="Office Phone" />
                                        <TextInput id="OfficePhone" inputmode="numeric" pattern="[0-9]*" type="text" v-model="form.OfficePhone" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.OfficePhone" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="MobileNo" value="Mobile No" />
                                        <TextInput id="MobileNo" inputmode="numeric" pattern="[0-9]*" type="text" v-model="form.MobileNo" class="form-control" />
                                         <InputError class="mt-2" :message="form.errors.MobileNo" />
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="URL" value="Website URL" />
                                        <TextInput id="URL" type="url" v-model="form.URL" class="form-control" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                  <div class="col-md-6">
                    <div class="card">
                        <div class="header">Operational Info</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <InputLabel for="Area" value="Area" />
                                        <TextInput id="Area" type="text" v-model="form.Area" class="form-control" />
                                    </div>
                                </div>
                                  <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="AgreementPath" value="Agreement Path" />
                                        <TextInput id="AgreementPath" type="text" v-model="form.AgreementPath" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="BankName" value="Bank Name" />
                                        <TextInput id="BankName" type="text" v-model="form.bankName" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="AccountNo" value="Account Title" />
                                        <TextInput id="AccountNo" type="text" v-model="form.AccountTitle" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="AccountNo" value="Account No" />
                                        <TextInput id="AccountNo" type="text" v-model="form.AccountNo" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="Rooms" value="No. of Rooms" />
                                        <TextInput id="Rooms" inputmode="numeric" pattern="[0-9]*" type="number" v-model="form.Rooms" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="TotalFaculty" value="Total Faculty" />
                                        <TextInput id="TotalFaculty" inputmode="numeric" pattern="[0-9]*" type="number" v-model="form.TotalFaculty" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="Rental" value="Rental" />
                                        <TextInput id="Rental" inputmode="numeric" pattern="[0-9]*" type="number" v-model="form.Rental" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="ContractDuration" value="Contract Duration (Months)" />
                                        <TextInput id="ContractDuration" inputmode="numeric" pattern="[0-9]*" type="number" v-model="form.ContractDuration" class="form-control" />
                                    </div>
                                </div>
                              
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="BranchCode" value="Branch Code" />
                                        <TextInput id="BranchCode" type="text" v-model="form.BranchCode" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="SessionId" value="Session ID" />
                                        <TextInput id="SessionId" type="number" v-model="form.SessionId" class="form-control" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row"> 
                        <div class="col-md-12">
                            <div class="card">
                                <div class="header">Status & Availability</div>
                                <div class="body">
                                
                                    <div class="flex items-center mt-2">
                                        <input id="IsAvailableForMobApp" type="checkbox" v-model="form.IsAvailableForMobApp"
                                            class="mr-2" />
                                        <InputLabel for="IsAvailableForMobApp" value="Available on Mobile App" class="mb-0" />
                                    </div>
                                    <div>
                                        <InputLabel for="SortOrder" value="Sort Order" />
                                        <TextInput id="SortOrder" type="number" v-model="form.SortOrder" class="form-control" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card">
                        <div class="header">Additional Info</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="Comments" value="Comments" />
                                        <textarea id="Comments" rows="2" v-model="form.Comments" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="Other" value="Other" />
                                        <textarea id="Other" rows="2" v-model="form.Other" class="form-control"></textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="w-100 d-inline-block">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-sm"> Submit</button>
                                    </div>
                                </div>
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
