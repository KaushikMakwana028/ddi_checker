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

        $user_id = $this->session->userdata('user_id');
        $data['doctor'] = $this->General_model->getOne('users', ['id' => $user_id]);
        $data['profile'] = $this->General_model->getOne('doctor_profiles', ['user_id' => $user_id]);

        // Load doctor layouts and dashboard view
        $this->load->view('doctor/layout/header', $data);
        $this->load->view('doctor/dashboard/index', $data);
        $this->load->view('doctor/layout/footer', $data);
    }
}
