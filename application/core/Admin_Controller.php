<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base Admin Controller
 * 
 * All admin controllers must extend this class to ensure
 * that the user is authenticated and has role = 1 (1 = admin).
 */
class Admin_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // Load database, session library, General_model and essential helpers
        $this->load->database();
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form', 'security']);
        $this->load->model('General_model');

        // Prevent browser caching of restricted admin pages
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

        // Check if user is logged in and has role 1 (1 = admin)
        $is_logged_in = $this->session->userdata('admin_logged_in');
        $role         = $this->session->userdata('admin_role');

        if (!$is_logged_in || (int)$role !== 1) { // 1 = admin
            $this->session->set_flashdata('error', 'Please sign in with an administrator account to continue.');
            redirect('admin');
        }
    }
}
