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

/**
 * Admin Dashboard - Complete Version
 */
public function dashboard()
{
    if (!is_admin()) {
        access_denied('lms_dashboard');
    }

    // Load model
    $this->load->model('elearning_admin_model');

    // Get all statistics
    $data['stats'] = [
        'total_courses' => $this->elearning_admin_model->count_total_courses(),
        'active_courses' => $this->elearning_admin_model->count_active_courses(),
        'total_videos' => $this->elearning_admin_model->count_total_videos(),
        'total_students' => $this->elearning_admin_model->count_total_students(),
        'enrolled_students' => $this->elearning_admin_model->count_enrolled_students(),
        'active_enrollments' => $this->elearning_admin_model->count_active_enrollments(),
        'pending_payments' => $this->elearning_admin_model->count_pending_payments(),
        'completed_payments' => $this->elearning_admin_model->count_completed_payments(),
        'total_revenue' => $this->elearning_admin_model->get_total_revenue(),
        'monthly_revenue' => $this->elearning_admin_model->get_monthly_revenue(),
        'course_completion_stats' => $this->elearning_admin_model->get_course_completion_stats(),
    ];

    // Get recent data
    $data['recent_enrollments'] = $this->elearning_admin_model->get_recent_enrollments(10);
    $data['recent_students'] = $this->elearning_admin_model->get_recent_students(10);
    $data['popular_courses'] = $this->elearning_admin_model->get_popular_courses(5);
    
    // Get chart data
    $data['enrollment_chart_data'] = $this->elearning_admin_model->get_enrollment_trend_data(30);
    $data['revenue_chart_data'] = $this->elearning_admin_model->get_revenue_trend_data(12);
    $data['category_distribution'] = $this->elearning_admin_model->get_category_distribution();
    $data['payment_status_distribution'] = $this->elearning_admin_model->get_payment_status_distribution();
    $data['recent_activities'] = $this->elearning_admin_model->get_recent_activities(15);

    // Set page title
    $data['title'] = 'LMS Dashboard';
    
    // Load view
    $this->load->view('admin/dashboard', $data);
}

/**
 * Enrollments List - View all enrollments with filtering
 */
public function enrollments()
{
    if (!is_admin()) {
        access_denied('lms_enrollments');
    }

    // Load model
    $this->load->model('elearning_admin_model');

    // Get filter parameters
    $data['payment_status'] = $this->input->get('payment_status');
    $data['access_status'] = $this->input->get('access_status');
    $data['course_id'] = $this->input->get('course_id');

    // Get all courses for filter dropdown
    $data['courses'] = $this->elearning_admin_model->get_all_courses();

    // Statistics
    $data['total_enrollments'] = $this->elearning_admin_model->count_enrolled_students();
    $data['paid_enrollments'] = $this->elearning_admin_model->count_completed_payments();
    $data['pending_enrollments'] = $this->elearning_admin_model->count_pending_payments();
    $data['active_enrollments'] = $this->elearning_admin_model->count_active_enrollments();

    $data['title'] = 'Enrollments Management';
    
    $this->load->view('admin/enrollments', $data);
}

/**
 * Get enrollments data for DataTable (AJAX)
 */
