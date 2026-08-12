<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="form-page-container">
    <!-- Page Header & Navigation -->
    <div class="page-header mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="<?php echo base_url('admin/drug-entry'); ?>" class="btn-back" title="Back to Drug Registry">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <div class="page-eyebrow">
                    <i class="bi bi-pencil-square"></i> Update Registry
                </div>
                <h2 class="page-title">Edit Drug Details</h2>
                <p class="page-subtitle">Update formulation, therapeutic classification, or stock quantity for <strong><?php echo html_escape($drug->drug_name); ?></strong>.</p>
            </div>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="card border-0 rounded-4 shadow-sm form-card">
        <div class="card-header bg-white border-bottom py-3.5 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div class="header-icon-box">
                    <i class="bi bi-capsule"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Formulary Specifications</h5>
                    <small class="text-muted">Drug ID: #<?php echo $drug->id; ?> • Added <?php echo date('M d, Y', strtotime($drug->created_at)); ?></small>
                </div>
            </div>
            <?php if ($drug->is_active): ?>
                <span class="status-badge status-active"><i class="bi bi-dot"></i>Active in Formulary</span>
            <?php else: ?>
                <span class="status-badge status-inactive"><i class="bi bi-dot"></i>Inactive / Deactivated</span>
            <?php endif; ?>
        </div>

        <div class="card-body p-4 p-md-5">
            <?php echo form_open('admin/drug-entry/edit/' . $drug->id, ['id' => 'editDrugForm', 'autocomplete' => 'off']); ?>
                <div class="row g-4">
                    <!-- Drug Name -->
                    <div class="col-md-6">
                        <div class="form-field">
                            <label for="drug_name">Drug Generic / Non-Proprietary Name <span class="req">*</span></label>
                            <div class="input-icon">
                                <i class="bi bi-capsule"></i>
                                <input type="text" id="drug_name" name="drug_name" value="<?php echo set_value('drug_name', $drug->drug_name); ?>" required placeholder="e.g. Ibuprofen, Warfarin">
                            </div>
                            <span class="field-hint">Primary non-proprietary chemical name.</span>
                        </div>
                    </div>

                    <!-- Therapeutic Category -->
                    <div class="col-md-6">
                        <div class="form-field">
                            <label for="category">Therapeutic Category</label>
                            <div class="input-icon">
                                <i class="bi bi-tag"></i>
                                <input type="text" id="category" name="category" value="<?php echo set_value('category', $drug->category); ?>" placeholder="e.g. NSAID, Anticoagulant">
                            </div>
                            <span class="field-hint">Classification based on pharmacological action.</span>
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div class="col-md-6">
                        <div class="form-field">
                            <label for="quantity">Stock Quantity <span class="req">*</span></label>
                            <div class="input-icon">
                                <i class="bi bi-123"></i>
                                <input type="number" id="quantity" name="quantity" min="0" value="<?php echo set_value('quantity', $drug->quantity); ?>" required placeholder="e.g. 100">
                            </div>
                            <span class="field-hint">Available on-hand clinical stock units.</span>
                        </div>
                    </div>

                    <!-- Unit -->
                    <div class="col-md-6">
                        <div class="form-field">
                            <label for="unit">Dosage / Packaging Unit</label>
                            <select id="unit" name="unit">
                                <option value="">Select Unit Type</option>
                                <option value="Tablets" <?php echo set_select('unit', 'Tablets', $drug->unit === 'Tablets'); ?>>Tablets</option>
                                <option value="Capsules" <?php echo set_select('unit', 'Capsules', $drug->unit === 'Capsules'); ?>>Capsules</option>
                                <option value="ml" <?php echo set_select('unit', 'ml', $drug->unit === 'ml'); ?>>ml</option>
                                <option value="Vials" <?php echo set_select('unit', 'Vials', $drug->unit === 'Vials'); ?>>Vials</option>
                                <option value="Sachets" <?php echo set_select('unit', 'Sachets', $drug->unit === 'Sachets'); ?>>Sachets</option>
                            </select>
                            <span class="field-hint">Standard dispensing unit of measurement.</span>
                        </div>
                    </div>

                    <!-- Synonyms & Brands -->
                    <div class="col-12">
                        <div class="form-field">
                            <label for="synonyms">Commercial Brand Names &amp; Synonyms</label>
                            <textarea id="synonyms" name="synonyms" rows="3" placeholder="e.g. Advil, Motrin, Nurofen"><?php echo set_value('synonyms', $drug->synonyms); ?></textarea>
                            <span class="field-hint">Separate multiple trade names or chemical synonyms with commas.</span>
                        </div>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="form-actions mt-5 pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <a href="<?php echo base_url('admin/drug-entry'); ?>" class="btn-page-ghost">
                        <i class="bi bi-arrow-left"></i> <span>Cancel &amp; Return</span>
                    </a>
                    <button type="submit" class="btn-page-primary">
                        <i class="bi bi-check-lg"></i> <span>Save &amp; Update Drug</span>
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

    .field-hint {
        display: block;
        font-size: 12px;
        color: #94a3b8;
        margin-top: 6px;
    }

    .form-field input[type="text"],
    .form-field input[type="number"],
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
    .form-field input[type="number"]:focus,
    .form-field select:focus,
    .form-field textarea:focus {
        border-color: #0f766e !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(15, 118, 110, .18) !important;
    }

    .form-field select {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 16px center !important;
        background-size: 16px !important;
        padding-right: 40px !important;
    }

    .form-field textarea {
        resize: vertical;
        min-height: 90px;
        line-height: 1.5;
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
    .form-field .input-icon input[type="number"],
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
