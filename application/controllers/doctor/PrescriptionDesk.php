<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Require Doctor_Controller if not already loaded
if (!class_exists('Doctor_Controller')) {
    require_once APPPATH . 'core/Doctor_Controller.php';
}

/**
 * Doctor Prescription Desk Controller
 * 
 * Handles patient intake form tab operations, validation, and auto-fetching.
 */
class PrescriptionDesk extends Doctor_Controller {

    public function __construct() {
        parent::__construct();
        // Doctor session authentication is automatically enforced by Doctor_Controller
    }

    /**
     * Display Prescription Desk Dashboard
     */
    public function index() {
        $doctor_id = $this->session->userdata('doctor_user_id');

        $data['title']      = 'Prescription Desk';
        $data['breadcrumb'] = 'Prescription Desk';

        // Fetch 5 most recently created/updated patients for the logged-in doctor
        $data['recent_patients'] = $this->db->select('*')
            ->from('patients')
            ->where('doctor_id', $doctor_id)
            ->order_by('updated_at', 'DESC')
            ->order_by('id', 'DESC')
            ->limit(5)
            ->get()
            ->result();

        $this->load->view('doctor/layout/header', $data);
        $this->load->view('doctor/prescription_desk/index', $data);
        $this->load->view('doctor/layout/footer', $data);
    }

