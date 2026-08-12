<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Require Admin_Controller if not already loaded
if (!class_exists('Admin_Controller')) {
    require_once APPPATH . 'core/Admin_Controller.php';
}

/**
 * Admin Dashboard Controller
 * 
 * Handles the administrative overview, metrics, and recent activity logs.
 */
class Dashboard extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        // Base Admin_Controller verifies session authentication and admin role
    }

    /**
     * Admin Dashboard Landing Page
     */
    public function index() {
        $data['title'] = 'Dashboard';
        $data['breadcrumb'] = 'Dashboard';

        // Fetch real database counts via General_model
        $total_drugs         = $this->General_model->getCount('drugs');
        $active_drugs        = $this->General_model->getCount('drugs', ['is_active' => 1]);
        $total_interactions  = $this->General_model->getCount('interactions');
        $active_interactions = $this->General_model->getCount('interactions', ['is_active' => 1]);
        $severe_alerts       = $this->General_model->getCount('interactions', ['severity' => 'Severe']);
        $moderate_alerts     = $this->General_model->getCount('interactions', ['severity' => 'Moderate']);
        $mild_alerts         = $this->General_model->getCount('interactions', ['severity' => 'Mild']);
        $total_doctors       = $this->General_model->getCount('users', ['role' => 0]); // 0 = doctor
        $active_doctors      = $this->General_model->getCount('users', ['role' => 0, 'is_active' => 1]);

        // Real Admin system statistics
        $data['stats'] = [
            'total_drugs'         => $total_drugs,
            'active_drugs'        => $active_drugs,
            'total_interactions'  => $total_interactions,
            'active_interactions' => $active_interactions,
            'severe_alerts'       => $severe_alerts,
            'moderate_alerts'     => $moderate_alerts,
            'mild_alerts'         => $mild_alerts,
            'total_doctors'       => $total_doctors,
            'active_doctors'      => $active_doctors
        ];

        // Fetch recent 6 interaction rules from database
        $data['recent_interactions'] = $this->General_model->getInteractions(6, 0);

        // Load admin layouts and dashboard view
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/dashboard/index', $data);
        $this->load->view('admin/layout/footer', $data);
    }
}
