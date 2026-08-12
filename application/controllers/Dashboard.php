<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public function __construct() {
        parent::__construct();
        // Since we extend MY_Controller, session authentication check is run automatically
    }

    /**
     * Dashboard Landing Page
     */
    public function index() {
        $data['title'] = 'Dashboard';

        // Mock statistics (default numbers as requested for Step 1)
        $data['stats'] = [
            'total_prescriptions' => 128,
            'total_interactions'  => 42,
            'severe_alerts'       => 15,
            'moderate_alerts'     => 18
        ];

        // Mock recent prescriptions data (last 10 rows)
        $data['recent_prescriptions'] = [
            [
                'date'               => '2026-08-03',
                'patient_name'       => 'Alice Smith',
                'drugs_count'        => 3,
                'interactions_count' => 2,
                'severity'           => 'Severe'
            ],
            [
                'date'               => '2026-08-03',
                'patient_name'       => 'Robert Chen',
                'drugs_count'        => 2,
                'interactions_count' => 1,
                'severity'           => 'Moderate'
            ],
            [
                'date'               => '2026-08-02',
                'patient_name'       => 'Eleanor Vance',
                'drugs_count'        => 4,
                'interactions_count' => 3,
                'severity'           => 'Severe'
            ],
            [
                'date'               => '2026-08-02',
                'patient_name'       => 'John Davis',
                'drugs_count'        => 3,
                'interactions_count' => 0,
                'severity'           => 'None'
            ],
            [
                'date'               => '2026-08-01',
                'patient_name'       => 'Sarah Jenkins',
                'drugs_count'        => 2,
                'interactions_count' => 1,
                'severity'           => 'Mild'
            ],
            [
                'date'               => '2026-08-01',
                'patient_name'       => 'Michael Chang',
                'drugs_count'        => 5,
                'interactions_count' => 4,
                'severity'           => 'Severe'
            ],
            [
                'date'               => '2026-07-31',
                'patient_name'       => 'Emma Watson',
                'drugs_count'        => 2,
                'interactions_count' => 2,
                'severity'           => 'Severe'
            ],
            [
                'date'               => '2026-07-30',
                'patient_name'       => 'James Wilson',
                'drugs_count'        => 3,
                'interactions_count' => 2,
                'severity'           => 'Severe'
            ],
            [
                'date'               => '2026-07-30',
                'patient_name'       => 'Sophia Martinez',
                'drugs_count'        => 4,
                'interactions_count' => 2,
                'severity'           => 'Moderate'
            ],
            [
                'date'               => '2026-07-29',
                'patient_name'       => 'David Miller',
                'drugs_count'        => 2,
                'interactions_count' => 1,
                'severity'           => 'Moderate'
            ]
        ];

        // Load templates and index view
        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/index', $data);
        $this->load->view('templates/footer', $data);
    }
}