public function get_enrollments_data()
{
    if (!is_admin()) {
        ajax_access_denied();
    }

    $this->load->model('elearning_admin_model');

    // Get DataTable parameters
    $draw = $this->input->post('draw');
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $search = $this->input->post('search')['value'];
    $order_column = $this->input->post('order')[0]['column'];
    $order_dir = $this->input->post('order')[0]['dir'];

    // Get filters
    $payment_status = $this->input->post('payment_status');
    $access_status = $this->input->post('access_status');
    $course_id = $this->input->post('course_id');

    // Column mapping for ordering
    $columns = [
        0 => 'e.id',
        1 => 'cont.firstname',
        2 => 'c.title',
        3 => 'e.enrolled_date',
        4 => 'e.payment_status',
        5 => 'e.access_status',
        6 => 'c.price',
    ];

    // Build query
    $this->db->select('
        e.*,
        c.title as course_title,
        c.price,
        c.is_free,
        c.category,
        cont.firstname,
        cont.lastname,
        cont.email,
        cont.phonenumber,
        cl.company
    ');
    $this->db->from(db_prefix() . 'elearning_enrollments e');
    $this->db->join(db_prefix() . 'elearning_courses c', 'c.id = e.course_id', 'left');
    $this->db->join(db_prefix() . 'contacts cont', 'cont.id = e.student_id', 'left');
    $this->db->join(db_prefix() . 'clients cl', 'cl.userid = cont.userid', 'left');

    // Apply filters
    if ($payment_status) {
        $this->db->where('e.payment_status', $payment_status);
    }
    if ($access_status) {
        $this->db->where('e.access_status', $access_status);
    }
    if ($course_id) {
        $this->db->where('e.course_id', $course_id);
    }

    // Search
    if ($search) {
        $this->db->group_start();
        $this->db->like('cont.firstname', $search);
        $this->db->or_like('cont.lastname', $search);
        $this->db->or_like('cont.email', $search);
        $this->db->or_like('c.title', $search);
        $this->db->or_like('cl.company', $search);
        $this->db->group_end();
    }

    // Count total records
    $total_records = $this->db->count_all_results('', false);

    // Order
    if (isset($columns[$order_column])) {
        $this->db->order_by($columns[$order_column], $order_dir);
    } else {
        $this->db->order_by('e.enrolled_date', 'DESC');
    }

    // Limit
    if ($length != -1) {
        $this->db->limit($length, $start);
    }

    $enrollments = $this->db->get()->result_array();

    // Format data for DataTable
    $data = [];
    foreach ($enrollments as $enrollment) {
        $row = [];
        
        // ID
        $row[] = $enrollment['id'];
        
        // Student
        $student_name = $enrollment['firstname'] . ' ' . $enrollment['lastname'];
        $row[] = '<div class="student-info">
                    <strong>' . html_escape($student_name) . '</strong><br>
                    <small class="text-muted">' . html_escape($enrollment['email']) . '</small>
                  </div>';
        
        // Course
        $row[] = '<div class="course-info">
                    <strong>' . html_escape($enrollment['course_title']) . '</strong><br>
                    <small class="text-muted">' . html_escape($enrollment['category']) . '</small>
                  </div>';
        
        // Enrollment Date
        $row[] = '<small>' . date('M d, Y H:i', strtotime($enrollment['enrolled_date'])) . '</small>';
        
        // Payment Status
        $payment_badge = '';
        switch ($enrollment['payment_status']) {
            case 'paid':
                $payment_badge = '<span class="label label-success">Paid</span>';
                break;
            case 'pending':
                $payment_badge = '<span class="label label-warning">Pending</span>';
                break;
            case 'failed':
                $payment_badge = '<span class="label label-danger">Failed</span>';
                break;
            default:
                $payment_badge = '<span class="label label-default">' . ucfirst($enrollment['payment_status']) . '</span>';
        }
        $row[] = $payment_badge;
        
        // Access Status
        $access_badge = '';
        switch ($enrollment['access_status']) {
            case 'active':
                $access_badge = '<span class="label label-success">Active</span>';
                break;
            case 'inactive':
                $access_badge = '<span class="label label-default">Inactive</span>';
                break;
            case 'suspended':
                $access_badge = '<span class="label label-danger">Suspended</span>';
                break;
            default:
                $access_badge = '<span class="label label-default">' . ucfirst($enrollment['access_status']) . '</span>';
        }
        $row[] = $access_badge;
        
        // Price
        if ($enrollment['is_free'] == 1 || $enrollment['price'] == 0) {
            $row[] = '<span class="text-success"><strong>FREE</strong></span>';
        } else {
            $row[] = '<strong>₹' . number_format($enrollment['price'], 2) . '</strong>';
        }
        
        // Payment Reference
        $row[] = !empty($enrollment['payment_reference']) 
                 ? '<small>' . html_escape($enrollment['payment_reference']) . '</small>' 
                 : '<small class="text-muted">-</small>';
        
        // Actions
        $actions = '<div class="btn-group">';
        $actions .= '<a href="' . admin_url('klms/Lms_admin/view_enrollment/' . $enrollment['id']) . '" class="btn btn-default btn-xs" title="View Details">
                        <i class="fa fa-eye"></i>
                     </a>';
        
        if ($enrollment['access_status'] !== 'active') {
            $actions .= '<a href="#" onclick="activateEnrollment(' . $enrollment['id'] . '); return false;" class="btn btn-success btn-xs" title="Activate Access">
                            <i class="fa fa-check"></i>
                         </a>';
        } else {
            $actions .= '<a href="#" onclick="suspendEnrollment(' . $enrollment['id'] . '); return false;" class="btn btn-warning btn-xs" title="Suspend Access">
                            <i class="fa fa-ban"></i>
                         </a>';
        }
        
        $actions .= '<a href="#" onclick="deleteEnrollment(' . $enrollment['id'] . '); return false;" class="btn btn-danger btn-xs" title="Delete">
                        <i class="fa fa-trash"></i>
                     </a>';
        $actions .= '</div>';
        
        $row[] = $actions;
        
        $data[] = $row;
    }

    $output = [
        'draw' => intval($draw),
        'recordsTotal' => $total_records,
        'recordsFiltered' => $total_records,
        'data' => $data
    ];

    echo json_encode($output);
}

/**
 * View enrollment details
 */
public function view_enrollment($enrollment_id = null)
{
    if (!is_admin()) {
        access_denied('lms_enrollments');
    }

    if (!$enrollment_id || !is_numeric($enrollment_id)) {
        show_404();
    }

    $this->load->model('elearning_admin_model');
    
    $data['enrollment'] = $this->elearning_admin_model->get_enrollment_details($enrollment_id);
    
    if (!$data['enrollment']) {
        show_404();
    }

    $data['title'] = 'Enrollment Details';
    
    $this->load->view('admin/view_enrollment', $data);
}

/**
 * Update enrollment status (AJAX)
 */
public function update_enrollment_status()
{
    if (!is_admin()) {
        ajax_access_denied();
    }

    $enrollment_id = $this->input->post('enrollment_id');
    $status = $this->input->post('status');
    $type = $this->input->post('type'); // 'access' or 'payment'

    if (!$enrollment_id || !$status || !$type) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
        return;
    }

    $this->load->model('elearning_admin_model');

    if ($type === 'access') {
        $result = $this->elearning_admin_model->update_enrollment_access($enrollment_id, $status);
    } else {
        $result = $this->elearning_admin_model->update_enrollment_payment($enrollment_id, $status);
    }

    if ($result) {
        log_activity('Enrollment #' . $enrollment_id . ' status updated to: ' . $status);
        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
}

/**
 * Delete enrollment (AJAX)
 */
public function delete_enrollment()
{
    if (!is_admin()) {
        ajax_access_denied();
    }

    $enrollment_id = $this->input->post('enrollment_id');

    if (!$enrollment_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid enrollment ID']);
        return;
    }

    $this->load->model('elearning_admin_model');
    
    $result = $this->elearning_admin_model->delete_enrollment($enrollment_id);

    if ($result) {
        log_activity('Enrollment #' . $enrollment_id . ' deleted');
        echo json_encode(['success' => true, 'message' => 'Enrollment deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete enrollment']);
    }
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
            return $courses;
        }else{
            set_alert('warning','No Course avaliable');
        }
    }

        public function get_course($id)
    {
        $courses = $this->elearning_admin_model->get_course($id);
        if($courses){
            return $courses;
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
            redirect(admin_url('klms/Lms_admin/manage_videos/' . $course_id));
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
 * View student details
 * 
 * @param int $student_id Contact ID
 */
public function view_student($student_id = null)
{
    if (!$student_id || !is_numeric($student_id)) {
        show_404();
    }

    // Get student data
    $student = $this->elearning_admin_model->get_student($student_id);
    
    if (!$student) {
        set_alert('warning', 'Student not found');
        redirect(admin_url('klms/Lms_admin/students'));
        return;
    }

    // Get enrolled courses
    $enrolled_courses = $this->elearning_admin_model->get_student_enrollments($student_id);
    
    // Get activity logs
    $activity_logs = $this->elearning_admin_model->get_student_activity($student_id, 20);
    
    // Calculate statistics
    $stats = [
        'total_courses' => count($enrolled_courses),
        'completed_courses' => 0,
        'in_progress_courses' => 0,
        'total_watch_time' => 0,
    ];
    
    foreach ($enrolled_courses as $course) {
        if (isset($course['progress_percentage']) && $course['progress_percentage'] >= 100) {
            $stats['completed_courses']++;
        } elseif (isset($course['progress_percentage']) && $course['progress_percentage'] > 0) {
            $stats['in_progress_courses']++;
        }
    }

    $data = [
        'title' => 'View Student: ' . $student->firstname . ' ' . $student->lastname,
        'student' => $student,
        'enrolled_courses' => $enrolled_courses,
        'stats' => $stats,
        'activity_logs' => $activity_logs,
    ];

    $this->load->view('admin/view_student', $data);
}

/**
 * Edit student details
 * 
 * @param int $student_id Contact ID
 */
public function edit_student($student_id = null)
{
    if (!$student_id || !is_numeric($student_id)) {
        show_404();
    }

    // Get student data
    $student = $this->elearning_admin_model->get_student($student_id);
    
    if (!$student) {
        set_alert('warning', 'Student not found');
        redirect(admin_url('klms/Lms_admin/students'));
        return;
    }

    // Handle form submission
    if ($this->input->post()) {
        $post_data = $this->input->post();
        
        // Update student
        $success = $this->elearning_admin_model->update_student($student_id, $post_data);
        
        if ($success) {
            set_alert('success', 'Student updated successfully');
            redirect(admin_url('klms/Lms_admin/view_student/' . $student_id));
        } else {
            set_alert('danger', 'Failed to update student. Please try again.');
        }
    }

    $data = [
        'title' => 'Edit Student: ' . $student->firstname . ' ' . $student->lastname,
        'student' => $student,
    ];

    $this->load->view('admin/edit_student', $data);
}

/**
 * Delete student (soft delete - deactivate)
 */
public function delete_student($student_id = null)
{
    if (!$student_id || !is_numeric($student_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid student ID']);
        return;
    }

    // Get student
    $student = $this->elearning_admin_model->get_student($student_id);
    
    if (!$student) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        return;
    }

    // Deactivate instead of delete (soft delete)
    $success = $this->db->where('id', $student_id)
                        ->update(db_prefix() . 'contacts', ['active' => 0]);

    if ($success) {
        log_activity('Student deactivated: ' . $student->email . ' (ID: ' . $student_id . ')');
        echo json_encode(['success' => true, 'message' => 'Student deactivated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to deactivate student']);
    }
}

/**
 * Activate/Deactivate student status
 */
public function toggle_student_status($student_id = null)
{
    if (!$student_id || !is_numeric($student_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid student ID']);
        return;
    }

    $student = $this->elearning_admin_model->get_student($student_id);
    
    if (!$student) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        return;
    }

    $new_status = $student->active == 1 ? 0 : 1;
    
    $success = $this->db->where('id', $student_id)
                        ->update(db_prefix() . 'contacts', ['active' => $new_status]);

    if ($success) {
        $status_text = $new_status == 1 ? 'activated' : 'deactivated';
        log_activity('Student ' . $status_text . ': ' . $student->email);
        echo json_encode(['success' => true, 'message' => 'Student ' . $status_text, 'new_status' => $new_status]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
}

/**
 * Bulk actions for students
 */
public function bulk_action()
{
    $action = $this->input->post('action');
    $student_ids = $this->input->post('student_ids');
    
    if (empty($action) || empty($student_ids) || !is_array($student_ids)) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        return;
    }

    $success_count = 0;
    $total = count($student_ids);

    foreach ($student_ids as $student_id) {
        switch ($action) {
            case 'activate':
                $result = $this->db->where('id', $student_id)
                                   ->update(db_prefix() . 'contacts', ['active' => 1]);
                break;
                
            case 'deactivate':
                $result = $this->db->where('id', $student_id)
                                   ->update(db_prefix() . 'contacts', ['active' => 0]);
                break;
                
            case 'delete':
                $result = $this->db->where('id', $student_id)
                                   ->delete(db_prefix() . 'contacts');
                break;
                
            default:
                $result = false;
        }
        
        if ($result) {
            $success_count++;
        }
    }

    log_activity('Bulk ' . $action . ' performed on ' . $success_count . ' students');

    if ($success_count === $total) {
        echo json_encode([
            'success' => true, 
            'message' => ucfirst($action) . ' completed successfully for ' . $success_count . ' student(s)'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Action completed for ' . $success_count . ' out of ' . $total . ' student(s)'
        ]);
    }
}
/**
 * Mark video as watched (AJAX endpoint)
 * Called via JavaScript when user watches video
 */
public function mark_video_watched()
{
    if (!is_client_logged_in()) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        return;
    }

    $course_id = (int)$this->input->post('course_id');
    $video_id = (int)$this->input->post('video_id');
    $watch_time = (int)$this->input->post('watch_time', true); // Seconds watched
    $total_duration = (int)$this->input->post('total_duration', true); // Total duration
    $contact_id = get_contact_user_id();

    if (!$course_id || !$video_id || !$contact_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
        return;
    }

    // Verify student has access to this course
    $has_access = $this->elearning_user_model->client_has_access_to_course($contact_id, $course_id);
    
    if (!$has_access) {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        return;
    }

    // Update progress
    $result = $this->elearning_user_model->mark_video_watched(
        $contact_id, 
        $course_id, 
        $video_id,
        $watch_time,
        $total_duration
    );
    
    if ($result) {
        // Get updated course progress
        $course_progress = $this->elearning_user_model->get_course_progress($contact_id, $course_id);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Progress saved',
            'course_progress' => $course_progress
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save progress']);
    }
}

/**
 * Get course progress (AJAX)
 */
public function get_course_progress($course_id = null)
{
    if (!is_client_logged_in()) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        return;
    }

    if (!$course_id || !is_numeric($course_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid course ID']);
        return;
    }

    $contact_id = get_contact_user_id();
    $progress = $this->elearning_user_model->get_course_progress($contact_id, $course_id);

    echo json_encode([
        'success' => true,
        'progress' => $progress
    ]);
}

/**
 * Export students to CSV
 */
public function export_students()
{
    $students = $this->db->get(db_prefix() . 'contacts')->result_array();
    
    if (empty($students)) {
        set_alert('warning', 'No students to export');
        redirect(admin_url('klms/Lms_admin/students'));
        return;
    }

    // Set headers for CSV download
    $filename = 'students_export_' . date('Y-m-d_His') . '.csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // Add CSV headers
    fputcsv($output, [
        'ID', 
        'First Name', 
        'Last Name', 
        'Email', 
        'Phone', 
        'Company', 
        'Title', 
        'Status', 
        'Email Verified', 
        'Registered Date', 
        'Last Login'
    ]);
    
    // Add student data
    foreach ($students as $student) {
        fputcsv($output, [
            $student['id'],
            $student['firstname'],
            $student['lastname'],
            $student['email'],
            $student['phonenumber'],
            $student['company'],
            $student['title'],
            $student['active'] == 1 ? 'Active' : 'Inactive',
            !empty($student['email_verified_at']) ? 'Yes' : 'No',
            $student['datecreated'],
            $student['last_login'] ?? 'Never'
        ]);
    }
    
    fclose($output);
    exit;
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
