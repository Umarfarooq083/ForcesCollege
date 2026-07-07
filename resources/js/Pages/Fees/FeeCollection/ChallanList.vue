<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref, watch, nextTick } from 'vue';
import debounce from 'lodash/debounce';
import 'vue-multiselect/dist/vue-multiselect.min.css';
import Pagination from '@/Components/Pagination.vue';

const form = useForm({
    ChallanNo: '',
});

const props = defineProps({
    GenerateFeeChallan: Object,
    errors: Object,
});

const page = usePage();
const receipt = ref(null);
const receiptPrintArea = ref(null);

const columns = [
    { label: 'Challan No' },
    { label: 'Student' },
    { label: 'Class' },
    { label: 'Section' },
    { label: 'Challan Month' },
    { label: 'Submit Date' },
    { label: 'Payment Mode' },
    { label: 'Amount' },
    { label: 'Actions' },
];

const search = ref('');
watch(search, debounce((value) => {
    router.get(route('fee.collection.list'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 800));

const openReceiptModal = async (data) => {
    receipt.value = data;
    await nextTick();
    if (typeof window !== 'undefined' && window.$) {
        window.$('#receiptModal').modal('show');
    }
};

// const printReceipt = () => {
//   if (!receiptPrintArea.value) return;

//   const content = receiptPrintArea.value.innerHTML;
//   const win = window.open('', '_blank', 'width=900,height=650');
//   if (!win) return;

//   win.document.open();
//   win.document.write(`
//     <!doctype html>
//     <html>
//       <head>
//         <meta charset="utf-8" />
//         <title>Fee Receipt</title>
//         <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
//         <style>
//           body { font-size: 12px; padding: 16px; }
//           .receipt-card { border: 1px solid #000; padding: 14px; }
//           .muted { color: #666; }
//           .kv td { padding: 4px 8px; }
//           .kv td:first-child { width: 160px; }
//         </style>
//       </head>
//       <body>${content}</body>
//     </html>
//   `);
//   win.document.close();
//   win.focus();
//   setTimeout(() => {
//     win.print();
//     win.close();
//   }, 250);
// };

const printReceipt = () => {
    if (!receiptPrintArea.value) return;

    const content = receiptPrintArea.value.innerHTML;
    const win = window.open('', '_blank', 'width=900,height=650');
    if (!win) return;

    win.document.open();
    win.document.write(`
    <!doctype html>
    <html>
      <head>
        <meta charset="utf-8" />
        <title>Fee Receipt</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <style>
          body { font-size: 12px; padding: 16px; }
            
          .receipt-card {
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            font-size: 14px;
            position: relative;
            padding: 20px;
          }
    
          .paid-stamp {
              position: absolute;
              top: 300px;
              left: 45%;
              width: 130px;
              height: 130px;
              border: 4px solid #2e7d32;
              border-radius: 50%;
              display: flex;
              flex-direction: column;
              align-items: center;
              justify-content: center;
              transform: rotate(-18deg);
              opacity: .7;
              z-index: 10;
          }

          .paid-stamp::before {
              content: '';
              position: absolute;
              inset: 5px;
              border: 2px dashed #2e7d32;
              border-radius: 50%;
          }

          .stamp-text {
              font-size: 38px;
              font-weight: 700;
              color: #2e7d32;
              letter-spacing: 2px;
              line-height: 1;
          }

          .stamp-sub {
              font-size: 9px;
              font-weight: 600;
              color: #2e7d32;
              letter-spacing: 3px;
              text-transform: uppercase;
              margin-top: 2px;
          }

          .sig-row {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
            }
            .sig-block {
                width: 200px;
            }
            .sig-line {
                border-bottom: 1.5px solid;
                height: 32px;
                margin-bottom: 5px;
            }

            .sig-label {
                font-size: 11px;
                font-weight: 500;
                text-align: center;
            }
          /* Optional: Better print colors */
          @media print {
            body {
              -webkit-print-color-adjust: exact;
              font-size: 16px !important;
            }
            row{
                display: flex;
                flex-wrap: wrap;
                margin-right: -15px;
                margin-left: -15px;
            }
            .col-md-6 {
                -ms-flex: 0 0 50%;
                flex: 0 0 50%;
                max-width: 50%;
            }
          }
        </style>
      </head>

      <body>
        ${content}
      </body>
    </html>
  `);

    win.document.close();
    win.focus();

    setTimeout(() => {
        win.print();
        win.close();
    }, 300);
};

watch(
    () => page.props.flash?.receipt,
    (val) => {
        if (val) openReceiptModal(val);
    },
    { immediate: true }
);
</script>

<template>
    <Head title="Challan List" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                challan list
            </h2>
        </template>

        <form @submit.prevent="form.get(route('fee.collection'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header d-flex justify-content-between align-items-center mb-3">
                            <!-- Left side -->
                            <div class="font-weight-bold">Challan Fee Collection</div>

                            <!-- Right side (Search) -->
                            <div>
                                <input type="text" class="form-control form-control-sm" v-model="search"
                                    placeholder="Search by Class, Section, Student, Challan No ..."
                                    style="width: 350px;" />
                            </div>
                        </div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-4">
                                    <TextInput type="number" v-model="form.ChallanNo"
                                        placeholder="Enter Challan No for collection" class="form-control" required />
                                    <InputError class="mt-2" :message="props.errors.ChallanNo" />
                                </div>

                                <div class="col-md-2">
                                    <PrimaryButton
                                        v-if="$page.props.auth.user.user_permissions.indexOf('fee.collection') > -1">
                                        Collect</PrimaryButton>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="card">
            <!-- <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Collected Challan List</div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" v-model="search" placeholder="Search Challan No" />
                    </div>
                </div>
            </div> -->
            <div class="body">

                <div class="table-responsive">
                    <table class="table mb-0 text-center table-hover c_list table-bordered">
                        <TableHeader :columns="columns" />
                        <tbody>
                            <tr v-for="(challan, index) in props.GenerateFeeChallan.data" :key="index">
                                <td>{{ challan?.challan_no }}</td>
                                <td>{{ challan?.student?.FirstName }} {{ challan?.student?.LastName }}</td>
                                <td>{{ challan?.student?.class?.ClassName }}</td>
                                <td>{{ challan?.student?.section?.SectionName }}</td>
                                <td>{{ challan?.ChallanMonth }}</td>
                                <td>{{ challan?.SubmitDate }}</td>
                                <td>{{ challan?.PaymentMode }}</td>
                                <td>
                                    {{ Number(challan?.transection_sum_balancefeeafterdiscount || 0) }}
                                    <!-- {{
                                        Number(challan?.partial_payments_sum_receivedamount || 0) +
                                        Number(challan?.WaivedFineAmount || 0)
                                    }} -->
                                </td>
                                <td>
                                    <Link method="get"
                                        :href="route('fee.challan.detail', { ChallanNo: challan?.challan_no })"
                                        type="button" class="btn btn-info btn-sm" title="Detail"><i
                                            class="fa fa-info-circle pull-right"></i></Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="GenerateFeeChallan.links" />
            </div>
        </div>

        <!-- Receipt Modal -->
        <div class="modal fade" id="receiptModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header bg-light">
                        <h5 class="modal-title font-weight-bold">Fee Receipt</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div ref="receiptPrintArea" class="receipt-card position-relative p-4" v-if="receipt">

                            <div class="paid-stamp">
                                <div class="stamp-text">PAID</div>
                                <!-- <div class="stamp-sub">Cleared</div> -->
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h4 class="mb-0 font-weight-bold text-primary">Receipt</h4>
                                    <div class="text-muted">Receipt No: {{ receipt.receipt_no }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-muted d-block">Date: {{ receipt.collected_at }}</div>
                                    <div class="text-muted">Challan No: {{ receipt.challan_no }}</div>
                                </div>
                            </div>

                            <hr>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td><strong>Student</strong></td>
                                            <td>{{ receipt.student_name || '---' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Roll #</strong></td>
                                            <td>{{ receipt.roll_no || '---' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Father</strong></td>
                                            <td>{{ receipt.father_name || '---' }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td><strong>Class / Section</strong></td>
                                            <td>{{ receipt.class_name }} / {{ receipt.section_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Payment Type</strong></td>
                                            <td class="text-uppercase">{{ receipt.payment_mode }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Collected By</strong></td>
                                            <td>{{ receipt.collected_by_name }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <table class="table table-bordered text-sm">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Description</th>
                                        <th class="text-right">Amount (PKR)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Payable Amount</td>
                                        <td class="text-right">{{ Number(receipt.payable_amount || 0).toFixed(2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Already Paid</td>
                                        <td class="text-right">{{ Number(receipt.already_paid_before || 0).toFixed(2) }}
                                        </td>
                                    </tr>
                                    <tr class="font-weight-bold text-success">
                                        <td>Received Now</td>
                                        <td class="text-right">{{ Number(receipt.received_amount || 0).toFixed(2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Waive Off</td>
                                        <td class="text-right">{{ Number(receipt.waived_amount_total || 0).toFixed(2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Late Fine</td>
                                        <td class="text-right">{{ Number(receipt.fine_amount_total || 0).toFixed(2) }}
                                        </td>
                                    </tr>
                                    <tr class="bg-light font-weight-bold">
                                        <td>Balance After</td>
                                        <td class="text-right">{{ Number(receipt.balance_after || 0).toFixed(2) }}</td>
                                    </tr>
                                </tbody>


                            </table>
                            <div class="sig-row">
                                <div class="sig-block">
                                    <div class="sig-line"></div>
                                    <div class="sig-label">Campus Signature</div>
                                </div>
                                <div class="sig-block" style="text-align:right;">
                                    <div class="sig-line"></div>
                                    <div class="sig-label">Parent / Student Signature</div>
                                </div>
                            </div>
                            <!-- Note -->
                            <div v-if="receipt.note" class="mt-3 p-2 border rounded bg-light">
                                <strong>Note:</strong>
                                <div style="white-space: pre-wrap;">{{ receipt.note }}</div>
                            </div>

                        </div>

                        <div v-else class="text-center text-muted">
                            No receipt to display.
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button class="btn btn-success" :disabled="!receipt" @click="printReceipt">
                            🖨 Print
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

.receipt-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    font-size: 14px;
}

.paid-stamp {
    position: absolute;
    top: 60%;
    left: 45%;
    width: 130px;
    height: 130px;
    border: 4px solid #2e7d32;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    transform: rotate(-18deg);
    opacity: .7;
    pointer-events: none;
    z-index: 10;
}

.paid-stamp::before {
    content: '';
    position: absolute;
    inset: 5px;
    border: 2px dashed #2e7d32;
    border-radius: 50%;
}

.paid-stamp .stamp-text {
    font-family: 'Playfair Display', serif;
    font-size: 38px;
    font-weight: 700;
    color: #2e7d32;
    letter-spacing: 2px;
    line-height: 1;
}

.paid-stamp .stamp-sub {
    font-size: 9px;
    font-weight: 600;
    color: #2e7d32;
    letter-spacing: 3px;
    text-transform: uppercase;
    margin-top: 2px;
}

@media print {
    .paid-stamp {
        color: rgba(40, 167, 69, 0.25);
        border-color: rgba(40, 167, 69, 0.3);
    }
}

.sig-row {
    display: flex;
    justify-content: space-between;
    margin-top: 8px;
}

.sig-block {
    width: 200px;
}

.sig-line {
    border-bottom: 1.5px solid;
    height: 32px;
    margin-bottom: 5px;
}

.sig-label {
    font-size: 11px;
    font-weight: 500;
    text-align: center;
}
</style>