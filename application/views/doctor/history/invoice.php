<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="doctor-invoice-view py-4">
    <!-- Breadcrumbs / Back navigation -->
    <div class="mb-4 print-hide d-flex justify-content-between align-items-center flex-wrap gap-2">
        <a href="<?php echo base_url('doctor/history'); ?>" class="btn btn-outline-teal d-inline-flex align-items-center gap-1.5 fw-semibold px-3 py-2 rounded-3">
            <i class="bi bi-arrow-left"></i> Back to History
        </a>
        <button type="button" class="btn btn-teal d-inline-flex align-items-center gap-1.5 fw-semibold px-4 py-2 rounded-3 shadow-sm" onclick="window.print()">
            <i class="bi bi-printer"></i> Print Prescription
        </button>
    </div>

    <!-- Active Invoice Container -->
    <div class="invoice-outer mb-4">
        <div class="invoice-card p-4 bg-white rounded-4 shadow-sm border" id="prescription_invoice_card">
            <!-- Invoice Document Wrapper (Targeted by printing) -->
            <div class="invoice-print-container" id="print_area">
                <!-- Header block -->
                <div class="row align-items-center mb-4 pb-3 border-bottom invoice-header-row">
                    <div class="col-12 col-md-4 header-col-brand">
                        <h4 class="invoice-brand mb-1 text-teal fw-bold d-flex align-items-center gap-2" style="color: #0f766e;">
                            <i class="bi bi-heart-pulse-fill"></i> DDI Checker
                        </h4>
                        <small class="text-secondary small">Clinical Decision Support Portal</small>
                    </div>
                    <div class="col-12 col-md-4 text-center header-col-middle">
                        <h5 class="mb-1 text-dark fw-bold" style="font-size: 1rem; color: #0f766e !important;"><?php echo html_escape($invoice['doctor']['hospital_clinic']); ?></h5>
                        <p class="text-secondary mb-0 small" style="font-size: 0.75rem; line-height: 1.3;"><?php echo nl2br(html_escape($invoice['doctor']['address'])); ?></p>
                    </div>
                    <div class="col-12 col-md-4 text-end header-col-label">
                        <h4 class="invoice-label mb-1 fw-bold text-dark" style="letter-spacing: 0.05em; font-size: 1.25rem;">PRESCRIPTION</h4>
                        <div class="text-secondary small"><strong>Invoice No:</strong> <span class="text-dark fw-bold"><?php echo html_escape($invoice['invoice_number']); ?></span></div>
                        <div class="text-secondary small"><strong>Date:</strong> <span class="text-dark"><?php echo html_escape($invoice['visit_date']); ?></span></div>
                    </div>
                </div>

                <!-- Doctor and Patient Block -->
                <div class="row mb-4 doctor-patient-row">
                    <div class="col-6 border-end details-column">
                        <h6 class="fw-bold text-teal mb-2 d-flex align-items-center gap-1.5" style="color: #0f766e; font-size: 0.85rem;">
                            <i class="bi bi-person-workspace"></i> Practitioner Details
                        </h6>
                        <div class="fw-bold text-dark fs-6"><?php echo html_escape($invoice['doctor']['name']); ?></div>
                        <div class="text-secondary small mb-0.5"><?php echo html_escape($invoice['doctor']['qualification']); ?> — <?php echo html_escape($invoice['doctor']['specialization']); ?></div>
                        <div class="text-secondary small mb-0.5"><strong>Reg No:</strong> <?php echo html_escape($invoice['doctor']['registration_number']); ?></div>
                        <div class="text-secondary small"><?php echo html_escape($invoice['doctor']['hospital_clinic']); ?></div>
                    </div>
                    <div class="col-6 ps-4 details-column">
                        <h6 class="fw-bold text-teal mb-2 d-flex align-items-center gap-1.5" style="color: #0f766e; font-size: 0.85rem;">
                            <i class="bi bi-person-circle"></i> Patient Details
                        </h6>
                        <div class="fw-bold text-dark fs-6"><?php echo html_escape($invoice['patient']['full_name']); ?></div>
                        <div class="text-secondary small mb-0.5"><strong>Age / Gender:</strong> <?php echo html_escape($invoice['patient']['age']); ?> Years / <?php echo html_escape($invoice['patient']['gender']); ?></div>
                        <div class="text-secondary small"><strong>Contact:</strong> <?php echo html_escape($invoice['patient']['contact_number'] ?: '—'); ?></div>
                    </div>
                </div>

                <!-- Medications Table -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped align-middle invoice-table mb-0">
                        <thead class="table-light text-secondary small fw-bold">
                            <tr>
                                <th style="width: 5%;" class="text-center">#</th>
                                <th style="width: 35%;">Medicine Name</th>
                                <th style="width: 15%;">Dosage</th>
                                <th style="width: 20%;">Frequency</th>
                                <th style="width: 12%;">Duration</th>
                                <th style="width: 13%;">Instructions</th>
                            </tr>
                        </thead>
                        <tbody class="text-dark small">
                            <?php if (!empty($invoice['items'])): ?>
                                <?php foreach ($invoice['items'] as $index => $item): ?>
                                    <tr>
                                        <td class="text-center"><?php echo $index + 1; ?></td>
                                        <td class="fw-semibold"><?php echo html_escape($item['drug_name']); ?></td>
                                        <td><?php echo html_escape($item['dosage']); ?></td>
                                        <td><?php echo html_escape($item['frequency']); ?></td>
                                        <td><?php echo html_escape($item['duration']); ?></td>
                                        <td><?php echo html_escape($item['special_instructions'] ?: '—'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">No medicines prescribed in this session.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Signatures -->
                <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mt-4 pt-3 border-top signatures-row">
                    <div>
                        <p class="text-secondary mb-0" style="font-size: 0.72rem; line-height: 1.45;">
                            <strong>Disclaimer:</strong> Generated by DDI Checker Clinical Decision Portal.<br>
                            This is an official medical prescription. For clinical use.
                        </p>
                    </div>
                    <div class="text-end pe-2 ms-auto signature-wrapper">
                        <?php if (!empty($invoice['doctor']['signature'])): ?>
                            <div class="mb-1 text-center" style="max-height: 55px; overflow: hidden;">
                                <img src="<?php echo html_escape($invoice['doctor']['signature']); ?>" alt="Doctor Signature" style="max-height: 50px; max-width: 160px; object-fit: contain;">
                            </div>
                        <?php else: ?>
                            <div style="height: 50px;"></div>
                        <?php endif; ?>
                        <div class="text-dark small" style="border-top: 1.5px solid #94a3b8; width: 180px; padding-top: 6px; font-weight: 500;">
                            Physician Signature
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling elements to align with other page brand values */
    .btn-teal {
        background-color: #0f766e;
        border-color: #0f766e;
        color: #ffffff;
    }
    .btn-teal:hover, .btn-teal:focus {
        background-color: #0d9488;
        border-color: #0d9488;
        color: #ffffff;
    }
    .btn-outline-teal {
        color: #0f766e;
        border-color: #0f766e;
    }
    .btn-outline-teal:hover {
        background-color: #0f766e;
        border-color: #0f766e;
        color: #ffffff;
    }

    /* Print styling rules */
    @media print {
        body {
            background-color: #ffffff !important;
            color: #000000 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Hide all workspace wrappers, menus, headers, footers, and page structures */
        header, footer, .sidebar, .page-header, .page-footer, .welcome-banner, .print-hide, .btn {
            display: none !important;
        }

        body, .doctor-invoice-view, .invoice-outer {
            display: block !important;
            position: static !important;
            overflow: visible !important;
            height: auto !important;
            min-height: 0 !important;
            max-height: none !important;
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
        }

        .invoice-card {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            background: transparent !important;
        }

        /* Enforce columns display side-by-side */
        .invoice-header-row, .doctor-patient-row, .signatures-row {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            margin-bottom: 1rem !important;
        }

        .details-column {
            width: 50% !important;
            flex: 0 0 50% !important;
            max-width: 50% !important;
        }

        .invoice-header-row .header-col-brand,
        .invoice-header-row .header-col-label {
            width: 35% !important;
            flex: 0 0 35% !important;
            max-width: 35% !important;
        }

        .invoice-header-row .header-col-middle {
            width: 30% !important;
            flex: 0 0 30% !important;
            max-width: 30% !important;
        }

        .signature-wrapper {
            margin-left: auto !important;
        }

        .table-responsive {
            overflow: visible !important;
        }
    }
</style>
