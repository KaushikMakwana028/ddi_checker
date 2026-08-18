<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="form-page-container">
    <!-- Page Header & Navigation -->
    <div class="page-header mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="<?php echo base_url('admin/interactions'); ?>" class="btn-back" title="Back to Interaction Rules">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <div class="page-eyebrow">
                    <i class="bi bi-pencil-square"></i> Clinical Safety Model
                </div>
                <h2 class="page-title">Edit Interaction Rule</h2>
                <p class="page-subtitle">Update clinical remarks, severity grade, or scientific citations for this medicine pair.</p>
            </div>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="card border-0 rounded-4 shadow-sm form-card">
        <div class="card-header bg-white border-bottom py-3.5 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div class="header-icon-box">
                    <i class="bi bi-shield-exclamation"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">
                        <?php echo html_escape($rule['drug_a_name'] ?? 'Drug A'); ?> + <?php echo html_escape($rule['drug_b_name'] ?? 'Drug B'); ?>
                    </h5>
                    <small class="text-muted">Rule ID: #<?php echo $rule['id']; ?> • Created <?php echo date('M d, Y', strtotime($rule['created_at'])); ?></small>
                </div>
            </div>
            <?php if ($rule['is_active']): ?>
                <span class="status-badge status-active"><i class="bi bi-dot"></i>Active Rule</span>
            <?php else: ?>
                <span class="status-badge status-inactive"><i class="bi bi-dot"></i>Inactive Rule</span>
            <?php endif; ?>
        </div>

        <div class="card-body p-4 p-md-5">
            <?php echo form_open('admin/interactions/edit/' . $rule['id'], ['id' => 'editRuleForm', 'autocomplete' => 'off']); ?>
                <div class="row g-4 mb-4">
                    <!-- Drug A -->
                    <div class="col-md-6">
                        <div class="form-field">
                            <label for="drug_a_id">Drug A (First Medicine) <span class="req">*</span></label>
                            <select id="drug_a_id" name="drug_a_id" required>
                                <option value="">Select First Drug...</option>
                                <?php foreach ($drugs as $d): ?>
                                    <?php 
                                    $qty = isset($d['quantity']) ? (int)$d['quantity'] : 0;
                                    $unit = !empty($d['unit']) ? ' ' . html_escape($d['unit']) : '';
                                    $cat = !empty($d['category']) ? ' (' . html_escape($d['category']) . ')' : '';
                                    $isOutOfStock = ($qty <= 0);
                                    ?>
                                    <option value="<?php echo $d['id']; ?>"
                                            data-qty="<?php echo $qty; ?>"
                                            data-unit="<?php echo html_escape($d['unit'] ?? ''); ?>"
                                            data-name="<?php echo html_escape($d['drug_name']); ?>"
                                            class="<?php echo $isOutOfStock ? 'opt-out-of-stock' : ''; ?>"
                                            style="<?php echo $isOutOfStock ? 'color: #dc2626; font-weight: 600;' : ''; ?>"
                                            <?php echo set_select('drug_a_id', $d['id'], ($rule['drug_a_id'] == $d['id'])); ?>>
                                        <?php echo html_escape($d['drug_name']) . $cat; ?> — <?php echo $isOutOfStock ? '[Out of Stock: 0' . $unit . ']' : 'Qty: ' . number_format($qty) . $unit; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div id="drug_a_stock_feedback" class="stock-status-feedback"></div>
                            <span class="field-hint">Select the primary prescribed chemical entity.</span>
                        </div>
                    </div>

                    <!-- Drug B -->
                    <div class="col-md-6">
                        <div class="form-field">
                            <label for="drug_b_id">Drug B (Interacting Medicine) <span class="req">*</span></label>
                            <select id="drug_b_id" name="drug_b_id" required>
                                <option value="">Select Interacting Drug...</option>
                                <?php foreach ($drugs as $d): ?>
                                    <?php 
                                    $qty = isset($d['quantity']) ? (int)$d['quantity'] : 0;
                                    $unit = !empty($d['unit']) ? ' ' . html_escape($d['unit']) : '';
                                    $cat = !empty($d['category']) ? ' (' . html_escape($d['category']) . ')' : '';
                                    $isOutOfStock = ($qty <= 0);
                                    ?>
                                    <option value="<?php echo $d['id']; ?>"
                                            data-qty="<?php echo $qty; ?>"
                                            data-unit="<?php echo html_escape($d['unit'] ?? ''); ?>"
                                            data-name="<?php echo html_escape($d['drug_name']); ?>"
                                            class="<?php echo $isOutOfStock ? 'opt-out-of-stock' : ''; ?>"
                                            style="<?php echo $isOutOfStock ? 'color: #dc2626; font-weight: 600;' : ''; ?>"
                                            <?php echo set_select('drug_b_id', $d['id'], ($rule['drug_b_id'] == $d['id'])); ?>>
                                        <?php echo html_escape($d['drug_name']) . $cat; ?> — <?php echo $isOutOfStock ? '[Out of Stock: 0' . $unit . ']' : 'Qty: ' . number_format($qty) . $unit; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div id="drug_b_stock_feedback" class="stock-status-feedback"></div>
                            <span class="field-hint">Select the co-administered compound.</span>
                        </div>
                    </div>

                    <!-- Severity Level -->
                    <div class="col-md-6">
                        <div class="form-field">
                            <label for="severity">Clinical Severity Level <span class="req">*</span></label>
                            <select id="severity" name="severity" required>
                                <option value="">Select Severity Level...</option>
                                <option value="Severe" <?php echo set_select('severity', 'Severe', ($rule['severity'] === 'Severe')); ?>>Severe (Contraindicated / Major Risk)</option>
                                <option value="MAJOR" <?php echo set_select('severity', 'MAJOR', ($rule['severity'] === 'MAJOR')); ?>>MAJOR</option>
                                <option value="Moderate" <?php echo set_select('severity', 'Moderate', ($rule['severity'] === 'Moderate')); ?>>Moderate (Adjust Dose / Close Monitoring)</option>
                                <option value="Mild" <?php echo set_select('severity', 'Mild', ($rule['severity'] === 'Mild')); ?>>Mild (Minor Significance / Low Risk)</option>
                                <option value="Not known interaction found" <?php echo set_select('severity', 'Not known interaction found', ($rule['severity'] === 'Not known interaction found')); ?>>Not known interaction found</option>
                            </select>
                            <span class="field-hint">Clinical risk classification tier.</span>
                        </div>
                    </div>

                    <!-- Source Citation -->
                    <div class="col-md-6">
                        <div class="form-field">
                            <label for="source">Source Citation / Reference</label>
                            <div class="input-icon">
                                <i class="bi bi-journal-text"></i>
                                <input type="text" id="source" name="source" value="<?php echo set_value('source', $rule['source']); ?>" placeholder="e.g. FDA Label, Stockley's, BNF 84">
                            </div>
                            <span class="field-hint">Medical formulary, compendium, or published literature source.</span>
                        </div>
                    </div>

                    <!-- Clinical Remarks -->
                    <div class="col-12">
                        <div class="form-field">
                            <label for="remarks">Clinical Remarks &amp; Pharmacological Mechanism <span class="req">*</span></label>
                            <textarea id="remarks" name="remarks" rows="4" required placeholder="Describe adverse mechanism, risk of toxicity, and clinical guidance..."><?php echo set_value('remarks', $rule['remarks']); ?></textarea>
                            <span class="field-hint">Detailed guidance evaluated by clinical decision algorithms and displayed to prescribing doctors.</span>
                        </div>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="form-actions mt-5 pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <a href="<?php echo base_url('admin/interactions'); ?>" class="btn-page-ghost">
                        <i class="bi bi-arrow-left"></i> <span>Cancel &amp; Return</span>
                    </a>
                    <button type="submit" class="btn-page-primary">
                        <i class="bi bi-check-lg"></i> <span>Save &amp; Update Rule</span>
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
        transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease !important;
    }

    .form-field input[type="text"]:focus,
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

    /* Out of Stock Select & Feedback Styling */
    select.is-out-of-stock {
        border-color: #dc2626 !important;
        background-color: #fef2f2 !important;
        color: #991b1b !important;
    }

    select.is-out-of-stock:focus {
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2) !important;
    }

    .opt-out-of-stock {
        color: #dc2626 !important;
        font-weight: 600 !important;
        background-color: #fef2f2 !important;
    }

    .stock-status-feedback {
        margin-top: 6px;
    }

    .out-of-stock-alert {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12.5px;
        font-weight: 600;
        color: #dc2626;
        background: #fef2f2;
        border: 1px solid #fecaca;
        padding: 5px 12px;
        border-radius: 8px;
    }

    .in-stock-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12.5px;
        font-weight: 500;
        color: #0f766e;
        background: #f0fdfa;
        border: 1px solid #99f6e4;
        padding: 4px 10px;
        border-radius: 8px;
    }

    .form-field textarea {
        resize: vertical;
        min-height: 100px;
        line-height: 1.6;
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const drugASelect = document.getElementById('drug_a_id');
    const drugBSelect = document.getElementById('drug_b_id');
    const drugAFeedback = document.getElementById('drug_a_stock_feedback');
    const drugBFeedback = document.getElementById('drug_b_stock_feedback');

    function checkStock(selectElem, feedbackElem) {
        if (!selectElem || !feedbackElem) return;
        const selectedOpt = selectElem.options[selectElem.selectedIndex];
        if (!selectedOpt || !selectedOpt.value) {
            feedbackElem.innerHTML = '';
            selectElem.classList.remove('is-out-of-stock');
            return;
        }

        const qty = parseInt(selectedOpt.dataset.qty || '0', 10);
        const unit = selectedOpt.dataset.unit ? ' ' + selectedOpt.dataset.unit : '';
        const name = selectedOpt.dataset.name || 'Medicine';

        if (qty <= 0) {
            selectElem.classList.add('is-out-of-stock');
            feedbackElem.innerHTML = `
                <div class="out-of-stock-alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span><strong>Out of Stock:</strong> 0${unit} available in pharmacy inventory.</span>
                </div>
            `;
        } else {
            selectElem.classList.remove('is-out-of-stock');
            feedbackElem.innerHTML = `
                <div class="in-stock-badge">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Available Stock: <strong>${qty.toLocaleString()}${unit}</strong></span>
                </div>
            `;
        }
    }

    if (drugASelect) {
        drugASelect.addEventListener('change', function() {
            checkStock(drugASelect, drugAFeedback);
        });
        checkStock(drugASelect, drugAFeedback);
    }

    if (drugBSelect) {
        drugBSelect.addEventListener('change', function() {
            checkStock(drugBSelect, drugBFeedback);
        });
        checkStock(drugBSelect, drugBFeedback);
    }
});
</script>
