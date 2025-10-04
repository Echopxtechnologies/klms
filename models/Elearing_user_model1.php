<?php defined('BASEPATH') or exit('No direct script access allowed');

class Elearing_user_model extends App_Model
{
    private $courses_table = 'elearning_courses';
    private $videos_table = 'elearning_videos';

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Get courses with pagination and filtering for DataTables
     */
    public function get_courses_for_datatables($aColumns, $sIndexColumn, $sTable, $join = [], $where = [], $additionalSelect = [])
    {
        return $this->get_table_data($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
    }

    /**
     * Get all courses with optional filtering
     */
    public function get_all_courses($where = [], $order_by = 'sort_order ASC, created_at DESC')
    {
        if (!empty($where)) {
            $this->db->where($where);
        }
        
        // Default to active courses only
        if (!isset($where['is_active'])) {
            $this->db->where('is_active', 1);
        }
        
        $this->db->order_by($order_by);
        $query = $this->db->get($this->courses_table);
        
        return $query->result_array();
    }

    /**
     * Get courses with enhanced pagination and filtering
     */
    public function get_courses_paginated($limit, $offset, $filters = [], $sort = 'sort_order_asc')
    {
        // Apply filters
        if (!empty($filters['category'])) {
            $this->db->where('category', $filters['category']);
        }
        
        if (!empty($filters['level'])) {
            $this->db->where('level', $filters['level']);
        }
        
        if (!empty($filters['language'])) {
            $this->db->where('language', $filters['language']);
        }
        
        if (!empty($filters['is_free'])) {
            $this->db->where('is_free', $filters['is_free']);
        }
        
        if (!empty($filters['price_min'])) {
            $this->db->where('price >=', $filters['price_min']);
        }
        
        if (!empty($filters['price_max'])) {
            $this->db->where('price <=', $filters['price_max']);
        }
        
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('title', $filters['search']);
            $this->db->or_like('description', $filters['search']);
            $this->db->or_like('category', $filters['search']);
            $this->db->group_end();
        }
        
        // Filter by public and active courses by default
        if (!isset($filters['include_private'])) {
            $this->db->where('is_public', 1);
        }
        
        if (!isset($filters['include_inactive'])) {
            $this->db->where('is_active', 1);
        }
        
        // Apply sorting
        switch ($sort) {
            case 'sort_order_asc':
                $this->db->order_by('sort_order', 'ASC');
                $this->db->order_by('created_at', 'DESC');
                break;
            case 'sort_order_desc':
                $this->db->order_by('sort_order', 'DESC');
                $this->db->order_by('created_at', 'DESC');
                break;
            case 'date_desc':
                $this->db->order_by('created_at', 'DESC');
                break;
            case 'date_asc':
                $this->db->order_by('created_at', 'ASC');
                break;
            case 'title_asc':
                $this->db->order_by('title', 'ASC');
                break;
            case 'title_desc':
                $this->db->order_by('title', 'DESC');
                break;
            case 'category':
                $this->db->order_by('category', 'ASC');
                $this->db->order_by('sort_order', 'ASC');
                break;
            case 'price_asc':
                $this->db->order_by('price', 'ASC');
                break;
            case 'price_desc':
                $this->db->order_by('price', 'DESC');
                break;
            case 'level':
                $this->db->order_by('FIELD(level, "Beginner", "Intermediate", "Advanced")', false);
                $this->db->order_by('sort_order', 'ASC');
                break;
            default:
                $this->db->order_by('sort_order', 'ASC');
                $this->db->order_by('created_at', 'DESC');
        }
        
        $this->db->limit($limit, $offset);
        $query = $this->db->get($this->courses_table);
        
        return $query->result_array();
    }

    /**
     * Get single course by ID with enhanced details
     */
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

    /**
     * Get courses by level
     */
    public function get_courses_by_level($level, $limit = null)
    {
        $this->db->where('level', $level);
        $this->db->where('is_active', 1);
        $this->db->where('is_public', 1);
        
        if ($limit) {
            $this->db->limit($limit);
        }
        
        $this->db->order_by('sort_order', 'ASC');
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get($this->courses_table);
        
        return $query->result_array();
    }

    /**
     * Get free courses
     */
    public function get_free_courses($limit = null)
    {
        $this->db->group_start();
        $this->db->where('is_free', 1);
        $this->db->or_where('price', 0);
        $this->db->group_end();
        
        $this->db->where('is_active', 1);
        $this->db->where('is_public', 1);
        
        if ($limit) {
            $this->db->limit($limit);
        }
        
        $this->db->order_by('sort_order', 'ASC');
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get($this->courses_table);
        
        return $query->result_array();
    }

