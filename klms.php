<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: KLMS
Description: Adds a "My Course" item in the client theme menu (guest + logged-in), like Appointly's Schedule Appointment.
Version: 1.0.4
Author: Echo Px
*/

define('KLMS_MODULE_NAME', 'klms');

if (function_exists('register_language_files')) {
    register_language_files(KLMS_MODULE_NAME, ['klms']);
}

$helper = __DIR__ . '/helpers/klms_helper.php';
if (file_exists($helper)) {
    require_once $helper;
}

register_activation_hook(KLMS_MODULE_NAME, 'klms_module_activate');
register_deactivation_hook(KLMS_MODULE_NAME, 'klms_module_deactivate');
register_uninstall_hook(KLMS_MODULE_NAME, 'klms_module_uninstall');

hooks()->add_action('admin_init', 'klms_module_init_menu_items');

function klms_module_activate()
{
    require_once __DIR__ . '/install.php';
    if (function_exists('klms_do_install')) {
        klms_do_install();
    }
    if (!get_option('klms_show_clients_my_course_button')) {
        add_option('klms_show_clients_my_course_button', 1);
    }
    if (!get_option('klms_tab_on_clients_page')) {
        add_option('klms_tab_on_clients_page', 1);
    }
}

function klms_module_deactivate() {}

function klms_module_uninstall()
{
    require_once __DIR__ . '/uninstall.php';
    if (function_exists('klms_uninstall_run')) {
        klms_uninstall_run();
    }
}

function klms_module_init_menu_items()
{
    $CI = &get_instance();

    // Parent menu -> valid admin page
    $CI->app_menu->add_sidebar_menu_item('klms', [
        'name'     => _l('klms_menu_title'),
        'href'     => admin_url('lms_admin/courses'), // ✅ valid target
        'position' => 30,
        'icon'     => 'fa fa-graduation-cap',
    ]);

    // Submenus
    $CI->app_menu->add_sidebar_children_item('klms', [
        'slug'     => 'klms-courses',
        'name'     => _l('klms_courses'),
        'href'     => admin_url('klms/Lms_admin/courses'),
        'position' => 1,
    ]);

        $CI->app_menu->add_sidebar_children_item('klms', [
        'slug'     => 'klms-student',
        'name'     => _l('klms_students'),
        'href'     => admin_url('klms/Lms_admin/students'),
        'position' => 1,
    ]);

}
