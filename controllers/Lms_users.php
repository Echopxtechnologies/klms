<?php defined('BASEPATH') or exit('No direct script access allowed');

class Lms_users extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('elearning_user_model'); 
        $this->load->language('klms', 'english');
        $this->module_base_url = '/klms/Lms_users';
    }

    public function index()
    {
        // if (!is_client_logged_in()) {
        //     redirect(site_url('authentication/login'));
        // }

        $contact_id = get_contact_user_id(); // IMPORTANT: contact, not client
        $data = $this->elearning_user_model->get_all_courses_data($this->module_base_url, $contact_id);
        $data['module_base_url'] = $this->module_base_url;

        $this->data($data);
        $this->view('users/all_courses');
        $this->layout();
    }

    public function view_course($course_id = null)
{
    if (!is_client_logged_in()) redirect(site_url('authentication/login'));
    if (!$course_id || !is_numeric($course_id)) show_404();

    $course = $this->elearning_admin_model->get_course($course_id);
    if (!$course) show_404();

    $videos          = $this->elearning_admin_model->get_course_videos($course_id);
    $related_courses = $this->elearning_admin_model->get_courses_by_category($course['category'], $course_id, 4);

    $contact_id = get_contact_user_id(); // contact id
    $can_watch  = $this->elearning_user_model->client_has_access_to_course($contact_id, $course_id);

    $data = [
        'title'           => $course['title'],
        'course'          => $course,
        'videos'          => $videos,
        'video_count'     => count($videos),
        'total_duration'  => $this->calculate_total_duration($videos),
        'related_courses' => $related_courses,
        'module_base_url' => $this->module_base_url,
        'breadcrumb'      => [
            ['title'=>'Courses','url'=>site_url($this->module_base_url)],
            ['title'=>$course['title'],'url'=>''],
        ],
        'can_watch'       => $can_watch, // <- pass to view
        'is_paid'         => !(!empty($course['is_free']) || (float)$course['price'] <= 0),
    ];

    $this->data($data);
    $this->view('users/view_course');
    $this->layout();
}
    

    /**
     * Guard a paid course before letting them see videos.
     */
    public function course_videos($course_id = null)
{
    if (!is_client_logged_in()) {
        redirect(site_url('authentication/login'));
    }

    if (!$course_id || !is_numeric($course_id)) {
        show_404();
    }

    // Get the contact_id of the logged-in user
    $contact_id = get_contact_user_id();

    // Check if the user has access to the course
    if (!$this->elearning_user_model->client_has_access_to_course($contact_id, $course_id)) {
        set_alert('warning', 'Please purchase this course to watch the videos.');
        redirect(site_url($this->module_base_url . '/view_course/' . $course_id));
        return;
    }

    // Fetch course data and videos
    $course = $this->elearning_admin_model->get_course($course_id);
    $videos = $this->elearning_admin_model->get_course_videos($course_id);
    $video_count = count($videos);

    // Find the video the user wants to watch (if provided)
    $video_id = $this->uri->segment(4);
    $video = $this->elearning_admin_model->get_video($video_id, $course_id);

    // Determine the next and previous videos in the course
    $previous_video = $this->elearning_admin_model->get_previous_video($video_id, $course_id);
    $next_video = $this->elearning_admin_model->get_next_video($video_id, $course_id);

    // Current video index
    $current_index = array_search($video_id, array_column($videos, 'id')) + 1;

    // Data for the view
    $data = [
        'title' => 'Watch Video - ' . html_escape($video['title']),
        'course' => $course,
        'video' => $video,
        'videos' => $videos,
        'total_videos' => $video_count,
        'current_index' => $current_index,
        'previous_video' => $previous_video,
        'next_video' => $next_video,
        'breadcrumb' => [
            ['title' => 'Courses', 'url' => site_url($this->module_base_url)],
            ['title' => $course['title'], 'url' => site_url($this->module_base_url . '/view_course/' . $course_id)],
            ['title' => 'Videos', 'url' => site_url($this->module_base_url . '/course_videos/' . $course_id)],
            ['title' => $video['title'], 'url' => ''],
        ],
    ];

    // Load the view
    $this->data($data);
    $this->view('users/course_videos');
    $this->layout();
}

