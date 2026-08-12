<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* Custom style to override SweetAlert2 defaults to match the DDI Checker brand */
.swal2-popup {
    font-family: 'Outfit', sans-serif !important;
    border-radius: 16px !important;
}
.swal2-title {
    font-weight: 700 !important;
    color: #0f172a !important;
}
.swal2-html-container {
    color: #475569 !important;
    font-size: 0.95rem !important;
}
</style>

<!-- Breadcrumbs & Page Header -->
<div class="row mb-4">
    <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item">
                        <a href="<?php echo base_url('dashboard'); ?>" class="text-teal text-decoration-none" style="color: #0d9488; font-weight: 500;">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Drug Entry</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0">Drug Database</h2>
            <p class="text-muted mb-0">Manage clinical drugs, therapeutic categories, and drug synonyms.</p>
        </div>
        <div>
            <button type="button" class="btn btn-teal rounded-pill px-4 py-2 d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#addDrugModal" style="background-color: #0d9488; border-color: #0d9488; color: #ffffff; font-weight: 500;">
                <i class="bi bi-plus-lg"></i> Add New Drug
            </button>
        </div>
    </div>
</div>

<!-- Searchable Drugs Table -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="row align-items-center g-3">
                    <div class="col-md-6">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-capsule text-teal" style="color: #0d9488;"></i>
                            <span>Registered Drugs</span>
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-secondary-subtle"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="tableSearchInput" class="form-control border-secondary-subtle" placeholder="Search drugs by name, category, or synonyms...">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive" style="min-height: 200px;">
                    <table class="table table-hover align-middle mb-0" id="drugsTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 20%;">Drug Name</th>
                                <th style="width: 15%;">Category</th>
                                <th style="width: 25%;">Synonyms</th>
                                <th style="width: 10%;">Status</th>
                                <th class="text-end pe-4" style="min-width: 300px; width: 300px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="drugsTableBody">
                            <?php if (empty($drugs)): ?>
                                <tr class="no-drugs-row">
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-folder-x display-6 mb-3 d-block text-secondary"></i>
                                        <span>No drugs found in the database. Click <strong>Add New Drug</strong> to register one.</span>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($drugs as $drug): ?>
                                    <tr class="drug-row" data-id="<?php echo $drug->id; ?>">
                                        <td class="ps-4 fw-semibold text-dark drug-name-cell">
                                            <?php echo html_escape($drug->drug_name); ?>
                                        </td>
                                        <td class="text-secondary category-cell">
                                            <?php echo !empty($drug->category) ? html_escape($drug->category) : '<span class="text-muted small">None</span>'; ?>
                                        </td>
                                        <td class="synonyms-cell">
                                            <?php 
                                            if (!empty($drug->synonyms)) {
                                                $syns = explode(',', $drug->synonyms);
                                                foreach ($syns as $syn) {
                                                    $syn_trimmed = trim($syn);
                                                    if ($syn_trimmed !== '') {
                                                        echo '<span class="badge bg-light text-secondary border me-1 my-0.5">' . html_escape($syn_trimmed) . '</span>';
                                                    }
                                                }
                                            } else {
                                                echo '<span class="text-muted small">-</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="status-cell">
                                            <?php if ($drug->is_active): ?>
                                                <span class="badge bg-success-subtle text-success px-3 py-1.5 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger px-3 py-1.5 rounded-pill"><i class="bi bi-x-circle-fill me-1"></i>Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4 actions-cell">
                                            <div class="d-flex justify-content-end gap-2 flex-nowrap">
                                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-2.5 edit-drug-btn" 
                                                        data-id="<?php echo $drug->id; ?>" 
                                                        data-name="<?php echo html_escape($drug->drug_name); ?>"
                                                        data-category="<?php echo html_escape($drug->category); ?>"
                                                        data-synonyms="<?php echo html_escape($drug->synonyms); ?>">
                                                    <i class="bi bi-pencil me-1"></i> Edit
                                                </button>
                                                <?php if ($drug->is_active): ?>
                                                    <button type="button" class="btn btn-outline-warning btn-sm rounded-pill px-2.5 deactivate-drug-btn" 
                                                            data-id="<?php echo $drug->id; ?>"
                                                            data-name="<?php echo html_escape($drug->drug_name); ?>">
                                                        <i class="bi bi-slash-circle me-1"></i> Deactivate
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-2.5 activate-drug-btn" 
                                                            data-id="<?php echo $drug->id; ?>"
                                                            data-name="<?php echo html_escape($drug->drug_name); ?>">
                                                        <i class="bi bi-check-circle me-1"></i> Activate
                                                    </button>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-2.5 delete-drug-btn" 
                                                        data-id="<?php echo $drug->id; ?>"
                                                        data-name="<?php echo html_escape($drug->drug_name); ?>">
                                                    <i class="bi bi-trash me-1"></i> Delete
                                                </button>
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
    </div>
</div>

<!-- Modal: Add Drug -->
<div class="modal fade" id="addDrugModal" tabindex="-1" aria-labelledby="addDrugModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold" id="addDrugModalLabel"><i class="bi bi-plus-circle text-teal me-2" style="color: #0d9488;"></i>Add New Drug</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('drug-entry/add', ['id' => 'addDrugForm']); ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="add_drug_name" class="form-label fw-semibold text-secondary small">Drug Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-secondary-subtle"><i class="bi bi-capsule text-muted"></i></span>
                            <input type="text" class="form-control border-secondary-subtle py-2" id="add_drug_name" name="drug_name" required placeholder="Enter drug name (e.g. Ibuprofen)">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="add_category" class="form-label fw-semibold text-secondary small">Therapeutic Category</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-secondary-subtle"><i class="bi bi-tag text-muted"></i></span>
                            <input type="text" class="form-control border-secondary-subtle py-2" id="add_category" name="category" placeholder="e.g. NSAID, Analgesic">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="add_synonyms" class="form-label fw-semibold text-secondary small">Synonyms / Brand Names (Comma-separated)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-secondary-subtle"><i class="bi bi-journal-text text-muted"></i></span>
                            <textarea class="form-control border-secondary-subtle py-2" id="add_synonyms" name="synonyms" rows="2" placeholder="e.g. Advil, Motrin, Nurofen"></textarea>
                        </div>
                        <div class="form-text text-muted small mt-1">Separate multiple names with a comma.</div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-teal rounded-pill px-4" style="background-color: #0d9488; border-color: #0d9488; color: #ffffff;">Save Drug</button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- Modal: Edit Drug -->
<div class="modal fade" id="editDrugModal" tabindex="-1" aria-labelledby="editDrugModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold" id="editDrugModalLabel"><i class="bi bi-pencil-square text-teal me-2" style="color: #0d9488;"></i>Edit Drug Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('', ['id' => 'editDrugForm']); ?>
                <input type="hidden" id="edit_drug_id" name="id" value="">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="edit_drug_name" class="form-label fw-semibold text-secondary small">Drug Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-secondary-subtle"><i class="bi bi-capsule text-muted"></i></span>
                            <input type="text" class="form-control border-secondary-subtle py-2" id="edit_drug_name" name="drug_name" required placeholder="Enter drug name">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_category" class="form-label fw-semibold text-secondary small">Therapeutic Category</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-secondary-subtle"><i class="bi bi-tag text-muted"></i></span>
                            <input type="text" class="form-control border-secondary-subtle py-2" id="edit_category" name="category" placeholder="e.g. NSAID, Analgesic">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_synonyms" class="form-label fw-semibold text-secondary small">Synonyms / Brand Names (Comma-separated)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-secondary-subtle"><i class="bi bi-journal-text text-muted"></i></span>
                            <textarea class="form-control border-secondary-subtle py-2" id="edit_synonyms" name="synonyms" rows="2" placeholder="e.g. Advil, Motrin"></textarea>
                        </div>
                        <div class="form-text text-muted small mt-1">Separate multiple names with a comma.</div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-teal rounded-pill px-4" style="background-color: #0d9488; border-color: #0d9488; color: #ffffff;">Update Drug</button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Global CSRF details loaded initially
    window.csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    window.csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

    // Custom Mixin for confirmations styled with our design system
    const SwalCustom = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-teal rounded-pill px-4 py-2 mx-1 shadow-sm',
            cancelButton: 'btn btn-outline-secondary rounded-pill px-4 py-2 mx-1'
        },
        buttonsStyling: false
    });

    const drugsTableBody = document.getElementById('drugsTableBody');
    const tableSearchInput = document.getElementById('tableSearchInput');

    // 1. Client-Side Live Search Filtering
    if (tableSearchInput) {
        tableSearchInput.addEventListener('keyup', function() {
            filterTable();
        });
    }

    function filterTable() {
        const query = tableSearchInput.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#drugsTableBody tr.drug-row');
        
        let visibleCount = 0;
        rows.forEach(row => {
            const name = row.querySelector('.drug-name-cell').textContent.toLowerCase();
            const category = row.querySelector('.category-cell').textContent.toLowerCase();
            const synonyms = row.querySelector('.synonyms-cell').textContent.toLowerCase();
            
            if (name.includes(query) || category.includes(query) || synonyms.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide empty state message if filtering hides everything
        const existingNoResults = document.getElementById('noResultsRow');
        if (visibleCount === 0 && rows.length > 0) {
            if (!existingNoResults) {
                const noResults = document.createElement('tr');
                noResults.id = 'noResultsRow';
                noResults.innerHTML = `
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="bi bi-search mb-2 d-block text-secondary fs-4"></i>
                        <span>No drugs match your search query: "${escapeHtml(query)}"</span>
                    </td>
                `;
                drugsTableBody.appendChild(noResults);
            } else {
                existingNoResults.style.display = '';
                existingNoResults.querySelector('span').textContent = `No drugs match your search query: "${query}"`;
            }
        } else if (existingNoResults) {
            existingNoResults.style.display = 'none';
        }
    }

    // 2. Delegate Actions (Edit / Deactivate Click Listeners)
    if (drugsTableBody) {
        drugsTableBody.addEventListener('click', function(e) {
            // Edit button click
            const editBtn = e.target.closest('.edit-drug-btn');
            if (editBtn) {
                const id = editBtn.dataset.id;
                const name = editBtn.dataset.name;
                const category = editBtn.dataset.category;
                const synonyms = editBtn.dataset.synonyms;

                // Fill Form
                document.getElementById('edit_drug_id').value = id;
                document.getElementById('edit_drug_name').value = name;
                document.getElementById('edit_category').value = category;
                document.getElementById('edit_synonyms').value = synonyms;

                // Update form action url dynamically
                document.getElementById('editDrugForm').action = `<?php echo base_url('drug-entry/edit/'); ?>${id}`;

                // Show Modal
                const editModal = new bootstrap.Modal(document.getElementById('editDrugModal'));
                editModal.show();
                return;
            }

            // Deactivate button click
            const deactivateBtn = e.target.closest('.deactivate-drug-btn');
            if (deactivateBtn) {
                const id = deactivateBtn.dataset.id;
                const name = deactivateBtn.dataset.name;
                SwalCustom.fire({
                    title: 'Deactivate Drug?',
                    html: `Are you sure you want to deactivate <strong>"${escapeHtml(name)}"</strong>?<br><small class="text-muted">Past prescriptions referencing this drug will still be preserved, but it will not appear in autocomplete searches.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, deactivate',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        deactivateDrug(id);
                    }
                });
                return;
            }

            // Activate button click
            const activateBtn = e.target.closest('.activate-drug-btn');
            if (activateBtn) {
                const id = activateBtn.dataset.id;
                const name = activateBtn.dataset.name;
                SwalCustom.fire({
                    title: 'Reactivate Drug?',
                    html: `Are you sure you want to reactivate <strong>"${escapeHtml(name)}"</strong>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, reactivate',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        activateDrug(id);
                    }
                });
                return;
            }

            // Delete button click
            const deleteBtn = e.target.closest('.delete-drug-btn');
            if (deleteBtn) {
                const id = deleteBtn.dataset.id;
                const name = deleteBtn.dataset.name;
                SwalCustom.fire({
                    title: 'Permanently Delete?',
                    html: `Are you sure you want to permanently delete <strong>"${escapeHtml(name)}"</strong>?<br><small class="text-danger">This action cannot be undone and will delete the drug completely from the database.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'btn btn-danger rounded-pill px-4 py-2 mx-1 shadow-sm',
                        cancelButton: 'btn btn-outline-secondary rounded-pill px-4 py-2 mx-1'
                    },
                    buttonsStyling: false,
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteDrug(id);
                    }
                });
                return;
            }
        });
    }

    // 3. Form Submit Handler: Add Drug
    const addDrugForm = document.getElementById('addDrugForm');
    if (addDrugForm) {
        addDrugForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(addDrugForm);

            // Fetch request
            fetch(addDrugForm.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Always update the CSRF values
                updateCsrfTokens(data.csrf_name, data.csrf_hash);

                if (data.status === 'success') {
                    showAlert('success', data.message);
                    addDrugForm.reset();

                    // Close modal
                    const modalEl = document.getElementById('addDrugModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) {
                        modal.hide();
                    } else {
                        // Fallback close if modal instance isn't fetched
                        const bootstrapModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        bootstrapModal.hide();
                    }

                    // Refresh table list
                    refreshTable();
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error adding drug:', error);
                showAlert('error', 'An error occurred while attempting to save the drug.');
            });
        });
    }

    // 4. Form Submit Handler: Edit Drug
    const editDrugForm = document.getElementById('editDrugForm');
    if (editDrugForm) {
        editDrugForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(editDrugForm);

            // Fetch request
            fetch(editDrugForm.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Always update the CSRF values
                updateCsrfTokens(data.csrf_name, data.csrf_hash);

                if (data.status === 'success') {
                    showAlert('success', data.message);

                    // Close modal
                    const modalEl = document.getElementById('editDrugModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) {
                        modal.hide();
                    } else {
                        const bootstrapModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        bootstrapModal.hide();
                    }

                    // Refresh table list
                    refreshTable();
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error updating drug:', error);
                showAlert('error', 'An error occurred while attempting to update drug details.');
            });
        });
    }

    // 5. AJAX Call: Deactivate Drug (Soft-Delete)
    function deactivateDrug(id) {
        const formData = new FormData();
        formData.append(window.csrfName, window.csrfHash);

        fetch(`<?php echo base_url('drug-entry/deactivate/'); ?>${id}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Always update CSRF
            updateCsrfTokens(data.csrf_name, data.csrf_hash);

            if (data.status === 'success') {
                showAlert('success', data.message);
                refreshTable();
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error deactivating drug:', error);
            showAlert('error', 'An error occurred while attempting to deactivate the drug.');
        });
    }

    // 5b. AJAX Call: Activate Drug
    function activateDrug(id) {
        const formData = new FormData();
        formData.append(window.csrfName, window.csrfHash);

        fetch(`<?php echo base_url('drug-entry/activate/'); ?>${id}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Always update CSRF
            updateCsrfTokens(data.csrf_name, data.csrf_hash);

            if (data.status === 'success') {
                showAlert('success', data.message);
                refreshTable();
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error activating drug:', error);
            showAlert('error', 'An error occurred while attempting to activate the drug.');
        });
    }

    // 5c. AJAX Call: Delete Drug (Hard-Delete)
    function deleteDrug(id) {
        const formData = new FormData();
        formData.append(window.csrfName, window.csrfHash);

        fetch(`<?php echo base_url('drug-entry/delete/'); ?>${id}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Always update CSRF
            updateCsrfTokens(data.csrf_name, data.csrf_hash);

            if (data.status === 'success') {
                showAlert('success', data.message);
                refreshTable();
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error deleting drug:', error);
            showAlert('error', 'An error occurred while attempting to delete the drug.');
        });
    }

    // 6. Refresh Table Content (via partial HTML loading)
    function refreshTable() {
        fetch('<?php echo base_url("drug-entry"); ?>')
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTableBody = doc.getElementById('drugsTableBody');
                if (newTableBody && drugsTableBody) {
                    drugsTableBody.innerHTML = newTableBody.innerHTML;
                    
                    // Re-apply filter query if user has typed something
                    if (tableSearchInput.value !== '') {
                        filterTable();
                    }
                }
            })
            .catch(error => {
                console.error('Error reloading drug list:', error);
            });
    }

    // 7. CSRF Token Sync Utility
    function updateCsrfTokens(name, hash) {
        window.csrfName = name;
        window.csrfHash = hash;

        // Find all hidden input fields with name matching csrfName and update their values
        const csrfInputs = document.querySelectorAll(`input[name="${name}"]`);
        csrfInputs.forEach(input => {
            input.value = hash;
        });
    }

    // 8. Custom Toast Alert Spawner using SweetAlert2
    function showAlert(type, message) {
        const isSuccess = type === 'success';
        
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        Toast.fire({
            icon: isSuccess ? 'success' : 'error',
            title: message
        });
    }

    // HTML Escaper for Safety
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
