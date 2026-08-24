<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="doctor-history-page py-4">

    <!-- Header / Navigation Info -->
    <div class="ph-page-header mb-4">
        <div>
            <div class="ph-eyebrow"><i class="bi bi-clock-history"></i> Clinical Portal</div>
            <h2 class="ph-page-title">Prescription History</h2>
            <p class="ph-page-subtitle">View and search all previous prescription regimens and printed invoices.</p>
        </div>
        <a href="<?php echo base_url('doctor/prescription-desk'); ?>" class="btn btn-teal ph-new-btn fw-semibold d-inline-flex align-items-center justify-content-center gap-2 shadow-sm">
            <i class="bi bi-plus-lg"></i> New Prescription
        </a>
    </div>

    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?php echo $this->session->flashdata('success'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $this->session->flashdata('error'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Stat Cards Section -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="ph-stat-card">
                <div class="ph-stat-icon"><i class="bi bi-file-earmark-medical"></i></div>
                <div class="ph-stat-body">
                    <span class="ph-stat-label">Total Prescriptions</span>
                    <h3 class="ph-stat-value"><?php echo html_escape($stats['total_prescriptions']); ?></h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="ph-stat-card">
                <div class="ph-stat-icon"><i class="bi bi-calendar-check"></i></div>
                <div class="ph-stat-body">
                    <span class="ph-stat-label">Prescriptions Today</span>
                    <h3 class="ph-stat-value"><?php echo html_escape($stats['prescriptions_today']); ?></h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="ph-stat-card">
                <div class="ph-stat-icon"><i class="bi bi-people"></i></div>
                <div class="ph-stat-body">
                    <span class="ph-stat-label">Unique Patients</span>
                    <h3 class="ph-stat-value"><?php echo html_escape($stats['unique_patients']); ?></h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="ph-stat-card">
                <div class="ph-stat-icon"><i class="bi bi-clock-history"></i></div>
                <div class="ph-stat-body">
                    <span class="ph-stat-label">Last Visit</span>
                    <h3 class="ph-stat-value ph-stat-value-sm text-truncate"><?php echo html_escape($stats['last_visit']); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Validation Error -->
    <?php if (!empty($date_error)): ?>
        <div class="alert ph-alert-warning border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5 ph-alert-warning-icon"></i>
            <div>
                <strong>From Date must be before To Date</strong> &mdash; Date filter was bypassed to prevent empty results.
            </div>
        </div>
    <?php endif; ?>

    <!-- Combined Search & Date Range Filters Bar -->
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-body p-4">
            <form action="<?php echo base_url('doctor/history'); ?>" method="GET" id="historyFilterForm" class="ph-filter-form">

                <div class="ph-filter-search">
                    <label class="form-label fw-semibold text-dark small"><i class="bi bi-search me-1"></i> Search Patient / Invoice</label>
                    <div class="input-group">
                        <span class="input-group-text ph-input-icon"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control ph-input" name="search" value="<?php echo html_escape($search); ?>" placeholder="Search by patient name or invoice...">
                    </div>
                </div>

                <div class="ph-filter-row">
                    <div class="ph-filter-date">
                        <label class="form-label fw-semibold text-dark small"><i class="bi bi-calendar-event me-1"></i> From Date</label>
                        <input type="date" class="form-control ph-input" name="from_date" value="<?php echo html_escape($from_date ?? ''); ?>">
                    </div>

                    <div class="ph-filter-date">
                        <label class="form-label fw-semibold text-dark small"><i class="bi bi-calendar-event me-1"></i> To Date</label>
                        <input type="date" class="form-control ph-input" name="to_date" value="<?php echo html_escape($to_date ?? ''); ?>">
                    </div>

                    <div class="ph-filter-actions">
                        <span class="ph-filter-actions-spacer d-none d-lg-block">&nbsp;</span>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-teal fw-semibold flex-fill d-inline-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-filter"></i> Apply
                            </button>
                            <button type="button" onclick="triggerExcelExport()" class="btn btn-outline-teal fw-semibold flex-fill d-inline-flex align-items-center justify-content-center gap-1" title="Export current view to Excel">
                                <i class="bi bi-file-earmark-excel"></i> Export
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Filter Status Indicators -->
            <?php if (!empty($search) || !empty($from_date) || !empty($to_date)): ?>
                <div class="ph-filter-status">
                    <div class="ph-filter-chips">
                        <span class="ph-chips-label">Active Filters:</span>
                        <?php if (!empty($search)): ?>
                            <span class="badge ph-chip">Search: "<?php echo html_escape($search); ?>"</span>
                        <?php endif; ?>
                        <?php if (!empty($from_date)): ?>
                            <span class="badge ph-chip">From: <?php echo html_escape($from_date); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($to_date)): ?>
                            <span class="badge ph-chip">To: <?php echo html_escape($to_date); ?></span>
                        <?php endif; ?>
                    </div>
                    <a href="<?php echo base_url('doctor/history'); ?>" class="ph-clear-filters">
                        <i class="bi bi-x-circle-fill me-1"></i> Clear All Filters
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- History List Card -->
    <div class="card border-0 rounded-4 shadow-sm overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-journal-medical ph-icon-teal"></i>
                <span>Prescription Records</span>
            </h5>
            <?php if (!empty($search)): ?>
                <span class="badge ph-match-badge">
                    Found <?php echo $total_count; ?> matching records
                </span>
            <?php endif; ?>
        </div>

        <?php if (empty($prescriptions)): ?>
            <!-- Empty State -->
            <div class="card-body py-5 text-center">
                <div class="ph-empty-icon"><i class="bi bi-inbox-fill"></i></div>
                <h4 class="fw-bold text-dark">No Prescriptions Found</h4>
                <p class="text-secondary small mb-4">
                    <?php if (!empty($search)): ?>
                        No prescription records match your search criteria. Try a different query or clear the filter.
                    <?php else: ?>
                        No prescriptions have been finalized yet. Visit the Prescription Desk to create one.
                    <?php endif; ?>
                </p>
                <?php if (!empty($search)): ?>
                    <a href="<?php echo base_url('doctor/history'); ?>" class="btn btn-teal px-4 fw-semibold"><i class="bi bi-arrow-left"></i> Clear Filter</a>
                <?php else: ?>
                    <a href="<?php echo base_url('doctor/prescription-desk'); ?>" class="btn btn-teal px-4 fw-semibold"><i class="bi bi-plus-lg"></i> Go to Prescription Desk</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Responsive Table & Stacked Grid -->
            <div class="card-body p-0">
                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle mb-0 ph-table">
                        <thead>
                            <tr>
                                <th style="width: 15%;">Invoice No.</th>
                                <th style="width: 15%;">Visit Date</th>
                                <th style="width: 30%;">Patient Name</th>
                                <th style="width: 18%;">Contact Number</th>
                                <th style="width: 12%;" class="text-center"># Medicines</th>
                                <th style="width: 10%;" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prescriptions as $p): ?>
                                <tr>
                                    <td class="fw-bold ph-invoice-cell"><?php echo html_escape($p['invoice_number']); ?></td>
                                    <td class="text-secondary"><?php echo date('d-M-Y', strtotime($p['visit_date'])); ?></td>
                                    <td class="fw-semibold"><?php echo html_escape($p['patient_name']); ?></td>
                                    <td class="text-secondary"><?php echo html_escape($p['patient_contact'] ?: '—'); ?></td>
                                    <td class="text-center">
                                        <span class="badge ph-med-badge"><?php echo html_escape($p['medicine_count']); ?> Med(s)</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?php echo base_url('doctor/history/view-invoice/' . $p['id']); ?>" class="btn btn-sm btn-outline-teal fw-semibold d-inline-flex align-items-center gap-1 ph-view-btn">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card Stack View -->
                <div class="d-block d-md-none p-3">
                    <div class="row g-3">
                        <?php foreach ($prescriptions as $p): ?>
                            <div class="col-12">
                                <div class="ph-record-card">
                                    <div class="ph-record-top">
                                        <div class="ph-record-invoice-wrap">
                                            <span class="ph-record-invoice"><?php echo html_escape($p['invoice_number']); ?></span>
                                            <span class="ph-record-date"><?php echo date('d-M-Y', strtotime($p['visit_date'])); ?></span>
                                        </div>
                                        <span class="badge ph-med-badge"><?php echo html_escape($p['medicine_count']); ?> Med(s)</span>
                                    </div>
                                    <div class="ph-record-body">
                                        <div class="ph-record-patient"><?php echo html_escape($p['patient_name']); ?></div>
                                        <div class="ph-record-contact"><i class="bi bi-telephone me-1"></i> <?php echo html_escape($p['patient_contact'] ?: '—'); ?></div>
                                    </div>
                                    <a href="<?php echo base_url('doctor/history/view-invoice/' . $p['id']); ?>" class="btn btn-teal w-100 fw-semibold py-2 d-flex align-items-center justify-content-center gap-2 rounded-3">
                                        <i class="bi bi-eye"></i> View Prescription
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Pagination Block -->
            <?php if (!empty($pagination)): ?>
                <div class="card-footer bg-white py-3 border-top d-flex justify-content-center">
                    <?php echo $pagination; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<style>
    /* All colors below are unchanged from the original page — only structure, spacing and
       invalid Bootstrap utility classes (e.g. mb-0.5, gap-1.5, px-2.5) have been fixed. */
    :root {
        --ph-teal-900: #0f766e;
        --ph-teal-700: #0d9488;
        --ph-teal-100: #ccfbf1;
        --ph-teal-050: #f0fdfa;
        --ph-amber-bg: #fffbeb;
        --ph-amber-text: #b45309;
        --ph-amber-icon: #d97706;
        --ph-slate-600: #475569;
        --ph-slate-500: #64748b;
        --ph-slate-300: #cbd5e1;
        --ph-slate-200: #e2e8f0;
        --ph-slate-100: #f1f5f9;
        --ph-slate-050: #f8fafc;
    }

    .doctor-history-page {
        max-width: 1280px;
        margin: 0 auto;
    }

    /* ---------- Buttons (colors unchanged) ---------- */
    .btn-teal {
        background-color: var(--ph-teal-900);
        border-color: var(--ph-teal-900);
        color: #ffffff;
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .btn-teal:hover,
    .btn-teal:focus {
        background-color: var(--ph-teal-700);
        border-color: var(--ph-teal-700);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 8px 16px -6px rgba(13, 148, 136, 0.35);
    }

    .btn-outline-teal {
        color: var(--ph-teal-900);
        border-color: var(--ph-teal-900);
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .btn-outline-teal:hover {
        background-color: var(--ph-teal-900);
        border-color: var(--ph-teal-900);
        color: #ffffff;
    }

    .ph-icon-teal {
        color: var(--ph-teal-900);
    }

    /* ---------- Page header ---------- */
    .ph-page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .ph-eyebrow {
        color: var(--ph-teal-900);
        font-weight: 600;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .ph-page-title {
        font-weight: 700;
        font-size: clamp(1.3rem, 1.05rem + 1vw, 1.75rem);
        color: #1e293b;
        margin-bottom: 4px;
    }

    .ph-page-subtitle {
        font-size: 0.9rem;
        color: var(--ph-slate-500);
        margin-bottom: 0;
        max-width: 520px;
    }

    .ph-new-btn {
        padding: 0.65rem 1.4rem;
    }

    /* ---------- Stat cards ---------- */
    .ph-stat-card {
        background: #fff;
        border: 0;
        border-radius: 16px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
        padding: 1.25rem;
        height: 100%;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .ph-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px -14px rgba(15, 23, 42, 0.2);
    }

    .ph-stat-icon {
        width: 52px;
        height: 52px;
        flex-shrink: 0;
        border-radius: 12px;
        background-color: var(--ph-teal-050);
        color: var(--ph-teal-900);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .ph-stat-body {
        min-width: 0;
    }

    .ph-stat-label {
        display: block;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--ph-slate-500);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 2px;
    }

    .ph-stat-value {
        font-weight: 700;
        color: #1e293b;
        font-size: 1.6rem;
        margin-bottom: 0;
        line-height: 1.2;
    }

    .ph-stat-value-sm {
        font-size: 1.05rem;
    }

    /* ---------- Warning alert ---------- */
    .ph-alert-warning {
        background-color: var(--ph-amber-bg);
        color: var(--ph-amber-text);
    }

    .ph-alert-warning-icon {
        color: var(--ph-amber-icon);
    }

    /* ---------- Filter bar ---------- */
    .ph-input-icon {
        background-color: var(--ph-slate-050);
        color: var(--ph-teal-900);
        border-color: var(--ph-slate-200);
    }

    .ph-input {
        background-color: var(--ph-slate-050);
        border-color: var(--ph-slate-200);
        font-size: 0.92rem;
    }

    .ph-input:focus {
        background-color: #fff;
        border-color: var(--ph-teal-700);
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.14);
    }

    .ph-filter-form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .ph-filter-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1.3fr;
        gap: 1rem;
        align-items: end;
    }

    .ph-filter-actions-spacer {
        display: block;
        line-height: 1.4;
        margin-bottom: 0.25rem;
        font-size: 0.8rem;
    }

    /* ---------- Filter status / chips ---------- */
    .ph-filter-status {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--ph-slate-100);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .ph-filter-chips {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        color: var(--ph-slate-500);
    }

    .ph-chips-label {
        font-weight: 500;
    }

    .ph-chip {
        background-color: var(--ph-slate-050);
        color: #1e293b;
        border: 1px solid var(--ph-slate-200);
        font-weight: 500;
        font-size: 0.78rem;
        padding: 5px 12px;
        border-radius: 999px;
    }

    .ph-clear-filters {
        color: var(--ph-teal-900);
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none;
        white-space: nowrap;
    }

    .ph-clear-filters:hover {
        text-decoration: underline;
    }

    /* ---------- Match badge ---------- */
    .ph-match-badge {
        background-color: var(--ph-teal-100);
        color: var(--ph-teal-900);
        font-weight: 600;
        font-size: 0.78rem;
        padding: 6px 12px;
        border-radius: 999px;
    }

    /* ---------- Table ---------- */
    .ph-table thead th {
        background-color: var(--ph-slate-050);
        color: var(--ph-slate-500);
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.9rem 1.5rem;
        border-bottom: 1px solid var(--ph-slate-100);
        white-space: nowrap;
    }

    .ph-table tbody td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--ph-slate-100);
        color: #1e293b;
        font-size: 0.9rem;
        vertical-align: middle;
    }

    .ph-table tbody tr:last-child td {
        border-bottom: none;
    }

    .ph-table tbody tr:hover {
        background-color: var(--ph-teal-050);
    }

    .ph-invoice-cell {
        color: var(--ph-teal-900);
    }

    .ph-med-badge {
        background-color: var(--ph-teal-100);
        color: var(--ph-teal-900);
        font-weight: 600;
        font-size: 0.78rem;
        padding: 0.35em 0.85em;
        border-radius: 999px;
    }

    .ph-view-btn {
        padding: 0.35rem 0.85rem;
        border-radius: 8px;
    }

    /* ---------- Empty state ---------- */
    .ph-empty-icon {
        font-size: 3.2rem;
        color: var(--ph-slate-300);
        margin-bottom: 0.75rem;
    }

    /* ---------- Mobile record cards ---------- */
    .ph-record-card {
        background-color: var(--ph-slate-050);
        border: 1px solid var(--ph-slate-100);
        border-radius: 14px;
        padding: 1rem;
    }

    .ph-record-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        padding-bottom: 0.75rem;
        margin-bottom: 0.75rem;
        border-bottom: 1px solid var(--ph-slate-200);
    }

    .ph-record-invoice-wrap {
        min-width: 0;
    }

    .ph-record-invoice {
        display: block;
        font-weight: 700;
        color: var(--ph-teal-900);
        font-size: 1rem;
    }

    .ph-record-date {
        display: block;
        color: var(--ph-slate-500);
        font-size: 0.8rem;
        margin-top: 2px;
    }

    .ph-record-body {
        margin-bottom: 1rem;
    }

    .ph-record-patient {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.95rem;
        margin-bottom: 4px;
    }

    .ph-record-contact {
        color: var(--ph-slate-500);
        font-size: 0.84rem;
    }

    /* ---------- Mobile ---------- */
    @media (max-width: 767.98px) {
        .doctor-history-page {
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }

        .ph-page-header {
            flex-direction: column;
            align-items: stretch;
        }

        .ph-new-btn {
            width: 100%;
        }

        .ph-filter-row {
            grid-template-columns: 1fr 1fr;
        }

        .ph-filter-actions {
            grid-column: 1 / -1;
        }

        .ph-filter-actions-spacer {
            display: none;
        }

        .ph-filter-status {
            align-items: flex-start;
        }

        .ph-clear-filters {
            align-self: flex-start;
        }
    }

    @media (max-width: 420px) {
        .ph-filter-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    function triggerExcelExport() {
        const form = document.getElementById('historyFilterForm');
        const search = form.querySelector('[name="search"]').value;
        const fromDate = form.querySelector('[name="from_date"]').value;
        const toDate = form.querySelector('[name="to_date"]').value;

        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (fromDate) params.append('from_date', fromDate);
        if (toDate) params.append('to_date', toDate);

        window.location.href = '<?php echo base_url("doctor/history/export"); ?>?' + params.toString();
    }
</script>