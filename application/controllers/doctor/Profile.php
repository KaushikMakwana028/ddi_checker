<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Require Doctor_Controller if not already loaded
if (!class_exists('Doctor_Controller')) {
    require_once APPPATH . 'core/Doctor_Controller.php';
}

/**
 * Doctor Profile Controller
 * 
 * Manages clinical practitioner personal credentials, professional profiles,
 * registration details, avatar uploads, and password security.
 */
class Profile extends Doctor_Controller {

    public function __construct() {
        parent::__construct();
        // Authentication automatically enforced by Doctor_Controller
    }

    /**
     * Display Profile settings page
     */
    public function index() {
        $user_id = $this->session->userdata('doctor_user_id');
        
        $data['title']      = 'My Profile';
        $data['breadcrumb'] = 'Profile Settings';
        $data['user']       = $this->General_model->getById('users', $user_id);
        $data['profile']    = $this->General_model->getOne('doctor_profiles', ['user_id' => $user_id]);

        if (!$data['user']) {
            show_error('Practitioner account records not found.', 404);
        }

        $this->load->view('doctor/layout/header', $data);
        $this->load->view('doctor/profile/index', $data);
        $this->load->view('doctor/layout/footer', $data);
    }

    /**
     * Update Doctor profile details (avatar and fields)
     */
    public function update() {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        $user_id = $this->session->userdata('doctor_user_id');
        $user    = $this->General_model->getById('users', $user_id);
        $profile = $this->General_model->getOne('doctor_profiles', ['user_id' => $user_id]);

        if (!$user) {
            $this->session->set_flashdata('error', 'User record not found.');
            redirect('doctor/profile');
        }

        // Form validation rules
        $this->form_validation->set_rules('name', 'Full Name', 'required|trim|max_length[150]');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|trim|max_length[20]');
        
        $email = trim($this->input->post('email', TRUE));
        if ($email !== $user->email) {
            $this->form_validation->set_rules('email', 'Email Address', 'required|valid_email|is_unique[users.email]|trim|max_length[150]');
        } else {
            $this->form_validation->set_rules('email', 'Email Address', 'required|valid_email|trim|max_length[150]');
        }

        $registration_number = trim($this->input->post('registration_number', TRUE));
        $this->form_validation->set_rules('registration_number', 'Medical Registration Number', 'required|trim|max_length[100]');
        if ($profile && $registration_number !== $profile->registration_number) {
            $this->form_validation->set_rules('registration_number', 'Medical Registration Number', 'required|trim|is_unique[doctor_profiles.registration_number]|max_length[100]');
        }

        $this->form_validation->set_rules('specialization', 'Specialization', 'trim|max_length[150]');
        $this->form_validation->set_rules('qualification', 'Qualification', 'trim|max_length[150]');
        $this->form_validation->set_rules('hospital_clinic', 'Hospital / Clinic', 'trim|max_length[200]');
        $this->form_validation->set_rules('address', 'Address', 'trim');
        $this->form_validation->set_rules('medical_council', 'Medical Council', 'trim|max_length[150]');
        $this->form_validation->set_rules('years_experience', 'Years of Experience', 'trim|integer|greater_than_equal_to[0]');

        if ($this->form_validation->run() === TRUE) {
            $this->db->trans_begin();

            $update_user = [
                'name'       => trim($this->input->post('name', TRUE)),
                'email'      => $email,
                'mobile'     => trim($this->input->post('mobile', TRUE)),
                'address'    => trim($this->input->post('address', TRUE)) ?: NULL,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Image Upload Handling
            if (!empty($_FILES['profile_image']['name'])) {
                $upload_dir = FCPATH . 'uploads/profile_images/';
                if (!is_dir($upload_dir)) {
                    @mkdir($upload_dir, 0777, true);
                }

                $config['upload_path']   = './uploads/profile_images/';
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size']      = 2048; // 2MB
                $config['file_name']     = 'profile_doc_' . $user_id . '_' . time();

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('profile_image')) {
                    $upload_data = $this->upload->data();
                    $image_path  = 'uploads/profile_images/' . $upload_data['file_name'];

                    // Delete old image
                    if (!empty($user->profile_image) && file_exists(FCPATH . $user->profile_image)) {
                        @unlink(FCPATH . $user->profile_image);
                    }

                    $update_user['profile_image'] = $image_path;
                    $this->session->set_userdata('doctor_profile_image', $image_path);
                } else {
                    $upload_error = $this->upload->display_errors('', '');
                    $this->session->set_flashdata('error', 'Image upload failed: ' . $upload_error);
                    $this->db->trans_rollback();
                    redirect('doctor/profile');
                }
            }

            // Update user table
            $this->General_model->update('users', ['id' => $user_id], $update_user);

            // Update profile table
            $years_exp = $this->input->post('years_experience', TRUE);
            $update_profile = [
                'specialization'      => trim($this->input->post('specialization', TRUE)) ?: NULL,
                'qualification'       => trim($this->input->post('qualification', TRUE)) ?: NULL,
                'hospital_clinic'     => trim($this->input->post('hospital_clinic', TRUE)) ?: NULL,
                'registration_number' => $registration_number,
                'medical_council'     => trim($this->input->post('medical_council', TRUE)) ?: NULL,
                'years_experience'    => ($years_exp !== '' && $years_exp !== NULL) ? (int)$years_exp : NULL,
                'updated_at'          => date('Y-m-d H:i:s')
            ];

            // Signature Image Upload Handling
            if (!empty($_FILES['signature']['name'])) {
                $upload_dir = FCPATH . 'uploads/signatures/';
                if (!is_dir($upload_dir)) {
                    @mkdir($upload_dir, 0777, true);
                }

                $config_sig['upload_path']   = './uploads/signatures/';
                $config_sig['allowed_types'] = 'gif|jpg|jpeg|png';
                $config_sig['max_size']      = 2048; // 2MB
                $config_sig['file_name']     = 'sig_doc_' . $user_id . '_' . time();

                $this->load->library('upload');
                $this->upload->initialize($config_sig);

                if ($this->upload->do_upload('signature')) {
                    $upload_data = $this->upload->data();
                    $sig_path  = 'uploads/signatures/' . $upload_data['file_name'];

                    // Delete old signature
                    if ($profile && !empty($profile->signature) && file_exists(FCPATH . $profile->signature)) {
                        @unlink(FCPATH . $profile->signature);
                    }

                    $update_profile['signature'] = $sig_path;
                } else {
                    $upload_error = $this->upload->display_errors('', '');
                    $this->session->set_flashdata('error', 'Signature upload failed: ' . $upload_error);
                    $this->db->trans_rollback();
                    redirect('doctor/profile');
                }
            }

            if ($profile) {
                $this->General_model->update('doctor_profiles', ['user_id' => $user_id], $update_profile);
            } else {
                $update_profile['user_id'] = $user_id;
                $update_profile['created_at'] = date('Y-m-d H:i:s');
                $this->General_model->insert('doctor_profiles', $update_profile);
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Failed to update profile details.');
            } else {
                $this->db->trans_commit();
                $this->session->set_userdata('doctor_name', $update_user['name']);
                $this->session->set_userdata('doctor_email', $update_user['email']);
                $this->session->set_flashdata('success', 'Profile details updated successfully.');
            }
        } else {
            $this->session->set_flashdata('error', strip_tags(validation_errors()));
        }

        redirect('doctor/profile');
    }

    /**
     * Change password
     */
    public function change_password() {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        $user_id = $this->session->userdata('doctor_user_id');
        $user    = $this->General_model->getById('users', $user_id);

        if (!$user) {
            $this->session->set_flashdata('error', 'User record not found.');
            redirect('doctor/profile');
        }

        $this->form_validation->set_rules('current_password', 'Current Password', 'required');
        $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Confirm New Password', 'required|matches[new_password]');

        if ($this->form_validation->run() === TRUE) {
            $current_password = $this->input->post('current_password');
            $new_password     = $this->input->post('new_password');

            if (password_verify($current_password, $user->password)) {
                $update_data = [
                    'password'   => password_hash($new_password, PASSWORD_BCRYPT),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if ($this->General_model->update('users', ['id' => $user_id], $update_data)) {
                    $this->session->set_flashdata('success', 'Password updated successfully.');
                } else {
                    $this->session->set_flashdata('error', 'Failed to update password.');
                }
            } else {
                $this->session->set_flashdata('error', 'Incorrect current password provided.');
            }
        } else {
            $this->session->set_flashdata('error', strip_tags(validation_errors()));
        }

        redirect('doctor/profile#security');
    }
}
