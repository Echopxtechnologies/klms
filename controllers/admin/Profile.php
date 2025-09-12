<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * KLMS Profile Controller for Admin
 */
class Profile extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        
        // Check admin permissions
        if (!is_admin()) {
            access_denied('KLMS Profile');
        }
    }

    /**
     * Profile management page
     */
    public function index()
    {
        // Get existing profile data from options table (no separate DB needed)
        $profile_data = [
            'instructor_name' => get_option('klms_instructor_name'),
            'instructor_title' => get_option('klms_instructor_title'),
            'instructor_bio' => get_option('klms_instructor_bio'),
            'instructor_email' => get_option('klms_instructor_email'),
            'instructor_phone' => get_option('klms_instructor_phone'),
            'profile_image' => get_option('klms_profile_image'),
            'social_linkedin' => get_option('klms_social_linkedin'),
            'social_twitter' => get_option('klms_social_twitter'),
            'social_facebook' => get_option('klms_social_facebook'),
            'show_on_frontend' => get_option('klms_show_on_frontend')
        ];

        // Handle form submission
        if ($this->input->post()) {
            $this->_handle_profile_update();
        }

        $data['title'] = _l('klms_profile_management');
        $data['profile'] = $profile_data;
        $data['bodyclass'] = 'klms-profile';
        
        $this->load->view('admin/klms/profile/manage', $data);
    }

    /**
     * Handle profile form update
     */
    private function _handle_profile_update()
    {
        // CSRF Protection
        if (!$this->input->post()) {
            return;
        }

        // Simple validation
        $instructor_name = $this->input->post('instructor_name', true);
        $instructor_title = $this->input->post('instructor_title', true);
        $instructor_bio = $this->input->post('instructor_bio', true);
        $instructor_email = $this->input->post('instructor_email', true);
        $instructor_phone = $this->input->post('instructor_phone', true);
        $social_linkedin = $this->input->post('social_linkedin', true);
        $social_twitter = $this->input->post('social_twitter', true);
        $social_facebook = $this->input->post('social_facebook', true);
        $show_on_frontend = (int)$this->input->post('show_on_frontend');

        if (empty($instructor_name) || empty($instructor_title)) {
            set_alert('danger', _l('klms_required_fields_empty'));
            return;
        }

        // Update options (no separate table needed)
        update_option('klms_instructor_name', $instructor_name);
        update_option('klms_instructor_title', $instructor_title);
        update_option('klms_instructor_bio', $instructor_bio);
        update_option('klms_instructor_email', $instructor_email);
        update_option('klms_instructor_phone', $instructor_phone);
        update_option('klms_social_linkedin', $social_linkedin);
        update_option('klms_social_twitter', $social_twitter);
        update_option('klms_social_facebook', $social_facebook);
        update_option('klms_show_on_frontend', $show_on_frontend);

        set_alert('success', _l('klms_profile_updated_successfully'));
        redirect(admin_url('klms/profile'));
    }

    /**
     * Handle image upload via AJAX
     */
    public function upload_image()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $response = ['success' => false, 'message' => ''];

        if (empty($_FILES['profile_image']['name'])) {
            $response['message'] = _l('klms_no_file_selected');
            echo json_encode($response);
            return;
        }

        // Create upload directory
        $upload_path = FCPATH . 'uploads/klms/profile/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $this->load->library('upload');
        
        $config = [
            'upload_path'   => $upload_path,
            'allowed_types' => 'jpg|jpeg|png|gif',
            'max_size'      => 2048, // 2MB
            'encrypt_name'  => true
        ];

        $this->upload->initialize($config);

        if ($this->upload->do_upload('profile_image')) {
            $upload_data = $this->upload->data();
            
            // Delete old image if exists
            $old_image = get_option('klms_profile_image');
            if ($old_image && file_exists($upload_path . $old_image)) {
                unlink($upload_path . $old_image);
            }
            
            // Save new image name
            update_option('klms_profile_image', $upload_data['file_name']);
            
            $response['success'] = true;
            $response['message'] = _l('klms_image_uploaded_successfully');
            $response['image_url'] = base_url('uploads/klms/profile/' . $upload_data['file_name']);
        } else {
            $response['message'] = strip_tags($this->upload->display_errors());
        }

        echo json_encode($response);
    }

    /**
     * Delete profile image
     */
    public function delete_image()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $image_name = get_option('klms_profile_image');
        if ($image_name) {
            $image_path = FCPATH . 'uploads/klms/profile/' . $image_name;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            update_option('klms_profile_image', '');
        }

        echo json_encode([
            'success' => true,
            'message' => _l('klms_image_deleted_successfully')
        ]);
    }
}