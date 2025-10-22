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
        
        // ✅ CRITICAL: Disable CSRF for payment callback methods
        $csrf_exempt_methods = [
            'payment_success',
            'razorpay_webhook'
        ];
        
        if (in_array($this->router->method, $csrf_exempt_methods)) {
            $this->security->csrf_show_error = false;
            $this->config->set_item('csrf_protection', false);
        }
    }

    // ===================================================================
    // DASHBOARD & COURSE LISTING
    // ===================================================================

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
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }

        if (!$course_id || !is_numeric($course_id)) {
            show_404();
        }
        $course_id = (int)$course_id;

        $course = $this->elearning_user_model->get_course($course_id);
        if (!$course) {
            show_404();
        }

        $videos = $this->elearning_admin_model->get_course_videos($course_id);
        $related_courses = $this->elearning_user_model->get_courses_by_category(
            $course['category'], 
            $course_id, 
            4
        );

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
        if (!is_client_logged_in()) {
            set_alert('warning', 'Please log in to access course videos.');
            redirect(site_url('authentication/login'));
            return;
        }

        if (!$course_id || !is_numeric($course_id)) {
            show_404();
            return;
        }
        $course_id = (int)$course_id;

        $contact_id = get_contact_user_id();

        $course = $this->elearning_admin_model->get_course($course_id);
        if (!$course) {
            show_404();
            return;
        }

        // Verify course access with payment proof
        if (!$this->verify_course_access_with_payment($course_id, $contact_id)) {
            set_alert('warning', 'You are not enrolled in this course or your payment is pending.');
            redirect(site_url($this->module_base_url . '/view_course/' . $course_id));
            return;
        }

        $videos = $this->elearning_admin_model->get_course_videos($course_id);
        $video_count = count($videos);
        $total_duration = $this->calculate_total_duration($videos);

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

    // 4. Verify course exists
    $course = $this->elearning_admin_model->get_course($course_id);
    if (!$course) {
        show_404();
        return;
    }

    // 5. CHECK ENROLLMENT WITH PAYMENT - Critical security check
    if (!$this->verify_course_access_with_payment($course_id, $contact_id)) {
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
 * Purchase course - direct payment (bypasses invoice page)
 */
/**
 * Purchase course - direct payment (bypasses invoice page)
 */
public function purchase($course_id = null)
{
    // ✅ 1. Check if user is logged in
    if (!is_client_logged_in()) {
        set_alert('warning', 'Please log in to purchase courses.');
        redirect(site_url('authentication/login'));
        return;
    }

    // ✅ 2. Validate course ID
    if (!$course_id || !is_numeric($course_id)) {
        show_404();
    }

    // ✅ 3. Get course details
    $course = $this->elearning_admin_model->get_course($course_id);
    if (!$course) {
        show_404();
    }

    // ✅ 4. Get contact ID
    $contact_id = get_contact_user_id();

    // ✅ 5. Load contact and verify they exist
    $this->load->model('clients_model');
    $contact = $this->clients_model->get_contact($contact_id);
    
    if (!$contact) {
        set_alert('danger', 'User account not found. Please contact support.');
        redirect(site_url('authentication/logout'));
        return;
    }

    // ✅ 6. CRITICAL: Check if user account is active
    if (!isset($contact->active) || $contact->active != 1) {
        log_activity('Inactive user attempted purchase - Contact ID: ' . $contact_id . ' - Course ID: ' . $course_id);
        
        set_alert('danger', 'Your account is inactive. Please contact support to activate your account.');
        redirect(site_url($this->module_base_url . '/all_courses'));
        return;
    }

    // ✅ 7. Check if already enrolled
    $existing_enrollment = $this->db->get_where(db_prefix() . 'elearning_enrollments', [
        'student_id' => $contact_id,
        'course_id' => $course_id,
        'access_status' => 'active',
        'payment_status' => 'paid'
    ])->row();

    if ($existing_enrollment) {
        set_alert('info', 'You are already enrolled in this course.');
        redirect(site_url($this->module_base_url . '/course_videos/' . $course_id));
        return;
    }

    // ✅ 8. FREE COURSE: Direct enrollment
    if ($course['is_free'] == 1 || (float)$course['price'] <= 0) {
        $free_payment_id = 'FREE_' . time() . '_' . $contact_id;
        
        redirect(site_url($this->module_base_url . '/payment_success/0/' . $course_id . '?free=1&payment_id=' . $free_payment_id));
        return;
    }

    // ✅ 9. PAID COURSE: Verify client account exists
    $client_id = $contact->userid;
    
    if (!$client_id || $client_id <= 0) {
        set_alert('danger', 'No client account found. Please contact support.');
        redirect(site_url($this->module_base_url . '/view_course/' . $course_id));
        return;
    }

    // ✅ 10. Check for existing unpaid invoice
    $this->load->model('invoices_model');
    
    $existing_invoice = $this->db->select('id, status, hash')
        ->from(db_prefix() . 'invoices')
        ->where('clientid', $client_id)
        ->like('adminnote', 'Course ID: ' . $course_id)
        ->where('status !=', 2) // Not paid
        ->order_by('id', 'DESC')
        ->limit(1)
        ->get()
        ->row();

    if ($existing_invoice) {
        // Use existing unpaid invoice
        $invoice_id = $existing_invoice->id;
        $invoice = $this->invoices_model->get($invoice_id);
        
        log_activity('Reusing existing invoice - Invoice: ' . $invoice_id . ' - Course: ' . $course_id . ' - Contact: ' . $contact_id);
    } else {
        // ✅ 11. Create new invoice
        $invoice_data = [
            'clientid'      => $client_id,
            'date'          => date('Y-m-d'),
            'duedate'       => date('Y-m-d', strtotime('+7 days')),
            'currency'      => get_base_currency()->id,
            'subtotal'      => $course['price'],
            'total'         => $course['price'],
            'status'        => 1, // unpaid
            'allowed_payment_modes' => ['razorpay'], // Only Razorpay
            'clientnote'    => 'Invoice for Course: ' . $course['title'],
            'adminnote'     => 'Course ID: ' . $course_id . ', Student ID: ' . $contact_id,
            'newitems'      => [
                [
                    'description' => 'Course Enrollment: ' . $course['title'],
                    'long_description' => 'Access to all course videos and materials',
                    'qty'         => 1,
                    'rate'        => $course['price'],
                    'unit'        => '',
                    'order'       => 1,
                ]
            ]
        ];

        $invoice_id = $this->invoices_model->add($invoice_data);
        
        if (!$invoice_id) {
            log_activity('Invoice creation failed - Course: ' . $course_id . ' - Contact: ' . $contact_id);
            set_alert('danger', 'Failed to create invoice. Please try again or contact support.');
            redirect(site_url($this->module_base_url . '/view_course/' . $course_id));
            return;
        }

        $invoice = $this->invoices_model->get($invoice_id);
        
        log_activity('Invoice created - Invoice: ' . $invoice_id . ' - Course: ' . $course_id . ' - Amount: ₹' . $course['price'] . ' - Contact: ' . $contact_id);
    }

    // ✅ 12. Verify invoice was retrieved
    if (!$invoice) {
        log_activity('Invoice retrieval failed - Invoice ID: ' . $invoice_id);
        set_alert('danger', 'Invoice not found. Please contact support.');
        redirect(site_url($this->module_base_url . '/view_course/' . $course_id));
        return;
    }

    // ✅ 13. DIRECT PAYMENT: Load Razorpay gateway and process payment
    try {
        // Load the Razorpay payment gateway library
        $this->load->library('razor_pay_gateway');
        
        // Prepare payment data
        $payment_data = [
            'invoice'   => $invoice,
            'invoiceid' => $invoice_id,
            'amount'    => $invoice->total,
            'hash'      => $invoice->hash,
        ];
        
        log_activity('Initiating Razorpay payment - Invoice: ' . $invoice_id . ' - Course: ' . $course_id . ' - Amount: ₹' . $invoice->total . ' - Contact: ' . $contact_id);
        
        // ✅ Call Razorpay's process_payment() - this will redirect to payment page
        $this->razor_pay_gateway->process_payment($payment_data);
        
    } catch (Exception $e) {
        log_activity('Razorpay Payment Error - Invoice: ' . $invoice_id . ' - Error: ' . $e->getMessage());
        set_alert('danger', 'Payment processing failed: ' . $e->getMessage());
        redirect(site_url($this->module_base_url . '/view_course/' . $course_id));
    }
}

    /**
     * Payment success callback - handles both GET (Razorpay redirect) and free courses
     */
/**
 * Payment success callback - handles Perfex Razorpay module redirects
 */
public function payment_success($invoice_id = null, $course_id = null)
{
    if (!is_client_logged_in()) {
        redirect(site_url('authentication/login'));
    }
    
    $contact_id = get_contact_user_id();
    
    // ✅ HANDLE FREE COURSE
    $is_free_course = $this->input->get('free');
    $free_payment_id = $this->input->get('payment_id');
    
    if ($is_free_course && $course_id && $free_payment_id) {
        return $this->handle_free_course_enrollment($course_id, $contact_id, $free_payment_id);
    }
    
    // ✅ VALIDATE INVOICE
    if (!$invoice_id || !is_numeric($invoice_id)) {
        set_alert('warning', 'Invalid invoice reference.');
        redirect(site_url($this->module_base_url . '/all_courses'));
        return;
    }

    $this->load->model('invoices_model');
    $invoice = $this->invoices_model->get($invoice_id);

    if (!$invoice) {
        set_alert('warning', 'Invoice not found.');
        redirect(site_url($this->module_base_url . '/all_courses'));
        return;
    }

    // Extract course_id from adminnote
    if (!$course_id) {
        $course_id = $this->extract_course_id_from_admin_note($invoice->adminnote);
    }

    if (!$course_id) {
        set_alert('warning', 'Course information not found.');
        redirect(site_url($this->module_base_url . '/all_courses'));
        return;
    }

    // Validate client ownership
    $this->load->model('clients_model');
    $contact = $this->clients_model->get_contact($contact_id);
    if (!$contact || $invoice->clientid != $contact->userid) {
        set_alert('warning', 'Access denied.');
        redirect(site_url($this->module_base_url . '/all_courses'));
        return;
    }

    // ✅ Check if payment was verified by Razorpay module
    $payment_verified = $this->input->get('payment_verified');
    $payment_id = $this->input->get('razorpay_payment_id');
    
    // Check for payment error
    if ($this->input->get('payment_error')) {
        set_alert('warning', 'Payment verification failed. Please try again or contact support.');
        redirect(site_url($this->module_base_url . '/view_course/' . $course_id));
        return;
    }

    // ✅ If payment verified by Razorpay module, check invoice status
    if ($payment_verified || !$payment_id) {
        // Check if invoice is paid (Razorpay module already recorded payment)
        if ($invoice->status != 2) { // 2 = paid
            log_activity('Payment incomplete - Invoice: ' . $invoice_id . ' - Status: ' . $invoice->status);
            set_alert('warning', 'Payment not completed. Please try again.');
            redirect(site_url($this->module_base_url . '/view_course/' . $course_id));
            return;
        }
        
        // ✅ Get the actual payment transaction ID from invoice payments
        $this->load->model('payments_model');
        $payments = $this->payments_model->get_invoice_payments($invoice_id);
        
        if (empty($payments)) {
            log_activity('No payment record found - Invoice: ' . $invoice_id);
            set_alert('warning', 'Payment record not found. Please contact support.');
            redirect(site_url($this->module_base_url . '/view_course/' . $course_id));
            return;
        }
        
        // Get the latest payment
        $latest_payment = end($payments);
        $payment_id = $latest_payment['transactionid'];
        
        log_activity('Payment found in CRM - Transaction ID: ' . $payment_id . ' - Invoice: ' . $invoice_id);
    }

    // ✅ CHECK DUPLICATE ENROLLMENT
    $existing = $this->db->get_where(db_prefix() . 'elearning_enrollments', [
        'student_id' => $contact_id,
        'course_id' => $course_id
    ])->row();

    if ($existing && $existing->payment_status === 'paid' && $existing->access_status === 'active') {
        set_alert('info', 'You are already enrolled in this course.');
        redirect(site_url($this->module_base_url . '/course_videos/' . $course_id));
        return;
    }

    // ✅ CREATE OR UPDATE ENROLLMENT
    return $this->finalize_enrollment($invoice_id, $course_id, $contact_id, $payment_id, $invoice);
}

/**
 * Finalize enrollment after payment verification
 */
private function finalize_enrollment($invoice_id, $course_id, $contact_id, $payment_id, $invoice)
{
    try {
        $this->db->trans_start();

        // Check for existing enrollment
        $existing_enrollment = $this->db->get_where(db_prefix() . 'elearning_enrollments', [
            'student_id' => $contact_id,
            'course_id' => $course_id
        ])->row();

        if ($existing_enrollment) {
            // Update existing enrollment
            $this->db->where('id', $existing_enrollment->id);
            $this->db->update(db_prefix() . 'elearning_enrollments', [
                'payment_status' => 'paid',
                'payment_reference' => $payment_id,
                'access_status' => 'active',
                'enrolled_date' => date('Y-m-d H:i:s'),
            ]);
            $enrollment_id = $existing_enrollment->id;
        } else {
            // Create new enrollment
            $this->db->insert(db_prefix() . 'elearning_enrollments', [
                'student_id' => $contact_id,
                'course_id' => $course_id,
                'enrolled_date' => date('Y-m-d H:i:s'),
                'payment_status' => 'paid',
                'payment_reference' => $payment_id,
                'access_status' => 'active',
            ]);
            $enrollment_id = $this->db->insert_id();
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            throw new Exception('Database transaction failed');
        }

        // ✅ Log success
        log_activity('LMS Enrollment Created - Transaction: ' . $payment_id . ' - Invoice: ' . $invoice_id . ' - Course: ' . $course_id . ' - Enrollment: ' . $enrollment_id);

        set_alert('success', 'Payment successful! You are now enrolled in the course. Transaction ID: ' . $payment_id);
        redirect(site_url($this->module_base_url . '/course_videos/' . $course_id));

    } catch (Exception $e) {
        $this->db->trans_rollback();
        
        log_activity('Enrollment Creation Failed - Transaction: ' . $payment_id . ' - Error: ' . $e->getMessage());
        
        set_alert('warning', 'Payment received but enrollment failed. Please contact support with Transaction ID: ' . $payment_id);
        redirect(site_url($this->module_base_url . '/all_courses'));
    }
}

    /**
     * Handle free course enrollment
     */
    private function handle_free_course_enrollment($course_id, $contact_id, $payment_id)
    {
        // Check if already enrolled
        $existing_enrollment = $this->db->get_where(db_prefix() . 'elearning_enrollments', [
            'student_id' => $contact_id,
            'course_id' => $course_id
        ])->row();

        if ($existing_enrollment) {
            // Update existing enrollment
            $this->db->where('id', $existing_enrollment->id);
            $this->db->update(db_prefix() . 'elearning_enrollments', [
                'payment_status' => 'paid',
                'payment_reference' => $payment_id,
                'access_status' => 'active',
                'enrolled_date' => date('Y-m-d H:i:s'),
            ]);
        } else {
            // Create new enrollment
            $this->db->insert(db_prefix() . 'elearning_enrollments', [
                'student_id' => $contact_id,
                'course_id' => $course_id,
                'enrolled_date' => date('Y-m-d H:i:s'),
                'payment_status' => 'paid',
                'payment_reference' => $payment_id,
                'access_status' => 'active',
            ]);
        }

        log_activity('Free course enrollment - Course ID: ' . $course_id . ' - Student: ' . $contact_id);
        
        set_alert('success', 'Enrollment successful! You can now access the course.');
        redirect(site_url($this->module_base_url . '/course_videos/' . $course_id));
    }

    /**
     * Finalize payment and create enrollment
     */
    private function finalize_payment_and_enrollment($invoice_id, $course_id, $contact_id, $payment_id, $invoice)
    {
        try {
            // Start database transaction
            $this->db->trans_start();

            // ✅ 1. Update CRM invoice as paid
            $this->load->model('invoices_model');
            $success = $this->invoices_model->mark_as_paid($invoice_id, $payment_id, 'Razorpay');
            
            if (!$success) {
                throw new Exception('Failed to update invoice status');
            }

            // ✅ 2. Add payment record in CRM payments table
            $this->load->model('payments_model');
            $payment_data = [
                'invoiceid' => $invoice_id,
                'amount' => $invoice->total,
                'paymentmode' => 'Razorpay',
                'transactionid' => $payment_id,
                'date' => date('Y-m-d H:i:s'),
            ];
            
            $payment_record_id = $this->payments_model->add($payment_data);

            // ✅ 3. Check for existing enrollment
            $existing_enrollment = $this->db->get_where(db_prefix() . 'elearning_enrollments', [
                'student_id' => $contact_id,
                'course_id' => $course_id
            ])->row();

            if ($existing_enrollment) {
                // Update existing enrollment with payment proof
                $this->db->where('id', $existing_enrollment->id);
                $this->db->update(db_prefix() . 'elearning_enrollments', [
                    'payment_status' => 'paid',
                    'payment_reference' => $payment_id,
                    'access_status' => 'active',
                    'enrolled_date' => date('Y-m-d H:i:s'),
                ]);
                $enrollment_id = $existing_enrollment->id;
            } else {
                // Create new enrollment with payment proof
                $this->db->insert(db_prefix() . 'elearning_enrollments', [
                    'student_id' => $contact_id,
                    'course_id' => $course_id,
                    'enrolled_date' => date('Y-m-d H:i:s'),
                    'payment_status' => 'paid',
                    'payment_reference' => $payment_id,
                    'access_status' => 'active',
                ]);
                $enrollment_id = $this->db->insert_id();
            }

            // Commit transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Database transaction failed');
            }

            // ✅ Log successful transaction
            log_activity('Payment Finalized - Transaction: ' . $payment_id . ' - Invoice: ' . $invoice_id . ' - Course: ' . $course_id . ' - Enrollment: ' . $enrollment_id);

            set_alert('success', 'Payment successful! Transaction ID: ' . $payment_id . '. You are now enrolled.');
            redirect(site_url($this->module_base_url . '/course_videos/' . $course_id));

        } catch (Exception $e) {
            $this->db->trans_rollback();
            
            log_activity('Payment Finalization Error - Transaction: ' . $payment_id . ' - Error: ' . $e->getMessage());
            
            set_alert('danger', 'Payment received but enrollment failed. Contact support. Transaction ID: ' . $payment_id);
            redirect(site_url($this->module_base_url . '/all_courses'));
        }
    }

    /**
     * Extract course ID from invoice admin note
     */
    private function extract_course_id_from_admin_note($note)
    {
        if (preg_match('/Course ID: (\d+)/', $note, $matches)) {
            return (int)$matches[1];
        }
        return null;
    }

    /**
     * Verify course access with payment proof
     */
    /**
 * Verify course access with payment proof (with debugging)
 */
