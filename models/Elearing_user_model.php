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
    public function get_all_courses($where = [], $order_by = 'created_at DESC')
    {
        if (!empty($where)) {
            $this->db->where($where);
        }
        
        $this->db->order_by($order_by);
        $query = $this->db->get($this->courses_table);
        
        return $query->result_array();
    }

    /**
     * Get courses with pagination and sorting
     */
    public function get_courses_paginated($limit, $offset, $filters = [], $sort = 'date_desc')
    {
        // Apply filters
        if (!empty($filters['category'])) {
            $this->db->where('category', $filters['category']);
        }
        
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('title', $filters['search']);
            $this->db->or_like('description', $filters['search']);
            $this->db->group_end();
        }
        
        // Apply sorting
        switch ($sort) {
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
                $this->db->order_by('title', 'ASC');
                break;
            default:
                $this->db->order_by('created_at', 'DESC');
        }
        
        $this->db->limit($limit, $offset);
        $query = $this->db->get($this->courses_table);
        
        return $query->result_array();
    }

    /**
     * Get single course by ID
     */
    public function get_course($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get($this->courses_table);
        
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        
        return null;
    }

    /**
     * Get courses by category (excluding specific course ID)
     */
    public function get_courses_by_category($category, $exclude_id = null, $limit = null)
    {
        $this->db->where('category', $category);
        
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        
        if ($limit) {
            $this->db->limit($limit);
        }
        
        $this->db->order_by('created_at', 'DESC');
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
     * Get all unique categories
     */
    public function get_all_categories()
    {
        $this->db->select('category, COUNT(*) as course_count');
        $this->db->where('category IS NOT NULL');
        $this->db->where('category !=', '');
        $this->db->group_by('category');
        $this->db->order_by('category', 'ASC');
        $query = $this->db->get($this->courses_table);
        
        return $query->result_array();
    }

    /**
     * Search courses by title, description, or category
     */
    public function search_courses($search_term, $limit = null)
    {
        $this->db->group_start();
        $this->db->like('title', $search_term);
        $this->db->or_like('description', $search_term);
        $this->db->or_like('category', $search_term);
        $this->db->group_end();
        
        if ($limit) {
            $this->db->limit($limit);
        }
        
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
        
        return $this->db->count_all_results($this->courses_table);
    }

    /**
     * Get featured courses (you can modify criteria)
     */
    public function get_featured_courses($limit = 6)
    {
        // For now, returns most recent courses
        // You can add a 'featured' column to mark specific courses as featured
        $this->db->limit($limit);
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get($this->courses_table);
        
        return $query->result_array();
    }

    /**
     * Get latest courses
     */
    public function get_latest_courses($limit = 10)
    {
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
        $this->db->limit($limit);
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get($this->courses_table);
        
        return $query->result_array();
    }

    /**
     * Get courses statistics
     */
    public function get_courses_statistics()
    {
        $stats = [];
        
        // Total courses
        $stats['total_courses'] = $this->db->count_all($this->courses_table);
        
        // Courses by category
        $this->db->select('category, COUNT(*) as count');
        $this->db->where('category IS NOT NULL');
        $this->db->where('category !=', '');
        $this->db->group_by('category');
        $this->db->order_by('count', 'DESC');
        $query = $this->db->get($this->courses_table);
        $stats['by_category'] = $query->result_array();
        
        // Recent courses (last 30 days)
        $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime('-30 days')));
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
     * Get courses with video counts
     */
    public function get_courses_with_video_counts($limit = null, $offset = null)
    {
        $this->db->select($this->courses_table . '.*, COUNT(' . $this->videos_table . '.id) as video_count');
        $this->db->from($this->courses_table);
        $this->db->join($this->videos_table, $this->videos_table . '.course_id = ' . $this->courses_table . '.id', 'left');
        $this->db->group_by($this->courses_table . '.id');
        $this->db->order_by($this->courses_table . '.created_at', 'DESC');
        
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        return $query->result_array();
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
}