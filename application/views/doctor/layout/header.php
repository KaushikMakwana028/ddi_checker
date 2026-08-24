<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? html_escape($title) : 'Doctor Portal'; ?> - DDI Checker</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary-teal: #0f766e;
            --primary-teal-hover: #0d9488;
            --primary-teal-light: rgba(15, 118, 110, 0.12);
            --sidebar-bg: #0f172a;
            --sidebar-border: #1e293b;
            --sidebar-hover: #1e293b;
            --text-muted-dark: #94a3b8;
            --body-bg: #f8fafc;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--body-bg);
            color: #0f172a;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* App Container & Sidebar */
        .app-container {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        .sidebar {
            width: 280px;
            background-color: var(--sidebar-bg);
            color: var(--text-muted-dark);
            min-height: 100vh;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1050;
            padding: 0;
            border-right: 1px solid var(--sidebar-border);
            display: flex;
            flex-direction: column;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.12);
        }

        .sidebar-brand {
            padding: 22px 24px;
            font-size: 1.25rem;
            font-weight: 700;
            color: #f8fafc;
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-icon-wrapper {
            width: 38px;
            height: 38px;
            background-color: var(--primary-teal-light);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-name {
            letter-spacing: -0.3px;
        }

        .sidebar-menu {
            padding: 16px 0;
            list-style: none;
            margin: 0;
            flex-grow: 1;
        }

        .sidebar-menu-item a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 13px 24px;
            color: var(--text-muted-dark);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            border-left: 4px solid transparent;
            transition: all 0.2s ease;
        }

        .sidebar-menu-item a:hover {
            color: #ffffff;
            background-color: var(--sidebar-hover);
            border-left-color: var(--primary-teal);
        }

        .sidebar-menu-item.active a {
            color: #ffffff;
            background-color: var(--sidebar-hover);
            border-left-color: var(--primary-teal);
            font-weight: 600;
        }

        .sidebar-menu-item a i {
            font-size: 1.2rem;
            min-width: 24px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: var(--body-bg);
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: calc(100% - 280px);
        }

        .navbar-custom {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .page-body {
            padding: 32px;
            flex: 1;
        }

        /* Accent & Buttons */
        .btn-teal {
            background-color: var(--primary-teal);
            border-color: var(--primary-teal);
            color: #ffffff;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .btn-teal:hover, .btn-teal:focus {
            background-color: var(--primary-teal-hover);
            border-color: var(--primary-teal-hover);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.25);
        }

        .text-teal {
            color: var(--primary-teal) !important;
        }

        /* Stat Cards */
        .stat-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            background-color: #ffffff;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
        }

        /* Avatar circle */
        .avatar-circle {
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: 600;
            overflow: hidden;
            flex-shrink: 0;
        }
        .avatar-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* Action UI Buttons */
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            font-size: 0.82rem;
            font-weight: 500;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            white-space: nowrap;
            transition: all 0.15s ease-in-out;
            cursor: pointer;
            text-decoration: none;
            line-height: 1.3;
        }
        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.06);
        }
        .action-btn-edit {
            color: #0f766e;
            border-color: #99f6e4;
            background-color: #f0fdfa;
        }
        .action-btn-edit:hover {
            color: #ffffff;
            background-color: #0d9488;
            border-color: #0d9488;
        }
        .action-btn-warning {
            color: #b45309;
            border-color: #fde68a;
            background-color: #fffbeb;
        }
        .action-btn-warning:hover {
            color: #ffffff;
            background-color: #d97706;
            border-color: #d97706;
        }
        .action-btn-success {
            color: #15803d;
            border-color: #bbf7d0;
            background-color: #f0fdf4;
        }
        .action-btn-success:hover {
            color: #ffffff;
            background-color: #16a34a;
            border-color: #16a34a;
        }
        .action-btn-danger {
            color: #b91c1c;
            border-color: #fecaca;
            background-color: #fef2f2;
        }
        .action-btn-danger:hover {
            color: #ffffff;
            background-color: #dc2626;
            border-color: #dc2626;
        }

        /* Sidebar backdrop for mobile */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(15, 23, 42, 0.6);
            z-index: 1040;
            display: none;
            backdrop-filter: blur(4px);
            transition: opacity 0.3s ease;
        }
        .sidebar-backdrop.show {
            display: block;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                left: -280px;
            }
            .sidebar.active {
                left: 0;
            }
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            .navbar-custom {
                padding: 12px 18px;
            }
            .page-body {
                padding: 18px 14px;
            }
        }

        .dropdown-toggle::after {
            display: none !important;
        }
    </style>
