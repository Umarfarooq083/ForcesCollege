<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';
import 'vue-multiselect/dist/vue-multiselect.min.css'
import { ref, watch } from 'vue';
import { useToast } from 'vue-toastification';
const toast = useToast();



const props = defineProps({
    staffList: Object,
    classesList: Object,
})


const form = useForm({
    StaffId: '',
    Day: '',
    rows: [],
    Duration: '',
    DatesArray: []
});


// Days List
const DaysList = [
    { id: 'Monday', name: 'Monday' },
    { id: 'Tuesday', name: 'Tuesday' },
    { id: 'Wednesday', name: 'Wednesday' },
    { id: 'Thursday', name: 'Thursday' },
    { id: 'Friday', name: 'Friday' },
    { id: 'Saturday', name: 'Saturday' },
    { id: 'Sunday', name: 'Sunday' },
];


// String → JS day number map
const daysMap = {
  Sunday: 0,
  Monday: 1,
  Tuesday: 2,
  Wednesday: 3,
  Thursday: 4,
  Friday: 5,
  Saturday: 6,
}

// Function to generate dates based on duration
function generateDates(dayName, duration) 
{
    let dates = []
    let now = new Date()
    let startDate = new Date(now.getFullYear(), now.getMonth(), 1)
    let endDate

    if (duration === 'monthly') {
        endDate = new Date(now.getFullYear(), now.getMonth() + 1, 0)
    } else if (duration === 'biannual') {
        endDate = new Date(now.getFullYear(), now.getMonth() + 6, 0)
    } else if (duration === 'annual') {
        endDate = new Date(now.getFullYear(), 11, 31)
    }

    let selectedDay = daysMap[dayName]

    for (let d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
        if (d.getDay() === selectedDay) {
            dates.push(new Date(d).toLocaleDateString('en-CA'))
        }
    }

    return dates
}

// Watch Day & Duration both
watch(() => [form.Day, form.Duration],
  ([newDay, newDuration]) => {
    if (newDay && newDuration) {
      form.DatesArray = generateDates(newDay, newDuration)
      console.log('Generated Dates:', form.DatesArray)
    }
  }
)

// Days List
const DurationList = [
    { id: 'monthly', name: 'Monthly' },
    { id: 'biannual', name: 'Bi-Annual' },
    { id: 'annual', name: 'Annual' },
];

const rows = ref([]);

const addRow = () => {
    const newRow = {
        ClassId: '',
        SectionId: '',
        SubjectId: '',
        TimeFrom: '',
        TimeTo: '',
        availableSections: [],
        availableSubjects: [],
        _watchInitialized: false,
    };
    
    rows.value.push(newRow);
    updateFormRows();
};

const removeRow = (index) => {
    if (rows.value.length > 1) {
        rows.value.splice(index, 1);
        updateFormRows();
    } 
    else {
        toast.info("At least one row must remain.", {timeout: 3000 });
    }
};

// Helper function to update form.rows with clean data
const updateFormRows = () => {
    form.rows = rows.value.map(row => ({
        ClassId: row.ClassId,
        SectionId: row.SectionId,
        SubjectId: row.SubjectId,
        TimeFrom: row.TimeFrom,
        TimeTo: row.TimeTo,
    }));
};

// Fetch sections and subjects dynamically based on class
const fetchClassRelatedData = async (index) => {
    const classId = rows.value[index].ClassId;
    if (!classId) return;

    try {
        const res = await axios.get(route('classtimetable.sections_subjects', classId));
        rows.value[index].availableSections = res.data.sections;
        rows.value[index].availableSubjects = res.data.subjects;

        // Clear previous selections to prevent conflict
        rows.value[index].SectionId = '';
        rows.value[index].SubjectId = '';
        
        updateFormRows();
    } 
    catch (error) {
        console.error(error);
        toast.error("Failed to fetch class data.", {timeout: 3000});
    }
};

// Watch when ClassId changes per row
watch(
    rows,
    (newVal, oldVal) => {
        newVal.forEach((row, index) => {
            if (row._watchInitialized) return;

            watch(
                () => row.ClassId,
                async (newClassId) => {
                    if (newClassId) {
                        await fetchClassRelatedData(index);
                    }
                },
                { immediate: true }
            );

            // Watch Section and Subject changes
            watch(
                () => [row.SectionId, row.SubjectId, row.TimeFrom, row.TimeTo],
                () => {
                    updateFormRows();
                },
                { immediate: true }
            );

            row._watchInitialized = true;
        });
    },
    { deep: true }
);



// const isDuplicate = (currentIndex, classId, sectionId, subjectId) => {
//     return rows.value.some((row, i) => {
//         return (
//             i !== currentIndex &&
//             row.ClassId === classId &&
//             row.SectionId === sectionId &&
//             row.SubjectId === subjectId
//         );
//     });
// };

