<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lms_admin extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('elearning_admin_model'); // class elearning_admin_model
        $this->load->language('klms', 'english');
        $this->load->model('clients_model');
        $this->load->model('authentication_model');
    }

    public function courses()
{
    $data['courses'] = $this->elearning_admin_model->get_courses();
    $data['title']   = _l('klms_courses'); // Use language helper
    // $this->load->view('admin/add_course', $data); // Keep your existing view path
    $this->load->view('admin/manage_course', $data);
}

public function add_course()
{
    if ($this->input->post()) {
        $course_data = $this->input->post();
        
        // Handle file upload
        if (!empty($_FILES['cover_image']['name'])) {
            $upload_config = [
                'upload_path'   => './uploads/courses/',
                'allowed_types' => 'gif|jpg|jpeg|png',
                'max_size'      => 2048, // 2MB
                'max_width'     => 2000,
                'max_height'    => 2000,
                'encrypt_name'  => true
            ];
            
            // Create directory if it doesn't exist
            if (!is_dir('./uploads/courses/')) {
                mkdir('./uploads/courses/', 0755, true);
            }
            
            $this->load->library('upload', $upload_config);
            
            if ($this->upload->do_upload('cover_image')) {
                $upload_data = $this->upload->data();
                $course_data['cover_image'] = 'uploads/courses/' . $upload_data['file_name'];
            } else {
                set_alert('warning', 'Image upload failed: ' . $this->upload->display_errors());
                redirect(admin_url('klms/Lms_admin/add_course'));
                return;
            }
        }
        
        if ($this->elearning_admin_model->add_course($course_data)) {
            set_alert('success', 'Course added successfully');
        } else {
            set_alert('warning', 'Error adding course');
        }
        
        redirect(admin_url('klms/Lms_admin/courses'));
    }

    $data['title'] = 'Add New Course';
    $this->load->view('admin/add_course', $data);
}

    public function get_course()
    {
        $courses = $this->elearning_admin_model->get_course();
        if($courses){
            echo "hello";
        }else{
            echo "world";
        }
    }
    /**
 * Edit existing course
 */
public function edit_course($course_id = '')
{
    if (empty($course_id) || !is_numeric($course_id)) {
        show_404();
    }

    $course = $this->elearning_admin_model->get_course($course_id);
    if (!$course) {
        show_404();
    }

    if ($this->input->post()) {
        $course_data = $this->input->post();
        
        // Handle file upload
        if (!empty($_FILES['cover_image']['name'])) {
            $upload_config = [
                'upload_path'   => './uploads/courses/',
                'allowed_types' => 'gif|jpg|jpeg|png',
                'max_size'      => 2048,
                'max_width'     => 2000,
                'max_height'    => 2000,
                'encrypt_name'  => true
            ];
            
            if (!is_dir('./uploads/courses/')) {
                mkdir('./uploads/courses/', 0755, true);
            }
            
            $this->load->library('upload', $upload_config);
            
            if ($this->upload->do_upload('cover_image')) {
                // Delete old image if exists
                if (!empty($course['cover_image']) && file_exists($course['cover_image'])) {
                    unlink($course['cover_image']);
                }
                
                $upload_data = $this->upload->data();
                $course_data['cover_image'] = 'uploads/courses/' . $upload_data['file_name'];
            } else {
                set_alert('warning', 'Image upload failed: ' . $this->upload->display_errors());
            }
        }
        
        if ($this->elearning_admin_model->update_course($course_id, $course_data)) {
            set_alert('success', 'Course updated successfully');
        } else {
            set_alert('warning', 'Error updating course');
        }
        
        redirect(admin_url('klms/Lms_admin/edit_course/' . $course_id));
    }

    $data['course'] = $course;
    $data['title'] = 'Edit Course - ' . $course['title'];
    $this->load->view('admin/edit_course', $data);
}
    public function delete_course($id)
    {
        echo "<pre>";
        print($id);
        echo "</pre>";
    }
    public function manage_videos($course_id = '')
    {
        if(empty($course_id) || !is_numeric($course_id)){
            show_404();
        }
        $course = $this->elearning_admin_model->get_course($course_id);

        if(!$course){
            show_404();
        }
        $videos = $this->elearning_admin_model->get_course_videos($course_id);

        $data['course'] = $course;
        $data['videos'] = $videos;
        $data['title'] = 'Manage Videos - ' . $course['title'];
        
        $this->load->view('admin/manage_videos', $data);

    }
     public function add_video($course_id = '')
    {
        if (empty($course_id) || !is_numeric($course_id)) {
            show_404();
        }

        $course = $this->elearning_admin_model->get_course($course_id);
        if (!$course) {
            show_404();
        }

        if ($this->input->post()) {
            $video_data = $this->input->post();
            $video_data['course_id'] = $course_id;
            
            if ($this->elearning_admin_model->add_video($video_data)) {
                set_alert('success', 'Video added successfully');
            } else {
                set_alert('warning', 'Error adding video');
            }
            redirect(admin_url('klms/Lms_admin/manage_videos/' . $course_id));
        }

        $data['course'] = $course;
        $data['title'] = 'Add Video - ' . $course['title'];
        $this->load->view('admin/add_video', $data);
    }

    /**
     * Edit existing video
     */
    public function edit_video($course_id = '', $video_id = '')
    {
        if (empty($course_id) || !is_numeric($course_id) || empty($video_id) || !is_numeric($video_id)) {
            show_404();
        }

        $course = $this->elearning_admin_model->get_course($course_id);
        $video = $this->elearning_admin_model->get_video($video_id);
        
        if (!$course || !$video) {
            show_404();
        }

        if ($this->input->post()) {
            $video_data = $this->input->post();
            
            if ($this->elearning_admin_model->update_video($video_id, $video_data)) {
                set_alert('success', 'Video updated successfully');
            } else {
                set_alert('warning', 'Error updating video');
            }
            redirect(admin_url('lms_admin/manage_videos/' . $course_id));
        }

        $data['course'] = $course;
        $data['video'] = $video;
        $data['title'] = 'Edit Video - ' . $video['title'];
        $this->load->view('admin/edit_video', $data);
    }

    /**
     * Delete video
     */
    public function delete_video($course_id = '', $video_id = '')
    {
        if (empty($course_id) || !is_numeric($course_id) || empty($video_id) || !is_numeric($video_id)) {
            redirect(admin_url('lms_admin/courses'));
        }

        if ($this->elearning_admin_model->delete_video($video_id)) {
            set_alert('success', 'Video deleted successfully');
        } else {
            set_alert('warning', 'Error deleting video');
        }
        
        redirect(admin_url('klms/Lms_admin/manage_videos/' . $course_id));
    }


