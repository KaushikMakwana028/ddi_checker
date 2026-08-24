<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="prescription-desk-container py-4">

    <!-- Top Premium Banner -->
    <div class="welcome-banner mb-4 d-flex justify-content-between align-items-center p-4 rounded-4 shadow-sm">
        <div>
            <span class="eyebrow-chip text-uppercase d-inline-block mb-2">
                <i class="bi bi-prescription2"></i> Clinical Workspace
            </span>
            <h2 class="banner-title mb-1 fw-bold">Prescription Desk Intake</h2>
            <p class="banner-desc mb-0">Record patient intake details, log chief complaints, and prescribe medications regimen in real-time.</p>
        </div>
        <div class="banner-icon d-none d-md-flex">
            <i class="bi bi-clipboard2-pulse"></i>
        </div>
    </div>

    <!-- Rx Tab Bar -->
    <div class="card border-0 rounded-4 shadow-sm mb-4 rx-tabs-card">
        <div class="card-body p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rx-tabs-container flex-grow-1">
                    <ul class="rx-tabs-list" id="rxTabsList">
                        <!-- Populated dynamically by JS -->
                    </ul>
                </div>
                <button class="rx-new-tab-btn btn btn-teal d-flex align-items-center gap-1.5 fw-semibold flex-shrink-0" id="addNewRxBtn" onclick="addNewTab()">
                    <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">New Rx</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Dual Column Workspace Grid -->
    <div class="row g-4 mb-4 rx-workspace-row">

        <!-- Left Column: Patient Intake File -->
        <div class="col-lg-5">
            <div class="card border-0 rounded-4 shadow-sm h-100 workspace-card">
                <div class="card-header bg-white border-bottom d-flex align-items-center gap-2">
                    <span class="card-header-icon"><i class="bi bi-person-vcard"></i></span>
                    <h5 class="mb-0 fw-bold text-dark">Patient Intake File</h5>
                </div>
                <div class="card-body p-4">
                    <form id="rxPatientForm" onsubmit="handleSavePatient(event)">
                        <input type="hidden" id="patient_id" name="patient_id">

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="full_name" class="form-label fw-semibold text-dark mb-1 small-label">Patient Full Name <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-secondary"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="full_name" required placeholder="Enter full name" oninput="saveCurrentTabInputs(); saveToStorage();">
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="contact_number" class="form-label fw-semibold text-dark mb-1 small-label">Contact Number</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-secondary"><i class="bi bi-telephone"></i></span>
                                    <input type="text" class="form-control" id="contact_number" placeholder="Enter phone number">
                                </div>
                                <div id="phone-status-note" class="form-text mt-1 small"></div>
                            </div>

                            <div class="col-6">
                                <label for="age" class="form-label fw-semibold text-dark mb-1 small-label">Age (Years) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-secondary"><i class="bi bi-calendar-event"></i></span>
                                    <input type="number" class="form-control" id="age" required min="1" max="150" placeholder="Age" oninput="saveCurrentTabInputs(); saveToStorage();">
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="gender" class="form-label fw-semibold text-dark mb-1 small-label">Gender <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-secondary"><i class="bi bi-gender-ambiguous"></i></span>
                                    <select class="form-select" id="gender" required onchange="saveCurrentTabInputs(); saveToStorage();">
                                        <option value="">Select</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="height_cm" class="form-label fw-semibold text-dark mb-1 small-label">Height (cm)</label>
                                <input type="number" step="0.1" min="10" max="300" class="form-control form-control-sm" id="height_cm" placeholder="cm" oninput="saveCurrentTabInputs(); saveToStorage();">
                            </div>

                            <div class="col-6">
                                <label for="weight_kg" class="form-label fw-semibold text-dark mb-1 small-label">Weight (kg)</label>
                                <input type="number" step="0.1" min="1" max="500" class="form-control form-control-sm" id="weight_kg" placeholder="kg" oninput="saveCurrentTabInputs(); saveToStorage();">
                            </div>

                            <div class="col-12">
                                <label for="chief_complaints" class="form-label fw-semibold text-dark mb-1 small-label">Chief Complaints (C/O)</label>
                                <textarea class="form-control form-control-sm" id="chief_complaints" rows="3" placeholder="Describe symptoms or reasons for visit..." oninput="saveCurrentTabInputs(); saveToStorage();"></textarea>
                            </div>

                            <div class="col-12">
                                <label for="medical_history" class="form-label fw-semibold text-dark mb-1 small-label">Medical History (H/O)</label>
                                <textarea class="form-control form-control-sm" id="medical_history" rows="3" placeholder="Relevant history, allergies, chronic conditions..." oninput="saveCurrentTabInputs(); saveToStorage();"></textarea>
                            </div>

                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-teal w-100 w-sm-auto float-sm-end fw-semibold d-inline-flex align-items-center justify-content-center gap-1.5 shadow-sm">
                                    <i class="bi bi-check2-circle fs-5"></i>
                                    <span>Save Patient Intake</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Prescription Builder -->
        <div class="col-lg-7">
            <div class="card border-0 rounded-4 shadow-sm h-100 gated-section" id="medicine-section-container">

                <!-- Gating Locked Overlay -->
                <div class="gated-msg">
                    <div class="text-center p-4 p-md-5 rounded-4 gated-msg-box">
                        <i class="bi bi-shield-lock-fill mb-3 d-block gated-msg-icon"></i>
                        <h5 class="fw-bold text-dark mb-1">Prescription Builder Locked</h5>
                        <p class="text-secondary small mb-0">Please save the patient intake file on the left to start adding medications.</p>
                    </div>
                </div>

                <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="card-header-icon"><i class="bi bi-capsule-therapeutic"></i></span>
                        <h5 class="mb-0 fw-bold text-dark">Prescription Regimen</h5>
                    </div>
                    <span id="medicine-count-badge" class="badge rounded-pill medicine-count-badge">
                        0 medicines added
                    </span>
                </div>

                <div class="card-body p-4 medicine-content d-flex flex-column h-100">
                    <!-- Search Field and Results Dropdown -->
                    <div class="position-relative mb-4">
                        <label for="drugSearchInput" class="form-label fw-semibold text-dark mb-1 small-label">Search &amp; Add Medicine</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" id="drugSearchInput" placeholder="Type medicine name (e.g. Paracetamol)..." autocomplete="off">
                        </div>
                        <div id="drugSearchResults" class="dropdown-menu shadow-lg w-100 mt-1 drug-search-dropdown"></div>
                        <div id="duplicate-warning-note" class="text-danger fw-semibold mt-1 small" style="display: none;">
                            <i class="bi bi-exclamation-circle-fill"></i> Already added — edit the existing row instead
                        </div>
                    </div>

                    <!-- Items Table (Notion / Spreadsheet Layout, collapses to cards on mobile) -->
                    <div class="rx-table-wrapper flex-grow-1 mb-4">
                        <table class="table align-middle border-0 table-notion table-hover mb-0" id="medicationsTable">
                            <thead>
                                <tr>
                                    <th class="col-med">Medicine Name</th>
                                    <th class="col-dose">Dosage</th>
                                    <th class="col-freq">Frequency</th>
                                    <th class="col-dur">Duration</th>
                                    <th class="col-instr">Instructions</th>
                                    <th class="col-remove"></th>
                                </tr>
                            </thead>
                            <tbody id="medicationsTableBody">
                                <!-- Populated dynamically by JS -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Bottom Action Bar -->
                    <div id="prescription-actions-row" class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-auto pt-3 border-top">
                        <span class="text-muted small"><i class="bi bi-info-circle-fill text-teal"></i> Regimen automatically checks drug-drug interactions.</span>
                        <button type="button" id="savePrescriptionBtn" class="btn btn-teal fw-semibold d-inline-flex align-items-center gap-2 shadow-sm ms-auto" onclick="handleSavePrescription()">
                            <i class="bi bi-receipt fs-5"></i>
                            <span>Save &amp; Generate Invoice</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Display Area — full width so it never squeezes into a narrow column -->
    <div id="invoice-display-container" class="mb-4 invoice-outer" style="display: none;">
        <!-- Rendered dynamically by JS -->
    </div>

    <!-- Recent Patients Section -->
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2 text-dark">
                <span class="card-header-icon"><i class="bi bi-clock-history"></i></span>
                <span>Recent Patients Queue</span>
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3" id="recent-patients-list">
                <!-- Dynamically populated or rendered by PHP -->
            </div>
        </div>
    </div>
</div>