    /**
     * Get paid courses
     */
    public function get_paid_courses($limit = null)
    {
        $this->db->where('is_free', 0);
        $this->db->where('price >', 0);
        $this->db->where('is_active', 1);
        $this->db->where('is_public', 1);
        
        if ($limit) {
            $this->db->limit($limit);
        }
        
        $this->db->order_by('sort_order', 'ASC');
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get($this->courses_table);
        
        return $query->result_array();
    }

    /**
     * Get courses by price range
     */
    public function get_courses_by_price_range($min_price = 0, $max_price = null, $limit = null)
    {
        $this->db->where('price >=', $min_price);
        
        if ($max_price !== null) {
            $this->db->where('price <=', $max_price);
        }
        
        $this->db->where('is_active', 1);
        $this->db->where('is_public', 1);
        
        if ($limit) {
            $this->db->limit($limit);
        }
        
        $this->db->order_by('price', 'ASC');
        $this->db->order_by('sort_order', 'ASC');
        $query = $this->db->get($this->courses_table);
        
        return $query->result_array();
    }

    /**
     * Get course videos ordered by sort_order
     */
    public function get_course_videos($course_id)
    {
        $this->db->where('course_id', $course_id);
        $this->db->order_by('sort_order', 'ASC');
        $this->db->order_by('created_at', 'ASC');
        $query = $this->db->get($this->videos_table);
        
        return $query->result_array();
    }

    /**
     * Get single video by ID
     */
    public function get_video($video_id)
    {
        $this->db->where('id', $video_id);
        $query = $this->db->get($this->videos_table);
        
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        
        return null;
    }

