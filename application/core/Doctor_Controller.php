<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base Doctor Controller
 * 
 * All doctor controllers extend this class to ensure
 * the user is authenticated and has role = 0 (0 = doctor).
 */
class Doctor_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // Load database, session library, General_model and essential helpers
        $this->load->database();
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form', 'security']);
        $this->load->model('General_model');

        // Prevent browser caching of restricted clinical doctor pages
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

        // Check if user is logged in and has role 0 (0 = doctor)
        $is_logged_in = $this->session->userdata('doctor_logged_in');
        $role         = $this->session->userdata('doctor_role');

        if (!$is_logged_in || (int)$role !== 0) { // 0 = doctor
            $this->session->set_flashdata('error', 'Please sign in with a registered doctor account to continue.');
            redirect('doctor');
        }
    }
}
