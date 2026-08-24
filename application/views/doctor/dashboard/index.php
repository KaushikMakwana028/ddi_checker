<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="doctor-dashboard-page">
    <!-- Welcome & Practitioner Overview Banner -->
    <div class="welcome-banner mb-4">
        <div class="banner-content">
            <span class="eyebrow-chip">
                <i class="bi bi-heart-pulse-fill"></i> Clinical Decision Support System
            </span>
            <h2 class="banner-title">Welcome, Dr. <?php echo html_escape($this->session->userdata('doctor_name') ?: 'Practitioner'); ?></h2>
            <p class="banner-desc">Evaluate multiregimen prescription safety, check adverse drug interactions, and safeguard patient treatments.</p>
        </div>
        <div class="banner-icon d-none d-md-flex">
            <i class="bi bi-prescription2"></i>
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="ddi-stats mb-4">
        <div class="stat-card stat-total">
            <div class="stat-main">
                <div class="stat-icon-wrap">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-meta">
                    <span class="stat-label">Total Patients</span>
                    <h3 class="stat-value"><?php echo number_format($stats['total_patients']); ?></h3>
                </div>
            </div>
            <div class="stat-footer">
                <span>Clinical registry file</span>
                <a href="<?php echo base_url('doctor/history'); ?>" class="stat-link">View <i class="bi bi-chevron-right"></i></a>
            </div>
        </div>

        <div class="stat-card stat-blue">
            <div class="stat-main">
                <div class="stat-icon-wrap">
                    <i class="bi bi-journal-medical"></i>
                </div>
                <div class="stat-meta">
                    <span class="stat-label">Total Prescriptions</span>
                    <h3 class="stat-value"><?php echo number_format($stats['total_prescriptions']); ?></h3>
                </div>
            </div>
            <div class="stat-footer">
                <span>All-time issued</span>
                <a href="<?php echo base_url('doctor/history'); ?>" class="stat-link">History <i class="bi bi-chevron-right"></i></a>
            </div>
        </div>

        <div class="stat-card stat-active">
            <div class="stat-main">
                <div class="stat-icon-wrap" style="background: #ecfdf5; color: #059669;">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stat-meta">
                    <span class="stat-label">Prescriptions Today</span>
                    <h3 class="stat-value"><?php echo number_format($stats['prescriptions_today']); ?></h3>
                </div>
            </div>
            <div class="stat-footer">
                <span>Clinical sessions today</span>
                <a href="<?php echo base_url('doctor/prescription-desk'); ?>" class="stat-link" style="color: #059669;">New Rx <i class="bi bi-chevron-right"></i></a>
            </div>
        </div>

        <div class="stat-card stat-purple">
            <div class="stat-main">
                <div class="stat-icon-wrap">
                    <i class="bi bi-capsule"></i>
                </div>
                <div class="stat-meta">
                    <span class="stat-label">Formulary Drugs</span>
                    <h3 class="stat-value"><?php echo number_format($stats['total_drugs']); ?></h3>
                </div>
            </div>
            <div class="stat-footer">
                <span>Active DDI database</span>
                <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1" style="font-size: 0.65rem;">Updated</span>
            </div>
        </div>
    </div>

    <!-- Prescription Desk Quick Access Card -->
    <div class="card border-0 rounded-4 shadow-sm mb-4 notice-card">
        <div class="card-body p-4 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="notice-icon">
                    <i class="bi bi-clipboard2-pulse"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-1">Prescription Desk &amp; Interaction Checker</h5>
                    <p class="text-secondary mb-0 small">Access the interactive multi-drug prescription analyzer, check safety alerts, and generate invoices.</p>
                </div>
            </div>
            <div>
                <a href="<?php echo base_url('doctor/prescription-desk'); ?>" class="btn btn-teal fw-semibold px-4 py-2.5 rounded-3 d-inline-flex align-items-center gap-1.5 shadow-sm">
                    <i class="bi bi-plus-circle"></i> Open Rx Desk
                </a>
            </div>
        </div>
    </div>

    <!-- Practitioner Profile & Clinical Safeguards -->
    <div class="row g-4">
        <!-- Doctor Profile Card -->
        <div class="col-lg-6">
            <div class="card border-0 rounded-4 shadow-sm h-100 panel-card">
                <div class="card-header bg-white border-bottom py-3.5 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-person-badge text-teal" style="color: #0f766e;"></i>
                        <span>Practitioner Credentials</span>
                    </h5>
                    <span class="source-chip"><i class="bi bi-patch-check"></i> Verified Doctor</span>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="avatar-circle shadow-sm" style="width: 52px; height: 52px; background-color: #f0fdfa; border: 1px solid #99f6e4; color: #0f766e; font-size: 1.2rem; font-weight: 700; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <?php
                            $name = $this->session->userdata('doctor_name') ?: 'Doctor';
                            $initials = '';
                            foreach (explode(' ', $name) as $w) {
                                $initials .= substr($w, 0, 1);
                            }
                            echo strtoupper(substr($initials, 0, 2)) ?: 'DR';
                            ?>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0.5">Dr. <?php echo html_escape($name); ?></h5>
                            <span class="text-secondary small"><?php echo html_escape($this->session->userdata('doctor_email')); ?></span>
                        </div>
                    </div>

                    <div class="row g-3 text-dark">
                        <div class="col-sm-6">
                            <span class="text-secondary small d-block mb-0.5">Specialization</span>
                            <strong class="fw-semibold text-dark"><?php echo !empty($profile->specialization) ? html_escape($profile->specialization) : 'General Practice'; ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-secondary small d-block mb-0.5">Qualification</span>
                            <strong class="fw-semibold text-dark"><?php echo !empty($profile->qualification) ? html_escape($profile->qualification) : 'MBBS / MD'; ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-secondary small d-block mb-1">Registration #</span>
                            <span class="stock-badge" style="font-size: 12px; background-color: #f8fafc; border: 1px solid #e2e8f0; color: #0f172a; padding: 3px 9px; border-radius: 6px;">
                                <i class="bi bi-shield-check text-teal" style="color: #0f766e;"></i>
                                <?php echo !empty($profile->registration_number) ? html_escape($profile->registration_number) : '—'; ?>
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-secondary small d-block mb-0.5">Medical Council</span>
                            <strong class="fw-semibold text-dark"><?php echo !empty($profile->medical_council) ? html_escape($profile->medical_council) : 'State Medical Council'; ?></strong>
                        </div>
                        <div class="col-12 mt-3 pt-2 border-top">
                            <span class="text-secondary small d-block mb-0.5">Hospital / Clinic Affiliation</span>
                            <strong class="fw-semibold text-dark"><?php echo !empty($profile->hospital_clinic) ? html_escape($profile->hospital_clinic) : 'Private Practice'; ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Safeguards Card -->
        <div class="col-lg-6">
            <div class="card border-0 rounded-4 shadow-sm h-100 panel-card">
                <div class="card-header bg-white border-bottom py-3.5 px-4">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-shield-check text-teal" style="color: #0f766e;"></i>
                        <span>Clinical Decision Safeguards</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3">
                        <div class="p-3 rounded-3 d-flex align-items-start gap-3 border" style="background-color: #fef2f2; border-color: #fecaca !important;">
                            <div class="p-2 rounded-3" style="background-color: #fee2e2; color: #dc2626;">
                                <i class="bi bi-shield-fill-x fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Severe Interaction Alerts</h6>
                                <p class="text-secondary small mb-0" style="line-height: 1.45;">Identifies contraindicated drug combinations that risk major cardiac, metabolic, or central adverse reactions.</p>
                            </div>
                        </div>
                        <div class="p-3 rounded-3 d-flex align-items-start gap-3 border" style="background-color: #fff7ed; border-color: #fed7aa !important;">
                            <div class="p-2 rounded-3" style="background-color: #ffedd5; color: #ea580c;">
                                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Moderate Interaction Warnings</h6>
                                <p class="text-secondary small mb-0" style="line-height: 1.45;">Flags co-prescriptions requiring dosage adjustments or therapeutic drug monitoring.</p>
                            </div>
                        </div>
                        <div class="p-3 rounded-3 d-flex align-items-start gap-3 border" style="background-color: #f0fdfa; border-color: #99f6e4 !important;">
                            <div class="p-2 rounded-3" style="background-color: #ccfbf1; color: #0f766e;">
                                <i class="bi bi-check-circle-fill fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Stock &amp; Synonym Matching</h6>
                                <p class="text-secondary small mb-0" style="line-height: 1.45;">Cross-references brand names, generic formulations, and available clinic inventory.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .doctor-dashboard-page,
    .doctor-dashboard-page * {
        font-family: 'Poppins', sans-serif;
        box-sizing: border-box;
    }

    .doctor-dashboard-page {
        --teal: #0f766e;
        --teal-hover: #0d9488;
        --teal-bg: #f0fdfa;
        --teal-border: #99f6e4;
        --bg: #f8fafc;
        --surface: #ffffff;
        --border: #e6eaf0;
        --text: #0f172a;
        --text-soft: #64748b;
    }

    .welcome-banner {
        background: linear-gradient(135deg, #0f766e 0%, #0f172a 100%);
        border-radius: 20px;
        padding: 32px 36px;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        box-shadow: 0 10px 25px -5px rgba(15, 118, 110, 0.25);
    }

    .eyebrow-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(204, 251, 241, 0.2);
        color: #ccfbf1;
        border: 1px solid rgba(204, 251, 241, 0.3);
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 600;
        letter-spacing: .05em;
        text-transform: uppercase;
        padding: 4px 12px;
        margin-bottom: 12px;
    }

    .banner-title {
        font-size: 24px;
        font-weight: 700;
        margin: 0 0 6px;
        line-height: 1.25;
    }

    .banner-desc {
        color: rgba(241, 245, 249, 0.85);
        font-size: 14px;
        margin: 0;
        max-width: 580px;
        line-height: 1.5;
    }

    .banner-icon i {
        font-size: 72px;
        color: rgba(255, 255, 255, 0.2);
    }

    /* Stat Cards */
    .ddi-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 18px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
    }

    .stat-main {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .stat-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
        flex-shrink: 0;
    }

    .stat-total .stat-icon-wrap { background: #f0fdfa; color: #0f766e; }
    .stat-blue .stat-icon-wrap { background: #eff6ff; color: #0284c7; }
    .stat-active .stat-icon-wrap { background: #ecfdf5; color: #059669; }
    .stat-purple .stat-icon-wrap { background: #fdf4ff; color: #c026d3; }

    .stat-meta {
        min-width: 0;
    }

    .stat-label {
        font-size: 12px;
        font-weight: 500;
        color: #64748b;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .stat-value {
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        line-height: 1.15;
    }

    .stat-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 12px;
        color: #64748b;
        border-top: 1px solid #f1f5f9;
        padding-top: 10px;
    }

    .stat-link {
        color: #0f766e;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        transition: gap .15s ease;
    }

    .stat-link:hover {
        gap: 6px;
        color: #0d9488;
    }

    .notice-card {
        background-color: #f0fdfa;
        border: 1px solid #99f6e4 !important;
    }

    .notice-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background-color: #0f766e;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .panel-card {
        border: 1px solid #e6eaf0 !important;
        overflow: hidden;
    }

    .source-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #64748b;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: 4px 10px;
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

    .btn-teal {
        background-color: #0f766e;
        border-color: #0f766e;
        color: #ffffff;
    }
    .btn-teal:hover, .btn-teal:focus {
        background-color: #0d9488;
        border-color: #0d9488;
        color: #ffffff;
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
