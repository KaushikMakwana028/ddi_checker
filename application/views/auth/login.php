<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="auth-card">
    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle mb-3" style="background-color: #ccfbf1;">
            <i class="bi bi-shield-plus fs-1" style="color: #0d9488;"></i>
        </div>
        <h3 class="fw-bold text-dark mb-1">DDI Checker</h3>
        <p class="text-muted small">Clinical Drug Interaction Portal</p>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php echo html_escape($this->session->flashdata('success')); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo html_escape($this->session->flashdata('error')); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php echo form_open('auth/login', ['class' => 'needs-validation']); ?>
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" id="email" class="form-control bg-light border-start-0 <?php echo form_error('email') ? 'is-invalid' : ''; ?>" placeholder="name@clinic.com" value="<?php echo set_value('email'); ?>" required>
            </div>
            <?php echo form_error('email', '<div class="text-danger small mt-1">', '</div>'); ?>
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label fw-semibold mb-0">Password</label>
            </div>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" id="password" class="form-control bg-light border-start-0 <?php echo form_error('password') ? 'is-invalid' : ''; ?>" placeholder="••••••••" required>
            </div>
            <?php echo form_error('password', '<div class="text-danger small mt-1">', '</div>'); ?>
        </div>

        <div class="mb-4 d-flex justify-content-between align-items-center">
            <div class="form-check">
                <input type="checkbox" name="remember" id="remember" class="form-check-input" value="1">
                <label for="remember" class="form-check-label text-secondary small">Remember me</label>
            </div>
        </div>

        <button type="submit" class="btn btn-teal w-100 py-2 fs-6 rounded-3 mb-3">
            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
        </button>
        
        <div class="text-center">
            <span class="text-muted small">New to the platform? <a href="<?php echo base_url('auth/register'); ?>" class="fw-semibold text-decoration-none" style="color: #0d9488;">Create an account</a></span>
        </div>
    <?php echo form_close(); ?>
</div>
