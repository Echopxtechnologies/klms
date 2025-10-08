<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * LMS Users Controller
 * Handles student/client-facing course and video functionality
 */
class Lms_users extends ClientsController
{
    private $module_base_url;

    public function __construct()
    {
        parent::__construct();
        
        // Load dependencies
        $this->load->model('elearning_admin_model');
        $this->load->model('elearning_user_model');
        $this->load->language('klms', 'english');
        
        $this->module_base_url = 'klms/Lms_users';
    }

        public function index()
    {
        $contact_id = get_contact_user_id();
        $data = $this->elearning_user_model->get_all_courses_data($this->module_base_url, $contact_id);
        $data['module_base_url'] = $this->module_base_url;
        $data['title'] = 'Explore Courses';

        $this->data($data);
        $this->view('users/all_courses');
        $this->layout();
    }

    // ===================================================================
    // DASHBOARD & COURSE LISTING
    // ===================================================================

    /**
     * Main Dashboard - Shows enrolled courses
     */
    public function Dashboard()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }

        $contact_id = get_contact_user_id();
        
        // Get enrolled courses
        $enrolled_courses = $this->elearning_user_model->get_student_enrolled_courses($contact_id);
        
        // Calculate statistics
        $stats = [
            'total_enrolled' => count($enrolled_courses),
            'in_progress' => 0,
            'completed' => 0,
            'not_started' => 0,
        ];

        // Count courses by status
        foreach ($enrolled_courses as $course) {
            $status = $course['progress_status'] ?? 'not_started';
            if (isset($stats[$status])) {
                $stats[$status]++;
            }
        }

        $data = [
            'title' => 'My Learning Dashboard',
            'enrolled_courses' => $enrolled_courses,
            'stats' => $stats,
            'module_base_url' => $this->module_base_url,
        ];

        $this->data($data);
        $this->view('users/dashboard');
        $this->layout();
    }

    /**
     * Browse all available courses
     */
    public function all_courses()
    {
        $contact_id = get_contact_user_id();
        $data = $this->elearning_user_model->get_all_courses_data($this->module_base_url, $contact_id);
        $data['module_base_url'] = $this->module_base_url;
        $data['title'] = 'Explore Courses';

        $this->data($data);
        $this->view('users/all_courses');
        $this->layout();
    }

    // ===================================================================
    // COURSE VIEWING & VIDEO ACCESS
    // ===================================================================

    /**
     * View single course details
     */
    public function view_course($course_id = null)
    {
        // Authentication check
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }

        // Validate course ID
        if (!$course_id || !is_numeric($course_id)) {
            show_404();
        }
        $course_id = (int)$course_id;

        // Get course
        $course = $this->elearning_user_model->get_course($course_id);
        if (!$course) {
            show_404();
        }

        // Get course data
        $videos = $this->elearning_admin_model->get_course_videos($course_id);
        $related_courses = $this->elearning_user_model->get_courses_by_category(
            $course['category'], 
            $course_id, 
            4
        );

        // Check access
        $contact_id = get_contact_user_id();
        $can_watch = $this->elearning_user_model->client_has_access_to_course($contact_id, $course_id);
        $is_paid = !($course['is_free'] == 1 || (float)$course['price'] <= 0);

        $data = [
            'title' => $course['title'],
            'course' => $course,
            'videos' => $videos,
            'video_count' => count($videos),
            'total_duration' => $this->calculate_total_duration($videos),
            'related_courses' => $related_courses,
            'module_base_url' => $this->module_base_url,
            'breadcrumb' => [
                ['title' => 'Courses', 'url' => site_url($this->module_base_url)],
                ['title' => $course['title'], 'url' => ''],
            ],
            'can_watch' => $can_watch,
            'is_paid' => $is_paid,
        ];

        $this->data($data);
        $this->view('users/view_course');
        $this->layout();
    }

    /**
     * Display course videos list (with enrollment check)
     */
    public function course_videos($course_id = null)
    {
        // 1. Authentication check
        if (!is_client_logged_in()) {
            set_alert('warning', 'Please log in to access course videos.');
            redirect(site_url('authentication/login'));
            return;
        }

        // 2. Validate course ID
        if (!$course_id || !is_numeric($course_id)) {
            show_404();
            return;
        }
        $course_id = (int)$course_id;

        // 3. Get contact ID
        $contact_id = get_contact_user_id();

        // 4. Verify course exists
        $course = $this->elearning_admin_model->get_course($course_id);
        if (!$course) {
            show_404();
            return;
        }

        // 5. CHECK ENROLLMENT - Critical security check
        if (!$this->check_course_access($contact_id, $course_id, $course)) {
            set_alert('warning', 'Please purchase this course to access the videos.');
            redirect(site_url($this->module_base_url . '/view_course/' . $course_id));
            return;
        }

        // 6. Get videos
        $videos = $this->elearning_admin_model->get_course_videos($course_id);
        $video_count = count($videos);
        $total_duration = $this->calculate_total_duration($videos);

        // 7. Prepare view data
        $data = [
            'title' => $course['title'] . ' - Course Videos',
            'course' => $course,
            'videos' => $videos,
            'video_count' => $video_count,
            'total_duration' => $total_duration,
            'module_base_url' => $this->module_base_url,
            'breadcrumb' => [
                ['title' => 'Courses', 'url' => site_url($this->module_base_url)],
                ['title' => $course['title'], 'url' => site_url($this->module_base_url . '/view_course/' . $course_id)],
                ['title' => 'Videos', 'url' => ''],
            ],
        ];

        $this->data($data);
        $this->view('users/course_videos');
        $this->layout();
    }

    /**
     * Watch individual video (with enrollment check)
     */
    public function watch_video($course_id = null, $video_id = null)
    {
        // 1. Authentication check
        if (!is_client_logged_in()) {
            set_alert('warning', 'Please log in to watch videos.');
            redirect(site_url('authentication/login'));
            return;
        }

        // 2. Validate IDs
        if (!$course_id || !$video_id || !is_numeric($course_id) || !is_numeric($video_id)) {
            show_404();
            return;
        }
        $course_id = (int)$course_id;
        $video_id = (int)$video_id;

        // 3. Get contact ID
        $contact_id = get_contact_user_id();
        if (!$contact_id) {
            set_alert('danger', 'Invalid session. Please log in again.');
            redirect(site_url('authentication/login'));
            return;
        }
        $sig = $this->input->get('sig', true);

        // Recreate current and previous-minute signatures to allow a ~60s window
        $encKey = (string) config_item('encryption_key');
        $now    = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        $makeSig = function(DateTimeImmutable $t) use ($encKey, $contact_id, $course_id, $video_id) {
        $ts = $t->format('Y-m-d\TH:i'); // minute granularity
        return hash_hmac('sha256', $contact_id . '|' . (int)$course_id . '|' . (int)$video_id . '|' . $ts, $encKey);
        };

        $valid = hash_equals($makeSig($now), (string)$sig)
            || hash_equals($makeSig($now->modify('-1 minute')), (string)$sig);

        if (!$valid) {
        // Don’t reveal which part failed
        set_alert('danger', 'Invalid or expired link. Please reopen the course and try again.');
        redirect(site_url($this->module_base_url . '/course_videos/' . (int)$course_id));
        return;
        }

        // 4. Verify course exists
        $course = $this->elearning_admin_model->get_course($course_id);
        if (!$course) {
            show_404();
            return;
        }

        // 5. CHECK ENROLLMENT - Critical security check
        if (!$this->check_course_access($contact_id, $course_id, $course)) {
            set_alert('warning', 'You need to purchase this course to watch videos.');
            redirect(site_url($this->module_base_url . '/view_course/' . $course_id));
            return;
        }

        // 6. Verify video exists and belongs to course
        $video = $this->elearning_admin_model->get_video($video_id, $course_id);
        if (!$video || (int)$video['course_id'] !== $course_id) {
            set_alert('danger', 'Video not found or does not belong to this course.');
            redirect(site_url($this->module_base_url . '/course_videos/' . $course_id));
            return;
        }

        // 7. Get navigation data
        $videos = $this->elearning_admin_model->get_course_videos($course_id);
        $total_videos = count($videos);
        $previous_video = $this->elearning_admin_model->get_previous_video($video_id, $course_id);
        $next_video = $this->elearning_admin_model->get_next_video($video_id, $course_id);

        // 8. Calculate current index
        $current_index = 1;
        foreach ($videos as $idx => $v) {
            if ((int)$v['id'] === $video_id) {
                $current_index = $idx + 1;
                break;
            }
        }

        // 9. Prepare view data
        $data = [
            'title' => 'Watch: ' . html_escape($video['title']),
            'course' => $course,
            'video' => $video,
            'videos' => $videos,
            'total_videos' => $total_videos,
            'current_index' => $current_index,
            'previous_video' => $previous_video,
            'next_video' => $next_video,
            'module_base_url' => $this->module_base_url,
        ];

        $this->data($data);
        $this->view('users/watch_video');
        $this->layout();
    }

    // ===================================================================
    // ENROLLMENT & PAYMENT
    // ===================================================================

    /**
     * Purchase course page
     */
    public function purchase($course_id = null)
    {
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }

        if (!$course_id || !is_numeric($course_id)) {
            show_404();
        }
        $course_id = (int)$course_id;

        $course = $this->elearning_admin_model->get_course($course_id);
        if (!$course) {
            show_404();
        }

        $data = [
            'title' => 'Purchase Course',
            'course' => $course,
            'module_base_url' => $this->module_base_url,
        ];

        $this->data($data);
        $this->view('users/purchase');
        $this->layout();
    }

    /**
     * Payment success handler
     */
    public function payment_success($course_id = null)
    {
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }

        if (!$course_id || !is_numeric($course_id)) {
            show_404();
        }
        $course_id = (int)$course_id;

        $contact_id = get_contact_user_id();
        if (!$contact_id) {
            set_alert('danger', 'Could not determine your contact profile. Please log out and log in again.');
            redirect(site_url('authentication/login'));
            return;
        }

        // Create enrollment record
        $enrollment_data = [
            'student_id' => $contact_id,
            'course_id' => $course_id,
            'enrolled_date' => date('Y-m-d H:i:s'),
            'payment_status' => 'paid',
            'payment_reference' => 'TEST-' . strtoupper(bin2hex(random_bytes(4))),
            'expiry_date' => null,
            'access_status' => 'active',
        ];

        // Check if enrollment exists
        $existing = $this->db->where('student_id', $contact_id)
                             ->where('course_id', $course_id)
                             ->get(db_prefix() . 'elearning_enrollments')
                             ->row();

        if ($existing) {
            // Update existing enrollment
            $this->db->where('id', $existing->id)
                     ->update(db_prefix() . 'elearning_enrollments', [
                         'payment_status' => 'paid',
                         'payment_reference' => $enrollment_data['payment_reference'],
                         'access_status' => 'active',
                     ]);
        } else {
            // Insert new enrollment
            $this->db->insert(db_prefix() . 'elearning_enrollments', $enrollment_data);
        }

        set_alert('success', 'Payment successful! You are now enrolled in the course.');
        // redirect(site_url($this->module_base_url . '/course_videos/' . $course_id));
        redirect(site_url($this->module_base_url . '/dashboard'));

    }

