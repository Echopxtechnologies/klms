<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * E-Learning User Model
 * Handles student-facing course and enrollment operations
 */
class Elearning_user_model extends App_Model
{
    private $courses_table = 'elearning_courses';
    private $videos_table = 'elearning_videos';
    private $enrollments_table = 'elearning_enrollments';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('elearning_admin_model');
    }

    // ===================================================================
    // COURSE ACCESS & ENROLLMENT
    // ===================================================================

    /**
     * Check if contact has access to a course
     * @param int $contact_id
     * @param int $course_id
     * @return bool
     */
    public function client_has_access_to_course($contact_id, $course_id)
    {
        // Check enrollment
        $enrollment = $this->db->where('student_id', (int)$contact_id)
                               ->where('course_id', (int)$course_id)
                               ->where('access_status', 'active')
                               ->where('payment_status', 'paid')
                               ->get(db_prefix() . $this->enrollments_table)
                               ->row();
        
        if ($enrollment) {
            return true;
        }
        
        // Check if course is free
        $course = $this->db->select('is_free, price')
                           ->where('id', (int)$course_id)
                           ->get(db_prefix() . $this->courses_table)
                           ->row();
        
        if ($course && ($course->is_free == 1 || (float)$course->price <= 0)) {
            return true;
        }
        
        return false;
    }

    /**
     * Get enrollment status with detailed information
     * @param int $contact_id
     * @param int $course_id
     * @return array
     */
    public function get_enrollment_status($contact_id, $course_id)
    {
        $enrollment = $this->db->select('*')
            ->where('student_id', (int)$contact_id)
            ->where('course_id', (int)$course_id)
            ->get(db_prefix() . $this->enrollments_table)
            ->row_array();

        if (!$enrollment) {
            return [
                'enrolled' => false,
                'status' => 'not_enrolled',
                'message' => 'You need to purchase this course',
            ];
        }

        if ($enrollment['access_status'] !== 'active') {
            return [
                'enrolled' => true,
                'status' => 'access_revoked',
                'message' => 'Your access has been revoked',
                'data' => $enrollment,
            ];
        }

        if ($enrollment['payment_status'] !== 'paid') {
            return [
                'enrolled' => true,
                'status' => 'payment_pending',
                'message' => 'Payment confirmation pending',
                'data' => $enrollment,
            ];
        }

        // Check expiry
        if (!empty($enrollment['expiry_date'])) {
            if (strtotime($enrollment['expiry_date']) < time()) {
                return [
                    'enrolled' => true,
                    'status' => 'expired',
                    'message' => 'Your enrollment has expired',
                    'data' => $enrollment,
                ];
            }
        }

        return [
            'enrolled' => true,
            'status' => 'active',
            'message' => 'Active enrollment',
            'data' => $enrollment,
        ];
    }

    // ===================================================================
    // COURSE DATA RETRIEVAL
    // ===================================================================

    /**
     * Get all courses with computed flags and URLs
     * @param string $module_base_url
     * @param int|null $contact_id
     * @return array
     */
    public function get_all_courses_data($module_base_url = 'lms/Lms_users', $contact_id = null)
    {
        $courses = $this->elearning_admin_model->get_all_courses();
        $categories = [];

        foreach ($courses as &$c) {
            $price = (float)($c['price'] ?? 0);
            $is_free = !empty($c['is_free']) || $price <= 0;
            $can_watch = $is_free;

            // Check enrollment if user is logged in
            if ($contact_id) {
                $can_watch = $this->client_has_access_to_course($contact_id, $c['id']);
            }

            // Computed flags
            $c['_computed'] = [
                'is_free' => $is_free,
                'is_paid' => !$is_free,
                'can_watch' => $can_watch,
            ];

            // URLs
            $c['urls'] = [
                'details' => site_url($module_base_url . '/view_course/' . $c['id']),
                'watch_first' => site_url($module_base_url . '/course_videos/' . $c['id']),
                'purchase' => site_url($module_base_url . '/purchase/' . $c['id']),
                'registration' => site_url($module_base_url . '/registration/' . $c['id']),
            ];

            // Collect categories
            if (!empty($c['category'])) {
                $categories[] = trim($c['category']);
            }

            // Cover image
            $c['cover_image_abs'] = !empty($c['cover_image']) 
                ? base_url(ltrim($c['cover_image'], '/')) 
                : '';
        }
        unset($c);

        // Unique categories
        $categories = array_values(array_unique($categories));
        sort($categories);

        return [
            'title' => 'All Courses',
            'courses' => $courses,
            'categories' => $categories,
        ];
    }

    /**
 * Get student's enrolled courses with progress tracking
 * @param int $contact_id
 * @return array
 */