const isDuplicate = (currentIndex, classId, sectionId, subjectId, timeFrom, timeTo) => {
    return rows.value.some((row, i) => {
        if (i === currentIndex) return false; // skip current row

        // must be same class, section, subject
        if (row.ClassId === classId && row.SectionId === sectionId && row.SubjectId === subjectId) {
            // check if time overlaps
            if (row.TimeFrom && row.TimeTo && timeFrom && timeTo) {
                return !(
                    timeTo <= row.TimeFrom || 
                    timeFrom >= row.TimeTo   
                );
            }
        }
        return false;
    });
};

function timesOverlap(startA, endA, startB, endB) {
    return !(endA <= startB || startA >= endB);
}

function hasTimeConflicts(rows) {
    for (let i = 0; i < rows.length; i++) {
        for (let j = i + 1; j < rows.length; j++) {
            if (
                rows[i].ClassId === rows[j].ClassId &&
                rows[i].SectionId === rows[j].SectionId &&
                rows[i].SubjectId === rows[j].SubjectId
            ) {
                if (
                    rows[i].TimeFrom &&
                    rows[i].TimeTo &&
                    rows[j].TimeFrom &&
                    rows[j].TimeTo
                ) {
                    // 🚨 check for exact duplicate slot
                    if (
                        rows[i].TimeFrom === rows[j].TimeFrom &&
                        rows[i].TimeTo === rows[j].TimeTo
                    ) {
                        return true; // same slot = not allowed
                    }

                    // check overlap as well
                    if (
                        !(
                            rows[i].TimeTo <= rows[j].TimeFrom ||
                            rows[i].TimeFrom >= rows[j].TimeTo
                        )
                    ) {
                        return true; // overlapping slot = not allowed
                    }
                }
            }
        }
    }
    return false;
}



const isClassFullyUsed = (classId) => {
    const usedCombos = rows.value
        .filter(row => row.ClassId === classId)
        .map(row => `${row.SectionId}-${row.SubjectId}`);

    const latestRow = rows.value.find(r => r.ClassId === classId);
    if (!latestRow || !latestRow.availableSections.length || !latestRow.availableSubjects.length) {
        return false;
    }

    const allCombos = [];
    for (const section of latestRow.availableSections) {
        for (const subject of latestRow.availableSubjects) {
            allCombos.push(`${section.id}-${subject.id}`);
        }
    }

    return allCombos.every(combo => usedCombos.includes(combo));
};

const isSectionFullyUsed = (classId, sectionId) => {
    // Find rows that match this class and section
    const usedSubjectIds = rows.value
        .filter(row => row.ClassId === classId && row.SectionId === sectionId)
        .map(row => row.SubjectId);

    const targetRow = rows.value.find(
        row => row.ClassId === classId && row.SectionId === sectionId
    );

    if (!targetRow || !targetRow.availableSubjects.length) return false;

    const totalSubjects = targetRow.availableSubjects.map(s => s.id);
    
    return totalSubjects.every(subjectId => usedSubjectIds.includes(subjectId));
};

const rowAddedOnce = ref(false);

watch(
    () => [form.StaffId, form.Day],
    ([newStaffId, newDay]) => {
        if (newStaffId && newDay && rows.value.length === 0) {
            addRow();
            rowAddedOnce.value = true;
        }
    }
);

// Custom submit function to ensure data is properly formatted
const submitForm = () => {
    updateFormRows();
    
    if (form.rows.length === 0) {
        toast.error("Please add at least one timetable row.", {timeout: 3000});
        return;
    }

    const invalidTimeRange = form.rows.some(row => row.TimeTo && row.TimeTo <= row.TimeFrom);
    if (invalidTimeRange) {
        toast.error('"Time To" must be after "Time From" for all rows.', { timeout: 3000 });
        return;
    }
    
    const incompleteRows = form.rows.some(row => 
        !row.ClassId || !row.SectionId || !row.SubjectId || !row.TimeFrom || !row.TimeTo
    );
    
    if (incompleteRows) {
        toast.error("Please fill in all required fields for each row.", {timeout: 3000});
        return;
    }

    if (hasTimeConflicts(form.rows)) {
        toast.error("Duplicate Class/Section/Subject cannot have the same or overlapping time slot.", { timeout: 3000 });
        return;
    }

    
    
    // Submit the form
    form.post(route('classtimetable.submit'), {
        onSuccess: () => {
            form.reset();
            rows.value = [];
            rowAddedOnce.value = false;
        },
        onError: (errors) => {
            console.log('Validation errors:', errors);
            if (errors.time_conflict) {
                toast.error(errors.time_conflict, {timeout: 5000});
            }
            if (errors.class_conflict) {
                toast.error(errors.class_conflict, {timeout: 5000});
            }
            if (errors.error) {
                toast.error(errors.error, {timeout: 5000});
            }
        }
    });
};

</script>

