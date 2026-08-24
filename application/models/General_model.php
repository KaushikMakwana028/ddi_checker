<?php defined('BASEPATH') or exit('No direct script access allowed');

class General_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /*
    |--------------------------------------------------------------------------
    | Get Single Row
    |--------------------------------------------------------------------------
    */
    public function getOne($table, $where = [])
    {
        return $this->db
            ->where($where)
            ->get($table)
            ->row();
    }

    /*
    |--------------------------------------------------------------------------
    | Get Single Row Array
    |--------------------------------------------------------------------------
    */
    public function getRowArray($table, $where = [])
    {
        return $this->db
            ->where($where)
            ->get($table)
            ->row_array();
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Records (Object)
    |--------------------------------------------------------------------------
    */
    public function getAll($table, $where = [])
    {
        if (!empty($where)) {
            $this->db->where($where);
        }

        return $this->db
            ->order_by('id', 'DESC')
            ->get($table)
            ->result();
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Records (Array)
    |--------------------------------------------------------------------------
    */
    public function getResultArray($table, $where = [])
    {
        if (!empty($where)) {
            $this->db->where($where);
        }

        return $this->db
            ->order_by('id', 'DESC')
            ->get($table)
            ->result_array();
    }

    /*
    |--------------------------------------------------------------------------
    | Insert Record
    |--------------------------------------------------------------------------
    */
    public function insert($table, $data)
    {
        $this->db->insert($table, $data);

        return $this->db->insert_id();
    }

    /*
    |--------------------------------------------------------------------------
    | Update Record
    |--------------------------------------------------------------------------
    */
    public function update($table, $where, $data)
    {
        return $this->db
            ->where($where)
            ->update($table, $data);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Record
    |--------------------------------------------------------------------------
    */
    public function delete($table, $where)
    {
        return $this->db
            ->where($where)
            ->delete($table);
    }

    /*
    |--------------------------------------------------------------------------
    | Count Records
    |--------------------------------------------------------------------------
    */
    public function getCount($table, $where = [])
    {
        if (!empty($where)) {
            $this->db->where($where);
        }

        return $this->db->count_all_results($table);
    }

    /*
    |--------------------------------------------------------------------------
    | Check Exists
    |--------------------------------------------------------------------------
    */
    public function exists($table, $where = [])
    {
        return $this->db
            ->where($where)
            ->count_all_results($table) > 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Custom Query Result Array
    |--------------------------------------------------------------------------
    */
    public function query($sql)
    {
        return $this->db->query($sql)->result_array();
    }

    /*
    |--------------------------------------------------------------------------
    | Custom Query Row
    |--------------------------------------------------------------------------
    */
    public function queryRow($sql)
    {
        return $this->db->query($sql)->row();
    }

    /*
    |--------------------------------------------------------------------------
    | Get By ID
    |--------------------------------------------------------------------------
    */
    public function getById($table, $id)
    {
        return $this->db
            ->where('id', $id)
            ->get($table)
            ->row();
    }

    /*
    |--------------------------------------------------------------------------
    | Get Paginated Interaction Rules Joined with Drug Names
    |--------------------------------------------------------------------------
    */
    public function getInteractions($limit = 25, $offset = 0, $search = '', $severity = '')
    {
        $this->db->select('i.*, da.drug_name AS drug_a_name, db.drug_name AS drug_b_name, u.name AS created_by_name');
        $this->db->from('interactions i');
        $this->db->join('drugs da', 'da.id = i.drug_a_id', 'left');
        $this->db->join('drugs db', 'db.id = i.drug_b_id', 'left');
        $this->db->join('users u', 'u.id = i.created_by', 'left');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('da.drug_name', $search);
            $this->db->or_like('db.drug_name', $search);
            $this->db->or_like('i.remarks', $search);
            $this->db->or_like('i.source', $search);
            $this->db->group_end();
        }

        if (!empty($severity) && in_array($severity, ['Mild', 'Moderate', 'Severe', 'MAJOR', 'Not known interaction found'])) {
            $this->db->where('i.severity', $severity);
        }

        $this->db->order_by('i.id', 'DESC');
        $this->db->limit($limit, $offset);

        return $this->db->get()->result_array();
    }

    /*
    |--------------------------------------------------------------------------
    | Count Interaction Rules with Filters
    |--------------------------------------------------------------------------
    */
    public function countInteractions($search = '', $severity = '')
    {
        $this->db->from('interactions i');
        $this->db->join('drugs da', 'da.id = i.drug_a_id', 'left');
        $this->db->join('drugs db', 'db.id = i.drug_b_id', 'left');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('da.drug_name', $search);
            $this->db->or_like('db.drug_name', $search);
            $this->db->or_like('i.remarks', $search);
            $this->db->or_like('i.source', $search);
            $this->db->group_end();
        }

        if (!empty($severity) && in_array($severity, ['Mild', 'Moderate', 'Severe', 'MAJOR', 'Not known interaction found'])) {
            $this->db->where('i.severity', $severity);
        }

        return $this->db->count_all_results();
    }

    public function getInteractionById($id)
    {
        $this->db->select('i.*, da.drug_name AS drug_a_name, db.drug_name AS drug_b_name');
        $this->db->from('interactions i');
        $this->db->join('drugs da', 'da.id = i.drug_a_id', 'left');
        $this->db->join('drugs db', 'db.id = i.drug_b_id', 'left');
        $this->db->where('i.id', $id);
        return $this->db->get()->row_array();
    }

    /*
    |--------------------------------------------------------------------------
    | Get Paginated Prescription History
    |--------------------------------------------------------------------------
    */
    public function get_paginated_history($doctor_id, $limit, $offset, $search = '', $from_date = '', $to_date = '')
    {
        $this->db->select('pr.*, p.full_name AS patient_name, p.contact_number AS patient_contact, COUNT(pi.id) AS medicine_count');
        $this->db->from('prescriptions pr');
        $this->db->join('patients p', 'p.id = pr.patient_id');
        $this->db->join('prescription_items pi', 'pi.prescription_id = pr.id', 'left');
        $this->db->where('pr.doctor_id', $doctor_id);
        $this->db->where('pr.invoice_number IS NOT NULL');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('p.full_name', $search);
            $this->db->or_like('pr.invoice_number', $search);
            $this->db->group_end();
        }

        if (!empty($from_date)) {
            $this->db->where('pr.visit_date >=', $from_date);
        }
        if (!empty($to_date)) {
            $this->db->where('pr.visit_date <=', $to_date);
        }

        $this->db->group_by('pr.id');
        $this->db->order_by('pr.created_at', 'DESC');
        $this->db->order_by('pr.id', 'DESC');
        $this->db->limit($limit, $offset);

        return $this->db->get()->result_array();
    }

    /*
    |--------------------------------------------------------------------------
    | Count Prescription History
    |--------------------------------------------------------------------------
    */
    public function get_history_count($doctor_id, $search = '', $from_date = '', $to_date = '')
    {
        $this->db->from('prescriptions pr');
        $this->db->join('patients p', 'p.id = pr.patient_id');
        $this->db->where('pr.doctor_id', $doctor_id);
        $this->db->where('pr.invoice_number IS NOT NULL');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('p.full_name', $search);
            $this->db->or_like('pr.invoice_number', $search);
            $this->db->group_end();
        }

        if (!empty($from_date)) {
            $this->db->where('pr.visit_date >=', $from_date);
        }
        if (!empty($to_date)) {
            $this->db->where('pr.visit_date <=', $to_date);
        }

        return $this->db->count_all_results();
    }

    /*
    |--------------------------------------------------------------------------
    | Get History Stats
    |--------------------------------------------------------------------------
    */
    public function get_history_stats($doctor_id)
    {
        $stats = [];
        
        // Total prescriptions
        $stats['total_prescriptions'] = $this->db->where('doctor_id', $doctor_id)
            ->where('invoice_number IS NOT NULL')
            ->count_all_results('prescriptions');
            
        // Prescriptions today
        $stats['prescriptions_today'] = $this->db->where('doctor_id', $doctor_id)
            ->where('invoice_number IS NOT NULL')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->count_all_results('prescriptions');
            
        // Unique Patients Seen
        $stats['unique_patients'] = $this->db->select('COUNT(DISTINCT(patient_id)) as count')
            ->where('doctor_id', $doctor_id)
            ->where('invoice_number IS NOT NULL')
            ->get('prescriptions')
            ->row()->count;
            
        // Last Visit Date
        $last = $this->db->select('visit_date')
            ->where('doctor_id', $doctor_id)
            ->where('invoice_number IS NOT NULL')
            ->order_by('created_at', 'DESC')
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get('prescriptions')
            ->row();
        $stats['last_visit'] = $last ? $last->visit_date : 'N/A';
        
        return $stats;
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Prescription History for Export
    |--------------------------------------------------------------------------
    */
    public function get_all_history_for_export($doctor_id, $search = '', $from_date = '', $to_date = '')
    {
        $this->db->select('pr.*, p.full_name AS patient_name, p.contact_number AS patient_contact, p.age AS patient_age, p.gender AS patient_gender');
        $this->db->from('prescriptions pr');
        $this->db->join('patients p', 'p.id = pr.patient_id');
        $this->db->where('pr.doctor_id', $doctor_id);
        $this->db->where('pr.invoice_number IS NOT NULL');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('p.full_name', $search);
            $this->db->or_like('pr.invoice_number', $search);
            $this->db->group_end();
        }

        if (!empty($from_date)) {
            $this->db->where('pr.visit_date >=', $from_date);
        }
        if (!empty($to_date)) {
            $this->db->where('pr.visit_date <=', $to_date);
        }

        $this->db->order_by('pr.created_at', 'DESC');
        $this->db->order_by('pr.id', 'DESC');

        $prescriptions = $this->db->get()->result_array();
        
        // For each prescription, fetch all items
        foreach ($prescriptions as &$p) {
            $items = $this->db->select('pi.dosage, pi.frequency, pi.duration, d.drug_name')
                ->from('prescription_items pi')
                ->join('drugs d', 'd.id = pi.drug_id')
                ->where('pi.prescription_id', $p['id'])
                ->get()
                ->result_array();
            
            $formatted_items = [];
            foreach ($items as $item) {
                $formatted_items[] = $item['drug_name'] . ' (' . $item['dosage'] . ', ' . $item['frequency'] . ', ' . $item['duration'] . ')';
            }
            
            $p['medicines_list'] = implode('; ', $formatted_items);
            $p['medicine_count'] = count($items);
        }
        
        return $prescriptions;
    }
}
