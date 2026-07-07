<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    payrollData: {
        type: Array,
        default: () => []
    },
    selectedMonth: Number,
    selectedYear: Number,
});

const months = {
    1: 'January', 2: 'February', 3: 'March', 4: 'April',
    5: 'May', 6: 'June', 7: 'July', 8: 'August',
    9: 'September', 10: 'October', 11: 'November', 12: 'December',
};

const form = useForm({
    month: props.selectedMonth,
    year: props.selectedYear,
    staff_id: props.payrollData?.[0]?.staff_id || '',
});

const isLoaded = ref(false);
onMounted(() => {
    isLoaded.value = true;
});

const downloadPdf = async () => {
    try {
        const response = await axios.post(route('payrollslip.store'), form.data(), {
            responseType: 'blob',
            headers: {
                'Accept': 'application/pdf',
                'Content-Type': 'application/json'
            }
        });
        
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        
        let fileName = 'Payroll_Slip.pdf';
        const contentDisposition = response.headers['content-disposition'];
        if (contentDisposition) {
            const fileNameMatch = contentDisposition.match(/filename="?([^"]+)"?/);
            if (fileNameMatch) {
                fileName = fileNameMatch[1];
            }
        }
        
        link.setAttribute('download', fileName);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Download failed:', error);
    }
};

// Computed Aggregated Data
const totalGrossSalary = computed(() => 
    props.payrollData?.reduce((sum, p) => sum + parseFloat(p.gross_salary || 0), 0) || 0
);

const totalNetSalary = computed(() => 
    props.payrollData?.reduce((sum, p) => sum + parseFloat(p.net_salary || 0), 0) || 0
);

const totalDeductions = computed(() => {
    return props.payrollData?.reduce((sum, p) => {
        return sum + parseFloat(p.total_absent_deduction || 0) + 
               parseFloat(p.fine_deduction || 0) + 
               parseFloat(p.late_fine_deduction || 0) + 
               parseFloat(p.salary_tax || 0) + 
               parseFloat(p.security_deduction || 0);
    }, 0) || 0;
});

const totalEmployees = computed(() => props.payrollData?.length || 0);
const averageSalary = computed(() => totalEmployees.value > 0 ? totalNetSalary.value / totalEmployees.value : 0);

const getStatusBadge = (status) => {
    const badges = {
        pending: 'badge-pending',
        approved: 'badge-approved',
        rejected: 'badge-rejected'
    };
    return badges[status?.toLowerCase()] || 'badge-secondary';
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'PKR'
    }).format(amount || 0);
};

const getRowTheme = (id) => {
    const themes = ['theme-primary', 'theme-indigo', 'theme-emerald', 'theme-amber'];
    return themes[id % themes.length] || 'theme-primary';
};

const expandedCards = ref({});
const toggleExpand = (id) => {
    expandedCards.value[id] = !expandedCards.value[id];
};
</script>

