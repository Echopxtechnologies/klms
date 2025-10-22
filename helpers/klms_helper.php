<?php defined('BASEPATH') or exit('No direct script access allowed');

hooks()->add_action('clients_init', 'lms_clients_area_get_course');

if (!function_exists('lms_clients_area_get_course')) {
    function lms_clients_area_get_course()
    {
        // Use the same key we add on activation
        $guestOpt = get_option('lms_show_clients_my_course_button');
        $tabOpt   = get_option('lms_tab_on_clients_page');

        $showGuest = ($guestOpt === null || $guestOpt === '' ? true : ((int)$guestOpt === 1));
        $showTab   = ($tabOpt === null || $tabOpt === '' ? true : ((int)$tabOpt === 1));

        // Define consistent base URL for the module
        $module_base_url = 'lms/lms_users';

        if ($showGuest && !is_client_logged_in()) {
            add_theme_menu_item('lms-my-course-guest', [
                'name'     => _l('lms_get_course'),
                'href'     => site_url($module_base_url),
                'position' => 10,
            ]);
        }

        if (is_client_logged_in() && $showTab) {
            add_theme_menu_item('lms-my-course-logged', [
                'name'     => _l('lms_get_course'),
                'href'     => site_url($module_base_url),
                'position' => 1,
            ]);
        }
    }
}

/**
 * Helper function to get consistent module base URL
 */
if (!function_exists('lms_get_base_url')) {
    function lms_get_base_url($path = '') {
        $base = 'lms/lms_users';
        return empty($path) ? site_url($base) : site_url($base . '/' . ltrim($path, '/'));
    }
}