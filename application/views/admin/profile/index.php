<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="admin-profile-page">
    <!-- Header / Navigation Info -->
    <div class="page-header mb-4">
        <div>
            <div class="page-eyebrow">
                <i class="bi bi-person-gear"></i> Account Settings
            </div>
            <h2 class="page-title">Administrator Profile</h2>
            <p class="page-subtitle">Manage administrative credentials, avatar picture, and security preferences.</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: User Summary & Avatar Selection -->
        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm h-100 overflow-hidden text-center profile-summary-card">
                <!-- Top Header Gradient -->
                <div class="profile-card-cover"></div>
                
                <div class="card-body p-4 position-relative" style="margin-top: -55px;">
                    <!-- Profile Image & Camera Overlay -->
                    <div class="mx-auto position-relative mb-3 shadow avatar-container" style="width: 110px; height: 110px; border-radius: 50%;">
                        <div class="avatar-preview-wrapper w-100 h-100 rounded-circle overflow-hidden border border-4 border-white bg-light position-relative d-flex align-items-center justify-content-center">
                            <?php if (!empty($user->profile_image) && file_exists(FCPATH . $user->profile_image)): ?>
                                <img id="avatarImage" src="<?php echo base_url($user->profile_image); ?>" alt="Profile Picture" class="w-100 h-100 object-fit-cover">
                            <?php else: ?>
                                <div id="avatarInitials" class="w-100 h-100 d-flex align-items-center justify-content-center text-teal fw-bold fs-2" style="color: #0f766e; background-color: #f0fdfa;">
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
                                        $initials = 'AD';
                                    }
                                    echo $initials;
                                    ?>
                                </div>
                                <img id="avatarImage" src="" alt="Profile Picture" class="w-100 h-100 object-fit-cover" style="display: none;">
                            <?php endif; ?>
                        </div>
                        
                        <!-- Camera Icon Overlay -->
                        <label for="profileImageInput" class="camera-overlay shadow" title="Choose profile photo">
                            <i class="bi bi-camera-fill text-white"></i>
                        </label>
                        <input type="file" id="profileImageInput" name="profile_image" form="adminProfileForm" style="display: none;" accept="image/*">
                    </div>

                    <h4 class="fw-bold text-dark mb-1"><?php echo html_escape($user->name); ?></h4>
                    <p class="text-secondary small mb-3"><i class="bi bi-envelope me-1"></i><?php echo html_escape($user->email); ?></p>
                    <span class="badge px-3 py-2 rounded-pill fw-semibold mb-4" style="background-color: #f0fdfa; border: 1px solid #99f6e4; color: #0f766e;">
                        <i class="bi bi-shield-lock-fill me-1"></i>Super Administrator
                    </span>

                    <hr class="my-4 text-muted opacity-25">
                    
                    <div class="alert alert-info border-0 rounded-3 py-2.5 px-3 text-start small mb-0" style="background-color: #f0fdfa; border: 1px solid #99f6e4; color: #0f766e;">
                        <i class="bi bi-info-circle-fill me-1.5"></i>
                        Click the camera icon on your photo to upload a new picture. Click <strong>Save Profile Changes</strong> to apply updates.
                    </div>
                </div>
                
                <div class="card-footer bg-light border-0 py-3 text-start mt-auto">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-secondary small">Access Level</span>
                        <span class="badge bg-teal-subtle text-teal py-1 px-2.5 rounded-pill small" style="background-color: #ccfbf1; color: #0f766e;">Full System Access</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary small">Account Created</span>
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
                                <i class="bi bi-person-lines-fill"></i> <span>Profile Details</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation" style="flex: 1;">
                            <button class="nav-link w-100 rounded-3 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab" aria-controls="security" aria-selected="false">
                                <i class="bi bi-shield-lock"></i> <span>Security &amp; Password</span>
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body p-4">
                    <div class="tab-content" id="profileTabsContent">
                        
                        <!-- Tab 1: Profile Details Form -->
                        <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                            <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                                <i class="bi bi-person-gear text-teal"></i>
                                <span>Update Administrator Info</span>
                            </h5>
                            
                            <?php echo form_open_multipart('admin/profile/update', ['id' => 'adminProfileForm', 'autocomplete' => 'off']); ?>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-field">
                                            <label for="name">Full Name <span class="req">*</span></label>
                                            <div class="input-icon">
                                                <i class="bi bi-person"></i>
                                                <input type="text" id="name" name="name" value="<?php echo html_escape($user->name); ?>" required placeholder="Enter full name">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-field">
                                            <label for="email">Email Address <span class="req">*</span></label>
                                            <div class="input-icon">
                                                <i class="bi bi-envelope"></i>
                                                <input type="email" id="email" name="email" value="<?php echo html_escape($user->email); ?>" required placeholder="admin@domain.com">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-field">
                                            <label for="mobile">Mobile Phone Number</label>
                                            <div class="input-icon">
                                                <i class="bi bi-telephone"></i>
                                                <input type="text" id="mobile" name="mobile" value="<?php echo html_escape($user->mobile); ?>" placeholder="+1 (555) 019-2834">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-field">
                                            <label for="address">Administrative Office / Address</label>
                                            <div class="input-icon">
                                                <i class="bi bi-geo-alt"></i>
                                                <textarea id="address" name="address" rows="3" placeholder="Enter administrative office or physical address"><?php echo html_escape($user->address); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-4 text-end">
                                        <button type="submit" class="btn-primary">
                                            <i class="bi bi-check-circle"></i> <span>Save Profile Changes</span>
                                        </button>
                                    </div>
                                </div>
                            <?php echo form_close(); ?>
                        </div>
                        
                        <!-- Tab 2: Security & Password Form -->
                        <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                            <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                                <i class="bi bi-key text-teal"></i>
                                <span>Change Password</span>
                            </h5>
                            
                            <?php echo form_open('admin/profile/change_password', ['autocomplete' => 'off']); ?>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-field">
                                            <label for="current_password">Current Password <span class="req">*</span></label>
                                            <div class="input-icon">
                                                <i class="bi bi-lock"></i>
                                                <input type="password" id="current_password" name="current_password" required placeholder="Enter current password">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-field">
                                            <label for="new_password">New Password (Min. 6 chars) <span class="req">*</span></label>
                                            <div class="input-icon">
                                                <i class="bi bi-shield-lock"></i>
                                                <input type="password" id="new_password" name="new_password" required placeholder="••••••••">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-field">
                                            <label for="confirm_password">Confirm New Password <span class="req">*</span></label>
                                            <div class="input-icon">
                                                <i class="bi bi-shield-check"></i>
                                                <input type="password" id="confirm_password" name="confirm_password" required placeholder="••••••••">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 mt-4 text-end">
                                        <button type="submit" class="btn-primary">
                                            <i class="bi bi-arrow-repeat"></i> <span>Update Password</span>
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
</div>

