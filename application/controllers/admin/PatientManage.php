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

        // Query joining patients with doctors and their profile for hospital names
        $sql = "SELECT p.*, u.name AS doctor_name, dp.hospital_clinic AS hospital_name
                FROM patients p
                LEFT JOIN users u ON u.id = p.doctor_id
                LEFT JOIN doctor_profiles dp ON dp.user_id = p.doctor_id";

        if (!empty($search)) {
            $sql .= " WHERE p.full_name LIKE " . $this->db->escape('%' . $search . '%') . " 
                      OR p.contact_number LIKE " . $this->db->escape('%' . $search . '%') . " 
                      OR u.name LIKE " . $this->db->escape('%' . $search . '%') . " 
                      OR dp.hospital_clinic LIKE " . $this->db->escape('%' . $search . '%');
        }

        $sql .= " ORDER BY p.id DESC";

        $data['patients'] = $this->General_model->query($sql);
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
