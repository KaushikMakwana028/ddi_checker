<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Require Admin_Controller if not already loaded
if (!class_exists('Admin_Controller')) {
    require_once APPPATH . 'core/Admin_Controller.php';
}

/**
 * Admin DrugEntry Controller
 * 
 * Manages clinical drug definitions, categories, brand synonyms,
 * stock quantity, unit types, and activation states in the database.
 */
class DrugEntry extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        // Authentication & role checks handled by Admin_Controller
    }

    /**
     * View List page of drugs
     */
    public function index() {
        $data['title']      = 'Drug Entry';
        $data['breadcrumb'] = 'Drug Entry';
        
        // Fetch all drugs
        $data['drugs'] = $this->General_model->getAll('drugs');

        // Render in Admin Layout
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/drug_entry/index', $data);
        $this->load->view('admin/layout/footer', $data);
    }

    /**
     * Add new drug page and submission handler
     */
    public function add() {
        if ($this->input->method() === 'get') {
            $data['title']      = 'Add New Drug';
            $data['breadcrumb'] = 'Add New Drug';

            $this->load->view('admin/layout/header', $data);
            $this->load->view('admin/drug_entry/add', $data);
            $this->load->view('admin/layout/footer', $data);
            return;
        }

        // Form validation rules
        $this->form_validation->set_rules('drug_name', 'Drug Name', 'required|trim|min_length[2]');
        $this->form_validation->set_rules('quantity', 'Quantity', 'required|integer|greater_than_equal_to[0]');
        $this->form_validation->set_rules('unit', 'Unit', 'trim');

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

            $data['title']      = 'Add New Drug';
            $data['breadcrumb'] = 'Add New Drug';
            $this->session->set_flashdata('error', validation_errors());
            $this->load->view('admin/layout/header', $data);
            $this->load->view('admin/drug_entry/add', $data);
            $this->load->view('admin/layout/footer', $data);
            return;
        }

        $drug_name = trim($this->input->post('drug_name', TRUE));
        $synonyms  = trim($this->input->post('synonyms', TRUE));
        $category  = trim($this->input->post('category', TRUE));
        $quantity  = (int)$this->input->post('quantity');
        $unit      = trim($this->input->post('unit', TRUE));

        // Check for duplicate drug name (case-insensitive)
        $existing = $this->General_model->getOne('drugs', [
            'LOWER(drug_name)' => strtolower($drug_name)
        ]);

        if ($existing) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'error',
                    'message'   => 'A drug with this name already exists in the database.',
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $this->session->set_flashdata('error', 'A drug with this name already exists in the database.');
            redirect('admin/drug-entry/add');
            return;
        }

        // Insert new drug
        $data = [
            'drug_name'  => $drug_name,
            'synonyms'   => !empty($synonyms) ? $synonyms : NULL,
            'category'   => !empty($category) ? $category : NULL,
            'quantity'   => $quantity,
            'unit'       => !empty($unit) ? $unit : NULL,
            'is_active'  => 1,
            'created_by' => $this->session->userdata('user_id'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $insert_id = $this->General_model->insert('drugs', $data);

        if ($insert_id) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'success',
                    'message'   => 'Drug registered successfully.',
                    'redirect'  => base_url('admin/drug-entry'),
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $this->session->set_flashdata('success', 'Drug registered successfully.');
            redirect('admin/drug-entry');
        } else {
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'error',
                    'message'   => 'Failed to add the drug. Please try again.',
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $this->session->set_flashdata('error', 'Failed to add the drug. Please try again.');
            redirect('admin/drug-entry/add');
        }
    }

    /**
     * Edit existing drug page and submission handler
     */
    public function edit($id = NULL) {
        if (!$id) {
            redirect('admin/drug-entry');
        }

        // Verify drug exists
        $drug = $this->General_model->getById('drugs', $id);
        if (!$drug) {
            $this->session->set_flashdata('error', 'Drug not found in registry.');
            redirect('admin/drug-entry');
        }

        if ($this->input->method() === 'get') {
            $data['title']      = 'Edit Drug Details';
            $data['breadcrumb'] = 'Edit Drug';
            $data['drug']       = $drug;

            $this->load->view('admin/layout/header', $data);
            $this->load->view('admin/drug_entry/edit', $data);
            $this->load->view('admin/layout/footer', $data);
            return;
        }

        // Form validation rules
        $this->form_validation->set_rules('drug_name', 'Drug Name', 'required|trim|min_length[2]');
        $this->form_validation->set_rules('quantity', 'Quantity', 'required|integer|greater_than_equal_to[0]');
        $this->form_validation->set_rules('unit', 'Unit', 'trim');

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

            $data['title']      = 'Edit Drug Details';
            $data['breadcrumb'] = 'Edit Drug';
            $data['drug']       = $drug;
            $this->session->set_flashdata('error', validation_errors());
            $this->load->view('admin/layout/header', $data);
            $this->load->view('admin/drug_entry/edit', $data);
            $this->load->view('admin/layout/footer', $data);
            return;
        }

        $drug_name = trim($this->input->post('drug_name', TRUE));
        $synonyms  = trim($this->input->post('synonyms', TRUE));
        $category  = trim($this->input->post('category', TRUE));
        $quantity  = (int)$this->input->post('quantity');
        $unit      = trim($this->input->post('unit', TRUE));

        // Check for duplicate drug name (case-insensitive, excluding current drug)
        $existing = $this->General_model->getOne('drugs', [
            'LOWER(drug_name)' => strtolower($drug_name),
            'id !='            => $id
        ]);

        if ($existing) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'error',
                    'message'   => 'Another drug with this name already exists.',
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $this->session->set_flashdata('error', 'Another drug with this name already exists.');
            redirect('admin/drug-entry/edit/' . $id);
            return;
        }

        // Update drug details
        $data = [
            'drug_name'  => $drug_name,
            'synonyms'   => !empty($synonyms) ? $synonyms : NULL,
            'category'   => !empty($category) ? $category : NULL,
            'quantity'   => $quantity,
            'unit'       => !empty($unit) ? $unit : NULL,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $updated = $this->General_model->update('drugs', ['id' => $id], $data);

        if ($updated !== FALSE) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'success',
                    'message'   => 'Drug updated successfully.',
                    'redirect'  => base_url('admin/drug-entry'),
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $this->session->set_flashdata('success', 'Drug updated successfully.');
            redirect('admin/drug-entry');
        } else {
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'error',
                    'message'   => 'Failed to update drug details. Please try again.',
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $this->session->set_flashdata('error', 'Failed to update drug details.');
            redirect('admin/drug-entry/edit/' . $id);
        }
    }

    /**
     * Soft delete drug (deactivate)
     */
    public function deactivate($id) {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        $drug = $this->General_model->getById('drugs', $id);
        if (!$drug) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'error',
                'message'   => 'Drug not found.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
            return;
        }

        $data = [
            'is_active'  => 0,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $updated = $this->General_model->update('drugs', ['id' => $id], $data);

        if ($updated) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'success',
                'message'   => 'Drug deactivated successfully.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'error',
                'message'   => 'Failed to deactivate drug.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        }
    }

    /**
     * Reactivate drug
     */
    public function activate($id) {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        $drug = $this->General_model->getById('drugs', $id);
        if (!$drug) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'error',
                'message'   => 'Drug not found.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
            return;
        }

        $data = [
            'is_active'  => 1,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $updated = $this->General_model->update('drugs', ['id' => $id], $data);

        if ($updated) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'success',
                'message'   => 'Drug activated successfully.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'error',
                'message'   => 'Failed to activate drug.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        }
    }

    /**
     * Permanently Delete drug
     */
    public function delete($id) {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        $drug = $this->General_model->getById('drugs', $id);
        if (!$drug) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'error',
                'message'   => 'Drug not found.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
            return;
        }

        // Delete associated interaction rules first
        $this->db->where('drug_a_id', $id)->or_where('drug_b_id', $id)->delete('interactions');

        // Delete drug record
        $deleted = $this->General_model->delete('drugs', ['id' => $id]);

        if ($deleted) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'success',
                'message'   => 'Drug and related interaction rules deleted permanently.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'error',
                'message'   => 'Failed to delete the drug.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        }
    }
}
