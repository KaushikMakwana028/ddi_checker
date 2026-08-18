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

    /**
     * Download Sample CSV for Drugs
     */
    public function sample_csv() {
        $filename = 'drugs_import_sample.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Write Header
        fputcsv($output, ['Drug Name', 'Synonyms', 'Category', 'Quantity', 'Unit']);

        // Write Sample Rows
        fputcsv($output, ['Aspirin', 'acetylsalicylic acid, ASA', 'Analgesics', '150', 'tablets']);
        fputcsv($output, ['Warfarin', 'Coumadin, Jantoven', 'Anticoagulants', '80', 'mg']);
        fputcsv($output, ['Ibuprofen', 'Advil, Motrin', 'NSAIDs', '200', 'tablets']);

        fclose($output);
        exit;
    }

    /**
     * Export Drugs to CSV
     */
    public function export() {
        $sql = "SELECT drug_name, synonyms, category, quantity, unit,
                       IF(is_active = 1, 'Active', 'Inactive') AS status,
                       created_at
                FROM drugs
                ORDER BY id ASC";

        $drugs = $this->General_model->query($sql);

        $filename = 'drug_registry_export_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Write CSV Header
        fputcsv($output, ['Drug Name', 'Synonyms', 'Category', 'Quantity', 'Unit', 'Status', 'Date Added']);

        foreach ($drugs as $row) {
            fputcsv($output, [
                $row['drug_name'],
                $row['synonyms'] ?? '',
                $row['category'] ?? '',
                $row['quantity'],
                $row['unit'] ?? '',
                $row['status'],
                $row['created_at']
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Bulk Import Drugs (Excel / CSV)
     */
    public function import() {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        if (empty($_FILES['csv_file']['name']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'error',
                'message'   => 'Please choose a valid Excel (.xlsx) or CSV file to upload.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
            return;
        }

        // Validate file extension
        $file_name = $_FILES['csv_file']['name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!in_array($ext, ['csv', 'xlsx'])) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'error',
                'message'   => 'Invalid file format. Only Excel (.xlsx) and CSV (.csv) files are supported.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
            return;
        }

        $tmp_file = $_FILES['csv_file']['tmp_name'];
        $rows = [];

        if ($ext === 'csv') {
            $handle = fopen($tmp_file, 'r');
            if (!$handle) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'error',
                    'message'   => 'Unable to read the uploaded CSV file.',
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }
            while (($r = fgetcsv($handle, 2048, ',')) !== FALSE) {
                $rows[] = $r;
            }
            fclose($handle);
        } else {
            // Excel XLSX parsing
            require_once APPPATH . 'libraries/SimpleXLSXReader.php';
            $rows = SimpleXLSXReader::parse($tmp_file);
            if ($rows === false) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'error',
                    'message'   => 'Unable to parse the uploaded Excel file. Please ensure it is a standard .xlsx workbook.',
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }
        }

        if (empty($rows)) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'error',
                'message'   => 'The uploaded file contains no data rows.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
            return;
        }

        // Preload existing drugs case-insensitively to avoid duplicates
        $all_drugs = $this->db->select('drug_name')->get('drugs')->result_array();
        $existing_names = [];
        foreach ($all_drugs as $d) {
            $existing_names[strtolower(trim($d['drug_name']))] = true;
        }

        $validation_errors = [];
        $validated_rows = [];
        $seen_in_csv = [];

        // Check if first row is header
        $startIdx = 0;
        $firstRowCol0 = strtolower(trim($rows[0][0] ?? ''));
        if (strpos($firstRowCol0, 'drug') !== false || strpos($firstRowCol0, 'name') !== false) {
            $startIdx = 1; // skip header row
        }

        for ($i = $startIdx; $i < count($rows); $i++) {
            $row = $rows[$i];
            $row_number = $i + 1;

            if (empty(array_filter($row, 'strlen'))) {
                continue;
            }

            $drug_name = trim($row[0] ?? '');
            $synonyms  = trim($row[1] ?? '');
            $category  = trim($row[2] ?? '');
            $quantity  = trim($row[3] ?? '');
            $unit      = trim($row[4] ?? '');

            if (empty($drug_name)) {
                $validation_errors[] = "Row {$row_number}: Drug name is missing.";
                continue;
            }

            if (strlen($drug_name) < 2) {
                $validation_errors[] = "Row {$row_number}: Drug name must be at least 2 characters long.";
            }

            if ($quantity === '') {
                $validation_errors[] = "Row {$row_number}: Quantity is missing.";
            } elseif (!is_numeric($quantity) || intval($quantity) != $quantity || intval($quantity) < 0) {
                $validation_errors[] = "Row {$row_number}: Quantity must be a non-negative integer (got '{$quantity}').";
            }

            $key = strtolower($drug_name);
            if (isset($seen_in_csv[$key])) {
                // If it's a duplicate name in the CSV itself, skip it, or log it
            } else {
                $seen_in_csv[$key] = true;
            }

            if (empty($validation_errors)) {
                $validated_rows[] = [
                    'drug_name' => $drug_name,
                    'synonyms' => !empty($synonyms) ? $synonyms : null,
                    'category' => !empty($category) ? $category : null,
                    'quantity' => intval($quantity),
                    'unit' => !empty($unit) ? $unit : null
                ];
            }
        }

        if (!empty($validation_errors)) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'error',
                'message'   => 'Import failed. Some rows could not be validated.',
                'errors'    => $validation_errors,
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
            return;
        }

        // Pass 2: Execution (atomic database transactions)
        $this->db->trans_begin();
        $admin_id = $this->session->userdata('user_id');
        $now = date('Y-m-d H:i:s');
        $imported = 0;
        $skipped  = 0;
        $seen_inserted = [];

        foreach ($validated_rows as $v_row) {
            $drug_name = $v_row['drug_name'];
            $key = strtolower($drug_name);

            if (isset($existing_names[$key]) || isset($seen_inserted[$key])) {
                $skipped++;
                continue;
            }

            $insert_data = [
                'drug_name'  => $drug_name,
                'synonyms'   => $v_row['synonyms'],
                'category'   => $v_row['category'],
                'quantity'   => $v_row['quantity'],
                'unit'       => $v_row['unit'],
                'is_active'  => 1,
                'created_by' => $admin_id,
                'created_at' => $now,
                'updated_at' => $now
            ];

            $this->General_model->insert('drugs', $insert_data);
            $seen_inserted[$key] = true;
            $imported++;
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'error',
                'message'   => 'Database error encountered during bulk import. Changes reverted.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        } else {
            $this->db->trans_commit();
            $msg = "Import completed: {$imported} drug(s) imported, {$skipped} duplicate(s) skipped.";
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'success',
                'message'   => $msg,
                'imported'  => $imported,
                'skipped'   => $skipped,
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        }
    }
}
