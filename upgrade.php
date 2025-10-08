<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Module Upgrade Script
 */

$CI = &get_instance();

// Get current module version
$current_version = get_option('klms_module_version') ?: '0.0.0';

// Upgrade to version 1.1.0 - Add unique email constraint
if (version_compare($current_version, '1.1.0', '<')) {
    
    $indexes = $CI->db->query("SHOW INDEX FROM `" . db_prefix() . "contacts` WHERE Key_name = 'unique_contact_email'")->result();
    
    if (empty($indexes)) {
        // Clean duplicates
        $CI->db->query("
            DELETE t1 FROM `" . db_prefix() . "contacts` t1
            INNER JOIN `" . db_prefix() . "contacts` t2 
            WHERE t1.id > t2.id 
            AND LOWER(TRIM(t1.email)) = LOWER(TRIM(t2.email))
        ");
        
        // Add constraint
        $CI->db->query("ALTER TABLE `" . db_prefix() . "contacts` 
            ADD UNIQUE KEY `unique_contact_email` (`email`)");
        
        log_activity('E-Learning Module: Upgraded to v1.1.0 - Added email uniqueness');
    }
    
    // Add elearning_progress table if not exists
    if (!$CI->db->table_exists(db_prefix() . 'elearning_progress')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . 'elearning_progress` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `student_id` int(11) NOT NULL,
            `course_id` int(11) NOT NULL,
            `video_id` int(11) NOT NULL,
            `watched_at` datetime NOT NULL,
            `watch_duration` int(11) DEFAULT 0,
            `completed` tinyint(1) DEFAULT 0,
            `last_position` int(11) DEFAULT 0,
            PRIMARY KEY (`id`),
            UNIQUE KEY `student_course_video` (`student_id`, `course_id`, `video_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
    }
    
    update_option('klms_module_version', '1.1.0');
}