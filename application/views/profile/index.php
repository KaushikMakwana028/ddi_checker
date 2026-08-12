<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Alert Notifications -->
<div class="row">
    <div class="col-12">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 py-3 d-flex align-items-center" role="alert" style="background-color: #f0fdf4; border-left: 5px solid #16a34a !important; color: #15803d;">
                <i class="bi bi-check-circle-fill me-3 fs-4 text-success"></i>
                <div>
                    <strong>Success!</strong> <?php echo $this->session->flashdata('success'); ?>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 py-3 d-flex align-items-center" role="alert" style="background-color: #fef2f2; border-left: 5px solid #dc2626 !important; color: #b91c1c;">
                <i class="bi bi-exclamation-octagon-fill me-3 fs-4 text-danger"></i>
                <div>
                    <strong>Error!</strong> <?php echo $this->session->flashdata('error'); ?>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div> 
        <?php endif; ?>
    </div>
</div>

<!-- Header / Navigation info -->
<div class="row mb-4">
    <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>" class="text-teal text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0">Account Settings</h2>
            <p class="text-muted mb-0">Manage your profile details, avatar picture, and security credentials.</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: User Summary & Avatar Selection -->
    <div class="col-lg-4">
        <div class="card border-0 rounded-4 shadow-sm h-100 overflow-hidden text-center">
            <!-- Sleek Top Header Gradient -->
            <div style="height: 120px; background: linear-gradient(135deg, #0d9488 0%, #115e59 100%);"></div>
            
            <div class="card-body p-4 position-relative" style="margin-top: -60px;">
                <!-- Profile Image & Camera Overlay -->
                <div class="mx-auto position-relative mb-3 shadow" style="width: 120px; height: 120px; border-radius: 50%;">
                    <div class="avatar-preview-wrapper w-100 h-100 rounded-circle overflow-hidden border border-4 border-white bg-light position-relative d-flex align-items-center justify-content-center">
                        <?php if (!empty($user->profile_image) && file_exists(FCPATH . $user->profile_image)): ?>
                            <img id="avatarImage" src="<?php echo base_url($user->profile_image); ?>" alt="Profile Picture" class="w-100 h-100 object-fit-cover">
                        <?php else: ?>
                            <div id="avatarInitials" class="w-100 h-100 d-flex align-items-center justify-content-center text-teal fw-bold fs-1" style="color: #0f766e; background-color: #ccfbf1;">
                                <?php 
                                $name = $user->name;
                                $initials = '';
                                if (!empty($name)) {
                                    $words = explode(' ', $name);
                                    foreach ($words as $w) {
                                        $initials .= substr($w, 0, 1);
                                    }
                                    $initials = strtoupper(substr($initials, 0, 2));
                                } else {
                                    $initials = 'U';
                                }
                                echo $initials;
                                ?>
                            </div>
                            <img id="avatarImage" src="" alt="Profile Picture" class="w-100 h-100 object-fit-cover" style="display: none;">
                        <?php endif; ?>
                    </div>
                    
                    <!-- Camera Icon Overlay (Linked to hidden file input) -->
                    <label for="profileImageInput" class="camera-overlay shadow" title="Choose profile image">
                        <i class="bi bi-camera-fill fs-6 text-white"></i>
                    </label>
                    <!-- Hidden File Input targeting the main form -->
                    <input type="file" id="profileImageInput" name="profile_image" form="profileUpdateForm" style="display: none;" accept="image/*">
                </div>

                <h4 class="fw-bold text-dark mb-1"><?php echo html_escape($user->name); ?></h4>
                <p class="text-secondary small mb-3"><i class="bi bi-envelope me-1"></i><?php echo html_escape($user->email); ?></p>
                <span class="badge px-3 py-2 rounded-pill fw-semibold mb-4" style="background-color: #e2e8f0; color: #475569;">
                    <i class="bi bi-shield-check me-1"></i>Clinical Support Staff
                </span>

                <hr class="my-4 text-muted">
                
                <div class="alert alert-info border-0 rounded-3 py-2 px-3 text-start small mb-0" style="background-color: #f0f9ff; color: #0369a1;">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Click the camera icon on the photo to select a new image. Click <strong>Save Profile Details</strong> to apply all updates.
                </div>
            </div>
            
            <div class="card-footer bg-light border-0 py-3 text-start mt-auto">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-secondary small">Status</span>
                    <span class="badge bg-success-subtle text-success py-1 px-2.5 rounded-pill small">Active</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-secondary small">Member Since</span>
                    <span class="text-dark small fw-semibold"><?php echo date('M d, Y', strtotime($user->created_at)); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Settings Tabs -->
    <div class="col-lg-8">
        <div class="card border-0 rounded-4 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <ul class="nav nav-pills card-header-pills bg-light p-1.5 rounded-3" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation" style="flex: 1;">
                        <button class="nav-link active w-100 rounded-3 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-controls="details" aria-selected="true">
                            <i class="bi bi-person-lines-fill"></i> <span class="d-none d-sm-inline">Profile Details</span><span class="d-inline d-sm-none">Details</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation" style="flex: 1;">
                        <button class="nav-link w-100 rounded-3 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab" aria-controls="security" aria-selected="false">
                            <i class="bi bi-shield-lock"></i> <span class="d-none d-sm-inline">Security & Password</span><span class="d-inline d-sm-none">Security</span>
                        </button>
                    </li>
                </ul>
            </div>
            
            <div class="card-body p-4">
                <div class="tab-content" id="profileTabsContent">
                    
                    <!-- Tab 1: Profile Details Form -->
                    <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                        <h4 class="fw-bold text-dark mb-4"><i class="bi bi-person text-teal me-2"></i>Update Personal Details</h4>
                        
                        <?php echo form_open_multipart('profile/update', ['id' => 'profileUpdateForm']); ?>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold text-secondary small">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-secondary-subtle"><i class="bi bi-person text-muted"></i></span>
                                        <input type="text" class="form-control rounded-end border-secondary-subtle py-2.5" id="name" name="name" value="<?php echo html_escape($user->name); ?>" required placeholder="Enter full name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold text-secondary small">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-secondary-subtle"><i class="bi bi-envelope text-muted"></i></span>
                                        <input type="email" class="form-control rounded-end border-secondary-subtle py-2.5" id="email" name="email" value="<?php echo html_escape($user->email); ?>" required placeholder="Enter email address">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="mobile" class="form-label fw-semibold text-secondary small">Mobile Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-secondary-subtle"><i class="bi bi-telephone text-muted"></i></span>
                                        <input type="text" class="form-control rounded-end border-secondary-subtle py-2.5" id="mobile" name="mobile" value="<?php echo html_escape($user->mobile); ?>" placeholder="Enter phone number (e.g. +1234567890)">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="address" class="form-label fw-semibold text-secondary small">Physical / Clinical Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-secondary-subtle"><i class="bi bi-geo-alt text-muted"></i></span>
                                        <textarea class="form-control rounded-end border-secondary-subtle py-2" id="address" name="address" rows="3" placeholder="Enter clinical office, hospital address or personal address"><?php echo html_escape($user->address); ?></textarea>
                                    </div>
                                </div>
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-teal px-4 py-2.5 rounded-3">
                                        <i class="bi bi-check-circle me-1"></i> Save Profile Details
                                    </button>
                                </div>
                            </div>
                        <?php echo form_close(); ?>
                    </div>
                    
                    <!-- Tab 2: Security & Password Form -->
                    <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                        <h4 class="fw-bold text-dark mb-4"><i class="bi bi-key text-teal me-2"></i>Change Account Password</h4>
                        
                        <?php echo form_open('profile/change_password'); ?>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="current_password" class="form-label fw-semibold text-secondary small">Current Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-secondary-subtle"><i class="bi bi-lock text-muted"></i></span>
                                        <input type="password" class="form-control rounded-end border-secondary-subtle py-2.5" id="current_password" name="current_password" required placeholder="Enter current password">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="new_password" class="form-label fw-semibold text-secondary small">New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-secondary-subtle"><i class="bi bi-shield-lock text-muted"></i></span>
                                        <input type="password" class="form-control rounded-end border-secondary-subtle py-2.5" id="new_password" name="new_password" required placeholder="At least 6 characters">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="confirm_password" class="form-label fw-semibold text-secondary small">Confirm New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-secondary-subtle"><i class="bi bi-shield-check text-muted"></i></span>
                                        <input type="password" class="form-control rounded-end border-secondary-subtle py-2.5" id="confirm_password" name="confirm_password" required placeholder="Re-type new password">
                                    </div>
                                </div>
                                
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-teal px-4 py-2.5 rounded-3">
                                        <i class="bi bi-arrow-repeat me-1"></i> Update Password
                                    </button>
                                </div>
                            </div>
                        <?php echo form_close(); ?>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Page Custom Styling Details */
