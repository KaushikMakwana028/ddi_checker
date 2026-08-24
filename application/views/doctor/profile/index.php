<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
// Pre-compute display helpers (kept in PHP so both view-mode and edit-mode reuse the same values)
$dp_name           = !empty($user->name) ? $user->name : '';
$dp_email          = !empty($user->email) ? $user->email : '';
$dp_mobile         = !empty($user->mobile) ? $user->mobile : '';
$dp_address        = !empty($user->address) ? $user->address : '';
$dp_reg_no         = !empty($profile->registration_number) ? $profile->registration_number : '';
$dp_council        = !empty($profile->medical_council) ? $profile->medical_council : '';
$dp_specialization = !empty($profile->specialization) ? $profile->specialization : '';
$dp_qualification  = !empty($profile->qualification) ? $profile->qualification : '';
$dp_experience     = (!empty($profile->years_experience) || $profile->years_experience === '0') ? $profile->years_experience : '';
$dp_hospital       = !empty($profile->hospital_clinic) ? $profile->hospital_clinic : '';
$dp_member_since   = date('M Y', strtotime($user->created_at));

$dp_initials = 'DR';
if (!empty($dp_name)) {
    $dp_initials = '';
    foreach (explode(' ', $dp_name) as $w) {
        $dp_initials .= substr($w, 0, 1);
    }
    $dp_initials = strtoupper(substr($dp_initials, 0, 2));
}

$dp_has_avatar    = !empty($user->profile_image) && file_exists(FCPATH . $user->profile_image);
$dp_has_signature = !empty($profile->signature) && file_exists(FCPATH . $profile->signature);
?>

