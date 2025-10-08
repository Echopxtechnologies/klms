<?php
defined('BASEPATH') or exit('No direct script access allowed');
function klms_uninstall_run(){ delete_option('klms_show_clients_my_course_button'); delete_option('klms_tab_on_clients_page'); }

/**
 * Module Uninstallation
 */

// Get CI instance
$CI = &get_instance();

// ===================================================================
// CRITICAL: Remove unique email constraint from tblcontacts
// ===================================================================

// Check if the constraint exists before trying to remove it
$indexes = $CI->db->query("SHOW INDEX FROM `" . db_prefix() . "contacts` WHERE Key_name = 'unique_contact_email'")->result();

if (!empty($indexes)) {
    // Drop the unique constraint
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contacts` 
        DROP INDEX `unique_contact_email`");
    
    log_activity('E-Learning Module: Removed unique email constraint from contacts table');
}

// Drop module tables in reverse order (respecting foreign keys)
$tables_to_drop = [
    'elearning_progress',
    'elearning_enrollments',
    'elearning_videos',
    'elearning_courses',
];

foreach ($tables_to_drop as $table) {
    if ($CI->db->table_exists(db_prefix() . $table)) {
        $CI->db->query('DROP TABLE `' . db_prefix() . $table . '`');
    }
}

// Remove any module options
// delete_option('klms_module_version');

log_activity('E-Learning Module Uninstalled Successfully');