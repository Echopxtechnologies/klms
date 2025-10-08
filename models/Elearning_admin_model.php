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


}
