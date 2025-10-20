<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: KLMS
Description: Adds a "My Course" item in the client theme menu (guest + logged-in), like Appointly's Schedule Appointment.
Version: 1.0.4
Author: Raju
*/

define('KLMS_MODULE_NAME', 'klms');

if (function_exists('register_language_files')) {
    register_language_files(KLMS_MODULE_NAME, ['klms']);
}

$helper = __DIR__ . '/helpers/klms_helper.php';
if (file_exists($helper)) {
    require_once $helper;
}
if (!function_exists('klms_module_init_menu_items')) {
    require_once __DIR__ . '/klms.php';
}




// Remove "Files" and "Calendar" from client navigation
hooks()->add_filter('customers_area_navigation', function ($nav) {
    foreach ($nav as $key => $item) {
        $slug = $item['slug'] ?? $key;
        if (in_array($slug, ['files', 'calendar'], true)) {
            unset($nav[$key]);
        }
    }
    return $nav;
});

// Also hide any top-right shortcuts that some themes add
hooks()->add_action('app_customers_head', function () {
    echo '<style>
      a[href*="clients/files"],
      a[href*="clients/calendar"]{display:none!important}
    </style>';
});

// Add "Dashboard" nav item (only after login; not on login page)
hooks()->add_action('app_customers_head', function () {
    $dashboardUrl = site_url('klms/Lms_users/dashboard');
    echo '
    <style>
      /* Dashboard Button Styling */
      .navbar .navbar-right .klms-dashboard-btn {
        background-color: #0d6efd; /* Perfex Primary Blue */
        color: #fff !important;
        border-radius: 6px;
        padding: 7px 15px;
        margin-right: 12px;
        font-weight: 500;
        transition: all 0.3s ease;
      }
      .navbar .navbar-right .klms-dashboard-btn:hover {
        background-color: #084298; /* Darker on hover */
        text-decoration: none;
      }
      /* For dark themes or transparent headers */
      .navbar.navbar-default .klms-dashboard-btn {
        border: 1px solid rgba(255, 255, 255, 0.2);
      }
    </style>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        // Must be logged in (profile menu exists in the logged-in header)
        var rightNav = document.querySelector(".customers-top-navbar .navbar-right, .header .navbar-right");
        var hasProfile = document.querySelector("li.customers-nav-item-profile");
        if (!rightNav || !hasProfile) return;

        // Don\'t duplicate
        if (document.querySelector("li.customers-nav-item-klms-dashboard")) return;

        // Build proper <li><a></a></li>
        var li = document.createElement("li");
        li.className = "customers-nav-item-klms-dashboard";
        var a = document.createElement("a");
        a.href = "' . $dashboardUrl . '";
        a.textContent = "Dashboard";
        // If you want it styled like a button, keep class below; else remove it for a normal link
        a.className = "btn btn-primary klms-dashboard-btn";
        li.appendChild(a);

        // Put it first on the right side
        rightNav.prepend(li);
      });
    </script>
    ';
});




register_activation_hook(KLMS_MODULE_NAME, 'klms_module_activate');
register_deactivation_hook(KLMS_MODULE_NAME, 'klms_module_deactivate');
register_uninstall_hook(KLMS_MODULE_NAME, 'klms_module_uninstall');

// Try hook priority -10 (earlier execution)
hooks()->add_action('admin_init', 'klms_module_init_menu_items', -10);

// AND add a fallback hook:
hooks()->add_action('after_admin_init', 'klms_module_init_menu_items', 1);


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
        'href'     => admin_url('klms/Lms_admin/courses'), // ✅ valid target
        'position' => 30,
        'icon'     => 'fa fa-graduation-cap',
    ]);

    // Submenus
    $CI->app_menu->add_sidebar_children_item('klms', [
        'slug'     => 'klms_dashboard',
        'name'     => _l('klms_dashboard'),
        'href'     => admin_url('klms/Lms_admin/dashboard'),
        'position' => 1,
    ]);
    $CI->app_menu->add_sidebar_children_item('klms', [
        'slug'     => 'klms_courses',
        'name'     => _l('klms_courses'),
        'href'     => admin_url('klms/Lms_admin/courses'),
        'position' => 2,
    ]);
        $CI->app_menu->add_sidebar_children_item('klms', [
        'slug'     => 'klms_students',
        'name'     => _l('klms_students'),
        'href'     => admin_url('klms/Lms_admin/students'),
        'position' => 3,
    ]);
    $CI->app_menu->add_sidebar_children_item('klms', [
            'slug'     => 'klms_enrollments',
            'name'     => _l('klms_enrollments'),
            'href'     => admin_url('klms/Lms_admin/enrollments'),
            'position' => 4,
        ]);

}

