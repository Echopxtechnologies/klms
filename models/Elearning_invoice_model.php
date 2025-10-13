<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Elearning_invoice_model extends App_Model
{
    private $table = 'klms_invoices';

    public function create_invoice($student_id, $course_id, $total)
    {
        $data = [
            'student_id' => $student_id,
            'course_id' => $course_id,
            'total' => $total,
            'hash' => uniqid('', true),
        ];

        $this->db->insert(db_prefix().$this->table, $data);
        return $this->db->insert_id();
    }

    public function get($invoice_id)
    {
        return $this->db->where('id', $invoice_id)->get(db_prefix().$this->table)->row();
    }

    public function update_status($invoice_id, $status)
    {
        $this->db->where('id', $invoice_id)->update(db_prefix().$this->table, ['status' => $status]);
    }

    public function update_token($invoice_id, $token)
    {
        $this->db->where('id', $invoice_id)->update(db_prefix().$this->table, ['token' => $token]);
    }
}
