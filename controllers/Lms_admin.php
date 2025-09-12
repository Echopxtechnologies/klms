<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lms_admin extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('elearning_model'); // class Elearning_model
    }

    public function courses()
{
    $data['courses'] = $this->elearning_model->get_courses();
    $data['title']   = _l('klms_courses'); // Use language helper
    // $this->load->view('admin/add_course', $data); // Keep your existing view path
    $this->load->view('admin/manage_course', $data);
}

    public function add_course()
    {
        if ($this->input->post()) {
            $this->elearning_model->add_course($this->input->post());
            set_alert('success', 'Course added successfully');
            redirect(admin_url('klms/Lms_admin/courses')); // lowercase in URL
        }

        $data['title'] = 'Add New Course';
        $this->load->view('admin/add_course', $data);
    }
    public function get_course()
    {
        $courses = $this->elearning_model->get_course();
        if($courses){
            echo "hello";
        }else{
            echo "world";
        }
    }
    public function edit_course($id)
    {
        echo "<pre>";
        print($id);
        echo "</pre>";
        
    }
    public function delete_course($id)
    {
        echo "<pre>";
        print($id);
        echo "</pre>";
    }


/**
 * ================================== This code is for only testing don't delete it ===============
 * 
 */
//     public function test_courses()
// {
//     $courses = $this->elearning_model->get_courses();
    
//     echo "<h3>Testing get_courses() function:</h3>";
//     echo "<pre>";
//     var_dump($courses);
//     echo "</pre>";
    
// }
}
