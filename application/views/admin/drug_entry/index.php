<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Compute Drug Statistics
$total_drugs = count($drugs);
$active_drugs = 0;
$inactive_drugs = 0;
$total_stock = 0;
$out_of_stock = 0;

foreach ($drugs as $drug) {
    if (isset($drug->is_active) && $drug->is_active == 1) {
        $active_drugs++;
    } else {
        $inactive_drugs++;
    }

    $qty = isset($drug->quantity) ? (int)$drug->quantity : 0;
    $total_stock += $qty;
    if ($qty === 0) {
        $out_of_stock++;
    }
}
?>
<!-- SweetAlert2 CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="drug-entry-page">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div>
            <div class="page-eyebrow">
                <i class="bi bi-capsule"></i> Master Formulary
            </div>
            <h2 class="page-title">Clinical Drug Registry</h2>
            <p class="page-subtitle">Maintain active drugs, stock quantities, chemical synonyms, and therapeutic categories.</p>
        </div>
        <div class="header-actions">
            <a href="<?php echo base_url('admin/drug-entry/export'); ?>" class="btn-ghost">
                <i class="bi bi-download"></i> <span>Export CSV</span>
            </a>
            <button type="button" class="btn-ghost" data-bs-toggle="modal" data-bs-target="#importDrugsModal">
                <i class="bi bi-upload"></i> <span>Import CSV</span>
            </button>
            <a href="<?php echo base_url('admin/drug-entry/add'); ?>" class="btn-primary">
                <i class="bi bi-plus-lg"></i> <span>Add New Drug</span>
            </a>
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="ddi-stats mb-4">
        <div class="stat-card stat-total">
            <div class="stat-icon"><i class="bi bi-capsule"></i></div>
            <div class="stat-info">
                <span class="stat-label">Total Drugs</span>
                <span class="stat-value"><?php echo number_format($total_drugs); ?></span>
            </div>
        </div>
        <div class="stat-card stat-active">
            <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-info">
                <span class="stat-label">Active</span>
                <span class="stat-value"><?php echo number_format($active_drugs); ?></span>
            </div>
        </div>
        <div class="stat-card stat-inactive">
            <div class="stat-icon"><i class="bi bi-pause-circle-fill"></i></div>
            <div class="stat-info">
                <span class="stat-label">Inactive</span>
                <span class="stat-value"><?php echo number_format($inactive_drugs); ?></span>
            </div>
        </div>
        <div class="stat-card stat-stock">
            <div class="stat-icon"><i class="bi bi-box-seam-fill"></i></div>
            <div class="stat-info">
                <span class="stat-label">Total Stock</span>
                <span class="stat-value"><?php echo number_format($total_stock); ?></span>
            </div>
        </div>
        <div class="stat-card stat-out">
            <div class="stat-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="stat-info">
                <span class="stat-label">Out of Stock</span>
                <span class="stat-value text-danger" style="color: #dc2626 !important;"><?php echo number_format($out_of_stock); ?></span>
            </div>
        </div>
    </div>

    <!-- Main Panel & Searchable Drugs Table -->
    <div class="panel-card">
        <!-- Filter Bar -->
        <div class="table-filterbar">
            <div class="filter-search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" id="tableSearchInput" placeholder="Filter by drug name, category, quantity, or synonym..." autocomplete="off">
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="text-secondary fw-semibold" style="font-size: 13px;">Show</span>
                <select id="pageSizeSelect" class="form-select form-select-sm" style="width: 120px; height: 40px; border-radius: 10px; border-color: #cbd5e1; font-weight: 500; font-size: 13px; cursor: pointer; box-shadow: none;">
                    <option value="10">10 rows</option>
                    <option value="25">25 rows</option>
                    <option value="50">50 rows</option>
                    <option value="100">100 rows</option>
                    <option value="-1">View All</option>
                </select>
            </div>

            <button type="button" id="clearSearchBtn" class="filter-reset-btn" title="Reset Search">
                <i class="bi bi-arrow-counterclockwise"></i> <span>Reset</span>
            </button>
        </div>

        <div class="table-responsive" style="min-height: 220px;">
            <table class="table table-hover align-middle mb-0 ddi-table" id="drugsTable">
                <thead>
                    <tr>
                        <th style="min-width: 170px; width: 22%;">Drug Name</th>
                        <th style="min-width: 140px; width: 16%;">Therapeutic Category</th>
                        <th style="min-width: 120px; width: 14%;">Quantity / Stock</th>
                        <th style="min-width: 180px; width: 26%;">Synonyms / Brands</th>
                        <th style="min-width: 90px; width: 10%;">Status</th>
                        <th class="text-end" style="min-width: 115px; width: 12%;">Actions</th>
                    </tr>
                </thead>
                <tbody id="drugsTableBody">
                    <?php if (empty($drugs)): ?>
                        <tr class="no-drugs-row">
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="empty-state">
                                    <i class="bi bi-folder-x"></i>
                                    <h3>No drugs found</h3>
                                    <p>Get started by registering your first clinical drug.</p>
                                    <a href="<?php echo base_url('admin/drug-entry/add'); ?>" class="btn-primary">
                                        <i class="bi bi-plus-lg"></i> Add New Drug
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($drugs as $drug): ?>
                            <tr class="drug-row" data-id="<?php echo $drug->id; ?>">
                                <td class="drug-name-cell">
                                    <span class="drug-chip"><i class="bi bi-capsule"></i><?php echo html_escape($drug->drug_name); ?></span>
                                </td>
                                <td class="category-cell">
                                    <?php if (!empty($drug->category)): ?>
                                        <span class="category-badge"><?php echo html_escape($drug->category); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="quantity-cell">
                                    <?php
                                    $qty = isset($drug->quantity) ? (int)$drug->quantity : 0;
                                    $unit = !empty($drug->unit) ? html_escape($drug->unit) : '';
                                    ?>
                                    <span class="stock-badge">
                                        <i class="bi bi-box-seam text-teal"></i>
                                        <?php echo number_format($qty) . ($unit !== '' ? ' ' . $unit : ''); ?>
                                    </span>
                                </td>
                                <td class="synonyms-cell">
                                    <?php
                                    if (!empty($drug->synonyms)) {
                                        $syns = explode(',', $drug->synonyms);
                                        foreach ($syns as $syn) {
                                            $syn_trimmed = trim($syn);
                                            if ($syn_trimmed !== '') {
                                                echo '<span class="synonym-chip">' . html_escape($syn_trimmed) . '</span>';
                                            }
                                        }
                                    } else {
                                        echo '<span class="text-muted small">—</span>';
                                    }
                                    ?>
                                </td>
                                <td class="status-cell">
                                    <?php if ($drug->is_active): ?>
                                        <span class="status-badge status-active"><i class="bi bi-dot"></i>Active</span>
                                    <?php else: ?>
                                        <span class="status-badge status-inactive"><i class="bi bi-dot"></i>Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end actions-cell">
                                    <div class="actions-group">
                                        <a href="<?php echo base_url('admin/drug-entry/edit/' . $drug->id); ?>" class="rule-action-btn edit-drug-btn"
                                            title="Edit Drug" aria-label="Edit Drug">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <?php if ($drug->is_active): ?>
                                            <button type="button" class="rule-action-btn btn-warning-soft deactivate-drug-btn"
                                                data-id="<?php echo $drug->id; ?>"
                                                data-name="<?php echo html_escape($drug->drug_name); ?>"
                                                title="Deactivate Drug" aria-label="Deactivate Drug">
                                                <i class="bi bi-pause-circle"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="rule-action-btn btn-success-soft activate-drug-btn"
                                                data-id="<?php echo $drug->id; ?>"
                                                data-name="<?php echo html_escape($drug->drug_name); ?>"
                                                title="Activate Drug" aria-label="Activate Drug">
                                                <i class="bi bi-play-circle"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="rule-action-btn btn-danger-soft delete-drug-btn"
                                            data-id="<?php echo $drug->id; ?>"
                                            data-name="<?php echo html_escape($drug->drug_name); ?>"
                                            title="Delete Drug" aria-label="Delete Drug">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination Wrapper -->
        <div id="paginationWrapper"></div>
    </div>
