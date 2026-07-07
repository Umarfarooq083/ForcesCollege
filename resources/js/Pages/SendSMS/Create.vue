<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { ref, computed, watch, watchEffect } from 'vue'
import axios from 'axios'
import { useToast } from 'vue-toastification'

// Initialize vue-toastification
const toast = useToast()
const page = usePage()

const props = defineProps({
  phonebook_groups: Array,
  departments: Array,
  classes: Array,
  sections: Array,
  remainingCredit: Number
})

const form = useForm({
  smsContent: '',
  selectedContacts: [],
})

// States
const selectedContactType = ref(null)
const availableContacts = ref([])
const selectedContacts = ref([])
const selectedAvailable = ref([])
const selectedRight = ref([])
const isLoading = ref(false)

const remainingCredit = ref(props.remainingCredit)
const toggles = ref({ getAll: false })

const phonebookGroup = ref('')
const phoneNumber = ref('')
const department = ref('')
const selectedClass = ref('')
const selectedSection = ref('')
const sections_ref = ref([])

// ✅ SMS Counter Logic
const smsContent = ref('')
const SMS_LENGTH = 160

const smsCount = computed(() => {
  const length = smsContent.value.length
  if (length === 0) return 1
  return Math.ceil(length / SMS_LENGTH)
})

const characterCount = computed(() => smsContent.value.length)

const smsCountText = computed(() => {
  return `${characterCount.value} Character ${smsCount.value} SMS`
})

// Contact types with colors
const contactTypes = [
  { key: 'phonebook', label: 'Phonebook', color: '#6c5ce7' },
  { key: 'phone', label: 'Phone', color: '#00b894' },
  { key: 'father', label: 'Father', color: '#0984e3' },
  { key: 'mother', label: 'Mother', color: '#fd79a8' },
  { key: 'guardian', label: 'Guardian', color: '#e17055' },
  { key: 'student', label: 'Student', color: '#00cec9' },
  { key: 'staff', label: 'Staff', color: '#f1c40f' },
]

// Watch for flash messages from Laravel - WITH ERROR HANDLING
watch(() => page.props.flash, (flash) => {
  try {
    if (flash && typeof flash === 'object') {
      if (flash.success) {
        toast.success(flash.success, {
          position: "top-right",
          timeout: 5000,
        })
        // Refresh remaining credit after successful send
        refreshRemainingCredit()
      }
      if (flash.error) {
        toast.error(flash.error, {
          position: "top-right",
          timeout: 5000,
        })
      }
      if (flash.warning) {
        toast.warning(flash.warning, {
          position: "top-right",
          timeout: 5000,
        })
        // Refresh remaining credit on partial success
        refreshRemainingCredit()
      }
    }
  } catch (error) {
    console.error('Error in flash message watcher:', error)
  }
}, { deep: true })

// Watchers for SMS functionality
watch(() => selectedClass.value, (newClassId) => {
  if (newClassId) {
    loadSections(newClassId)
  } else {
    sections_ref.value = []
    selectedSection.value = ''
  }
})

// Watch for SMS content changes to update character count in real-time
watch(() => smsContent.value, (newContent) => {
  if (newContent.length > 480) { // 3 SMS limit
    toast.warning('Message is getting too long. Consider shortening it.', {
      position: "top-right",
      timeout: 3000,
    })
  }
})

// Watch for selected contacts changes
watch(() => selectedContacts.value.length, (newCount, oldCount) => {
  if (newCount > oldCount) {
    // Contacts were added
    const creditRequired = newCount * smsCount.value
    if (creditRequired > remainingCredit.value) {
      toast.warning(`Selected contacts require ${creditRequired} credits, but you only have ${remainingCredit.value}`, {
        position: "top-right",
        timeout: 4000,
      })
    }
  }
})

// Watch for SMS count changes
watch(smsCount, (newCount) => {
  const totalCreditsRequired = selectedContacts.value.length * newCount
  if (totalCreditsRequired > remainingCredit.value && selectedContacts.value.length > 0) {
    toast.warning(`This will require ${totalCreditsRequired} credits (${selectedContacts.value.length} contacts × ${newCount} SMS each)`, {
      position: "top-right",
      timeout: 4000,
    })
  }
})

