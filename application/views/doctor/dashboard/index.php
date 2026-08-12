<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="doctor-dashboard-page">
    <!-- Welcome & Practitioner Overview Banner -->
    <div class="welcome-banner mb-4">
        <div class="banner-content">
            <span class="eyebrow-chip">
                <i class="bi bi-heart-pulse-fill"></i> Clinical Decision Support System
            </span>
            <h2 class="banner-title">Welcome, Dr. <?php echo html_escape($this->session->userdata('name') ?: 'Practitioner'); ?></h2>
            <p class="banner-desc">Evaluate multiregimen prescription safety, check adverse drug interactions, and safeguard patient treatments.</p>
        </div>
        <div class="banner-icon d-none d-md-flex">
            <i class="bi bi-prescription2"></i>
        </div>
    </div>

    <!-- Prescription Desk Notice -->
    <div class="card border-0 rounded-4 shadow-sm mb-4 notice-card">
        <div class="card-body p-4 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="notice-icon">
                    <i class="bi bi-clipboard2-pulse"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-1">Prescription Desk &amp; Interaction Checker</h5>
                    <p class="text-secondary mb-0 small">The interactive multi-drug prescription analyzer is authenticated and connected to the master clinical formulary.</p>
                </div>
            </div>
            <div>
                <span class="status-badge status-active px-3 py-2">
                    <i class="bi bi-check-circle-fill"></i> Portal Access Active
                </span>
            </div>
        </div>
    </div>

    <!-- Practitioner Profile & Clinical Credentials Overview -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 rounded-4 shadow-sm h-100 panel-card">
                <div class="card-header bg-white border-bottom py-3.5 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="bi bi-person-badge text-teal"></i>
                        <span>Practitioner Credentials</span>
                    </h5>
                    <span class="source-chip"><i class="bi bi-patch-check"></i> Verified Doctor</span>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="avatar-circle shadow-sm" style="width: 50px; height: 50px; background-color: #f0fdfa; border: 1px solid #99f6e4; color: #0f766e; font-size: 1.15rem; font-weight: 700;">
                            <?php
                            $name = $this->session->userdata('name') ?: 'Doctor';
                            $initials = '';
                            foreach (explode(' ', $name) as $w) {
                                $initials .= substr($w, 0, 1);
                            }
                            echo strtoupper(substr($initials, 0, 2)) ?: 'DR';
                            ?>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Dr. <?php echo html_escape($name); ?></h5>
                            <span class="text-muted small"><?php echo html_escape($this->session->userdata('email')); ?></span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-secondary small d-block">Specialization</span>
                            <strong class="text-dark"><?php echo !empty($profile->specialization) ? html_escape($profile->specialization) : 'General Practice'; ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-secondary small d-block">Qualification</span>
                            <strong class="text-dark"><?php echo !empty($profile->qualification) ? html_escape($profile->qualification) : 'MBBS / MD'; ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-secondary small d-block">Registration #</span>
                            <span class="stock-badge">
                                <i class="bi bi-shield-check text-teal"></i>
                                <?php echo !empty($profile->registration_number) ? html_escape($profile->registration_number) : '—'; ?>
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-secondary small d-block">Medical Council</span>
                            <strong class="text-dark"><?php echo !empty($profile->medical_council) ? html_escape($profile->medical_council) : 'State Medical Council'; ?></strong>
                        </div>
                        <div class="col-12">
                            <span class="text-secondary small d-block">Hospital / Clinic Affiliation</span>
                            <strong class="text-dark"><?php echo !empty($profile->hospital_clinic) ? html_escape($profile->hospital_clinic) : 'Private Practice'; ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 rounded-4 shadow-sm h-100 panel-card">
                <div class="card-header bg-white border-bottom py-3.5 px-4">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check text-teal"></i>
                        <span>Clinical Decision Safeguards</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3">
                        <div class="p-3 rounded-3 d-flex align-items-start gap-3" style="background-color: #fef2f2; border: 1px solid #fecaca;">
                            <div class="p-2 rounded-3" style="background-color: #fee2e2; color: #dc2626;">
                                <i class="bi bi-shield-fill-x fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Severe Interaction Alerts</h6>
                                <p class="text-muted small mb-0">Identifies contraindicated drug combinations that risk major cardiac, metabolic, or central adverse reactions.</p>
                            </div>
                        </div>
                        <div class="p-3 rounded-3 d-flex align-items-start gap-3" style="background-color: #fff7ed; border: 1px solid #fed7aa;">
                            <div class="p-2 rounded-3" style="background-color: #ffedd5; color: #ea580c;">
                                <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Moderate Interaction Warnings</h6>
                                <p class="text-muted small mb-0">Flags co-prescriptions requiring dosage adjustments or therapeutic drug monitoring.</p>
                            </div>
                        </div>
                        <div class="p-3 rounded-3 d-flex align-items-start gap-3" style="background-color: #f0fdfa; border: 1px solid #99f6e4;">
                            <div class="p-2 rounded-3" style="background-color: #ccfbf1; color: #0f766e;">
                                <i class="bi bi-check-circle-fill fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Stock &amp; Synonym Matching</h6>
                                <p class="text-muted small mb-0">Cross-references brand names, generic formulations, and available clinic inventory.</p>
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
        --teal-light: #14b8a6;
        --teal-bg: #f0fdfa;
        --teal-border: #99f6e4;
        --bg: #f8fafc;
        --surface: #ffffff;
        --border: #e6eaf0;
        --text: #0f172a;
        --text-soft: #64748b;
        --text-faint: #94a3b8;
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

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 999px;
    }

    .status-active {
        background: #ecfdf5;
        color: #059669;
    }

    @media (max-width: 767px) {
        .welcome-banner {
            padding: 20px;
            border-radius: 16px;
        }

        .banner-title {
            font-size: 19px;
        }

        .banner-desc {
            font-size: 13px;
        }
    }
</style>