<!-- Custom Page Styling -->
<style>
    :root {
        --rx-teal-900: #0f766e;
        --rx-teal-700: #0d9488;
        --rx-teal-500: #14b8a6;
        --rx-teal-100: #e0f2f1;
        --rx-teal-050: #f0fdfa;
        --rx-ink: #1e293b;
        --rx-slate-600: #475569;
        --rx-slate-500: #64748b;
        --rx-slate-400: #94a3b8;
        --rx-slate-300: #cbd5e1;
        --rx-slate-200: #e2e8f0;
        --rx-slate-100: #f1f5f9;
        --rx-slate-050: #f8fafc;
        --rx-danger: #ef4444;
        --rx-radius: 16px;
    }

    .prescription-desk-container {
        font-family: 'Poppins', sans-serif;
        max-width: 1360px;
        margin: 0 auto;
    }

    /* ---------- Banner ---------- */
    .welcome-banner {
        background: linear-gradient(135deg, var(--rx-teal-900), var(--rx-teal-700));
        color: #ffffff;
        gap: 16px;
        flex-wrap: wrap;
    }

    .eyebrow-chip {
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.06em;
        background-color: rgba(255, 255, 255, 0.16);
        padding: 4px 10px;
        border-radius: 6px;
    }

    .banner-title {
        font-size: clamp(1.35rem, 1.05rem + 1.1vw, 1.75rem);
        line-height: 1.25;
    }

    .banner-desc {
        font-size: 0.9rem;
        opacity: 0.92;
        max-width: 560px;
    }

    .banner-icon {
        font-size: 2.6rem;
        opacity: 0.75;
    }

    /* ---------- Buttons ---------- */
    .btn-teal {
        background-color: var(--rx-teal-900);
        color: #ffffff;
        border: 1px solid var(--rx-teal-900);
        border-radius: 10px;
        padding: 0.6rem 1.15rem;
        transition: all 0.2s ease;
    }

    .btn-teal:hover,
    .btn-teal:focus {
        background-color: var(--rx-teal-700);
        border-color: var(--rx-teal-700);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 8px 16px -6px rgba(13, 148, 136, 0.35);
    }

    .btn-outline-teal {
        background-color: transparent;
        color: var(--rx-teal-900);
        border: 1px solid var(--rx-teal-900);
        border-radius: 10px;
        padding: 0.55rem 1.1rem;
        transition: all 0.2s ease;
    }

    .btn-outline-teal:hover {
        background-color: var(--rx-teal-050);
        color: var(--rx-teal-900);
    }

    .text-teal {
        color: var(--rx-teal-900) !important;
    }

    .card-header-icon {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background-color: var(--rx-teal-050);
        color: var(--rx-teal-900);
        font-size: 1rem;
        flex-shrink: 0;
    }

    .workspace-card .card-header,
    .gated-section .card-header,
    .card>.card-header {
        padding: 1rem 1.25rem;
    }

    .medicine-count-badge {
        font-size: 0.78rem;
        font-weight: 600;
        padding: 6px 12px;
        background-color: var(--rx-teal-100);
        color: #00796b;
    }

    /* ---------- Labels & inputs ---------- */
    .small-label {
        font-size: 0.8rem;
        color: var(--rx-slate-600);
        font-weight: 600;
        letter-spacing: 0.01em;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--rx-teal-700);
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.14);
    }

    .form-control,
    .form-select {
        font-size: 0.92rem;
    }

    /* ---------- Rx Tab Bar ---------- */
    .rx-tabs-card .card-body {
        padding: 0.65rem 0.75rem;
    }

    .rx-tabs-container {
        overflow-x: auto;
        white-space: nowrap;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
    }

    .rx-tabs-container::-webkit-scrollbar {
        display: none;
    }

    .rx-tabs-list {
        display: flex;
        flex-wrap: nowrap;
        padding-left: 0;
        margin: 0;
        list-style: none;
        gap: 6px;
    }

    .rx-tab-item {
        flex: 0 0 auto;
    }

    .rx-tab-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 14px;
        border: 1px solid var(--rx-slate-200);
        background-color: var(--rx-slate-050);
        color: var(--rx-slate-500);
        font-weight: 500;
        font-size: 0.86rem;
        text-decoration: none;
        border-radius: 10px;
        transition: all 0.18s ease;
    }

    .rx-tab-link:hover {
        background-color: var(--rx-slate-100);
        color: var(--rx-teal-900);
    }

    .rx-tab-link.active {
        background-color: var(--rx-teal-050);
        color: var(--rx-teal-900);
        border-color: var(--rx-teal-500);
        font-weight: 600;
    }

    .rx-tab-close-btn {
        background: none;
        border: none;
        padding: 0;
        font-size: 0.8rem;
        line-height: 1;
        color: var(--rx-slate-400);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        transition: all 0.15s ease;
    }

    .rx-tab-close-btn:hover {
        color: #ffffff;
        background-color: var(--rx-danger);
    }

    /* ---------- Gated Prescription Column ---------- */
    .gated-section {
        position: relative;
    }

    .gated-section.gated .medicine-content {
        opacity: 0.1;
        pointer-events: none;
        filter: blur(1.5px);
    }

    .gated-section.gated .gated-msg {
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        inset: 0;
        background-color: rgba(255, 255, 255, 0.55);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        z-index: 100;
        border-radius: var(--rx-radius);
        animation: fadeInOverlay 0.25s ease-out;
        padding: 1rem;
    }

    .gated-section .gated-msg {
        display: none;
    }

    .gated-msg-box {
        background-color: rgba(255, 255, 255, 0.97);
        border: 1px solid var(--rx-slate-200);
        box-shadow: 0 20px 40px -20px rgba(15, 118, 110, 0.35);
        max-width: 320px;
    }

    .gated-msg-icon {
        font-size: 2.8rem;
        color: var(--rx-teal-700);
    }

    @keyframes fadeInOverlay {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    /* ---------- Medicine Table / Card layout ---------- */
    .rx-table-wrapper {
        border: 1px solid var(--rx-slate-100);
        border-radius: 12px;
        overflow: hidden;
        min-height: 220px;
    }

    .table-notion thead tr {
        background-color: var(--rx-slate-050);
    }

    .table-notion th {
        border: none;
        border-bottom: 2px solid var(--rx-slate-100);
        color: var(--rx-slate-500);
        font-weight: 600;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 10px 12px;
        white-space: nowrap;
    }

    .table-notion td {
        border-bottom: 1px solid var(--rx-slate-100);
        padding: 8px 8px;
        vertical-align: middle;
    }

    .table-notion tr:last-child td {
        border-bottom: none;
    }

    .col-med {
        width: 30%;
    }

    .col-dose {
        width: 13%;
    }

    .col-freq {
        width: 20%;
    }

    .col-dur {
        width: 15%;
    }

    .col-instr {
        width: 17%;
    }

    .col-remove {
        width: 5%;
    }

    .table-notion input,
    .table-notion select {
        border: 1px solid var(--rx-slate-200);
        background-color: #ffffff;
        padding: 6px 8px;
        border-radius: 7px;
        transition: all 0.15s ease;
        font-size: 0.85rem !important;
        font-weight: 500;
        color: #334155;
        width: 100%;
    }

    .table-notion input::placeholder {
        color: var(--rx-slate-300);
        opacity: 1;
    }

    .table-notion input:hover,
    .table-notion select:hover {
        border-color: var(--rx-slate-300);
    }

    .table-notion input:focus,
    .table-notion select:focus {
        background-color: #ffffff;
        border-color: var(--rx-teal-700);
        box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.14);
        outline: none;
    }

    .rx-remove-btn {
        width: 28px;
        height: 28px;
        border: none;
        background: transparent;
        color: var(--rx-slate-400);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }

    .rx-remove-btn:hover {
        background-color: #fee2e2;
        color: var(--rx-danger);
    }

    .rx-empty-state {
        text-align: center;
        color: var(--rx-slate-400);
        padding: 3rem 1rem;
    }

    .rx-empty-state i {
        font-size: 2.1rem;
        opacity: 0.55;
        display: block;
        margin-bottom: 0.5rem;
    }

    /* Convert medicine table to stacked cards on small screens */
    @media (max-width: 767.98px) {
        .table-notion thead {
            display: none;
        }

        .table-notion,
        .table-notion tbody,
        .table-notion tr,
        .table-notion td {
            display: block;
            width: 100% !important;
        }

        .table-notion tr {
            border: 1px solid var(--rx-slate-200);
            border-radius: 12px;
            padding: 10px 12px;
            margin-bottom: 10px;
            background: #ffffff;
            position: relative;
        }

        .table-notion tr:last-child {
            margin-bottom: 0;
        }

        .table-notion td {
            border-bottom: 1px dashed var(--rx-slate-100);
            padding: 8px 2px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .table-notion td:last-child {
            border-bottom: none;
            padding-top: 4px;
        }

        .table-notion td::before {
            content: attr(data-label);
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--rx-slate-500);
            flex-shrink: 0;
        }

        .table-notion td.col-med::before {
            display: none;
        }

        .table-notion td.col-med {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--rx-ink);
            padding-bottom: 8px;
            border-bottom: 1px solid var(--rx-slate-100);
        }

        .table-notion td.col-remove {
            justify-content: flex-end;
        }

        .table-notion input,
        .table-notion select {
            max-width: 62%;
            margin-left: auto;
        }

        .rx-table-wrapper {
            border: none;
            border-radius: 0;
        }
    }

    /* ---------- Drug search dropdown ---------- */
    .drug-search-dropdown {
        max-height: 260px;
        overflow-y: auto;
        border-radius: 10px;
        display: none;
        z-index: 1000;
        border: 1px solid var(--rx-slate-200);
        padding: 4px;
    }

    .drug-search-dropdown .dropdown-item {
        border-radius: 8px;
        padding: 8px 10px !important;
    }

    .drug-search-dropdown .dropdown-item:hover {
        background-color: var(--rx-teal-050);
    }

    /* ---------- Recent Patients ---------- */
    .recent-patient-card {
        border: 1px solid var(--rx-slate-200);
        border-radius: 12px;
        transition: all 0.2s ease;
        height: 100%;
    }

    .recent-patient-card:hover {
        border-color: var(--rx-teal-700);
        background-color: var(--rx-teal-050);
        transform: translateY(-2px);
        box-shadow: 0 12px 22px -10px rgba(13, 148, 136, 0.22);
    }

    /* ---------- Invoice ---------- */
    .invoice-outer {
        background-color: #ffffff;
        border: 1.5px solid var(--rx-slate-200);
        border-radius: var(--rx-radius);
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    }

    .invoice-card {
        padding: 1.75rem;
        font-family: 'Poppins', sans-serif;
    }

    .invoice-brand {
        color: var(--rx-teal-900);
        font-weight: 700;
    }

    .invoice-label {
        letter-spacing: 0.1em;
        font-weight: 700;
        color: var(--rx-ink);
    }

    .invoice-table th {
        font-size: 0.75rem;
        background-color: var(--rx-slate-050);
    }

    .invoice-table td {
        font-size: 0.85rem;
    }

    @media (max-width: 575.98px) {
        .invoice-print-container .row.mb-4 .col-6 {
            width: 100%;
            flex: 0 0 100%;
            max-width: 100%;
        }

        .invoice-print-container .col-6.border-end {
            border-end: none !important;
            border-bottom: 1px solid var(--rx-slate-200);
            padding-bottom: 12px;
            margin-bottom: 12px;
        }

        .invoice-print-container .col-6.ps-4 {
            padding-left: 0 !important;
        }

        .invoice-table thead {
            display: none;
        }

        .invoice-table,
        .invoice-table tbody,
        .invoice-table tr,
        .invoice-table td {
            display: block;
            width: 100%;
        }

        .invoice-table tr {
            border: 1px solid var(--rx-slate-200);
            border-radius: 10px;
            margin-bottom: 8px;
            padding: 8px 10px;
        }

        .invoice-table td {
            border: none;
            padding: 4px 0;
            display: flex;
            justify-content: space-between;
        }

        .invoice-table td::before {
            content: attr(data-label);
            font-weight: 600;
            color: var(--rx-slate-500);
            font-size: 0.7rem;
            text-transform: uppercase;
        }
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: var(--rx-slate-050);
        border-radius: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: var(--rx-slate-300);
        border-radius: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: var(--rx-slate-400);
    }

    /* ---------- General mobile polish ---------- */
    @media (max-width: 575.98px) {
        .prescription-desk-container {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .welcome-banner {
            padding: 1.25rem !important;
        }

        .card-body {
            padding: 1.1rem !important;
        }

        .btn-teal,
        .btn-outline-teal {
            width: 100%;
            justify-content: center;
        }

        #prescription-actions-row .btn-teal {
            width: 100%;
        }

        #prescription-actions-row {
            flex-direction: column;
            align-items: stretch !important;
        }

        #prescription-actions-row span {
            text-align: center;
        }

        .rx-patient-form-submit {
            width: 100%;
        }
    }

    /* ---------- Print Styles ---------- */
    @media print {
        body {
            background-color: #ffffff !important;
            color: #000000 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Hide all page layouts, menus, tabs, and workspace sections */
        header,
        footer,
        .sidebar,
        .page-header,
        .page-footer,
        .welcome-banner,
        .rx-tabs-card,
        .rx-workspace-row,
        #recent-patients-list,
        .print-hide,
        .btn,
        #addNewRxBtn,
        #duplicate-warning-note {
            display: none !important;
        }

        /* Flatten outer layers of the active invoice container */
        body,
        .prescription-desk-container,
        #invoice-display-container,
        .invoice-outer {
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

        .print-invoice-area {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            box-shadow: none !important;
        }

        .invoice-card {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            background: transparent !important;
        }

        /* Keep practitioner and patient detail columns side by side inside the printed invoice */
        .invoice-print-container .row {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            margin-bottom: 1rem !important;
        }

        .invoice-print-container .col-6 {
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
        
        .table-responsive {
            overflow: visible !important;
        }
    }
</style>

<!-- Page Script State and Handlers -->
<script>
    // State management array mirroring localStorage
    let rxTabs = [];
    let activeTabId = null;

    <?php
    $ci =& get_instance();
    $doctor_id = $ci->session->userdata('doctor_user_id');
    $doctor_user = $ci->General_model->getById('users', $doctor_id);
    $doctor_profile = $ci->General_model->getOne('doctor_profiles', ['user_id' => $doctor_id]);
    $current_signature = (!empty($doctor_profile->signature)) ? base_url($doctor_profile->signature) : '';
    $current_address = (!empty($doctor_user->address)) ? $doctor_user->address : '';
    $current_hospital = (!empty($doctor_profile->hospital_clinic)) ? $doctor_profile->hospital_clinic : '';
    ?>
    const currentDoctorSignature = '<?php echo $current_signature; ?>';
    const currentDoctorAddress = <?php echo json_encode($current_address); ?>;
    const currentDoctorHospital = <?php echo json_encode($current_hospital); ?>;

    // CSRF references updated dynamically from responses
    let csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    let csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

    // Server-populated initial recent patients
    let recentPatients = <?php echo json_encode($recent_patients); ?>;

    const STORAGE_KEY = 'ddi_checker_rx_tabs';
    const ACTIVE_KEY = 'ddi_checker_rx_active_id';

    document.addEventListener('DOMContentLoaded', function() {
        // Load initial state
        loadFromStorage();

        // Setup contact number input handlers
        const contactInput = document.getElementById('contact_number');
        let phoneDebounceTimeout = null;

        contactInput.addEventListener('input', function(e) {
            const phoneVal = e.target.value;
            const cleanPhone = phoneVal.replace(/\D/g, '');

            // Sync current values back to tab state
            saveCurrentTabInputs();
            saveToStorage();

            if (phoneDebounceTimeout) {
                clearTimeout(phoneDebounceTimeout);
            }

            // Only trigger auto-lookup if 10 or more digits typed
            if (cleanPhone.length >= 10) {
                phoneDebounceTimeout = setTimeout(function() {
                    triggerPatientLookup(phoneVal);
                }, 500);
            } else {
                document.getElementById('phone-status-note').innerHTML = '';
            }
        });

        // Setup Drug Autocomplete input handler
        const drugInput = document.getElementById('drugSearchInput');
        let drugSearchTimeout = null;

        drugInput.addEventListener('input', function(e) {
            const term = e.target.value.trim();
            if (drugSearchTimeout) clearTimeout(drugSearchTimeout);

            const resultsDiv = document.getElementById('drugSearchResults');

            if (term.length < 2) {
                resultsDiv.style.display = 'none';
                return;
            }

            drugSearchTimeout = setTimeout(function() {
                fetch('<?php echo base_url("doctor/prescription-desk/search_drugs"); ?>?term=' + encodeURIComponent(term))
                    .then(res => {
                        if (!res.ok) throw new Error('Failed to query drugs');
                        return res.json();
                    })
                    .then(drugs => {
                        if (drugs.length === 0) {
                            resultsDiv.innerHTML = '<div class="dropdown-item text-muted">No medicines found</div>';
                            resultsDiv.style.display = 'block';
                            return;
                        }

                        let html = '';
                        drugs.forEach(d => {
                            const qty = parseInt(d.quantity) || 0;
                            const isOutOfStock = qty <= 0;
                            const stockText = isOutOfStock ? '<span class="text-danger fw-semibold">[Out of Stock]</span>' : `<span class="text-success">[Qty: ${qty} ${htmlEscape(d.unit || '')}]</span>`;
                            const catText = d.category ? ` <span class="badge bg-secondary-subtle text-secondary">${htmlEscape(d.category)}</span>` : '';

                            // Parse object safely for onclick argument
                            const drugJson = JSON.stringify({
                                id: d.id,
                                drug_name: d.drug_name
                            });

                            html += `
                            <button type="button" class="dropdown-item d-flex align-items-center justify-content-between py-2 border-bottom text-start" onclick='handleAddDrug(${drugJson})'>
                                <div>
                                    <strong class="text-dark" style="font-size: 0.88rem;">${htmlEscape(d.drug_name)}</strong> ${catText}
                                </div>
                                <small class="text-muted" style="font-size: 0.78rem;">${stockText}</small>
                            </button>
                            `;
                        });
                        resultsDiv.innerHTML = html;
                        resultsDiv.style.display = 'block';
                    })
                    .catch(err => {
                        console.error('Drug search failed:', err);
                    });
            }, 250);
        });

        // Close search results dropdown on outside click
        document.addEventListener('click', function(e) {
            const resultsDiv = document.getElementById('drugSearchResults');
            if (resultsDiv && e.target !== drugInput && !resultsDiv.contains(e.target)) {
                resultsDiv.style.display = 'none';
            }
        });

        // Initial render of recent patients
        renderRecentPatients();
    });

    /**
     * Restore state from localStorage or setup first default tab
     */
    function loadFromStorage() {
        const stored = localStorage.getItem(STORAGE_KEY);
        const active = localStorage.getItem(ACTIVE_KEY);

        if (stored) {
            try {
                rxTabs = JSON.parse(stored);
                activeTabId = active;
                if (!rxTabs.some(t => t.id === activeTabId)) {
                    activeTabId = rxTabs.length > 0 ? rxTabs[0].id : null;
                }
            } catch (e) {
                rxTabs = [];
                activeTabId = null;
            }
        }

        if (rxTabs.length === 0) {
            addNewTab();
        } else {
            // Clean tabs data just in case structure differs from Step 1
            rxTabs.forEach(t => {
                if (!t.items) t.items = [];
                if (t.prescription_id === undefined) t.prescription_id = null;
                if (t.finalized === undefined) t.finalized = false;
                if (t.invoice_data === undefined) t.invoice_data = null;
            });
            renderTabs();
            loadActiveTabForm();
        }
    }

    /**
     * Write active tab array to localStorage
     */
    function saveToStorage() {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(rxTabs));
        localStorage.setItem(ACTIVE_KEY, activeTabId);
    }

    /**
     * Render the tab headers list in HTML
     */
    function renderTabs() {
        const tabsList = document.getElementById('rxTabsList');
        if (!tabsList) return;

        let html = '';
        rxTabs.forEach((tab) => {
            const isActive = tab.id === activeTabId;
            const statusLabel = tab.finalized ? ' <i class="bi bi-patch-check-fill text-success" title="Finalized" style="font-size: 0.82rem;"></i>' : '';
            html += `
            <li class="rx-tab-item">
                <a href="#" class="rx-tab-link ${isActive ? 'active' : ''}" onclick="switchTab('${tab.id}', event)">
                    <span>${htmlEscape(tab.label)}${statusLabel}</span>
                    <button class="rx-tab-close-btn" onclick="closeTab('${tab.id}', event)">
                        <i class="bi bi-x"></i>
                    </button>
                </a>
            </li>
            `;
        });
        tabsList.innerHTML = html;

        // Scroll the active tab into view
        setTimeout(() => {
            const activeLink = tabsList.querySelector('.rx-tab-link.active');
            if (activeLink) {
                activeLink.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                    inline: 'nearest'
                });
            }
        }, 50);
    }

    /**
     * Open a new blank prescription tab
     */
    function addNewTab() {
        saveCurrentTabInputs();

        const newId = Date.now().toString() + '_' + Math.floor(Math.random() * 1000);
        const newIndex = rxTabs.length + 1;

        const newTab = {
            id: newId,
            label: `Rx ${newIndex}`,
            patient_id: null,
            prescription_id: null,
            finalized: false,
            invoice_data: null,
            items: [],
            data: {
                full_name: "",
                contact_number: "",
                age: "",
                gender: "",
                chief_complaints: "",
                height_cm: "",
                weight_kg: "",
                medical_history: ""
            },
            original_data: {}
        };

        rxTabs.push(newTab);
        activeTabId = newId;

        renderTabs();
        loadActiveTabForm();
        saveToStorage();
    }

    /**
     * Switch to a different tab index
     */
    function switchTab(tabId, event) {
        if (event) event.preventDefault();
        if (activeTabId === tabId) return;

        saveCurrentTabInputs();
        activeTabId = tabId;

        renderTabs();
        loadActiveTabForm();
        saveToStorage();
    }

    /**
     * Close a tab by ID with confirmation of unsaved edits
     */
    function closeTab(tabId, event) {
        if (event) event.stopPropagation(); // Stop tab switching behavior

        const tabIndex = rxTabs.findIndex(t => t.id === tabId);
        if (tabIndex === -1) return;

        const tab = rxTabs[tabIndex];

        if (isTabDirty(tab)) {
            Swal.fire({
                title: 'Discard unsaved changes?',
                text: `You have unsaved clinical entries in ${tab.label}. Closing this tab will discard them.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Discard',
                cancelButtonText: 'No, Keep Open',
                background: '#ffffff',
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-4 shadow-lg p-4 border-0',
                    title: 'fw-bold text-dark fs-5 mb-2',
                    confirmButton: 'btn btn-danger px-4 py-2.5 rounded-3 fw-semibold shadow-sm mx-1',
                    cancelButton: 'btn btn-teal px-4 py-2.5 rounded-3 fw-semibold shadow-sm mx-1',
                    actions: 'd-flex gap-2 justify-content-center mt-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    executeTabClose(tabId, tabIndex);
                }
            });
        } else {
            executeTabClose(tabId, tabIndex);
        }
    }

    function executeTabClose(tabId, index) {
        rxTabs.splice(index, 1);

        // Re-number labels sequentially
        rxTabs.forEach((tab, i) => {
            tab.label = `Rx ${i + 1}`;
        });

        // Resolve active tab refocus
        if (activeTabId === tabId) {
            if (rxTabs.length > 0) {
                const nextActiveIndex = Math.min(index, rxTabs.length - 1);
                activeTabId = rxTabs[nextActiveIndex].id;
            } else {
                activeTabId = null;
            }
        }

        if (rxTabs.length === 0) {
            addNewTab(); // Always ensure at least one tab is open
        } else {
            renderTabs();
            loadActiveTabForm();
            saveToStorage();
        }
    }

    /**
     * Pull form values into JavaScript array state for active tab
     */
    function saveCurrentTabInputs() {
        if (!activeTabId) return;
        const activeTab = rxTabs.find(t => t.id === activeTabId);
        if (!activeTab) return;

        activeTab.data.full_name = document.getElementById('full_name').value;
        activeTab.data.contact_number = document.getElementById('contact_number').value;
        activeTab.data.age = document.getElementById('age').value;
        activeTab.data.gender = document.getElementById('gender').value;
        activeTab.data.chief_complaints = document.getElementById('chief_complaints').value;
        activeTab.data.height_cm = document.getElementById('height_cm').value;
        activeTab.data.weight_kg = document.getElementById('weight_kg').value;
        activeTab.data.medical_history = document.getElementById('medical_history').value;
    }

    /**
     * Push active tab state values into form elements
     */
    function loadActiveTabForm() {
        if (!activeTabId) return;
        const activeTab = rxTabs.find(t => t.id === activeTabId);
        if (!activeTab) return;

        document.getElementById('patient_id').value = activeTab.patient_id || '';
        document.getElementById('full_name').value = activeTab.data.full_name || '';
        document.getElementById('contact_number').value = activeTab.data.contact_number || '';
        document.getElementById('age').value = activeTab.data.age || '';
        document.getElementById('gender').value = activeTab.data.gender || '';
        document.getElementById('chief_complaints').value = activeTab.data.chief_complaints || '';
        document.getElementById('height_cm').value = activeTab.data.height_cm || '';
        document.getElementById('weight_kg').value = activeTab.data.weight_kg || '';
        document.getElementById('medical_history').value = activeTab.data.medical_history || '';

        // Reset phone fetch status notes
        document.getElementById('phone-status-note').innerHTML = '';

        if (activeTab.patient_id) {
            const note = document.getElementById('phone-status-note');
            note.className = "form-text text-success fw-medium small";
            note.innerHTML = '<i class="bi bi-check-circle-fill"></i> Patient intake loaded';
        }

        // Toggle visual gating lock on Prescription Builder Column
        const medSection = document.getElementById('medicine-section-container');
        if (activeTab.patient_id) {
            medSection.classList.remove('gated');
        } else {
            medSection.classList.add('gated');
        }

        // Lock form inputs if prescription is finalized
        const isFinal = !!activeTab.finalized;
        const formInputs = document.querySelectorAll('#rxPatientForm input, #rxPatientForm select, #rxPatientForm textarea, #rxPatientForm button[type="submit"]');
        formInputs.forEach(input => {
            input.disabled = isFinal;
        });

        // Lock drug search autocomplete
        document.getElementById('drugSearchInput').disabled = isFinal;

        // Toggle visibility of regimen saving controls vs finalized invoice display
        const actionsRow = document.getElementById('prescription-actions-row');
        const invoiceContainer = document.getElementById('invoice-display-container');
        if (isFinal) {
            actionsRow.style.setProperty('display', 'none', 'important');
            invoiceContainer.style.display = 'block';
            renderInvoiceHTML(activeTab);
        } else {
            actionsRow.style.setProperty('display', 'flex', 'important');
            invoiceContainer.style.display = 'none';
            invoiceContainer.innerHTML = '';
        }

        // Render medications list table for this tab
        renderItemsTable();
    }

    /**
     * Compute dirty edit state compared to snapshot
     */
    function isTabDirty(tab) {
        const curr = tab.data;
        const orig = tab.original_data || {};

        if (tab.patient_id) {
            return (
                curr.full_name !== (orig.full_name || "") ||
                curr.contact_number !== (orig.contact_number || "") ||
                curr.age.toString() !== (orig.age || "").toString() ||
                curr.gender !== (orig.gender || "") ||
                curr.chief_complaints !== (orig.chief_complaints || "") ||
                curr.height_cm.toString() !== (orig.height_cm || "").toString() ||
                curr.weight_kg.toString() !== (orig.weight_kg || "").toString() ||
                curr.medical_history !== (orig.medical_history || "")
            );
        } else {
            return (
                curr.full_name.trim() !== "" ||
                curr.contact_number.trim() !== "" ||
                curr.age.trim() !== "" ||
                curr.gender !== "" ||
                curr.chief_complaints.trim() !== "" ||
                curr.height_cm.trim() !== "" ||
                curr.weight_kg.trim() !== "" ||
                curr.medical_history.trim() !== ""
            );
        }
    }

    /**
     * Execute AJAX GET query to look up existing patient by phone
     */
    function triggerPatientLookup(phone) {
        const statusNote = document.getElementById('phone-status-note');
        statusNote.className = "form-text text-muted small";
        statusNote.innerHTML = '<span class="spinner-border spinner-border-sm text-teal" role="status" style="border-width: 0.15em; width: 12px; height: 12px;"></span> Checking records...';

        const url = '<?php echo base_url("doctor/prescription-desk/fetch_patient"); ?>?phone=' + encodeURIComponent(phone);

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network error during patient look up.');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'found' && data.patient) {
                    const patient = data.patient;

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: `Profile loaded: ${patient.full_name}`,
                        showConfirmButton: false,
                        timer: 2500
                    });

                    const activeTab = rxTabs.find(t => t.id === activeTabId);
                    if (activeTab) {
                        activeTab.patient_id = patient.id;
                        activeTab.data = {
                            full_name: patient.full_name || '',
                            contact_number: patient.contact_number || '',
                            age: patient.age ? patient.age.toString() : '',
                            gender: patient.gender || '',
                            chief_complaints: patient.chief_complaints || '',
                            height_cm: patient.height_cm ? patient.height_cm.toString() : '',
                            weight_kg: patient.weight_kg ? patient.weight_kg.toString() : '',
                            medical_history: patient.medical_history || ''
                        };
                        activeTab.original_data = JSON.parse(JSON.stringify(activeTab.data));

                        loadActiveTabForm();
                        saveToStorage();
                    }
                } else {
                    statusNote.innerHTML = '';
                }
            })
            .catch(error => {
                console.error('Patient lookup failed:', error);
                statusNote.className = "form-text text-warning small";
                statusNote.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Database issue, continuing new entry';
            });
    }

    /**
     * AJAX POST save patient handler
     */
    function handleSavePatient(event) {
        event.preventDefault();
        saveCurrentTabInputs();

        const activeTab = rxTabs.find(t => t.id === activeTabId);
        if (!activeTab) return;

        if (!activeTab.data.full_name || !activeTab.data.age || !activeTab.data.gender) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Patient Name, Age, and Gender are required.',
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-4 shadow-lg p-4 border-0',
                    title: 'fw-bold text-dark fs-5 mb-2',
                    confirmButton: 'btn btn-teal px-4 py-2.5 rounded-3 fw-semibold shadow-sm'
                }
            });
            return;
        }

        const saveUrl = '<?php echo base_url("doctor/prescription-desk/save_patient"); ?>';

        const formData = new FormData();
        formData.append(csrfName, csrfHash);
        if (activeTab.patient_id) {
            formData.append('patient_id', activeTab.patient_id);
        }
        formData.append('full_name', activeTab.data.full_name);
        formData.append('contact_number', activeTab.data.contact_number);
        formData.append('age', activeTab.data.age);
        formData.append('gender', activeTab.data.gender);
        formData.append('chief_complaints', activeTab.data.chief_complaints);
        formData.append('height_cm', activeTab.data.height_cm);
        formData.append('weight_kg', activeTab.data.weight_kg);
        formData.append('medical_history', activeTab.data.medical_history);

        Swal.fire({
            title: 'Saving Profile...',
            text: 'Writing patient credentials to secure database',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(saveUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                return response.json().then(data => {
                    if (!response.ok) {
                        return Promise.reject(data);
                    }
                    return data;
                });
            })
            .then(data => {
                if (data.csrf_hash) {
                    csrfHash = data.csrf_hash;
                }

                // Success save state update
                activeTab.patient_id = data.patient_id;
                activeTab.original_data = JSON.parse(JSON.stringify(activeTab.data));

                saveToStorage();

                if (data.recent_patients) {
                    recentPatients = data.recent_patients;
                    renderRecentPatients();
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Profile Saved',
                    text: 'Patient details registered successfully. Prescription Builder is now unlocked.',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-4 shadow-lg p-4 border-0',
                        title: 'fw-bold text-dark fs-5 mb-2',
                        confirmButton: 'btn btn-teal px-4 py-2.5 rounded-3 fw-semibold shadow-sm'
                    },
                    timer: 2500
                });

                loadActiveTabForm();
            })
            .catch(err => {
                if (err && err.csrf_hash) {
                    csrfHash = err.csrf_hash;
                }

                const errorMsg = err && err.message ? err.message : 'A server or database write issue occurred.';

                Swal.fire({
                    icon: 'error',
                    title: 'Failed to Save',
                    html: errorMsg,
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-4 shadow-lg p-4 border-0',
                        title: 'fw-bold text-dark fs-5 mb-2',
                        confirmButton: 'btn btn-danger px-4 py-2.5 rounded-3 fw-semibold shadow-sm'
                    }
                });
            });
    }

    /**
     * Render the 5 recent profiles
     */
    function renderRecentPatients() {
        const container = document.getElementById('recent-patients-list');
        if (!container) return;

        if (!recentPatients || recentPatients.length === 0) {
            container.innerHTML = '<div class="col-12 text-muted text-center py-3">No recent patient queue items found.</div>';
            return;
        }

        let html = '';
        recentPatients.forEach((p, index) => {
            html += `
            <div class="col-md-4 col-sm-6 col-12">
                <div class="card h-100 border rounded-3 p-3 shadow-sm recent-patient-card" style="cursor: pointer;" onclick="handleRecentPatientClick(${index})">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 75%; font-size:0.92rem;">Dr. Selected / ${htmlEscape(p.full_name)}</h6>
                        <span class="badge bg-teal-subtle text-teal" style="font-size: 0.68rem; background-color: #e0f2f1; color: #00796b; font-weight:600;">
                            ${htmlEscape(p.gender)}
                        </span>
                    </div>
                    <div class="small text-secondary mb-1" style="font-size: 0.8rem;">
                        <i class="bi bi-telephone text-muted me-1"></i>${htmlEscape(p.contact_number || 'No contact')}
                    </div>
                    <div class="small text-secondary" style="font-size: 0.8rem;">
                        <i class="bi bi-person text-muted me-1"></i>Age: ${htmlEscape(p.age)} yrs
                    </div>
                </div>
            </div>
            `;
        });
        container.innerHTML = html;
    }

    function handleRecentPatientClick(index) {
        const patient = recentPatients[index];
        if (!patient) return;

        const activeTab = rxTabs.find(t => t.id === activeTabId);
        if (!activeTab) return;

        if (isTabDirty(activeTab)) {
            Swal.fire({
                title: 'Overwrite changes?',
                text: 'The current tab has unsaved modifications. Overwrite it with this patient profile?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Overwrite',
                cancelButtonText: 'Cancel',
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-4 shadow-lg p-4 border-0',
                    title: 'fw-bold text-dark fs-5 mb-2',
                    confirmButton: 'btn btn-teal px-4 py-2.5 rounded-3 fw-semibold shadow-sm mx-1',
                    cancelButton: 'btn btn-secondary px-4 py-2.5 rounded-3 fw-semibold shadow-sm mx-1',
                    actions: 'd-flex gap-2 justify-content-center mt-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    applyPatientToTab(activeTab, patient);
                }
            });
        } else {
            applyPatientToTab(activeTab, patient);
        }
    }

    function applyPatientToTab(tab, patient) {
        tab.patient_id = patient.id;
        tab.prescription_id = null; // Clear old prescription ID as it is a new load
        tab.items = []; // Clear old items
        tab.data = {
            full_name: patient.full_name || '',
            contact_number: patient.contact_number || '',
            age: patient.age ? patient.age.toString() : '',
            gender: patient.gender || '',
            chief_complaints: patient.chief_complaints || '',
            height_cm: patient.height_cm ? patient.height_cm.toString() : '',
            weight_kg: patient.weight_kg ? patient.weight_kg.toString() : '',
            medical_history: patient.medical_history || ''
        };
        tab.original_data = JSON.parse(JSON.stringify(tab.data));

        loadActiveTabForm();
        saveToStorage();

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: `Loaded: ${patient.full_name}`,
            showConfirmButton: false,
            timer: 2000
        });
    }

    /**
     * Add drug selected to active items list and update layout
     */
    function handleAddDrug(drug) {
        document.getElementById('drugSearchResults').style.display = 'none';
        document.getElementById('drugSearchInput').value = '';

        const activeTab = rxTabs.find(t => t.id === activeTabId);
        if (!activeTab) return;

        const warningDiv = document.getElementById('duplicate-warning-note');

        // Prevent duplicate drug additions
        if (activeTab.items.some(item => parseInt(item.drug_id) === parseInt(drug.id))) {
            warningDiv.style.display = 'block';
            setTimeout(() => {
                warningDiv.style.display = 'none';
            }, 4000);
            return;
        }

        warningDiv.style.display = 'none';

        const newItem = {
            id: null,
            drug_id: drug.id,
            drug_name: drug.drug_name,
            dosage: "",
            frequency: "Once daily",
            duration: "3 days",
            special_instructions: ""
        };

        activeTab.items.push(newItem);
        renderItemsTable();
        saveToStorage();
    }

    /**
     * Sync value of custom input boxes with active tab's items array
     */
    function updateItemField(index, field, value) {
        const activeTab = rxTabs.find(t => t.id === activeTabId);
        if (!activeTab || !activeTab.items[index]) return;

        activeTab.items[index][field] = value;
        saveToStorage();
    }

    /**
     * Delete item from active state (and AJAX delete if in database)
     */
    function handleRemoveItem(itemIndex) {
        const activeTab = rxTabs.find(t => t.id === activeTabId);
        if (!activeTab || !activeTab.items[itemIndex]) return;

        const item = activeTab.items[itemIndex];

        if (item.id) {
            Swal.fire({
                title: 'Delete saved medicine?',
                text: `Are you sure you want to remove "${item.drug_name}" from database records?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel',
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-4 shadow-lg p-4 border-0',
                    title: 'fw-bold text-dark fs-5 mb-2',
                    confirmButton: 'btn btn-danger px-4 py-2.5 rounded-3 fw-semibold shadow-sm mx-1',
                    cancelButton: 'btn btn-teal px-4 py-2.5 rounded-3 fw-semibold shadow-sm mx-1',
                    actions: 'd-flex gap-2 justify-content-center mt-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    executeRemoveItemAjax(itemIndex, item.id);
                }
            });
        } else {
            activeTab.items.splice(itemIndex, 1);
            renderItemsTable();
            saveToStorage();
        }
    }

    function executeRemoveItemAjax(index, itemId) {
        const url = '<?php echo base_url("doctor/prescription-desk/remove_item/"); ?>' + itemId;

        const formData = new FormData();
        formData.append(csrfName, csrfHash);

        Swal.fire({
            title: 'Removing Item...',
            text: 'Updating prescription regimen records',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(res => {
                return res.json().then(data => {
                    if (!res.ok) {
                        return Promise.reject(data);
                    }
                    return data;
                });
            })
            .then(data => {
                if (data.csrf_hash) {
                    csrfHash = data.csrf_hash;
                }

                const activeTab = rxTabs.find(t => t.id === activeTabId);
                if (activeTab) {
                    activeTab.items.splice(index, 1);
                    renderItemsTable();
                    saveToStorage();
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Item Deleted',
                    text: 'Medicine successfully removed.',
                    timer: 1500,
                    showConfirmButton: false
                });
            })
            .catch(err => {
                if (err && err.csrf_hash) {
                    csrfHash = err.csrf_hash;
                }
                const errMsg = err && err.message ? err.message : 'Database update failed.';
                Swal.fire({
                    icon: 'error',
                    title: 'Deletion Failed',
                    html: errMsg,
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-4 shadow-lg p-4 border-0',
                        title: 'fw-bold text-dark fs-5 mb-2',
                        confirmButton: 'btn btn-danger px-4 py-2.5 rounded-3 fw-semibold shadow-sm'
                    }
                });
            });
    }

    /**
     * Render medications table in active tab form (data-label attrs power the mobile card view)
     */
    function renderItemsTable() {
        const activeTab = rxTabs.find(t => t.id === activeTabId);
        if (!activeTab) return;

        const countBadge = document.getElementById('medicine-count-badge');
        const itemsCount = activeTab.items ? activeTab.items.length : 0;
        countBadge.innerText = `${itemsCount} medicine${itemsCount === 1 ? '' : 's'} added`;

        const tableBody = document.getElementById('medicationsTableBody');
        if (itemsCount === 0) {
            tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="p-0">
                    <div class="rx-empty-state">
                        <i class="bi bi-capsule-therapeutic"></i>
                        No medications added to this prescription yet. Search and select a drug above to start.
                    </div>
                </td>
            </tr>
            `;
            return;
        }

        const frequencies = [
            "Once daily",
            "Twice daily",
            "Thrice daily",
            "Four times daily"
        ];

        const durations = [
            "3 days",
            "5 days",
            "7 days",
            "10 days",
            "14 days",
            "1 month"
        ];

        const isFinal = !!activeTab.finalized;
        let html = '';
        activeTab.items.forEach((item, index) => {
            let freqOptions = '';
            frequencies.forEach(f => {
                freqOptions += `<option value="${f}" ${item.frequency === f ? 'selected' : ''}>${f}</option>`;
            });

            let durOptions = '';
            durations.forEach(d => {
                durOptions += `<option value="${d}" ${item.duration === d ? 'selected' : ''}>${d}</option>`;
            });

            html += `
            <tr class="align-middle">
                <td class="col-med" data-label="Medicine">
                    <div class="fw-semibold text-dark" style="font-size: 0.85rem;">${htmlEscape(item.drug_name)}</div>
                </td>
                <td class="col-dose" data-label="Dosage">
                    <input type="text" class="form-control form-control-sm" value="${htmlEscape(item.dosage || '')}" placeholder="e.g. 500mg" oninput="updateItemField(${index}, 'dosage', this.value)" required ${isFinal ? 'disabled' : ''}>
                </td>
                <td class="col-freq" data-label="Frequency">
                    <select class="form-select form-select-sm" onchange="updateItemField(${index}, 'frequency', this.value)" ${isFinal ? 'disabled' : ''}>
                        ${freqOptions}
                    </select>
                </td>
                <td class="col-dur" data-label="Duration">
                    <select class="form-select form-select-sm" onchange="updateItemField(${index}, 'duration', this.value)" ${isFinal ? 'disabled' : ''}>
                        ${durOptions}
                    </select>
                </td>
                <td class="col-instr" data-label="Instructions">
                    <input type="text" class="form-control form-control-sm" value="${htmlEscape(item.special_instructions || '')}" placeholder="e.g. after meals" oninput="updateItemField(${index}, 'special_instructions', this.value)" ${isFinal ? 'disabled' : ''}>
                </td>
                <td class="col-remove text-center" data-label="">
                    ${isFinal ? '' : `
                    <button type="button" class="rx-remove-btn" onclick="handleRemoveItem(${index})" title="Remove item">
                        <i class="bi bi-x fs-5" style="line-height: 1;"></i>
                    </button>
                    `}
                </td>
            </tr>
            `;
        });
        tableBody.innerHTML = html;
    }

    /**
     * AJAX POST save prescription and medications list
     */
    function handleSavePrescription() {
        saveCurrentTabInputs();

        const activeTab = rxTabs.find(t => t.id === activeTabId);
        if (!activeTab) return;

        if (!activeTab.patient_id) {
            Swal.fire({
                icon: 'error',
                title: 'Patient Required',
                text: 'Save patient details first before saving the prescription.',
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-4 shadow-lg p-4 border-0',
                    title: 'fw-bold text-dark fs-5 mb-2',
                    confirmButton: 'btn btn-teal px-4 py-2.5 rounded-3 fw-semibold shadow-sm'
                }
            });
            return;
        }

        if (!activeTab.items || activeTab.items.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Regimen Empty',
                text: 'Please add at least one medicine to save.',
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-4 shadow-lg p-4 border-0',
                    title: 'fw-bold text-dark fs-5 mb-2',
                    confirmButton: 'btn btn-teal px-4 py-2.5 rounded-3 fw-semibold shadow-sm'
                }
            });
            return;
        }

        // Validate inline drug item fields before checking interactions
        for (let idx = 0; idx < activeTab.items.length; idx++) {
            const item = activeTab.items[idx];
            if (!item.dosage || item.dosage.trim() === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Dosage',
                    text: `Please enter dosage for "${item.drug_name}".`,
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-4 shadow-lg p-4 border-0',
                        title: 'fw-bold text-dark fs-5 mb-2',
                        confirmButton: 'btn btn-teal px-4 py-2.5 rounded-3 fw-semibold shadow-sm'
                    }
                });
                return;
            }
        }

        // Step 1: Query interactions via AJAX POST before saving
        const checkInteractionsUrl = '<?php echo base_url("doctor/prescription-desk/check_interactions"); ?>';

        const checkFormData = new FormData();
        checkFormData.append(csrfName, csrfHash);
        checkFormData.append('items', JSON.stringify(activeTab.items));

        Swal.fire({
            title: 'Evaluating Regimen...',
            text: 'Checking for drug-drug interactions in registry',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(checkInteractionsUrl, {
                method: 'POST',
                body: checkFormData
            })
            .then(res => res.json())
            .then(data => {
                if (data.csrf_hash) {
                    csrfHash = data.csrf_hash;
                }

                if (data.status === 'success' && data.interactions && data.interactions.length > 0) {
                    // Interactions detected! Generate premium HTML list for SweetAlert review
                    let listHtml = '<div class="text-start mt-2 px-1 custom-scrollbar" style="max-height: 280px; overflow-y: auto; font-family:\'Poppins\',sans-serif;">';
                    data.interactions.forEach(item => {
                        let badgeColor = '#64748b'; // default gray
                        let badgeBg = '#f1f5f9';
                        let borderLeftColor = '#64748b';
                        const severity = (item.severity || '').toUpperCase();
                        if (severity === 'SEVERE') {
                            badgeColor = '#ef4444'; // Red
                            badgeBg = '#fee2e2';
                            borderLeftColor = '#ef4444';
                        } else if (severity === 'MAJOR') {
                            badgeColor = '#f97316'; // Orange
                            badgeBg = '#ffedd5';
                            borderLeftColor = '#f97316';
                        } else if (severity === 'MODERATE') {
                            badgeColor = '#d97706'; // Dark yellow
                            badgeBg = '#fef3c7';
                            borderLeftColor = '#d97706';
                        } else if (severity === 'MILD') {
                            badgeColor = '#3b82f6'; // Blue
                            badgeBg = '#dbeafe';
                            borderLeftColor = '#3b82f6';
                        }

                        listHtml += `
                    <div class="card border mb-2.5 rounded-3 shadow-sm p-3 bg-white" style="border-color: #e2e8f0 !important; border-left: 4px solid ${borderLeftColor} !important;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <strong style="font-size: 0.9rem; color: #1e293b;">${htmlEscape(item.drug_a_name)} <i class="bi bi-arrow-left-right text-teal mx-1"></i> ${htmlEscape(item.drug_b_name)}</strong>
                            <span class="badge py-1 px-2.5 rounded-pill fw-bold" style="color: ${badgeColor}; background-color: ${badgeBg}; font-size: 0.68rem; border: 1px solid ${badgeColor}33;">
                                ${htmlEscape(item.severity)}
                            </span>
                        </div>
                        <p class="text-secondary small mb-1.5" style="line-height: 1.45; font-size: 0.8rem;">${htmlEscape(item.remarks)}</p>
                        ${item.source ? `<div class="small text-muted" style="font-size: 0.72rem;"><i class="bi bi-journal-text me-1"></i> Source: <em>${htmlEscape(item.source)}</em></div>` : ''}
                    </div>
                    `;
                    });
                    listHtml += '</div>';

                    Swal.fire({
                        title: 'Clinical Interaction Alert!',
                        html: `
                        <div class="mb-3 text-start small text-danger fw-semibold" style="font-size: 0.85rem;">
                            <i class="bi bi-exclamation-triangle-fill"></i> ${data.interactions.length} drug interaction(s) detected in the current regimen:
                        </div>
                        ${listHtml}
                        <div class="mt-3 text-start text-secondary small" style="font-size: 0.82rem; font-weight: 500;">
                            Do you want to proceed and submit this prescription anyway?
                        </div>
                    `,
                        icon: 'warning',
                        width: '600px',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Save Regimen anyway',
                        cancelButtonText: 'Cancel & Edit Regimen',
                        background: '#ffffff',
                        buttonsStyling: false,
                        customClass: {
                            popup: 'rounded-4 shadow-lg p-4 border-0',
                            title: 'fw-bold text-dark fs-4 mb-2',
                            confirmButton: 'btn btn-danger px-4 py-2.5 rounded-3 fw-semibold shadow-sm mx-1',
                            cancelButton: 'btn btn-teal px-4 py-2.5 rounded-3 fw-semibold shadow-sm mx-1',
                            actions: 'd-flex gap-2 justify-content-center mt-4 w-100'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Proceed to save
                            executeSavePrescription(activeTab);
                        } else {
                            // Abort, do not submit
                            Swal.close();
                        }
                    });
                } else {
                    // No interactions, save immediately
                    executeSavePrescription(activeTab);
                }
            })
            .catch(err => {
                if (err && err.csrf_hash) {
                    csrfHash = err.csrf_hash;
                }
                console.error('Interaction evaluation failed:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Evaluation Failed',
                    text: 'Could not run drug interaction checks. Please try again.',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-4 shadow-lg p-4 border-0',
                        title: 'fw-bold text-dark fs-5 mb-2',
                        confirmButton: 'btn btn-danger px-4 py-2.5 rounded-3 fw-semibold shadow-sm'
                    }
                });
            });
    }

    /**
     * Call save prescription AJAX endpoint
     */
    function executeSavePrescription(activeTab) {
        const saveRxUrl = '<?php echo base_url("doctor/prescription-desk/save_prescription"); ?>';

        const formData = new FormData();
        formData.append(csrfName, csrfHash);
        formData.append('patient_id', activeTab.patient_id);
        if (activeTab.prescription_id) {
            formData.append('prescription_id', activeTab.prescription_id);
        }
        formData.append('items', JSON.stringify(activeTab.items));

        Swal.fire({
            title: 'Saving Prescription...',
            text: 'Registering prescription details and medication items',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(saveRxUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                return response.json().then(data => {
                    if (!response.ok) {
                        return Promise.reject(data);
                    }
                    return data;
                });
            })
            .then(data => {
                if (data.csrf_hash) {
                    csrfHash = data.csrf_hash;
                }

                // Sync saved prescription details and invoice state
                activeTab.prescription_id = data.prescription_id;
                activeTab.finalized = true;
                activeTab.invoice_data = data;
                activeTab.items = data.items;

                saveToStorage();
                renderTabs();
                loadActiveTabForm();

                Swal.fire({
                    icon: 'success',
                    title: 'Invoice Generated',
                    text: `Prescription finalized under invoice ${data.invoice_number}.`,
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-4 shadow-lg p-4 border-0',
                        title: 'fw-bold text-dark fs-5 mb-2',
                        confirmButton: 'btn btn-teal px-4 py-2.5 rounded-3 fw-semibold shadow-sm'
                    }
                });

                // Bring the newly generated invoice into view
                const invoiceEl = document.getElementById('invoice-display-container');
                if (invoiceEl) {
                    setTimeout(() => invoiceEl.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    }), 300);
                }
            })
            .catch(err => {
                if (err && err.csrf_hash) {
                    csrfHash = err.csrf_hash;
                }
                const errMsg = err && err.message ? err.message : 'Database write error occurred.';
                Swal.fire({
                    icon: 'error',
                    title: 'Prescription Save Failed',
                    html: errMsg,
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-4 shadow-lg p-4 border-0',
                        title: 'fw-bold text-dark fs-5 mb-2',
                        confirmButton: 'btn btn-danger px-4 py-2.5 rounded-3 fw-semibold shadow-sm'
                    }
                });
            });
    }

    /**
     * Render HTML card for finalized invoice (full-width, sits below the workspace grid)
     */
    function renderInvoiceHTML(tab) {
        const container = document.getElementById('invoice-display-container');
        if (!container || !tab.invoice_data) return;

        const data = tab.invoice_data;

        let medRows = '';
        if (data.items && data.items.length > 0) {
            data.items.forEach((item, index) => {
                medRows += `
                <tr>
                    <td class="text-center small" data-label="#">${index + 1}</td>
                    <td class="fw-semibold text-dark small" data-label="Medicine">${htmlEscape(item.drug_name)}</td>
                    <td class="small" data-label="Dosage">${htmlEscape(item.dosage)}</td>
                    <td class="small" data-label="Frequency">${htmlEscape(item.frequency)}</td>
                    <td class="small" data-label="Duration">${htmlEscape(item.duration)}</td>
                    <td class="small" data-label="Instructions">${htmlEscape(item.special_instructions || '—')}</td>
                </tr>
                `;
            });
        } else {
            medRows = `<tr><td colspan="6" class="text-center text-muted">No medicines prescribed.</td></tr>`;
        }

        container.innerHTML = `
        <div class="invoice-card" id="invoice_container_${tab.id}">
            <!-- Invoice Document Wrapper (Targeted by printing) -->
            <div class="invoice-print-container" id="print_area_${tab.id}">
                <!-- Header block -->
                <div class="row align-items-center mb-4 pb-3 border-bottom invoice-header-row">
                    <div class="col-12 col-md-4 header-col-brand">
                        <h4 class="invoice-brand mb-1 text-teal fw-bold d-flex align-items-center gap-2" style="color: #0f766e;">
                            <i class="bi bi-heart-pulse-fill"></i> DDI Checker
                        </h4>
                        <small class="text-secondary small">Clinical Decision Support Portal</small>
                    </div>
                    <div class="col-12 col-md-4 text-center header-col-middle">
                        <h5 class="mb-1 text-dark fw-bold" style="font-size: 1rem; color: #0f766e !important;">${htmlEscape((data.doctor && data.doctor.hospital_clinic) || currentDoctorHospital || '')}</h5>
                        <p class="text-secondary mb-0 small" style="font-size: 0.75rem; line-height: 1.3;">${htmlEscape((data.doctor && data.doctor.address) || currentDoctorAddress || '').replace(/\n/g, '<br>')}</p>
                    </div>
                    <div class="col-12 col-md-4 text-end header-col-label">
                        <h4 class="invoice-label mb-1 fw-bold text-dark" style="letter-spacing: 0.05em; font-size: 1.25rem;">PRESCRIPTION</h4>
                        <div class="text-secondary small"><strong>Invoice No:</strong> <span class="text-dark fw-bold">${htmlEscape(data.invoice_number)}</span></div>
                        <div class="text-secondary small"><strong>Date:</strong> <span class="text-dark">${htmlEscape(data.visit_date)}</span></div>
                    </div>
                </div>

                <!-- Doctor and Patient Block -->
                <div class="row mb-4">
                    <div class="col-6 border-end">
                        <h6 class="fw-bold text-teal mb-2" style="font-size:0.85rem;"><i class="bi bi-person-workspace"></i> Practitioner Details</h6>
                        <div class="fw-bold text-dark fs-6">${htmlEscape(data.doctor.name)}</div>
                        <div class="text-secondary small mb-0.5">${htmlEscape(data.doctor.qualification)} — ${htmlEscape(data.doctor.specialization)}</div>
                        <div class="text-secondary small mb-0.5"><strong>Reg No:</strong> ${htmlEscape(data.doctor.registration_number)}</div>
                        <div class="text-secondary small">${htmlEscape(data.doctor.hospital_clinic)}</div>
                    </div>
                    <div class="col-6 ps-4">
                        <h6 class="fw-bold text-teal mb-2" style="font-size:0.85rem;"><i class="bi bi-person-circle"></i> Patient Details</h6>
                        <div class="fw-bold text-dark fs-6">${htmlEscape(data.patient.full_name)}</div>
                        <div class="text-secondary small mb-0.5"><strong>Age / Gender:</strong> ${htmlEscape(data.patient.age)} Years / ${htmlEscape(data.patient.gender)}</div>
                        <div class="text-secondary small"><strong>Contact:</strong> ${htmlEscape(data.patient.contact_number || 'N/A')}</div>
                    </div>
                </div>

                <!-- Medications Table -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped align-middle invoice-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;" class="text-center">#</th>
                                <th style="width: 35%;">Medicine Name</th>
                                <th style="width: 15%;">Dosage</th>
                                <th style="width: 20%;">Frequency</th>
                                <th style="width: 15%;">Duration</th>
                                <th style="width: 15%;">Instructions</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${medRows}
                        </tbody>
                    </table>
                </div>

                <!-- Footer Signatures -->
                <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mt-4 pt-3 border-top">
                    <div>
                        <p class="text-secondary mb-0" style="font-size: 0.72rem; line-height: 1.45;">
                            <strong>Disclaimer:</strong> Generated by DDI Checker Clinical Decision Portal.<br>
                            This is an official medical prescription. For clinical use.
                        </p>
                    </div>
                    <div class="text-end pe-2 ms-auto">
                        ${(data.doctor.signature || currentDoctorSignature) ? `
                            <div class="mb-1 text-center" style="max-height: 55px; overflow: hidden;">
                                <img src="${htmlEscape(data.doctor.signature || currentDoctorSignature)}" alt="Doctor Signature" style="max-height: 50px; max-width: 160px; object-fit: contain;">
                            </div>
                        ` : '<div style="height: 50px;"></div>'}
                        <div class="text-dark small" style="border-top: 1.5px solid #94a3b8; width: 180px; padding-top: 6px; font-weight: 500;">
                            Physician Signature
                        </div>
                    </div>
                </div>
            </div>

            <!-- Print/Start New Rx Button Bar (Hidden when printing) -->
            <div class="d-flex justify-content-end flex-wrap gap-2 mt-4 pt-3 border-top print-hide">
                <button type="button" class="btn btn-outline-teal fw-semibold d-flex align-items-center justify-content-center gap-1.5" onclick="handlePrintInvoice('print_area_${tab.id}')">
                    <i class="bi bi-printer"></i>
                    <span>Print Prescription</span>
                </button>
                <button type="button" class="btn btn-teal fw-semibold d-flex align-items-center justify-content-center gap-1.5" onclick="handleStartNewRx('${tab.id}')">
                    <i class="bi bi-plus-circle"></i>
                    <span>Start New Rx</span>
                </button>
            </div>
        </div>
        `;
    }

    /**
     * Print the specified invoice node
     */
    function handlePrintInvoice(printAreaId) {
        // Clear past targets
        const allPrintAreas = document.querySelectorAll('.invoice-print-container');
        allPrintAreas.forEach(el => el.classList.remove('print-invoice-area'));

        const target = document.getElementById(printAreaId);
        if (target) {
            target.classList.add('print-invoice-area');
            window.print();
        }
    }

    /**
     * Start New Rx by closing the current finalized tab and launching a fresh tab
     */
    function handleStartNewRx(tabId) {
        const tabIndex = rxTabs.findIndex(t => t.id === tabId);
        if (tabIndex !== -1) {
            // Close current finalized tab
            rxTabs.splice(tabIndex, 1);

            // Re-number other remaining tabs
            rxTabs.forEach((tab, i) => {
                tab.label = `Rx ${i + 1}`;
            });

            // Set next focus tab
            if (rxTabs.length > 0) {
                activeTabId = rxTabs[0].id;
            } else {
                activeTabId = null;
            }
        }

        // Start a fresh new blank tab
        addNewTab();
    }

    /**
     * JS HTML character escaping helper
     */
    function htmlEscape(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
</script>