// Toggle contact type
const toggleContactType = async (key) => {
  if (selectedContactType.value === key) {
    selectedContactType.value = null
    availableContacts.value = []
  } else {
    selectedContactType.value = key
    toggles.value.getAll = false
    availableContacts.value = []
    selectedContacts.value = []
  }
}

// Get contacts from backend
const getContacts = async () => {
  if (!selectedContactType.value) return

  if (selectedContactType.value === 'phone') {
    // If user manually entered a phone number
    if (!phoneNumber.value.trim()) {
      toast.error('Please enter a phone number', {
        position: "top-right",
        timeout: 3000,
      })
      return
    }

    // Validate phone number format
    const phoneRegex = /^[0-9+\-\s()]+$/;
    if (!phoneRegex.test(phoneNumber.value.trim())) {
      toast.error('Please enter a valid phone number', {
        position: "top-right",
        timeout: 3000,
      })
      return
    }

    // Normalize phone number to start with 92
    let formattedNumber = phoneNumber.value.trim().replace(/\s+/g, '')

    if (formattedNumber.startsWith('0')) {
      formattedNumber = '92' + formattedNumber.slice(1)
    } else if (!formattedNumber.startsWith('92')) {
      formattedNumber = '92' + formattedNumber
    }

    // Create a contact object
    const newContact = {
      name: 'Manual Entry',
      phone: formattedNumber,
    }

    // Add to available contacts (prevent duplicates)
    const exists = availableContacts.value.some(c => c.phone === newContact.phone)
    if (!exists) {
      availableContacts.value.push(newContact)    
      toast.success('Contact added successfully', {
        position: "top-right",
        timeout: 3000,
      })
    } else {
      toast.warning('Contact already exists', {
        position: "top-right",
        timeout: 3000,
      })
    }

    // Clear input field
    phoneNumber.value = ''
    return
  }

  try {
    const params = {
      type: selectedContactType.value,
      group_id: phonebookGroup.value,
      class_id: selectedClass.value,
      section_id: selectedSection.value,
      department_id: department.value,
      get_all: toggles.value.getAll || false,
    }

    const res = await axios.get('/sms/get-contacts', { params })
    availableContacts.value = res.data
    toast.success(`Loaded ${res.data.length} contacts`, {
      position: "top-right",
      timeout: 3000,
    })
  } catch (e) {
    console.error('Error loading contacts', e)
    toast.error('Failed to load contacts', {
      position: "top-right",
      timeout: 3000,
    })
  }
}

// Move/Remove functions
const moveSelected = (selected) => {
  if (selected.length === 0) {
    toast.warning('Please select contacts to move', {
      position: "top-right",
      timeout: 3000,
    })
    return
  }
  selected.forEach((contact) => {
    selectedContacts.value.push(contact)
    availableContacts.value = availableContacts.value.filter((c) => c.phone !== contact.phone)
  })
  selectedAvailable.value = []
  toast.success(`Moved ${selected.length} contacts`, {
    position: "top-right",
    timeout: 3000,
  })
}

const moveAll = () => {
  if (availableContacts.value.length === 0) {
    toast.warning('No contacts available to move', {
      position: "top-right",
      timeout: 3000,
    })
    return
  }
  const count = availableContacts.value.length
  selectedContacts.value.push(...availableContacts.value)
  availableContacts.value = []
  toast.success(`Moved all ${count} contacts`, {
    position: "top-right",
    timeout: 3000,
  })
}

const removeSelected = (selected) => {
  if (selected.length === 0) {
    toast.warning('Please select contacts to remove', {
      position: "top-right",
      timeout: 3000,
    })
    return
  }
  selected.forEach((contact) => {
    availableContacts.value.push(contact)
    selectedContacts.value = selectedContacts.value.filter((c) => c.phone !== contact.phone)
  })
  selectedRight.value = []
  toast.success(`Removed ${selected.length} contacts`, {
    position: "top-right",
    timeout: 3000,
  })
}

