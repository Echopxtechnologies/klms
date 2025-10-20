<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * E-Learning Module Uninstallation
 * Removes all tables, options, and constraints
 */

// Get CI instance
$CI = &get_instance();

// ===================================================================
// REMOVE MODULE OPTIONS
// ===================================================================
delete_option('klms_show_clients_my_course_button');
delete_option('klms_tab_on_clients_page');
delete_option('klms_module_version');
delete_option('klms_installation_date');

log_activity('E-Learning: Removed module options');

// ===================================================================
// REMOVE UNIQUE EMAIL CONSTRAINT FROM CONTACTS TABLE
// ===================================================================
$indexes = $CI->db->query("SHOW INDEX FROM `" . db_prefix() . "contacts` WHERE Key_name = 'unique_contact_email'")->result();

if (!empty($indexes)) {
    try {
        $CI->db->query("ALTER TABLE `" . db_prefix() . "contacts` DROP INDEX `unique_contact_email`");
        log_activity('E-Learning: Removed unique email constraint from contacts table');
    } catch (Exception $e) {
        log_activity('E-Learning: Failed to remove email constraint: ' . $e->getMessage());
    }
}

// ===================================================================
// DROP TABLES IN REVERSE ORDER (Respecting Foreign Keys)
// ===================================================================

// Order is critical: Drop child tables before parent tables
$tables_to_drop = [
    'elearning_video_progress',  // No FK dependencies
    'elearning_progress',         // No FK dependencies
    'elearning_enrollments',      // FK to courses and contacts
    'elearning_videos',           // FK to courses
    'elearning_courses',          // Parent table (drop last)
];

foreach ($tables_to_drop as $table) {
    $full_table_name = db_prefix() . $table;
    
    if ($CI->db->table_exists($full_table_name)) {
        try {
            // Disable foreign key checks temporarily
            $CI->db->query('SET FOREIGN_KEY_CHECKS = 0');
            
            // Drop the table
            $CI->db->query('DROP TABLE IF EXISTS `' . $full_table_name . '`');
            
            // Re-enable foreign key checks
            $CI->db->query('SET FOREIGN_KEY_CHECKS = 1');
            
            log_activity('E-Learning: Dropped table ' . $full_table_name);
        } catch (Exception $e) {
            log_activity('E-Learning: Failed to drop table ' . $full_table_name . ' - ' . $e->getMessage());
        }
    }
}

// ===================================================================
// REMOVE UPLOAD DIRECTORIES (Optional - Uncomment if you want to delete files)
// ===================================================================
/*
$upload_dir = './uploads/courses/';
if (is_dir($upload_dir)) {
    // Delete all files in directory
    $files = glob($upload_dir . '*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    
    // Remove directory
    rmdir($upload_dir);
    
    log_activity('E-Learning: Removed uploads/courses directory');
}
*/

// ===================================================================
// REMOVE PERMISSIONS
// ===================================================================
$CI->db->where('shortname', 'klms');
$CI->db->delete(db_prefix() . 'permissions');

log_activity('E-Learning: Removed module permissions');

// ===================================================================
// FINAL LOG
// ===================================================================
log_activity('E-Learning Module Uninstalled Successfully - All 5 tables dropped');
