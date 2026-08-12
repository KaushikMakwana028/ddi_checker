<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="auth-card">
    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle mb-3" style="background-color: #ccfbf1;">
            <i class="bi bi-person-plus fs-1" style="color: #0d9488;"></i>
        </div>
        <h3 class="fw-bold text-dark mb-1">Create Account</h3>
        <p class="text-muted small">Register to check Drug-Drug Interactions</p>
    </div>

    <?php echo form_open('auth/register', ['class' => 'needs-validation']); ?>
        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Full Name</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                <input type="text" name="name" id="name" class="form-control bg-light border-start-0 <?php echo form_error('name') ? 'is-invalid' : ''; ?>" placeholder="Dr. Jane Doe" value="<?php echo set_value('name'); ?>" required>
            </div>
            <?php echo form_error('name', '<div class="text-danger small mt-1">', '</div>'); ?>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" id="email" class="form-control bg-light border-start-0 <?php echo form_error('email') ? 'is-invalid' : ''; ?>" placeholder="name@clinic.com" value="<?php echo set_value('email'); ?>" required>
            </div>
            <?php echo form_error('email', '<div class="text-danger small mt-1">', '</div>'); ?>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" id="password" class="form-control bg-light border-start-0 <?php echo form_error('password') ? 'is-invalid' : ''; ?>" placeholder="At least 6 characters" required>
            </div>
            <?php echo form_error('password', '<div class="text-danger small mt-1">', '</div>'); ?>
        </div>

        <div class="mb-4">
            <label for="confirm_password" class="form-label fw-semibold">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock-fill"></i></span>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control bg-light border-start-0 <?php echo form_error('confirm_password') ? 'is-invalid' : ''; ?>" placeholder="Repeat your password" required>
            </div>
            <?php echo form_error('confirm_password', '<div class="text-danger small mt-1">', '</div>'); ?>
        </div>

        <button type="submit" class="btn btn-teal w-100 py-2 fs-6 rounded-3 mb-3">
            <i class="bi bi-person-check me-1"></i> Sign Up
        </button>
        
        <div class="text-center">
            <span class="text-muted small">Already have an account? <a href="<?php echo base_url('auth/login'); ?>" class="fw-semibold text-decoration-none" style="color: #0d9488;">Sign In</a></span>
        </div>
    <?php echo form_close(); ?>
</div>
