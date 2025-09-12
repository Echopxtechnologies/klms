<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Elearning_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add_course($data)
    {
        $insert = [
            'title'       => $data['title'],
            'category'    => $data['category'],
            'description' => $data['description']
        ];
        $this->db->insert('elearning_courses', $insert);
        return $this->db->insert_id();
    }

    public function get_courses()
    {
        return $this->db->get('elearning_courses')->result_array();
    }
}