    /**
     * Get all unique categories with enhanced statistics
     */
    public function get_all_categories($include_counts = true)
    {
        if ($include_counts) {
            $this->db->select('category, COUNT(*) as course_count, 
                              AVG(price) as avg_price,
                              MIN(price) as min_price,
                              MAX(price) as max_price,
                              SUM(CASE WHEN is_free = 1 OR price = 0 THEN 1 ELSE 0 END) as free_courses');
        } else {
            $this->db->select('category');
        }
        
        $this->db->where('category IS NOT NULL');
        $this->db->where('category !=', '');
        $this->db->where('is_active', 1);
        $this->db->where('is_public', 1);
        $this->db->group_by('category');
        $this->db->order_by('category', 'ASC');
        $query = $this->db->get($this->courses_table);
        
        return $query->result_array();
    }

    /**
     * Get all available levels
     */
    public function get_all_levels()
    {
        $this->db->select('level, COUNT(*) as course_count');
        $this->db->where('is_active', 1);
        $this->db->where('is_public', 1);
        $this->db->group_by('level');
        $this->db->order_by('FIELD(level, "Beginner", "Intermediate", "Advanced")', false);
        $query = $this->db->get($this->courses_table);
        
        return $query->result_array();
    }

    /**
     * Get all available languages
     */
    public function get_all_languages()
    {
        $this->db->select('language, COUNT(*) as course_count');
        $this->db->where('language IS NOT NULL');
        $this->db->where('language !=', '');
        $this->db->where('is_active', 1);
        $this->db->where('is_public', 1);
        $this->db->group_by('language');
        $this->db->order_by('language', 'ASC');
        $query = $this->db->get($this->courses_table);
        
        return $query->result_array();
    }

    /**
     * Search courses with enhanced filtering
     */
    public function search_courses($search_term, $filters = [], $limit = null)
    {
        $this->db->group_start();
        $this->db->like('title', $search_term);
        $this->db->or_like('description', $search_term);
        $this->db->or_like('category', $search_term);
        $this->db->group_end();
        
        // Apply additional filters
        if (!empty($filters['category'])) {
            $this->db->where('category', $filters['category']);
        }
        
        if (!empty($filters['level'])) {
            $this->db->where('level', $filters['level']);
        }
        
        if (!empty($filters['language'])) {
            $this->db->where('language', $filters['language']);
        }
        
        if (!empty($filters['is_free'])) {
            $this->db->where('is_free', $filters['is_free']);
        }
        
        $this->db->where('is_active', 1);
        $this->db->where('is_public', 1);
        
        if ($limit) {
            $this->db->limit($limit);
        }
        
        $this->db->order_by('sort_order', 'ASC');
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get($this->courses_table);
        
        return $query->result_array();
    }

    /**
     * Advanced search with multiple filters
     */
    public function advanced_search($filters = [])
    {
        if (!empty($filters['title'])) {
            $this->db->like('title', $filters['title']);
        }
        
        if (!empty($filters['category'])) {
            $this->db->where('category', $filters['category']);
        }
        
        if (!empty($filters['level'])) {
            $this->db->where('level', $filters['level']);
        }
        
        if (!empty($filters['language'])) {
            $this->db->where('language', $filters['language']);
        }
        
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('title', $filters['search']);
            $this->db->or_like('description', $filters['search']);
            $this->db->group_end();
        }
        
        if (!empty($filters['date_from'])) {
            $this->db->where('created_at >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $this->db->where('created_at <=', $filters['date_to']);
        }
        
        if (isset($filters['is_free'])) {
            $this->db->where('is_free', $filters['is_free']);
        }
        
        if (!empty($filters['price_min'])) {
            $this->db->where('price >=', $filters['price_min']);
        }
        
        if (!empty($filters['price_max'])) {
            $this->db->where('price <=', $filters['price_max']);
        }
        
        if (!empty($filters['course_duration'])) {
            $this->db->like('course_duration', $filters['course_duration']);
        }
        
        // Default filters
        $this->db->where('is_active', 1);
        if (!isset($filters['include_private'])) {
            $this->db->where('is_public', 1);
        }
        
        $this->db->order_by('sort_order', 'ASC');
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get($this->courses_table);
        
        return $query->result_array();
    }

    /**
     * Get courses count with optional filters
     */
    public function get_courses_count($where = [])
    {
        if (!empty($where)) {
            $this->db->where($where);
        }
        
        // Default to active courses
        if (!isset($where['is_active'])) {
            $this->db->where('is_active', 1);
        }
        
        return $this->db->count_all_results($this->courses_table);
    }

    /**
     * Get featured courses (based on sort_order)
     */
    public function get_featured_courses($limit = 6)
    {
        $this->db->where('is_active', 1);
        $this->db->where('is_public', 1);
        $this->db->limit($limit);
        $this->db->order_by('sort_order', 'ASC');
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get($this->courses_table);
        
        return $query->result_array();
    }

    /**
     * Get latest courses
     */
    public function get_latest_courses($limit = 10)
    {
        $this->db->where('is_active', 1);
        $this->db->where('is_public', 1);
        $this->db->limit($limit);
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get($this->courses_table);
        
        return $query->result_array();
    }

    /**
     * Get popular courses (placeholder - you can enhance with enrollment/view counts)
     */
    public function get_popular_courses($limit = 10)
    {
        // This is a placeholder - you can enhance by adding view counts or enrollment data
        $this->db->where('is_active', 1);
        $this->db->where('is_public', 1);
        $this->db->limit($limit);
        $this->db->order_by('sort_order', 'ASC');
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get($this->courses_table);
        
        return $query->result_array();
    }

    /**
     * Get courses statistics with enhanced data
     */
    public function get_courses_statistics()
    {
        $stats = [];
        
        // Total courses
        $stats['total_courses'] = $this->db->count_all($this->courses_table);
        
        // Active courses
        $this->db->where('is_active', 1);
        $stats['active_courses'] = $this->db->count_all_results($this->courses_table);
        
        // Public courses
        $this->db->where('is_active', 1);
        $this->db->where('is_public', 1);
        $stats['public_courses'] = $this->db->count_all_results($this->courses_table);
        
        // Free courses
        $this->db->where('is_active', 1);
        $this->db->where('is_public', 1);
        $this->db->group_start();
        $this->db->where('is_free', 1);
        $this->db->or_where('price', 0);
        $this->db->group_end();
        $stats['free_courses'] = $this->db->count_all_results($this->courses_table);
        
        // Paid courses
        $this->db->where('is_active', 1);
        $this->db->where('is_public', 1);
        $this->db->where('is_free', 0);
        $this->db->where('price >', 0);
        $stats['paid_courses'] = $this->db->count_all_results($this->courses_table);
        
        // Courses by category
        $this->db->select('category, COUNT(*) as count');
        $this->db->where('category IS NOT NULL');
        $this->db->where('category !=', '');
        $this->db->where('is_active', 1);
        $this->db->where('is_public', 1);
        $this->db->group_by('category');
        $this->db->order_by('count', 'DESC');
        $query = $this->db->get($this->courses_table);
        $stats['by_category'] = $query->result_array();
        
        // Courses by level
        $this->db->select('level, COUNT(*) as count');
        $this->db->where('is_active', 1);
        $this->db->where('is_public', 1);
        $this->db->group_by('level');
        $this->db->order_by('FIELD(level, "Beginner", "Intermediate", "Advanced")', false);
        $query = $this->db->get($this->courses_table);
        $stats['by_level'] = $query->result_array();
        
        // Courses by language
        $this->db->select('language, COUNT(*) as count');
        $this->db->where('language IS NOT NULL');
        $this->db->where('language !=', '');
        $this->db->where('is_active', 1);
        $this->db->where('is_public', 1);
        $this->db->group_by('language');
        $this->db->order_by('count', 'DESC');
        $query = $this->db->get($this->courses_table);
        $stats['by_language'] = $query->result_array();
        
        // Price statistics
        $this->db->select('AVG(price) as avg_price, MIN(price) as min_price, MAX(price) as max_price, SUM(price) as total_value');
        $this->db->where('is_active', 1);
        $this->db->where('is_public', 1);
        $query = $this->db->get($this->courses_table);
        $stats['price_stats'] = $query->row_array();
        
        // Recent courses (last 30 days)
        $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime('-30 days')));
        $this->db->where('is_active', 1);
        $stats['recent_courses'] = $this->db->count_all_results($this->courses_table);
        
