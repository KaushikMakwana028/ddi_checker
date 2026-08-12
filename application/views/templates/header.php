<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? html_escape($title) : 'DDI Checker'; ?> - Clinical Drug-Drug Interaction</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            min-height: 100vh;
        }
        
        /* Auth Styles */
        .auth-bg {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #115e59 100%);
            padding: 20px;
        }
        .auth-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            background: #ffffff;
            width: 100%;
            max-width: 450px;
            padding: 2.5rem;
        }
        
        /* Dashboard Sidebar & Layout */
        .app-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 280px;
            background-color: #0f172a;
            color: #94a3b8;
            min-height: 100vh;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1050; /* Keep sidebar above backdrop */
            padding: 0;
            border-right: 1px solid #1e293b;
            transition: left 0.3s ease;
        }
        
        .sidebar-brand {
            padding: 24px;
            font-size: 1.25rem;
            font-weight: 700;
            color: #f8fafc;
            border-bottom: 1px solid #1e293b;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .sidebar-menu {
            padding: 20px 0;
            list-style: none;
            margin: 0;
        }
        
        .sidebar-menu-item a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: #94a3b8;
            text-decoration: none;
            font-weight: 500;
            border-left: 4px solid transparent;
            transition: all 0.2s ease;
        }
        
        .sidebar-menu-item a:hover {
            color: #ffffff;
            background-color: #1e293b;
            border-left-color: #0d9488;
        }
        
        .sidebar-menu-item.active a {
            color: #ffffff;
            background-color: #1e293b;
            border-left-color: #0d9488;
            font-weight: 600;
        }
        
        .main-content {
            flex: 1;
            margin-left: 280px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8fafc;
            transition: margin-left 0.3s ease;
        }
        
        .navbar-custom {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .navbar-custom .welcome-text {
            font-size: 1rem;
            color: #64748b;
        }
        
        .navbar-custom .welcome-text strong {
            color: #0f172a;
        }
        
        .page-body {
            padding: 32px;
            flex: 1;
        }
        
        /* Alerts and cards styling */
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
        
        .btn-teal {
            background-color: #0d9488;
            border-color: #0d9488;
            color: #ffffff;
            font-weight: 500;
        }
        .btn-teal:hover, .btn-teal:focus {
            background-color: #0f766e;
            border-color: #0f766e;
            color: #ffffff;
        }
        
        .avatar-circle {
            width: 36px;
            height: 36px;
            background-color: #ccfbf1;
            color: #0f766e;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: 600;
            font-size: 0.9rem;
            overflow: hidden;
        }
        .avatar-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Sidebar backdrop for mobile */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(15, 23, 42, 0.4);
            z-index: 1040;
            display: none;
            backdrop-filter: blur(4px);
            transition: opacity 0.3s ease;
        }
        .sidebar-backdrop.show {
            display: block;
        }

        /* Responsive Breakpoints */
        @media (max-width: 991.98px) {
            .sidebar {
                left: -280px;
            }
            .sidebar.active {
                left: 0;
            }
            .main-content {
                margin-left: 0;
            }
            .navbar-custom {
                padding: 16px 20px;
            }
            .page-body {
                padding: 20px;
            }
        }

        /* Custom dropdown styles matching the reference image */
        .dropdown-toggle::after {
            display: none !important;
        }
        
        .navbar-custom .dropdown .btn {
            border-color: #cbd5e1 !important;
            transition: all 0.2s ease;
        }
        
        .navbar-custom .dropdown .btn:hover {
            background-color: #f8fafc;
            border-color: #94a3b8 !important;
        }
        
        .navbar-custom .dropdown .dropdown-item {
            color: #334155;
            transition: all 0.15s ease;
        }
        
        .navbar-custom .dropdown .dropdown-item:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }
        
        .navbar-custom .dropdown .dropdown-item.text-danger:hover {
            background-color: #fef2f2;
            color: #dc2626;
        }
        
        #dropdownChevron {
            transition: transform 0.2s ease;
        }
        
        .dropdown-toggle[aria-expanded="true"] #dropdownChevron {
            transform: rotate(180deg);
        }

    </style>
</head>
<body>