</div>

<!-- Import Drugs Modal -->
<div class="modal fade" id="importDrugsModal" tabindex="-1" aria-labelledby="importDrugsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom py-3.5 px-4 bg-white d-flex align-items-center justify-content-between">
                <h5 class="modal-title fw-bold text-dark fs-5 d-flex align-items-center gap-2" id="importDrugsModalLabel">
                    <i class="bi bi-file-earmark-spreadsheet text-teal fs-4"></i>
                    <span>Bulk Import Drugs (Excel / CSV)</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="box-shadow: none; border: 0; background: none; font-size: 20px;"><i class="bi bi-x"></i></button>
            </div>

            <form id="importDrugsForm" enctype="multipart/form-data">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" class="csrf_sync_field">

                <div class="modal-body p-4">
                    <div class="p-3 mb-3 rounded-3" style="background-color: #f0fdfa; border: 1px solid #99f6e4; font-size: 12.5px; color: #0f766e;">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                            <strong><i class="bi bi-file-earmark-spreadsheet me-1"></i> Expected Column Format:</strong>
                            <a href="<?php echo base_url('admin/drug-entry/sample_csv'); ?>" class="btn btn-sm text-white px-2.5 py-1 rounded-2 fw-semibold" style="background-color: #0f766e; font-size: 11.5px; text-decoration: none;">
                                <i class="bi bi-download me-1"></i> Download Sample CSV
                            </a>
                        </div>
                        <code>Drug Name, Synonyms, Category, Quantity, Unit</code>
                        <ul class="mb-0 mt-1.5 ps-3">
                            <li>Supports standard <strong>.xlsx (Excel)</strong> and <strong>.csv</strong> files.</li>
                            <li>Drug name is required and must be unique case-insensitively.</li>
                            <li>Quantity is required and must be a non-negative integer.</li>
                            <li>Existing duplicate drugs are safely skipped.</li>
                        </ul>
                    </div>

                    <div class="form-field">
                        <label for="csv_file_input" class="form-label fw-semibold text-dark fs-6 mb-2">Select Excel (.xlsx) or CSV File <span class="text-danger">*</span></label>
                        <input type="file" name="csv_file" id="csv_file_input" class="form-control" style="border: 1px solid #cbd5e1; background-color: #f8fafc; border-radius: 10px; padding: 10px 14px;" accept=".csv, .xlsx, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, text/csv" required>
                    </div>
                </div>

                <div class="modal-footer border-top py-3 px-4 bg-white d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white px-4 py-2 rounded-3 fw-semibold" id="importDrugsSubmitBtn" style="background-color: #0f766e; border: 1px solid #0f766e;">
                        <i class="bi bi-upload me-1"></i> Start Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .drug-entry-page,
    .drug-entry-page * {
        font-family: 'Poppins', sans-serif;
        box-sizing: border-box;
    }

    /* Stat Cards */
    .ddi-stats {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .stat-total .stat-icon {
        background: #f0fdfa;
        color: #0f766e;
    }

    .stat-active .stat-icon {
        background: #f0fdf4;
        color: #16a34a;
    }

    .stat-inactive .stat-icon {
        background: #fffbeb;
        color: #d97706;
    }

    .stat-stock .stat-icon {
        background: #eff6ff;
        color: #2563eb;
    }

    .stat-out .stat-icon {
        background: #fef2f2;
        color: #dc2626;
    }

    .stat-info {
        display: flex;
        flex-direction: column;
    }

    .stat-label {
        font-size: 12px;
        font-weight: 500;
        color: #64748b;
        margin-bottom: 2px;
    }

    .stat-value {
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.1;
    }

    .page-header {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-end;
        gap: 16px;
    }

    .page-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: #0f766e;
        background: #f0fdfa;
        border: 1px solid #99f6e4;
        padding: 4px 12px;
        border-radius: 999px;
        margin-bottom: 8px;
    }

    .page-title {
        font-size: 26px;
        font-weight: 700;
        margin: 0 0 4px;
        color: #0f172a;
        line-height: 1.25;
    }

    .page-subtitle {
        font-size: 14px;
        color: #64748b;
        margin: 0;
    }

    .header-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
    }

    .btn-primary,
    .btn-ghost {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 12px;
        height: 44px;
        padding: 0 20px;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all .15s ease;
        white-space: nowrap;
        text-decoration: none;
        box-sizing: border-box;
        line-height: 1;
    }

    .btn-primary {
        background: #0f766e !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(15, 118, 110, .20);
    }

    .btn-primary:hover {
        background: #0c5f59 !important;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(15, 118, 110, .30);
    }

    .btn-ghost {
        background: #ffffff !important;
        color: #475569 !important;
        border-color: #cbd5e1 !important;
    }

    .btn-ghost:hover {
        background: #f8fafc !important;
        color: #0f172a !important;
        border-color: #94a3b8 !important;
        transform: translateY(-1px);
    }

    /* Panel Card & Filterbar */
    .panel-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
    }

    .table-filterbar {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        background: #ffffff;
        flex-wrap: wrap;
    }

    .filter-search-wrap {
        flex: 1 1 260px;
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 0 14px;
        height: 40px;
        max-height: 40px;
    }

    .filter-search-wrap i {
        color: #94a3b8;
        font-size: 14px;
        flex-shrink: 0;
    }

    .filter-search-wrap input {
        border: none;
        background: transparent;
        outline: none;
        width: 100%;
        height: 100%;
        font-size: 13.5px;
        color: #0f172a;
    }

    .filter-reset-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 0 14px;
        height: 40px;
        max-height: 40px;
        font-size: 13px;
        font-weight: 500;
        color: #64748b;
        cursor: pointer;
        transition: all .15s ease;
    }

    .filter-reset-btn:hover {
        color: #0f766e;
        border-color: #0f766e;
    }

    /* Table Styles */
    .ddi-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
    }

    .ddi-table thead th {
        background-color: #fbfcfe;
        color: #64748b;
        font-size: 11.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
        padding: 14px 18px;
        border-bottom: 1px solid #e2e8f0;
        border-top: none;
        white-space: nowrap;
    }

    .ddi-table tbody tr {
        transition: background-color .15s ease;
        border-bottom: 1px solid #f1f5f9;
    }

    .ddi-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .ddi-table tbody td {
        padding: 13px 18px;
        vertical-align: middle;
        font-size: 13.5px;
        border-bottom: 1px solid #f1f5f9;
    }

    .drug-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        font-size: 13.5px;
        color: #0f172a;
        white-space: nowrap;
    }

    .drug-chip i {
        color: #0f766e;
        font-size: 12px;
        background: #f0fdfa;
        border: 1px solid #99f6e4;
        width: 24px;
        height: 24px;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .category-badge {
        display: inline-flex;
        font-size: 12px;
        font-weight: 500;
        padding: 3px 9px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
    }

    .stock-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12.5px;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #0f172a;
    }

    .synonym-chip {
        display: inline-flex;
        font-size: 11.5px;
        padding: 2px 7px;
        border-radius: 6px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #64748b;
        margin: 2px 4px 2px 0;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        font-size: 12px;
        font-weight: 600;
        padding: 3px 9px 3px 5px;
        border-radius: 999px;
        white-space: nowrap;
    }

    .status-badge i {
        font-size: 20px;
        line-height: 0;
    }

    .status-active {
        background: #ecfdf5;
        color: #059669;
    }

    .status-inactive {
        background: #fef2f2;
        color: #dc2626;
    }

    /* Actions */
    .actions-group {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        justify-content: flex-end;
        flex-nowrap: nowrap;
    }

    .rule-action-btn {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        cursor: pointer;
        transition: all .15s ease;
        padding: 0;
        line-height: 1;
        flex-shrink: 0;
        text-decoration: none;
    }

    .rule-action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, .08);
    }

    .rule-action-btn.edit-drug-btn {
        color: #0f766e;
        border-color: #99f6e4;
        background: #f0fdfa;
    }

    .rule-action-btn.edit-drug-btn:hover {
        color: #ffffff;
        background: #0d9488;
        border-color: #0d9488;
    }

    .rule-action-btn.btn-warning-soft {
        color: #b45309;
        border-color: #fde68a;
        background: #fffbeb;
    }

    .rule-action-btn.btn-warning-soft:hover {
        color: #ffffff;
        background: #d97706;
        border-color: #d97706;
    }

    .rule-action-btn.btn-success-soft {
        color: #15803d;
        border-color: #bbf7d0;
        background: #f0fdf4;
    }

    .rule-action-btn.btn-success-soft:hover {
        color: #ffffff;
        background: #16a34a;
        border-color: #16a34a;
    }

    .rule-action-btn.btn-danger-soft {
        color: #dc2626;
        border-color: #fecaca;
        background: #fef2f2;
    }

    .rule-action-btn.btn-danger-soft:hover {
        color: #ffffff;
        background: #dc2626;
        border-color: #dc2626;
    }

    .empty-state {
        text-align: center;
        padding: 48px 20px;
        color: #64748b;
    }

    .empty-state i {
        font-size: 40px;
        color: #94a3b8;
        display: block;
        margin-bottom: 12px;
    }

    .empty-state h3 {
        font-size: 16px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .empty-state p {
        font-size: 13px;
        margin-bottom: 16px;
        max-width: 360px;
        margin-left: auto;
        margin-right: auto;
    }

    @media (max-width: 1200px) {
        .ddi-stats {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .ddi-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .ddi-stats {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767px) {
        .page-header {
            flex-direction: column;
            align-items: stretch;
            gap: 14px;
        }

        .header-actions .btn-primary {
            width: 100%;
            justify-content: center;
        }

        .table-filterbar {
            padding: 12px 14px;
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
        }

        .filter-search-wrap {
            flex: 0 0 40px !important;
            width: 100% !important;
            height: 40px !important;
            max-height: 40px !important;
            min-height: 40px !important;
        }

        .filter-reset-btn {
            flex: 0 0 40px !important;
            width: 100% !important;
            height: 40px !important;
            max-height: 40px !important;
            min-height: 40px !important;
        }
    }

    /* Footer Pagination */
    .ddi-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        border-top: 1px solid #e2e8f0;
        background: #fbfcfe;
        flex-wrap: wrap;
        gap: 12px;
    }

    .footer-count {
        font-size: 13px;
        color: #64748b;
    }

    .footer-count strong {
        color: #0f172a;
    }

    .ddi-pagination {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .page-nav-btn,
    .page-num-btn {
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        font-size: 13px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all .15s ease;
    }

    .page-num-btn.active {
        background: #0f766e;
        border-color: #0f766e;
        color: #ffffff;
        font-weight: 600;
    }

    .page-nav-btn.disabled {
        opacity: .4;
        pointer-events: none;
    }

    .page-ellipsis {
        padding: 0 4px;
        color: #94a3b8;
        font-size: 13px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
        window.csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

        const SwalCustom = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-primary px-4 py-2 mx-1',
                cancelButton: 'btn btn-outline-secondary px-4 py-2 mx-1'
            },
            buttonsStyling: false
        });

        const drugsTableBody = document.getElementById('drugsTableBody');
        const tableSearchInput = document.getElementById('tableSearchInput');
        const clearSearchBtn = document.getElementById('clearSearchBtn');

        // 1. Client-Side Live Search Filtering & Pagination
        let currentPage = 1;

        if (tableSearchInput) {
            tableSearchInput.addEventListener('keyup', function() {
                currentPage = 1;
                paginateTable();
            });
        }

        const pageSizeSelect = document.getElementById('pageSizeSelect');
        if (pageSizeSelect) {
            pageSizeSelect.addEventListener('change', function() {
                currentPage = 1;
                paginateTable();
            });
        }

        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', function() {
                if (tableSearchInput) {
                    tableSearchInput.value = '';
                    if (pageSizeSelect) pageSizeSelect.value = '10';
                    currentPage = 1;
                    paginateTable();
                }
            });
        }

        function paginateTable() {
            const query = tableSearchInput ? tableSearchInput.value.toLowerCase().trim() : '';
            const rows = document.querySelectorAll('#drugsTableBody tr.drug-row');

            const pageSizeSelect = document.getElementById('pageSizeSelect');
            const selectedLimit = pageSizeSelect ? parseInt(pageSizeSelect.value) : 10;
            const currentLimit = selectedLimit === -1 ? rows.length : selectedLimit;

            const matchedRows = [];
            rows.forEach(row => {
                const name = row.querySelector('.drug-name-cell') ? row.querySelector('.drug-name-cell').textContent.toLowerCase() : '';
                const category = row.querySelector('.category-cell') ? row.querySelector('.category-cell').textContent.toLowerCase() : '';
                const quantity = row.querySelector('.quantity-cell') ? row.querySelector('.quantity-cell').textContent.toLowerCase() : '';
                const synonyms = row.querySelector('.synonyms-cell') ? row.querySelector('.synonyms-cell').textContent.toLowerCase() : '';

                if (name.includes(query) || category.includes(query) || quantity.includes(query) || synonyms.includes(query)) {
                    matchedRows.push(row);
                } else {
                    row.style.display = 'none';
                }
            });

            const totalRows = matchedRows.length;
            const totalPages = Math.ceil(totalRows / currentLimit);

            if (currentPage < 1) currentPage = 1;
            if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;

            const startIndex = (currentPage - 1) * currentLimit;
            const endIndex = startIndex + currentLimit;

            matchedRows.forEach((row, idx) => {
                if (idx >= startIndex && idx < endIndex) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            const existingNoResults = document.getElementById('noResultsRow');
            if (totalRows === 0 && rows.length > 0) {
                if (!existingNoResults) {
                    const noResults = document.createElement('tr');
                    noResults.id = 'noResultsRow';
                    noResults.innerHTML = `
                        <td colspan="6" class="text-center py-5 text-muted">
                            <div class="empty-state">
                                <i class="bi bi-search"></i>
                                <h3>No matching drugs</h3>
                                <p>No registered drugs match: "<strong>${escapeHtml(query)}</strong>"</p>
                                <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5" onclick="document.getElementById('clearSearchBtn').click()">Reset Search</button>
                            </div>
                        </td>
                    `;
                    drugsTableBody.appendChild(noResults);
                } else {
                    existingNoResults.style.display = '';
                    existingNoResults.querySelector('p').innerHTML = `No registered drugs match: "<strong>${escapeHtml(query)}</strong>"`;
                }
            } else if (existingNoResults) {
                existingNoResults.style.display = 'none';
            }

            // Render pagination footer inside paginationWrapper
            const pagWrap = document.getElementById('paginationWrapper');
            if (!pagWrap) return;

            if (totalRows === 0) {
                pagWrap.innerHTML = '';
                return;
            }

            const showingTo = Math.min(startIndex + currentLimit, totalRows);
            let pagHtml = `
                <div class="ddi-footer">
                    <div class="footer-count">
                        Showing <strong>${totalRows > 0 ? startIndex + 1 : 0}</strong>–<strong>${showingTo}</strong> of <strong>${totalRows}</strong> drugs
                    </div>
            `;

            if (totalPages > 1) {
                pagHtml += `<nav class="ddi-pagination">`;
                pagHtml += `<a href="#" class="page-nav-btn ${currentPage <= 1 ? 'disabled' : ''}" data-page="${currentPage - 1}"><i class="bi bi-chevron-left"></i></a>`;

                let pages = [];
                if (totalPages <= 5) {
                    for (let i = 1; i <= totalPages; i++) pages.push(i);
                } else {
                    if (currentPage <= 3) {
                        pages.push(1, 2, 3, '...', totalPages);
                    } else if (currentPage >= totalPages - 2) {
                        pages.push(1, '...', totalPages - 2, totalPages - 1, totalPages);
                    } else {
                        pages.push(1, '...', currentPage - 1, currentPage, currentPage + 1, '...', totalPages);
                    }
                }

                pages.forEach(p => {
                    if (p === '...') {
                        pagHtml += `<span class="page-ellipsis">…</span>`;
                    } else {
                        pagHtml += `<a href="#" class="page-num-btn ${p === currentPage ? 'active' : ''}" data-page="${p}">${p}</a>`;
                    }
                });

                pagHtml += `<a href="#" class="page-nav-btn ${currentPage >= totalPages ? 'disabled' : ''}" data-page="${currentPage + 1}"><i class="bi bi-chevron-right"></i></a>`;
                pagHtml += `</nav>`;
            }

            pagHtml += `</div>`;
            pagWrap.innerHTML = pagHtml;
        }

        // Setup page navigation listener
        const paginationWrapper = document.getElementById('paginationWrapper');
        if (paginationWrapper) {
            paginationWrapper.addEventListener('click', function(e) {
                const btn = e.target.closest('a');
                if (!btn || btn.classList.contains('disabled') || !btn.dataset.page) return;
                e.preventDefault();
                currentPage = parseInt(btn.dataset.page);
                paginateTable();
            });
        }

        // 2. Delegate Actions (Deactivate, Activate, Delete)
        if (drugsTableBody) {
            drugsTableBody.addEventListener('click', function(e) {
                const deactivateBtn = e.target.closest('.deactivate-drug-btn');
                if (deactivateBtn) {
                    const id = deactivateBtn.dataset.id;
                    const name = deactivateBtn.dataset.name;
                    SwalCustom.fire({
                        title: 'Deactivate Drug?',
                        html: `Are you sure you want to deactivate <strong>"${escapeHtml(name)}"</strong>?<br><small class="text-muted">Past prescriptions will be preserved, but it will not appear in active prescription searches.</small>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Deactivate',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deactivateDrug(id);
                        }
                    });
                    return;
                }

                const activateBtn = e.target.closest('.activate-drug-btn');
                if (activateBtn) {
                    const id = activateBtn.dataset.id;
                    const name = activateBtn.dataset.name;
                    SwalCustom.fire({
                        title: 'Reactivate Drug?',
                        html: `Are you sure you want to reactivate <strong>"${escapeHtml(name)}"</strong>?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Reactivate',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            activateDrug(id);
                        }
                    });
                    return;
                }

                const deleteBtn = e.target.closest('.delete-drug-btn');
                if (deleteBtn) {
                    const id = deleteBtn.dataset.id;
                    const name = deleteBtn.dataset.name;
                    Swal.fire({
                        title: 'Permanently Delete?',
                        html: `Are you sure you want to permanently delete <strong>"${escapeHtml(name)}"</strong>?<br><small class="text-danger">This action cannot be undone and will delete the drug completely from the registry.</small>`,
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Yes, Delete Permanently',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deleteDrug(id);
                        }
                    });
                    return;
                }
            });
        }

        function deactivateDrug(id) {
            const formData = new FormData();
            formData.append(window.csrfName, window.csrfHash);

            fetch(`<?php echo base_url('admin/drug-entry/deactivate/'); ?>${id}`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    updateCsrfTokens(data.csrf_name, data.csrf_hash);
                    if (data.status === 'success') {
                        showAlert('success', data.message);
                        refreshTable();
                    } else {
                        showAlert('error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error deactivating drug:', error);
                    showAlert('error', 'An error occurred while attempting to deactivate the drug.');
                });
        }

        function activateDrug(id) {
            const formData = new FormData();
            formData.append(window.csrfName, window.csrfHash);

            fetch(`<?php echo base_url('admin/drug-entry/activate/'); ?>${id}`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    updateCsrfTokens(data.csrf_name, data.csrf_hash);
                    if (data.status === 'success') {
                        showAlert('success', data.message);
                        refreshTable();
                    } else {
                        showAlert('error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error activating drug:', error);
                    showAlert('error', 'An error occurred while attempting to activate the drug.');
                });
        }

        function deleteDrug(id) {
            const formData = new FormData();
            formData.append(window.csrfName, window.csrfHash);

            fetch(`<?php echo base_url('admin/drug-entry/delete/'); ?>${id}`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    updateCsrfTokens(data.csrf_name, data.csrf_hash);
                    if (data.status === 'success') {
                        showAlert('success', data.message);
                        refreshTable();
                    } else {
                        showAlert('error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error deleting drug:', error);
                    showAlert('error', 'An error occurred while attempting to delete the drug.');
                });
        }

        function refreshTable() {
            fetch('<?php echo base_url("admin/drug-entry"); ?>')
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTableBody = doc.getElementById('drugsTableBody');
                    if (newTableBody && drugsTableBody) {
                        drugsTableBody.innerHTML = newTableBody.innerHTML;
                        paginateTable();
                    }
                })
                .catch(error => {
                    console.error('Error reloading drug list:', error);
                });
        }

        function updateCsrfTokens(name, hash) {
            window.csrfName = name;
            window.csrfHash = hash;

            const csrfInputs = document.querySelectorAll(`input[name="${name}"]`);
            csrfInputs.forEach(input => {
                input.value = hash;
            });
        }

        function showAlert(type, message) {
            const isSuccess = type === 'success';
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            Toast.fire({
                icon: isSuccess ? 'success' : 'error',
                title: message
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

        // 3. Bulk CSV Import Form Handler
        const importDrugsForm = document.getElementById('importDrugsForm');
        if (importDrugsForm) {
            importDrugsForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = document.getElementById('importDrugsSubmitBtn');
                const originalBtnHtml = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Importing...';

                const formData = new FormData(this);

                fetch('<?php echo base_url("admin/drug-entry/import"); ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHtml;
                        updateCsrfTokens(data.csrf_name, data.csrf_hash);

                        if (data.status === 'success') {
                            const modalEl = document.getElementById('importDrugsModal');
                            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                            if (modal) modal.hide();
                            importDrugsForm.reset();
                            SwalCustom.fire({
                                title: 'Import Completed',
                                text: data.message,
                                icon: 'success'
                            });
                            refreshTable();
                        } else {
                            if (data.errors && data.errors.length > 0) {
                                const allErrors = data.errors;

                                // Custom premium header layout + search bar + container
                                let html = `
                            <div class="ddi-import-error-modal text-start" style="font-family: 'Poppins', sans-serif;">
                                <!-- Header Warning Card -->
                                <div class="d-flex align-items-center gap-3 p-3 mb-3 border-0" style="background-color: #fff5f5; border-left: 4px solid #fa5252 !important; border-radius: 12px;">
                                    <div style="background-color: #ffe3e3; color: #e03131; width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 8px rgba(224, 49, 49, 0.15);">
                                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                                    </div>
                                    <div style="flex-grow: 1;">
                                        <h5 class="fw-bold mb-0.5" style="color: #c92a2a; font-size: 15px; margin: 0;">Import Blocked (${allErrors.length} Errors)</h5>
                                        <p class="mb-0 text-muted" style="font-size: 12px; line-height: 1.4; font-weight: 500;">No changes were saved. Please resolve the issues below.</p>
                                    </div>
                                </div>

                                <!-- Search Filter Input -->
                                <div class="mb-3">
                                    <div class="input-group" style="box-shadow: none;">
                                        <span class="input-group-text bg-white border-end-0" style="border-color: #e2e8f0; border-top-left-radius: 8px; border-bottom-left-radius: 8px; padding: 6px 12px;"><i class="bi bi-funnel text-muted fs-6"></i></span>
                                        <input type="text" id="swalErrorFilter" class="form-control border-start-0" placeholder="Type to filter errors by row or drug name..." style="border-color: #e2e8f0; border-top-right-radius: 8px; border-bottom-right-radius: 8px; outline: none; box-shadow: none; font-size: 13px; padding: 6px 12px;" autocomplete="off">
                                    </div>
                                    <div class="d-flex justify-content-end align-items-center mt-2 px-1">
                                        <span id="swalErrorCountText" class="badge px-2.5 py-1.5 rounded-pill" style="font-size: 11px; font-weight: 600; background-color: #f8fafc; border: 1px solid #e2e8f0; color: #64748b !important;">Showing ${Math.min(allErrors.length, 100)} of ${allErrors.length} errors</span>
                                    </div>
                                </div>

                                <!-- Scrollable Error List Container -->
                                <div style="max-height: 260px; overflow-y: auto; padding-right: 4px;" class="swal-errors-scroll">
                                    <div class="d-flex flex-column gap-2" id="swalErrorListContainer" style="display: flex; flex-direction: column; gap: 8px;">
                                        <!-- Dynamic elements will be rendered here for high performance -->
                                    </div>
                                </div>
                            </div>

                            <style>
                                .swal-errors-scroll::-webkit-scrollbar { width: 5px; }
                                .swal-errors-scroll::-webkit-scrollbar-track { background: #f8fafc; border-radius: 999px; }
                                .swal-errors-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 999px; }
                                .swal-errors-scroll::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
                            </style>
                            `;

                                Swal.fire({
                                    title: '',
                                    html: html,
                                    showConfirmButton: true,
                                    confirmButtonColor: '#0f766e',
                                    confirmButtonText: 'Dismiss',
                                    customClass: {
                                        popup: 'rounded-4 border-0 shadow-lg p-3.5',
                                        htmlContainer: 'm-0'
                                    },
                                    width: window.innerWidth < 576 ? '95%' : '560px',
                                    didOpen: () => {
                                        const container = document.getElementById('swalErrorListContainer');
                                        const countLabel = document.getElementById('swalErrorCountText');
                                        const filterInput = document.getElementById('swalErrorFilter');

                                        function renderSwalErrors(filterText = '') {
                                            const val = filterText.toLowerCase().trim();
                                            const filtered = allErrors.filter(err => err.toLowerCase().includes(val));
                                            const limit = 100;
                                            const visibleList = filtered.slice(0, limit);

                                            let itemsHtml = '';
                                            visibleList.forEach(err => {
                                                let displayContent = escapeHtml(err);
                                                let match = err.match(/^Row (\d+):\s*(.*)$/);
                                                if (match) {
                                                    let rowNum = match[1];
                                                    let msg = match[2];
                                                    displayContent = `<span class="badge me-2" style="background-color: #fee2e2; color: #e03131; border: 1px solid #ffc9c9; font-weight: 700; font-size: 10px; padding: 3px 6px; border-radius: 4px; flex-shrink: 0; min-width: 50px; text-align: center;">Row ${rowNum}</span> <span style="font-weight: 500; font-size: 12px; line-height: 1.4; color: #495057; text-align: left; word-break: break-word;">${escapeHtml(msg)}</span>`;
                                                } else {
                                                    displayContent = `<span style="font-weight: 500; font-size: 12px; line-height: 1.4; color: #495057; text-align: left; word-break: break-word;">${displayContent}</span>`;
                                                }
                                                itemsHtml += `<div class="d-flex align-items-center py-2 px-2.5 rounded-3" style="background-color: #fff5f5; color: #c92a2a; border-left: 3.5px solid #fa5252 !important; box-shadow: 0 1px 2px rgba(0,0,0,0.01); text-align: left; display: flex !important;">
                                                ${displayContent}
                                            </div>`;
                                            });

                                            if (filtered.length > limit) {
                                                itemsHtml += `<div class="text-center py-2 text-muted small fw-medium" style="background: #f8fafc; border-radius: 8px; border: 1px dashed #e2e8f0; font-size: 11px; margin-top: 4px;">
                                                <i class="bi bi-info-circle me-1"></i> Showing first 100 of ${filtered.length} matching errors. Refine search.
                                            </div>`;
                                            }

                                            if (filtered.length === 0) {
                                                itemsHtml = `<div class="text-center py-5 text-muted border" style="background: #f8fafc; border-style: dashed !important; border-radius: 8px; font-size: 13px;">
                                                <i class="bi bi-search fs-4 d-block mb-2 text-secondary opacity-50"></i>
                                                No matching errors found
                                            </div>`;
                                            }

                                            container.innerHTML = itemsHtml;
                                            if (countLabel) {
                                                countLabel.textContent = `Showing ${filtered.length} of ${allErrors.length} errors`;
                                            }
                                        }

                                        renderSwalErrors();

                                        if (filterInput) {
                                            filterInput.focus();
                                            filterInput.addEventListener('input', function(e) {
                                                renderSwalErrors(e.target.value);
                                            });
                                        }
                                    }
                                });
                            } else {
                                showAlert('error', data.message || 'An error occurred during CSV import.');
                            }
                        }
                    })
                    .catch(error => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHtml;
                        showAlert('error', 'An error occurred during CSV import.');
                    });
            });
        }

        // Trigger initial pagination
        paginateTable();
    });
</script>