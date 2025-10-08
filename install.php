<?php
defined('BASEPATH') or exit('No direct script access allowed');

function klms_do_install(){ /* future db stuff here */ }
add_permission('klms', 'view');
add_permission('klms', 'create');
add_permission('klms', 'edit');
add_permission('klms', 'delete');
if (!is_dir('./uploads/courses/')) {
    mkdir('./uploads/courses/', 0755, true);
}

/**
 * Module Installation
 */

// Get CI instance
$CI = &get_instance();

// Create elearning_courses table
if (!$CI->db->table_exists(db_prefix() . 'elearning_courses')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'elearning_courses` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `description` text,
        `category` varchar(100) DEFAULT NULL,
        `level` enum("Beginner","Intermediate","Advanced") DEFAULT "Beginner",
        `language` varchar(50) DEFAULT "English",
        `price` decimal(10,2) DEFAULT 0.00,
        `is_free` tinyint(1) DEFAULT 0,
        `cover_image` varchar(255) DEFAULT NULL,
        `course_duration` varchar(50) DEFAULT NULL,
        `is_public` tinyint(1) DEFAULT 1,
        `is_active` tinyint(1) DEFAULT 1,
        `sort_order` int(11) DEFAULT 0,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `category` (`category`),
        KEY `level` (`level`),
        KEY `is_active` (`is_active`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
}

// Create elearning_videos table
if (!$CI->db->table_exists(db_prefix() . 'elearning_videos')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'elearning_videos` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `course_id` int(11) NOT NULL,
        `title` varchar(255) NOT NULL,
        `description` text,
        `vimeo_url` varchar(500) DEFAULT NULL,
        `duration` varchar(20) DEFAULT NULL,
        `sort_order` int(11) DEFAULT 0,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `course_id` (`course_id`),
        CONSTRAINT `fk_videos_course` FOREIGN KEY (`course_id`) REFERENCES `' . db_prefix() . 'elearning_courses` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
}

// Create elearning_enrollments table
if (!$CI->db->table_exists(db_prefix() . 'elearning_enrollments')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'elearning_enrollments` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `student_id` int(11) NOT NULL COMMENT "References tblcontacts.id",
        `course_id` int(11) NOT NULL,
        `enrolled_date` datetime NOT NULL,
        `payment_status` enum("paid","pending","failed") DEFAULT "pending",
        `payment_reference` varchar(100) DEFAULT NULL,
        `expiry_date` datetime DEFAULT NULL,
        `access_status` enum("active","revoked","expired") DEFAULT "active",
        `notes` text,
        PRIMARY KEY (`id`),
        UNIQUE KEY `student_course` (`student_id`, `course_id`),
        KEY `student_id` (`student_id`),
        KEY `course_id` (`course_id`),
        CONSTRAINT `fk_enrollments_course` FOREIGN KEY (`course_id`) REFERENCES `' . db_prefix() . 'elearning_courses` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
}

// Create elearning_progress table
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
        UNIQUE KEY `student_course_video` (`student_id`, `course_id`, `video_id`),
        KEY `student_id` (`student_id`),
        KEY `course_id` (`course_id`),
        KEY `video_id` (`video_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
}

// ===================================================================
// CRITICAL: Add unique email constraint to tblcontacts
// ===================================================================

// First, check if the constraint already exists
$constraint_exists = false;
$indexes = $CI->db->query("SHOW INDEX FROM `" . db_prefix() . "contacts` WHERE Key_name = 'unique_contact_email'")->result();

if (empty($indexes)) {
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
    
    log_activity('E-Learning Module: Added unique email constraint to contacts table');
}

// Add module menu items or permissions here if needed
// Example:
// add_option('klms_module_version', '1.0.0');

log_activity('E-Learning Module Installed Successfully');