.text-teal {
    color: #0d9488 !important;
}
.btn-teal {
    background-color: #0d9488;
    border-color: #0d9488;
    color: #ffffff;
    transition: all 0.2s ease;
}
.btn-teal:hover, .btn-teal:focus {
    background-color: #0f766e;
    border-color: #0f766e;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(13, 148, 136, 0.2);
}
.nav-pills .nav-link {
    color: #64748b;
    transition: all 0.2s ease;
}
.nav-pills .nav-link.active, .nav-pills .show > .nav-link {
    background-color: #0d9488;
    color: #ffffff;
}
.form-control:focus {
    border-color: #0d9488;
    box-shadow: 0 0 0 0.25rem rgba(13, 148, 136, 0.15);
}
.input-group-text {
    border-color: #cbd5e1;
}

/* Camera overlay styles */
.camera-overlay {
    position: absolute;
    bottom: 2px;
    right: 2px;
    background-color: #0d9488;
    border: 3px solid #ffffff;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}
.camera-overlay:hover {
    background-color: #0f766e;
    transform: scale(1.15);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('profileImageInput');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const avatarImg = document.getElementById('avatarImage');
                    const initialsDiv = document.getElementById('avatarInitials');
                    
                    if (avatarImg) {
                        avatarImg.src = event.target.result;
                        avatarImg.style.display = 'block';
                    }
                    if (initialsDiv) {
                        initialsDiv.style.display = 'none';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Switch to security tab if hash is #security
    if (window.location.hash === '#security') {
        const securityTab = document.getElementById('security-tab');
        if (securityTab) {
            const tab = new bootstrap.Tab(securityTab);
            tab.show();
        }
    }

    // Add listener for dropdown Change Password link click to switch tab
    const changePasswordLink = document.getElementById('changePasswordMenuLink');
    if (changePasswordLink) {
        changePasswordLink.addEventListener('click', function() {
            const securityTab = document.getElementById('security-tab');
            if (securityTab) {
                const tab = new bootstrap.Tab(securityTab);
                tab.show();
            }
        });
    }
});
</script>
