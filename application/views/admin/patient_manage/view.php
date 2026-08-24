<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="patient-profile-page">
    <!-- Page Header & Navigation -->
    <div class="page-header mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="<?php echo base_url('admin/patients'); ?>" class="btn-back" title="Back to Patients">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <div class="page-eyebrow">
                    <i class="bi bi-person-badge-fill"></i> Patient Registry
                </div>
                <h2 class="page-title">Patient Profile</h2>
                <p class="page-subtitle">View clinical profiles, doctor assignments, and previous prescription history logs.</p>
            </div>
        </div>
    </div>

    <!-- Main Profile Card Header -->
    <div class="card border-0 rounded-4 shadow-sm form-card mb-4">
        <div class="pp-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div class="header-icon-box">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark"><?php echo html_escape($patient->full_name); ?></h5>
                    <small class="text-muted">Patient ID: #<?php echo $patient->id; ?> &bull; Registered <?php echo date('M d, Y', strtotime($patient->created_at)); ?></small>
                </div>
            </div>
            <span class="status-badge status-active"><i class="bi bi-dot"></i>Active Record</span>
        </div>

        <div class="card-body p-4 p-md-5">
            <div class="row g-4">
                <!-- Left: Clinical Profile Info -->
                <div class="col-lg-7">
                    <div class="form-section-heading mb-3">
                        <i class="bi bi-heart-pulse-fill me-2 text-teal"></i>
                        <span>1. Medical Profile Info</span>
                    </div>

                    <div class="row g-4 mb-4 border-bottom pb-4">
                        <div class="col-sm-4">
                            <span class="pp-field-label">Age &amp; Gender</span>
                            <div class="pp-field-value">
                                <?php echo html_escape($patient->age); ?> Yrs / <?php echo html_escape($patient->gender); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <span class="pp-field-label">Height (cm)</span>
                            <div class="pp-field-value">
                                <?php echo !empty($patient->height_cm) ? html_escape($patient->height_cm) . ' cm' : '&mdash;'; ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <span class="pp-field-label">Weight (kg)</span>
                            <div class="pp-field-value">
                                <?php echo !empty($patient->weight_kg) ? html_escape($patient->weight_kg) . ' kg' : '&mdash;'; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <span class="pp-field-label pp-field-label-block">Chief Complaints</span>
                        <div class="pp-notes-box">
                            <?php echo !empty($patient->chief_complaints) ? nl2br(html_escape($patient->chief_complaints)) : '<span class="text-muted fst-italic">No complaints documented.</span>'; ?>
                        </div>
                    </div>

                    <div class="mb-0">
                        <span class="pp-field-label pp-field-label-block">Medical History</span>
                        <div class="pp-notes-box">
                            <?php echo !empty($patient->medical_history) ? nl2br(html_escape($patient->medical_history)) : '<span class="text-muted fst-italic">No medical history documented.</span>'; ?>
                        </div>
                    </div>
                </div>

                <!-- Right: Attending Doctor Details -->
                <div class="col-lg-5">
                    <div class="form-section-heading mb-3">
                        <i class="bi bi-person-workspace me-2 text-teal"></i>
                        <span>2. Attending Practitioner</span>
                    </div>

                    <div class="pp-doctor-card">
                        <div class="pp-doctor-avatar">
                            <i class="bi bi-person-fill-gear"></i>
                        </div>
                        <h4 class="pp-doctor-name"><?php echo html_escape($patient->doctor_name ?: 'Not Assigned'); ?></h4>
                        <div class="pp-doctor-role">Attending Medical Practitioner</div>

                        <hr class="pp-doctor-divider">

                        <div class="text-start">
                            <div class="pp-detail-row">
                                <span class="pp-detail-label">Hospital / Clinic</span>
                                <span class="pp-detail-value"><?php echo !empty($patient->hospital_name) ? html_escape($patient->hospital_name) : 'No clinic details'; ?></span>
                            </div>
                            <div class="pp-detail-row pp-detail-row-last">
                                <span class="pp-detail-label">Contact Info</span>
                                <span class="pp-detail-value"><?php echo html_escape($patient->contact_number ?: 'N/A'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Prescription Regimen History Panel -->
    <div class="panel-card mt-4">
        <div class="pp-card-header">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-receipt text-teal"></i>
                <span>Prescription Regimen History</span>
            </h5>
        </div>

        <?php if (empty($prescriptions)): ?>
            <div class="empty-state py-5 text-center">
                <i class="bi bi-receipt pp-empty-icon"></i>
                <h3 class="fw-bold text-dark mt-2 pp-empty-title">No Regimens Issued</h3>
                <p class="text-secondary small mb-0">No prescription records have been issued for this patient profile yet.</p>
            </div>
        <?php else: ?>

            <!-- Desktop Table View -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle mb-0 ddi-table">
                    <thead>
                        <tr>
                            <th style="width: 20%;">Invoice No.</th>
                            <th style="width: 18%;">Visit Date</th>
                            <th style="width: 25%;">Attending Practitioner</th>
                            <th style="width: 22%;">Affiliated Hospital</th>
                            <th style="width: 15%;" class="text-center"># Medicines</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prescriptions as $p): ?>
                            <tr>
                                <td class="fw-bold pp-invoice-cell"><?php echo html_escape($p['invoice_number'] ?: '—'); ?></td>
                                <td><?php echo date('d-M-Y', strtotime($p['visit_date'])); ?></td>
                                <td class="fw-semibold text-dark"><?php echo html_escape($p['doctor_name'] ?: '—'); ?></td>
                                <td class="text-secondary small fw-medium"><?php echo html_escape($p['hospital_name'] ?: '—'); ?></td>
                                <td class="text-center">
                                    <span class="stock-badge"><?php echo html_escape($p['medicine_count']); ?> Med(s)</span>
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
                            <div class="pp-record-card">
                                <div class="pp-record-top">
                                    <div class="pp-record-invoice-wrap">
                                        <span class="pp-record-invoice"><?php echo html_escape($p['invoice_number'] ?: '—'); ?></span>
                                        <span class="pp-record-date"><?php echo date('d-M-Y', strtotime($p['visit_date'])); ?></span>
                                    </div>
                                    <span class="stock-badge"><?php echo html_escape($p['medicine_count']); ?> Med(s)</span>
                                </div>
                                <div class="pp-record-body">
                                    <div class="pp-record-row">
                                        <span class="pp-record-key"><i class="bi bi-person-workspace me-1"></i>Practitioner</span>
                                        <span class="pp-record-val"><?php echo html_escape($p['doctor_name'] ?: '—'); ?></span>
                                    </div>
                                    <div class="pp-record-row">
                                        <span class="pp-record-key"><i class="bi bi-hospital me-1"></i>Hospital</span>
                                        <span class="pp-record-val"><?php echo html_escape($p['hospital_name'] ?: '—'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php endif; ?>
    </div>
</div>

<style>
    .patient-profile-page,
    .patient-profile-page * {
        font-family: 'Poppins', sans-serif;
        box-sizing: border-box;
    }

    .patient-profile-page {
        max-width: 1280px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .btn-back {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        text-decoration: none;
        transition: all .15s ease;
        flex-shrink: 0;
    }

    .btn-back:hover {
        background: #f0fdfa;
        border-color: #0f766e;
        color: #0f766e;
        transform: translateX(-2px);
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
        margin-bottom: 6px;
    }

    .page-title {
        font-size: 24px;
        font-weight: 700;
        margin: 0 0 4px;
        color: #0f172a;
        line-height: 1.25;
    }

    .page-subtitle {
        font-size: 14px;
        color: #64748b;
        margin: 0;
        max-width: 560px;
    }

    .form-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
    }

    /* Fixed: was `py-3.5` (not a real Bootstrap class), causing inconsistent header padding */
    .pp-card-header {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 1.1rem 1.5rem;
    }

    .header-icon-box {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: #f0fdfa;
        border: 1px solid #99f6e4;
        color: #0f766e;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px 4px 6px;
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

    .form-section-heading {
        font-size: 13.5px;
        font-weight: 700;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: .04em;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 8px;
    }

    /* ---------- Field labels/values ---------- */
    .pp-field-label {
        display: block;
        font-size: 12.5px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #64748b;
        margin-bottom: 4px;
    }

    .pp-field-label-block {
        margin-bottom: 8px;
    }

    .pp-field-value {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.98rem;
    }

    .pp-notes-box {
        padding: 0.9rem 1rem;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        color: #0f172a;
        min-height: 80px;
        font-size: 0.92rem;
        line-height: 1.55;
    }

    /* ---------- Attending doctor card ---------- */
    .pp-doctor-card {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.75rem 1.5rem;
        text-align: center;
    }

    .pp-doctor-avatar {
        width: 76px;
        height: 76px;
        margin: 0 auto 0.9rem;
        border-radius: 50%;
        background-color: #f0fdfa;
        color: #0f766e;
        border: 1px solid #99f6e4;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.1rem;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
    }

    .pp-doctor-name {
        font-weight: 700;
        color: #0f172a;
        font-size: 1.15rem;
        margin: 0 0 4px;
    }

    .pp-doctor-role {
        color: #0f766e;
        font-weight: 600;
        font-size: 0.85rem;
        margin-bottom: 0.9rem;
    }

    .pp-doctor-divider {
        margin: 0.9rem 0;
        border: none;
        border-top: 1px solid #e2e8f0;
        opacity: 1;
    }

    .pp-detail-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding-bottom: 0.6rem;
        margin-bottom: 0.6rem;
        border-bottom: 1px dashed #e2e8f0;
    }

    .pp-detail-row-last {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .pp-detail-label {
        font-size: 0.82rem;
        color: #64748b;
        flex-shrink: 0;
    }

    .pp-detail-value {
        font-size: 0.85rem;
        font-weight: 600;
        color: #0f172a;
        text-align: right;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 60%;
    }

    /* ---------- Panel Card & Table Styles ---------- */
    .panel-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
    }

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

    .ddi-table tbody tr:last-child td {
        border-bottom: none;
    }

    .pp-invoice-cell {
        color: #0f766e;
    }

    .stock-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12.5px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 999px;
        background: #f0fdfa;
        border: 1px solid #99f6e4;
        color: #0f766e;
    }

    /* ---------- Empty state ---------- */
    .pp-empty-icon {
        font-size: 2.5rem;
        color: #94a3b8;
    }

    .pp-empty-title {
        font-size: 1.15rem;
    }

    /* ---------- Mobile prescription cards ---------- */
    .pp-record-card {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1rem;
    }

    .pp-record-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        padding-bottom: 0.75rem;
        margin-bottom: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .pp-record-invoice {
        display: block;
        font-weight: 700;
        color: #0f766e;
        font-size: 0.98rem;
    }

    .pp-record-date {
        display: block;
        color: #64748b;
        font-size: 0.8rem;
        margin-top: 2px;
    }

    .pp-record-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        font-size: 0.84rem;
        padding: 4px 0;
    }

    .pp-record-key {
        color: #64748b;
        display: flex;
        align-items: center;
    }

    .pp-record-val {
        color: #0f172a;
        font-weight: 600;
        text-align: right;
    }

    /* ---------- Mobile ---------- */
    @media (max-width: 767.98px) {
        .patient-profile-page {
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }

        .page-header .d-flex {
            align-items: flex-start;
        }

        .page-subtitle {
            max-width: 100%;
        }

        .pp-card-header {
            padding: 1rem 1.15rem;
        }

        .card-body.p-4.p-md-5 {
            padding: 1.15rem !important;
        }

        .pp-detail-value {
            max-width: 55%;
            white-space: normal;
        }
    }

    @media (max-width: 420px) {
        .status-badge {
            font-size: 11px;
        }
    }
</style>