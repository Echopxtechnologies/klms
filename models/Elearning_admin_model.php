<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Elearning_admin_model extends App_Model
{
    private $courses_table = 'elearning_courses';
    private $videos_table  = 'elearning_videos';

    public function __construct()
    {
        parent::__construct();
    }

    public function add_course($data)
    {
        $insert = [
            'title'           => $data['title'],
            'category'        => $data['category'],
            'description'     => $data['description'],
            'cover_image'     => isset($data['cover_image']) ? $data['cover_image'] : null,
            'price'           => isset($data['price']) ? (float)$data['price'] : 0.00,
            'is_free'         => isset($data['is_free']) ? 1 : 0,
            'is_public'       => isset($data['is_public']) ? 1 : 0,
            'is_active'       => isset($data['is_active']) ? 1 : 0,
            'course_duration' => isset($data['course_duration']) ? $data['course_duration'] : null,
            'level'           => isset($data['level']) ? $data['level'] : 'beginner',
            'language'        => isset($data['language']) ? $data['language'] : 'English',
            'sort_order'      => isset($data['sort_order']) ? (int)$data['sort_order'] : 0,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s')
        ];
        $this->db->insert('elearning_courses', $insert);
        return $this->db->insert_id();
    }

    public function get_all_courses()
    {
        return $this->db->get('elearning_courses')->result_array();
    }

    public function update_course($id, $data)
{
    $update = [
        'title'           => $data['title'],
        'category'        => $data['category'],
        'description'     => $data['description'],
        'price'           => isset($data['price']) ? (float)$data['price'] : 0.00,
        'is_free'         => isset($data['is_free']) ? 1 : 0,
        'is_public'       => isset($data['is_public']) ? 1 : 0,
        'is_active'       => isset($data['is_active']) ? 1 : 0,
        'course_duration' => isset($data['course_duration']) ? $data['course_duration'] : null,
        'level'           => isset($data['level']) ? $data['level'] : 'beginner',
        'language'        => isset($data['language']) ? $data['language'] : 'English',
        'sort_order'      => isset($data['sort_order']) ? (int)$data['sort_order'] : 0,
        'updated_at'      => date('Y-m-d H:i:s')
    ];
    
    // Only update cover_image if provided
    if (isset($data['cover_image'])) {
        $update['cover_image'] = $data['cover_image'];
    }
    
    $this->db->where('id', $id);
    $this->db->update('elearning_courses', $update);
    return $this->db->affected_rows() > 0;
}

    
    public function add_video($data)
    {
        $insert = [
            'course_id'   => $data['course_id'],
            'title'       => $data['title'],
            'description' => isset($data['description']) ? $data['description'] : '',
            'vimeo_url'   => $data['vimeo_url'],
            'sort_order'  => isset($data['sort_order']) ? $data['sort_order'] : 0,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tblelearning_videos', $insert);
        return $this->db->insert_id();
    }

public function get_course($course_id)
    {
        return $this->db->get_where($this->courses_table, ['id' => $course_id])->row_array();
    }

        public function get_course_videos($course_id)
    {
        return $this->db->get_where($this->videos_table, ['course_id' => $course_id])->result_array();
    }   
    
    public function get_video($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('elearning_videos')->row_array();
    }
    // Get the previous video based on sort_order
    public function get_previous_video($video_id, $course_id)
    {
        $this->db->where('course_id', $course_id);
        $this->db->where('sort_order <', $video_id);
        $this->db->order_by('sort_order', 'desc');
        return $this->db->get($this->videos_table, 1)->row_array();
    }

    // Get the next video based on sort_order
    public function get_next_video($video_id, $course_id)
    {
        $this->db->where('course_id', $course_id);
        $this->db->where('sort_order >', $video_id);
        $this->db->order_by('sort_order', 'asc');
        return $this->db->get($this->videos_table, 1)->row_array();
    }

    public function update_video($id,$data)
    {
        if (empty($id) && empty($data)){
            return null;
        }

        $update = [
            'title'       => $data['title'],
            'description' => isset($data['description']) ? $data['description'] : '',
            'vimeo_url'   => $data['vimeo_url'],
            'sort_order'  => isset($data['sort_order']) ? $data['sort_order'] : 0,
            'duration'    => isset($data['duration']) ? $data['duration'] : '',
            'updated_at'  => date('Y-m-d H:i:s')
        ];
        $this->db->where('id',$id);
        $this->db->update('elearning_videos',$update);
        return $this->db->affected_row() > 0;
    }
    public function delete_video($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('elearning_videos');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Enroll a student to a course
     */
    public function enroll_student($student_id, $course_id, $payment_status = 'free', $reference = null)
        {
            if (empty($student_id) || empty($course_id)) {
                return false;
            }

            // Prevent duplicates
            $exists = $this->db->where('student_id', $student_id)
                            ->where('course_id', $course_id)
                            ->get(db_prefix() . 'elearning_enrollments')
                            ->row();
            if ($exists) {
                return $exists->id; // Already enrolled
            }

            $data = [
                'student_id'        => $student_id,
                'course_id'         => $course_id,
                'payment_status'    => $payment_status,
                'payment_reference' => $reference,
                'access_status'     => ($payment_status === 'paid' || $payment_status === 'free') ? 'active' : 'pending',
                'enrolled_date'     => date('Y-m-d H:i:s'),
            ];

            $this->db->insert(db_prefix() . 'elearning_enrollments', $data);
            return $this->db->insert_id();
        }

    /**
     * Create student account (similar to Authentication::register)
     */
    /**
 * Create student account (contact only, no enrollment)
 * @param array $data
 * @param int|null $auto_enroll_course_id (deprecated - kept for compatibility)
 * @return int|false Contact ID or false
 */
public function create_student_account($data, $auto_enroll_course_id = null)
{
    // Validate required fields
    if (empty($data['firstname']) || empty($data['lastname']) || empty($data['email']) || empty($data['password'])) {
        log_activity('Student registration failed: Missing required fields');
        return false;
    }

    // Normalize email
    $email = trim(strtolower($data['email']));

    // CRITICAL: Check if email already exists
    $existing = $this->db->where('email', $email)
                         ->get(db_prefix() . 'contacts')
                         ->row();
    
    if ($existing) {
        log_activity('Student registration failed: Duplicate email - ' . $email);
        return false;
    }

    // Hash password using Perfex's hasher
    $hashed_password = app_hasher()->HashPassword($data['password']);

    // Prepare contact data
    $contact_data = [
        'firstname'         => trim($data['firstname']),
        'lastname'          => trim($data['lastname']),
        'email'             => $email, // Normalized email
        'phonenumber'       => $data['phonenumber'] ?? '',
        'title'             => $data['title'] ?? 'Student',
        'password'          => $hashed_password, // Use Perfex hasher
        'datecreated'       => date('Y-m-d H:i:s'),
        'email_verified_at' => date('Y-m-d H:i:s'),
        'active'            => 1,
        'is_primary'        => 1,
        'userid'            => 0, // Standalone contact
    ];

    // Insert new student contact
    $this->db->insert(db_prefix() . 'contacts', $contact_data);

    if ($this->db->affected_rows() > 0) {
        $contact_id = $this->db->insert_id();

        // Log successful creation
        log_activity('New student contact created: ' . $email . ' (ID: ' . $contact_id . ')');

        return $contact_id;
    }

    return false;
}

/**
 * Get single student by ID
 * 
 * @param int $student_id Contact ID
 * @return object|null Student data
 */
public function get_student($student_id)
{
    return $this->db->select('*')
                    ->from(db_prefix() . 'contacts')
                    ->where('id', $student_id)
                    ->get()
                    ->row();
}

/**
 * Update student information
 * 
 * @param int $student_id Contact ID
 * @param array $data Update data
 * @return bool Success status
 */
public function update_student($student_id, $data)
{
    // Validate required fields
    if (empty($data['firstname']) || empty($data['lastname']) || empty($data['email'])) {
        return false;
    }

    // Normalize email
    $email = trim(strtolower($data['email']));

    // Check for duplicate email (excluding current student)
    $existing = $this->db->where('email', $email)
                         ->where('id !=', $student_id)
                         ->get(db_prefix() . 'contacts')
                         ->row();
    
    if ($existing) {
        log_activity('Student update failed: Duplicate email - ' . $email);
        return false;
    }

    // Prepare update data
    $update_data = [
        'firstname'   => trim($data['firstname']),
        'lastname'    => trim($data['lastname']),
        'email'       => $email,
        'phonenumber' => $data['phonenumber'] ?? '',
        'title'       => $data['title'] ?? 'Student',
    ];

    // Update password only if provided
    if (!empty($data['password'])) {
        $update_data['password'] = app_hasher()->HashPassword($data['password']);
    }

    // Update student
    $this->db->where('id', $student_id);
    $this->db->update(db_prefix() . 'contacts', $update_data);

    if ($this->db->affected_rows() >= 0) {
        log_activity('Student updated: ' . $email . ' (ID: ' . $student_id . ')');
        return true;
    }

    return false;
}

/**
 * Get student enrollments with course details and progress
 * 
 * @param int $student_id Contact ID
 * @return array Enrolled courses with progress
 */
public function get_student_enrollments($student_id)
{
    $table_exists = $this->db->table_exists(db_prefix() . 'elearning_video_progress');
    
    if ($table_exists) {
        $this->db->select('
            e.*,
            c.id as course_id,
            c.title as course_title,
            c.description,
            c.category,
            c.price,
            c.is_free,
            (SELECT COUNT(*) 
             FROM ' . db_prefix() . 'elearning_videos v 
             WHERE v.course_id = c.id) as total_videos,
            (SELECT COUNT(*) 
             FROM ' . db_prefix() . 'elearning_video_progress vp 
             WHERE vp.student_id = e.student_id 
             AND vp.course_id = e.course_id 
             AND vp.completed = 1) as completed_videos
        ');
    } else {
        $this->db->select('
            e.*,
            c.id as course_id,
            c.title as course_title,
            c.description,
            c.category,
            c.price,
            c.is_free,
            (SELECT COUNT(*) 
             FROM ' . db_prefix() . 'elearning_videos v 
             WHERE v.course_id = c.id) as total_videos,
            0 as completed_videos
        ');
    }
    
    $this->db->from(db_prefix() . 'elearning_enrollments e');
    $this->db->join(db_prefix() . 'elearning_courses c', 'c.id = e.course_id', 'left');
    $this->db->where('e.student_id', $student_id);
    $this->db->order_by('e.enrolled_date', 'DESC');
    
    $enrollments = $this->db->get()->result_array();

    foreach ($enrollments as &$enrollment) {
        if (isset($enrollment['total_videos']) && $enrollment['total_videos'] > 0) {
            $completed = isset($enrollment['completed_videos']) ? $enrollment['completed_videos'] : 0;
            $enrollment['progress_percentage'] = round(($completed / $enrollment['total_videos']) * 100, 2);
        } else {
            $enrollment['progress_percentage'] = 0;
        }
        
        if ($enrollment['progress_percentage'] >= 100) {
            $enrollment['progress_status'] = 'completed';
        } elseif ($enrollment['progress_percentage'] > 0) {
            $enrollment['progress_status'] = 'in_progress';
        } else {
            $enrollment['progress_status'] = 'not_started';
        }
    }

    return $enrollments;
}

/**
 * Get student activity logs
 * 
 * @param int $student_id Contact ID
 * @param int $limit Number of records
 * @return array Activity logs
 */
public function get_student_activity($student_id, $limit = 50)
{
    // Get from Perfex activity log
    $this->db->select('*');
    $this->db->from(db_prefix() . 'activity_log');
    $this->db->where('staffid', 0); // Client activities
    $this->db->like('description', 'Student');
    $this->db->or_like('description', 'contact ' . $student_id);
    $this->db->order_by('date', 'DESC');
    $this->db->limit($limit);
    
    return $this->db->get()->result_array();
}

/**
 * Mark video as watched/update progress
 * 
 * @param int $student_id Contact ID
 * @param int $course_id Course ID
 * @param int $video_id Video ID
 * @param int $watch_time Seconds watched
 * @param int $total_duration Total video duration
 * @return bool Success status
 */
public function mark_video_watched($student_id, $course_id, $video_id, $watch_time = 0, $total_duration = 0)
{
    // Check if progress record exists
    $existing = $this->db->select('id, watch_time, watch_count')
                        ->where([
                            'student_id' => $student_id,
                            'video_id' => $video_id
                        ])
                        ->get(db_prefix() . 'elearning_video_progress')
                        ->row();

    // Calculate progress
    $progress_percentage = 0;
    $completed = 0;
    
    if ($total_duration > 0) {
        $progress_percentage = round(($watch_time / $total_duration) * 100, 2);
        $completed = $progress_percentage >= 90 ? 1 : 0; // 90% threshold for completion
    }

    $data = [
        'course_id' => $course_id,
        'watch_time' => $watch_time,
        'total_duration' => $total_duration,
        'progress_percentage' => $progress_percentage,
        'completed' => $completed,
        'last_watched' => date('Y-m-d H:i:s'),
    ];

    if ($existing) {
        // Update existing record
        $data['watch_count'] = $existing->watch_count + 1;
        
        $this->db->where('id', $existing->id);
        $result = $this->db->update(db_prefix() . 'elearning_video_progress', $data);
    } else {
        // Insert new record
        $data['student_id'] = $student_id;
        $data['video_id'] = $video_id;
        $data['first_watched'] = date('Y-m-d H:i:s');
        $data['watch_count'] = 1;
        
        $result = $this->db->insert(db_prefix() . 'elearning_video_progress', $data);
    }

    if ($result) {
        log_activity('Video progress updated - Student: ' . $student_id . ' - Video: ' . $video_id . ' - Progress: ' . $progress_percentage . '%');
        return true;
    }

    return false;
}

/**
 * Get video progress for a student
 * 
 * @param int $student_id Contact ID
 * @param int $video_id Video ID
 * @return object|null Progress data
 */
public function get_video_progress($student_id, $video_id)
{
    return $this->db->where([
                        'student_id' => $student_id,
                        'video_id' => $video_id
                    ])
                    ->get(db_prefix() . 'elearning_video_progress')
                    ->row();
}

/**
 * Get course progress for a student
 * 
 * @param int $student_id Contact ID
 * @param int $course_id Course ID
 * @return array Progress summary
 */
public function get_course_progress($student_id, $course_id)
{
    // Get total videos in course
    $total_videos = $this->db->where('course_id', $course_id)
                             ->count_all_results(db_prefix() . 'elearning_videos');

    // Get completed videos
    $completed_videos = $this->db->where([
                                    'student_id' => $student_id,
                                    'course_id' => $course_id,
                                    'completed' => 1
                                ])
                                ->count_all_results(db_prefix() . 'elearning_video_progress');

    // Calculate progress
    $progress_percentage = 0;
    if ($total_videos > 0) {
        $progress_percentage = round(($completed_videos / $total_videos) * 100, 2);
    }

    return [
        'total_videos' => $total_videos,
        'completed_videos' => $completed_videos,
        'progress_percentage' => $progress_percentage,
        'status' => $progress_percentage >= 100 ? 'completed' : ($progress_percentage > 0 ? 'in_progress' : 'not_started')
    ];
}

/**
 * Get all video progress for a student in a course
 * 
 * @param int $student_id Contact ID
 * @param int $course_id Course ID
 * @return array Video progress records
 */
public function get_student_course_progress($student_id, $course_id)
{
    $this->db->select('vp.*, v.title as video_title, v.order_index');
    $this->db->from(db_prefix() . 'elearning_video_progress vp');
    $this->db->join(db_prefix() . 'elearning_videos v', 'v.id = vp.video_id', 'left');
    $this->db->where([
        'vp.student_id' => $student_id,
        'vp.course_id' => $course_id
    ]);
    $this->db->order_by('v.order_index', 'ASC');
    
    return $this->db->get()->result_array();
}



    /*------------------------------------------------------------
     | Getters / Setters
     *------------------------------------------------------------*/
    public function set_last_created_student_id($id)
    {
        $this->last_created_student_id = $id;
    }

    public function get_last_created_student_id()
    {
        return $this->last_created_student_id;
    }

    public function check_payment_status($enrollment_id)
{
    $enrollment = $this->db->get_where(db_prefix() . 'elearning_enrollments', [
        'id' => $enrollment_id
    ])->row();

    if ($enrollment) {
        if (!empty($enrollment->payment_reference)) {
            return [
                'status' => 'paid',
                'transaction_id' => $enrollment->payment_reference,
                'message' => 'Payment verified with Transaction ID: ' . $enrollment->payment_reference
            ];
        } else {
            return [
                'status' => 'unpaid',
                'transaction_id' => null,
                'message' => 'No payment transaction found'
            ];
        }
    }
    
    return ['status' => 'not_found', 'message' => 'Enrollment not found'];
}

}
