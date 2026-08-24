<?php 
defined('BASEPATH') or exit('No direct script access allowed'); 

// Compute Patient Statistics
$total_patients = count($patients);
$male_count = 0;
$female_count = 0;
$other_count = 0;
$hospitals = [];

foreach ($patients as $p) {
    if (strtolower($p['gender']) === 'male') {
        $male_count++;
    } elseif (strtolower($p['gender']) === 'female') {
        $female_count++;
    } else {
        $other_count++;
    }
    if (!empty($p['hospital_name'])) {
        $hospitals[] = trim(strtolower($p['hospital_name']));
    }
}
$unique_hospitals = count(array_unique($hospitals));
?>

<div class="patient-manage-page">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div>
            <div class="page-eyebrow">
                <i class="bi bi-person-badge-fill"></i> Clinical Registry
            </div>
            <h2 class="page-title">Patient Directory</h2>
            <p class="page-subtitle">View clinical profiles, assigned attending doctors, and clinical clinic locations.</p>
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="ddi-stats mb-4">
        <div class="stat-card stat-total">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-info">
                <span class="stat-label">Total Patients</span>
                <span class="stat-value"><?php echo number_format($total_patients); ?></span>
            </div>
        </div>
        <div class="stat-card stat-active">
            <div class="stat-icon" style="background: #eff6ff; color: #2563eb;"><i class="bi bi-gender-male"></i></div>
            <div class="stat-info">
                <span class="stat-label">Male Patients</span>
                <span class="stat-value"><?php echo number_format($male_count); ?></span>
            </div>
        </div>
        <div class="stat-card stat-inactive">
            <div class="stat-icon" style="background: #fef2f2; color: #dc2626;"><i class="bi bi-gender-female"></i></div>
            <div class="stat-info">
                <span class="stat-label">Female Patients</span>
                <span class="stat-value"><?php echo number_format($female_count); ?></span>
            </div>
        </div>
        <div class="stat-card stat-hosp">
            <div class="stat-icon"><i class="bi bi-building-fill"></i></div>
            <div class="stat-info">
                <span class="stat-label">Active Hospitals</span>
                <span class="stat-value"><?php echo number_format($unique_hospitals); ?></span>
            </div>
        </div>
    </div>

    <!-- Main Panel & Searchable Patients Table -->
    <div class="panel-card">
        <!-- Filter Bar -->
        <div class="table-filterbar">
            <form action="<?php echo base_url('admin/patients'); ?>" method="GET" style="display:flex; width: 100%; gap: 12px; flex-wrap: wrap; margin: 0;">
                <div class="filter-search-wrap" style="flex: 1 1 300px;">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" value="<?php echo html_escape($search); ?>" placeholder="Search by patient name, phone, doctor name, or hospital..." autocomplete="off">
                </div>
                
                <div class="d-flex align-items-center gap-2">
                    <button type="submit" class="btn-primary" style="height: 40px; padding: 0 20px; font-size: 13.5px; border-radius: 10px;">
                        <i class="bi bi-filter"></i> Search
                    </button>
                    <?php if (!empty($search)): ?>
                        <a href="<?php echo base_url('admin/patients'); ?>" class="filter-reset-btn text-decoration-none d-inline-flex align-items-center justify-content-center" style="height: 40px;">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 ddi-table">
                <thead>
                    <tr>
                        <th style="padding: 14px 18px; width: 25%;">Patient Name</th>
                        <th style="padding: 14px 18px; width: 12%;" class="text-center">Age / Gender</th>
                        <th style="padding: 14px 18px; width: 15%;">Contact</th>
                        <th style="padding: 14px 18px; width: 20%;">Attending Doctor</th>
                        <th style="padding: 14px 18px; width: 20%;">Hospital / Clinic</th>
                        <th class="text-end" style="padding: 14px 18px; width: 8%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($patients)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="empty-state py-4 text-center">
                                    <i class="bi bi-people-fill text-muted mb-2" style="font-size: 3rem; color: #94a3b8 !important;"></i>
                                    <h3 class="fw-bold text-dark mt-2" style="font-size: 1.25rem;">No patients found</h3>
                                    <p class="text-secondary small mb-0">No patient records matched the active filter queries.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($patients as $p): ?>
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="avatar-circle shadow-sm" style="width: 36px; height: 36px; min-width: 36px; background-color: #f0fdfa; border: 1px solid #99f6e4; color: #0f766e; font-size: 0.85rem; font-weight: 700; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <?php
                                            $words = explode(' ', $p['full_name']);
                                            $initials = '';
                                            foreach ($words as $w) {
                                                $initials .= substr($w, 0, 1);
                                            }
                                            echo strtoupper(substr($initials, 0, 2)) ?: 'PT';
                                            ?>
                                        </div>
                                        <div class="d-flex flex-column text-truncate" style="min-width: 0;">
                                            <span class="fw-semibold text-dark text-truncate"><?php echo html_escape($p['full_name']); ?></span>
                                            <small class="text-muted" style="font-size: 11px;">Created: <?php echo date('d-M-Y', strtotime($p['created_at'])); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="fw-semibold mb-0.5" style="font-size:13.5px;"><?php echo html_escape($p['age']); ?> Yrs</div>
                                    <?php if (strtolower($p['gender']) === 'male'): ?>
                                        <span class="status-badge status-active" style="padding: 1px 8px; font-size:11px;"><i class="bi bi-dot"></i>Male</span>
                                    <?php elseif (strtolower($p['gender']) === 'female'): ?>
                                        <span class="status-badge status-inactive" style="padding: 1px 8px; font-size:11px; background:#fef2f2; color:#dc2626;"><i class="bi bi-dot"></i>Female</span>
                                    <?php else: ?>
                                        <span class="category-badge" style="padding: 1px 8px; font-size:11px;"><?php echo html_escape($p['gender']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-secondary font-medium">
                                    <span class="text-nowrap"><i class="bi bi-telephone text-muted me-1 small"></i><?php echo html_escape($p['contact_number'] ?: '—'); ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($p['doctor_name'])): ?>
                                        <span class="stock-badge" style="font-size: 12px; color: #0f766e;">
                                            <i class="bi bi-person-workspace text-teal"></i>
                                            <?php echo html_escape($p['doctor_name']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-secondary small fw-medium">
                                    <?php if (!empty($p['hospital_name'])): ?>
                                        <span class="d-inline-flex align-items-center gap-1"><i class="bi bi-hospital text-muted"></i> <?php echo html_escape($p['hospital_name']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="<?php echo base_url('admin/patients/view/' . $p['id']); ?>" class="rule-action-btn" title="View Patient Details" style="text-decoration:none; display: inline-flex; align-items:center; justify-content:center;">
                                        <i class="bi bi-eye" style="color: #0f766e;"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .patient-manage-page,
    .patient-manage-page * {
        font-family: 'Poppins', sans-serif;
        box-sizing: border-box;
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
    .stat-active .stat-icon { background: #eff6ff; color: #2563eb; }
    .stat-inactive .stat-icon { background: #fef2f2; color: #dc2626; }
    .stat-hosp .stat-icon { background: #fdf4ff; color: #c026d3; }

    .stat-info { display: flex; flex-direction: column; }
    .stat-label { font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 2px; }
    .stat-value { font-size: 22px; font-weight: 700; color: #0f172a; line-height: 1.1; }

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

    .btn-primary {
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
        background: #0f766e !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(15, 118, 110, .25);
    }

    .btn-primary:hover {
        background: #0c5f59 !important;
        color: #ffffff !important;
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

    .rule-action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #64748b;
        cursor: pointer;
        transition: all .15s ease;
    }

    .rule-action-btn:hover {
        color: #0f766e;
        border-color: #0f766e;
        background: #f0fdfa;
    }

    @media (max-width: 992px) {
        .ddi-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .ddi-stats {
            grid-template-columns: 1fr;
        }
    }
</style>