<div class="dp-page">

    <!-- Page heading + mode switch -->
    <div class="dp-topbar">
        <div>
            <div class="dp-eyebrow"><i class="bi bi-heart-pulse-fill"></i> Clinical Portal</div>
            <h1 class="dp-title">Your Profile</h1>
        </div>
        <div class="dp-topbar-actions">
            <button type="button" class="dp-btn dp-btn-ghost" id="dpCancelEdit"><i class="bi bi-x-lg"></i> Cancel</button>
            <button type="button" class="dp-btn dp-btn-primary" id="dpEditToggle">
                <i class="bi bi-pencil-fill"></i> <span>Edit Profile</span>
            </button>
        </div>
    </div>

    <!-- ============ ID-CARD HERO ============ -->
    <div class="dp-hero">
        <div class="dp-hero-pattern" aria-hidden="true"></div>

        <div class="dp-hero-top">
            <div class="dp-avatar-wrap">
                <div class="dp-avatar">
                    <?php if ($dp_has_avatar): ?>
                        <img id="avatarImage" src="<?php echo base_url($user->profile_image); ?>" alt="Profile photo">
                    <?php else: ?>
                        <span id="avatarInitials"><?php echo $dp_initials; ?></span>
                        <img id="avatarImage" src="" alt="Profile photo" style="display:none;">
                    <?php endif; ?>
                </div>
                <label for="profileImageInput" class="dp-avatar-cam" title="Change photo">
                    <i class="bi bi-camera-fill"></i>
                </label>
                <input type="file" id="profileImageInput" name="profile_image" form="doctorProfileForm" accept="image/*" onchange="dpPreviewAvatar(event)" hidden>
            </div>

            <div class="dp-hero-identity">
                <div class="dp-hero-name-row">
                    <h2 class="dp-hero-name">Dr. <?php echo html_escape($dp_name); ?></h2>
                    <span class="dp-verified"><i class="bi bi-patch-check-fill"></i> Verified</span>
                </div>
                <p class="dp-hero-sub">
                    <?php echo !empty($dp_specialization) ? html_escape($dp_specialization) : 'Specialization not set'; ?>
                    <?php if (!empty($dp_qualification)): ?>
                        <span class="dp-hero-dot">&bull;</span> <?php echo html_escape($dp_qualification); ?>
                    <?php endif; ?>
                </p>
                <p class="dp-hero-contact">
                    <i class="bi bi-envelope"></i> <?php echo html_escape($dp_email); ?>
                    <span class="dp-hero-dot">&bull;</span>
                    <i class="bi bi-telephone"></i> <?php echo html_escape($dp_mobile); ?>
                </p>
            </div>
        </div>

        <!-- Credential strip = the signature element: reads like a printed ID badge -->
        <div class="dp-cred-strip">
            <div class="dp-cred">
                <span class="dp-cred-label">Registration No.</span>
                <span class="dp-cred-value dp-mono"><?php echo !empty($dp_reg_no) ? html_escape($dp_reg_no) : '&mdash;'; ?></span>
            </div>
            <div class="dp-cred-sep" aria-hidden="true"></div>
            <div class="dp-cred">
                <span class="dp-cred-label">Medical Council</span>
                <span class="dp-cred-value"><?php echo !empty($dp_council) ? html_escape($dp_council) : '&mdash;'; ?></span>
            </div>
            <div class="dp-cred-sep" aria-hidden="true"></div>
            <div class="dp-cred">
                <span class="dp-cred-label">Experience</span>
                <span class="dp-cred-value"><?php echo $dp_experience !== '' ? html_escape($dp_experience) . ' yrs' : '&mdash;'; ?></span>
            </div>
            <div class="dp-cred-sep" aria-hidden="true"></div>
            <div class="dp-cred">
                <span class="dp-cred-label">Member Since</span>
                <span class="dp-cred-value dp-mono"><?php echo html_escape($dp_member_since); ?></span>
            </div>
        </div>
    </div>

    <!-- ============ BODY: view / edit toggle ============ -->
    <div class="dp-body" id="dpBody">

        <?php echo form_open_multipart('doctor/profile/update', ['id' => 'doctorProfileForm', 'autocomplete' => 'off']); ?>

        <div class="dp-grid">

            <!-- Basic Information -->
            <div class="dp-card">
                <div class="dp-card-head">
                    <span class="dp-card-icon"><i class="bi bi-person-vcard"></i></span>
                    <div>
                        <h3>Basic Information</h3>
                        <p>Your name and contact details</p>
                    </div>
                </div>

                <div class="dp-view">
                    <div class="dp-row"><span class="dp-row-label">Full name</span><span class="dp-row-value">Dr. <?php echo html_escape($dp_name); ?></span></div>
                    <div class="dp-row"><span class="dp-row-label">Email</span><span class="dp-row-value"><?php echo html_escape($dp_email); ?></span></div>
                    <div class="dp-row"><span class="dp-row-label">Mobile</span><span class="dp-row-value"><?php echo html_escape($dp_mobile); ?></span></div>
                </div>

                <div class="dp-edit">
                    <div class="dp-field">
                        <label for="name">Full Name <em>*</em></label>
                        <input type="text" id="name" name="name" value="<?php echo html_escape($dp_name); ?>" required placeholder="Dr. Full Name">
                    </div>
                    <div class="dp-field">
                        <label for="email">Email Address <em>*</em></label>
                        <input type="email" id="email" name="email" value="<?php echo html_escape($dp_email); ?>" required placeholder="doctor@hospital.com">
                    </div>
                    <div class="dp-field">
                        <label for="mobile">Mobile Number <em>*</em></label>
                        <input type="text" id="mobile" name="mobile" value="<?php echo html_escape($dp_mobile); ?>" required placeholder="Mobile phone">
                    </div>
                </div>
            </div>

            <!-- Medical Credentials -->
            <div class="dp-card">
                <div class="dp-card-head">
                    <span class="dp-card-icon"><i class="bi bi-patch-check"></i></span>
                    <div>
                        <h3>Medical Credentials</h3>
                        <p>Registration and qualification details</p>
                    </div>
                </div>

                <div class="dp-view">
                    <div class="dp-row"><span class="dp-row-label">Registration no.</span><span class="dp-row-value dp-mono"><?php echo !empty($dp_reg_no) ? html_escape($dp_reg_no) : '&mdash;'; ?></span></div>
                    <div class="dp-row"><span class="dp-row-label">Medical council</span><span class="dp-row-value"><?php echo !empty($dp_council) ? html_escape($dp_council) : '&mdash;'; ?></span></div>
                    <div class="dp-row"><span class="dp-row-label">Specialization</span><span class="dp-row-value"><?php echo !empty($dp_specialization) ? html_escape($dp_specialization) : '&mdash;'; ?></span></div>
                    <div class="dp-row"><span class="dp-row-label">Qualification</span><span class="dp-row-value"><?php echo !empty($dp_qualification) ? html_escape($dp_qualification) : '&mdash;'; ?></span></div>
                    <div class="dp-row"><span class="dp-row-label">Experience</span><span class="dp-row-value"><?php echo $dp_experience !== '' ? html_escape($dp_experience) . ' years' : '&mdash;'; ?></span></div>
                </div>

                <div class="dp-edit">
                    <div class="dp-field">
                        <label for="registration_number">Medical Registration Number <em>*</em></label>
                        <input type="text" id="registration_number" name="registration_number" value="<?php echo html_escape($dp_reg_no); ?>" required placeholder="Reg No.">
                    </div>
                    <div class="dp-field">
                        <label for="medical_council">Medical Council</label>
                        <input type="text" id="medical_council" name="medical_council" value="<?php echo html_escape($dp_council); ?>" placeholder="State Medical Council">
                    </div>
                    <div class="dp-field">
                        <label for="specialization">Specialization</label>
                        <input type="text" id="specialization" name="specialization" value="<?php echo html_escape($dp_specialization); ?>" placeholder="Cardiologist, General Practice...">
                    </div>
                    <div class="dp-field">
                        <label for="qualification">Qualification</label>
                        <input type="text" id="qualification" name="qualification" value="<?php echo html_escape($dp_qualification); ?>" placeholder="MBBS, MD, etc.">
                    </div>
                    <div class="dp-field dp-field-narrow">
                        <label for="years_experience">Years of Experience</label>
                        <input type="number" min="0" max="80" id="years_experience" name="years_experience" value="<?php echo html_escape($dp_experience); ?>" placeholder="0">
                    </div>
                </div>
            </div>

            <!-- Practice Details -->
            <div class="dp-card">
                <div class="dp-card-head">
                    <span class="dp-card-icon"><i class="bi bi-hospital"></i></span>
                    <div>
                        <h3>Practice Details</h3>
                        <p>Where patients can find you</p>
                    </div>
                </div>

                <div class="dp-view">
                    <div class="dp-row"><span class="dp-row-label">Hospital / clinic</span><span class="dp-row-value"><?php echo !empty($dp_hospital) ? html_escape($dp_hospital) : '&mdash;'; ?></span></div>
                    <div class="dp-row dp-row-block"><span class="dp-row-label">Office address</span><span class="dp-row-value"><?php echo !empty($dp_address) ? nl2br(html_escape($dp_address)) : '&mdash;'; ?></span></div>
                </div>

                <div class="dp-edit">
                    <div class="dp-field">
                        <label for="hospital_clinic">Hospital / Clinic</label>
                        <input type="text" id="hospital_clinic" name="hospital_clinic" value="<?php echo html_escape($dp_hospital); ?>" placeholder="Current affiliated hospital or clinic">
                    </div>
                    <div class="dp-field">
                        <label for="address">Professional Office Address</label>
                        <textarea id="address" name="address" rows="3" placeholder="Enter clinical office location or professional address"><?php echo html_escape($dp_address); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Digital Signature -->
            <div class="dp-card">
                <div class="dp-card-head">
                    <span class="dp-card-icon"><i class="bi bi-pen"></i></span>
                    <div>
                        <h3>Digital Signature</h3>
                        <p>Appears on printed prescriptions</p>
                    </div>
                </div>

                <div class="dp-view">
                    <?php if ($dp_has_signature): ?>
                        <div class="dp-sig-preview">
                            <img src="<?php echo base_url($profile->signature); ?>" alt="Doctor signature">
                        </div>
                    <?php else: ?>
                        <div class="dp-row"><span class="dp-row-label">Signature</span><span class="dp-row-value">Not uploaded yet</span></div>
                    <?php endif; ?>
                </div>

                <div class="dp-edit">
                    <label for="signature" class="dp-sig-upload">
                        <input type="file" id="signature" name="signature" accept="image/*" onchange="dpPreviewSignature(event)" hidden>
                        <i class="bi bi-cloud-arrow-up"></i>
                        <span class="dp-sig-upload-title" id="signatureFileLabel">Choose signature image</span>
                        <span class="dp-sig-upload-hint">PNG or JPG, transparent or white background</span>
                    </label>
                    <?php if ($dp_has_signature): ?>
                        <div class="dp-sig-preview dp-sig-preview-edit">
                            <span>Current signature</span>
                            <img src="<?php echo base_url($profile->signature); ?>" alt="Current doctor signature">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <div class="dp-save-bar dp-edit">
            <button type="button" class="dp-btn dp-btn-ghost" id="dpCancelEdit2"><i class="bi bi-x-lg"></i> Cancel</button>
            <button type="submit" class="dp-btn dp-btn-primary"><i class="bi bi-check2"></i> Save Changes</button>
        </div>

        <?php echo form_close(); ?>

        <!-- ============ SECURITY: collapsed row that expands ============ -->
        <div class="dp-card dp-security-card">
            <button type="button" class="dp-security-toggle" id="dpSecurityToggle" aria-expanded="false">
                <span class="dp-card-icon"><i class="bi bi-shield-lock"></i></span>
                <div class="dp-security-toggle-text">
                    <h3>Password &amp; Security</h3>
                    <p>Update the password used to sign in to your clinical account</p>
                </div>
                <i class="bi bi-chevron-down dp-security-chevron"></i>
            </button>

            <div class="dp-security-panel" id="dpSecurityPanel">
                <div class="dp-security-grid">
                    <?php echo form_open('doctor/profile/change_password', ['id' => 'doctorPasswordForm']); ?>
                    <div class="dp-field">
                        <label for="current_password">Current Password <em>*</em></label>
                        <div class="dp-pw-field">
                            <input type="password" id="current_password" name="current_password" required placeholder="Enter current password">
                            <button type="button" class="dp-pw-eye" data-target="current_password"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>
                    <div class="dp-field">
                        <label for="new_password">New Password <em>*</em></label>
                        <div class="dp-pw-field">
                            <input type="password" id="new_password" name="new_password" required minlength="6" placeholder="At least 6 characters">
                            <button type="button" class="dp-pw-eye" data-target="new_password"><i class="bi bi-eye"></i></button>
                        </div>
                        <span class="dp-field-hint"><i class="bi bi-info-circle"></i> Minimum 6 characters required.</span>
                    </div>
                    <div class="dp-field">
                        <label for="confirm_password">Confirm New Password <em>*</em></label>
                        <div class="dp-pw-field">
                            <input type="password" id="confirm_password" name="confirm_password" required minlength="6" placeholder="Re-enter new password">
                            <button type="button" class="dp-pw-eye" data-target="confirm_password"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>
                    <div class="dp-save-bar">
                        <button type="submit" class="dp-btn dp-btn-primary"><i class="bi bi-key"></i> Update Password</button>
                    </div>
                    <?php echo form_close(); ?>

                    <ul class="dp-security-tips">
                        <li><i class="bi bi-check-circle"></i> Use at least 6 characters, mixing letters, numbers &amp; symbols</li>
                        <li><i class="bi bi-check-circle"></i> Avoid reusing passwords from other accounts</li>
                        <li><i class="bi bi-check-circle"></i> Never share your login credentials with patients or staff</li>
                        <li><i class="bi bi-check-circle"></i> Update your password periodically for account safety</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap');

    :root {
        --dp-ink-950: #0f766e;
        --dp-ink-800: #1e293b;
        --dp-ink-700: #334155;
        --dp-ink-500: #64748b;
        --dp-paper: #f8fafc;
        --dp-card: #ffffff;
        --dp-line: #e2e8f0;
        --dp-gold-600: #0d9488;
        --dp-gold-700: #0f766e;
        --dp-gold-100: #ccfbf1;
        --dp-gold-050: #f0fdfa;
        --dp-danger: #ef4444;
    }

    .dp-page {
        font-family: 'Inter', sans-serif;
        background: var(--dp-paper);
        max-width: 980px;
        margin: 0 auto;
        padding: 1.75rem 1rem 3rem;
        color: var(--dp-ink-800);
    }

    .dp-page *,
    .dp-page *::before,
    .dp-page *::after {
        box-sizing: border-box;
    }

    .dp-page :focus-visible {
        outline: 2px solid var(--dp-gold-600);
        outline-offset: 2px;
    }

    /* ---------- Top bar ---------- */
    .dp-topbar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.25rem;
    }

    .dp-eyebrow {
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--dp-gold-700);
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 4px;
    }

    .dp-title {
        font-family: 'Inter', sans-serif;
        font-weight: 700;
        font-size: clamp(1.5rem, 1.2rem + 1.2vw, 2rem);
        color: var(--dp-ink-950);
        margin: 0;
    }

    .dp-topbar-actions {
        display: flex;
        gap: 0.6rem;
    }

    .dp-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 10px;
        padding: 0.65rem 1.15rem;
        font-family: 'Inter', sans-serif;
        font-weight: 600;
        font-size: 0.88rem;
        border: 1px solid transparent;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
        white-space: nowrap;
    }

    .dp-btn-primary {
        background: var(--dp-ink-950);
        color: #fff;
    }

    .dp-btn-primary:hover {
        background: var(--dp-ink-800);
        transform: translateY(-1px);
        box-shadow: 0 10px 20px -10px rgba(15, 27, 46, 0.5);
    }

    .dp-btn-ghost {
        background: transparent;
        border-color: var(--dp-line);
        color: var(--dp-ink-700);
        display: none;
    }

    .dp-btn-ghost:hover {
        background: #fff;
        border-color: var(--dp-ink-500);
    }

    /* ---------- Hero / ID card ---------- */
    .dp-hero {
        position: relative;
        background: linear-gradient(155deg, var(--dp-ink-950), #1E3049 70%);
        border-radius: 20px;
        padding: 2rem 2rem 0;
        overflow: hidden;
        box-shadow: 0 20px 40px -24px rgba(15, 27, 46, 0.55);
    }

    .dp-hero-pattern {
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255, 255, 255, 0.09) 1px, transparent 1px);
        background-size: 16px 16px;
        mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.9), transparent 75%);
        pointer-events: none;
    }

    .dp-hero-top {
        position: relative;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding-bottom: 1.75rem;
        flex-wrap: wrap;
    }

    .dp-avatar-wrap {
        position: relative;
        flex-shrink: 0;
    }

    .dp-avatar {
        width: 92px;
        height: 92px;
        border-radius: 50%;
        border: 3px solid rgba(255, 255, 255, 0.85);
        background: var(--dp-gold-100);
        color: var(--dp-gold-700);
        font-family: 'Inter', sans-serif;
        font-weight: 700;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .dp-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .dp-avatar-cam {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: var(--dp-gold-600);
        border: 2px solid var(--dp-ink-950);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .dp-avatar-cam:hover {
        background: var(--dp-gold-700);
    }

    .dp-hero-identity {
        min-width: 0;
        flex: 1;
    }

    .dp-hero-name-row {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        flex-wrap: wrap;
    }

    .dp-hero-name {
        font-family: 'Inter', sans-serif;
        font-weight: 700;
        font-size: clamp(1.35rem, 1.1rem + 1vw, 1.8rem);
        color: #fff;
        margin: 0;
        letter-spacing: 0.01em;
    }

    .dp-verified {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: var(--dp-gold-100);
        color: var(--dp-gold-700);
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 4px 10px;
        border-radius: 999px;
    }

    .dp-hero-sub {
        color: rgba(255, 255, 255, 0.82);
        font-size: 0.95rem;
        margin: 0.35rem 0 0;
    }

    .dp-hero-contact {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.82rem;
        margin: 0.5rem 0 0;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
    }

    .dp-hero-dot {
        color: rgba(255, 255, 255, 0.35);
        margin: 0 2px;
    }

    /* Credential strip: the signature element */
    .dp-cred-strip {
        position: relative;
        display: flex;
        background: rgba(255, 255, 255, 0.06);
        border-top: 1px solid rgba(255, 255, 255, 0.14);
        backdrop-filter: blur(2px);
        flex-wrap: wrap;
    }

    .dp-cred {
        flex: 1 1 0;
        min-width: 130px;
        padding: 0.9rem 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .dp-cred-label {
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: rgba(255, 255, 255, 0.5);
        font-weight: 600;
    }

    .dp-cred-value {
        font-size: 0.92rem;
        font-weight: 600;
        color: var(--dp-gold-100);
    }

    .dp-mono {
        font-family: 'JetBrains Mono', monospace;
        letter-spacing: 0.01em;
    }

    .dp-cred-sep {
        width: 1px;
        background: rgba(255, 255, 255, 0.14);
        margin: 0.9rem 0;
    }

    /* ---------- Body grid ---------- */
    .dp-body {
        margin-top: 1.5rem;
    }

    .dp-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.1rem;
    }

    .dp-card {
        background: var(--dp-card);
        border: 1px solid var(--dp-line);
        border-radius: 16px;
        padding: 1.4rem 1.5rem;
    }

    .dp-card-head {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 1.1rem;
    }

    .dp-card-icon {
        width: 38px;
        height: 38px;
        flex-shrink: 0;
        border-radius: 10px;
        background: var(--dp-gold-050);
        color: var(--dp-gold-700);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
    }

    .dp-card-head h3 {
        font-family: 'Inter', sans-serif;
        font-weight: 600;
        font-size: 1.05rem;
        color: var(--dp-ink-950);
        margin: 0 0 2px;
    }

    .dp-card-head p {
        font-size: 0.8rem;
        color: var(--dp-ink-500);
        margin: 0;
    }

    /* View mode rows */
    .dp-row {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.65rem 0;
        border-bottom: 1px dashed var(--dp-line);
    }

    .dp-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .dp-row-block {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }

    .dp-row-label {
        font-size: 0.8rem;
        color: var(--dp-ink-500);
        flex-shrink: 0;
    }

    .dp-row-value {
        font-size: 0.92rem;
        font-weight: 600;
        color: var(--dp-ink-950);
        text-align: right;
        word-break: break-word;
    }

    .dp-row-block .dp-row-value {
        text-align: left;
        font-weight: 500;
        line-height: 1.5;
    }

    /* Edit mode fields (hidden by default) */
    .dp-edit {
        display: none;
    }

    .dp-field {
        margin-bottom: 1rem;
    }

    .dp-field:last-child {
        margin-bottom: 0;
    }

    .dp-field-narrow {
        max-width: 160px;
    }

    .dp-field label {
        display: block;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--dp-ink-700);
        margin-bottom: 0.35rem;
    }

    .dp-field label em {
        color: var(--dp-danger);
        font-style: normal;
    }

    .dp-field input[type="text"],
    .dp-field input[type="email"],
    .dp-field input[type="number"],
    .dp-field input[type="password"],
    .dp-field textarea {
        width: 100%;
        border: 1px solid var(--dp-line);
        background: var(--dp-paper);
        border-radius: 10px;
        padding: 0.62rem 0.85rem;
        font-family: 'Inter', sans-serif;
        font-size: 0.9rem;
        color: var(--dp-ink-950);
        transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }

    .dp-field textarea {
        resize: vertical;
        min-height: 84px;
    }

    .dp-field input:focus,
    .dp-field textarea:focus {
        outline: none;
        border-color: var(--dp-gold-600);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(179, 133, 42, 0.16);
    }

    .dp-field-hint {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.75rem;
        color: var(--dp-ink-500);
        margin-top: 0.4rem;
    }

    /* Signature */
    .dp-sig-preview {
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--dp-paper);
        border: 1px solid var(--dp-line);
        border-radius: 12px;
        padding: 1rem;
    }

    .dp-sig-preview img {
        max-height: 64px;
        max-width: 100%;
    }

    .dp-sig-preview-edit {
        flex-direction: column;
        gap: 0.5rem;
        margin-top: 0.9rem;
        font-size: 0.78rem;
        color: var(--dp-ink-500);
        font-weight: 600;
    }

    .dp-sig-upload {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 4px;
        border: 1.5px dashed var(--dp-line);
        border-radius: 12px;
        padding: 1.4rem 1rem;
        color: var(--dp-ink-500);
        cursor: pointer;
        background: var(--dp-paper);
        transition: all 0.15s ease;
    }

    .dp-sig-upload i {
        font-size: 1.5rem;
        color: var(--dp-gold-600);
        margin-bottom: 4px;
    }

    .dp-sig-upload:hover {
        border-color: var(--dp-gold-600);
        background: var(--dp-gold-050);
    }

    .dp-sig-upload-title {
        font-weight: 600;
        color: var(--dp-ink-800);
        font-size: 0.88rem;
    }

    .dp-sig-upload-hint {
        font-size: 0.75rem;
    }

    /* Save bar */
    .dp-save-bar {
        display: none;
        justify-content: flex-end;
        gap: 0.7rem;
        margin-top: 1.25rem;
        padding-top: 1.1rem;
        border-top: 1px solid var(--dp-line);
    }

    /* ---------- Editing state ---------- */
    .dp-body.is-editing .dp-view {
        display: none;
    }

    .dp-body.is-editing .dp-edit {
        display: block;
    }

    .dp-body.is-editing .dp-save-bar {
        display: flex;
    }

    .dp-body.is-editing .dp-btn-ghost {
        display: inline-flex;
    }

    /* ---------- Security card ---------- */
    .dp-security-card {
        margin-top: 1.1rem;
        padding: 0;
        overflow: hidden;
    }

    .dp-security-toggle {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 12px;
        background: transparent;
        border: none;
        padding: 1.4rem 1.5rem;
        cursor: pointer;
        text-align: left;
    }

    .dp-security-toggle-text {
        flex: 1;
    }

    .dp-security-toggle-text h3 {
        font-family: 'Inter', sans-serif;
        font-weight: 600;
        font-size: 1.05rem;
        color: var(--dp-ink-950);
        margin: 0 0 2px;
    }

    .dp-security-toggle-text p {
        font-size: 0.8rem;
        color: var(--dp-ink-500);
        margin: 0;
    }

    .dp-security-chevron {
        color: var(--dp-ink-500);
        transition: transform 0.2s ease;
        font-size: 1rem;
    }

    .dp-security-toggle[aria-expanded="true"] .dp-security-chevron {
        transform: rotate(180deg);
    }

    .dp-security-panel {
        display: none;
        padding: 0 1.5rem 1.5rem;
        border-top: 1px solid var(--dp-line);
    }

    .dp-security-panel.is-open {
        display: block;
    }

    .dp-security-grid {
        display: grid;
        grid-template-columns: 1.3fr 1fr;
        gap: 2rem;
        padding-top: 1.4rem;
        align-items: start;
    }

    .dp-pw-field {
        position: relative;
    }

    .dp-pw-field input {
        padding-right: 2.6rem;
    }

    .dp-pw-eye {
        position: absolute;
        right: 0.6rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--dp-ink-500);
        cursor: pointer;
        padding: 4px;
    }

    .dp-pw-eye:hover {
        color: var(--dp-gold-700);
    }

    .dp-security-tips {
        list-style: none;
        margin: 0;
        padding: 1rem 1.1rem;
        background: var(--dp-gold-050);
        border: 1px solid var(--dp-gold-100);
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .dp-security-tips li {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 0.82rem;
        color: var(--dp-ink-700);
        line-height: 1.45;
    }

    .dp-security-tips i {
        color: var(--dp-gold-600);
        margin-top: 2px;
        flex-shrink: 0;
    }

    /* ---------- Mobile ---------- */
    @media (max-width: 860px) {
        .dp-grid {
            grid-template-columns: 1fr;
        }

        .dp-security-grid {
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }
    }

    @media (max-width: 640px) {
        .dp-page {
            padding: 1.25rem 0.75rem 2.5rem;
        }

        .dp-topbar {
            align-items: flex-start;
        }

        .dp-topbar-actions {
            width: 100%;
        }

        .dp-topbar-actions .dp-btn {
            flex: 1;
        }

        .dp-hero {
            padding: 1.5rem 1.25rem 0;
            border-radius: 16px;
        }

        .dp-hero-top {
            flex-direction: column;
            text-align: center;
            gap: 0.9rem;
            padding-bottom: 1.4rem;
        }

        .dp-hero-name-row {
            justify-content: center;
        }

        .dp-hero-sub,
        .dp-hero-contact {
            justify-content: center;
        }

        .dp-cred-strip {
            flex-wrap: wrap;
            margin: 0 -1.25rem;
            width: calc(100% + 2.5rem);
        }

        .dp-cred {
            flex: 1 1 50%;
            min-width: 0;
        }

        .dp-cred-sep:nth-child(4) {
            display: none;
        }

        .dp-card {
            padding: 1.15rem 1.1rem;
        }

        .dp-save-bar {
            position: sticky;
            bottom: 0.75rem;
            background: var(--dp-card);
            border: 1px solid var(--dp-line);
            border-radius: 12px;
            padding: 0.85rem;
            margin-top: 1rem;
            box-shadow: 0 10px 24px -12px rgba(15, 27, 46, 0.25);
        }

        .dp-body.is-editing .dp-save-bar {
            display: flex;
        }

        .dp-save-bar .dp-btn {
            flex: 1;
        }
    }
</style>

<script>
    (function() {
        var body = document.getElementById('dpBody');
        var editBtn = document.getElementById('dpEditToggle');
        var cancelBtns = [document.getElementById('dpCancelEdit'), document.getElementById('dpCancelEdit2')];

        function enterEdit() {
            body.classList.add('is-editing');
            editBtn.style.display = 'none';
        }

        function exitEdit() {
            body.classList.remove('is-editing');
            editBtn.style.display = '';
        }

        if (editBtn) editBtn.addEventListener('click', enterEdit);
        cancelBtns.forEach(function(btn) {
            if (btn) btn.addEventListener('click', exitEdit);
        });

        // Security panel expand/collapse
        var secToggle = document.getElementById('dpSecurityToggle');
        var secPanel = document.getElementById('dpSecurityPanel');
        if (secToggle && secPanel) {
            secToggle.addEventListener('click', function() {
                var isOpen = secPanel.classList.toggle('is-open');
                secToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        }

        // Password show/hide toggles
        document.querySelectorAll('.dp-pw-eye').forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                var input = document.getElementById(toggle.getAttribute('data-target'));
                var icon = toggle.querySelector('i');
                if (!input) return;
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('bi-eye', 'bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('bi-eye-slash', 'bi-eye');
                }
            });
        });

        // Confirm-password live match
        var newPw = document.getElementById('new_password');
        var confirmPw = document.getElementById('confirm_password');
        if (newPw && confirmPw) {
            var checkMatch = function() {
                confirmPw.setCustomValidity(
                    confirmPw.value && confirmPw.value !== newPw.value ? 'Passwords do not match' : ''
                );
            };
            newPw.addEventListener('input', checkMatch);
            confirmPw.addEventListener('input', checkMatch);
        }
    })();

    function dpPreviewAvatar(event) {
        var input = event.target;
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = document.getElementById('avatarImage');
                var initials = document.getElementById('avatarInitials');
                img.src = e.target.result;
                img.style.display = 'block';
                if (initials) initials.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function dpPreviewSignature(event) {
        var input = event.target;
        var label = document.getElementById('signatureFileLabel');
        if (label) {
            label.textContent = (input.files && input.files[0]) ? input.files[0].name : 'Choose signature image';
        }
    }
</script>