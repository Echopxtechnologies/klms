<?php defined('BASEPATH') or exit('No direct script access allowed');

class Elearning_user_model extends App_Model
{
    private $courses_table = 'elearning_courses';
    private $videos_table  = 'elearning_videos';
    private $enrollments_table = 'elearning_enrollments';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('elearning_admin_model');
    }

    /**
     * Simple entitlement check.
     * You can replace this with your real logic (orders, invoices, subscriptions).
     */
    public function client_has_access_to_course($student_id, $course_id)
    {
        // Check the enrollment table if the user has access to the course
        $this->db->select('payment_status');
        $this->db->from($this->enrollments_table);
        $this->db->where('student_id', $student_id);
        $this->db->where('course_id', $course_id);
        $this->db->where('payment_status', 'paid');  // Or check your actual payment status
        $result = $this->db->get()->row_array();
        
        // If the user has 'paid' status, return true, else false
        return !empty($result);
    }



    /**
     * Returns ALL course fields + computed flags and URLs for the view.
     */
    public function get_all_courses_data($module_base_url = '/klms/Lms_users', $contact_id = null)
{
    $courses = $this->elearning_admin_model->get_all_courses();
    $categories = [];

    foreach ($courses as &$c) {
        $price   = (float)($c['price'] ?? 0);
        $is_free = !empty($c['is_free']) || $price <= 0;
        $can_watch = $is_free;

        if ($contact_id) {
            $can_watch = $this->client_has_access_to_course($contact_id, $c['id']);
        }

        $c['_computed'] = [
            'is_free'   => $is_free,
            'is_paid'   => !$is_free,
            'can_watch' => $can_watch,
        ];

        $c['urls'] = [
            'details'      => site_url($module_base_url.'/view_course/'.$c['id']),
            'watch_first'  => site_url($module_base_url.'/course_videos/'.$c['id']),
            // if logged in we’ll send to purchase; if not, registration controller will redirect as you coded
            'purchase'     => site_url($module_base_url.'/purchase/'.$c['id']),
            'registration' => site_url($module_base_url.'/registration/'.$c['id']),
        ];

        if (!empty($c['category'])) $categories[] = trim($c['category']);
        $c['cover_image_abs'] = !empty($c['cover_image']) ? base_url(ltrim($c['cover_image'],'/')) : '';
    }
    unset($c);

    $categories = array_values(array_unique($categories));
    sort($categories);

    return [
        'title'       => 'All Courses',
        'courses'     => $courses,
        'categories'  => $categories,
    ];
}

    /**
     * Get course video count
     */
    public function get_course_video_count($course_id)
    {
        $this->db->where('course_id', $course_id);
        return $this->db->count_all_results($this->videos_table);
    }
        /**
     * Format course price for display
     */
    public function format_course_price($course)
    {
        if ($course['is_free'] == 1 || $course['price'] == 0) {
            return 'Free';
        }
        
        return number_format($course['price'], 2);
    }


    public function enroll_student($data, $courses_id)
    {
        $client_id = $this->elearning_admin_model->create_student_account($data,$courses_id);
        return $client_id;
    }

    public function get_course($id, $include_inactive = false)
    {
        $this->db->where('id', $id);
        
        if (!$include_inactive) {
            $this->db->where('is_active', 1);
        }
        
        $query = $this->db->get($this->courses_table);
        
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
        public function get_course_videos($course_id)
    {
        $this->db->where('course_id', $course_id);
        $this->db->order_by('sort_order', 'ASC');
        $this->db->order_by('created_at', 'ASC');
        $query = $this->db->get($this->videos_table);
        
        return $query->result_array();
    }
    /**
     * Get courses by category with enhanced filtering
     */
    public function get_courses_by_category($category, $exclude_id = null, $limit = null, $filters = [])
    {
        $this->db->where('category', $category);
        $this->db->where('is_active', 1);
        $this->db->where('is_public', 1);
        
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        
        // Apply additional filters
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
            $this->db->limit($limit);
        }
        
        $this->db->order_by('sort_order', 'ASC');
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get($this->courses_table);
        
        return $query->result_array();
    }



}
