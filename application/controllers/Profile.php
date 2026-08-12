<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends MY_Controller {

    public function __construct() {
        parent::__construct();
        // MY_Controller handles authentication check
        $this->load->database();
        $this->load->library(['form_validation', 'session']);
        $this->load->helper(['url', 'form']);
        $this->load->model('General_model');
        
        // Prevent browser caching
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    }

    /**
     * Display profile page
     */
    public function index() {
        $user_id = $this->session->userdata('user_id');
        $data['title'] = 'My Profile';
        $data['user'] = $this->General_model->getById('users', $user_id);

        if (!$data['user']) {
            show_error('User not found.', 404);
        }

        $this->load->view('templates/header', $data);
        $this->load->view('profile/index', $data);
        $this->load->view('templates/footer', $data);
    }

    /**
     * Update profile details (including Email and Avatar)
     */
    public function update() {
        $user_id = $this->session->userdata('user_id');
        $user = $this->General_model->getById('users', $user_id);

        if (!$user) {
            $this->session->set_flashdata('error', 'User not found.');
            redirect('profile');
        }
        
        $this->form_validation->set_rules('name', 'Name', 'required|trim|max_length[150]');
        
        // Validate email uniqueness only if it was modified
        $email = $this->input->post('email');
        if ($email !== $user->email) {
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]|trim|max_length[150]');
        } else {
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim|max_length[150]');
        }
        
        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|max_length[20]');
        $this->form_validation->set_rules('address', 'Address', 'trim');

        if ($this->form_validation->run() === TRUE) {
            $update_data = [
                'name'       => $this->input->post('name'),
                'email'      => $email,
                'mobile'     => $this->input->post('mobile'),
                'address'    => $this->input->post('address'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Handle Profile Image upload if a file was selected
            if (!empty($_FILES['profile_image']['name'])) {
                $config['upload_path']   = './uploads/profile_images/';
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size']      = 2048; // 2MB
                $config['file_name']     = 'profile_' . $user_id . '_' . time();

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('profile_image')) {
                    $upload_data = $this->upload->data();
                    $image_path = 'uploads/profile_images/' . $upload_data['file_name'];

                    // Delete old image file
                    if (!empty($user->profile_image) && file_exists(FCPATH . $user->profile_image)) {
                        @unlink(FCPATH . $user->profile_image);
                    }

                    $update_data['profile_image'] = $image_path;
                } else {
                    $upload_error = $this->upload->display_errors(' ', ' ');
                    $this->session->set_flashdata('error', 'Details validation passed, but image upload failed: ' . $upload_error);
                    redirect('profile');
                }
            }

            if ($this->General_model->update('users', ['id' => $user_id], $update_data)) {
                // Update session values
                $this->session->set_userdata('name', $update_data['name']);
                $this->session->set_userdata('email', $update_data['email']);
                $this->session->set_flashdata('success', 'Profile updated successfully.');
            } else {
                $this->session->set_flashdata('error', 'Failed to update profile details.');
            }
        } else {
            $this->session->set_flashdata('error', validation_errors(' ', ' '));
        }

        redirect('profile');
    }

    /**
     * Change user password
     */
    public function change_password() {
        $user_id = $this->session->userdata('user_id');
        $user = $this->General_model->getById('users', $user_id);

        if (!$user) {
            $this->session->set_flashdata('error', 'User not found.');
            redirect('profile');
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
                    $this->session->set_flashdata('success', 'Password changed successfully.');
                } else {
                    $this->session->set_flashdata('error', 'Failed to change password.');
                }
            } else {
                $this->session->set_flashdata('error', 'Incorrect current password.');
            }
        } else {
            $this->session->set_flashdata('error', validation_errors(' ', ' '));
        }

        redirect('profile');
    }
}
