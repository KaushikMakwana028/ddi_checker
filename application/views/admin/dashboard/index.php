<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="dashboard-page">
    <!-- Welcome & Overview Banner -->
    <div class="welcome-banner mb-4">
        <div class="banner-content">
            <span class="eyebrow-chip">
                <i class="bi bi-shield-lock-fill"></i> Admin Management Console
            </span>
            <h2 class="banner-title">Welcome, <?php echo html_escape($this->session->userdata('name') ?: 'Administrator'); ?></h2>
            <p class="banner-desc">Manage clinical drug registries, evaluate adverse interaction models, and monitor system health.</p>
        </div>
        <div class="banner-icon d-none d-md-flex">
            <i class="bi bi-shield-shaded"></i>
        </div>
    </div>

    <!-- Stat Cards Grid -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Registered Drugs -->
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-teal h-100">
                <div class="stat-main">
                    <div class="stat-icon-wrap">
                        <i class="bi bi-capsule"></i>
                    </div>
                    <div class="stat-meta">
                        <span class="stat-label">Registered Drugs</span>
                        <h3 class="stat-value"><?php echo number_format($stats['total_drugs']); ?></h3>
                    </div>
                </div>
                <div class="stat-footer">
                    <span>Active: <strong><?php echo number_format($stats['active_drugs']); ?></strong></span>
                    <a href="<?php echo base_url('admin/drug-entry'); ?>" class="stat-link">Manage <i class="bi bi-chevron-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Card 2: Interaction Rules -->
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-blue h-100">
                <div class="stat-main">
                    <div class="stat-icon-wrap">
                        <i class="bi bi-shield-exclamation"></i>
                    </div>
                    <div class="stat-meta">
                        <span class="stat-label">Interaction Rules</span>
                        <h3 class="stat-value"><?php echo number_format($stats['total_interactions']); ?></h3>
                    </div>
                </div>
                <div class="stat-footer">
                    <span>Active: <strong><?php echo number_format($stats['active_interactions']); ?></strong></span>
                    <a href="<?php echo base_url('admin/interactions'); ?>" class="stat-link">View Rules <i class="bi bi-chevron-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Card 3: Severe Alerts -->
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-severe h-100">
                <div class="stat-main">
                    <div class="stat-icon-wrap">
                        <i class="bi bi-exclamation-octagon"></i>
                    </div>
                    <div class="stat-meta">
                        <span class="stat-label">Severe Alerts</span>
                        <h3 class="stat-value text-danger"><?php echo number_format($stats['severe_alerts']); ?></h3>
                    </div>
                </div>
                <div class="stat-footer text-danger">
                    <span><i class="bi bi-shield-fill-x me-1"></i>Contraindicated</span>
                    <a href="<?php echo base_url('admin/interactions?severity=Severe'); ?>" class="stat-link text-danger">Filter <i class="bi bi-chevron-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Card 4: Registered Doctors -->
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-purple h-100">
                <div class="stat-main">
                    <div class="stat-icon-wrap">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-meta">
                        <span class="stat-label">Doctors</span>
                        <h3 class="stat-value"><?php echo number_format($stats['total_doctors']); ?></h3>
                    </div>
                </div>
                <div class="stat-footer">
                    <span>Active: <strong><?php echo number_format($stats['active_doctors']); ?></strong></span>
                    <a href="<?php echo base_url('admin/doctors'); ?>" class="stat-link">Manage <i class="bi bi-chevron-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Clinical Interaction Rules -->
    <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom py-3.5 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-teal"></i>
                <span>Recent Interaction Rules Registry</span>
            </h5>
            <div class="d-flex gap-2">
                <a href="<?php echo base_url('admin/interactions'); ?>" class="btn-ghost-sm">
                    <i class="bi bi-list-check"></i> View All Rules
                </a>
                <a href="<?php echo base_url('admin/drug-entry'); ?>" class="btn-primary-sm">
                    <i class="bi bi-capsule"></i> Manage Drugs
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 dashboard-table">
                    <thead>
                        <tr>
                            <th style="min-width: 140px; width: 16%;">Drug A (Primary)</th>
                            <th style="min-width: 140px; width: 16%;">Drug B (Secondary)</th>
                            <th style="min-width: 110px; width: 12%;">Severity Level</th>
                            <th style="min-width: 240px; width: 34%;">Clinical Remarks</th>
                            <th style="min-width: 140px; width: 14%;">Source Citation</th>
                            <th class="text-end" style="min-width: 90px; width: 8%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_interactions)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-shield-slash fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                    <p class="mb-2 fw-medium">No Interaction Rules Added Yet</p>
                                    <a href="<?php echo base_url('admin/interactions'); ?>" class="btn btn-sm btn-teal rounded-pill px-3">
                                        <i class="bi bi-plus-lg me-1"></i> Add Interaction Rule
                                    </a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_interactions as $row): ?>
                                <?php $sev = strtolower($row['severity']); ?>
                                <tr>
                                    <td>
                                        <span class="drug-chip"><i class="bi bi-capsule"></i><?php echo html_escape($row['drug_a_name']); ?></span>
                                    </td>
                                    <td>
                                        <span class="drug-chip drug-chip-b"><i class="bi bi-capsule"></i><?php echo html_escape($row['drug_b_name']); ?></span>
                                    </td>
                                    <td>
                                        <span class="sev-badge sev-<?php echo $sev; ?>">
                                            <?php echo html_escape($row['severity']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-secondary small line-clamp-2">
                                            <?php echo html_escape($row['remarks'] ?: 'No clinical remarks recorded.'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="source-chip">
                                            <i class="bi bi-journal-text"></i> <?php echo html_escape($row['source'] ?: 'Standard Formulary'); ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($row['is_active']): ?>
                                            <span class="status-badge status-active"><i class="bi bi-dot"></i>Active</span>
                                        <?php else: ?>
                                            <span class="status-badge status-inactive"><i class="bi bi-dot"></i>Inactive</span>
                                        <?php endif; ?>
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

<style>
    .dashboard-page {
        font-family: 'Poppins', sans-serif;
    }

    .welcome-banner {
        background: linear-gradient(135deg, #0f766e 0%, #0f172a 100%);
        border-radius: 20px;
        padding: 32px 36px;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        box-shadow: 0 10px 25px -5px rgba(15, 118, 110, 0.25);
    }

    .eyebrow-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(204, 251, 241, 0.2);
        color: #ccfbf1;
        border: 1px solid rgba(204, 251, 241, 0.3);
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 600;
        letter-spacing: .05em;
        text-transform: uppercase;
        padding: 4px 12px;
        margin-bottom: 12px;
    }

    .banner-title {
        font-size: 24px;
        font-weight: 700;
        margin: 0 0 6px;
        line-height: 1.25;
    }

    .banner-desc {
        color: rgba(241, 245, 249, 0.85);
        font-size: 14px;
        margin: 0;
        max-width: 580px;
        line-height: 1.5;
    }

    .banner-icon i {
        font-size: 72px;
        color: rgba(255, 255, 255, 0.2);
    }

    /* Stat Cards */
    .stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 18px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
    }

    .stat-main {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .stat-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
        flex-shrink: 0;
    }

    .stat-teal .stat-icon-wrap { background: #f0fdfa; color: #0f766e; }
    .stat-blue .stat-icon-wrap { background: #f1f5f9; color: #0284c7; }
    .stat-severe .stat-icon-wrap { background: #fef2f2; color: #dc2626; }
    .stat-purple .stat-icon-wrap { background: #f5f3ff; color: #7c3aed; }

    .stat-meta {
        min-width: 0;
    }

    .stat-label {
        font-size: 12px;
        font-weight: 500;
        color: #64748b;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .stat-value {
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        line-height: 1.15;
    }

    .stat-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 12px;
        color: #64748b;
        border-top: 1px solid #f1f5f9;
        padding-top: 10px;
    }

    .stat-link {
        color: #0f766e;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        transition: gap .15s ease;
    }

    .stat-link:hover {
        gap: 6px;
        color: #0d9488;
    }

    /* Table Styles */
    .dashboard-table thead th {
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

    .dashboard-table tbody td {
        padding: 13px 18px;
        vertical-align: middle;
        font-size: 13px;
        border-bottom: 1px solid #f1f5f9;
    }

    .drug-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        font-size: 13px;
        color: #0f172a;
        white-space: nowrap;
    }

    .drug-chip i {
        color: #0f766e;
        font-size: 12px;
        background: #f0fdfa;
        border: 1px solid #99f6e4;
        width: 24px;
        height: 24px;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .drug-chip-b i {
        color: #475569;
        background: #f1f5f9;
        border-color: #e2e8f0;
    }

    .sev-badge {
        font-size: 11.5px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .sev-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .sev-badge.sev-severe { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .sev-badge.sev-moderate { background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; }
    .sev-badge.sev-mild { background: #fefce8; color: #b45309; border: 1px solid #fde68a; }

    .source-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11.5px;
        color: #64748b;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: 3px 9px;
        max-width: 180px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        font-size: 11.5px;
        font-weight: 600;
        padding: 3px 9px 3px 5px;
        border-radius: 999px;
        white-space: nowrap;
    }

    .status-badge i {
        font-size: 18px;
        line-height: 0;
    }

    .status-active { background: #ecfdf5; color: #059669; }
    .status-inactive { background: #fef2f2; color: #dc2626; }

    .btn-primary-sm,
    .btn-ghost-sm {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 10px;
        padding: 7px 14px;
        border: 1px solid transparent;
        text-decoration: none;
        transition: all .15s ease;
        white-space: nowrap;
    }

    .btn-primary-sm {
        background: #0f766e;
        color: #ffffff;
    }
    .btn-primary-sm:hover {
        background: #0d9488;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .btn-ghost-sm {
        background: #ffffff;
        color: #0f172a;
        border-color: #e2e8f0;
    }
    .btn-ghost-sm:hover {
        border-color: #0f766e;
        color: #0f766e;
        background: #f0fdfa;
    }

    @media (max-width: 767px) {
        .welcome-banner {
            padding: 20px;
            border-radius: 16px;
        }

        .banner-title {
            font-size: 19px;
        }

        .banner-desc {
            font-size: 13px;
        }

        .stat-card {
            padding: 12px 14px;
        }

        .stat-icon-wrap {
            width: 36px;
            height: 36px;
            font-size: 16px;
            border-radius: 10px;
        }

        .stat-value {
            font-size: 18px;
        }

        .stat-footer {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
            padding-top: 8px;
        }
    }
</style>
