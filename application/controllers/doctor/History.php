<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Require Doctor_Controller if not already loaded
if (!class_exists('Doctor_Controller')) {
    require_once APPPATH . 'core/Doctor_Controller.php';
}

/**
 * Doctor History Controller
 * 
 * Manages the prescription history for the logged-in doctor,
 * search filters, date-range filtering, and Excel export.
 */
class History extends Doctor_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Display prescription history with statistics, search, date-range filters, and pagination
     */
    public function index($offset = 0) {
        $doctor_id = $this->session->userdata('doctor_user_id');
        $search = trim($this->input->get('search', TRUE) ?? '');
        $from_date = trim($this->input->get('from_date', TRUE) ?? '');
        $to_date = trim($this->input->get('to_date', TRUE) ?? '');

        // Date range validation
        $date_error = '';
        if (!empty($from_date) && !empty($to_date)) {
            if (strtotime($from_date) > strtotime($to_date)) {
                $date_error = 'From Date must be before or equal to To Date.';
            }
        }

        // Apply date filters ONLY if validation is clean
        $applied_from = empty($date_error) ? $from_date : '';
        $applied_to = empty($date_error) ? $to_date : '';

        // Get total rows with combined filters
        $total_rows = $this->General_model->get_history_count($doctor_id, $search, $applied_from, $applied_to);

        // Load and configure CodeIgniter Pagination Library
        $this->load->library('pagination');
        $config['base_url']             = base_url('doctor/history');
        $config['total_rows']           = $total_rows;
        $config['per_page']             = 25;
        $config['uri_segment']          = 3;
        $config['reuse_query_string']   = TRUE;

        // Custom HTML tags to integrate Bootstrap styling
        $config['full_tag_open']    = '<ul class="pagination pagination-sm justify-content-center m-0">';
        $config['full_tag_close']   = '</ul>';
        $config['first_link']       = 'First';
        $config['last_link']        = 'Last';
        $config['first_tag_open']   = '<li class="page-item">';
        $config['first_tag_close']  = '</li>';
        $config['prev_link']        = '<i class="bi bi-chevron-left"></i>';
        $config['prev_tag_open']    = '<li class="page-item">';
        $config['prev_tag_close']   = '</li>';
        $config['next_link']        = '<i class="bi bi-chevron-right"></i>';
        $config['next_tag_open']    = '<li class="page-item">';
        $config['next_tag_close']   = '</li>';
        $config['last_tag_open']    = '<li class="page-item">';
        $config['last_tag_close']   = '</li>';
        $config['cur_tag_open']     = '<li class="page-item active"><a class="page-link border-0 text-white" style="background-color: #0f766e;" href="#">';
        $config['cur_tag_close']    = '</a></li>';
        $config['num_tag_open']     = '<li class="page-item">';
        $config['num_tag_close']    = '</li>';
        $config['attributes']       = ['class' => 'page-link', 'style' => 'color: #0f766e; border-color: #e2e8f0;'];

        $this->pagination->initialize($config);

        // Fetch records with combined filters
        $prescriptions = $this->General_model->get_paginated_history($doctor_id, $config['per_page'], $offset, $search, $applied_from, $applied_to);

        // Load stats (always all-time / daily totals scoped to doctor)
        $stats = $this->General_model->get_history_stats($doctor_id);

        $data['title']          = 'Patient History';
        $data['breadcrumb']     = 'History';
        $data['prescriptions']  = $prescriptions;
        $data['stats']          = $stats;
        $data['search']         = $search;
        $data['from_date']      = $from_date;
        $data['to_date']        = $to_date;
        $data['date_error']     = $date_error;
        $data['pagination']     = $this->pagination->create_links();
        $data['total_count']    = $total_rows;

        // Render views
        $this->load->view('doctor/layout/header', $data);
        $this->load->view('doctor/history/index', $data);
        $this->load->view('doctor/layout/footer', $data);
    }

    /**
     * View specific invoice with strict doctor ownership verification
     */
    public function view_invoice($prescription_id) {
        $doctor_id = $this->session->userdata('doctor_user_id');

        if (empty($prescription_id)) {
            $this->session->set_flashdata('error', 'Invalid prescription reference.');
            redirect('doctor/history');
        }

        // Fetch prescription and verify existence
        $prescription = $this->General_model->getOne('prescriptions', ['id' => $prescription_id]);
        if (!$prescription) {
            $this->session->set_flashdata('error', 'Access Denied: Prescription record not found.');
            redirect('doctor/history');
        }

        // STRICTOR OWNERSHIP SCOPE VERIFICATION
        if ((int)$prescription->doctor_id !== (int)$doctor_id) {
            $this->session->set_flashdata('error', 'Access Denied: You do not have permission to view another practitioner\'s prescription.');
            redirect('doctor/history');
        }

        // Fetch details
        $doctor_user = $this->General_model->getById('users', $doctor_id);
        $doctor_profile = $this->General_model->getOne('doctor_profiles', ['user_id' => $doctor_id]);
        $patient = $this->General_model->getById('patients', $prescription->patient_id);

        // Fetch items
        $saved_items = $this->db->select('pi.id, pi.drug_id, pi.dosage, pi.frequency, pi.duration, pi.special_instructions, d.drug_name')
            ->from('prescription_items pi')
            ->join('drugs d', 'd.id = pi.drug_id')
            ->where('pi.prescription_id', $prescription_id)
            ->get()
            ->result_array();

        // Package invoice data matching JS schema shape
        $data['invoice'] = [
            'prescription_id' => $prescription->id,
            'invoice_number'  => $prescription->invoice_number,
            'visit_date'      => date('d-M-Y', strtotime($prescription->visit_date)),
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
                'contact_number' => !empty($patient->contact_number) ? $patient->contact_number : '',
            ],
            'items' => $saved_items
        ];

        $data['title']      = 'View Prescription: ' . $prescription->invoice_number;
        $data['breadcrumb'] = 'View Prescription';

        // Render views
        $this->load->view('doctor/layout/header', $data);
        $this->load->view('doctor/history/invoice', $data);
        $this->load->view('doctor/layout/footer', $data);
    }

    /**
     * Export filtered prescription history to a clean Microsoft Excel (.xlsx) sheet
     */
    public function export() {
        $doctor_id = $this->session->userdata('doctor_user_id');
        $search = trim($this->input->get('search', TRUE) ?? '');
        $from_date = trim($this->input->get('from_date', TRUE) ?? '');
        $to_date = trim($this->input->get('to_date', TRUE) ?? '');

        // Validate date-range
        if (!empty($from_date) && !empty($to_date) && strtotime($from_date) > strtotime($to_date)) {
            $this->session->set_flashdata('error', 'From Date must be before or equal to To Date.');
            redirect('doctor/history');
        }

        // Fetch filtered dataset
        $prescriptions = $this->General_model->get_all_history_for_export($doctor_id, $search, $from_date, $to_date);

        // Load doctor details for filename
        $doctor_user = $this->General_model->getById('users', $doctor_id);
        $doctor_name = $doctor_user ? $doctor_user->name : 'doctor';

        // Build Excel document using PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Prescription History');

        // Document Headers
        $headers = [
            'Invoice No.',
            'Visit Date',
            'Patient Name',
            'Contact Number',
            'Age',
            'Gender',
            'Medicines Prescribed',
            '# Medicines'
        ];

        // Populate Headers
        foreach ($headers as $index => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($colLetter . '1', $header);
        }

        // Populate Records
        $rowNum = 2;
        foreach ($prescriptions as $p) {
            $sheet->setCellValue('A' . $rowNum, $p['invoice_number']);
            $sheet->setCellValue('B' . $rowNum, date('d-M-Y', strtotime($p['visit_date'])));
            $sheet->setCellValue('C' . $rowNum, $p['patient_name']);
            $sheet->setCellValue('D' . $rowNum, $p['patient_contact'] ?: '—');
            $sheet->setCellValue('E' . $rowNum, $p['patient_age']);
            $sheet->setCellValue('F' . $rowNum, $p['patient_gender']);
            $sheet->setCellValue('G' . $rowNum, $p['medicines_list'] ?: 'No medicines');
            $sheet->setCellValue('H' . $rowNum, $p['medicine_count']);
            $rowNum++;
        }

        $highestRow = $rowNum - 1;

        // Apply gorgeous branding and formatting
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF0F766E'], // Deep Teal Theme Color
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ];

        // Style the Header Row
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Apply borders and alignments across data cells
        if ($highestRow >= 2) {
            $borderStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFD1D5DB'], // Light gray borders
                    ],
                ],
            ];
            $sheet->getStyle('A1:H' . $highestRow)->applyFromArray($borderStyle);

            // Set alignment rules
            $sheet->getStyle('A2:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E2:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H2:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C2:D' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('G2:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }

        // Auto-fit columns (except G which contains long text lists)
        foreach (range('A', 'H') as $col) {
            if ($col === 'G') {
                $sheet->getColumnDimension($col)->setAutoSize(false)->setWidth(55);
                $sheet->getStyle('G2:G' . $rowNum)->getAlignment()->setWrapText(true);
            } else {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        // Establish the file name parameters
        if (!empty($from_date) && !empty($to_date)) {
            $filename = "prescription-history-{$from_date}-to-{$to_date}.xlsx";
        } elseif (!empty($from_date)) {
            $filename = "prescription-history-from-{$from_date}-to-all.xlsx";
        } elseif (!empty($to_date)) {
            $filename = "prescription-history-up-to-{$to_date}.xlsx";
        } else {
            $clean_doc = preg_replace('/[^a-zA-Z0-9\-]/', '-', $doctor_name);
            $clean_doc = strtolower(trim(preg_replace('/-+/', '-', $clean_doc), '-'));
            $date_stamp = date('Ymd');
            $filename = "prescription-history-{$clean_doc}-all-{$date_stamp}.xlsx";
        }

        // Clear output buffer to ensure clean file download stream
        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
