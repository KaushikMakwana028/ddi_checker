<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Require Doctor_Controller if not already loaded
if (!class_exists('Doctor_Controller')) {
    require_once APPPATH . 'core/Doctor_Controller.php';
}

/**
 * Doctor Dashboard Controller
 * 
 * Landing portal for registered medical practitioners to access the
 * Clinical Decision Support System and Prescription Desk.
 */
class Dashboard extends Doctor_Controller {

    public function __construct() {
        parent::__construct();
        // Doctor session authentication is automatically enforced by Doctor_Controller
    }

    /**
     * Doctor Portal Landing Page
     */
    public function index() {
        $data['title']      = 'Doctor Dashboard';
        $data['breadcrumb'] = 'Dashboard';

        $user_id = $this->session->userdata('doctor_user_id');
        $data['doctor'] = $this->General_model->getOne('users', ['id' => $user_id]);
        $data['profile'] = $this->General_model->getOne('doctor_profiles', ['user_id' => $user_id]);

        // Calculate doctor stats
        $data['stats'] = [
            'total_patients'      => $this->db->where('doctor_id', $user_id)->count_all_results('patients'),
            'total_prescriptions' => $this->db->where('doctor_id', $user_id)->where('invoice_number IS NOT NULL')->count_all_results('prescriptions'),
            'prescriptions_today' => $this->db->where('doctor_id', $user_id)->where('invoice_number IS NOT NULL')->where('DATE(created_at)', date('Y-m-d'))->count_all_results('prescriptions'),
            'total_drugs'         => $this->General_model->getCount('drugs')
        ];

        // Load doctor layouts and dashboard view
        $this->load->view('doctor/layout/header', $data);
        $this->load->view('doctor/dashboard/index', $data);
        $this->load->view('doctor/layout/footer', $data);
    }
}
