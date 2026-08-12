<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Require Admin_Controller & Doctor_Controller so they are available globally to all subfolder controllers
if (!class_exists('Admin_Controller')) {
    require_once APPPATH . 'core/Admin_Controller.php';
}
if (!class_exists('Doctor_Controller')) {
    require_once APPPATH . 'core/Doctor_Controller.php';
}

/**
 * Base Application Controller
 */
class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // Load the session library and url helper
        $this->load->library('session');
        $this->load->helper(['url', 'form', 'security']);
    }
}
