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
            <span class="badge ph-match-badge" id="historyCountBadge" style="display: none;"></span>
        </div>

        <div class="card-body p-0" id="historyListContainer">
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
                    <tbody id="historyTableBody">
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card Stack View -->
            <div class="d-block d-md-none p-3">
                <div class="row g-3" id="historyMobileCards">
                </div>
            </div>
        </div>

        <!-- Pagination Block -->
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-center" id="paginationWrapper">
        </div>
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
    let currentPage = 1;
    let isFetching = false;

    const historyTableBody = document.getElementById('historyTableBody');
    const historyMobileCards = document.getElementById('historyMobileCards');
    const historyListContainer = document.getElementById('historyListContainer');
    const historyFilterForm = document.getElementById('historyFilterForm');
    const paginationWrapper = document.getElementById('paginationWrapper');
    const historyCountBadge = document.getElementById('historyCountBadge');

    function fetchHistory() {
        if (isFetching) return;
        isFetching = true;

        const form = document.getElementById('historyFilterForm');
        const search = form.querySelector('[name="search"]').value.trim();
        const fromDate = form.querySelector('[name="from_date"]').value;
        const toDate = form.querySelector('[name="to_date"]').value;

        // Show loading indicator
        if (historyTableBody) {
            historyTableBody.innerHTML = `
                <tr class="loading-row">
                    <td colspan="6" class="text-center py-5 text-muted">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <div class="spinner-border text-teal mb-2" role="status" style="width: 2.5rem; height: 2.5rem; color: #0f766e;"></div>
                            <span>Loading history...</span>
                        </div>
                    </td>
                </tr>
            `;
        }

        if (historyMobileCards) {
            historyMobileCards.innerHTML = `
                <div class="col-12 text-center py-5 text-muted">
                    <div class="spinner-border text-teal mb-2" role="status" style="width: 2.5rem; height: 2.5rem; color: #0f766e;"></div>
                    <div>Loading history...</div>
                </div>
            `;
        }

        const url = new URL('<?php echo base_url("doctor/history"); ?>');
        url.searchParams.append('ajax', '1');
        url.searchParams.append('page', currentPage);
        if (search) url.searchParams.append('search', search);
        if (fromDate) url.searchParams.append('from_date', fromDate);
        if (toDate) url.searchParams.append('to_date', toDate);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                isFetching = false;
                if (data.status === 'success') {
                    renderTable(data.prescriptions, data.current_page, data.total_rows, data.limit, data.total_pages);
                    updateStats(data.stats);
                }
            })
            .catch(error => {
                isFetching = false;
                console.error('Error fetching prescription history:', error);
                const errorHtml = `
                    <tr class="error-row">
                        <td colspan="6" class="text-center py-5 text-danger">
                            <i class="bi bi-exclamation-triangle-fill fs-2"></i>
                            <p class="mt-2 mb-0">Failed to load history. Please try again.</p>
                        </td>
                    </tr>
                `;
                if (historyTableBody) historyTableBody.innerHTML = errorHtml;
                if (historyMobileCards) {
                    historyMobileCards.innerHTML = `
                        <div class="col-12 text-center py-5 text-danger">
                            <i class="bi bi-exclamation-triangle-fill fs-2"></i>
                            <p class="mt-2 mb-0">Failed to load history. Please try again.</p>
                        </div>
                    `;
                }
            });
    }

    function renderTable(prescriptions, currentPage, totalRows, limit, totalPages) {
        const form = document.getElementById('historyFilterForm');
        const search = form.querySelector('[name="search"]').value.trim();

        if (historyCountBadge) {
            if (totalRows > 0) {
                historyCountBadge.textContent = `Found ${totalRows} matching record${totalRows === 1 ? '' : 's'}`;
                historyCountBadge.style.display = 'inline-block';
            } else {
                historyCountBadge.style.display = 'none';
            }
        }

        if (!prescriptions || prescriptions.length === 0) {
            const emptyHtml = `
                <div class="card-body py-5 text-center w-100">
                    <div class="ph-empty-icon"><i class="bi bi-inbox-fill" style="font-size: 3rem; color: #cbd5e1;"></i></div>
                    <h4 class="fw-bold text-dark mt-2">No Prescriptions Found</h4>
                    <p class="text-secondary small mb-4">
                        ${search ? 'No prescription records match your search criteria. Try a different query or clear the filter.' : 'No prescriptions have been finalized yet. Visit the Prescription Desk to create one.'}
                    </p>
                    ${search ? `<button type="button" class="btn btn-teal px-4 fw-semibold" id="clearFiltersBtn"><i class="bi bi-arrow-left"></i> Clear Filter</button>` : `
                    <a href="<?php echo base_url('doctor/prescription-desk'); ?>" class="btn btn-teal px-4 fw-semibold"><i class="bi bi-plus-lg"></i> Go to Prescription Desk</a>`}
                </div>
            `;
            if (historyListContainer) {
                historyListContainer.innerHTML = emptyHtml;
            }
            if (paginationWrapper) {
                paginationWrapper.innerHTML = '';
            }
            
            // Add clear filters click listener
            const clearBtn = document.getElementById('clearFiltersBtn');
            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    form.reset();
                    currentPage = 1;
                    fetchHistory();
                });
            }
            return;
        }

        // Restore list container if it was empty state
        if (historyListContainer && !historyListContainer.querySelector('.table-responsive')) {
            historyListContainer.innerHTML = `
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
                        <tbody id="historyTableBody">
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card Stack View -->
                <div class="d-block d-md-none p-3">
                    <div class="row g-3" id="historyMobileCards">
                    </div>
                </div>
            `;
        }

        const tbody = document.getElementById('historyTableBody');
        const mcards = document.getElementById('historyMobileCards');

        let tableHtml = '';
        let mobileHtml = '';

        prescriptions.forEach(p => {
            // Visit Date formatted
            const vdate = new Date(p.visit_date);
            const formattedDate = vdate.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            }).replace(/ /g, '-');

            tableHtml += `
                <tr>
                    <td class="fw-bold ph-invoice-cell">${escapeHtml(p.invoice_number)}</td>
                    <td class="text-secondary">${formattedDate}</td>
                    <td class="fw-semibold">${escapeHtml(p.patient_name)}</td>
                    <td class="text-secondary">${escapeHtml(p.patient_contact || '—')}</td>
                    <td class="text-center">
                        <span class="badge ph-med-badge">${escapeHtml(p.medicine_count)} Med(s)</span>
                    </td>
                    <td class="text-end">
                        <a href="<?php echo base_url('doctor/history/view-invoice/'); ?>${p.id}" class="btn btn-sm btn-outline-teal fw-semibold d-inline-flex align-items-center gap-1 ph-view-btn">
                            <i class="bi bi-eye"></i> View
                        </a>
                    </td>
                </tr>
            `;

            mobileHtml += `
                <div class="col-12">
                    <div class="ph-record-card">
                        <div class="ph-record-top">
                            <div class="ph-record-invoice-wrap">
                                <span class="ph-record-invoice">${escapeHtml(p.invoice_number)}</span>
                                <span class="ph-record-date">${formattedDate}</span>
                            </div>
                            <span class="badge ph-med-badge">${escapeHtml(p.medicine_count)} Med(s)</span>
                        </div>
                        <div class="ph-record-body">
                            <div class="ph-record-patient">${escapeHtml(p.patient_name)}</div>
                            <div class="ph-record-contact"><i class="bi bi-telephone me-1"></i> ${escapeHtml(p.patient_contact || '—')}</div>
                        </div>
                        <a href="<?php echo base_url('doctor/history/view-invoice/'); ?>${p.id}" class="btn btn-teal w-100 fw-semibold py-2 d-flex align-items-center justify-content-center gap-2 rounded-3">
                            <i class="bi bi-eye"></i> View Prescription
                        </a>
                    </div>
                </div>
            `;
        });

        if (tbody) tbody.innerHTML = tableHtml;
        if (mcards) mcards.innerHTML = mobileHtml;

        // Render pagination controls
        if (paginationWrapper) {
            if (totalPages > 1) {
                let pagHtml = `<ul class="pagination pagination-sm justify-content-center m-0">`;
                
                // First page link
                pagHtml += `<li class="page-item ${currentPage <= 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="1" style="color: #0f766e; border-color: #e2e8f0;">First</a></li>`;
                // Previous link
                pagHtml += `<li class="page-item ${currentPage <= 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage - 1}" style="color: #0f766e; border-color: #e2e8f0;"><i class="bi bi-chevron-left"></i></a></li>`;

                let pages = getPaginationPages(currentPage, totalPages);
                pages.forEach(p => {
                    if (p === '...') {
                        pagHtml += `<li class="page-item disabled"><span class="page-link" style="border-color: #e2e8f0;">…</span></li>`;
                    } else if (p === currentPage) {
                        pagHtml += `<li class="page-item active"><a class="page-link border-0 text-white" style="background-color: #0f766e;" href="#">${p}</a></li>`;
                    } else {
                        pagHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${p}" style="color: #0f766e; border-color: #e2e8f0;">${p}</a></li>`;
                    }
                });

                // Next link
                pagHtml += `<li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage + 1}" style="color: #0f766e; border-color: #e2e8f0;"><i class="bi bi-chevron-right"></i></a></li>`;
                // Last page link
                pagHtml += `<li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${totalPages}" style="color: #0f766e; border-color: #e2e8f0;">Last</a></li>`;

                pagHtml += `</ul>`;
                paginationWrapper.innerHTML = pagHtml;
            } else {
                paginationWrapper.innerHTML = '';
            }
        }
    }

    function getPaginationPages(currentPage, totalPages) {
        if (totalPages <= 7) {
            let pages = [];
            for (let i = 1; i <= totalPages; i++) pages.push(i);
            return pages;
        }
        
        let pages = [];
        pages.push(1);
        
        let start = Math.max(2, currentPage - 1);
        let end = Math.min(totalPages - 1, currentPage + 1);
        
        if (start > 2) {
            pages.push('...');
        }
        
        for (let i = start; i <= end; i++) {
            pages.push(i);
        }
        
        if (end < totalPages - 1) {
            pages.push('...');
        }
        
        pages.push(totalPages);
        return pages;
    }

    function updateStats(stats) {
        if (!stats) return;
        
        // Update stats on the UI
        const totalPrescVal = document.querySelector('.stat-card.stat-total .stat-value');
        if (totalPrescVal) totalPrescVal.textContent = parseInt(stats.total_prescriptions).toLocaleString();
        
        const todayPrescVal = document.querySelector('.stat-card.stat-active .stat-value');
        if (todayPrescVal) todayPrescVal.textContent = parseInt(stats.prescriptions_today).toLocaleString();
        
        const patientsSeenVal = document.querySelector('.stat-card.stat-inactive .stat-value');
        if (patientsSeenVal) patientsSeenVal.textContent = parseInt(stats.unique_patients).toLocaleString();
        
        const lastVisitVal = document.querySelector('.stat-card.stat-hosp .stat-value');
        if (lastVisitVal) {
            if (stats.last_visit && stats.last_visit !== 'N/A') {
                const ldate = new Date(stats.last_visit);
                lastVisitVal.textContent = ldate.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                }).replace(/ /g, '-');
            } else {
                lastVisitVal.textContent = 'N/A';
            }
        }
    }

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

    if (historyFilterForm) {
        historyFilterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            currentPage = 1;
            fetchHistory();
        });
    }

    if (paginationWrapper) {
        paginationWrapper.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link || !link.dataset.page || link.parentElement.classList.contains('disabled')) return;
            e.preventDefault();
            currentPage = parseInt(link.dataset.page);
            fetchHistory();
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Load initial history records
    fetchHistory();
</script>