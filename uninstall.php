<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * LMS Module Uninstallation
 * Removes all tables, options, constraints, files, AND the module folder
 * 
 * @return void
 */
function lms_uninstall_run()
{
    $CI = &get_instance();
    
    try {
        // Start transaction for data integrity
        $CI->db->trans_start();
        
        // ===================================================================
        // 1. REMOVE MODULE OPTIONS
        // ===================================================================
        $options_to_remove = [
            'lms_show_clients_my_course_button',
            'lms_tab_on_clients_page',
            'lms_module_version',
            'lms_installation_date',
        ];
        
        foreach ($options_to_remove as $option) {
            delete_option($option);
        }
        
        log_activity('LMS Module: Removed all module options');
        
        // ===================================================================
        // 2. REMOVE UNIQUE EMAIL CONSTRAINT (if exists)
        // ===================================================================
        $constraint_check = $CI->db->query(
            "SELECT CONSTRAINT_NAME 
             FROM information_schema.TABLE_CONSTRAINTS 
             WHERE TABLE_SCHEMA = DATABASE() 
             AND TABLE_NAME = '" . db_prefix() . "contacts' 
             AND CONSTRAINT_NAME = 'unique_contact_email'"
        )->result();
        
        if (!empty($constraint_check)) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contacts` DROP INDEX `unique_contact_email`");
            log_activity('LMS Module: Removed unique email constraint from contacts');
        }
        
        // ===================================================================
        // 3. DROP TABLES (Disable FK checks for clean removal)
        // ===================================================================
        $CI->db->query('SET FOREIGN_KEY_CHECKS = 0');
        
        // Tables in order (child to parent to respect FK relationships)
        $tables_to_drop = [
            'elearning_video_progress',
            'elearning_progress',
            'elearning_enrollments',
            'elearning_videos',
            'elearning_courses',
        ];
        
        $dropped_count = 0;
        foreach ($tables_to_drop as $table) {
            $full_table = db_prefix() . $table;
            
            if ($CI->db->table_exists($full_table)) {
                $CI->db->query("DROP TABLE IF EXISTS `{$full_table}`");
                $dropped_count++;
                log_activity("LMS Module: Dropped table {$full_table}");
            }
        }
        
        // Re-enable FK checks
        $CI->db->query('SET FOREIGN_KEY_CHECKS = 1');
        
        log_activity("LMS Module: Successfully dropped {$dropped_count} tables");
        
        // ===================================================================
        // 4. REMOVE STAFF PERMISSIONS
        // ===================================================================
        $CI->db->where('feature', 'lms');
        $CI->db->delete(db_prefix() . 'staff_permissions');
        
        log_activity('LMS Module: Removed staff permissions');
        
        // ===================================================================
        // 5. REMOVE CUSTOM FIELDS (if any)
        // ===================================================================
        $CI->db->where('belongsto', 'lms');
        $CI->db->delete(db_prefix() . 'customfields');
        
        // ===================================================================
        // 6. CLEAN UP UPLOAD DIRECTORIES
        // ===================================================================
        $upload_paths = [
            FCPATH . 'uploads/lms/',
            FCPATH . 'uploads/courses/',
            FCPATH . 'uploads/elearning/',
        ];
        
        foreach ($upload_paths as $path) {
            if (is_dir($path)) {
                lms_delete_directory($path);
                log_activity("LMS Module: Removed directory {$path}");
            }
        }
        
        // ===================================================================
        // 7. REMOVE MODULE FROM DATABASE REGISTRY
        // ===================================================================
        $CI->db->where('module_name', 'lms');
        $CI->db->delete(db_prefix() . 'modules');
        
        // ===================================================================
        // 8. REMOVE MODULE MENU CACHE
        // ===================================================================
        if (function_exists('delete_staff_menu_cache')) {
            delete_staff_menu_cache();
        }
        
        // Commit transaction
        $CI->db->trans_complete();
        
        if ($CI->db->trans_status() === FALSE) {
            log_activity('LMS Module: Uninstallation failed - Transaction rolled back');
            return false;
        }
        
        // ===================================================================
        // 9. SCHEDULE MODULE FOLDER DELETION
        // ===================================================================
        $module_path = realpath(__DIR__);
        
        // Create a flag file to mark this module for deletion
        $deletion_flag = APPPATH . '../writable/lms_delete_flag.txt';
        file_put_contents($deletion_flag, $module_path);
        
        // Register shutdown function to delete folder after script completes
        register_shutdown_function('lms_delete_module_folder', $module_path);
        
        log_activity('LMS Module: Scheduled module folder deletion');
        
        // ===================================================================
        // FINAL SUCCESS LOG
        // ===================================================================
        log_activity('LMS Module: Successfully uninstalled - All data removed');
        
        return true;
        
    } catch (Exception $e) {
        $CI->db->trans_rollback();
        log_activity('LMS Module: Uninstallation error - ' . $e->getMessage());
        return false;
    }
}

/**
 * Delete module folder after uninstall completes
 * This runs AFTER the uninstall script finishes
 * 
 * @param string $module_path Path to module directory
 * @return bool
 */
function lms_delete_module_folder($module_path)
{
    // Small delay to ensure all file handles are released
    sleep(1);
    
    try {
        if (is_dir($module_path)) {
            // Use recursive deletion
            $success = lms_delete_directory($module_path);
            
            if ($success) {
                // Log to a separate file since database might not be accessible
                $log_file = APPPATH . '../writable/uninstall_log.txt';
                $log_message = date('Y-m-d H:i:s') . " - LMS Module folder deleted: {$module_path}\n";
                file_put_contents($log_file, $log_message, FILE_APPEND);
                
                return true;
            }
        }
    } catch (Exception $e) {
        // Silent fail - folder might already be deleted
        return false;
    }
    
    return false;
}

/**
 * Recursively delete a directory and all its contents
 * 
 * @param string $dir Directory path
 * @return bool Success status
 */
function lms_delete_directory($dir)
{
    if (!is_dir($dir)) {
        return false;
    }
    
    $items = array_diff(scandir($dir), ['.', '..']);
    
    foreach ($items as $item) {
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        
        if (is_dir($path)) {
            lms_delete_directory($path);
        } else {
            @unlink($path);
        }
    }
    
    return @rmdir($dir);
}