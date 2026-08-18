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
}