</head>
<body>

<div class="app-container">
    <!-- Combined Doctor Sidebar -->
    <aside class="sidebar" id="appSidebar">
        <div class="sidebar-brand">
            <div class="d-flex align-items-center gap-2">
                <div class="brand-icon-wrapper">
                    <i class="bi bi-heart-pulse-fill fs-4" style="color: #0d9488;"></i>
                </div>
                <div>
                    <span class="brand-name">DDI Checker</span>
                    <span class="badge rounded-pill ms-1" style="background-color: #0d9488; color: #ffffff; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px; padding: 3px 8px;">DOCTOR</span>
                </div>
            </div>
            <!-- Close button for mobile -->
            <button class="btn btn-sm text-white d-lg-none p-1" id="sidebarCloseBtn" aria-label="Close Sidebar">
                <i class="bi bi-x-lg fs-5"></i>
            </button>
        </div>

        <?php
        $current_segment2 = $this->uri->segment(2);
        $current_segment1 = $this->uri->segment(1);
        ?>

        <ul class="sidebar-menu">
            <li class="sidebar-menu-item <?php echo ($current_segment2 === 'dashboard' || ($current_segment1 === 'doctor' && empty($current_segment2))) ? 'active' : ''; ?>">
                <a href="<?php echo base_url('doctor/dashboard'); ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="sidebar-menu-item <?php echo ($current_segment2 === 'prescription-desk' || $current_segment2 === 'prescriptions') ? 'active' : ''; ?>">
                <a href="<?php echo base_url('doctor/prescription-desk'); ?>">
                    <i class="bi bi-prescription"></i>
                    <span>Prescription Desk</span>
                </a>
            </li>

            <li class="sidebar-menu-item <?php echo ($current_segment2 === 'history') ? 'active' : ''; ?>">
                <a href="<?php echo base_url('doctor/history'); ?>">
                    <i class="bi bi-clock-history"></i>
                    <span>Patient History</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer mt-auto p-3 border-top" style="border-color: #1e293b !important;">
            <a href="<?php echo base_url('doctor/logout'); ?>" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2 rounded-3 py-2" style="font-weight: 500; font-size: 0.9rem; border-color: rgba(239, 68, 68, 0.4);">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Shell -->
    <div class="main-content">
        <!-- Top Navigation Bar -->
        <header class="navbar-custom">
            <div class="d-flex align-items-center gap-2">
                <!-- Hamburger Menu Button -->
                <button class="btn btn-light border d-lg-none p-1.5 px-2 rounded-3 me-2" id="sidebarToggle" aria-label="Toggle Sidebar">
                    <i class="bi bi-list fs-5"></i>
                </button>

                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                            <li class="breadcrumb-item"><a href="<?php echo base_url('doctor/dashboard'); ?>" class="text-teal text-decoration-none fw-medium">Clinical Portal</a></li>
                            <?php if (isset($breadcrumb) && !empty($breadcrumb)): ?>
                                <li class="breadcrumb-item active text-secondary" aria-current="page"><?php echo html_escape($breadcrumb); ?></li>
                            <?php else: ?>
                                <li class="breadcrumb-item active text-secondary" aria-current="page"><?php echo isset($title) ? html_escape($title) : 'Dashboard'; ?></li>
                            <?php endif; ?>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Right Profile Dropdown -->
            <div class="d-flex align-items-center gap-3">
                <?php
                $ci =& get_instance();
                $ci->load->model('General_model');
                $user_id = $this->session->userdata('doctor_user_id');
                $doc_user = $ci->General_model->getById('users', $user_id);
                $doc_prof = $ci->General_model->getOne('doctor_profiles', ['user_id' => $user_id]);
                
                $name = ($doc_user && !empty($doc_user->name)) ? $doc_user->name : ($this->session->userdata('doctor_name') ?: 'Practitioner');
                $email = ($doc_user && !empty($doc_user->email)) ? $doc_user->email : ($this->session->userdata('doctor_email') ?: '');

                $initials = '';
                if (!empty($name)) {
                    $words = explode(' ', $name);
                    foreach ($words as $w) {
                        $initials .= substr($w, 0, 1);
                    }
                    $initials = strtoupper(substr($initials, 0, 2));
                } else {
                    $initials = 'DR';
                }

                $profile_image_url = '';
                if ($doc_user && !empty($doc_user->profile_image) && file_exists(FCPATH . $doc_user->profile_image)) {
                    $profile_image_url = base_url($doc_user->profile_image);
                }
                ?>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary d-flex align-items-center gap-2 rounded-pill px-2.5 py-1.5 dropdown-toggle border shadow-sm" type="button" id="doctorProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #ffffff; border-color: #e2e8f0 !important; color: #0f172a; font-weight: 500;">
                        <div class="avatar-circle" style="width: 32px; height: 32px; background-color: #0d9488; color: #ffffff; font-size: 0.8rem;">
                            <?php if (!empty($profile_image_url)): ?>
                                <img src="<?php echo $profile_image_url; ?>" alt="Doctor Profile">
                            <?php else: ?>
                                <?php echo $initials; ?>
                            <?php endif; ?>
                        </div>
                        <span class="d-none d-sm-inline text-truncate ms-1" style="max-width: 140px; font-weight: 600; font-size: 0.9rem;">
                            Dr. <?php echo html_escape($name); ?>
                        </span>
                        <i class="bi bi-chevron-down ms-1 small text-muted"></i>
                    </button>
                    
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-2" aria-labelledby="doctorProfileDropdown" style="border-radius: 16px; min-width: 270px; background-color: #ffffff; border: 1px solid #e2e8f0 !important;">
                        <li class="px-3 py-3 d-flex align-items-center gap-3">
                            <div class="avatar-circle shadow-sm" style="width: 44px; height: 44px; min-width: 44px; background-color: #0d9488; color: #ffffff; font-size: 1rem;">
                                <?php if (!empty($profile_image_url)): ?>
                                    <img src="<?php echo $profile_image_url; ?>" alt="Doctor Profile">
                                <?php else: ?>
                                    <?php echo $initials; ?>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex flex-column text-truncate" style="min-width: 0;">
                                <h6 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 0.92rem;">Dr. <?php echo html_escape($name); ?></h6>
                                <small class="text-muted text-truncate" style="font-size: 0.8rem;"><?php echo html_escape($email); ?></small>
                                <span class="badge bg-teal-subtle text-teal align-self-start mt-1" style="font-size: 0.65rem; background-color: #ccfbf1; color: #0f766e;">
                                    <?php echo !empty($doc_prof->specialization) ? html_escape($doc_prof->specialization) : 'Doctor'; ?>
                                </span>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider my-2" style="border-color: #f1f5f9;"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2.5 py-2 px-3 rounded-3 text-dark" href="<?php echo base_url('doctor/profile'); ?>">
                                <i class="bi bi-person-gear fs-5 text-teal" style="color: #0f766e;"></i>
                                <span style="font-weight: 500; font-size: 0.92rem;">My Profile</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-2" style="border-color: #f1f5f9;"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2.5 py-2 px-3 rounded-3 text-danger" href="<?php echo base_url('doctor/logout'); ?>">
                                <i class="bi bi-box-arrow-right fs-5"></i>
                                <span style="font-weight: 500; font-size: 0.92rem;">Sign Out</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>
        
        <!-- Page Body starts -->
        <main class="page-body">