<template>
    <Head title="Payroll Management" />
    <AuthenticatedLayout>

        <div v-if="payrollData?.length" class="payroll-view-wrapper mt-5" :class="{ 'is-rendered': isLoaded }">
            
            <div class="payroll-dashboard-header">
                <div class="header-main-info">
                    <!-- <div class="context-tag">
                        <span class="live-indicator"></span>
                        Fiscal Period: {{ selectedYear }}
                    </div> -->
                    <h1 class="page-title">
                        Payroll Registry 
                        <span class="date-badge">
                            <i class="fa-regular fa-calendar"></i> {{ months[selectedMonth] }} {{ selectedYear }}
                        </span>
                    </h1>
                    <!-- <p class="subtitle-text">
                        <i class="fa-solid fa-chart-pie"></i> Authorized overview of execution fields across {{ totalEmployees }} profile accounts.
                    </p> -->
                </div>
                <div class="header-actions">
                    <button class="action-btn-primary" @click="downloadPdf">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <span>Compile & Issue Slips</span>
                    </button>
                </div>
            </div>

            <div class="metrics-summary-bar">
                <!-- <div class="metric-block">
                    <div class="m-icon icon-blue"><i class="fa-solid fa-users-gear"></i></div>
                    <div class="m-data">
                        <label>Active Staff</label>
                        <h3>{{ totalEmployees }}</h3>
                    </div>
                </div> -->
                <div class="metric-block">
                    <div class="m-icon icon-green"><i class="fa-solid fa-vault"></i></div>
                    <div class="m-data">
                        <label>Gross Commitment</label>
                        <h3>{{ formatCurrency(totalGrossSalary) }}</h3>
                    </div>
                </div>
                <div class="metric-block">
                    <div class="m-icon icon-red"><i class="fa-solid fa-percentage"></i></div>
                    <div class="m-data">
                        <label>Total Deductions</label>
                        <h3>{{ formatCurrency(totalDeductions) }}</h3>
                    </div>
                </div>
                <div class="metric-block">
                    <div class="m-icon icon-purple"><i class="fa-solid fa-circle-check"></i></div>
                    <div class="m-data">
                        <label>Net Payroll</label>
                        <h3 class="highlight-net">{{ formatCurrency(totalNetSalary) }}</h3>
                    </div>
                </div>
                <!-- <div class="metric-block">
                    <div class="m-icon icon-amber"><i class="fa-solid fa-calculator"></i></div>
                    <div class="m-data">
                        <label>Median Balance</label>
                        <h3>{{ formatCurrency(averageSalary) }}</h3>
                    </div>
                </div> -->
            </div>

            <div class="payroll-cards-grid mt-5 mb-5">
                <div v-for="(payroll, index) in payrollData" 
                     :key="payroll.staff_id" 
                     class="modern-payroll-card is-open"
                     :class="[getRowTheme(payroll.staff_id), { 'is-open': expandedCards[payroll.staff_id] }]"
                     :style="{ animationDelay: `${index * 0.05}s` }">
                    
                    <div class="card-main-row" @click="toggleExpand(payroll.staff_id)">
                        <div class="profile-identity">
                            <div class="avatar-initials">
                                {{ payroll.staff_name?.charAt(0) || 'S' }}
                            </div>
                            <div class="identity-text">
                                <h4>{{ payroll.staff_name }}</h4>
                                <span class="sub-id">ID: #{{ payroll.staff_id }}</span>
                            </div>
                        </div>

                        <div class="attendance-inline-summary">
                            <span class="days-pill">
                                <i class="fa-regular fa-clock"></i> {{ payroll.present_days }} / {{ payroll.working_days }} Days Active
                            </span>
                        </div>

                        <div class="financial-quick-look">
                            <div class="quick-val">
                                <label>Net Pay</label>
                                <span class="net-accent">{{ formatCurrency(payroll.net_salary) }}</span>
                            </div>
                            <div class="expansion-trigger-indicator">
                                <i class="fa-solid fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>

                    <div class="card-expanded-content">
                        <div class="inner-details-grid">
                            
                            <div class="details-pane clear-pane">
                                <h5 class="pane-headline text-emerald-heading"><i class="fa-solid fa-circle-plus"></i> Base & Allowances</h5>
                                <div class="pane-rows">
                                    <div class="p-row"><span>Basic Salary</span><strong>{{ formatCurrency(payroll.basic_salary) }}</strong></div>
                                    <div class="p-row"><span>Transport Allocation</span><span>{{ formatCurrency(payroll.transport_allowance) }}</span></div>
                                    <div class="p-row"><span>Hardware & Computer Allowance</span><span>{{ formatCurrency(payroll.computer_allowance) }}</span></div>
                                    <div class="p-row"><span>Mobile Connectivity Stipend</span><span>{{ formatCurrency(payroll.mobile_allowance) }}</span></div>
                                    <div class="p-row"><span>Recreational Allocation</span><span>{{ formatCurrency(payroll.recreation_allowance) }}</span></div>
                                </div>
                            </div>

                            <div class="details-pane clear-pane">
                                <h5 class="pane-headline text-rose-heading"><i class="fa-solid fa-circle-minus"></i> Regulatory & Internal Deductions</h5>
                                <div class="pane-rows">
                                    <div class="p-row"><span>Gross Salary</span><span class="text-danger">{{ formatCurrency(payroll.total_absent_deduction) }}</span></div>
                                    <div class="p-row"><span>Fine Deduction</span><span class="text-danger">{{ formatCurrency(payroll.fine_deduction) }}</span></div>
                                    <div class="p-row"><span>Late Fine Deduction</span><span class="text-danger">{{ formatCurrency(payroll.late_fine_deduction) }}</span></div>
                                    <div class="p-row"><span>Salary Tax</span><span class="text-danger">{{ formatCurrency(payroll.salary_tax) }}</span></div>
                                    <div class="p-row"><span>Security Deductions</span><span class="text-danger">{{ formatCurrency(payroll.security_deduction) }}</span></div>
                                </div>
                            </div>

                            <div class="details-pane unified-pane">
                                <h5 class="pane-headline"><i class="fa-solid fa-sliders"></i> Time Logging</h5>
                                <div class="pane-compact-stats">
                                    <div class="cs-block"><span class="lbl">Present</span><span class="val bold text-success">{{ payroll.present_days }}</span></div>
                                    <div class="cs-block"><span class="lbl">Absent</span><span class="val bold text-danger">{{ payroll.absent_days }}</span></div>
                                    <div class="cs-block"><span class="lbl">Leave</span><span class="val bold text-warning">{{ payroll.leave_days }}</span></div>
                                    <div class="cs-block"><span class="lbl">Gazetted</span><span class="val bold">{{ payroll.gazetted_leaves_count }}</span></div>
                                </div>
                            </div>

                            <div class="details-pane unified-pane">
                                <h5 class="pane-headline"><i class="fa-solid fa-scale-balanced"></i> Reconciliations</h5>
                                <div class="pane-rows">
                                    <div class="p-row"><span>Misc Payments</span><span class="text-success">{{ formatCurrency(payroll.miscellaneous_payment) }}</span></div>
                                    <div class="p-row"><span>Security Refund Release</span><span class="text-success">{{ formatCurrency(payroll.security_refund) }}</span></div>
                                </div>
                            </div>

                            <div class="details-pane system-pane full-width-pane" v-if="payroll?.applyed_leave_requests?.length">
                                <h5 class="pane-headline"><i class="fa-solid fa-envelope-open-text"></i> Leave Request Auditing</h5>
                                <div class="leave-badges-flex">
                                    <div v-for="leaves in payroll.applyed_leave_requests" 
                                         :key="leaves.status" 
                                         :class="['status-chip', getStatusBadge(leaves.status)]">
                                        <span class="chip-dot"></span>
                                        <span class="chip-label">{{ leaves.status }}: <strong>{{ leaves.total }}</strong></span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card-interactive-footer">
                        <div class="f-meta"><i class="fa-solid fa-shield-halved"></i> Immutable Ledger Record</div>
                        <div class="f-badge"><i class="fa-solid fa-circle-check"></i> System Audited</div>
                    </div>

                </div>
            </div>
        </div>

        <div v-else class="corporate-empty-wrapper">
            <div class="empty-state-panel">
                <div class="empty-vector-container">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <h3>No Payroll Records Discovered</h3>
                <p>The payroll query for your designated date target returned zero balances. Verify your configuration variables or trigger an update.</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:deep() {
    font-family: 'Inter', sans-serif;
    box-sizing: border-box;
}

