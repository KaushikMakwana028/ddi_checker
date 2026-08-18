<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Require Admin_Controller if not already loaded
if (!class_exists('Admin_Controller')) {
    require_once APPPATH . 'core/Admin_Controller.php';
}

/**
 * Admin Interaction Rules Controller
 * 
 * Manages the clinical Drug-Drug Interaction rules database.
 * Enforces pair normalization: drug_a_id is always stored SMALLER than drug_b_id.
 */
class InteractionRules extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        // Admin authentication and role = 1 verified by Admin_Controller
    }

    /**
     * Interaction Rules List Page with Server-Side Pagination and Filtering
     */
    public function index() {
        $data['title']      = 'Interaction Rules';
        $data['breadcrumb'] = 'Interaction Rules';

        // Filtering parameters
        $search   = trim($this->input->get('search', TRUE) ?? '');
        $severity = trim($this->input->get('severity', TRUE) ?? '');
        if (!in_array($severity, ['Mild', 'Moderate', 'Severe', 'MAJOR', 'Not known interaction found'])) {
            $severity = '';
        }

        // Pagination parameters
        $limit_param = $this->input->get('limit');
        if ($limit_param === '-1') {
            $limit = 999999;
        } else {
            $limit = (int)$limit_param;
            if ($limit <= 0) {
                $limit = 10; // default
            }
        }
        $page  = max(1, (int)$this->input->get('page'));
        $total_rows = $this->General_model->countInteractions($search, $severity);
        $total_pages = max(1, ceil($total_rows / $limit));
        if ($page > $total_pages && $total_rows > 0) {
            $page = $total_pages;
        }
        $offset = ($page - 1) * $limit;

        // Fetch paginated records
        $data['interactions'] = $this->General_model->getInteractions($limit, $offset, $search, $severity);
        
        // Summary metrics
        $data['stats'] = [
            'total'     => $this->General_model->getCount('interactions'),
            'severe'    => $this->General_model->getCount('interactions', ['severity' => 'Severe']),
            'major'     => $this->General_model->getCount('interactions', ['severity' => 'MAJOR']),
            'moderate'  => $this->General_model->getCount('interactions', ['severity' => 'Moderate']),
            'mild'      => $this->General_model->getCount('interactions', ['severity' => 'Mild']),
            'not_known' => $this->General_model->getCount('interactions', ['severity' => 'Not known interaction found']),
            'active'    => $this->General_model->getCount('interactions', ['is_active' => 1])
        ];

        // Pass pagination info to view
        $data['search']      = $search;
        $data['severity']    = $severity;
        $data['current_page']= $page;
        $data['total_pages'] = $total_pages;
        $data['total_rows']  = $total_rows;
        $data['limit']       = $limit;
        $data['offset']      = $offset;

        // If AJAX request, return raw data in JSON for client-side JavaScript rendering
        if ($this->input->is_ajax_request() || $this->input->get('ajax') == 1) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'       => 'success',
                'interactions' => $data['interactions'],
                'total_rows'   => $total_rows,
                'total_pages'  => $total_pages,
                'current_page' => $page,
                'limit'        => $limit,
                'offset'       => $offset,
                'stats'        => $data['stats']
            ]));
            return;
        }

        // Load admin layout and view
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/interaction_rules/index', $data);
        $this->load->view('admin/layout/footer', $data);
    }

    /**
     * Add new Interaction Rule page and submission handler
     */
    public function add() {
        if ($this->input->method() === 'get') {
            $data['title']      = 'Add Interaction Rule';
            $data['breadcrumb'] = 'Add Rule';

            // Preload active drugs list sorted by name
            $data['drugs'] = $this->db->order_by('drug_name', 'ASC')->get_where('drugs', ['is_active' => 1])->result_array();
            $data['drug_a_id'] = (int)$this->input->get('drug_a_id');
            $data['drug_b_id'] = (int)$this->input->get('drug_b_id');

            $this->load->view('admin/layout/header', $data);
            $this->load->view('admin/interaction_rules/add', $data);
            $this->load->view('admin/layout/footer', $data);
            return;
        }

        $this->form_validation->set_rules('drug_a_id', 'Drug A', 'required|integer');
        $this->form_validation->set_rules('drug_b_id', 'Drug B', 'required|integer');
        $this->form_validation->set_rules('severity', 'Severity', 'required|in_list[Mild,Moderate,Severe,MAJOR,Not known interaction found]');
        $this->form_validation->set_rules('remarks', 'Clinical Remarks', 'required|trim');
        $this->form_validation->set_rules('source', 'Clinical Source', 'trim|max_length[255]');

        if ($this->form_validation->run() === FALSE) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'error',
                    'message'   => strip_tags(validation_errors()),
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $data['title']      = 'Add Interaction Rule';
            $data['breadcrumb'] = 'Add Rule';
            $data['drugs']      = $this->db->order_by('drug_name', 'ASC')->get_where('drugs', ['is_active' => 1])->result_array();
            $this->session->set_flashdata('error', validation_errors());
            $this->load->view('admin/layout/header', $data);
            $this->load->view('admin/interaction_rules/add', $data);
            $this->load->view('admin/layout/footer', $data);
            return;
        }

        $drug_a_id = (int)$this->input->post('drug_a_id');
        $drug_b_id = (int)$this->input->post('drug_b_id');
        $severity  = trim($this->input->post('severity', TRUE));
        $remarks   = trim($this->input->post('remarks', TRUE));
        $source    = trim($this->input->post('source', TRUE));

        // Validate distinct drugs
        if ($drug_a_id === $drug_b_id) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'error',
                    'message'   => 'Drug A and Drug B cannot be the exact same medicine.',
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $this->session->set_flashdata('error', 'Drug A and Drug B cannot be the exact same medicine.');
            redirect('admin/interactions/add');
            return;
        }

        // Validate existence in drugs table
        $drug_a = $this->General_model->getById('drugs', $drug_a_id);
        $drug_b = $this->General_model->getById('drugs', $drug_b_id);
        if (!$drug_a || !$drug_b) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'error',
                    'message'   => 'One or both selected drugs could not be found in the registry.',
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $this->session->set_flashdata('error', 'One or both selected drugs could not be found in the registry.');
            redirect('admin/interactions/add');
            return;
        }

        // CRITICAL NORMALIZATION: drug_a_id must always be stored SMALLER than drug_b_id
        if ($drug_a_id > $drug_b_id) {
            $temp = $drug_a_id;
            $drug_a_id = $drug_b_id;
            $drug_b_id = $temp;
        }

        // Check for existing rule for this normalized pair
        $existing = $this->General_model->getOne('interactions', [
            'drug_a_id' => $drug_a_id,
            'drug_b_id' => $drug_b_id
        ]);

        if ($existing) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'      => 'error',
                    'message'     => 'This drug pair already has an interaction rule — edit it instead.',
                    'existing_id' => $existing->id,
                    'csrf_name'   => $this->security->get_csrf_token_name(),
                    'csrf_hash'   => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $this->session->set_flashdata('error', 'This drug pair already has an interaction rule.');
            redirect('admin/interactions/edit/' . $existing->id);
            return;
        }

        // Execute Transaction Insert
        $this->db->trans_begin();

        $interaction_data = [
            'drug_a_id'   => $drug_a_id,
            'drug_b_id'   => $drug_b_id,
            'severity'    => $severity,
            'remarks'     => $remarks,
            'source'      => !empty($source) ? $source : NULL,
            'is_active'   => 1,
            'created_by'  => $this->session->userdata('user_id'),
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        $insert_id = $this->General_model->insert('interactions', $interaction_data);

        if ($this->db->trans_status() === FALSE || !$insert_id) {
            $this->db->trans_rollback();
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'error',
                    'message'   => 'Failed to save interaction rule due to a database error.',
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $this->session->set_flashdata('error', 'Failed to save interaction rule due to a database error.');
            redirect('admin/interactions/add');
        } else {
            $this->db->trans_commit();
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'success',
                    'message'   => 'Interaction rule created successfully.',
                    'redirect'  => base_url('admin/interactions'),
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $this->session->set_flashdata('success', 'Interaction rule created successfully.');
            redirect('admin/interactions');
        }
    }

    /**
     * Edit existing Interaction Rule page and submission handler
     */
    public function edit($id = NULL) {
        if (!$id) {
            redirect('admin/interactions');
        }

        $rule = $this->General_model->getInteractionById($id);
        if (!$rule) {
            $this->session->set_flashdata('error', 'Interaction rule not found.');
            redirect('admin/interactions');
        }

        if ($this->input->method() === 'get') {
            $data['title']       = 'Edit Interaction Rule';
            $data['breadcrumb']  = 'Edit Rule';
            $data['rule']        = $rule;
            $data['drugs']       = $this->db->order_by('drug_name', 'ASC')->get_where('drugs', ['is_active' => 1])->result_array();

            $this->load->view('admin/layout/header', $data);
            $this->load->view('admin/interaction_rules/edit', $data);
            $this->load->view('admin/layout/footer', $data);
            return;
        }

        $this->form_validation->set_rules('drug_a_id', 'Drug A', 'required|integer');
        $this->form_validation->set_rules('drug_b_id', 'Drug B', 'required|integer');
        $this->form_validation->set_rules('severity', 'Severity', 'required|in_list[Mild,Moderate,Severe,MAJOR,Not known interaction found]');
        $this->form_validation->set_rules('remarks', 'Clinical Remarks', 'required|trim');
        $this->form_validation->set_rules('source', 'Clinical Source', 'trim|max_length[255]');

        if ($this->form_validation->run() === FALSE) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'error',
                    'message'   => strip_tags(validation_errors()),
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $data['title']       = 'Edit Interaction Rule';
            $data['breadcrumb']  = 'Edit Rule';
            $data['rule']        = $rule;
            $data['drugs']       = $this->db->order_by('drug_name', 'ASC')->get_where('drugs', ['is_active' => 1])->result_array();
            $this->session->set_flashdata('error', validation_errors());
            $this->load->view('admin/layout/header', $data);
            $this->load->view('admin/interaction_rules/edit', $data);
            $this->load->view('admin/layout/footer', $data);
            return;
        }

        $drug_a_id = (int)$this->input->post('drug_a_id');
        $drug_b_id = (int)$this->input->post('drug_b_id');
        $severity  = trim($this->input->post('severity', TRUE));
        $remarks   = trim($this->input->post('remarks', TRUE));
        $source    = trim($this->input->post('source', TRUE));

        if ($drug_a_id === $drug_b_id) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'error',
                    'message'   => 'Drug A and Drug B cannot be the exact same medicine.',
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $this->session->set_flashdata('error', 'Drug A and Drug B cannot be the exact same medicine.');
            redirect('admin/interactions/edit/' . $id);
            return;
        }

        // Validate existence
        $drug_a = $this->General_model->getById('drugs', $drug_a_id);
        $drug_b = $this->General_model->getById('drugs', $drug_b_id);
        if (!$drug_a || !$drug_b) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'error',
                    'message'   => 'One or both selected drugs could not be found.',
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $this->session->set_flashdata('error', 'One or both selected drugs could not be found.');
            redirect('admin/interactions/edit/' . $id);
            return;
        }

        // Normalize order
        if ($drug_a_id > $drug_b_id) {
            $temp = $drug_a_id;
            $drug_a_id = $drug_b_id;
            $drug_b_id = $temp;
        }

        // Duplicate check excluding current ID
        $existing = $this->General_model->getOne('interactions', [
            'drug_a_id' => $drug_a_id,
            'drug_b_id' => $drug_b_id,
            'id !='     => $id
        ]);

        if ($existing) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'      => 'error',
                    'message'     => 'Another interaction rule already exists for this drug pair.',
                    'existing_id' => $existing->id,
                    'csrf_name'   => $this->security->get_csrf_token_name(),
                    'csrf_hash'   => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $this->session->set_flashdata('error', 'Another interaction rule already exists for this drug pair.');
            redirect('admin/interactions/edit/' . $id);
            return;
        }

        $this->db->trans_begin();

        $update_data = [
            'drug_a_id'  => $drug_a_id,
            'drug_b_id'  => $drug_b_id,
            'severity'   => $severity,
            'remarks'    => $remarks,
            'source'     => !empty($source) ? $source : NULL,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->General_model->update('interactions', ['id' => $id], $update_data);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'error',
                    'message'   => 'Failed to update interaction rule due to database error.',
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $this->session->set_flashdata('error', 'Failed to update interaction rule due to database error.');
            redirect('admin/interactions/edit/' . $id);
        } else {
            $this->db->trans_commit();
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'    => 'success',
                    'message'   => 'Interaction rule updated successfully.',
                    'redirect'  => base_url('admin/interactions'),
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $this->session->set_flashdata('success', 'Interaction rule updated successfully.');
            redirect('admin/interactions');
        }
    }

    /**
     * Soft Delete (Deactivate) Rule
     */
    public function deactivate($id) {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        $rule = $this->General_model->getById('interactions', $id);
        if (!$rule) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'error',
                'message'   => 'Interaction rule not found.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
            return;
        }

        $updated = $this->General_model->update('interactions', ['id' => $id], [
            'is_active'  => 0,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($updated) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'success',
                'message'   => 'Interaction rule deactivated.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'error',
                'message'   => 'Failed to deactivate interaction rule.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        }
    }

    /**
     * Reactivate Rule
     */
    public function activate($id) {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        $rule = $this->General_model->getById('interactions', $id);
        if (!$rule) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'error',
                'message'   => 'Interaction rule not found.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
            return;
        }

        $updated = $this->General_model->update('interactions', ['id' => $id], [
            'is_active'  => 1,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($updated) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'success',
                'message'   => 'Interaction rule reactivated.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'error',
                'message'   => 'Failed to reactivate interaction rule.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        }
    }

    /**
     * Hard Delete Rule
     */
    public function delete($id) {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        $rule = $this->General_model->getById('interactions', $id);
        if (!$rule) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'error',
                'message'   => 'Interaction rule not found.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
            return;
        }

        $deleted = $this->General_model->delete('interactions', ['id' => $id]);

        if ($deleted) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'success',
                'message'   => 'Interaction rule permanently deleted.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'error',
                'message'   => 'Failed to delete interaction rule.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        }
    }

    /**
     * Download Sample CSV / Excel format template
     */
    public function sample_csv() {
        $filename = 'sample_interaction_rules_template.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Header
        fputcsv($output, ['Drug A', 'Drug B', 'Severity', 'Remarks', 'Source Citation']);

        // Sample data rows
        fputcsv($output, ['Aspirin', 'Warfarin', 'Severe', 'Co-administration significantly elevates bleeding and severe hemorrhage risk.', 'FDA Black Box Warning']);
        fputcsv($output, ['Ibuprofen', 'Lisinopril', 'Moderate', 'NSAIDs may reduce the antihypertensive effect of ACE inhibitors and increase nephrotoxicity risk.', "Stockley's Drug Interactions"]);
        fputcsv($output, ['Metformin', 'Cimetidine', 'Mild', 'Cimetidine reduces metformin clearance; monitor blood glucose levels closely.', 'Clinical Pharmacology']);

        fclose($output);
        exit;
    }

    /**
     * Bulk CSV & Excel (.xlsx) Import with validation and reporting
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

        // Preload drugs for fast case-insensitive memory lookup
        $all_drugs = $this->db->select('id, drug_name')->get('drugs')->result_array();
        $drug_map = [];
        foreach ($all_drugs as $d) {
            $drug_map[strtolower(trim($d['drug_name']))] = (int)$d['id'];
        }

        $validation_errors = [];
        $validated_rows = [];

        // Check if first row is header
        $startIdx = 0;
        $firstRowCol0 = strtolower(trim($rows[0][0] ?? ''));
        if (strpos($firstRowCol0, 'drug') !== false || strpos($firstRowCol0, 'medicine') !== false) {
            $startIdx = 1; // skip header row
        }

        for ($i = $startIdx; $i < count($rows); $i++) {
            $row = $rows[$i];
            $row_number = $i + 1;

            if (empty(array_filter($row, 'strlen'))) {
                continue;
            }

            $drug_a_name = trim($row[0] ?? '');
            $drug_b_name = trim($row[1] ?? '');
            $raw_severity = trim($row[2] ?? '');
            $remarks     = trim($row[3] ?? '');
            $source      = trim($row[4] ?? '');

            if (empty($drug_a_name) || empty($drug_b_name)) {
                $validation_errors[] = "Row {$row_number}: Drug A or Drug B name is missing.";
                continue;
            }

            $key_a = strtolower($drug_a_name);
            $key_b = strtolower($drug_b_name);

            $id_a = null;
            $id_b = null;

            if (!isset($drug_map[$key_a])) {
                $validation_errors[] = "Row {$row_number}: Drug A '{$drug_a_name}' is not available in Drug Registry.";
            } else {
                $id_a = $drug_map[$key_a];
            }

            if (!isset($drug_map[$key_b])) {
                $validation_errors[] = "Row {$row_number}: Drug B '{$drug_b_name}' is not available in Drug Registry.";
            } else {
                $id_b = $drug_map[$key_b];
            }

            if ($id_a !== null && $id_b !== null) {
                if ($id_a === $id_b) {
                    $validation_errors[] = "Row {$row_number}: Drug A and Drug B cannot be the same medicine ('{$drug_a_name}').";
                }
            }

            if (empty($raw_severity)) {
                $validation_errors[] = "Row {$row_number}: Severity level is missing.";
            } else {
                $resolved_severity = $this->resolveSeverity($raw_severity);
                if ($resolved_severity === null) {
                    $validation_errors[] = "Row {$row_number}: Severity level '{$raw_severity}' is not available.";
                }
            }

            if (empty($validation_errors)) {
                $validated_rows[] = [
                    'id_a' => $id_a,
                    'id_b' => $id_b,
                    'severity' => isset($resolved_severity) ? $resolved_severity : null,
                    'remarks' => $remarks,
                    'source' => $source
                ];
            }
        }

        if (!empty($validation_errors)) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'    => 'error',
                'message'   => 'Import failed. Some rules could not be validated.',
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
        $seen_pairs = [];

        foreach ($validated_rows as $v_row) {
            $id_a = $v_row['id_a'];
            $id_b = $v_row['id_b'];
            $severity = $v_row['severity'];
            $remarks = $v_row['remarks'];
            $source = $v_row['source'];

            if ($id_a > $id_b) {
                $temp = $id_a;
                $id_a = $id_b;
                $id_b = $temp;
            }

            $pair_key = "{$id_a}_{$id_b}";
            if (isset($seen_pairs[$pair_key])) {
                $skipped++;
                continue;
            }

            $exists = $this->General_model->getOne('interactions', [
                'drug_a_id' => $id_a,
                'drug_b_id' => $id_b
            ]);

            if ($exists) {
                $skipped++;
                $seen_pairs[$pair_key] = true;
                continue;
            }

            $insert_data = [
                'drug_a_id'  => $id_a,
                'drug_b_id'  => $id_b,
                'severity'   => $severity,
                'remarks'    => !empty($remarks) ? $remarks : 'Clinical interaction monitoring advised.',
                'source'     => !empty($source) ? $source : NULL,
                'is_active'  => 1,
                'created_by' => $admin_id,
                'created_at' => $now,
                'updated_at' => $now
            ];

            $this->General_model->insert('interactions', $insert_data);
            $seen_pairs[$pair_key] = true;
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
            $msg = "Import completed: {$imported} rule(s) imported, {$skipped} duplicate pair(s) skipped.";
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

    /**
     * Export all active Interaction Rules to CSV
     */
    public function export() {
        $sql = "SELECT da.drug_name AS drug_a_name, db.drug_name AS drug_b_name,
                       i.severity, i.remarks, i.source,
                       IF(i.is_active = 1, 'Active', 'Inactive') AS status,
                       i.created_at
                FROM interactions i
                LEFT JOIN drugs da ON da.id = i.drug_a_id
                LEFT JOIN drugs db ON db.id = i.drug_b_id
                ORDER BY i.id ASC";

        $interactions = $this->General_model->query($sql);

        $filename = 'interaction_rules_export_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Write CSV Header
        fputcsv($output, ['Drug A', 'Drug B', 'Severity', 'Remarks', 'Source Citation', 'Status', 'Date Added']);

        foreach ($interactions as $row) {
            fputcsv($output, [
                $row['drug_a_name'] ?? 'Unknown Drug A',
                $row['drug_b_name'] ?? 'Unknown Drug B',
                $row['severity'],
                $row['remarks'],
                $row['source'] ?? '',
                $row['status'],
                $row['created_at']
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Resolve and fuzzy-correct severity levels
     */
    private function resolveSeverity($raw) {
        $s = strtolower(trim($raw));
        if (empty($s)) return null;

        // Exact match checks
        $exact_map = [
            'mild' => 'Mild',
            'moderate' => 'Moderate',
            'severe' => 'Severe',
            'major' => 'MAJOR',
            'not known interaction found' => 'Not known interaction found',
            'not known interction found' => 'Not known interaction found',
            'not known' => 'Not known interaction found',
            'not_known' => 'Not known interaction found',
            'unknown' => 'Not known interaction found'
        ];
        if (isset($exact_map[$s])) {
            return $exact_map[$s];
        }

        // Substring match checks
        if (strpos($s, 'severe') !== false || strpos($s, 'contraindicated') !== false) {
            return 'Severe';
        }
        if (strpos($s, 'major') !== false) {
            return 'MAJOR';
        }
        if (strpos($s, 'moderate') !== false || strpos($s, 'adjust') !== false || strpos($s, 'monitoring') !== false) {
            return 'Moderate';
        }
        if (strpos($s, 'mild') !== false || strpos($s, 'minor') !== false || strpos($s, 'low risk') !== false) {
            return 'Mild';
        }
        if (strpos($s, 'not known') !== false || strpos($s, 'unknown') !== false || strpos($s, 'no interaction') !== false) {
            return 'Not known interaction found';
        }

        // Fuzzy matching for spelling mistakes
        $candidates = ['Mild', 'Moderate', 'Severe', 'MAJOR', 'Not known interaction found'];
        $best_match = null;
        $shortest = -1;
        foreach ($candidates as $candidate) {
            $lev = levenshtein($s, strtolower($candidate));
            if ($lev <= 2) {
                if ($lev < $shortest || $shortest < 0) {
                    $best_match = $candidate;
                    $shortest = $lev;
                }
            }
        }

        return $best_match;
    }
}