/**
 * Student registration with duplicate prevention and auto-login
 */
public function registration($course_id = null)
{
    // Already logged in check
    if (is_client_logged_in()) {
        redirect(site_url($this->module_base_url . '/view_course/' . $course_id));
        exit();
    }

    if ($this->input->method(true) === 'POST') {
        // Token validation
        $form_token = $this->input->post('form_token');
        $expected_token = $this->session->userdata('expected_form_token');
        
        if (!$form_token || $form_token !== $expected_token) {
            redirect(site_url('authentication/login'));
            exit();
        }
        
        $this->session->unset_userdata('expected_form_token');
        
        $post = $this->input->post(null, true);
        
        // Validation
        if (empty($post['firstname']) || empty($post['lastname']) || empty($post['email']) || empty($post['password'])) {
            set_alert('danger', 'Please fill in all required fields.');
            redirect(site_url($this->module_base_url . '/registration/' . $course_id));
            exit();
        }

        $email = trim(strtolower($post['email']));
        $post['email'] = $email;

        // CORRECT: Call the model method
        $exists = $this->elearning_user_model->check_email_exists($email);

        if ($exists && isset($exists['exists']) && $exists['exists'] === true) {
            set_alert('warning', 'This email is already registered. Please <a href="' . site_url('authentication/login') . '">login here</a>');
            redirect(site_url($this->module_base_url . '/registration/' . $course_id));
            exit();
        }

        // Create account
        $result = $this->elearning_user_model->enroll_student_with_credentials($post, (int)$course_id);

        if ($result && is_array($result)) {
            // Auto-login
            if ($this->elearning_user_model->auto_login_contact($result['email'], $result['password'])) {
                set_alert('success', 'Welcome! Your account has been created successfully.');
                if ($course_id) {
                    redirect(site_url($this->module_base_url . '/view_course/' . $course_id));
                } else {
                    redirect(site_url($this->module_base_url . '/dashboard'));
                }
                exit();
            }
        }
        
        set_alert('danger', 'Registration failed. Please try again.');
        redirect(site_url($this->module_base_url . '/registration/' . $course_id));
        exit();
    }

    // Display form
    $data = [
        'title' => 'Student Registration',
        'module_base_url' => $this->module_base_url,
        'course_id' => (int)$course_id,
    ];

    $this->data($data);
    $this->view('users/register_user');
    $this->layout();
}
    /**
     * AJAX: Check if email exists (for real-time validation)
     */
    public function check_email_exists() {
        $email = trim(strtolower($this->input->post('email', true)));
        $exists = $email ? $this->elearning_user_model->check_email_exists($email) : false;
        $this->output->set_content_type('application/json')
                    ->set_output(json_encode([
                        'exists' => $exists ? true : false,
                        'type'   => $exists ? $exists['type'] : null,
                    ]));
    }


    /**
     * Enrollment success page
     */
    public function enroll_success($course_id = null)
    {
        if (!$course_id || !is_numeric($course_id)) {
            show_404();
        }

        $course = $this->elearning_admin_model->get_course($course_id);

        $data = [
            'title' => 'Enrollment Successful',
            'course' => $course,
            'module_base_url' => $this->module_base_url,
        ];

        $this->data($data);
        $this->view('users/enroll_success');
        $this->layout();
    }

    // ===================================================================
    // PRIVATE HELPER METHODS
    // ===================================================================

    /**
     * Check if user has access to course
     * @param int $contact_id
     * @param int $course_id
     * @param array $course
     * @return bool
     */
    private function check_course_access($contact_id, $course_id, $course)
    {
        // Check if course is free
        $is_free = ($course['is_free'] == 1 || (float)$course['price'] <= 0);
        
        if ($is_free) {
            return true; // Free courses are accessible to all logged-in users
        }

        // For paid courses, check enrollment
        $enrollment = $this->db->select('id, access_status, payment_status')
            ->where('student_id', (int)$contact_id)
            ->where('course_id', (int)$course_id)
            ->get(db_prefix() . 'elearning_enrollments')
            ->row();

        if (!$enrollment) {
            return false; // Not enrolled
        }

        // Check enrollment status
        if ($enrollment->access_status !== 'active' || $enrollment->payment_status !== 'paid') {
            return false; // Invalid enrollment
        }

        return true; // Valid enrollment
    }

    /**
     * Calculate total duration of videos
     * @param array $videos
     * @return string
     */
    private function calculate_total_duration($videos)
    {
        $total_minutes = 0;
        
        foreach ($videos as $video) {
            if (!empty($video['duration'])) {
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
     * Mark video as watched (AJAX endpoint)
     */
    public function mark_video_watched()
    {
        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            return;
        }

        $course_id = (int)$this->input->post('course_id');
        $video_id = (int)$this->input->post('video_id');
        $contact_id = get_contact_user_id();

        if ($course_id && $video_id && $contact_id) {
            $result = $this->elearning_user_model->mark_video_watched(
                $contact_id, 
                $course_id, 
                $video_id
            );
            
            echo json_encode(['success' => $result]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
        }
    }
}