<template>
    <Head title="Create Class Timetable" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Class Timetable</h2>
        </template>
        <form @submit.prevent="submitForm()">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Create Class Timetable</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <InputLabel value="Staff" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <select v-model="form.StaffId" class="form-control">
                                            <option value="" disabled selected>Select Staff</option>
                                            <option v-for="staff in props.staffList" :key="staff.id" :value="staff.id">
                                                {{ staff.FirstName }} {{ staff.LastName }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.StaffId" />
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <InputLabel value="Day" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <select class="form-control" v-model="form.Day">
                                            <option selected disabled value="">Select a Day</option>
                                            <option v-for="typeList in DaysList" :key="typeList.id" :value="typeList.id">
                                                {{ typeList.name }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.Day" />
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <InputLabel value="Duration" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <select class="form-control" v-model="form.Duration">
                                            <option selected disabled value="">Select a Day</option>
                                            <option v-for="Duration in DurationList" :key="Duration.id" :value="Duration.id">
                                                {{ Duration.name }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.Duration" />
                                    </div>
                                </div>



                                
                                <div v-if="form.StaffId && form.Day && form.Duration" class="mt-4 col-md-12">
                                    <div class="card">
                                        <div class="header">Add Timetable Rows</div>
                                        <div class="body table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Class</th>
                                                        <th>Section</th>
                                                        <th>Subject</th>
                                                        <th>Time From</th>
                                                        <th>Time To</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(row, index) in rows" :key="index">
                                                        <td>
                                                            <select v-model="row.ClassId" class="form-control">
                                                                <option value="">Select Class</option>
                                                                <!-- <option v-for="cls in props.classesList" :key="cls.id" :value="cls.id"
                                                                    :disabled="isClassFullyUsed(cls.id)"
                                                                    :class="{ 'bg-red-100 text-red-700': isClassFullyUsed(cls.id) }">
                                                                    {{ cls.ClassName }}
                                                                </option> -->
                                                                <option v-for="cls in props.classesList" :key="cls.id" :value="cls.id">
                                                                    {{ cls.ClassName }}
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select v-model="row.SectionId" class="form-control"
                                                                :disabled="!row.ClassId || row.availableSections.length === 0">
                                                                <option value="">Select Section</option>
                                                                <!-- <option v-for="sec in row.availableSections" :key="sec.id" :value="sec.id"
                                                                    :disabled="isSectionFullyUsed(row.ClassId, sec.id)"
                                                                    :class="{ 'bg-red-100 text-red-700': isSectionFullyUsed(row.ClassId, sec.id) }">
                                                                    {{ sec.SectionName }}
                                                                </option> -->
                                                                <option v-for="sec in row.availableSections" :key="sec.id" :value="sec.id">
                                                                    {{ sec.SectionName }}
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select v-model="row.SubjectId" class="form-control"
                                                                :disabled="!row.ClassId || !row.SectionId || row.availableSubjects.length === 0">
                                                                <option value="">Select Subject</option>
                                                                <!-- <option v-for="sub in row.availableSubjects" :key="sub.id" :value="sub.id"
                                                                    :disabled="isDuplicate(index, row.ClassId, row.SectionId, sub.id)"
                                                                    :class="{
                                                                        'bg-red-100 text-red-700': isDuplicate(index, row.ClassId, row.SectionId, sub.id),
                                                                        'opacity-50': !row.ClassId || !row.SectionId
                                                                    }">
                                                                    {{ sub.SubjectName }}
                                                                </option> -->
                                                                <option 
                                                                    v-for="sub in row.availableSubjects" 
                                                                    :key="sub.id" 
                                                                    :value="sub.id"
                                                                    :disabled="isDuplicate(index, row.ClassId, row.SectionId, sub.id, row.TimeFrom, row.TimeTo)"
                                                                    :class="{
                                                                        'bg-red-100 text-red-700': isDuplicate(index, row.ClassId, row.SectionId, sub.id, row.TimeFrom, row.TimeTo),
                                                                        'opacity-50': !row.ClassId || !row.SectionId
                                                                    }"
                                                                    >
                                                                    {{ sub.SubjectName }}
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="time" v-model="row.TimeFrom" 
                                                                class="form-control" :disabled="!row.ClassId || !row.SectionId || !row.SubjectId" />
                                                        </td>
                                                        <td>
                                                            <input type="time" v-model="row.TimeTo" 
                                                                class="form-control" :disabled="!row.TimeFrom" />
                                                            <div v-if="row.TimeTo && row.TimeTo <= row.TimeFrom" class="text-danger font-12">
                                                                "Time To" must be after "Time From"
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger btn-sm" title="Remove Row" 
                                                                 @click="removeRow(index)">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <button type="button" class="mt-2 btn btn-success btn-sm" title="Add Row" 
                                                @click="addRow()">
                                                <i class="fa fa-plus"></i> Add Row
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-start" v-if="form.StaffId && form.Day && form.Duration">
                                    <PrimaryButton :disabled="form.processing">
                                        <span v-if="form.processing" class="spinner-border spinner-border-sm me-1"></span>
                                        {{ form.processing ? 'Submitting...' : 'Submit' }}  
                                    </PrimaryButton>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>