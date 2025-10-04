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
    $data['courses'] = $this->elearning_admin_model->get_all_courses();
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

    public function get_all_course()
    {
        $courses = $this->elearning_admin_model->get_all_courses();
        if($courses){
            return $course;
        }else{
            set_alert('warning','No Course avaliable');
        }
    }

        public function get_course($id)
    {
        $courses = $this->elearning_admin_model->get_course($id);
        if($courses){
            echo "<pre>";
            print_r($courses);
            echo "</pre>";
        }else{
            set_alert('warning','No Course avaliable');
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
        

        $success = $this->elearning_admin_model->create_student_account($post_data);
        
        if ($success) {
            set_alert('success', 'Student account created successfully');
        } else {
            set_alert('warning', 'Error creating student account');
        }
        
        redirect(admin_url('klms/Lms_admin/students'));
    }

    $data['title'] = 'Add New Student';
    $this->load->view('admin/add_student', $data);
}


    /**
     * List all students
     */
    public function students()
{
    $students = $this->db->get(db_prefix() . 'contacts')->result_array();
    $data['students'] = $students ? $students : [];
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
