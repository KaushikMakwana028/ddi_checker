<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- SweetAlert2 CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="ddi-page">
    <!-- PAGE HEADER -->
    <div class="ddi-header">
        <div class="ddi-header-text">
            <div class="ddi-eyebrow"><i class="bi bi-shield-check"></i> Clinical Safety</div>
            <h1 class="ddi-title">Interaction Rules Database</h1>
            <p class="ddi-subtitle">Define drug combinations that trigger patient safety alerts.</p>
        </div>
        <div class="ddi-header-actions">
            <a href="<?php echo base_url('admin/interactions/export'); ?>" class="btn-ghost">
                <i class="bi bi-download"></i> <span>Export CSV</span>
            </a>
            <button type="button" class="btn-ghost" data-bs-toggle="modal" data-bs-target="#importCsvModal">
                <i class="bi bi-upload"></i> <span>Import CSV</span>
            </button>
            <a href="<?php echo base_url('admin/interactions/add'); ?>" class="btn-primary">
                <i class="bi bi-plus-lg"></i> <span>Add Rule</span>
            </a>
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="ddi-stats">
        <div class="stat-card stat-total">
            <div class="stat-icon"><i class="bi bi-shield-check"></i></div>
            <div class="stat-info">
                <span class="stat-label">Total Rules</span>
                <span class="stat-value" id="statTotalRules"><?php echo number_format($stats['total'] ?? 0); ?></span>
            </div>
        </div>
        <div class="stat-card stat-severe">
            <div class="stat-icon"><i class="bi bi-exclamation-octagon-fill"></i></div>
            <div class="stat-info">
                <span class="stat-label">Severe</span>
                <span class="stat-value" id="statSevereRules"><?php echo number_format($stats['severe'] ?? 0); ?></span>
            </div>
        </div>
        <div class="stat-card stat-moderate">
            <div class="stat-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="stat-info">
                <span class="stat-label">Moderate</span>
                <span class="stat-value" id="statModerateRules"><?php echo number_format($stats['moderate'] ?? 0); ?></span>
            </div>
        </div>
        <div class="stat-card stat-mild">
            <div class="stat-icon"><i class="bi bi-info-circle-fill"></i></div>
            <div class="stat-info">
                <span class="stat-label">Mild</span>
                <span class="stat-value" id="statMildRules"><?php echo number_format($stats['mild'] ?? 0); ?></span>
            </div>
        </div>
    </div>

    <!-- MAIN PANEL -->
    <div class="ddi-panel">
        <!-- Filter Bar -->
        <form id="interactionsFilterForm" class="ddi-filterbar" onsubmit="return false;">
            <div class="filter-search">
                <i class="bi bi-search"></i>
                <input type="text" name="search" id="filterSearchInput" placeholder="Search drug names, remarks, or citation..." value="<?php echo html_escape($search); ?>" autocomplete="off">
            </div>
            <select name="severity" id="filterSeveritySelect" class="filter-select">
                <option value="">All Severities</option>
                <option value="Severe" <?php echo ($severity === 'Severe') ? 'selected' : ''; ?>>Severe</option>
                <option value="Moderate" <?php echo ($severity === 'Moderate') ? 'selected' : ''; ?>>Moderate</option>
                <option value="Mild" <?php echo ($severity === 'Mild') ? 'selected' : ''; ?>>Mild</option>
            </select>
            <button type="button" id="clearFiltersBtn" class="filter-reset" title="Reset Filters">
                <i class="bi bi-arrow-counterclockwise"></i> <span>Reset</span>
            </button>
            <div class="filter-loading" id="filterLoadingIndicator">
                <span class="spin"></span> Filtering
            </div>
        </form>

        <!-- Table Container -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 ddi-table" id="interactionsTable">
                <thead>
                    <tr>
                        <th style="min-width: 140px; width: 14%;">Drug A</th>
                        <th style="min-width: 140px; width: 14%;">Drug B</th>
                        <th style="min-width: 110px; width: 11%;">Severity</th>
                        <th style="min-width: 250px; width: 31%;">Clinical Remarks</th>
                        <th style="min-width: 160px; width: 15%;">Source</th>
                        <th style="min-width: 95px; width: 8%;">Status</th>
                        <th class="text-end" style="min-width: 95px; width: 7%;">Actions</th>
                    </tr>
                </thead>
                <tbody id="interactionsTableBody">
                    <?php if (empty($interactions)): ?>
                        <tr class="empty-state-row">
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-shield-slash"></i>
                                    <h3>No interaction rules found</h3>
                                    <p><?php echo (!empty($search) || !empty($severity)) ? 'No rules matched your filter criteria. Try different terms.' : 'Get started by adding your first interaction pair or importing a CSV file.'; ?></p>
                                    <?php if (!empty($search) || !empty($severity)): ?>
                                        <button type="button" class="btn-ghost" onclick="document.getElementById('clearFiltersBtn').click()">Reset Filters</button>
                                    <?php else: ?>
                                        <a href="<?php echo base_url('admin/interactions/add'); ?>" class="btn-primary"><i class="bi bi-plus-lg"></i> Add Interaction Rule</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($interactions as $rule): ?>
                            <?php
                            $sevClass = strtolower($rule['severity']);
                            $fullRemarks = $rule['remarks'] ?: 'No clinical remarks recorded.';
                            $pairLabel = $rule['drug_a_name'] . ' + ' . $rule['drug_b_name'];
                            ?>
                            <tr id="rule-row-<?php echo $rule['id']; ?>" class="rule-row sev-row-<?php echo $sevClass; ?>">
                                <td>
                                    <span class="drug-chip"><i class="bi bi-capsule"></i><?php echo html_escape($rule['drug_a_name'] ?: 'Drug #' . $rule['drug_a_id']); ?></span>
                                </td>
                                <td>
                                    <span class="drug-chip drug-chip-b"><i class="bi bi-capsule"></i><?php echo html_escape($rule['drug_b_name'] ?: 'Drug #' . $rule['drug_b_id']); ?></span>
                                </td>
                                <td>
                                    <span class="sev-badge sev-<?php echo $sevClass; ?>"><?php echo html_escape($rule['severity']); ?></span>
                                </td>
                                <td>
                                    <span class="remarks-text">
                                        <?php if (strlen($fullRemarks) > 85): ?>
                                            <?php echo html_escape(substr($fullRemarks, 0, 85)) . '… '; ?>
                                            <button type="button" class="link-btn view-remarks-btn" data-remarks="<?php echo html_escape($fullRemarks); ?>" data-pair="<?php echo html_escape($pairLabel); ?>">Read more</button>
                                        <?php else: ?>
                                            <?php echo html_escape($fullRemarks); ?>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($rule['source'])): ?>
                                        <span class="source-chip" title="<?php echo html_escape($rule['source']); ?>"><i class="bi bi-journal-text"></i><?php echo html_escape($rule['source']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($rule['is_active']): ?>
                                        <span class="status-badge status-active"><i class="bi bi-dot"></i>Active</span>
                                    <?php else: ?>
                                        <span class="status-badge status-inactive"><i class="bi bi-dot"></i>Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="actions-group">
                                        <a href="<?php echo base_url('admin/interactions/edit/' . $rule['id']); ?>" class="rule-action-btn edit-rule-btn"
                                            title="Edit Rule" aria-label="Edit Rule">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <?php if ($rule['is_active']): ?>
                                            <button type="button" class="rule-action-btn btn-warning-soft deactivate-rule-btn" data-id="<?php echo $rule['id']; ?>" data-pair="<?php echo html_escape($pairLabel); ?>" title="Deactivate Rule" aria-label="Deactivate Rule">
                                                <i class="bi bi-pause-circle"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="rule-action-btn btn-success-soft activate-rule-btn" data-id="<?php echo $rule['id']; ?>" data-pair="<?php echo html_escape($pairLabel); ?>" title="Activate Rule" aria-label="Activate Rule">
                                                <i class="bi bi-play-circle"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="rule-action-btn btn-danger-soft delete-rule-btn" data-id="<?php echo $rule['id']; ?>" data-pair="<?php echo html_escape($pairLabel); ?>" title="Delete Rule" aria-label="Delete Rule">
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

        <!-- Pagination Footer -->
        <div id="paginationWrapper">
            <?php if ($total_rows > 0): ?>
                <div class="ddi-footer">
                    <div class="footer-count">
                        Showing <strong><?php echo number_format($offset + 1); ?></strong>–<strong><?php echo number_format(min($offset + $limit, $total_rows)); ?></strong> of <strong><?php echo number_format($total_rows); ?></strong> rules
                    </div>
                    <?php if ($total_pages > 1): ?>
                        <nav class="ddi-pagination" aria-label="Interaction rules pagination">
                            <?php
                            $queryParams = [];
                            if (!empty($search)) $queryParams['search'] = $search;
                            if (!empty($severity)) $queryParams['severity'] = $severity;

                            function getPageUrl($pageNum, $params) {
                                $params['page'] = $pageNum;
                                return base_url('admin/interactions') . '?' . http_build_query($params);
                            }
                            ?>
                            <a href="<?php echo ($current_page > 1) ? getPageUrl($current_page - 1, $queryParams) : '#'; ?>"
                                class="page-nav-btn <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>"
                                data-page="<?php echo $current_page - 1; ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>

                            <?php
                            $startPage = max(1, $current_page - 2);
                            $endPage   = min($total_pages, $current_page + 2);

                            if ($startPage > 1) {
                                echo '<a href="' . getPageUrl(1, $queryParams) . '" class="page-num-btn" data-page="1">1</a>';
                                if ($startPage > 2) echo '<span class="page-ellipsis">…</span>';
                            }

                            for ($i = $startPage; $i <= $endPage; $i++) {
                                $isActive = ($i == $current_page) ? 'active' : '';
                                echo '<a href="' . getPageUrl($i, $queryParams) . '" class="page-num-btn ' . $isActive . '" data-page="' . $i . '">' . $i . '</a>';
                            }

                            if ($endPage < $total_pages) {
                                if ($endPage < $total_pages - 1) echo '<span class="page-ellipsis">…</span>';
                                echo '<a href="' . getPageUrl($total_pages, $queryParams) . '" class="page-num-btn" data-page="' . $total_pages . '">' . $total_pages . '</a>';
                            }
                            ?>

                            <a href="<?php echo ($current_page < $total_pages) ? getPageUrl($current_page + 1, $queryParams) : '#'; ?>"
                                class="page-nav-btn <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>"
                                data-page="<?php echo $current_page + 1; ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </nav>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- MODAL: Bulk CSV & Excel Import -->
    <div class="modal fade ddi-modal" id="importCsvModal" tabindex="-1" aria-labelledby="importCsvModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                <div class="modal-header border-bottom py-3.5 px-4 bg-white">
                    <div class="modal-header-icon me-3"><i class="bi bi-file-earmark-spreadsheet text-teal"></i></div>
                    <div class="me-auto">
                        <h5 class="modal-title fw-bold text-dark mb-0" id="importCsvModalLabel">Bulk Import Rules (Excel / CSV)</h5>
                        <small class="text-muted">Upload an Excel (.xlsx) or CSV file to batch import rules.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="importCsvForm" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" class="csrf_sync_field">

                    <div class="modal-body p-4">
                        <div class="p-3 mb-3 rounded-3" style="background-color: #f0fdfa; border: 1px solid #99f6e4; font-size: 12.5px; color: #0f766e;">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                <strong><i class="bi bi-file-earmark-spreadsheet me-1"></i> Expected Column Format:</strong>
                                <a href="<?php echo base_url('admin/interactions/sample_csv'); ?>" class="btn btn-sm text-white px-2.5 py-1 rounded-2 fw-semibold" style="background-color: #0f766e; font-size: 11.5px; text-decoration: none;">
                                    <i class="bi bi-download me-1"></i> Download Sample CSV
                                </a>
                            </div>
                            <code>Drug A, Drug B, Severity, Remarks, Source</code>
                            <ul class="mb-0 mt-1.5 ps-3">
                                <li>Supports standard <strong>.xlsx (Excel)</strong> and <strong>.csv</strong> files.</li>
                                <li>Drug names are matched case-insensitively against active formulary.</li>
                                <li>Allowed severities: <code>Mild</code>, <code>Moderate</code>, <code>Severe</code>.</li>
                                <li>Existing duplicate pairs are safely skipped.</li>
                            </ul>
                        </div>

                        <div class="form-field">
                            <label for="csv_file_input" class="form-label fw-semibold text-dark fs-6 mb-2">Select Excel (.xlsx) or CSV File <span class="text-danger">*</span></label>
                            <input type="file" name="csv_file" id="csv_file_input" class="form-control" style="border: 1px solid #cbd5e1; background-color: #f8fafc; border-radius: 10px; padding: 10px 14px;" accept=".csv, .xlsx, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, text/csv" required>
                        </div>
                    </div>

                    <div class="modal-footer border-top py-3 px-4 bg-white d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 py-2 rounded-3 fw-semibold" id="importCsvSubmitBtn" style="background-color: #0f766e; border: 1px solid #0f766e;">
                            <i class="bi bi-upload me-1"></i> Start Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: Full Remarks View -->
    <div class="modal fade ddi-modal" id="remarksModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                <div class="modal-header border-bottom py-3.5 px-4 bg-white">
                    <div class="modal-header-icon me-3"><i class="bi bi-journal-medical text-teal"></i></div>
                    <div class="me-auto">
                        <h5 class="modal-title fw-bold text-dark mb-0" id="remarksModalTitle">Clinical Remarks</h5>
                        <small class="text-muted" id="remarksModalSubtitle">Adverse reaction &amp; management guide</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p id="remarksModalContent" class="mb-0 text-secondary" style="white-space: pre-wrap; line-height: 1.6; font-size: 13.5px;"></p>
                </div>
                <div class="modal-footer border-top py-3 px-4 bg-white text-end">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-3" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .ddi-page,
    .ddi-page * {
        font-family: 'Poppins', sans-serif;
        box-sizing: border-box;
    }

    .ddi-header {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-end;
        gap: 16px;
        margin-bottom: 24px;
    }

    .ddi-eyebrow {
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

    .ddi-title {
        font-size: 26px;
        font-weight: 700;
        margin: 0 0 4px;
        color: #0f172a;
        line-height: 1.25;
    }

    .ddi-subtitle {
        font-size: 14px;
        color: #64748b;
        margin: 0;
    }

    .ddi-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-primary,
    .btn-ghost {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 12px;
        padding: 10px 18px;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all .15s ease;
        white-space: nowrap;
        text-decoration: none;
    }

    .btn-primary {
        background: #0f766e;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(15, 118, 110, .25);
    }
    .btn-primary:hover {
        background: #0c5f59;
        transform: translateY(-1px);
    }

    .btn-ghost {
        background: #ffffff;
        color: #475569;
        border-color: #cbd5e1;
    }
    .btn-ghost:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
    }

    /* Stat Cards */
    .ddi-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
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

    .stat-total .stat-icon { background: #f0fdfa; color: #0f766e; }
    .stat-severe .stat-icon { background: #fef2f2; color: #dc2626; }
    .stat-moderate .stat-icon { background: #fffbeb; color: #d97706; }
    .stat-mild .stat-icon { background: #eff6ff; color: #2563eb; }

    .stat-info { display: flex; flex-direction: column; }
    .stat-label { font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 2px; }
    .stat-value { font-size: 22px; font-weight: 700; color: #0f172a; line-height: 1.1; }

    /* Panel & Filterbar */
    .ddi-panel {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
    }

    .ddi-filterbar {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        background: #ffffff;
        flex-wrap: wrap;
    }

    .filter-search {
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

    .filter-search i { color: #94a3b8; font-size: 14px; flex-shrink: 0; }
    .filter-search input { border: none; background: transparent; outline: none; width: 100%; height: 100%; font-size: 13.5px; color: #0f172a; }

    .filter-select {
        height: 40px;
        max-height: 40px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 0 34px 0 14px;
        font-size: 13.5px;
        color: #0f172a;
        background-color: #f8fafc;
        cursor: pointer;
        outline: none;
    }

    .filter-reset {
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
    .filter-reset:hover { color: #0f766e; border-color: #0f766e; }

    .filter-loading {
        display: none;
        align-items: center;
        gap: 6px;
        font-size: 12.5px;
        color: #0f766e;
    }
    .filter-loading.active { display: inline-flex; }
    .spin {
        width: 12px;
        height: 12px;
        border: 2px solid #99f6e4;
        border-top-color: #0f766e;
        border-radius: 50%;
        animation: spin .6s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

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
    .ddi-table tbody tr:hover { background-color: #f8fafc; }
    .ddi-table tbody td { padding: 13px 18px; vertical-align: middle; font-size: 13.5px; border-bottom: 1px solid #f1f5f9; }

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
    .drug-chip-b i {
        color: #d97706;
        background: #fffbeb;
        border-color: #fde68a;
    }

    .sev-badge {
        display: inline-flex;
        align-items: center;
        font-size: 12px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 999px;
        white-space: nowrap;
    }
    .sev-severe { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .sev-moderate { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .sev-mild { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }

    .remarks-text { color: #475569; font-size: 13px; line-height: 1.45; }
    .link-btn { background: none; border: none; padding: 0; color: #0f766e; font-weight: 600; font-size: 12.5px; cursor: pointer; text-decoration: underline; }

    .source-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        color: #64748b;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 3px 8px;
        border-radius: 6px;
        max-width: 170px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
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
    .status-badge i { font-size: 20px; line-height: 0; }
    .status-active { background: #ecfdf5; color: #059669; }
    .status-inactive { background: #fef2f2; color: #dc2626; }

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
    .rule-action-btn:hover { transform: translateY(-1px); box-shadow: 0 2px 6px rgba(0, 0, 0, .08); }

    .rule-action-btn.edit-rule-btn { color: #0f766e; border-color: #99f6e4; background: #f0fdfa; }
    .rule-action-btn.edit-rule-btn:hover { color: #ffffff; background: #0d9488; border-color: #0d9488; }

    .rule-action-btn.btn-warning-soft { color: #b45309; border-color: #fde68a; background: #fffbeb; }
    .rule-action-btn.btn-warning-soft:hover { color: #ffffff; background: #d97706; border-color: #d97706; }

    .rule-action-btn.btn-success-soft { color: #15803d; border-color: #bbf7d0; background: #f0fdf4; }
    .rule-action-btn.btn-success-soft:hover { color: #ffffff; background: #16a34a; border-color: #16a34a; }

    .rule-action-btn.btn-danger-soft { color: #dc2626; border-color: #fecaca; background: #fef2f2; }
    .rule-action-btn.btn-danger-soft:hover { color: #ffffff; background: #dc2626; border-color: #dc2626; }

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

    .footer-count { font-size: 13px; color: #64748b; }
    .footer-count strong { color: #0f172a; }

    .ddi-pagination {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .page-nav-btn, .page-num-btn {
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
    .page-num-btn.active { background: #0f766e; border-color: #0f766e; color: #ffffff; font-weight: 600; }
    .page-nav-btn.disabled { opacity: .4; pointer-events: none; }
    .page-ellipsis { padding: 0 4px; color: #94a3b8; font-size: 13px; }

    .empty-state { text-align: center; padding: 48px 20px; color: #64748b; }
    .empty-state i { font-size: 40px; color: #94a3b8; display: block; margin-bottom: 12px; }
    .empty-state h3 { font-size: 16px; font-weight: 600; color: #0f172a; margin-bottom: 6px; }
    .empty-state p { font-size: 13px; margin-bottom: 16px; max-width: 360px; margin-left: auto; margin-right: auto; }

    .modal-header-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #f0fdfa;
        border: 1px solid #99f6e4;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    @media (max-width: 991px) {
        .ddi-stats { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 767px) {
        .ddi-header { flex-direction: column; align-items: stretch; gap: 14px; }
        .ddi-header-actions { width: 100%; }
        .ddi-header-actions .btn-primary, .ddi-header-actions .btn-ghost { flex: 1; justify-content: center; }
        .ddi-filterbar { padding: 12px 14px; flex-direction: column; align-items: stretch; gap: 8px; }
        .filter-search, .filter-select, .filter-reset { flex: 0 0 40px !important; width: 100% !important; height: 40px !important; max-height: 40px !important; min-height: 40px !important; justify-content: center; }
        .ddi-footer { flex-direction: column; align-items: center; text-align: center; }
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

    const interactionsTableBody = document.getElementById('interactionsTableBody');
    const filterSearchInput = document.getElementById('filterSearchInput');
    const filterSeveritySelect = document.getElementById('filterSeveritySelect');
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');
    const filterLoading = document.getElementById('filterLoadingIndicator');

    let debounceTimer;

    // 1. Live Server-Side Filtering
    if (filterSearchInput) {
        filterSearchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchRules(1), 300);
        });
    }

    if (filterSeveritySelect) {
        filterSeveritySelect.addEventListener('change', function() {
            fetchRules(1);
        });
    }

    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            if (filterSearchInput) filterSearchInput.value = '';
            if (filterSeveritySelect) filterSeveritySelect.value = '';
            fetchRules(1);
        });
    }

    // Pagination Click
    document.addEventListener('click', function(e) {
        const pageBtn = e.target.closest('.page-num-btn, .page-nav-btn');
        if (pageBtn && !pageBtn.classList.contains('disabled') && !pageBtn.classList.contains('active')) {
            e.preventDefault();
            const page = pageBtn.dataset.page;
            if (page) fetchRules(page);
        }
    });

    function fetchRules(page = 1) {
        if (filterLoading) filterLoading.classList.add('active');
        const search = filterSearchInput ? filterSearchInput.value.trim() : '';
        const severity = filterSeveritySelect ? filterSeveritySelect.value : '';

        const url = new URL('<?php echo base_url("admin/interactions"); ?>');
        url.searchParams.set('ajax', '1');
        url.searchParams.set('page', page);
        if (search) url.searchParams.set('search', search);
        if (severity) url.searchParams.set('severity', severity);

        fetch(url.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (filterLoading) filterLoading.classList.remove('active');
            if (data.status === 'success') {
                renderTable(data.interactions, data.current_page, data.total_rows, data.limit, data.total_pages);
                updateStats(data.stats);
            }
        })
        .catch(error => {
            if (filterLoading) filterLoading.classList.remove('active');
            console.error('Error fetching interaction rules:', error);
        });
    }

    function renderTable(rules, currentPage, totalRows, limit, totalPages) {
        if (!interactionsTableBody) return;
        if (!rules || rules.length === 0) {
            interactionsTableBody.innerHTML = `
                <tr class="empty-state-row">
                    <td colspan="7" class="text-center py-5">
                        <div class="empty-state">
                            <i class="bi bi-shield-slash"></i>
                            <h3>No interaction rules found</h3>
                            <p>No rules matched your criteria.</p>
                            <button type="button" class="btn-ghost" onclick="document.getElementById('clearFiltersBtn').click()">Reset Filters</button>
                        </div>
                    </td>
                </tr>
            `;
            const pagWrap = document.getElementById('paginationWrapper');
            if (pagWrap) pagWrap.innerHTML = '';
            return;
        }

        let html = '';
        rules.forEach(rule => {
            const sevClass = (rule.severity || '').toLowerCase();
            const fullRemarks = rule.remarks || 'No clinical remarks recorded.';
            const pairLabel = (rule.drug_a_name || 'Drug A') + ' + ' + (rule.drug_b_name || 'Drug B');

            html += `
                <tr id="rule-row-${rule.id}" class="rule-row sev-row-${sevClass}">
                    <td><span class="drug-chip"><i class="bi bi-capsule"></i>${escapeHtml(rule.drug_a_name || 'Drug #' + rule.drug_a_id)}</span></td>
                    <td><span class="drug-chip drug-chip-b"><i class="bi bi-capsule"></i>${escapeHtml(rule.drug_b_name || 'Drug #' + rule.drug_b_id)}</span></td>
                    <td><span class="sev-badge sev-${sevClass}">${escapeHtml(rule.severity)}</span></td>
                    <td>
                        <span class="remarks-text">
                            ${fullRemarks.length > 85 ? escapeHtml(fullRemarks.substring(0, 85)) + '… <button type="button" class="link-btn view-remarks-btn" data-remarks="' + escapeHtml(fullRemarks) + '" data-pair="' + escapeHtml(pairLabel) + '">Read more</button>' : escapeHtml(fullRemarks)}
                        </span>
                    </td>
                    <td>
                        ${rule.source ? `<span class="source-chip" title="${escapeHtml(rule.source)}"><i class="bi bi-journal-text"></i>${escapeHtml(rule.source)}</span>` : '<span class="text-muted small">—</span>'}
                    </td>
                    <td>
                        ${parseInt(rule.is_active) === 1 ? '<span class="status-badge status-active"><i class="bi bi-dot"></i>Active</span>' : '<span class="status-badge status-inactive"><i class="bi bi-dot"></i>Inactive</span>'}
                    </td>
                    <td class="text-end">
                        <div class="actions-group">
                            <a href="<?php echo base_url('admin/interactions/edit/'); ?>${rule.id}" class="rule-action-btn edit-rule-btn" title="Edit Rule"><i class="bi bi-pencil-square"></i></a>
                            ${parseInt(rule.is_active) === 1 ? `
                                <button type="button" class="rule-action-btn btn-warning-soft deactivate-rule-btn" data-id="${rule.id}" data-pair="${escapeHtml(pairLabel)}" title="Deactivate Rule"><i class="bi bi-pause-circle"></i></button>
                            ` : `
                                <button type="button" class="rule-action-btn btn-success-soft activate-rule-btn" data-id="${rule.id}" data-pair="${escapeHtml(pairLabel)}" title="Activate Rule"><i class="bi bi-play-circle"></i></button>
                            `}
                            <button type="button" class="rule-action-btn btn-danger-soft delete-rule-btn" data-id="${rule.id}" data-pair="${escapeHtml(pairLabel)}" title="Delete Rule"><i class="bi bi-trash3"></i></button>
                        </div>
                    </td>
                </tr>
            `;
        });

        interactionsTableBody.innerHTML = html;

        // Render pagination controls
        const offset = (currentPage - 1) * limit;
        const showingTo = Math.min(offset + limit, totalRows);
        const pagWrap = document.getElementById('paginationWrapper');
        if (pagWrap && totalRows > 0) {
            let pagHtml = `
                <div class="ddi-footer">
                    <div class="footer-count">
                        Showing <strong>${offset + 1}</strong>–<strong>${showingTo}</strong> of <strong>${totalRows}</strong> rules
                    </div>
            `;
            if (totalPages > 1) {
                pagHtml += `<nav class="ddi-pagination">`;
                pagHtml += `<a href="#" class="page-nav-btn ${currentPage <= 1 ? 'disabled' : ''}" data-page="${currentPage - 1}"><i class="bi bi-chevron-left"></i></a>`;
                
                const startP = Math.max(1, currentPage - 2);
                const endP = Math.min(totalPages, currentPage + 2);
                if (startP > 1) {
                    pagHtml += `<a href="#" class="page-num-btn" data-page="1">1</a>`;
                    if (startP > 2) pagHtml += `<span class="page-ellipsis">…</span>`;
                }
                for (let i = startP; i <= endP; i++) {
                    pagHtml += `<a href="#" class="page-num-btn ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</a>`;
                }
                if (endP < totalPages) {
                    if (endP < totalPages - 1) pagHtml += `<span class="page-ellipsis">…</span>`;
                    pagHtml += `<a href="#" class="page-num-btn" data-page="${totalPages}">${totalPages}</a>`;
                }
                pagHtml += `<a href="#" class="page-nav-btn ${currentPage >= totalPages ? 'disabled' : ''}" data-page="${currentPage + 1}"><i class="bi bi-chevron-right"></i></a>`;
                pagHtml += `</nav>`;
            }
            pagHtml += `</div>`;
            pagWrap.innerHTML = pagHtml;
        }
    }

    function updateStats(stats) {
        if (!stats) return;
        if (document.getElementById('statTotalRules')) document.getElementById('statTotalRules').textContent = stats.total || 0;
        if (document.getElementById('statSevereRules')) document.getElementById('statSevereRules').textContent = stats.severe || 0;
        if (document.getElementById('statModerateRules')) document.getElementById('statModerateRules').textContent = stats.moderate || 0;
        if (document.getElementById('statMildRules')) document.getElementById('statMildRules').textContent = stats.mild || 0;
    }

    // 2. Table Action Handlers
    document.addEventListener('click', function(e) {
        // Read More Remarks Modal
        const remarksBtn = e.target.closest('.view-remarks-btn');
        if (remarksBtn) {
            const remarks = remarksBtn.dataset.remarks;
            const pair = remarksBtn.dataset.pair;
            document.getElementById('remarksModalTitle').textContent = pair || 'Clinical Remarks';
            document.getElementById('remarksModalContent').textContent = remarks;
            const remModal = new bootstrap.Modal(document.getElementById('remarksModal'));
            remModal.show();
            return;
        }

        // Deactivate Rule
        const deactBtn = e.target.closest('.deactivate-rule-btn');
        if (deactBtn) {
            const id = deactBtn.dataset.id;
            const pair = deactBtn.dataset.pair;
            SwalCustom.fire({
                title: 'Deactivate Rule?',
                html: `Are you sure you want to deactivate interaction rule for <strong>"${escapeHtml(pair)}"</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Deactivate',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    deactivateRule(id);
                }
            });
            return;
        }

        // Activate Rule
        const actBtn = e.target.closest('.activate-rule-btn');
        if (actBtn) {
            const id = actBtn.dataset.id;
            const pair = actBtn.dataset.pair;
            SwalCustom.fire({
                title: 'Reactivate Rule?',
                html: `Are you sure you want to reactivate interaction rule for <strong>"${escapeHtml(pair)}"</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Reactivate',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    activateRule(id);
                }
            });
            return;
        }

        // Delete Rule
        const delBtn = e.target.closest('.delete-rule-btn');
        if (delBtn) {
            const id = delBtn.dataset.id;
            const pair = delBtn.dataset.pair;
            Swal.fire({
                title: 'Permanently Delete Rule?',
                html: `Are you sure you want to delete the rule for <strong>"${escapeHtml(pair)}"</strong>?<br><small class="text-danger">This action cannot be undone.</small>`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Delete Permanently',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteRule(id);
                }
            });
            return;
        }
    });

    function deactivateRule(id) {
        const formData = new FormData();
        formData.append(window.csrfName, window.csrfHash);

        fetch(`<?php echo base_url('admin/interactions/deactivate/'); ?>${id}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            updateCsrfTokens(data.csrf_name, data.csrf_hash);
            if (data.status === 'success') {
                showAlert('success', data.message);
                fetchRules(1);
            } else {
                showAlert('error', data.message);
            }
        });
    }

    function activateRule(id) {
        const formData = new FormData();
        formData.append(window.csrfName, window.csrfHash);

        fetch(`<?php echo base_url('admin/interactions/activate/'); ?>${id}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            updateCsrfTokens(data.csrf_name, data.csrf_hash);
            if (data.status === 'success') {
                showAlert('success', data.message);
                fetchRules(1);
            } else {
                showAlert('error', data.message);
            }
        });
    }

    function deleteRule(id) {
        const formData = new FormData();
        formData.append(window.csrfName, window.csrfHash);

        fetch(`<?php echo base_url('admin/interactions/delete/'); ?>${id}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            updateCsrfTokens(data.csrf_name, data.csrf_hash);
            if (data.status === 'success') {
                showAlert('success', data.message);
                fetchRules(1);
            } else {
                showAlert('error', data.message);
            }
        });
    }

    // 3. Bulk CSV Import Form Handler
    const importCsvForm = document.getElementById('importCsvForm');
    if (importCsvForm) {
        importCsvForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = document.getElementById('importCsvSubmitBtn');
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Importing...';

            const formData = new FormData(this);

            fetch('<?php echo base_url("admin/interactions/import"); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
                updateCsrfTokens(data.csrf_name, data.csrf_hash);

                if (data.status === 'success') {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('importCsvModal'));
                    if (modal) modal.hide();
                    importCsvForm.reset();
                    SwalCustom.fire({
                        title: 'Import Completed',
                        text: data.message,
                        icon: 'success'
                    });
                    fetchRules(1);
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
                showAlert('error', 'An error occurred during CSV import.');
            });
        });
    }

    function updateCsrfTokens(name, hash) {
        window.csrfName = name;
        window.csrfHash = hash;

        const csrfInputs = document.querySelectorAll(`input[name="${name}"], .csrf_sync_field`);
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
            timer: 3500,
            timerProgressBar: true
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
});
</script>