<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lms_users extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('elearing_user_model'); 
        $this->load->language('klms', 'english');
        $this->load->model('elearning_admin_model');
        $this->load->model('authentication_model');
        
        // Additional libraries for enhanced functionality
        $this->load->library('pagination');
        $this->load->helper(['url', 'security']);
        
        // Define base URL for this module
        $this->module_base_url = '/klms/Lms_users';
    }
    
    public function add_student()
{
    if (!is_client_logged_in()) {
        redirect(site_url('authentication/login'));
    }

    $return_to = $this->input->get('return_to', true) ?: site_url($this->module_base_url);

    if ($this->input->post()) {
        $post = $this->input->post(null, true);

        // Attach the client id so the profile is linked
        $post['clientid'] = $this->current_client_id();

        $success = $this->elearning_admin_model->create_student_account($post);
        if ($success) {
            set_alert('success', 'Student account created successfully.');
            // come back to where they wanted to go
            redirect($return_to);
        } else {
            set_alert('warning', 'Error creating student account.');
        }
    }

    $data['title'] = 'Add New Student';
    $data['return_to'] = $return_to; // so we preserve it in the form action

    // Render inside client theme
    $theme = active_clients_theme();
    $this->load->view('themes/'.$theme.'/header', $data);
    $this->load->view('klms/users/register_user', $data);
    $this->load->view('themes/'.$theme.'/footer');
}


    /**
     * Main course catalog page with grid/list view
     */
    public function index()
    {
        $data['title'] = _l('klms_get_course');
        $data['page_title'] = 'Learning Management System - Course Catalog';
        
        // Get courses with pagination support
        $config['base_url'] = site_url($this->module_base_url . '/index');
        $config['total_rows'] = $this->elearing_user_model->get_courses_count();
        $config['per_page'] = 12; // Show 12 courses per page
        $config['uri_segment'] = 4; // Updated for correct segment
        $config['use_page_numbers'] = TRUE;
        
        // Pagination styling for Bootstrap
        $config['full_tag_open'] = '<nav><ul class="pagination">';
        $config['full_tag_close'] = '</ul></nav>';
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = 'Next &raquo;';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo; Previous';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = ['class' => 'page-link'];
        
        $this->pagination->initialize($config);
        
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 1; // Updated segment
        $offset = ($page - 1) * $config['per_page'];
        
        // Get courses for current page
        $data['courses'] = $this->elearing_user_model->get_courses_paginated($config['per_page'], $offset);
        $data['pagination'] = $this->pagination->create_links();
        
        // Get additional data for filters and stats
        $data['categories'] = $this->elearing_user_model->get_all_categories();
        $data['total_courses'] = $config['total_rows'];
        $data['featured_courses'] = $this->elearing_user_model->get_featured_courses(6);
        
        // Pass module base URL to view
        $data['module_base_url'] = $this->module_base_url;
        
        $this->data($data);
        $this->view('users/index'); 
        $this->layout();
    }

    

    /**
     * Single course detail page with video list
     */
    public function view_course($course_id = null)
    {
        if (!$course_id || !is_numeric($course_id)) {
            show_404();
        }

        $course = $this->elearing_user_model->get_course($course_id);

        if (!$course) {
            show_404();
        }
        $is_free = !empty($course['is_free']); 
        if (!$is_free && $is_free) {
            // Require profile and continue. If missing, user will be redirected to the form.
            $this->require_student_profile_or_redirect(
                site_url($this->module_base_url.'/view_course/'.$course_id)
            );
        }

        // Get course videos
        $videos = $this->elearing_user_model->get_course_videos($course_id);
        
        // Get related courses
        $related_courses = $this->elearing_user_model->get_courses_by_category($course['category'], $course_id, 4);
        
        $data['title'] = html_escape($course['title']);
        $data['course'] = $course;
        $data['videos'] = $videos;
        $data['video_count'] = count($videos);
        $data['total_duration'] = $this->calculate_total_duration($videos);
        $data['related_courses'] = $related_courses;
        $data['module_base_url'] = $this->module_base_url;
        
        // Breadcrumb data
        $data['breadcrumb'] = [
            ['title' => 'Courses', 'url' => site_url($this->module_base_url)],
            ['title' => $course['title'], 'url' => '']
        ];

        $this->data($data);
        $this->view('users/view_course');
        $this->layout();
    }

    /**
     * Course videos listing page
     */
    public function course_videos($course_id = null)
    {
        if (!$course_id || !is_numeric($course_id)) {
            show_404();
        }

        $course = $this->elearing_user_model->get_course($course_id);
        if (!$course) {
            show_404();
        }
        $is_free = !empty($course['is_free']); 
        if (!$is_free && $is_free) {
            // Require profile and continue. If missing, user will be redirected to the form.
            $this->require_student_profile_or_redirect(
                site_url($this->module_base_url.'/view_course/'.$course_id)
            );
        }
        $videos = $this->elearing_user_model->get_course_videos($course_id);
        
        $data['title'] = 'Videos - ' . html_escape($course['title']);
        $data['course'] = $course;
        $data['videos'] = $videos;
        $data['video_count'] = count($videos);
        $data['total_duration'] = $this->calculate_total_duration($videos);
        $data['module_base_url'] = $this->module_base_url;
        
        // Breadcrumb data
        $data['breadcrumb'] = [
            ['title' => 'Courses', 'url' => site_url($this->module_base_url)],
            ['title' => $course['title'], 'url' => site_url($this->module_base_url . '/view_course/' . $course_id)],
            ['title' => 'Videos', 'url' => '']
        ];

        $this->data($data);
        $this->view('users/course_videos');
        $this->layout();
    }

    /**
     * Video player page
     */
    public function watch_video($course_id = null, $video_id = null)
    {
        if (!$course_id || !is_numeric($course_id) || !$video_id || !is_numeric($video_id)) {
            show_404();
        }

        $course = $this->elearing_user_model->get_course($course_id);

        $is_free = !empty($course['is_free']); 
        if (!$is_free && $is_free) {
            // Require profile and continue. If missing, user will be redirected to the form.
            $this->require_student_profile_or_redirect(
                site_url($this->module_base_url.'/view_course/'.$course_id)
            );
        }
        $video = $this->elearing_user_model->get_video($video_id);
        
        if (!$course || !$video || $video['course_id'] != $course_id) {
            show_404();
        }

        // Get all videos for navigation
        $all_videos = $this->elearing_user_model->get_course_videos($course_id);
        
        // Find current video position and get next/previous
        $current_index = 0;
        foreach ($all_videos as $index => $v) {
            if ($v['id'] == $video_id) {
                $current_index = $index;
                break;
            }
        }
        
        $previous_video = isset($all_videos[$current_index - 1]) ? $all_videos[$current_index - 1] : null;
        $next_video = isset($all_videos[$current_index + 1]) ? $all_videos[$current_index + 1] : null;
        
        $data['title'] = html_escape($video['title']);
        $data['course'] = $course;
        $data['video'] = $video;
        $data['all_videos'] = $all_videos;
        $data['current_index'] = $current_index + 1;
        $data['total_videos'] = count($all_videos);
        $data['previous_video'] = $previous_video;
        $data['next_video'] = $next_video;
        $data['module_base_url'] = $this->module_base_url;
        
        // Breadcrumb data
        $data['breadcrumb'] = [
            ['title' => 'Courses', 'url' => site_url($this->module_base_url)],
            ['title' => $course['title'], 'url' => site_url($this->module_base_url . '/view_course/' . $course_id)],
            ['title' => 'Videos', 'url' => site_url($this->module_base_url . '/course_videos/' . $course_id)],
            ['title' => $video['title'], 'url' => '']
        ];

        $this->data($data);
        $this->view('users/watch_video');
        $this->layout();
    }

    /**
     * Search courses with filters
     */
    public function search()
    {
        $search_term = $this->input->get('q', TRUE);
        $category = $this->input->get('category', TRUE);
        
        // Prepare filters
        $filters = [];
        if (!empty($search_term)) {
            $filters['search'] = $search_term;
        }
        if (!empty($category) && $category !== 'all') {
            $filters['category'] = $category;
        }

        $courses = $this->elearing_user_model->advanced_search($filters);

        $data['title'] = 'Search Results';
        $data['search_term'] = $search_term;
        $data['category'] = $category;
        $data['courses'] = $courses;
        $data['categories'] = $this->elearing_user_model->get_all_categories();
        $data['results_count'] = count($courses);
        $data['module_base_url'] = $this->module_base_url;

        $this->data($data);
        $this->view('users/search_results');
        $this->layout();
    }

    /**
     * Browse courses by category
     */
    public function category($category_name = null)
    {
        if (!$category_name) {
            redirect($this->module_base_url);
        }

        $category_name = urldecode($category_name);
        $courses = $this->elearing_user_model->get_courses_by_category($category_name);

        $data['title'] = 'Courses in ' . html_escape($category_name);
        $data['category_name'] = $category_name;
        $data['courses'] = $courses;
        $data['categories'] = $this->elearing_user_model->get_all_categories();
        $data['course_count'] = count($courses);
        $data['module_base_url'] = $this->module_base_url;

        $this->data($data);
        $this->view('users/category');
        $this->layout();
    }

    /**
     * AJAX endpoints for dynamic loading
     */
    public function ajax_load_courses()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $page = $this->input->post('page', TRUE) ?: 1;
        $category = $this->input->post('category', TRUE);
        $sort = $this->input->post('sort', TRUE) ?: 'date_desc';
        $limit = 12;
        $offset = ($page - 1) * $limit;

        // Prepare filters
        $filters = [];
        if (!empty($category) && $category !== 'all') {
            $filters['category'] = $category;
        }

        $courses = $this->elearing_user_model->get_courses_paginated($limit, $offset, $filters, $sort);
        
        // Format courses for frontend
        foreach ($courses as &$course) {
            $course['url'] = site_url($this->module_base_url . '/view_course/' . $course['id']);
            $course['formatted_date'] = date('M d, Y', strtotime($course['created_at']));
        }

        $response = [
            'courses' => $courses,
            'has_more' => count($courses) == $limit,
            'csrf_token' => $this->security->get_csrf_hash()
        ];

        echo json_encode($response);
        exit;
    }

    public function ajax_search_suggestions()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $term = $this->input->get('term', TRUE);
        if (strlen($term) < 2) {
            echo json_encode([]);
            exit;
        }

        $courses = $this->elearing_user_model->search_courses($term, 10);
        $suggestions = [];

        foreach ($courses as $course) {
            $suggestions[] = [
                'id' => $course['id'],
                'label' => $course['title'],
                'category' => $course['category'],
                'url' => site_url($this->module_base_url . '/view_course/' . $course['id'])
            ];
        }

        echo json_encode($suggestions);
        exit;
    }

    /**
     * Get course statistics (AJAX)
     */
    public function ajax_get_statistics()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $statistics = $this->elearing_user_model->get_courses_statistics();
        echo json_encode($statistics);
        exit;
    }

    /**
     * Helper method to calculate total video duration
     */
    private function calculate_total_duration($videos)
    {
        $total_minutes = 0;
        
        foreach ($videos as $video) {
            if (!empty($video['duration'])) {
                // Parse duration (assuming format like "5:30" or "65" for minutes)
                if (strpos($video['duration'], ':') !== false) {
                    list($minutes, $seconds) = explode(':', $video['duration']);
                    $total_minutes += (int)$minutes + ((int)$seconds / 60);
                } else {
                    $total_minutes += (int)$video['duration'];
                }
            }
        }
        
        if ($total_minutes < 60) {
            return round($total_minutes) . ' minutes';
        } else {
            $hours = floor($total_minutes / 60);
            $minutes = round($total_minutes % 60);
            return $hours . 'h ' . $minutes . 'm';
        }
    }

    /**
     * Extract Vimeo video ID from URL
     */
    private function extract_vimeo_id($url)
    {
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return $matches[1];
        }
        return false;
    }

    private function current_client_id()
{
    print(get_client_user_id());
    return get_client_user_id(); // customer company id
}

private function require_student_profile_or_redirect($return_to)
{
    $client_id = $this->current_client_id();
    $student = $this->elearing_user_model->get_student_by_client_id($client_id);

    if (!$student) {
        // Save a flash note so the user knows why they were redirected
        set_alert('warning', 'Please complete your student profile before accessing this course.');
        redirect(site_url('klms/lms_users/add_student?return_to='.rawurlencode($return_to)));
        exit;
    }
    return $student;
}
}