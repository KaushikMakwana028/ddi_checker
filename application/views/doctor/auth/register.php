<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? html_escape($title) : 'Doctor Registration'; ?> - DDI Checker</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #134e4a 50%, #042f2e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
            color: #0f172a;
        }

        .auth-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            background: #ffffff;
            width: 100%;
            max-width: 780px;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
        }

        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #0d9488 0%, #2dd4bf 100%);
        }

        .btn-teal {
            background-color: #0d9488;
            border-color: #0d9488;
            color: #ffffff;
            font-weight: 600;
            padding: 0.65rem 1rem;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .btn-teal:hover, .btn-teal:focus {
            background-color: #0f766e;
            border-color: #0f766e;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.25);
        }

        .form-control {
            border-radius: 10px;
            font-size: 0.95rem;
            padding: 0.6rem 0.9rem;
            border-color: #cbd5e1;
        }

        .form-control:focus {
            border-color: #0d9488;
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
        }

        .input-group-text {
            border-radius: 10px;
            border-color: #cbd5e1;
        }

        .doctor-tag {
            background-color: rgba(13, 148, 136, 0.1);
            color: #0d9488;
            font-weight: 700;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-block;
        }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle mb-2" style="background-color: #ccfbf1;">
            <i class="bi bi-person-badge-fill fs-2" style="color: #0d9488;"></i>
        </div>
        <div>
            <span class="doctor-tag mb-2">PRACTITIONER SELF-REGISTRATION</span>
        </div>
        <h3 class="fw-bold text-dark mb-1">Doctor Portal Sign Up</h3>
        <p class="text-muted small">Register your clinical credentials to access the Drug-Drug Interaction Portal</p>
    </div>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 py-2.5 small" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo html_escape($this->session->flashdata('error')); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php echo form_open('doctor/register', ['class' => 'needs-validation']); ?>
        <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">1. Personal & Contact Information</h6>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="name" class="form-label fw-semibold text-secondary small mb-1">Doctor Full Name <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                    <input type="text" name="name" id="name" class="form-control bg-light border-start-0 <?php echo form_error('name') ? 'is-invalid' : ''; ?>" placeholder="Dr. John Doe" value="<?php echo set_value('name'); ?>" required>
                </div>
                <?php echo form_error('name', '<div class="text-danger small mt-1">', '</div>'); ?>
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label fw-semibold text-secondary small mb-1">Email Address <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" id="email" class="form-control bg-light border-start-0 <?php echo form_error('email') ? 'is-invalid' : ''; ?>" placeholder="doctor@clinic.com" value="<?php echo set_value('email'); ?>" required>
                </div>
                <?php echo form_error('email', '<div class="text-danger small mt-1">', '</div>'); ?>
            </div>

            <div class="col-md-6">
                <label for="mobile" class="form-label fw-semibold text-secondary small mb-1">Mobile Phone <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-telephone"></i></span>
                    <input type="text" name="mobile" id="mobile" class="form-control bg-light border-start-0 <?php echo form_error('mobile') ? 'is-invalid' : ''; ?>" placeholder="+1 (555) 019-2834" value="<?php echo set_value('mobile'); ?>" required>
                </div>
                <?php echo form_error('mobile', '<div class="text-danger small mt-1">', '</div>'); ?>
            </div>

            <div class="col-md-6">
                <label for="address" class="form-label fw-semibold text-secondary small mb-1">Clinic / Hospital Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-geo-alt"></i></span>
                    <input type="text" name="address" id="address" class="form-control bg-light border-start-0 <?php echo form_error('address') ? 'is-invalid' : ''; ?>" placeholder="Suite 200, Health Plaza" value="<?php echo set_value('address'); ?>">
                </div>
                <?php echo form_error('address', '<div class="text-danger small mt-1">', '</div>'); ?>
            </div>
        </div>

        <h6 class="fw-bold text-dark mb-3 mt-4 border-bottom pb-2">2. Clinical License & Credentials</h6>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="registration_number" class="form-label fw-semibold text-secondary small mb-1">Medical Registration # <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-card-checklist"></i></span>
                    <input type="text" name="registration_number" id="registration_number" class="form-control bg-light border-start-0 <?php echo form_error('registration_number') ? 'is-invalid' : ''; ?>" placeholder="MED-123456" value="<?php echo set_value('registration_number'); ?>" required>
                </div>
                <?php echo form_error('registration_number', '<div class="text-danger small mt-1">', '</div>'); ?>
            </div>

            <div class="col-md-6">
                <label for="medical_council" class="form-label fw-semibold text-secondary small mb-1">Medical Council / Board</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-building"></i></span>
                    <input type="text" name="medical_council" id="medical_council" class="form-control bg-light border-start-0 <?php echo form_error('medical_council') ? 'is-invalid' : ''; ?>" placeholder="State Medical Council" value="<?php echo set_value('medical_council'); ?>">
                </div>
                <?php echo form_error('medical_council', '<div class="text-danger small mt-1">', '</div>'); ?>
            </div>

            <div class="col-md-4">
                <label for="specialization" class="form-label fw-semibold text-secondary small mb-1">Specialization</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-award"></i></span>
                    <input type="text" name="specialization" id="specialization" class="form-control bg-light border-start-0 <?php echo form_error('specialization') ? 'is-invalid' : ''; ?>" placeholder="e.g. Cardiology" value="<?php echo set_value('specialization'); ?>">
                </div>
                <?php echo form_error('specialization', '<div class="text-danger small mt-1">', '</div>'); ?>
            </div>

            <div class="col-md-4">
                <label for="qualification" class="form-label fw-semibold text-secondary small mb-1">Qualification</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-mortarboard"></i></span>
                    <input type="text" name="qualification" id="qualification" class="form-control bg-light border-start-0 <?php echo form_error('qualification') ? 'is-invalid' : ''; ?>" placeholder="MBBS, MD" value="<?php echo set_value('qualification'); ?>">
                </div>
                <?php echo form_error('qualification', '<div class="text-danger small mt-1">', '</div>'); ?>
            </div>

            <div class="col-md-4">
                <label for="years_experience" class="form-label fw-semibold text-secondary small mb-1">Years of Experience</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-clock-history"></i></span>
                    <input type="number" name="years_experience" id="years_experience" class="form-control bg-light border-start-0 <?php echo form_error('years_experience') ? 'is-invalid' : ''; ?>" min="0" placeholder="e.g. 8" value="<?php echo set_value('years_experience'); ?>">
                </div>
                <?php echo form_error('years_experience', '<div class="text-danger small mt-1">', '</div>'); ?>
            </div>

            <div class="col-12">
                <label for="hospital_clinic" class="form-label fw-semibold text-secondary small mb-1">Hospital / Clinic Affiliation</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-hospital"></i></span>
                    <input type="text" name="hospital_clinic" id="hospital_clinic" class="form-control bg-light border-start-0 <?php echo form_error('hospital_clinic') ? 'is-invalid' : ''; ?>" placeholder="Memorial Hospital" value="<?php echo set_value('hospital_clinic'); ?>">
                </div>
                <?php echo form_error('hospital_clinic', '<div class="text-danger small mt-1">', '</div>'); ?>
            </div>
        </div>

        <h6 class="fw-bold text-dark mb-3 mt-4 border-bottom pb-2">3. Security Credentials</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="password" class="form-label fw-semibold text-secondary small mb-1">Password (Min. 6 chars) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control bg-light border-start-0 <?php echo form_error('password') ? 'is-invalid' : ''; ?>" placeholder="••••••••" required>
                </div>
                <?php echo form_error('password', '<div class="text-danger small mt-1">', '</div>'); ?>
            </div>

            <div class="col-md-6">
                <label for="confirm_password" class="form-label fw-semibold text-secondary small mb-1">Confirm Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control bg-light border-start-0 <?php echo form_error('confirm_password') ? 'is-invalid' : ''; ?>" placeholder="••••••••" required>
                </div>
                <?php echo form_error('confirm_password', '<div class="text-danger small mt-1">', '</div>'); ?>
            </div>
        </div>

        <button type="submit" class="btn btn-teal w-100 mb-3 shadow-sm py-2.5">
            <i class="bi bi-person-check me-1.5"></i> Complete Doctor Registration
        </button>
        
        <div class="text-center pt-2 border-top">
            <span class="text-muted small">Already have a practitioner account? <a href="<?php echo base_url('doctor'); ?>" class="fw-semibold text-decoration-none" style="color: #0d9488;">Sign In</a></span>
        </div>
    <?php echo form_close(); ?>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