<style>
    .admin-profile-page,
    .admin-profile-page * {
        font-family: 'Poppins', sans-serif;
        box-sizing: border-box;
    }

    .admin-profile-page {
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

    .page-header {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-end;
        gap: 16px;
    }

    .page-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: var(--teal);
        background: var(--teal-bg);
        border: 1px solid var(--teal-border);
        padding: 4px 12px;
        border-radius: 999px;
        margin-bottom: 8px;
    }

    .page-title {
        font-size: 26px;
        font-weight: 700;
        margin: 0 0 4px;
        color: var(--text);
        line-height: 1.25;
    }

    .page-subtitle {
        font-size: 14px;
        color: var(--text-soft);
        margin: 0;
    }

    .profile-card-cover {
        height: 110px;
        background: linear-gradient(135deg, #0f766e 0%, #0f172a 100%);
    }

    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 12px;
        padding: 10px 20px;
        background: var(--teal);
        color: #fff;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all .15s ease;
        box-shadow: 0 4px 12px rgba(15, 118, 110, .25);
    }

    .btn-primary:hover {
        background: #0c5f59;
        color: #fff;
        transform: translateY(-1px);
    }

    .nav-pills .nav-link {
        color: #64748b;
        font-weight: 600;
        font-size: 13.5px;
        transition: all 0.2s ease;
    }
    .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
        background-color: var(--teal);
        color: #ffffff;
    }

    .camera-overlay {
        position: absolute;
        bottom: 2px;
        right: 2px;
        background-color: var(--teal);
        border: 3px solid #ffffff;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        font-size: 14px;
    }
    .camera-overlay:hover {
        background-color: #0c5f59;
        transform: scale(1.1);
    }

    .form-field {
        position: relative;
        margin-bottom: 0;
        display: flex;
        flex-direction: column;
    }

    .form-field label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 7px;
    }

    .req {
        color: #dc2626;
    }

    .form-field input[type="text"],
    .form-field input[type="email"],
    .form-field input[type="password"],
    .form-field textarea {
        width: 100%;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 11px;
        padding: 11px 14px;
        font-size: 14px;
        color: var(--text);
        outline: none;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .form-field input[type="text"]:focus,
    .form-field input[type="email"]:focus,
    .form-field input[type="password"]:focus,
    .form-field textarea:focus {
        border-color: var(--teal);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(15, 118, 110, .12);
    }

    .input-icon {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .input-icon>i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-faint);
        font-size: 15px;
        pointer-events: none;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-field textarea + .input-icon > i,
    .input-icon > textarea ~ i {
        top: 20px;
    }

    .input-icon input,
    .form-field .input-icon input[type="text"],
    .form-field .input-icon input[type="email"],
    .form-field .input-icon input[type="password"],
    .form-field .input-icon textarea,
    .form-field .input-icon input {
        padding-left: 42px !important;
        width: 100%;
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
});
</script>
