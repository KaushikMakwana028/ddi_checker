<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Require Admin_Controller if not already loaded
if (!class_exists('Admin_Controller')) {
    require_once APPPATH . 'core/Admin_Controller.php';
}

/**
 * Admin PatientManage Controller
 * 
 * Allows administrators to list and view all patients, 
 * including associated medical practitioners and clinical hospitals.
 */
class PatientManage extends Admin_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Patients List Page
     */
    public function index() {
        $data['title']      = 'Patients';
        $data['breadcrumb'] = 'Patients';

        $search = trim($this->input->get('search', TRUE) ?? '');

        // Precompute statistics via optimized queries
        $data['total_patients']  = $this->General_model->getCount('patients');
        $data['male_count']      = $this->db->where('LOWER(gender)', 'male')->count_all_results('patients');
        $data['female_count']    = $this->db->where('LOWER(gender)', 'female')->count_all_results('patients');
        
        $hosp_query = $this->db->query("SELECT COUNT(DISTINCT LOWER(TRIM(dp.hospital_clinic))) as count 
                                        FROM patients p
                                        JOIN doctor_profiles dp ON dp.user_id = p.doctor_id 
                                        WHERE dp.hospital_clinic IS NOT NULL AND dp.hospital_clinic != ''");
        $data['unique_hospitals'] = $hosp_query->row()->count ?? 0;

        // If AJAX request, return raw data in JSON for client-side JavaScript rendering
        if ($this->input->is_ajax_request() || $this->input->get('ajax') == 1) {
            $limit_param = $this->input->get('limit');
            if ($limit_param === '-1') {
                $limit = 999999;
            } else {
                $limit = (int)$limit_param;
                if ($limit <= 0) {
                    $limit = 10; // default
                }
            }

            $page = max(1, (int)$this->input->get('page'));
            $total_rows = $this->General_model->get_patients_count($search);
            $total_pages = max(1, ceil($total_rows / $limit));
            if ($page > $total_pages && $total_rows > 0) {
                $page = $total_pages;
            }
            $offset = ($page - 1) * $limit;

            $patients = $this->General_model->get_paginated_patients($limit, $offset, $search);

            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'       => 'success',
                'patients'     => $patients,
                'total_rows'   => $total_rows,
                'total_pages'  => $total_pages,
                'current_page' => $page,
                'limit'        => $limit,
                'offset'       => $offset,
                'stats'        => [
                    'total_patients'   => $data['total_patients'],
                    'male_count'       => $data['male_count'],
                    'female_count'     => $data['female_count'],
                    'unique_hospitals' => $data['unique_hospitals']
                ]
            ]));
            return;
        }

        $data['patients'] = [];
        $data['search']   = $search;

        // Load admin layouts and patients view
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/patient_manage/index', $data);
        $this->load->view('admin/layout/footer', $data);
    }

    /**
     * View Patient Details and Prescription History
     */
    public function view($patient_id) {
        if (empty($patient_id)) {
            $this->session->set_flashdata('error', 'Invalid patient reference.');
            redirect('admin/patients');
        }

        // Fetch patient
        $sql_patient = "SELECT p.*, u.name AS doctor_name, dp.hospital_clinic AS hospital_name
                        FROM patients p
                        LEFT JOIN users u ON u.id = p.doctor_id
                        LEFT JOIN doctor_profiles dp ON dp.user_id = p.doctor_id
                        WHERE p.id = ? LIMIT 1";
        
        $patient = $this->db->query($sql_patient, [$patient_id])->row();

        if (!$patient) {
            $this->session->set_flashdata('error', 'Patient record not found.');
            redirect('admin/patients');
        }

        // Fetch prescription history of patient
        $sql_presc = "SELECT pr.*, u.name AS doctor_name, dp.hospital_clinic AS hospital_name, COUNT(pi.id) AS medicine_count
                      FROM prescriptions pr
                      LEFT JOIN users u ON u.id = pr.doctor_id
                      LEFT JOIN doctor_profiles dp ON dp.user_id = pr.doctor_id
                      LEFT JOIN prescription_items pi ON pi.prescription_id = pr.id
                      WHERE pr.patient_id = ?
                      GROUP BY pr.id
                      ORDER BY pr.created_at DESC, pr.id DESC";

        $prescriptions = $this->db->query($sql_presc, [$patient_id])->result_array();

        $data['title']          = 'Patient Profile: ' . $patient->full_name;
        $data['breadcrumb']     = 'Patient Profile';
        $data['patient']        = $patient;
        $data['prescriptions']  = $prescriptions;

        // Load views
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/patient_manage/view', $data);
        $this->load->view('admin/layout/footer', $data);
    }
}
