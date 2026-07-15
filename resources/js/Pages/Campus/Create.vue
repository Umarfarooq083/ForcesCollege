<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    zones: Array,
    regions: Array,
    campus_categories: Array,
    role_exist: Object,
})

const form = useForm({
    IsActive: true,
    IsDeleted: false,
    zoneid: '',
    regionid: '',
    campus_category_id: '',
    OwnerName: '',
    SchoolName: '',
    Address: '',
    PhoneNo: '',
    OfficePhone: '',
    MobileNo: '',
    Area: '',
    Rooms: '',
    City: '',
    EmailAddress: '',
    TotalFaculty: '',
    Rental: '',
    ContractDuration: '',
    Comments: '',
    Other: '',
    AgreementPath: '',
    URL: '',
    Code: '',
    AccountNo: '',
    AccountTitle: '',
    bankName: '',
    BranchCode: '',
    DomainName: '',
    Logo: '',
    IsAvailableForMobApp: false,
    SortOrder: 0,
});
</script>

<template>

    <Head title="Create Campus" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Campus</h2>
        </template>
        <h5 v-if="!role_exist" style="color: red; text-align: center; border: 1px solid gray; padding: 17px;">
            Role does not exist. Please create the Campus Admin role first, then create the campus.
        </h5>
        <form @submit.prevent="form.post(route('campus.submit'))">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="header">General Information</div>
                        <div class="body">
                            <div class="row">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <InputLabel for="OwnerName" value="Owner Name" />
                                        <TextInput id="OwnerName" type="text" v-model="form.OwnerName"
                                            class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.OwnerName" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="SchoolName" value="School Name" /> <span
                                            class="text-danger font-12 position-absolute">★</span>
                                        <TextInput id="SchoolName" type="text" v-model="form.SchoolName"
                                            class="form-control" />
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
                                        <InputLabel value="Zone" /> <span
                                            class="text-danger font-12 position-absolute">★</span>
                                        <select v-model="form.zoneid" class="form-control">
                                            <option value="">Select Zone</option>
                                            <option v-for="zone in props.zones" :key="zone.id" :value="zone.id">
                                                {{ zone.name }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.zoneid" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Region" />
                                        <select v-model="form.regionid" class="form-control">
                                            <option value="">Select Region</option>
                                            <option v-for="region in props.regions" :key="region.id" :value="region.id">
                                                {{ region.name }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.regionid" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Campus Category" />
                                        <select v-model="form.campus_category_id" class="form-control">
                                            <option value="">Select Category</option>
                                            <option v-for="category in props.campus_categories" :key="category.id"
                                                :value="category.id">
                                                {{ category.CategoryName }}
                                            </option>
                                        </select>
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.campus_category_id" />
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
                                        <InputLabel for="Address" value="Address" /> <span
                                            class="text-danger font-12 position-absolute">★</span>
                                        <TextInput id="Address" type="text" v-model="form.Address"
                                            class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.Address" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="City" value="City" /> <span
                                            class="text-danger font-12 position-absolute">★</span>
                                        <TextInput id="City" type="text" v-model="form.City" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.City" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="PhoneNo" value="Phone No" /> <span
                                            class="text-danger font-12 position-absolute">★</span>
                                        <TextInput type="text" inputmode="numeric" pattern="[0-9]*"
                                            v-model="form.PhoneNo" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.PhoneNo" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="OfficePhone" value="Office Phone" />
                                        <TextInput type="text" inputmode="numeric" pattern="[0-9]*"
                                            v-model="form.OfficePhone" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.OfficePhone" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="MobileNo" value="Mobile No" />
                                        <TextInput type="text" inputmode="numeric" pattern="[0-9]*"
                                            v-model="form.MobileNo" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.MobileNo" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="EmailAddress" value="Email Address" /> <span
                                            class="text-danger font-12 position-absolute">★</span>
                                        <TextInput id="EmailAddress" type="email" v-model="form.EmailAddress"
                                            class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.EmailAddress" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="URL" value="Website URL" />
                                        <TextInput id="URL" type="url" v-model="form.URL" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="DomainName" value="Domain Name" /> <span
                                            class="text-danger font-12 position-absolute">★</span>
                                        <TextInput id="DomainName" type="text" v-model="form.DomainName"
                                            class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.DomainName" />
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
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <InputLabel for="AgreementPath" value="Agreement Path" />
                                        <TextInput id="AgreementPath" type="text" v-model="form.AgreementPath"
                                            class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="AccountNo" value="Bank " />
                                        <TextInput id="AccountNo" type="text" v-model="form.bankName"
                                            class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="AccountNo" value="Account Title" />
                                        <TextInput id="AccountNo" type="text" v-model="form.AccountTitle"
                                            class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="AccountNo" value="Account No" />
                                        <TextInput id="AccountNo" type="text" v-model="form.AccountNo"
                                            class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="Rooms" value="No. of Rooms" />
                                        <TextInput id="Rooms" inputmode="numeric" pattern="[0-9]*" type="number"
                                            v-model="form.Rooms" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="TotalFaculty" value="Total Faculty" />
                                        <TextInput id="TotalFaculty" inputmode="numeric" pattern="[0-9]*" type="number"
                                            v-model="form.TotalFaculty" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="Rental" value="Rental" />
                                        <TextInput id="Rental" inputmode="numeric" pattern="[0-9]*" type="number"
                                            v-model="form.Rental" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="ContractDuration" value="Contract Duration (Months)" />
                                        <TextInput id="ContractDuration" type="number" v-model="form.ContractDuration"
                                            class="form-control" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel for="BranchCode" value="Branch Code" />
                                        <TextInput id="BranchCode" type="text" v-model="form.BranchCode"
                                            class="form-control" />
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
                                        <input id="IsAvailableForMobApp" type="checkbox"
                                            v-model="form.IsAvailableForMobApp" class="mr-2" />
                                        <InputLabel for="IsAvailableForMobApp" value="Available on Mobile App"
                                            class="mb-0" />
                                    </div>
                                    <div>
                                        <InputLabel for="SortOrder" value="Sort Order" />
                                        <TextInput id="SortOrder" type="number" v-model="form.SortOrder"
                                            class="form-control" />
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
                                                <textarea id="Comments" rows="2" v-model="form.Comments"
                                                    class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <InputLabel for="Other" value="Other" />
                                                <textarea id="Other" rows="2" v-model="form.Other"
                                                    class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <InputLabel for="Logo" value="Logo Path" />
                                                <TextInput id="Logo" type="text" v-model="form.Logo"
                                                    class="form-control" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="w-100 d-inline-block">&nbsp;</label>
                                                <button v-if="role_exist" type="submit" class="btn btn-primary btn-sm">
                                                    Submit</button>
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