public function add_student()
{
    if ($this->input->post()) {
        $post_data = $this->input->post();
        
        // Debug: Check what data is being submitted
        echo "<pre>POST Data: ";
        print_r($post_data);
        echo "</pre>";
        
        $success = $this->create_student_account($post_data);
        
        echo "<pre>Create result: ";
        var_dump($success);
        echo "</pre>";
        die(); // Stop here to see the debug output
        
        if ($success) {
            set_alert('success', 'Student account created successfully');
        } else {
            set_alert('warning', 'Error creating student account');
        }
        
        redirect(admin_url('lms_admin/students'));
    }

    $data['title'] = 'Add New Student';
    $this->load->view('admin/add_student', $data);
}
public function check_table_structure()
{
    $query = $this->db->query("DESCRIBE " . db_prefix() . "clients");
    $columns = $query->result_array();
    
    echo "<h3>Table Structure for tblclients:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

    /**
     * Create student account (similar to Authentication::register)
     */
   private function create_student_account($data)
{
    echo "<h3>Step 1: Input Validation</h3>";
    
    if (empty($data['firstname']) || empty($data['lastname']) || empty($data['email'])) {
        echo "❌ Validation failed - missing required fields<br>";
        return false;
    }
    echo "✅ Validation passed<br>";

    echo "<h3>Step 2: Check Email Exists in Contacts</h3>";
    
    // Check in contacts table, not clients table
    $this->db->where('email', $data['email']);
    $existing = $this->db->get(db_prefix() . 'contacts')->row();
    
    if ($existing) {
        echo "❌ Email already exists in contacts: " . $data['email'] . "<br>";
        return false;
    }
    echo "✅ Email is unique<br>";

    echo "<h3>Step 3: Insert into Contacts Table</h3>";
    
    $password = !empty($data['password']) ? $data['password'] : $this->generate_random_password();
    
    // Data for contacts table
    $contact_data = [
        'firstname'    => $data['firstname'],
        'lastname'     => $data['lastname'], 
        'email'        => $data['email'],
        'phonenumber'  => isset($data['phonenumber']) ? $data['phonenumber'] : '',
        'title'        => isset($data['title']) ? $data['title'] : 'Student',
        'password'     => password_hash($password, PASSWORD_DEFAULT),
        'datecreated'  => date('Y-m-d H:i:s'),
        'active'       => 1,
        'is_primary'   => 1,
        'userid'       => 0, // 0 for individual contacts not linked to a client
    ];

    echo "<pre>Contact data to insert: ";
    print_r($contact_data);
    echo "</pre>";

    // Insert into contacts table
    $this->db->insert(db_prefix() . 'contacts', $contact_data);
    
    if ($this->db->affected_rows() > 0) {
        $contact_id = $this->db->insert_id();
        echo "✅ Student contact created successfully with ID: " . $contact_id . "<br>";
        return $contact_id;
    } else {
        echo "❌ Insert failed<br>";
        $error = $this->db->error();
        echo "Database error: ";
        print_r($error);
        return false;
    }
}


private function generate_random_password($length = 8)
{
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    return substr(str_shuffle($characters), 0, $length);
}
    /**
     * List all students
     */
    // public function students()
    // {
    //     $data['students'] = $this->clients_model->get();
    //     $data['title'] = 'Students Management';
    //     $this->load->view('admin/students_list', $data);
    // }

    public function students()
{
    $students = $this->db->get(db_prefix() . 'contacts')->result_array();
    $data['students'] = $students ? $students : [];
    
    // Debug: Check the data structure
    // if (!empty($students)) {
    //     echo "<pre>";
    //     print_r($students[0]); // Show first student structure
    //     echo "</pre>";
    //     die();
    // }
    
    $data['students'] = $students;
    $data['title'] = 'Students Management';
    $this->load->view('admin/students_list', $data);
}







/**
 * ================================== This code is for only testing don't delete it ===============
 * 
 */
//     public function test_courses()
// {
//     $courses = $this->elearning_admin_model->get_courses();
    
//     echo "<h3>Testing get_courses() function:</h3>";
//     echo "<pre>";
//     var_dump($courses);
//     echo "</pre>";
// }
}
