<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Require Admin_Controller if not already loaded
if (!class_exists('Admin_Controller')) {
    require_once APPPATH . 'core/Admin_Controller.php';
}

/**
 * Admin Profile Controller
 * 
 * Manages administrator personal details, avatar uploads, and password updates.
 */
class Profile extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        // Admin role & session verification handled by Admin_Controller
    }

    /**
     * Display Admin Profile Page
     */
    public function index() {
        $user_id = $this->session->userdata('user_id');
        $data['title']      = 'Admin Profile';
        $data['breadcrumb'] = 'Profile Settings';
        $data['user']       = $this->General_model->getById('users', $user_id);

        if (!$data['user']) {
            show_error('Administrator account not found.', 404);
        }

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/profile/index', $data);
        $this->load->view('admin/layout/footer', $data);
    }

    /**
     * Update Admin Details & Avatar
     */
    public function update() {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        $user_id = $this->session->userdata('user_id');
        $user    = $this->General_model->getById('users', $user_id);

        if (!$user) {
            $this->session->set_flashdata('error', 'User not found.');
            redirect('admin/profile');
        }

        $this->form_validation->set_rules('name', 'Full Name', 'required|trim|max_length[150]');

        // Validate email uniqueness only if modified
        $email = trim($this->input->post('email', TRUE));
        if ($email !== $user->email) {
            $this->form_validation->set_rules('email', 'Email Address', 'required|valid_email|is_unique[users.email]|trim|max_length[150]');
        } else {
            $this->form_validation->set_rules('email', 'Email Address', 'required|valid_email|trim|max_length[150]');
        }

        $this->form_validation->set_rules('mobile', 'Mobile Number', 'trim|max_length[20]');
        $this->form_validation->set_rules('address', 'Physical Address', 'trim');

        if ($this->form_validation->run() === TRUE) {
            $update_data = [
                'name'       => trim($this->input->post('name', TRUE)),
                'email'      => $email,
                'mobile'     => trim($this->input->post('mobile', TRUE)) ?: NULL,
                'address'    => trim($this->input->post('address', TRUE)) ?: NULL,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Handle Profile Image upload
            if (!empty($_FILES['profile_image']['name'])) {
                $upload_dir = FCPATH . 'uploads/profile_images/';
                if (!is_dir($upload_dir)) {
                    @mkdir($upload_dir, 0777, true);
                }

                $config['upload_path']   = './uploads/profile_images/';
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size']      = 2048; // 2MB
                $config['file_name']     = 'profile_' . $user_id . '_' . time();

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('profile_image')) {
                    $upload_data = $this->upload->data();
                    $image_path  = 'uploads/profile_images/' . $upload_data['file_name'];

                    // Delete previous avatar file if exists
                    if (!empty($user->profile_image) && file_exists(FCPATH . $user->profile_image)) {
                        @unlink(FCPATH . $user->profile_image);
                    }

                    $update_data['profile_image'] = $image_path;
                    $this->session->set_userdata('profile_image', $image_path);
                } else {
                    $upload_error = $this->upload->display_errors('', '');
                    $this->session->set_flashdata('error', 'Profile details saved, but image upload failed: ' . $upload_error);
                    $this->General_model->update('users', ['id' => $user_id], $update_data);
                    $this->session->set_userdata('name', $update_data['name']);
                    $this->session->set_userdata('email', $update_data['email']);
                    redirect('admin/profile');
                }
            }

            if ($this->General_model->update('users', ['id' => $user_id], $update_data)) {
                $this->session->set_userdata('name', $update_data['name']);
                $this->session->set_userdata('email', $update_data['email']);
                $this->session->set_flashdata('success', 'Profile details updated successfully.');
            } else {
                $this->session->set_flashdata('error', 'Failed to update profile details.');
            }
        } else {
            $this->session->set_flashdata('error', strip_tags(validation_errors()));
        }

        redirect('admin/profile');
    }

    /**
     * Change Administrator Password
     */
    public function change_password() {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        $user_id = $this->session->userdata('user_id');
        $user    = $this->General_model->getById('users', $user_id);

        if (!$user) {
            $this->session->set_flashdata('error', 'User not found.');
            redirect('admin/profile');
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

        redirect('admin/profile#security');
    }
}