public function get_student_enrolled_courses($contact_id)
{
    $this->db->select('
        c.*,
        e.enrolled_date,
        e.payment_status,
        e.access_status,
        COUNT(DISTINCT v.id) as total_videos,
        COUNT(DISTINCT p.video_id) as videos_watched
    ');
    
    $this->db->from(db_prefix() . $this->courses_table . ' c');
    $this->db->join(db_prefix() . $this->enrollments_table . ' e', 'e.course_id = c.id', 'inner');
    $this->db->join(db_prefix() . $this->videos_table . ' v', 'v.course_id = c.id', 'left');
    $this->db->join(db_prefix() . 'elearning_progress p', 'p.course_id = c.id AND p.student_id = ' . (int)$contact_id . ' AND p.completed = 1', 'left');
    
    $this->db->where('e.student_id', (int)$contact_id);
    $this->db->where('e.access_status', 'active');
    $this->db->group_by('c.id');
    $this->db->order_by('e.enrolled_date', 'DESC');
    
    $query = $this->db->get();
    $courses = $query->result_array();
    
    // Add computed fields
    foreach ($courses as &$course) {
        $total = (int)$course['total_videos'];
        $watched = (int)$course['videos_watched'];
        
        // Calculate progress percentage
        if ($total > 0) {
            $progress = round(($watched / $total) * 100);
            $course['progress_percent'] = $progress;
            
            // Determine status
            if ($progress >= 100) {
                $course['progress_status'] = 'completed';
            } elseif ($progress > 0) {
                $course['progress_status'] = 'in_progress';
            } else {
                $course['progress_status'] = 'not_started';
            }
        } else {
            $course['progress_percent'] = 0;
            $course['progress_status'] = 'not_started';
        }
        
        // Get last watched video or first video
        $last_video = $this->db->select('video_id')
            ->from(db_prefix() . 'elearning_progress')
            ->where('student_id', (int)$contact_id)
            ->where('course_id', $course['id'])
            ->order_by('watched_at', 'DESC')
            ->limit(1)
            ->get()
            ->row();
        
        if ($last_video) {
            $course['last_video_id'] = $last_video->video_id;
        } else {
            // Get first video
            $first_video = $this->db->select('id')
                ->from(db_prefix() . $this->videos_table)
                ->where('course_id', $course['id'])
                ->order_by('sort_order', 'ASC')
                ->limit(1)
                ->get()
                ->row();
            
            $course['last_video_id'] = $first_video ? $first_video->id : 0;
        }
    }
    
    return $courses;
    }
    /**
     * Mark video as watched/completed
     * @param int $student_id
     * @param int $course_id
     * @param int $video_id
     * @param int $watch_duration
     * @return bool
     */
    public function mark_video_watched($student_id, $course_id, $video_id, $watch_duration = 0)
    {
        $data = [
            'student_id' => (int)$student_id,
            'course_id' => (int)$course_id,
            'video_id' => (int)$video_id,
            'watched_at' => date('Y-m-d H:i:s'),
            'watch_duration' => (int)$watch_duration,
            'completed' => 1,
            'last_position' => 0,
        ];

        // Check if already exists
        $existing = $this->db->where('student_id', $student_id)
                            ->where('course_id', $course_id)
                            ->where('video_id', $video_id)
                            ->get(db_prefix() . 'elearning_progress')
                            ->row();

        if ($existing) {
            return $this->db->where('id', $existing->id)
                            ->update(db_prefix() . 'elearning_progress', $data);
        } else {
            return $this->db->insert(db_prefix() . 'elearning_progress', $data);
        }
    }
    /**
     * Get purchased courses (alternative method)
     * @param int $contact_id
     * @return array
     */
    public function get_purchased_courses($contact_id)
    {
        $this->db->select('c.*, e.access_status, e.payment_status, e.enrolled_date');
        $this->db->from(db_prefix() . $this->courses_table . ' c');
        $this->db->join(db_prefix() . $this->enrollments_table . ' e', 'e.course_id = c.id', 'inner');
        $this->db->where('e.student_id', (int)$contact_id);
        $this->db->where('e.access_status', 'active');
        $this->db->where('e.payment_status', 'paid');
        $this->db->order_by('e.enrolled_date', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    // ===================================================================
    // COURSE & VIDEO HELPERS
    // ===================================================================

    /**
     * Get course by ID
     * @param int $id
     * @param bool $include_inactive
     * @return array|null
     */
    public function get_course($id, $include_inactive = false)
    {
        $this->db->where('id', (int)$id);
        
        if (!$include_inactive) {
            $this->db->where('is_active', 1);
        }
        
        $query = $this->db->get(db_prefix() . $this->courses_table);
        
        if ($query->num_rows() > 0) {
            $course = $query->row_array();
            
            // Add computed fields
            $course['video_count'] = $this->get_course_video_count($id);
            $course['is_free_course'] = ($course['is_free'] == 1 || $course['price'] == 0);
            $course['formatted_price'] = $this->format_course_price($course);
            
            return $course;
        }
        
        return null;
    }

    /**
     * Get course videos
     * @param int $course_id
     * @return array
     */
    public function get_course_videos($course_id)
    {
        $this->db->where('course_id', (int)$course_id);
        $this->db->order_by('sort_order', 'ASC');
        $this->db->order_by('created_at', 'ASC');
        
        $query = $this->db->get(db_prefix() . $this->videos_table);
        return $query->result_array();
    }

    /**
     * Get course video count
     * @param int $course_id
     * @return int
     */
    public function get_course_video_count($course_id)
    {
        $this->db->where('course_id', (int)$course_id);
        return $this->db->count_all_results(db_prefix() . $this->videos_table);
    }

    /**
     * Get courses by category
     * @param string $category
     * @param int|null $exclude_id
     * @param int|null $limit
     * @param array $filters
     * @return array
     */
    public function get_courses_by_category($category, $exclude_id = null, $limit = null, $filters = [])
    {
        $this->db->where('category', $category);
        $this->db->where('is_active', 1);
        $this->db->where('is_public', 1);
        
        if ($exclude_id) {
            $this->db->where('id !=', (int)$exclude_id);
        }
        
        // Apply filters
        if (!empty($filters['level'])) {
            $this->db->where('level', $filters['level']);
        }
        
        if (!empty($filters['language'])) {
            $this->db->where('language', $filters['language']);
        }
        
        if (!empty($filters['is_free'])) {
            $this->db->where('is_free', $filters['is_free']);
        }
        
        if ($limit) {
            $this->db->limit((int)$limit);
        }
        
        $this->db->order_by('sort_order', 'ASC');
        $this->db->order_by('created_at', 'DESC');
        
        $query = $this->db->get(db_prefix() . $this->courses_table);
        return $query->result_array();
    }

    // ===================================================================
    // ENROLLMENT OPERATIONS
    // ===================================================================

    /**
     * Enroll student (create account + enrollment)
     * @param array $data
     * @param int $course_id
     * @return int|bool Student ID or false
     */
    public function enroll_student($data, $course_id)
    {
        $student_id = $this->elearning_admin_model->create_student_account($data, $course_id);
        return $student_id;
    }

    // ===================================================================
    // FORMATTING HELPERS
    // ===================================================================

    /**
     * Format course price for display
     * @param array $course
     * @return string
     */
    public function format_course_price($course)
    {
        if ($course['is_free'] == 1 || $course['price'] == 0) {
            return 'Free';
        }
        
        return '₹' . number_format($course['price'], 2);
    }

    /**
     * Check if email exists
     * @param string $email
     * @return array|false
     */
    public function check_email_exists($email)
{
    $email = trim(strtolower($email));
    
    file_put_contents(FCPATH . 'debug.txt', 
        "\n=== MODEL CHECK ===\n" . 
        "Checking email: " . $email . "\n",
        FILE_APPEND
    );
    
    $contact = $this->db->where('email', $email)
                        ->get(db_prefix() . 'contacts')
                        ->row();
    
    file_put_contents(FCPATH . 'debug.txt', 
        "Contact found: " . var_export($contact ? true : false, true) . "\n",
        FILE_APPEND
    );
    
    if ($contact) {
        return [
            'exists' => true,
            'type' => 'contact',
            'data' => $contact
        ];
    }
    
    return false;
}

/**
 * Auto-login contact after registration
 * @param string $email
 * @param string $password (plain text)
 * @return bool
 */
public function auto_login_contact($email, $password)
{
    $email = trim(strtolower($email));

    // Load the authentication model
    $this->load->model('authentication_model');

    // Try to log in as a client contact
    $login = $this->authentication_model->login($email, $password, true, false);

    if ($login) {
        // Login successful
        $this->db->where('email', $email)
                 ->update(db_prefix() . 'contacts', ['last_login' => date('Y-m-d H:i:s')]);
        
        return true;
    } else {
        // Debug helper
        file_put_contents(FCPATH . 'debug.txt',
            "\nAuto login failed for email: $email\n",
            FILE_APPEND
        );
        return false;
    }
}


public function enroll_student_with_credentials($data, $course_id)
{
    // Store plain password
    $plain_password = $data['password'];
    $email = trim(strtolower($data['email']));
    $data['email'] = $email;
    
    // Double-check duplicate
    $existing = $this->db->where('email', $email)
                         ->get(db_prefix() . 'contacts')
                         ->row();
    
    if ($existing) {
        return false;
    }
    
    // Create account
    $student_id = $this->elearning_admin_model->create_student_account($data, null);
    
    if (!$student_id) {
        return false;
    }

    return [
        'student_id' => $student_id,
        'email' => $email,
        'password' => $plain_password,
    ];
}
}