private function verify_course_access_with_payment($course_id, $student_id)
{
    // Check if course is free first
    $course = $this->elearning_admin_model->get_course($course_id);
    if ($course && ($course['is_free'] == 1 || (float)$course['price'] <= 0)) {
        return true; // Free courses always accessible
    }

    // For paid courses, verify enrollment with payment
    $this->db->where([
        'student_id' => $student_id,
        'course_id' => $course_id,
    ]);
    
    $enrollment = $this->db->get(db_prefix() . 'elearning_enrollments')->row();

    if (!$enrollment) {
        log_activity('Access denied - No enrollment: Student ' . $student_id . ' - Course ' . $course_id);
        return false;
    }
    
    // Check payment status
    if ($enrollment->payment_status !== 'paid') {
        log_activity('Access denied - Payment pending: Student ' . $student_id . ' - Course ' . $course_id . ' - Status: ' . $enrollment->payment_status);
        return false;
    }
    
    // Check access status
    if ($enrollment->access_status !== 'active') {
        log_activity('Access denied - Not active: Student ' . $student_id . ' - Course ' . $course_id . ' - Status: ' . $enrollment->access_status);
        return false;
    }
    
    // Check payment reference exists
    if (empty($enrollment->payment_reference)) {
        log_activity('Access denied - No payment reference: Student ' . $student_id . ' - Course ' . $course_id);
        return false;
    }
    
    return true; // All checks passed
}

    // ===================================================================
    // REGISTRATION
    // ===================================================================

    /**
     * Student registration with duplicate prevention and auto-login
     */
    public function registration($course_id = null)
    {
        // Already logged in check
        if (is_client_logged_in()) {
            if ($course_id) {
                redirect(site_url($this->module_base_url . '/view_course/' . $course_id));
            } else {
                redirect(site_url($this->module_base_url . '/dashboard'));
            }
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

            // Check if email exists
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

        // Display registration form
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
     * AJAX: Check if email exists
     */
    public function check_email_exists()
    {
        $email = trim(strtolower($this->input->post('email', true)));
        $exists = $email ? $this->elearning_user_model->check_email_exists($email) : false;
        
        $this->output
            ->set_content_type('application/json')
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
     */
    private function check_course_access($contact_id, $course_id, $course)
    {
        // Check if course is free
        $is_free = ($course['is_free'] == 1 || (float)$course['price'] <= 0);
        
        if ($is_free) {
            return true;
        }

        // For paid courses, check enrollment with payment
        $enrollment = $this->db->select('id, access_status, payment_status, payment_reference')
            ->where('student_id', (int)$contact_id)
            ->where('course_id', (int)$course_id)
            ->get(db_prefix() . 'elearning_enrollments')
            ->row();

        if (!$enrollment) {
            return false;
        }

        // Check enrollment validity
        if ($enrollment->access_status !== 'active' || 
            $enrollment->payment_status !== 'paid' ||
            empty($enrollment->payment_reference)) {
            return false;
        }

        return true;
    }

    /**
     * Calculate total duration of videos
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