    /**
     * Auto-fetch patient profile by phone number (AJAX GET)
     */
    public function fetch_patient() {
        $phone = $this->input->get('phone', TRUE);
        $doctor_id = $this->session->userdata('doctor_user_id');

        if (empty($phone)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Phone number is required.'
                ]));
        }

        // Strip non-digit characters from the input
        $clean_phone = preg_replace('/\D/', '', $phone);

        if (strlen($clean_phone) < 10) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Phone number must be at least 10 digits.'
                ]));
        }

        // Query: SELECT * FROM patients WHERE doctor_id = ? AND REPLACE(REPLACE(contact_number,'-',''),' ','') = ? LIMIT 1
        $sql = "SELECT * FROM patients WHERE doctor_id = ? AND REPLACE(REPLACE(contact_number, '-', ''), ' ', '') = ? LIMIT 1";
        $patient = $this->db->query($sql, [$doctor_id, $clean_phone])->row();

        if ($patient) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'found',
                    'patient' => $patient
                ]));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'not_found'
                ]));
        }
    }

    /**
     * Save patient profile (AJAX POST)
     */
    public function save_patient() {
        // Set validation rules
        $this->form_validation->set_rules('full_name', 'Patient Full Name', 'required|trim|max_length[150]');
        $this->form_validation->set_rules('age', 'Age', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('gender', 'Gender', 'required|in_list[Male,Female,Other]');
        $this->form_validation->set_rules('contact_number', 'Contact Number', 'trim|max_length[20]');
        $this->form_validation->set_rules('height_cm', 'Height', 'trim|numeric|greater_than[0]');
        $this->form_validation->set_rules('weight_kg', 'Weight', 'trim|numeric|greater_than[0]');
        $this->form_validation->set_rules('chief_complaints', 'Chief Complaints', 'trim');
        $this->form_validation->set_rules('medical_history', 'Medical History', 'trim');

        if ($this->form_validation->run() === FALSE) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => validation_errors(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
        }

        $patient_id = $this->input->post('patient_id', TRUE);
        $doctor_id  = $this->session->userdata('doctor_user_id');

        $data = [
            'full_name'        => trim($this->input->post('full_name', TRUE)),
            'contact_number'   => trim($this->input->post('contact_number', TRUE)),
            'age'              => (int)$this->input->post('age', TRUE),
            'gender'           => $this->input->post('gender', TRUE),
            'chief_complaints' => trim($this->input->post('chief_complaints', TRUE)),
            'height_cm'        => $this->input->post('height_cm', TRUE) !== '' ? (float)$this->input->post('height_cm', TRUE) : NULL,
            'weight_kg'        => $this->input->post('weight_kg', TRUE) !== '' ? (float)$this->input->post('weight_kg', TRUE) : NULL,
            'medical_history'  => trim($this->input->post('medical_history', TRUE)),
            'updated_at'       => date('Y-m-d H:i:s')
        ];

        if (!empty($patient_id)) {
            // Verify ownership
            $exists = $this->General_model->exists('patients', ['id' => $patient_id, 'doctor_id' => $doctor_id]);
            if (!$exists) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(403)
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'Unauthorized operation: Patient record not found or does not belong to you.',
                        'csrf_hash' => $this->security->get_csrf_hash()
                    ]));
            }

            // Update patient
            $this->General_model->update('patients', ['id' => $patient_id], $data);
            $saved_id = $patient_id;
        } else {
            // Insert patient
            $data['doctor_id']  = $doctor_id;
            $data['created_at'] = date('Y-m-d H:i:s');
            $saved_id = $this->General_model->insert('patients', $data);
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'patient_id' => $saved_id,
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
    }

    /**
     * Search drugs autocomplete endpoint (AJAX GET)
     */
    public function search_drugs() {
        $term = $this->input->get('term', TRUE);
        
        if (empty($term)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([]));
        }

        // Search active drugs matching term by drug_name, category, or synonyms
        $drugs = $this->db->select('id, drug_name, category, unit, quantity')
            ->from('drugs')
            ->where('is_active', 1)
            ->group_start()
                ->like('drug_name', $term)
                ->or_like('category', $term)
                ->or_like('synonyms', $term)
            ->group_end()
            ->order_by('drug_name', 'ASC')
            ->limit(20)
            ->get()
            ->result();

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($drugs));
    }

    /**
     * Save prescription and items (AJAX POST)
     */
    public function save_prescription() {
        $doctor_id = $this->session->userdata('doctor_user_id');
        $patient_id = $this->input->post('patient_id', TRUE);
        $prescription_id = $this->input->post('prescription_id', TRUE);
        
        if (empty($patient_id)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Patient ID is required.',
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
        }

        // 1. Verify patient ownership
        $patient_exists = $this->General_model->exists('patients', ['id' => $patient_id, 'doctor_id' => $doctor_id]);
        if (!$patient_exists) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Unauthorized operation or patient record not found.',
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
        }

        // Read and parse items JSON
        $items_raw = $this->input->post('items');
        $items = !empty($items_raw) ? json_decode($items_raw, TRUE) : [];
        if (!is_array($items)) {
            $items = [];
        }

        // Require at least 1 medicine item
        if (empty($items)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Add at least one medicine before generating an invoice.',
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
        }

        // Verify if prescription is already finalized and belongs to the logged-in doctor
        if (!empty($prescription_id)) {
            $rx = $this->General_model->getOne('prescriptions', ['id' => $prescription_id, 'doctor_id' => $doctor_id]);
            if (!$rx) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(403)
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'Unauthorized operation or prescription not found.',
                        'csrf_hash' => $this->security->get_csrf_hash()
                    ]));
            }
            if (!empty($rx->invoice_number)) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'This prescription has already been finalized and cannot be modified.',
                        'csrf_hash' => $this->security->get_csrf_hash()
                    ]));
            }
        }

        // 4. Validate required fields per item before writing to DB
        foreach ($items as $idx => $item) {
            $row_num = $idx + 1;
            $drug_name = !empty($item['drug_name']) ? $item['drug_name'] : "Medicine {$row_num}";

            if (empty($item['drug_id'])) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => "Row {$row_num} ({$drug_name}): Medicine selection is invalid or missing.",
                        'csrf_hash' => $this->security->get_csrf_hash()
                    ]));
            }
            if (empty($item['dosage']) || trim($item['dosage']) === '') {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => "Row {$row_num} ({$drug_name}): Dosage is required (e.g. 500mg).",
                        'csrf_hash' => $this->security->get_csrf_hash()
                    ]));
            }
            if (empty($item['frequency'])) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => "Row {$row_num} ({$drug_name}): Frequency is required.",
                        'csrf_hash' => $this->security->get_csrf_hash()
                    ]));
            }
            if (empty($item['duration'])) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => "Row {$row_num} ({$drug_name}): Duration is required.",
                        'csrf_hash' => $this->security->get_csrf_hash()
                    ]));
            }
        }

        // Generate Unique Invoice Number sequence
        $prefix = 'RX' . date('Ymd');
        $this->db->select_max('invoice_number')
            ->from('prescriptions')
            ->where('doctor_id', $doctor_id)
            ->like('invoice_number', $prefix, 'after');
        $res = $this->db->get()->row();
        $max_invoice = isset($res->invoice_number) ? $res->invoice_number : NULL;

        $seq = 1;
        if ($max_invoice) {
            $last_seq = (int)substr($max_invoice, -3);
            $seq = $last_seq + 1;
        }

        $invoice_number = $prefix . sprintf('%03d', $seq);

        // Double check uniqueness globally to prevent collision
        $collision_limit = 100;
        while ($this->General_model->exists('prescriptions', ['invoice_number' => $invoice_number]) && $collision_limit > 0) {
            $seq++;
            $invoice_number = $prefix . sprintf('%03d', $seq);
            $collision_limit--;
        }

        // Start database transaction
        $this->db->trans_begin();

        // 2. Insert parent prescription if not exists, otherwise verify ownership
        if (empty($prescription_id)) {
            $prescription_data = [
                'invoice_number' => $invoice_number,
                'patient_id'     => $patient_id,
                'doctor_id'      => $doctor_id,
                'visit_date'     => date('Y-m-d'),
                'created_at'     => date('Y-m-d H:i:s')
            ];
            $prescription_id = $this->General_model->insert('prescriptions', $prescription_data);
        } else {
            $rx_exists = $this->General_model->exists('prescriptions', ['id' => $prescription_id, 'doctor_id' => $doctor_id]);
            if (!$rx_exists) {
                $this->db->trans_rollback();
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(403)
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'Unauthorized operation or prescription not found.',
                        'csrf_hash' => $this->security->get_csrf_hash()
                    ]));
            }
            // Update invoice number
            $this->General_model->update('prescriptions', ['id' => $prescription_id], ['invoice_number' => $invoice_number]);
        }

        // 3. Insert or update items
        foreach ($items as $idx => &$item) {
            $item_data = [
                'prescription_id'      => $prescription_id,
                'drug_id'              => $item['drug_id'],
                'dosage'               => trim($item['dosage']),
                'frequency'            => trim($item['frequency']),
                'duration'             => trim($item['duration']),
                'special_instructions' => (!empty($item['special_instructions']) ? trim($item['special_instructions']) : NULL)
            ];

            if (!empty($item['id'])) {
                // Verify item belongs to a prescription owned by this doctor
                $item_id = $item['id'];
                $item_db = $this->db->select('prescription_items.*')
                    ->from('prescription_items')
                    ->join('prescriptions', 'prescriptions.id = prescription_items.prescription_id')
                    ->where('prescription_items.id', $item_id)
                    ->where('prescriptions.doctor_id', $doctor_id)
                    ->get()
                    ->row();

                if ($item_db) {
                    $this->General_model->update('prescription_items', ['id' => $item_id], $item_data);
                } else {
                    $this->db->trans_rollback();
                    return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(403)
                        ->set_output(json_encode([
                            'status' => 'error',
                            'message' => 'Unauthorized operation on prescription item.',
                            'csrf_hash' => $this->security->get_csrf_hash()
                        ]));
                }
            } else {
                // Insert item
                $item_data['created_at'] = date('Y-m-d H:i:s');
                $inserted_id = $this->General_model->insert('prescription_items', $item_data);
                $item['id'] = $inserted_id;
            }
        }

        // 5. Transaction commit/rollback
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Failed to save prescription items in database.',
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
        } else {
            $this->db->trans_commit();

            // Fetch complete doctor profiles for invoice headers
            $doctor_user = $this->General_model->getById('users', $doctor_id);
            $doctor_profile = $this->General_model->getOne('doctor_profiles', ['user_id' => $doctor_id]);
            $patient = $this->General_model->getById('patients', $patient_id);

            // Reload saved items with full drug names
            $saved_items = $this->db->select('pi.id, pi.drug_id, pi.dosage, pi.frequency, pi.duration, pi.special_instructions, d.drug_name')
                ->from('prescription_items pi')
                ->join('drugs d', 'd.id = pi.drug_id')
                ->where('pi.prescription_id', $prescription_id)
                ->get()
                ->result_array();

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'          => 'success',
                    'prescription_id' => $prescription_id,
                    'invoice_number'  => $invoice_number,
                    'visit_date'      => date('d-M-Y'),
                    'doctor'          => [
                        'name'                => !empty($doctor_user->name) ? (preg_match('/^Dr\.?/i', $doctor_user->name) ? $doctor_user->name : 'Dr. ' . $doctor_user->name) : '',
                        'qualification'       => !empty($doctor_profile->qualification) ? $doctor_profile->qualification : 'MBBS',
                        'specialization'      => !empty($doctor_profile->specialization) ? $doctor_profile->specialization : 'General Practice',
                        'registration_number' => !empty($doctor_profile->registration_number) ? $doctor_profile->registration_number : 'N/A',
                        'hospital_clinic'     => !empty($doctor_profile->hospital_clinic) ? $doctor_profile->hospital_clinic : 'DDI Clinic',
                        'address'             => !empty($doctor_user->address) ? $doctor_user->address : '',
                        'signature'           => !empty($doctor_profile->signature) ? base_url($doctor_profile->signature) : ''
                    ],
                    'patient'         => [
                        'full_name'      => !empty($patient->full_name) ? $patient->full_name : '',
                        'age'            => !empty($patient->age) ? $patient->age : '',
                        'gender'         => !empty($patient->gender) ? $patient->gender : '',
                        'contact_number' => !empty($patient->contact_number) ? $patient->contact_number : ''
                    ],
                    'items'           => $saved_items,
                    'csrf_hash'       => $this->security->get_csrf_hash()
                ]));
        }
    }

    /**
     * Check interactions between drug items in the active prescription tab list (AJAX POST)
     */
    public function check_interactions() {
        $doctor_id = $this->session->userdata('doctor_user_id');
        $items_raw = $this->input->post('items');
        
        $items = !empty($items_raw) ? json_decode($items_raw, TRUE) : [];
        if (!is_array($items)) {
            $items = [];
        }

        $drug_ids = [];
        foreach ($items as $item) {
            if (!empty($item['drug_id'])) {
                $drug_ids[] = (int)$item['drug_id'];
            }
        }
        $drug_ids = array_unique(array_filter($drug_ids));

        if (count($drug_ids) < 2) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'interactions' => [],
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
        }

        // Query interactions where both drug_a_id and drug_b_id are in the selected drugs list
        $interactions = $this->db->select('i.id, i.drug_a_id, i.drug_b_id, i.severity, i.remarks, i.source, da.drug_name as drug_a_name, db.drug_name as drug_b_name')
            ->from('interactions i')
            ->join('drugs da', 'da.id = i.drug_a_id')
            ->join('drugs db', 'db.id = i.drug_b_id')
            ->where_in('i.drug_a_id', $drug_ids)
            ->where_in('i.drug_b_id', $drug_ids)
            ->where('i.is_active', 1)
            ->get()
            ->result_array();

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'interactions' => $interactions,
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
    }

    /**
     * Delete prescription item (AJAX POST)
     */
    public function remove_item($item_id) {
        $doctor_id = $this->session->userdata('doctor_user_id');

        if (empty($item_id)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Item ID is required.',
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
        }

        // Verify parent prescription ownership
        $item_db = $this->db->select('prescription_items.*')
            ->from('prescription_items')
            ->join('prescriptions', 'prescriptions.id = prescription_id')
            ->where('prescription_items.id', $item_id)
            ->where('prescriptions.doctor_id', $doctor_id)
            ->get()
            ->row();

        if (!$item_db) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Unauthorized operation or item not found.',
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
        }

        $this->General_model->delete('prescription_items', ['id' => $item_id]);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'message' => 'Item removed successfully.',
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
    }
}
