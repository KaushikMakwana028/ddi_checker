<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Require Doctor_Controller if needed
if (!class_exists('Doctor_Controller')) {
    require_once APPPATH . 'core/Doctor_Controller.php';
}

/**
 * Doctor Login Controller
 * 
 * Handles clinical practitioner authentication: login, self-registration, and logout.
 * Uses role = 0 (0 = doctor, 1 = admin).
 */
class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // Load database, session, validation, and general model
        $this->load->database();
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form', 'security']);
        $this->load->model('General_model');

        // Prevent browser caching of doctor authentication views
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
     * Doctor Login Page & Form Submission
     */
    public function login() {
        // If already authenticated as a doctor (role = 0), redirect to doctor dashboard
        if ($this->session->userdata('logged_in') && (int)$this->session->userdata('role') === 0) { // 0 = doctor
            redirect('doctor/dashboard');
        }

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === TRUE) {
            $email    = $this->input->post('email', TRUE);
            $password = $this->input->post('password');

            // Strictly query user WHERE email = ? AND role = 0 (0 = doctor, 1 = admin)
            $doctor_user = $this->General_model->getOne('users', [
                'email' => $email,
                'role'  => 0 // 0 = doctor
            ]);

            if ($doctor_user && password_verify($password, $doctor_user->password)) {
                // Check if active
                if ((int)$doctor_user->is_active === 0) {
                    $this->session->set_flashdata('error', 'Your practitioner account has been deactivated. Please contact administration.');
                    redirect('doctor/login');
                }

                // Set doctor session userdata (role = 0)
                $this->session->set_userdata([
                    'user_id'       => $doctor_user->id,
                    'name'          => $doctor_user->name,
                    'email'         => $doctor_user->email,
                    'role'          => 0, // 0 = doctor, 1 = admin
                    'profile_image' => $doctor_user->profile_image,
                    'logged_in'     => TRUE
                ]);

                redirect('doctor/dashboard');
            } else {
                // Check if this email belongs to an Admin account
                $admin_check = $this->General_model->getOne('users', ['email' => $email, 'role' => 1]);
                if ($admin_check && password_verify($password, $admin_check->password)) {
                    $this->session->set_flashdata('error', 'This account is registered as an Administrator. Please sign in via the Admin Portal at /admin/login.');
                } else {
                    $this->session->set_flashdata('error', 'Invalid email or password. Please try again.');
                }

                redirect('doctor/login');
            }
        } else {
            $data['title'] = 'Doctor Access';
            $this->load->view('doctor/auth/login', $data);
        }
    }

    /**
     * Doctor Self-Registration Page & Form Submission
     */
    public function register() {
        // If already authenticated as a doctor (role = 0), redirect to doctor dashboard
        if ($this->session->userdata('logged_in') && (int)$this->session->userdata('role') === 0) { // 0 = doctor
            redirect('doctor/dashboard');
        }

        // Validation Rules
        $this->form_validation->set_rules('name', 'Doctor Full Name', 'required|trim|max_length[150]');
        $this->form_validation->set_rules('email', 'Email Address', 'required|valid_email|is_unique[users.email]|trim|max_length[150]');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|trim|max_length[20]');
        $this->form_validation->set_rules('registration_number', 'Medical Registration Number', 'required|trim|is_unique[doctor_profiles.registration_number]|max_length[100]');
        $this->form_validation->set_rules('specialization', 'Specialization', 'trim|max_length[150]');
        $this->form_validation->set_rules('qualification', 'Qualification', 'trim|max_length[150]');
        $this->form_validation->set_rules('hospital_clinic', 'Hospital / Clinic', 'trim|max_length[200]');
        $this->form_validation->set_rules('address', 'Address', 'trim');
        $this->form_validation->set_rules('medical_council', 'Medical Council', 'trim|max_length[150]');
        $this->form_validation->set_rules('years_experience', 'Years of Experience', 'trim|integer|greater_than_equal_to[0]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');

        if ($this->form_validation->run() === TRUE) {
            $name                = trim($this->input->post('name', TRUE));
            $email               = trim($this->input->post('email', TRUE));
            $mobile              = trim($this->input->post('mobile', TRUE));
            $address             = trim($this->input->post('address', TRUE));
            $specialization      = trim($this->input->post('specialization', TRUE));
            $qualification       = trim($this->input->post('qualification', TRUE));
            $hospital_clinic     = trim($this->input->post('hospital_clinic', TRUE));
            $registration_number = trim($this->input->post('registration_number', TRUE));
            $medical_council     = trim($this->input->post('medical_council', TRUE));
            $years_experience    = $this->input->post('years_experience', TRUE);
            $password            = $this->input->post('password');

            // DB Transaction
            $this->db->trans_begin();

            $user_data = [
                'name'       => $name,
                'email'      => $email,
                'mobile'     => $mobile,
                'address'    => !empty($address) ? $address : NULL,
                'password'   => password_hash($password, PASSWORD_BCRYPT),
                'role'       => 0, // 0 = doctor, 1 = admin
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $user_id = $this->General_model->insert('users', $user_data);

            $profile_data = [
                'user_id'             => $user_id,
                'specialization'      => !empty($specialization) ? $specialization : NULL,
                'qualification'       => !empty($qualification) ? $qualification : NULL,
                'hospital_clinic'     => !empty($hospital_clinic) ? $hospital_clinic : NULL,
                'registration_number' => $registration_number,
                'medical_council'     => !empty($medical_council) ? $medical_council : NULL,
                'years_experience'    => ($years_experience !== '' && $years_experience !== NULL) ? (int)$years_experience : NULL,
                'added_by_admin_id'   => NULL, // Self registered
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s')
            ];

            $profile_id = $this->General_model->insert('doctor_profiles', $profile_data);

            if ($this->db->trans_status() === FALSE || !$user_id || !$profile_id) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Registration could not be completed. Please verify your details and try again.');
                redirect('doctor/register');
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('success', 'Doctor registration successful! Please sign in with your credentials.');
                redirect('doctor/login');
            }
        } else {
            $data['title'] = 'Doctor Registration';
            $this->load->view('doctor/auth/register', $data);
        }
    }

    /**
     * Doctor Logout
     */
    public function logout() {
        $this->session->sess_destroy();
        redirect('doctor/login');
    }
}
