<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Load database, session, and validation libraries
        $this->load->database();
        $this->load->library(['session', 'form_validation']);
        // Load url and form helpers
        $this->load->helper(['url', 'form']);
        
        // Prevent browser caching of login/register pages
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

        // Redirect to dashboard if already logged in (except when logging out)
        if ($this->session->userdata('logged_in') && $this->router->fetch_method() !== 'logout') {
            redirect('dashboard');
        }
    }

    /**
     * Login view and processing
     */
    public function index() {
        // If accessed via default root route, redirect to the clean auth/login route
        if ($this->uri->uri_string() === '' || $this->uri->uri_string() === 'login') {
            redirect('auth/login');
        }

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === TRUE) {
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            $this->load->model('General_model', 'User_model');
            $user = $this->User_model->getOne('users', ['email' => $email]);

            if ($user && password_verify($password, $user->password)) {
                if ($user->is_active == 0) {
                    $this->session->set_flashdata('error', 'Account disabled, contact admin');
                    redirect('auth/login');
                } else {
                    // Set session userdata
                    $this->session->set_userdata([
                        'user_id'   => $user->id,
                        'name'      => $user->name,
                        'email'     => $user->email,
                        'logged_in' => TRUE
                    ]);
                    
                    redirect('dashboard');
                }
            } else {
                $this->session->set_flashdata('error', 'Invalid email or password');
                redirect('auth/login');
            }
        } else {
            $data['title'] = 'Login';
            $this->load->view('templates/header', $data);
            $this->load->view('auth/login', $data);
            $this->load->view('templates/footer', $data);
        }
    }

    /**
     * User registration view and processing
     */
    public function register() {
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');

        if ($this->form_validation->run() === TRUE) {
            $name = $this->input->post('name');
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            $this->load->model('General_model', 'User_model');
            $user_data = [
                'name'       => $name,
                'email'      => $email,
                'password'   => password_hash($password, PASSWORD_BCRYPT),
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->User_model->insert('users', $user_data);
            $this->session->set_flashdata('success', 'Registration successful! Please login.');
            redirect('auth/login');
        } else {
            $data['title'] = 'Register';
            $this->load->view('templates/header', $data);
            $this->load->view('auth/register', $data);
            $this->load->view('templates/footer', $data);
        }
    }

    /**
     * Logout
     */
    public function logout() {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
