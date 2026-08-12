<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DrugEntry extends MY_Controller {

    public function __construct() {
        parent::__construct();
        // Load database, general model, form helper, url helper and validation library
        $this->load->model('General_model');
        $this->load->helper(['form', 'url']);
        $this->load->library('form_validation');
    }

    /**
     * View List page of drugs
     */
    public function index() {
        $data['title'] = 'Drug Entry';
        
        // Fetch all drugs
        $data['drugs'] = $this->General_model->getAll('drugs');

        // Render views
        $this->load->view('templates/header', $data);
        $this->load->view('drug_entry/index', $data);
        $this->load->view('templates/footer', $data);
    }

    /**
     * Add new drug
     */
    public function add() {
        // Only accept POST requests
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        // Form validation rules
        $this->form_validation->set_rules('drug_name', 'Drug Name', 'required|trim|min_length[2]');

        if ($this->form_validation->run() == FALSE) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error',
                'message' => strip_tags(validation_errors()),
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
            return;
        }

        $drug_name = trim($this->input->post('drug_name', TRUE));
        $synonyms = trim($this->input->post('synonyms', TRUE));
        $category = trim($this->input->post('category', TRUE));

        // Check for duplicate drug name (case-insensitive)
        $existing = $this->General_model->getOne('drugs', [
            'LOWER(drug_name)' => strtolower($drug_name)
        ]);

        if ($existing) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error',
                'message' => 'A drug with this name already exists in the database.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
            return;
        }

        // Insert new drug
        $data = [
            'drug_name' => $drug_name,
            'synonyms' => !empty($synonyms) ? $synonyms : NULL,
            'category' => !empty($category) ? $category : NULL,
            'is_active' => 1,
            'created_by' => $this->session->userdata('user_id'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $insert_id = $this->General_model->insert('drugs', $data);

        if ($insert_id) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'success',
                'message' => 'Drug added successfully.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error',
                'message' => 'Failed to add the drug. Please try again.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        }
    }

    /**
     * Edit existing drug
     */
    public function edit($id) {
        // Only accept POST requests
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        // Verify drug exists
        $drug = $this->General_model->getById('drugs', $id);
        if (!$drug) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error',
                'message' => 'Drug not found.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
            return;
        }

        // Form validation rules
        $this->form_validation->set_rules('drug_name', 'Drug Name', 'required|trim|min_length[2]');

        if ($this->form_validation->run() == FALSE) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error',
                'message' => strip_tags(validation_errors()),
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
            return;
        }

        $drug_name = trim($this->input->post('drug_name', TRUE));
        $synonyms = trim($this->input->post('synonyms', TRUE));
        $category = trim($this->input->post('category', TRUE));

        // Check for duplicate drug name (case-insensitive, excluding the current drug)
        $existing = $this->General_model->getOne('drugs', [
            'LOWER(drug_name)' => strtolower($drug_name),
            'id !=' => $id
        ]);

        if ($existing) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error',
                'message' => 'Another drug with this name already exists.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
            return;
        }

        // Update drug details
        $data = [
            'drug_name' => $drug_name,
            'synonyms' => !empty($synonyms) ? $synonyms : NULL,
            'category' => !empty($category) ? $category : NULL,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $updated = $this->General_model->update('drugs', ['id' => $id], $data);

        if ($updated) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'success',
                'message' => 'Drug updated successfully.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error',
                'message' => 'Failed to update drug details or no changes were made.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        }
    }

    /**
     * Soft delete drug (deactivate)
     */
    public function deactivate($id) {
        // Only accept POST requests
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        // Verify drug exists
        $drug = $this->General_model->getById('drugs', $id);
        if (!$drug) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error',
                'message' => 'Drug not found.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
            return;
        }

        // Perform soft delete (set is_active = 0)
        $data = [
            'is_active' => 0,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $updated = $this->General_model->update('drugs', ['id' => $id], $data);

        if ($updated) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'success',
                'message' => 'Drug deactivated successfully.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error',
                'message' => 'Failed to deactivate drug.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        }
    }

    /**
     * Reactivate drug
     */
    public function activate($id) {
        // Only accept POST requests
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        // Verify drug exists
        $drug = $this->General_model->getById('drugs', $id);
        if (!$drug) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error',
                'message' => 'Drug not found.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
            return;
        }

        // Perform activation (set is_active = 1)
        $data = [
            'is_active' => 1,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $updated = $this->General_model->update('drugs', ['id' => $id], $data);

        if ($updated) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'success',
                'message' => 'Drug activated successfully.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error',
                'message' => 'Failed to activate drug.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        }
    }

    /**
     * Hard delete drug
     */
    public function delete($id) {
        // Only accept POST requests
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        // Verify drug exists
        $drug = $this->General_model->getById('drugs', $id);
        if (!$drug) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error',
                'message' => 'Drug not found.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
            return;
        }

        // Perform hard delete
        $deleted = $this->General_model->delete('drugs', ['id' => $id]);

        if ($deleted) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'success',
                'message' => 'Drug deleted permanently.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error',
                'message' => 'Failed to delete drug.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        }
    }

    /**
     * Search endpoint for drug autocomplete
     * Returns JSON list of {id, drug_name}
     */
    public function search() {
        $term = $this->input->get('term', TRUE);
        if (empty($term) || strlen($term) < 1) {
            $this->output->set_content_type('application/json')->set_output(json_encode([]));
            return;
        }

        // Query active drugs matching term
        $this->db->select('id, drug_name');
        $this->db->from('drugs');
        $this->db->like('drug_name', $term);
        $this->db->where('is_active', 1);
        $this->db->limit(10);
        $results = $this->db->get()->result_array();

        $this->output->set_content_type('application/json')->set_output(json_encode($results));
    }
}