public function watch_video($course_id = null, $video_id = null)
{
    // 1. Authentication check
    if (!is_client_logged_in()) {
        set_alert('warning', 'Please log in to watch videos.');
        redirect(site_url('authentication/login'));
        return;
    }

    // 2. Input validation with strict type checking
    if (!$course_id || !$video_id || !is_numeric($course_id) || !is_numeric($video_id)) {
        show_404();
        return;
    }

    // Cast to integers to prevent SQL injection
    $course_id = (int)$course_id;
    $video_id = (int)$video_id;

    // 3. Get contact ID securely
    $contact_id = get_contact_user_id();
    if (!$contact_id) {
        set_alert('danger', 'Invalid session. Please log in again.');
        redirect(site_url('authentication/login'));
        return;
    }

    // 4. Verify course access
    if (!$this->elearning_user_model->client_has_access_to_course($contact_id, $course_id)) {
        set_alert('warning', 'You need to enroll in this course to watch videos.');
        redirect(site_url($this->module_base_url . '/view_course/' . $course_id));
        return;
    }

    // 5. Fetch course and verify it exists
    $course = $this->elearning_admin_model->get_course($course_id);
    if (!$course) {
        show_404();
        return;
    }

    // 6. Fetch video and verify it belongs to the course
    $video = $this->elearning_admin_model->get_video($video_id, $course_id);
    if (!$video || (int)$video['course_id'] !== $course_id) {
        set_alert('danger', 'Video not found or does not belong to this course.');
        redirect(site_url($this->module_base_url . '/view_course/' . $course_id));
        return;
    }

    // 7. Get all videos for navigation
    $videos = $this->elearning_admin_model->get_course_videos($course_id);
    $total_videos = count($videos);

    // 8. Get navigation videos
    $previous_video = $this->elearning_admin_model->get_previous_video($video_id, $course_id);
    $next_video = $this->elearning_admin_model->get_next_video($video_id, $course_id);

    // 9. Calculate current index safely
    $current_index = 1;
    foreach ($videos as $idx => $v) {
        if ((int)$v['id'] === $video_id) {
            $current_index = $idx + 1;
            break;
        }
    }

    // 10. Prepare view data
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
        'breadcrumb' => [
            ['title' => 'Courses', 'url' => site_url($this->module_base_url)],
            ['title' => html_escape($course['title']), 'url' => site_url($this->module_base_url . '/view_course/' . $course_id)],
            ['title' => html_escape($video['title']), 'url' => ''],
        ],
    ];

    // 11. Load view
    $this->data($data);
    $this->view('users/watch_video');
    $this->layout();
}

    /**
     * Optional purchase endpoint (skeleton).
     * Implement your gateway here, then create an enrollment row on success.
     */
public function purchase($course_id)
{
    if (!is_client_logged_in()) redirect(site_url('authentication/login'));
    if (!$course_id || !is_numeric($course_id)) show_404();

    $course = $this->elearning_admin_model->get_course($course_id);
    if (!$course) show_404();

    $data = [
        'title'  => 'Purchase Course',
        'course' => $course,
    ];

    $this->data($data);
    $this->view('users/purchase');
    $this->layout();
}

private function current_contact_id()
{
    // Perfex helper – contact logged in
    if (function_exists('get_contact_user_id')) {
        $cid = get_contact_user_id();
        if ($cid) return (int)$cid;
    }

    // Fallback: try to map company -> primary contact
    if (function_exists('get_client_user_id')) {
        $companyId = get_client_user_id();
        if ($companyId) {
            $row = $this->db->where('userid', $companyId)
                            ->where('is_primary', 1)
                            ->get(db_prefix().'contacts')
                            ->row();
            if ($row) return (int)$row->id;
        }
    }

    return 0;
}


/**
 * Simulated payment success handler
 * (in future, payment gateway webhook will redirect here)
 */
public function payment_success($course_id)
{
    if (!is_client_logged_in()) redirect(site_url('authentication/login'));
    if (!$course_id || !is_numeric($course_id)) show_404();

    $contact_id = $this->current_contact_id();   // ✅ CONTACT id
    if (!$contact_id) {
        set_alert('warning','Could not determine your contact profile. Please log out and log in again.');
        redirect(site_url('authentication/login'));
        return;
    }

    $enrollment = [
        'student_id'        => $contact_id,              // ✅ matches tblcontacts.id
        'course_id'         => (int)$course_id,
        'enrolled_date'     => date('Y-m-d H:i:s'),
        'payment_status'    => 'paid',
        'payment_reference' => 'TEST-'.strtoupper(bin2hex(random_bytes(4))),
        'expiry_date'       => null,
        'access_status'     => 'active',
    ];

    // upsert
    $exists = $this->db->where('student_id', $contact_id)
                       ->where('course_id', (int)$course_id)
                       ->get(db_prefix().'elearning_enrollments')
                       ->row();

    if ($exists) {
        $this->db->where('id', $exists->id)->update(db_prefix().'elearning_enrollments', [
            'payment_status'    => 'paid',
            'payment_reference' => $enrollment['payment_reference'],
            'access_status'     => 'active',
        ]);
    } else {
        $this->db->insert(db_prefix().'elearning_enrollments', $enrollment);
    }

    set_alert('success','Payment simulated successfully! You are now enrolled.');
    redirect(site_url('klms/lms_users/course_videos/'.$course_id));
}

    public function registration($course_id = null)
    {
        if(is_client_logged_in()){
            redirect(site_url('klms/Lms_users/purchase/'.$course_id));
        }
        if ($this->input->method(true) === 'POST') {
            $post = $this->input->post(null, true);

            // Create + auto-enroll (your wrapper)
            $student_id = $this->elearning_user_model->enroll_student($post, (int)$course_id);

            if ($student_id) {
                set_alert('success', 'Registration successful and enrollment created.');
                redirect(site_url($this->module_base_url . '/enroll_success/' . (int)$course_id));
                return;
            }
            set_alert('warning', 'Registration failed. The email may already be registered.');
        }

        $return_to = $this->input->get('return_to', true) ?: site_url($this->module_base_url);
        $data = [
            'title'           => 'Student Registration',
            'return_to'       => $return_to,
            'module_base_url' => $this->module_base_url,
            'course_id'       => (int)$course_id,
        ];
        $this->data($data);
        $this->view('users/register_user');
        $this->layout();
    }


    /**
     * Simple confirmation page after registration
     */
    public function enroll_success($course_id = null)
    {
        if (!$course_id) show_404();

        $course = $this->elearning_admin_model->get_course($course_id);

        $data = [
            'title'      => 'Enrollment Successful',
            'course'     => $course,
            'module_base_url' => $this->module_base_url,
        ];

        $this->data($data);
        $this->view('users/enroll_success');
        $this->layout();
    }

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






}