const removeAll = () => {
  if (selectedContacts.value.length === 0) {
    toast.warning('No contacts to remove', {
      position: "top-right",
      timeout: 3000,
    })
    return
  }
  const count = selectedContacts.value.length
  availableContacts.value.push(...selectedContacts.value)
  selectedContacts.value = []
  toast.success(`Removed all ${count} contacts`, {
    position: "top-right",
    timeout: 3000,
  })
}

// Submit SMS - Simplified version
const submitSms = async () => {
  if (isLoading.value) return
  
  form.smsContent = smsContent.value
  form.selectedContacts = selectedContacts.value.map(c => c.phone)
  
  if (form.selectedContacts.length === 0) {
    toast.error('Please select at least one contact', {
      position: "top-right",
      timeout: 3000,
    })
    return
  }
  
  if (!form.smsContent.trim()) {
    toast.error('Please enter SMS content', {
      position: "top-right",
      timeout: 3000,
    })
    return
  }

  // Check credit before sending
  const requiredCredit = selectedContacts.value.length * smsCount.value
  if (remainingCredit.value < requiredCredit) {
    toast.error(`Insufficient credits. Required: ${requiredCredit}, Available: ${remainingCredit.value}`, {
      position: "top-right",
      timeout: 5000,
    })
    return
  }

  isLoading.value = true

  try {
    const response = await axios.post('/sms/send-sms', form, {
      headers: {
        'Content-Type': 'application/json',
      }
    })

    // Handle successful response with detailed data
    const result = response.data
    
    if (result.success) {
      // Update remaining credit
      remainingCredit.value = result.summary.remainingCredit
      
      // Show detailed summary
      if (result.summary.failed === 0) {
        toast.success(result.message, {
          position: "top-right",
          timeout: 5000,
        })
      } else if (result.summary.success > 0) {
        // Show warning with details for partial success
        toast.warning(result.message, {
          position: "top-right",
          timeout: 6000,
        })
        
        // Optionally show more details in a separate toast or modal
        setTimeout(() => {
          toast.info(`✅ ${result.summary.success} successful, ❌ ${result.summary.failed} failed`, {
            position: "top-right",
            timeout: 5000,
          })
        }, 1000)
      } else {
        toast.error(result.message, {
          position: "top-right",
          timeout: 5000,
        })
      }
      
      // Show detailed results in console for debugging
      console.log('Detailed SMS results:', result.detailedResults)
      
      // You can also show a modal with detailed results
      showDetailedResults(result.detailedResults)
      
      // Clear form after successful send
      smsContent.value = ''
      selectedContacts.value = []
      availableContacts.value = []
      
    } else {
      toast.error(result.message || 'Failed to send SMS', {
        position: "top-right",
        timeout: 5000,
      })
    }
    
  } catch (error) {
    // Handle Axios errors (network, validation, etc.)
    if (error.response) {
      // Server responded with error status
      const errorData = error.response.data
      
      if (error.response.status === 422) {
        // Validation errors
        const errors = errorData.errors
        Object.keys(errors).forEach(key => {
          toast.error(errors[key][0], {
            position: "top-right",
            timeout: 5000,
          })
        })
      } else if (error.response.status === 400) {
        // Credit or other business logic errors
        toast.error(errorData.message, {
          position: "top-right",
          timeout: 5000,
        })
        // Refresh credit display
        refreshRemainingCredit()
      } else {
        toast.error(errorData.message || 'Failed to send SMS', {
          position: "top-right",
          timeout: 5000,
        })
      }
    } else if (error.request) {
      // Network error
      toast.error('Network error: Please check your internet connection', {
        position: "top-right",
        timeout: 5000,
      })
    } else {
      // Other errors
      toast.error('An unexpected error occurred', {
        position: "top-right",
        timeout: 5000,
      })
    }
  } finally {
    isLoading.value = false
  }
}