<?php if ($this->session->userdata('logged_in')): ?>
<?php
$profile_image_url = '';
$ci =& get_instance();
$ci->load->model('General_model');
$current_user = $ci->General_model->getById('users', $this->session->userdata('user_id'));
if ($current_user && !empty($current_user->profile_image) && file_exists(FCPATH . $current_user->profile_image)) {
    $profile_image_url = base_url($current_user->profile_image);
}
?>
<div class="app-container">
    <!-- Sidebar -->
    <div class="sidebar" id="appSidebar">
        <div class="sidebar-brand">
            <i class="bi bi-shield-plus fs-3" style="color: #0d9488;"></i>
            <span>DDI Checker</span>
        </div>
        <ul class="sidebar-menu">
            <li class="sidebar-menu-item <?php echo ($this->uri->segment(1) === 'dashboard') ? 'active' : ''; ?>">
                <a href="<?php echo base_url('dashboard'); ?>">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($this->uri->segment(1) === 'drug-entry' || $this->uri->segment(1) === 'DrugEntry') ? 'active' : ''; ?>">
                <a href="<?php echo base_url('drug-entry'); ?>">
                    <i class="bi bi-capsule"></i> Drug Entry
                </a>
            </li>
            <li class="sidebar-menu-item">
                <a href="#">
                    <i class="bi bi-plus-circle"></i> New Prescription
                </a>
            </li>
            <li class="sidebar-menu-item">
                <a href="#">
                    <i class="bi bi-clock-history"></i> History
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Top Navbar -->
        <header class="navbar-custom">
            <div class="d-flex align-items-center">
                <!-- Hamburger Menu Button -->
                <button class="btn btn-link text-dark border-0 p-0 me-3 d-lg-none" id="sidebarToggle" aria-label="Toggle Sidebar">
                    <i class="bi bi-list fs-3"></i>
                </button>
                <i class="bi bi-activity me-2 fs-4" style="color: #0d9488;"></i>
                <span class="fw-semibold text-secondary d-none d-sm-inline">Drug-Drug Interaction Portal</span>
                <span class="fw-semibold text-secondary d-inline d-sm-none">DDI Portal</span>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                
                <div class="dropdown">
                    <?php
                    $name = $this->session->userdata('name');
                    $initials = '';
                    if (!empty($name)) {
                        $words = explode(' ', $name);
                        foreach ($words as $w) {
                            $initials .= substr($w, 0, 1);
                        }
                        $initials = strtoupper(substr($initials, 0, 2));
                    } else {
                        $initials = 'U';
                    }
                    ?>
                    <button class="btn btn-outline-secondary d-flex align-items-center gap-2 rounded-pill px-3 py-1.5 dropdown-toggle border" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #ffffff; border-color: #cbd5e1 !important; color: #0f172a; font-weight: 500; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: all 0.2s ease;">
                        <div class="avatar-circle" style="width: 28px; height: 28px; font-size: 0.75rem;">
                            <?php if (!empty($profile_image_url)): ?>
                                <img src="<?php echo $profile_image_url; ?>" alt="Profile">
                            <?php else: ?>
                                <?php echo $initials; ?>
                            <?php endif; ?>
                        </div>
                        <span class="d-none d-sm-inline text-truncate" style="max-width: 140px; font-weight: 600; font-size: 0.9rem;">
                            <?php echo html_escape($name); ?>
                        </span>
                        <i class="bi bi-chevron-down ms-1 small text-muted" id="dropdownChevron"></i>
                    </button>
                    
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-2" aria-labelledby="profileDropdown" style="border-radius: 16px; min-width: 270px; background-color: #ffffff; border: 1px solid #f1f5f9 !important;">
                        <!-- Header with Avatar and User Info -->
                        <li class="px-3 py-3 d-flex align-items-center gap-3">
                            <div class="avatar-circle shadow-sm" style="width: 44px; height: 44px; background-color: #ccfbf1; font-size: 1.1rem;">
                                <?php if (!empty($profile_image_url)): ?>
                                    <img src="<?php echo $profile_image_url; ?>" alt="Profile">
                                <?php else: ?>
                                    <?php echo $initials; ?>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex flex-column text-truncate">
                                <h6 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 0.95rem;"><?php echo html_escape($name); ?></h6>
                                <small class="text-muted text-truncate" style="font-size: 0.82rem;"><?php echo html_escape($this->session->userdata('email')); ?></small>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider my-2" style="border-color: #f1f5f9;"></li>
                        <!-- Menu items -->
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2.5 py-2 px-3 rounded-3" href="<?php echo base_url('profile'); ?>">
                                <i class="bi bi-person text-secondary fs-5"></i>
                                <span style="font-weight: 500; font-size: 0.92rem; color: #334155;">Profile</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2.5 py-2 px-3 rounded-3" href="<?php echo base_url('profile#security'); ?>" id="changePasswordMenuLink">
                                <i class="bi bi-shield-lock text-secondary fs-5"></i>
                                <span style="font-weight: 500; font-size: 0.92rem; color: #334155;">Change Password</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-2" style="border-color: #f1f5f9;"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2.5 py-2 px-3 rounded-3 text-danger" href="<?php echo base_url('auth/logout'); ?>">
                                <i class="bi bi-box-arrow-right fs-5"></i>
                                <span style="font-weight: 500; font-size: 0.92rem;">Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>
        
        <!-- Page Body starts -->
        <main class="page-body">
<?php else: ?>
    <!-- For guest pages (login/register) -->
    <div class="auth-bg">
<?php endif; ?>
