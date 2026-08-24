<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Require Admin_Controller if needed
if (!class_exists('Admin_Controller')) {
    require_once APPPATH . 'core/Admin_Controller.php';
}

/**
 * Admin Login Controller
 * 
 * Handles Admin authentication: login, registration, and logout operations.
 * Uses role = 1 (1 = admin, 0 = doctor).
 */
class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // Load database, session, validation, and general model
        $this->load->database();
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form', 'security']);
        $this->load->model('General_model');

        // Prevent browser caching of authentication views
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    }

    /**
     * Default Index route
     */
    public function index() {
        $this->login();
    }

    /**
     * Admin Login Page & Form Handler
     */
    public function login() {
        // If already authenticated as an admin (role = 1), redirect to admin dashboard
        if ($this->session->userdata('admin_logged_in') && (int)$this->session->userdata('admin_role') === 1) { // 1 = admin
            redirect('admin/dashboard');
        }

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === TRUE) {
            $email    = $this->input->post('email', TRUE);
            $password = $this->input->post('password');

            // Strictly check for user WHERE email = ? AND role = 1 (1 = admin, 0 = doctor)
            $admin_user = $this->General_model->getOne('users', [
                'email' => $email,
                'role'  => 1 // 1 = admin
            ]);

            if ($admin_user && password_verify($password, $admin_user->password)) {
                // Check if account is active
                if ((int)$admin_user->is_active === 0) {
                    $this->session->set_flashdata('error', 'Your admin account has been disabled. Please contact system management.');
                    redirect('admin/login');
                }

                // Set admin session userdata (role = 1)
                $this->session->set_userdata([
                    'admin_user_id'       => $admin_user->id,
                    'admin_name'          => $admin_user->name,
                    'admin_email'         => $admin_user->email,
                    'admin_role'          => 1, // 1 = admin, 0 = doctor
                    'admin_profile_image' => $admin_user->profile_image,
                    'admin_logged_in'     => TRUE
                ]);

                redirect('admin/dashboard');
            } else {
                // Check if this email belongs to a Doctor account
                $doctor_check = $this->General_model->getOne('users', ['email' => $email, 'role' => 0]);
                if ($doctor_check && password_verify($password, $doctor_check->password)) {
                    $this->session->set_flashdata('error', 'This account is registered as a Doctor. Please sign in via the Doctor Portal at /doctor/login.');
                } else {
                    $this->session->set_flashdata('error', 'Invalid email or password. Please try again.');
                }

                redirect('admin/login');
            }
        } else {
            $data['title'] = 'Admin Access';
            $this->load->view('admin/auth/login', $data);
        }
    }

    /**
     * Admin Registration Page & Form Handler
     */
    public function register() {
        // If already authenticated as an admin, redirect to admin dashboard
        if ($this->session->userdata('admin_logged_in') && (int)$this->session->userdata('admin_role') === 1) { // 1 = admin
            redirect('admin/dashboard');
        }

        $this->form_validation->set_rules('name', 'Name', 'required|trim|max_length[150]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]|trim|max_length[150]');
        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|max_length[20]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');

        if ($this->form_validation->run() === TRUE) {
            $name     = $this->input->post('name', TRUE);
            $email    = $this->input->post('email', TRUE);
            $mobile   = $this->input->post('mobile', TRUE);
            $password = $this->input->post('password');

            $new_admin = [
                'name'       => $name,
                'email'      => $email,
                'mobile'     => !empty($mobile) ? $mobile : NULL,
                'password'   => password_hash($password, PASSWORD_BCRYPT),
                'role'       => 1, // 1 = admin, 0 = doctor
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $insert_id = $this->General_model->insert('users', $new_admin);

            if ($insert_id) {
                $this->session->set_flashdata('success', 'Admin registration successful! Please sign in with your credentials.');
                redirect('admin/login');
            } else {
                $this->session->set_flashdata('error', 'Failed to register admin account. Please try again.');
                redirect('admin/register');
            }
        } else {
            $data['title'] = 'Admin Registration';
            $this->load->view('admin/auth/register', $data);
        }
    }

    /**
     * Admin Logout
     */
    public function logout() {
        $this->session->unset_userdata(['admin_user_id', 'admin_name', 'admin_email', 'admin_role', 'admin_profile_image', 'admin_logged_in']);
        redirect('admin/login');
    }
}
