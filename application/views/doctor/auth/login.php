<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? html_escape($title) : 'Doctor Login'; ?> - DDI Checker</title>
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
            padding: 20px;
            color: #0f172a;
        }

        .auth-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            background: #ffffff;
            width: 100%;
            max-width: 440px;
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
            padding: 0.65rem 0.9rem;
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
            <i class="bi bi-heart-pulse-fill fs-2" style="color: #0d9488;"></i>
        </div>
        <div>
            <span class="doctor-tag mb-2">CLINICAL DECISION PORTAL</span>
        </div>
        <h3 class="fw-bold text-dark mb-1">Doctor Access</h3>
        <p class="text-muted small">Sign in to check prescriptions & drug interactions</p>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 py-2.5 small" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php echo html_escape($this->session->flashdata('success')); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 py-2.5 small" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo html_escape($this->session->flashdata('error')); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php echo form_open('doctor/login', ['class' => 'needs-validation']); ?>
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold text-secondary small mb-1">Registered Email</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" id="email" class="form-control bg-light border-start-0 <?php echo form_error('email') ? 'is-invalid' : ''; ?>" placeholder="doctor@clinic.com" value="<?php echo set_value('email'); ?>" required autocomplete="email">
            </div>
            <?php echo form_error('email', '<div class="text-danger small mt-1">', '</div>'); ?>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold text-secondary small mb-1">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-key"></i></span>
                <input type="password" name="password" id="password" class="form-control bg-light border-start-0 <?php echo form_error('password') ? 'is-invalid' : ''; ?>" placeholder="••••••••" required autocomplete="current-password">
            </div>
            <?php echo form_error('password', '<div class="text-danger small mt-1">', '</div>'); ?>
        </div>

        <div class="mb-4 d-flex justify-content-between align-items-center">
            <div class="form-check">
                <input type="checkbox" name="remember" id="remember" class="form-check-input" value="1">
                <label for="remember" class="form-check-label text-secondary small">Remember me</label>
            </div>
        </div>

        <button type="submit" class="btn btn-teal w-100 mb-3 shadow-sm">
            <i class="bi bi-box-arrow-in-right me-1.5"></i> Sign In
        </button>
        
        <div class="text-center pt-2 border-top">
            <span class="text-muted small">New practitioner? <a href="<?php echo base_url('doctor/register'); ?>" class="fw-semibold text-decoration-none" style="color: #0d9488;">Register as Doctor</a></span>
        </div>
    <?php echo form_close(); ?>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
