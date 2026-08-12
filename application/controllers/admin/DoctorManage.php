<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Require Admin_Controller if not already loaded
if (!class_exists('Admin_Controller')) {
    require_once APPPATH . 'core/Admin_Controller.php';
}

/**
 * Admin DoctorManage Controller
 * 
 * Allows administrators to list, register, update credentials,
 * and activate/deactivate clinical doctor accounts.
 * Uses role = 0 (0 = doctor, 1 = admin).
 */
class DoctorManage extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        // Admin authentication and role = 1 verification handled by Admin_Controller
    }

    /**
     * Doctors List Page
     */
    public function index() {
        $data['title']      = 'Doctors';
        $data['breadcrumb'] = 'Doctors';

        // Join users with doctor_profiles where role = 0 (0 = doctor, 1 = admin)
        $sql = "SELECT u.id, u.name, u.email, u.mobile, u.address, u.is_active, u.created_at,
                       dp.id AS profile_id, dp.specialization, dp.qualification, dp.hospital_clinic,
                       dp.registration_number, dp.medical_council, dp.years_experience, dp.added_by_admin_id
                FROM users u
                LEFT JOIN doctor_profiles dp ON dp.user_id = u.id
                WHERE u.role = 0
                ORDER BY u.id DESC";

        $data['doctors'] = $this->General_model->query($sql);

        // Load admin layouts and doctors view
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/doctor_manage/index', $data);
        $this->load->view('admin/layout/footer', $data);
    }

    /**
     * Add new Doctor page and submission handler
     */
    public function add() {
        if ($this->input->method() === 'get') {
            $data['title']      = 'Register Practitioner';
            $data['breadcrumb'] = 'Register Doctor';

            $this->load->view('admin/layout/header', $data);
            $this->load->view('admin/doctor_manage/add', $data);
            $this->load->view('admin/layout/footer', $data);
            return;
        }

        // Validation Rules
        $this->form_validation->set_rules('name', 'Doctor Name', 'required|trim|max_length[150]');
        $this->form_validation->set_rules('email', 'Email Address', 'required|valid_email|is_unique[users.email]|trim|max_length[150]');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|trim|max_length[20]');
        $this->form_validation->set_rules('registration_number', 'Medical Registration Number', 'required|trim|is_unique[doctor_profiles.registration_number]|max_length[100]');
        $this->form_validation->set_rules('specialization', 'Specialization', 'trim|max_length[150]');
        $this->form_validation->set_rules('qualification', 'Qualification', 'trim|max_length[150]');
        $this->form_validation->set_rules('hospital_clinic', 'Hospital / Clinic', 'trim|max_length[200]');
        $this->form_validation->set_rules('address', 'Address', 'trim');
        $this->form_validation->set_rules('medical_council', 'Medical Council', 'trim|max_length[150]');
        $this->form_validation->set_rules('years_experience', 'Years of Experience', 'trim|integer|greater_than_equal_to[0]');
        $this->form_validation->set_rules('password', 'Initial Password', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');

        if ($this->form_validation->run() == FALSE) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'error',
                    'message'   => strip_tags(validation_errors()),
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $data['title']      = 'Register Practitioner';
            $data['breadcrumb'] = 'Register Doctor';
            $this->session->set_flashdata('error', validation_errors());
            $this->load->view('admin/layout/header', $data);
            $this->load->view('admin/doctor_manage/add', $data);
            $this->load->view('admin/layout/footer', $data);
            return;
        }

        // Collect inputs
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

        // Execute Transaction
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
            'added_by_admin_id'   => $this->session->userdata('user_id'),
            'created_at'          => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s')
        ];

        $profile_id = $this->General_model->insert('doctor_profiles', $profile_data);

        if ($this->db->trans_status() === FALSE || !$user_id || !$profile_id) {
            $this->db->trans_rollback();
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'error',
                    'message'   => 'Failed to add doctor account due to a database error.',
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $this->session->set_flashdata('error', 'Failed to add doctor account due to a database error.');
            redirect('admin/doctors/add');
        } else {
            $this->db->trans_commit();
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'success',
                    'message'   => 'Doctor account registered successfully.',
                    'redirect'  => base_url('admin/doctors'),
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $this->session->set_flashdata('success', 'Doctor account registered successfully.');
            redirect('admin/doctors');
        }
    }

    /**
     * Edit Doctor credentials and profile page and handler
     */
    public function edit($id = NULL) {
        if (!$id) {
            redirect('admin/doctors');
        }

        // Verify doctor user exists (role = 0: 0 = doctor, 1 = admin)
        $doctor = $this->General_model->getOne('users', ['id' => $id, 'role' => 0]);
        if (!$doctor) {
            $this->session->set_flashdata('error', 'Doctor account not found.');
            redirect('admin/doctors');
        }

        $profile = $this->General_model->getOne('doctor_profiles', ['user_id' => $id]);

        if ($this->input->method() === 'get') {
            $data['title']      = 'Edit Doctor Details';
            $data['breadcrumb'] = 'Edit Doctor';
            $data['doctor']     = $doctor;
            $data['profile']    = $profile;

            $this->load->view('admin/layout/header', $data);
            $this->load->view('admin/doctor_manage/edit', $data);
            $this->load->view('admin/layout/footer', $data);
            return;
        }

        // Validation Rules
        $this->form_validation->set_rules('name', 'Doctor Name', 'required|trim|max_length[150]');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|trim|max_length[20]');
        $this->form_validation->set_rules('registration_number', 'Medical Registration Number', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('specialization', 'Specialization', 'trim|max_length[150]');
        $this->form_validation->set_rules('qualification', 'Qualification', 'trim|max_length[150]');
        $this->form_validation->set_rules('hospital_clinic', 'Hospital / Clinic', 'trim|max_length[200]');
        $this->form_validation->set_rules('address', 'Address', 'trim');
        $this->form_validation->set_rules('medical_council', 'Medical Council', 'trim|max_length[150]');
        $this->form_validation->set_rules('years_experience', 'Years of Experience', 'trim|integer|greater_than_equal_to[0]');

        // Email uniqueness only if modified
        $email = trim($this->input->post('email', TRUE));
        if ($email !== $doctor->email) {
            $this->form_validation->set_rules('email', 'Email Address', 'required|valid_email|is_unique[users.email]|trim|max_length[150]');
        } else {
            $this->form_validation->set_rules('email', 'Email Address', 'required|valid_email|trim|max_length[150]');
        }

        // Registration number uniqueness only if modified
        $registration_number = trim($this->input->post('registration_number', TRUE));
        if ($profile && $registration_number !== $profile->registration_number) {
            $this->form_validation->set_rules('registration_number', 'Registration Number', 'required|trim|is_unique[doctor_profiles.registration_number]|max_length[100]');
        }

        // Password validation if provided
        $password = $this->input->post('password');
        if (!empty($password)) {
            $this->form_validation->set_rules('password', 'New Password', 'min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');
        }

        if ($this->form_validation->run() == FALSE) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'error',
                    'message'   => strip_tags(validation_errors()),
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $data['title']      = 'Edit Doctor Details';
            $data['breadcrumb'] = 'Edit Doctor';
            $data['doctor']     = $doctor;
            $data['profile']    = $profile;
            $this->session->set_flashdata('error', validation_errors());
            $this->load->view('admin/layout/header', $data);
            $this->load->view('admin/doctor_manage/edit', $data);
            $this->load->view('admin/layout/footer', $data);
            return;
        }

        // Collect inputs
        $name             = trim($this->input->post('name', TRUE));
        $mobile           = trim($this->input->post('mobile', TRUE));
        $address          = trim($this->input->post('address', TRUE));
        $specialization   = trim($this->input->post('specialization', TRUE));
        $qualification    = trim($this->input->post('qualification', TRUE));
        $hospital_clinic  = trim($this->input->post('hospital_clinic', TRUE));
        $medical_council  = trim($this->input->post('medical_council', TRUE));
        $years_experience = $this->input->post('years_experience', TRUE);

        $this->db->trans_begin();

        // Update users table
        $user_update = [
            'name'       => $name,
            'email'      => $email,
            'mobile'     => $mobile,
            'address'    => !empty($address) ? $address : NULL,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if (!empty($password)) {
            $user_update['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->General_model->update('users', ['id' => $id], $user_update);

        // Update or Insert doctor_profiles table
        $profile_update = [
            'specialization'      => !empty($specialization) ? $specialization : NULL,
            'qualification'       => !empty($qualification) ? $qualification : NULL,
            'hospital_clinic'     => !empty($hospital_clinic) ? $hospital_clinic : NULL,
            'registration_number' => $registration_number,
            'medical_council'     => !empty($medical_council) ? $medical_council : NULL,
            'years_experience'    => ($years_experience !== '' && $years_experience !== NULL) ? (int)$years_experience : NULL,
            'updated_at'          => date('Y-m-d H:i:s')
        ];

        if ($profile) {
            $this->General_model->update('doctor_profiles', ['user_id' => $id], $profile_update);
        } else {
            $profile_update['user_id']           = $id;
            $profile_update['added_by_admin_id'] = $this->session->userdata('user_id');
            $profile_update['created_at']        = date('Y-m-d H:i:s');
            $this->General_model->insert('doctor_profiles', $profile_update);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'error',
                    'message'   => 'Database error while saving changes.',
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $this->session->set_flashdata('error', 'Database error while saving changes.');
            redirect('admin/doctors/edit/' . $id);
        } else {
            $this->db->trans_commit();
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'success',
                    'message'   => 'Doctor account updated successfully.',
                    'redirect'  => base_url('admin/doctors'),
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $this->session->set_flashdata('success', 'Doctor account updated successfully.');
            redirect('admin/doctors');
        }
    }

    /**
     * Deactivate doctor account
     */
    public function deactivate($id) {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        $doctor = $this->General_model->getOne('users', ['id' => $id, 'role' => 0]);
        if (!$doctor) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'error',
                'message'   => 'Doctor account not found.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
            return;
        }

        $updated = $this->General_model->update('users', ['id' => $id], ['is_active' => 0, 'updated_at' => date('Y-m-d H:i:s')]);

        if ($updated) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'success',
                'message'   => 'Doctor account suspended successfully.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'error',
                'message'   => 'Failed to deactivate account.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        }
    }

    /**
     * Activate doctor account
     */
    public function activate($id) {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        $doctor = $this->General_model->getOne('users', ['id' => $id, 'role' => 0]);
        if (!$doctor) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'error',
                'message'   => 'Doctor account not found.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
            return;
        }

        $updated = $this->General_model->update('users', ['id' => $id], ['is_active' => 1, 'updated_at' => date('Y-m-d H:i:s')]);

        if ($updated) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'success',
                'message'   => 'Doctor account restored successfully.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'error',
                'message'   => 'Failed to activate account.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        }
    }
}
