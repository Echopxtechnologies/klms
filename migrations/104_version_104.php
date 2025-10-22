<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: Version 1.0.4 for E-Learning (lms) Module
 * Purpose: Add "thumbnail" column to tblelearning_courses
 */

class Migration_Version_104
{
    public function up()
    {
        $CI = &get_instance();

        // Check if the main courses table exists before altering
        if ($CI->db->table_exists(db_prefix() . 'elearning_courses')) {
            if (!$CI->db->field_exists('thumbnail', db_prefix() . 'elearning_courses')) {
                $CI->db->query('ALTER TABLE ' . db_prefix() . 'elearning_courses ADD COLUMN thumbnail VARCHAR(255) DEFAULT NULL;');
                log_activity('lms Migration 104: Added "thumbnail" column to tblelearning_courses');
            } else {
                log_activity('lms Migration 104: "thumbnail" column already exists in tblelearning_courses');
            }
        } else {
            log_activity('lms Migration 104: Table ' . db_prefix() . 'elearning_courses not found, skipping migration.');
        }
    }

    public function down()
    {
        $CI = &get_instance();

        if ($CI->db->table_exists(db_prefix() . 'elearning_courses')) {
            if ($CI->db->field_exists('thumbnail', db_prefix() . 'elearning_courses')) {
                $CI->db->query('ALTER TABLE ' . db_prefix() . 'elearning_courses DROP COLUMN thumbnail;');
                log_activity('lms Migration 104: Dropped "thumbnail" column from tblelearning_courses');
            }
        }
    }
}
