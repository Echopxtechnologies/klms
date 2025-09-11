<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: KLMS
Description: Adds a "My Course" item in the client theme menu (guest + logged-in), like Appointly's Schedule Appointment.
Version: 1.0.3
Author: Echo Px
*/

define('KLMS_MODULE_NAME', 'klms');

/**
 * Load language and helper
 */
if (function_exists('register_language_files')) {
    register_language_files(KLMS_MODULE_NAME, ['klms']);
}

// Require helper (this registers the clients_init hook)
$helper = __DIR__ . '/helpers/klms_helper.php';
if (file_exists($helper)) {
    require_once $helper;
}

/**
 * Activation / Deactivation / Uninstall
 */
register_activation_hook(KLMS_MODULE_NAME, 'klms_module_activate');
register_deactivation_hook(KLMS_MODULE_NAME, 'klms_module_deactivate');
register_uninstall_hook(KLMS_MODULE_NAME, 'klms_module_uninstall');

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