// Add function to show detailed results (optional)
const showDetailedResults = (results) => {
  // You can implement a modal or detailed view here
  const successCount = results.filter(r => r.success).length
  const failedCount = results.filter(r => !r.success).length
  
  console.log(`SMS Send Results: ${successCount} successful, ${failedCount} failed`)
  
  // Show failed numbers in a separate toast if any
  const failedNumbers = results.filter(r => !r.success).slice(0, 5) // Show first 5 failures
  if (failedNumbers.length > 0) {
    setTimeout(() => {
      failedNumbers.forEach(failed => {
        toast.error(`Failed: ${failed.mobile} - ${failed.message}`, {
          position: "top-right",
          timeout: 4000,
        })
      })
    }, 1500)
  }
}

// Computed helpers
const needsClassSection = computed(() =>
  ['father', 'mother', 'guardian', 'student'].includes(selectedContactType.value)
)
const showClassSection = computed(() => needsClassSection.value && !toggles.value.getAll)
const showDepartment = computed(() => selectedContactType.value === 'staff' && !toggles.value.getAll)

// when class changes, load sections
const loadSections = async () => {
  if (!selectedClass.value) {
    sections_ref.value = []
    return
  }
  try {
    const res = await axios.get(`/sms/sections/${selectedClass.value}`)
    sections_ref.value = res.data
  } catch (error) {
    toast.error('Failed to load sections', {
      position: "top-right",
      timeout: 3000,
    })
  }
}

// Refresh remaining credit
const refreshRemainingCredit = async () => {
  try {
    const response = await axios.get('/sms/remaining-credit')
    remainingCredit.value = response.data.remainingCredit
  } catch (error) {
    console.error('Failed to refresh credit:', error)
  }
}
</script>

