<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Welcome & Overview Banner -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 rounded-4 shadow-sm text-white" style="background: linear-gradient(135deg, #0d9488 0%, #0f172a 100%);">
            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <span class="badge mb-3 px-3 py-2 fw-semibold" style="background-color: rgba(204, 251, 241, 0.2); color: #ccfbf1;">Decision Support Active</span>
                        <h2 class="fw-bold mb-2">Welcome back, <?php echo html_escape($this->session->userdata('name')); ?></h2>
                        <p class="mb-0 text-white-50 fs-5">Evaluate combinations, detect adverse drug events, and ensure clinical safety from one dashboard.</p>
                    </div>
                    <div class="col-md-4 text-md-end d-none d-md-block">
                        <i class="bi bi-shield-check display-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-4 mb-4">
    <!-- Card 1: Total Prescriptions Checked -->
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-semibold uppercase tracking-wider">Total Checked</span>
                    <h3 class="fw-bold mb-0 mt-1"><?php echo number_format($stats['total_prescriptions']); ?></h3>
                </div>
                <div class="p-3 rounded-3" style="background-color: #f0fdfa;">
                    <i class="bi bi-file-earmark-medical fs-3" style="color: #0d9488;"></i>
                </div>
            </div>
            <div class="mt-3 text-success small">
                <i class="bi bi-arrow-up-right me-1"></i>
                <span>All time volume</span>
            </div>
        </div>
    </div>

    <!-- Card 2: Total Interactions Found -->
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-semibold">Interactions Found</span>
                    <h3 class="fw-bold mb-0 mt-1"><?php echo number_format($stats['total_interactions']); ?></h3>
                </div>
                <div class="p-3 rounded-3" style="background-color: #fef3c7;">
                    <i class="bi bi-lightning-charge fs-3" style="color: #d97706;"></i>
                </div>
            </div>
            <div class="mt-3 text-secondary small">
                <span>Rate: <strong><?php echo number_format(($stats['total_interactions'] / $stats['total_prescriptions']) * 100, 1); ?>%</strong> of checks</span>
            </div>
        </div>
    </div>

    <!-- Card 3: Severe Alerts -->
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-semibold">Severe Alerts</span>
                    <h3 class="fw-bold mb-0 mt-1 text-danger"><?php echo number_format($stats['severe_alerts']); ?></h3>
                </div>
                <div class="p-3 rounded-3" style="background-color: #fee2e2;">
                    <i class="bi bi-exclamation-octagon fs-3 text-danger"></i>
                </div>
            </div>
            <div class="mt-3 text-danger small fw-semibold">
                <i class="bi bi-shield-fill-x me-1"></i>
                <span>Action required immediately</span>
            </div>
        </div>
    </div>

    <!-- Card 4: Moderate Alerts -->
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-semibold">Moderate Alerts</span>
                    <h3 class="fw-bold mb-0 mt-1" style="color: #ea580c;"><?php echo number_format($stats['moderate_alerts']); ?></h3>
                </div>
                <div class="p-3 rounded-3" style="background-color: #ffedd5;">
                    <i class="bi bi-exclamation-triangle fs-3" style="color: #ea580c;"></i>
                </div>
            </div>
            <div class="mt-3 text-secondary small">
                <span>Requires clinical review</span>
            </div>
        </div>
    </div>
</div>

<!-- Recent Checks Table -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2" style="color: #0d9488;"></i>Recent Prescriptions Checked</h5>
                <a href="#" class="btn btn-teal btn-sm rounded-pill px-3">
                    <i class="bi bi-plus-lg me-1"></i> New Check
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Patient Name</th>
                                <th># Drugs</th>
                                <th># Interactions Found</th>
                                <th>Max Severity</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_prescriptions as $row): ?>
                                <tr>
                                    <td class="ps-4 text-secondary small"><?php echo html_escape($row['date']); ?></td>
                                    <td class="fw-semibold text-dark"><?php echo html_escape($row['patient_name']); ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1"><?php echo html_escape($row['drugs_count']); ?> drugs</span>
                                    </td>
                                    <td>
                                        <?php if ($row['interactions_count'] > 0): ?>
                                            <span class="fw-bold text-dark"><i class="bi bi-lightning-fill text-warning me-1"></i><?php echo html_escape($row['interactions_count']); ?> interactions</span>
                                        <?php else: ?>
                                            <span class="text-muted"><i class="bi bi-check2-circle text-success me-1"></i>0</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $sev = $row['severity'];
                                        if ($sev === 'Severe') {
                                            echo '<span class="badge bg-danger px-3 py-1.5 rounded-pill"><i class="bi bi-shield-fill-x me-1"></i>Severe</span>';
                                        } elseif ($sev === 'Moderate') {
                                            echo '<span class="badge px-3 py-1.5 rounded-pill text-white" style="background-color: #ea580c;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Moderate</span>';
                                        } elseif ($sev === 'Mild') {
                                            echo '<span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill"><i class="bi bi-exclamation-circle-fill me-1"></i>Mild</span>';
                                        } else {
                                            echo '<span class="badge bg-success-subtle text-success px-3 py-1.5 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>None</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="alert('View prescription details for <?php echo html_escape($row['patient_name']); ?>')">
                                            <i class="bi bi-eye me-1"></i> View
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
