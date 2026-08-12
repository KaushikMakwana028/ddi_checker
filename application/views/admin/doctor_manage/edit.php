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
                    <i class="bi bi-pencil-square"></i> Practitioner Account
                </div>
                <h2 class="page-title">Edit Doctor Details</h2>
                <p class="page-subtitle">Update credentials, affiliations, or reset login password for <strong><?php echo html_escape($doctor->name); ?></strong>.</p>
            </div>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="card border-0 rounded-4 shadow-sm form-card">
        <div class="card-header bg-white border-bottom py-3.5 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div class="header-icon-box">
                    <i class="bi bi-person-badge"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark"><?php echo html_escape($doctor->name); ?></h5>
                    <small class="text-muted">Doctor ID: #<?php echo $doctor->id; ?> • Registered <?php echo date('M d, Y', strtotime($doctor->created_at)); ?></small>
                </div>
            </div>
            <?php if ($doctor->is_active): ?>
                <span class="status-badge status-active"><i class="bi bi-dot"></i>Active Access</span>
            <?php else: ?>
                <span class="status-badge status-inactive"><i class="bi bi-dot"></i>Suspended / Inactive</span>
            <?php endif; ?>
        </div>

        <div class="card-body p-4 p-md-5">
            <?php echo form_open('admin/doctors/edit/' . $doctor->id, ['id' => 'editDoctorForm', 'autocomplete' => 'off']); ?>
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
                                <input type="text" id="name" name="name" value="<?php echo set_value('name', $doctor->name); ?>" required placeholder="Dr. John Doe">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-field">
                            <label for="email">Email Address <span class="req">*</span></label>
                            <div class="input-icon">
                                <i class="bi bi-envelope"></i>
                                <input type="email" id="email" name="email" value="<?php echo set_value('email', $doctor->email); ?>" required placeholder="doctor@clinic.com">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-field">
                            <label for="mobile">Mobile Number <span class="req">*</span></label>
                            <div class="input-icon">
                                <i class="bi bi-telephone"></i>
                                <input type="text" id="mobile" name="mobile" value="<?php echo set_value('mobile', $doctor->mobile); ?>" required placeholder="+1 555-0192">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-field">
                            <label for="address">Clinic / Practice Address</label>
                            <div class="input-icon">
                                <i class="bi bi-geo-alt"></i>
                                <input type="text" id="address" name="address" value="<?php echo set_value('address', $doctor->address); ?>" placeholder="Suite 400, Medical Plaza">
                            </div>
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
                                <input type="text" id="registration_number" name="registration_number" value="<?php echo set_value('registration_number', $profile ? $profile->registration_number : ''); ?>" required placeholder="MED-109482">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-field">
                            <label for="medical_council">Medical Council / Board</label>
                            <div class="input-icon">
                                <i class="bi bi-bank"></i>
                                <input type="text" id="medical_council" name="medical_council" value="<?php echo set_value('medical_council', $profile ? $profile->medical_council : ''); ?>" placeholder="e.g. State Medical Board">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-field">
                            <label for="specialization">Specialization</label>
                            <div class="input-icon">
                                <i class="bi bi-heart-pulse"></i>
                                <input type="text" id="specialization" name="specialization" value="<?php echo set_value('specialization', $profile ? $profile->specialization : ''); ?>" placeholder="e.g. Cardiology">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-field">
                            <label for="qualification">Qualification</label>
                            <div class="input-icon">
                                <i class="bi bi-mortarboard"></i>
                                <input type="text" id="qualification" name="qualification" value="<?php echo set_value('qualification', $profile ? $profile->qualification : ''); ?>" placeholder="MBBS, MD">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-field">
                            <label for="years_experience">Years of Experience</label>
                            <div class="input-icon">
                                <i class="bi bi-hourglass-split"></i>
                                <input type="number" id="years_experience" name="years_experience" min="0" value="<?php echo set_value('years_experience', $profile ? $profile->years_experience : ''); ?>" placeholder="e.g. 10">
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-field">
                            <label for="hospital_clinic">Hospital / Clinic Affiliation</label>
                            <div class="input-icon">
                                <i class="bi bi-hospital"></i>
                                <input type="text" id="hospital_clinic" name="hospital_clinic" value="<?php echo set_value('hospital_clinic', $profile ? $profile->hospital_clinic : ''); ?>" placeholder="City General Hospital">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Reset Login Password (Optional) -->
                <div class="form-section-heading mb-3 pt-3 border-top">
                    <i class="bi bi-shield-lock-fill me-2 text-teal"></i>
                    <span>3. Reset Login Password (Leave blank to keep unchanged)</span>
                </div>
                <div class="row g-4 mb-3">
                    <div class="col-md-6">
                        <div class="form-field">
                            <label for="password">New Password (Min. 6 chars)</label>
                            <div class="input-icon">
                                <i class="bi bi-lock"></i>
                                <input type="password" id="password" name="password" placeholder="Leave empty to retain current">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-field">
                            <label for="confirm_password">Confirm New Password</label>
                            <div class="input-icon">
                                <i class="bi bi-shield-lock"></i>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat new password">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="form-actions mt-5 pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <a href="<?php echo base_url('admin/doctors'); ?>" class="btn-page-ghost">
                        <i class="bi bi-arrow-left"></i> <span>Cancel &amp; Return</span>
                    </a>
                    <button type="submit" class="btn-page-primary">
                        <i class="bi bi-check-lg"></i> <span>Save &amp; Update Practitioner</span>
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

    .status-badge {
        display: inline-flex;
        align-items: center;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px 4px 6px;
        border-radius: 999px;
    }
    .status-badge i { font-size: 20px; line-height: 0; }
    .status-active { background: #ecfdf5; color: #059669; }
    .status-inactive { background: #fef2f2; color: #dc2626; }

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
