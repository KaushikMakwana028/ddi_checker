<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- SweetAlert2 CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="doctor-manage-page">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div>
            <div class="page-eyebrow">
                <i class="bi bi-people-fill"></i> Clinical Practitioners
            </div>
            <h2 class="page-title">Doctor Directory</h2>
            <p class="page-subtitle">Manage clinical practitioners, medical licenses, specializations, and authorized portal access.</p>
        </div>
        <div class="header-actions">
            <a href="<?php echo base_url('admin/doctors/add'); ?>" class="btn-primary">
                <i class="bi bi-person-plus-fill"></i> <span>Add New Doctor</span>
            </a>
        </div>
    </div>

    <!-- Main Panel & Searchable Doctors Table -->
    <div class="panel-card">
        <!-- Filter Bar -->
        <div class="table-filterbar">
            <div class="filter-search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" id="doctorSearchInput" placeholder="Search by doctor name, email, specialization, or license #..." autocomplete="off">
            </div>
            <button type="button" id="clearDoctorSearchBtn" class="filter-reset-btn" title="Reset Search">
                <i class="bi bi-arrow-counterclockwise"></i> <span>Reset</span>
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 ddi-table" id="doctorsTable">
                <thead>
                    <tr>
                        <th style="min-width: 190px; width: 22%;">Doctor</th>
                        <th style="min-width: 130px; width: 14%;">Contact</th>
                        <th style="min-width: 140px; width: 15%;">Specialization</th>
                        <th style="min-width: 170px; width: 18%;">Hospital / Clinic</th>
                        <th style="min-width: 130px; width: 13%;">Reg. Number</th>
                        <th style="min-width: 90px; width: 8%;">Status</th>
                        <th class="text-end" style="min-width: 95px; width: 10%;">Actions</th>
                    </tr>
                </thead>
                <tbody id="doctorsTableBody">
                    <?php if (empty($doctors)): ?>
                        <tr class="no-doctors-row">
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="empty-state">
                                    <i class="bi bi-person-x"></i>
                                    <h3>No doctors found</h3>
                                    <p>Get started by registering your first clinical practitioner.</p>
                                    <a href="<?php echo base_url('admin/doctors/add'); ?>" class="btn-primary">
                                        <i class="bi bi-person-plus-fill"></i> Add New Doctor
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($doctors as $doc): ?>
                            <tr class="doctor-row" data-id="<?php echo $doc['id']; ?>">
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="avatar-circle shadow-sm" style="width: 36px; height: 36px; min-width: 36px; background-color: #f0fdfa; border: 1px solid #99f6e4; color: #0f766e; font-size: 0.85rem; font-weight: 700; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <?php
                                            $doc_initials = '';
                                            $words = explode(' ', $doc['name']);
                                            foreach ($words as $w) {
                                                $doc_initials .= substr($w, 0, 1);
                                            }
                                            echo strtoupper(substr($doc_initials, 0, 2)) ?: 'DR';
                                            ?>
                                        </div>
                                        <div class="d-flex flex-column text-truncate" style="min-width: 0;">
                                            <span class="fw-semibold text-dark text-truncate doctor-name-cell"><?php echo html_escape($doc['name']); ?></span>
                                            <small class="text-muted text-truncate doctor-email-cell"><?php echo html_escape($doc['email']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-secondary doctor-mobile-cell">
                                    <span class="text-nowrap"><i class="bi bi-telephone text-muted me-1 small"></i><?php echo html_escape($doc['mobile']); ?></span>
                                </td>
                                <td class="doctor-spec-cell">
                                    <?php if (!empty($doc['specialization'])): ?>
                                        <span class="category-badge"><?php echo html_escape($doc['specialization']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">General Practice</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-secondary doctor-hospital-cell">
                                    <?php echo !empty($doc['hospital_clinic']) ? html_escape($doc['hospital_clinic']) : '<span class="text-muted small">—</span>'; ?>
                                </td>
                                <td class="doctor-reg-cell">
                                    <span class="stock-badge">
                                        <i class="bi bi-shield-check text-teal"></i>
                                        <?php echo html_escape($doc['registration_number'] ?: '—'); ?>
                                    </span>
                                </td>
                                <td class="status-cell">
                                    <?php if ($doc['is_active']): ?>
                                        <span class="status-badge status-active"><i class="bi bi-dot"></i>Active</span>
                                    <?php else: ?>
                                        <span class="status-badge status-inactive"><i class="bi bi-dot"></i>Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-3 actions-cell">
                                    <div class="actions-group">
                                        <a href="<?php echo base_url('admin/doctors/edit/' . $doc['id']); ?>" class="rule-action-btn edit-doctor-btn" 
                                            title="Edit Doctor" aria-label="Edit Doctor">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <?php if ($doc['is_active']): ?>
                                            <button type="button" class="rule-action-btn btn-warning-soft deactivate-doctor-btn" 
                                                data-id="<?php echo $doc['id']; ?>"
                                                data-name="<?php echo html_escape($doc['name']); ?>"
                                                title="Deactivate Doctor" aria-label="Deactivate Doctor">
                                                <i class="bi bi-pause-circle"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="rule-action-btn btn-success-soft activate-doctor-btn" 
                                                data-id="<?php echo $doc['id']; ?>"
                                                data-name="<?php echo html_escape($doc['name']); ?>"
                                                title="Activate Doctor" aria-label="Activate Doctor">
                                                <i class="bi bi-play-circle"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .doctor-manage-page,
    .doctor-manage-page * {
        font-family: 'Poppins', sans-serif;
        box-sizing: border-box;
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
        color: #0f766e;
        background: #f0fdfa;
        border: 1px solid #99f6e4;
        padding: 4px 12px;
        border-radius: 999px;
        margin-bottom: 8px;
    }

    .page-title {
        font-size: 26px;
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

    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 12px;
        padding: 10px 18px;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all .15s ease;
        white-space: nowrap;
        text-decoration: none;
        background: #0f766e !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(15, 118, 110, .25);
    }

    .btn-primary:hover {
        background: #0c5f59 !important;
        color: #ffffff !important;
        transform: translateY(-1px);
    }

    /* Panel Card & Filterbar */
    .panel-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
    }

    .table-filterbar {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        background: #ffffff;
        flex-wrap: wrap;
    }

    .filter-search-wrap {
        flex: 1 1 260px;
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 0 14px;
        height: 40px;
        max-height: 40px;
    }

    .filter-search-wrap i {
        color: #94a3b8;
        font-size: 14px;
        flex-shrink: 0;
    }

    .filter-search-wrap input {
        border: none;
        background: transparent;
        outline: none;
        width: 100%;
        height: 100%;
        font-size: 13.5px;
        color: #0f172a;
    }

    .filter-reset-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 0 14px;
        height: 40px;
        max-height: 40px;
        font-size: 13px;
        font-weight: 500;
        color: #64748b;
        cursor: pointer;
        transition: all .15s ease;
    }

    .filter-reset-btn:hover {
        color: #0f766e;
        border-color: #0f766e;
    }

    /* Table Styles */
    .ddi-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
    }

    .ddi-table thead th {
        background-color: #fbfcfe;
        color: #64748b;
        font-size: 11.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
        padding: 14px 18px;
        border-bottom: 1px solid #e2e8f0;
        border-top: none;
        white-space: nowrap;
    }

    .ddi-table tbody tr {
        transition: background-color .15s ease;
        border-bottom: 1px solid #f1f5f9;
    }

    .ddi-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .ddi-table tbody td {
        padding: 13px 18px;
        vertical-align: middle;
        font-size: 13.5px;
        border-bottom: 1px solid #f1f5f9;
    }

    .category-badge {
        display: inline-flex;
        font-size: 12px;
        font-weight: 500;
        padding: 3px 9px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
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
        font-size: 12px;
        font-weight: 600;
        padding: 3px 9px 3px 5px;
        border-radius: 999px;
        white-space: nowrap;
    }

    .status-badge i {
        font-size: 20px;
        line-height: 0;
    }

    .status-active {
        background: #ecfdf5;
        color: #059669;
    }

    .status-inactive {
        background: #fef2f2;
        color: #dc2626;
    }

    /* Actions */
    .actions-group {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        justify-content: flex-end;
        flex-nowrap: nowrap;
    }

    .rule-action-btn {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        cursor: pointer;
        transition: all .15s ease;
        padding: 0;
        line-height: 1;
        flex-shrink: 0;
        text-decoration: none;
    }

    .rule-action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, .08);
    }

    .rule-action-btn.edit-doctor-btn {
        color: #0f766e;
        border-color: #99f6e4;
        background: #f0fdfa;
    }
    .rule-action-btn.edit-doctor-btn:hover {
        color: #ffffff;
        background: #0d9488;
        border-color: #0d9488;
    }

    .rule-action-btn.btn-warning-soft {
        color: #b45309;
        border-color: #fde68a;
        background: #fffbeb;
    }
    .rule-action-btn.btn-warning-soft:hover {
        color: #ffffff;
        background: #d97706;
        border-color: #d97706;
    }

    .rule-action-btn.btn-success-soft {
        color: #15803d;
        border-color: #bbf7d0;
        background: #f0fdf4;
    }
    .rule-action-btn.btn-success-soft:hover {
        color: #ffffff;
        background: #16a34a;
        border-color: #16a34a;
    }

    .empty-state {
        text-align: center;
        padding: 48px 20px;
        color: #64748b;
    }

    .empty-state i {
        font-size: 40px;
        color: #94a3b8;
        display: block;
        margin-bottom: 12px;
    }

    .empty-state h3 {
        font-size: 16px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .empty-state p {
        font-size: 13px;
        margin-bottom: 16px;
        max-width: 360px;
        margin-left: auto;
        margin-right: auto;
    }

    @media (max-width: 767px) {
        .page-header {
            flex-direction: column;
            align-items: stretch;
            gap: 14px;
        }

        .header-actions .btn-primary {
            width: 100%;
            justify-content: center;
        }

        .table-filterbar {
            padding: 12px 14px;
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
        }

        .filter-search-wrap {
            flex: 0 0 40px !important;
            width: 100% !important;
            height: 40px !important;
            max-height: 40px !important;
            min-height: 40px !important;
        }

        .filter-reset-btn {
            flex: 0 0 40px !important;
            width: 100% !important;
            height: 40px !important;
            max-height: 40px !important;
            min-height: 40px !important;
            justify-content: center;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    window.csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    window.csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

    const SwalCustom = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-primary px-4 py-2 mx-1',
            cancelButton: 'btn btn-outline-secondary px-4 py-2 mx-1'
        },
        buttonsStyling: false
    });

    const doctorsTableBody = document.getElementById('doctorsTableBody');
    const doctorSearchInput = document.getElementById('doctorSearchInput');
    const clearDoctorSearchBtn = document.getElementById('clearDoctorSearchBtn');

    // 1. Filter Table Search
    if (doctorSearchInput) {
        doctorSearchInput.addEventListener('keyup', function() {
            filterDoctors();
        });
    }

    if (clearDoctorSearchBtn) {
        clearDoctorSearchBtn.addEventListener('click', function() {
            if (doctorSearchInput) {
                doctorSearchInput.value = '';
                filterDoctors();
            }
        });
    }

    function filterDoctors() {
        const query = doctorSearchInput ? doctorSearchInput.value.toLowerCase().trim() : '';
        const rows = document.querySelectorAll('#doctorsTableBody tr.doctor-row');
        
        let visibleCount = 0;
        rows.forEach(row => {
            const name = row.querySelector('.doctor-name-cell') ? row.querySelector('.doctor-name-cell').textContent.toLowerCase() : '';
            const email = row.querySelector('.doctor-email-cell') ? row.querySelector('.doctor-email-cell').textContent.toLowerCase() : '';
            const spec = row.querySelector('.doctor-spec-cell') ? row.querySelector('.doctor-spec-cell').textContent.toLowerCase() : '';
            const hosp = row.querySelector('.doctor-hospital-cell') ? row.querySelector('.doctor-hospital-cell').textContent.toLowerCase() : '';
            const reg = row.querySelector('.doctor-reg-cell') ? row.querySelector('.doctor-reg-cell').textContent.toLowerCase() : '';
            
            if (name.includes(query) || email.includes(query) || spec.includes(query) || hosp.includes(query) || reg.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const existingNoResults = document.getElementById('noResultsDoctorRow');
        if (visibleCount === 0 && rows.length > 0) {
            if (!existingNoResults) {
                const noResults = document.createElement('tr');
                noResults.id = 'noResultsDoctorRow';
                noResults.innerHTML = `
                    <td colspan="7" class="text-center py-5 text-muted">
                        <div class="empty-state">
                            <i class="bi bi-search"></i>
                            <h3>No matching doctors</h3>
                            <p>No practitioners match: "<strong>${escapeHtml(query)}</strong>"</p>
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5" onclick="document.getElementById('clearDoctorSearchBtn').click()">Reset Search</button>
                        </div>
                    </td>
                `;
                doctorsTableBody.appendChild(noResults);
            } else {
                existingNoResults.style.display = '';
                existingNoResults.querySelector('p').innerHTML = `No practitioners match: "<strong>${escapeHtml(query)}</strong>"`;
            }
        } else if (existingNoResults) {
            existingNoResults.style.display = 'none';
        }
    }

    // 2. Delegate Actions (Deactivate, Activate)
    if (doctorsTableBody) {
        doctorsTableBody.addEventListener('click', function(e) {
            const deactivateBtn = e.target.closest('.deactivate-doctor-btn');
            if (deactivateBtn) {
                const id = deactivateBtn.dataset.id;
                const name = deactivateBtn.dataset.name;
                SwalCustom.fire({
                    title: 'Deactivate Doctor Account?',
                    html: `Are you sure you want to suspend portal access for <strong>"${escapeHtml(name)}"</strong>?<br><small class="text-muted">The practitioner will be unable to log in until reactivated.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Deactivate',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deactivateDoctor(id);
                    }
                });
                return;
            }

            const activateBtn = e.target.closest('.activate-doctor-btn');
            if (activateBtn) {
                const id = activateBtn.dataset.id;
                const name = activateBtn.dataset.name;
                SwalCustom.fire({
                    title: 'Reactivate Doctor Account?',
                    html: `Are you sure you want to restore portal access for <strong>"${escapeHtml(name)}"</strong>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Reactivate',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        activateDoctor(id);
                    }
                });
                return;
            }
        });
    }

    function deactivateDoctor(id) {
        const formData = new FormData();
        formData.append(window.csrfName, window.csrfHash);

        fetch(`<?php echo base_url('admin/doctors/deactivate/'); ?>${id}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            updateCsrfTokens(data.csrf_name, data.csrf_hash);
            if (data.status === 'success') {
                showAlert('success', data.message);
                refreshDoctorsTable();
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error deactivating doctor:', error);
            showAlert('error', 'An error occurred while attempting to deactivate the account.');
        });
    }

    function activateDoctor(id) {
        const formData = new FormData();
        formData.append(window.csrfName, window.csrfHash);

        fetch(`<?php echo base_url('admin/doctors/activate/'); ?>${id}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            updateCsrfTokens(data.csrf_name, data.csrf_hash);
            if (data.status === 'success') {
                showAlert('success', data.message);
                refreshDoctorsTable();
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error activating doctor:', error);
            showAlert('error', 'An error occurred while attempting to activate the account.');
        });
    }

    function refreshDoctorsTable() {
        fetch('<?php echo base_url("admin/doctors"); ?>')
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTableBody = doc.getElementById('doctorsTableBody');
                if (newTableBody && doctorsTableBody) {
                    doctorsTableBody.innerHTML = newTableBody.innerHTML;
                    if (doctorSearchInput && doctorSearchInput.value !== '') {
                        filterDoctors();
                    }
                }
            })
            .catch(error => {
                console.error('Error refreshing doctor list:', error);
            });
    }

    function updateCsrfTokens(name, hash) {
        window.csrfName = name;
        window.csrfHash = hash;

        const csrfInputs = document.querySelectorAll(`input[name="${name}"]`);
        csrfInputs.forEach(input => {
            input.value = hash;
        });
    }

    function showAlert(type, message) {
        const isSuccess = type === 'success';
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        Toast.fire({
            icon: isSuccess ? 'success' : 'error',
            title: message
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
});
</script>
