<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * E-Learning Module Installation
 * Creates all required database tables matching the actual schema
 */

// Get CI instance
$CI = &get_instance();

// ===================================================================
// CREATE UPLOAD DIRECTORIES
// ===================================================================
if (!is_dir('./uploads/courses/')) {
    mkdir('./uploads/courses/', 0755, true);
    log_activity('E-Learning: Created uploads/courses directory');
}

// ===================================================================
// TABLE 1: COURSES
// ===================================================================
if (!$CI->db->table_exists(db_prefix() . 'elearning_courses')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'elearning_courses` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `category` varchar(100) DEFAULT NULL,
        `description` text DEFAULT NULL,
        `cover_image` varchar(500) DEFAULT NULL,
        `price` decimal(10,2) DEFAULT 0.00,
        `is_free` tinyint(1) DEFAULT 0,
        `is_public` tinyint(1) DEFAULT 1,
        `is_active` tinyint(1) DEFAULT 1,
        `course_duration` varchar(50) DEFAULT NULL,
        `level` enum("Beginner","Intermediate","Advanced") DEFAULT "Beginner",
        `language` varchar(50) DEFAULT "English",
        `sort_order` int(11) DEFAULT NULL,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `category` (`category`),
        KEY `is_active` (`is_active`),
        KEY `level` (`level`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;');
    
    log_activity('E-Learning: Created table tblelearning_courses');
}

// ===================================================================
// TABLE 2: VIDEOS
// ===================================================================
if (!$CI->db->table_exists(db_prefix() . 'elearning_videos')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'elearning_videos` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `course_id` int(11) NOT NULL,
        `title` varchar(255) NOT NULL,
        `description` text DEFAULT NULL,
        `vimeo_url` varchar(500) NOT NULL,
        `sort_order` int(11) DEFAULT 0,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `course_id` (`course_id`),
        CONSTRAINT `tblelearning_videos_ibfk_1` FOREIGN KEY (`course_id`) 
            REFERENCES `' . db_prefix() . 'elearning_courses` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;');
    
    log_activity('E-Learning: Created table tblelearning_videos');
}

// ===================================================================
// TABLE 3: ENROLLMENTS
// ===================================================================
if (!$CI->db->table_exists(db_prefix() . 'elearning_enrollments')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'elearning_enrollments` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `student_id` int(11) NOT NULL COMMENT "References tblcontacts.id",
        `course_id` int(11) NOT NULL,
        `enrolled_date` datetime DEFAULT CURRENT_TIMESTAMP,
        `payment_status` enum("pending","paid","failed","free") DEFAULT "pending",
        `payment_reference` varchar(255) DEFAULT NULL,
        `expiry_date` datetime DEFAULT NULL,
        `access_status` enum("active","expired","suspended") DEFAULT "active",
        `notes` text DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `student_id` (`student_id`),
        KEY `course_id` (`course_id`),
        KEY `payment_status` (`payment_status`),
        KEY `access_status` (`access_status`),
        CONSTRAINT `fk_enrollments_student` FOREIGN KEY (`student_id`) 
            REFERENCES `' . db_prefix() . 'contacts` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_enrollments_course` FOREIGN KEY (`course_id`) 
            REFERENCES `' . db_prefix() . 'elearning_courses` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');
    
    log_activity('E-Learning: Created table tblelearning_enrollments');
}

// ===================================================================
// TABLE 4: PROGRESS (Legacy - for compatibility)
// ===================================================================
if (!$CI->db->table_exists(db_prefix() . 'elearning_progress')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'elearning_progress` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `student_id` int(11) NOT NULL COMMENT "References tblcontacts.id",
        `course_id` int(11) NOT NULL,
        `video_id` int(11) NOT NULL,
        `watched_at` datetime NOT NULL,
        `watch_duration` int(11) DEFAULT 0 COMMENT "Seconds watched",
        `completed` tinyint(1) DEFAULT 0,
        `last_position` int(11) DEFAULT 0 COMMENT "Last playback position in seconds",
        PRIMARY KEY (`id`),
        UNIQUE KEY `student_course_video` (`student_id`,`course_id`,`video_id`),
        KEY `student_id` (`student_id`),
        KEY `course_id` (`course_id`),
        KEY `video_id` (`video_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
    
    log_activity('E-Learning: Created table tblelearning_progress');
}

// ===================================================================
// TABLE 5: VIDEO PROGRESS (Enhanced tracking)
// ===================================================================
if (!$CI->db->table_exists(db_prefix() . 'elearning_video_progress')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'elearning_video_progress` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `student_id` int(11) NOT NULL COMMENT "Contact ID from tblcontacts",
        `course_id` int(11) NOT NULL,
        `video_id` int(11) NOT NULL,
        `watch_time` int(11) DEFAULT 0 COMMENT "Seconds watched",
        `total_duration` int(11) DEFAULT 0 COMMENT "Total video duration in seconds",
        `progress_percentage` decimal(5,2) DEFAULT 0.00,
        `completed` tinyint(1) DEFAULT 0,
        `first_watched` datetime DEFAULT NULL,
        `last_watched` datetime DEFAULT NULL,
        `watch_count` int(11) DEFAULT 0,
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_student_video` (`student_id`,`video_id`),
        KEY `idx_student_course` (`student_id`,`course_id`),
        KEY `idx_video` (`video_id`),
        KEY `idx_completed` (`completed`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
    
    log_activity('E-Learning: Created table tblelearning_video_progress');
}

// ===================================================================
// ADD UNIQUE EMAIL CONSTRAINT TO CONTACTS TABLE
// ===================================================================
$indexes = $CI->db->query("SHOW INDEX FROM `" . db_prefix() . "contacts` WHERE Key_name = 'unique_contact_email'")->result();

if (empty($indexes)) {
    try {
        // Clean any existing duplicate emails before adding constraint
        $CI->db->query("
            DELETE t1 FROM `" . db_prefix() . "contacts` t1
            INNER JOIN `" . db_prefix() . "contacts` t2 
            WHERE t1.id > t2.id 
            AND LOWER(TRIM(t1.email)) = LOWER(TRIM(t2.email))
        ");
        
        // Add the unique constraint
        $CI->db->query("ALTER TABLE `" . db_prefix() . "contacts` 
            ADD UNIQUE KEY `unique_contact_email` (`email`)");
        
        log_activity('E-Learning: Added unique email constraint to contacts table');
    } catch (Exception $e) {
        log_activity('E-Learning: Email constraint already exists or failed: ' . $e->getMessage());
    }
}

// ===================================================================
// MODULE OPTIONS
// ===================================================================
if (!get_option('klms_module_version')) {
    add_option('klms_module_version', '1.0.0');
}

if (!get_option('klms_installation_date')) {
    add_option('klms_installation_date', date('Y-m-d H:i:s'));
}

if (!get_option('klms_show_clients_my_course_button')) {
    add_option('klms_show_clients_my_course_button', '1');
}

if (!get_option('klms_tab_on_clients_page')) {
    add_option('klms_tab_on_clients_page', '1');
}

// ===================================================================
// FINAL LOG
// ===================================================================
log_activity('E-Learning Module Installed Successfully - All 5 tables created');