.payroll-dashboard-header {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

.context-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #475569;
    text-transform: uppercase;
    background: #f1f5f9;
    padding: 0.35rem 0.75rem;
    border-radius: 6px;
    margin-bottom: 0.5rem;
}

.live-indicator {
    width: 6px;
    height: 6px;
    background: #10b981;
    border-radius: 50%;
}

.page-title {
    font-size: 1.6rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.02em;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.date-badge {
    font-size: 0.9rem;
    font-weight: 500;
    color: #4f46e5;
    background: #e0e7ff;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
}

.subtitle-text {
    font-size: 0.875rem;
    color: #64748b;
    margin: 0.35rem 0 0 0;
}

.action-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #4f46e5;
    color: #ffffff;
    border: none;
    padding: 0.65rem 1.25rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.action-btn-primary:hover {
    background: #4338ca;
}

.metrics-summary-bar {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1rem;
    margin: 1.5rem 0;
}

@media (max-width: 1200px) {
    .metrics-summary-bar { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 768px) {
    .metrics-summary-bar { grid-template-columns: 1fr; }
}

.metric-block {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.m-icon {
    width: 44px;
    height: 44px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}
.icon-blue { background: #eff6ff; color: #2563eb; }
.icon-green { background: #ecfdf5; color: #059669; }
.icon-red { background: #fff1f2; color: #e11d48; }
.icon-purple { background: #f5f3ff; color: #7c3aed; }
.icon-amber { background: #fffbeb; color: #d97706; }

.m-data label {
    display: block;
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 500;
    margin-bottom: 0.15rem;
}

.m-data h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}
.m-data .highlight-net {
    color: #7c3aed;
}

.payroll-cards-grid {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.modern-payroll-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    /* overflow: hidden; */
    transition: box-shadow 0.2s, border-color 0.2s;
}

.modern-payroll-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
}

.card-main-row {
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    user-select: none;
    gap: 1.5rem;
}

.profile-identity {
    display: flex;
    align-items: center;
    gap: 1rem;
    min-width: 240px;
}

.avatar-initials {
    width: 40px;
    height: 40px;
    background: #f1f5f9;
    color: #334155;
    font-weight: 700;
    font-size: 0.95rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e2e8f0;
}

.identity-text h4 {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 600;
    color: #0f172a;
}

.identity-text .sub-id {
    font-size: 0.75rem;
    color: #94a3b8;
}

.days-pill {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    font-size: 0.8rem;
    color: #475569;
    font-weight: 500;
}

.financial-quick-look {
    display: flex;
    align-items: center;
    gap: 2rem;
}

.quick-val {
    text-align: right;
}

.quick-val label {
    display: block;
    font-size: 0.7rem;
    color: #94a3b8;
    text-transform: uppercase;
}

.quick-val .net-accent {
    font-size: 1.05rem;
    font-weight: 700;
    color: #10b981;
}

.expansion-trigger-indicator {
    color: #94a3b8;
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.modern-payroll-card.is-open .expansion-trigger-indicator {
    transform: rotate(180deg);
    color: #4f46e5;
}

.card-expanded-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    background: #fafafa;
    border-top: 1px dashed transparent;
}

.modern-payroll-card.is-open .card-expanded-content {
    max-height: 1200px;
    border-top-color: #e2e8f0;
}

.inner-details-grid {
    padding: 1.5rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
}

@media (max-width: 768px) {
    .inner-details-grid { grid-template-columns: 1fr; }
}

.details-pane {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1rem;
}

.pane-headline {
    margin: 0 0 0.85rem 0;
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: #334155;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.text-emerald-heading { color: #059669 !important; }
.text-rose-heading { color: #e11d48 !important; }

.pane-rows {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.p-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.85rem;
    color: #475569;
    padding-bottom: 0.35rem;
    border-bottom: 1px solid #f1f5f9;
}

.p-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.pane-compact-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.5rem;
    text-align: center;
}

.cs-block {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 0.5rem;
}

.cs-block .lbl {
    display: block;
    font-size: 0.7rem;
    color: #64748b;
    margin-bottom: 0.15rem;
}

.cs-block .val {
    font-size: 0.9rem;
    font-weight: 600;
}

.full-width-pane {
    grid-column: 1 / -1;
}

.leave-badges-flex {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.status-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
}

.chip-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

.badge-pending { background: #fef3c7; color: #d97706; }
.badge-pending .chip-dot { background: #d97706; }

.badge-approved { background: #dcfce7; color: #15803d; }
.badge-approved .chip-dot { background: #15803d; }

.badge-rejected { background: #ffe4e6; color: #b91c1c; }
.badge-rejected .chip-dot { background: #b91c1c; }

.card-interactive-footer {
    padding: 0.65rem 1.5rem;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    font-size: 0.75rem;
    color: #94a3b8;
}

.card-interactive-footer .f-badge {
    color: #10b981;
    font-weight: 600;
}

.theme-primary { border-left: 3px solid #4f46e5; }
.theme-indigo { border-left: 3px solid #06b6d4; }
.theme-emerald { border-left: 3px solid #10b981; }
.theme-amber { border-left: 3px solid #f59e0b; }

.text-success { color: #10b981; }
.text-danger { color: #ef4444; }
.text-warning { color: #f59e0b; }

    /* ===== Corporate Empty State Layer ===== */
.corporate-empty-wrapper {
    padding: 4rem 1rem;
    display: flex;
    justify-content: center;
}

.empty-state-panel {
    text-align: center;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 3rem 2rem;
    max-width: 440px;
}

.empty-vector-container {
    font-size: 2.5rem;
    color: #cbd5e1;
    margin-bottom: 1rem;
}

.empty-state-panel h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
    color: #0f172a;
    font-weight: 600;
}

.empty-state-panel p {
    margin: 0;
    font-size: 0.875rem;
    color: #64748b;
    line-height: 1.5;
}
</style>    