<template>
  <Head title="Send SMS" />   
  <AuthenticatedLayout>
    <div class="card shadow-sm">
      <div class="card-header bg-light">
        <h5 class="mb-0 fw-bold">Create SMS</h5><h5>Remaining Credit: {{ remainingCredit }}</h5>
      </div>

      <div class="card-body">     
        <!-- Your existing template content remains the same -->
        <!-- Select Contact Type -->
        <div class="mb-3">
          <label class="fw-bold mb-2 d-block">Select Contact From</label>
          <div class="d-flex flex-wrap align-items-center gap-4">
            <div
              v-for="type in contactTypes"
              :key="type.key"
              class="d-flex align-items-center gap-2"
            >
              <div class="custom-control custom-switch">
                <input
                  type="checkbox"
                  class="custom-control-input"
                  :id="type.key"
                  :checked="selectedContactType === type.key"
                  @change="toggleContactType(type.key)"
                />
                <label
                  class="custom-control-label cstm_switch"
                  :for="type.key"
                  :data-color="type.key"
                >
                  {{ type.label }}
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Dynamic Filters -->
        <div v-if="selectedContactType" class="border p-3 rounded mb-3">
          <!-- Phonebook -->
          <div v-if="selectedContactType === 'phonebook'" class="d-flex gap-3 align-items-center">
            <label>Group</label>
            <select v-model="phonebookGroup" class="form-control form-control-sm w-auto">
              <option value="">Select Group</option>
              <option v-for="g in phonebook_groups" :key="g.id" :value="g.id">{{ g.name }}</option>
            </select>
            <button class="btn btn-sm btn-primary" @click="getContacts">Get Contacts</button>
          </div>

          <!-- Staff -->
          <div v-else-if="selectedContactType === 'staff'" class="d-flex gap-3 align-items-center">
            <label>Get All</label>
            <input type="checkbox" v-model="toggles.getAll" />
            <div v-if="showDepartment">
              <label>Department</label>
              <select v-model="department" class="form-control form-control-sm w-auto">
                <option value="">Select Department</option>
                <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.DepartmentName }}</option>
              </select>
            </div>
            <button class="btn btn-sm btn-primary" @click="getContacts">Get Data</button>
          </div>

          <!-- Phone Number -->
          <div v-else-if="selectedContactType === 'phone'" class="d-flex gap-3 align-items-center">
            <input type="text" v-model="phoneNumber" class="form-control form-control-sm w-auto" placeholder="Enter phone number"/>
            <button class="btn btn-sm btn-primary" @click="getContacts">Add Contact</button>
          </div>

          <!-- Father/Mother/Guardian/Student -->
          <div v-else-if="needsClassSection" class="d-flex gap-3 align-items-center">
            <label>Get All</label>
            <input type="checkbox" v-model="toggles.getAll" />
            <div v-if="showClassSection">
              <label>Class</label>
              <select v-model="selectedClass" class="form-control form-control-sm w-auto" @change="loadSections">
                <option value="">Select Class</option>
                <option v-for="cls in classes" :key="cls.id" :value="cls.id">
                  {{ cls.ClassName }}
                </option>
              </select>
            </div>
            <div v-if="showClassSection">
              <label>Section</label>
              <select v-model="selectedSection" class="form-control form-control-sm w-auto">
                <option value="">Select Section</option>
                <option v-for="sec in sections_ref" :key="sec.id" :value="sec.id">
                  {{ sec.SectionName }}
                </option>
              </select>
            </div>
            <button class="btn btn-sm btn-primary" @click="getContacts">Get Data</button>
          </div>
        </div>

        <!-- CONTACT BOXES -->
        <div class="d-flex justify-content-between gap-4 flex-wrap mb-4">
          <!-- Available Contacts -->
          <div class="flex-grow-1">
            <label class="fw-semibold mb-2 d-block">Available Contacts</label>
            <select multiple class="form-control mb-2" style="height: 180px" v-model="selectedAvailable">
              <option v-for="c in availableContacts" :key="c.phone" :value="c">
                {{ c.name }} — {{ c.phone }}
              </option>
            </select>
            <div class="d-flex gap-2 justify-content-center">
              <button class="btn btn-outline-primary btn-sm" @click="moveSelected(selectedAvailable)">Move ></button>
              <button class="btn btn-outline-primary btn-sm" @click="moveAll">Move All >></button>
            </div>
          </div>

          <!-- Selected Contacts -->
          <div class="flex-grow-1">
            <label class="fw-semibold mb-2 d-block">Selected Contacts ({{ selectedContacts.length }})</label>
            <select multiple class="form-control mb-2" style="height: 180px" v-model="selectedRight">
              <option v-for="c in selectedContacts" :key="c.phone" :value="c">
                {{ c.name }} — {{ c.phone }}
              </option>
            </select>
            <div class="d-flex gap-2 justify-content-center">
              <button class="btn btn-outline-danger btn-sm" @click="removeSelected(selectedRight)">< Remove</button>
              <button class="btn btn-outline-danger btn-sm" @click="removeAll"><< Remove All</button>
            </div>
          </div>
        </div>

        <!-- SMS TEXTAREA WITH COUNTER -->
        <div class="mb-3">
          <label class="fw-bold mb-2 d-block">SMS Content</label>
          <div class="position-relative">
            <textarea
              v-model="smsContent"
              class="form-control"
              rows="5"
              placeholder="Type your SMS message here..."
              :disabled="isLoading"
            ></textarea>
          </div>
          <div class="d-flex justify-content-start mt-2">
            <span class="badge bg-light text-dark border">
              {{ smsCountText }}
            </span>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="d-flex justify-content-end gap-2">
          <button 
            class="btn btn-success position-relative"
            @click="submitSms"
            :disabled="isLoading || selectedContacts.length === 0 || smsContent.trim() === ''"
          >
            <div v-if="isLoading" class="position-absolute top-50 start-50 translate-middle">
              <div class="spinner-border spinner-border-sm text-white" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
            
            <div :class="{ 'opacity-0': isLoading }" class="d-flex align-items-center">
              <i class="bi bi-send me-2"></i> 
              {{ isLoading ? 'Sending...' : 'Send SMS' }}
            </div>
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.cstm_switch {
  cursor: pointer;
  user-select: none;
}

.btn:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

.position-relative {
  position: relative;
}
</style>