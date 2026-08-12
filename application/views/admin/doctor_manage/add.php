<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="form-page-container">
    <!-- Page Header & Navigation -->
    <div class="page-header mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="<?php echo base_url('admin/doctors'); ?>" class="btn-back" title="Back to Doctors">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <div class="page-eyebrow">
                    <i class="bi bi-person-plus-fill"></i> Practitioner Registration
                </div>
                <h2 class="page-title">Register Clinical Doctor</h2>
                <p class="page-subtitle">Create a certified practitioner account authorized for prescription evaluation.</p>
            </div>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="card border-0 rounded-4 shadow-sm form-card">
        <div class="card-header bg-white border-bottom py-3.5 px-4 d-flex align-items-center gap-3">
            <div class="header-icon-box">
                <i class="bi bi-person-plus-fill"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark">Practitioner Account &amp; Credentials</h5>
                <small class="text-muted">Fields marked with <span class="text-danger">*</span> are mandatory.</small>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <?php echo form_open('admin/doctors/add', ['id' => 'addDoctorForm', 'autocomplete' => 'off']); ?>
                <!-- Section 1: Personal & Contact Information -->
                <div class="form-section-heading mb-3">
                    <i class="bi bi-person-lines-fill me-2 text-teal"></i>
                    <span>1. Personal &amp; Contact Information</span>
                </div>
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="form-field">
                            <label for="name">Doctor Full Name <span class="req">*</span></label>
                            <div class="input-icon">
                                <i class="bi bi-person"></i>
                                <input type="text" id="name" name="name" value="<?php echo set_value('name'); ?>" required placeholder="Dr. Sarah Johnson">
                            </div>
                            <span class="field-hint">Include prefix (Dr.) and full medical legal name.</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-field">
                            <label for="email">Email Address <span class="req">*</span></label>
                            <div class="input-icon">
                                <i class="bi bi-envelope"></i>
                                <input type="email" id="email" name="email" value="<?php echo set_value('email'); ?>" required placeholder="doctor@clinic.com">
                            </div>
                            <span class="field-hint">Used for practitioner portal login and notifications.</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-field">
                            <label for="mobile">Mobile Number <span class="req">*</span></label>
                            <div class="input-icon">
                                <i class="bi bi-telephone"></i>
                                <input type="text" id="mobile" name="mobile" value="<?php echo set_value('mobile'); ?>" required placeholder="+1 (555) 0192">
                            </div>
                            <span class="field-hint">Primary direct contact number.</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-field">
                            <label for="address">Clinic / Practice Address</label>
                            <div class="input-icon">
                                <i class="bi bi-geo-alt"></i>
                                <input type="text" id="address" name="address" value="<?php echo set_value('address'); ?>" placeholder="Suite 400, Medical Plaza, City">
                            </div>
                            <span class="field-hint">Physical clinic or primary practice location.</span>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Clinical Credentials & Registration -->
                <div class="form-section-heading mb-3 pt-3 border-top">
                    <i class="bi bi-award-fill me-2 text-teal"></i>
                    <span>2. Clinical Credentials &amp; Registration</span>
                </div>
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="form-field">
                            <label for="registration_number">Medical Registration / License # <span class="req">*</span></label>
                            <div class="input-icon">
                                <i class="bi bi-card-checklist"></i>
                                <input type="text" id="registration_number" name="registration_number" value="<?php echo set_value('registration_number'); ?>" required placeholder="MED-109482">
                            </div>
                            <span class="field-hint">Official state or national medical licensing number.</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-field">
                            <label for="medical_council">Medical Council / Board</label>
                            <div class="input-icon">
                                <i class="bi bi-bank"></i>
                                <input type="text" id="medical_council" name="medical_council" value="<?php echo set_value('medical_council'); ?>" placeholder="e.g. State Medical Council">
                            </div>
                            <span class="field-hint">Authorizing regulatory medical board.</span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-field">
                            <label for="specialization">Primary Specialization</label>
                            <div class="input-icon">
                                <i class="bi bi-heart-pulse"></i>
                                <input type="text" id="specialization" name="specialization" value="<?php echo set_value('specialization'); ?>" placeholder="e.g. Cardiology, Internal Med">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-field">
                            <label for="qualification">Qualifications</label>
                            <div class="input-icon">
                                <i class="bi bi-mortarboard"></i>
                                <input type="text" id="qualification" name="qualification" value="<?php echo set_value('qualification'); ?>" placeholder="MBBS, MD, FACC">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-field">
                            <label for="years_experience">Years of Experience</label>
                            <div class="input-icon">
                                <i class="bi bi-hourglass-split"></i>
                                <input type="number" id="years_experience" name="years_experience" min="0" value="<?php echo set_value('years_experience'); ?>" placeholder="e.g. 12">
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-field">
                            <label for="hospital_clinic">Hospital / Clinic Affiliation</label>
                            <div class="input-icon">
                                <i class="bi bi-hospital"></i>
                                <input type="text" id="hospital_clinic" name="hospital_clinic" value="<?php echo set_value('hospital_clinic'); ?>" placeholder="City General Hospital / Private Practice">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Portal Login Credentials -->
                <div class="form-section-heading mb-3 pt-3 border-top">
                    <i class="bi bi-shield-lock-fill me-2 text-teal"></i>
                    <span>3. Portal Login Credentials</span>
                </div>
                <div class="row g-4 mb-3">
                    <div class="col-md-6">
                        <div class="form-field">
                            <label for="password">Initial Password (Min. 6 chars) <span class="req">*</span></label>
                            <div class="input-icon">
                                <i class="bi bi-lock"></i>
                                <input type="password" id="password" name="password" required placeholder="••••••••">
                            </div>
                            <span class="field-hint">Initial temporary password for practitioner access.</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-field">
                            <label for="confirm_password">Confirm Password <span class="req">*</span></label>
                            <div class="input-icon">
                                <i class="bi bi-shield-lock"></i>
                                <input type="password" id="confirm_password" name="confirm_password" required placeholder="••••••••">
                            </div>
                            <span class="field-hint">Re-type identical password for verification.</span>
                        </div>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="form-actions mt-5 pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <a href="<?php echo base_url('admin/doctors'); ?>" class="btn-page-ghost">
                        <i class="bi bi-arrow-left"></i> <span>Cancel &amp; Return</span>
                    </a>
                    <button type="submit" class="btn-page-primary">
                        <i class="bi bi-check-lg"></i> <span>Register Practitioner</span>
                    </button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<style>
    .form-page-container,
    .form-page-container * {
        font-family: 'Poppins', sans-serif;
        box-sizing: border-box;
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
    }

    .form-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
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

    .form-section-heading {
        font-size: 13.5px;
        font-weight: 700;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: .04em;
        display: flex;
        align-items: center;
    }

    .form-field {
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .form-field label {
        display: block !important;
        font-size: 13.5px !important;
        font-weight: 600 !important;
        color: #0f172a !important;
        margin-bottom: 8px !important;
    }

    .req {
        color: #dc2626 !important;
    }

    .field-hint {
        display: block;
        font-size: 12px;
        color: #94a3b8;
        margin-top: 6px;
    }

    .form-field input[type="text"],
    .form-field input[type="email"],
    .form-field input[type="number"],
    .form-field input[type="password"],
    .form-field select,
    .form-field textarea {
        width: 100% !important;
        background-color: #f8fafc !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 11px !important;
        padding: 11px 16px !important;
        font-size: 14px !important;
        color: #0f172a !important;
        outline: none !important;
        transition: border-color .15s ease, box-shadow .15s ease !important;
    }

    .form-field input[type="text"]:focus,
    .form-field input[type="email"]:focus,
    .form-field input[type="number"]:focus,
    .form-field input[type="password"]:focus,
    .form-field select:focus,
    .form-field textarea:focus {
        border-color: #0f766e !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(15, 118, 110, .18) !important;
    }

    .input-icon {
        position: relative !important;
        display: flex !important;
        align-items: center !important;
        width: 100% !important;
    }

    .input-icon > i {
        position: absolute !important;
        left: 14px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        color: #94a3b8 !important;
        font-size: 16px !important;
        pointer-events: none !important;
        z-index: 3 !important;
    }

    .form-field .input-icon input,
    .form-field .input-icon input[type="text"],
    .form-field .input-icon input[type="email"],
    .form-field .input-icon input[type="number"],
    .form-field .input-icon input[type="password"],
    .input-icon input {
        padding-left: 44px !important;
    }

    .btn-page-primary {
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        font-size: 14.5px !important;
        font-weight: 600 !important;
        border-radius: 12px !important;
        padding: 11px 26px !important;
        background-color: #0f766e !important;
        border: 1px solid #0f766e !important;
        color: #ffffff !important;
        cursor: pointer !important;
        transition: all .15s ease !important;
        box-shadow: 0 4px 14px rgba(15, 118, 110, .28) !important;
        text-decoration: none !important;
    }

    .btn-page-primary:hover {
        background-color: #0c5f59 !important;
        border-color: #0c5f59 !important;
        color: #ffffff !important;
        transform: translateY(-1px) !important;
    }

    .btn-page-ghost {
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        font-size: 14.5px !important;
        font-weight: 600 !important;
        border-radius: 12px !important;
        padding: 11px 22px !important;
        background-color: #ffffff !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1 !important;
        cursor: pointer !important;
        transition: all .15s ease !important;
        text-decoration: none !important;
    }

    .btn-page-ghost:hover {
        background-color: #f8fafc !important;
        border-color: #94a3b8 !important;
        color: #0f172a !important;
    }

    @media (max-width: 767px) {
        .form-card .card-body {
            padding: 20px 16px !important;
        }

        .form-actions {
            flex-direction: column-reverse;
            align-items: stretch !important;
        }

        .btn-page-primary,
        .btn-page-ghost {
            width: 100% !important;
            justify-content: center !important;
        }
    }
</style>