        // Total videos
        $stats['total_videos'] = $this->db->count_all($this->videos_table);
        
        return $stats;
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
     * Get courses with video counts and enhanced details
     */
    public function get_courses_with_video_counts($limit = null, $offset = null, $include_inactive = false)
    {
        $this->db->select($this->courses_table . '.*, COUNT(' . $this->videos_table . '.id) as video_count');
        $this->db->from($this->courses_table);
        $this->db->join($this->videos_table, $this->videos_table . '.course_id = ' . $this->courses_table . '.id', 'left');
        
        if (!$include_inactive) {
            $this->db->where($this->courses_table . '.is_active', 1);
        }
        
        $this->db->group_by($this->courses_table . '.id');
        $this->db->order_by($this->courses_table . '.sort_order', 'ASC');
        $this->db->order_by($this->courses_table . '.created_at', 'DESC');
        
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        return $query->result_array();
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

    /**
     * Get course duration in human readable format
     */
    public function get_formatted_duration($course_duration)
    {
        if (empty($course_duration)) {
            return 'Not specified';
        }
        
        return $course_duration;
    }

    /**
     * Update course sort order
     */
    public function update_course_sort_order($course_id, $sort_order)
    {
        $this->db->where('id', $course_id);
        return $this->db->update($this->courses_table, ['sort_order' => $sort_order, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Bulk update sort orders
     */
    public function update_courses_sort_order($course_orders)
    {
        $this->db->trans_start();
        
        foreach ($course_orders as $course_id => $sort_order) {
            $this->db->where('id', $course_id);
            $this->db->update($this->courses_table, [
                'sort_order' => $sort_order,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Get table names
     */
    public function get_courses_table_name()
    {
        return $this->courses_table;
    }

    public function get_videos_table_name()
    {
        return $this->videos_table;
    }

    /**
     * Get next/previous video in course
     */
    public function get_adjacent_videos($course_id, $current_video_id)
    {
        $videos = $this->get_course_videos($course_id);
        $result = ['previous' => null, 'next' => null];
        
        foreach ($videos as $index => $video) {
            if ($video['id'] == $current_video_id) {
                if ($index > 0) {
                    $result['previous'] = $videos[$index - 1];
                }
                if ($index < count($videos) - 1) {
                    $result['next'] = $videos[$index + 1];
                }
                break;
            }
        }
        
        return $result;
    }

    /**
     * Get course filters for frontend
     */
    public function get_course_filters()
    {
        $filters = [];
        
        // Categories
        $filters['categories'] = $this->get_all_categories(false);
        
        // Levels
        $filters['levels'] = $this->get_all_levels();
        
        // Languages
        $filters['languages'] = $this->get_all_languages();
        
        // Price ranges
        $this->db->select('MIN(price) as min_price, MAX(price) as max_price');
        $this->db->where('is_active', 1);
        $this->db->where('is_public', 1);
        $query = $this->db->get($this->courses_table);
        $price_range = $query->row_array();
        
        $filters['price_range'] = [
            'min' => (float)$price_range['min_price'],
            'max' => (float)$price_range['max_price']
        ];
        
        return $filters;
    }

    /**
     * Get courses for sitemap or SEO
     */
    public function get_courses_for_sitemap()
    {
        $this->db->select('id, title, updated_at, category');
        $this->db->where('is_active', 1);
        $this->db->where('is_public', 1);
        $this->db->order_by('updated_at', 'DESC');
        $query = $this->db->get($this->courses_table);
        
        return $query->result_array();
    }


    public function get_student_by_client_id($client_id)
{
    return $this->db->where('clientid', $client_id)
                    ->get(db_prefix().'klms_students') // adjust table name if different
                    ->row_array();
}
}