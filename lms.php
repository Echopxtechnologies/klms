<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: lms
Description: Adds a "My Course" item in the client theme menu (guest + logged-in), like Appointly's Schedule Appointment.
Version: 1.0.4
Author: Raju
*/

define('lms_MODULE_NAME', 'lms');

if (function_exists('register_language_files')) {
    register_language_files(lms_MODULE_NAME, ['lms']);
}

$helper = __DIR__ . '/helpers/lms_helper.php';
if (file_exists($helper)) {
    require_once $helper;
}
if (!function_exists('lms_module_init_menu_items')) {
    require_once __DIR__ . '/lms.php';
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
    $dashboardUrl = site_url('lms/Lms_users/dashboard');
    $getCoursesUrl = site_url('lms/lms_users');
    echo '
    <style>
      /* Dashboard Button Styling */
      .navbar .navbar-right .lms-dashboard-btn {
        background-color: #0d6efd; /* Perfex Primary Blue */
        color: #fff !important;
        border-radius: 6px;
        padding: 7px 15px;
        margin-right: 12px;
        font-weight: 500;
        transition: all 0.3s ease;
      }
      .navbar .navbar-right .lms-dashboard-btn:hover {
        background-color: #084298; /* Darker on hover */
        text-decoration: none;
      }
      /* For dark themes or transparent headers */
      .navbar.navbar-default .lms-dashboard-btn {
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
        if (document.querySelector("li.customers-nav-item-lms-dashboard")) return;

        // Build proper <li><a></a></li>
        var li = document.createElement("li");
        li.className = "customers-nav-item-lms-dashboard";
        var a = document.createElement("a");
        a.href = "' . $dashboardUrl . '";
        a.textContent = "Dashboard";
        // If you want it styled like a button, keep class below; else remove it for a normal link
        a.className = "btn btn-primary lms-dashboard-btn";
        li.appendChild(a);

        // Put it first on the right side
        rightNav.prepend(li);
      });
    </script>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    var rightNav = document.querySelector(".customers-top-navbar .navbar-right, .header .navbar-right");
    if (!rightNav) return;

    // ✅ Add Get Courses button (visible for guest + logged-in)
    if (!document.querySelector(".customers-nav-item-lms-get-courses")) {

        var liCourses = document.createElement("li");
        liCourses.className = "customers-nav-item-lms-get-courses";

        var aCourses = document.createElement("a");
        aCourses.href = "'. $getCoursesUrl. '";
        aCourses.textContent = "Get Courses";
        aCourses.className = "btn btn-outline-primary lms-get-courses-btn";
        aCourses.style.marginRight = "12px";

        liCourses.appendChild(aCourses);

        // ✅ Add BEFORE login/profile icon (prepend to right area)
        rightNav.prepend(liCourses);
    }
});
</script>

<style>
/* ✅ Get Courses Button Styling */
.lms-get-courses-btn {
    border-radius: 6px;
    padding: 7px 15px;
    font-weight: 500;
    border: 1px solid #0d6efd;
    background-color: transparent;
    color: #0d6efd !important;
    transition: all 0.3s ease;
}

.lms-get-courses-btn:hover {
    background-color: #084298; /* darker hover */
    border-color: #084298;
    color: #000000ff !important;
}
</style>


    ';
});




register_activation_hook(lms_MODULE_NAME, 'lms_module_activate');
register_deactivation_hook(lms_MODULE_NAME, 'lms_module_deactivate');
register_uninstall_hook(lms_MODULE_NAME, 'lms_module_uninstall');

// Try hook priority -10 (earlier execution)
hooks()->add_action('admin_init', 'lms_module_init_menu_items', -10);

// AND add a fallback hook:
hooks()->add_action('after_admin_init', 'lms_module_init_menu_items', 1);


function lms_module_activate()
{
    require_once __DIR__ . '/install.php';
    if (function_exists('lms_do_install')) {
        lms_do_install();
    }
    if (!get_option('lms_show_clients_my_course_button')) {
        add_option('lms_show_clients_my_course_button', 1);
    }
    if (!get_option('lms_tab_on_clients_page')) {
        add_option('lms_tab_on_clients_page', 1);
    }
}

function lms_module_deactivate() {}
function lms_module_uninstall()
{
    // Load uninstall script
    $uninstall_file = __DIR__ . '/uninstall.php';
    
    if (!file_exists($uninstall_file)) {
        log_activity('LMS Module: uninstall.php not found');
        return false;
    }
    
    require_once $uninstall_file;
    
    if (function_exists('lms_uninstall_run')) {
        $result = lms_uninstall_run();
        
        if ($result === false) {
            set_alert('danger', 'Failed to uninstall LMS module. Check activity log for details.');
        } else {
            set_alert('success', 'LMS Module uninstalled successfully. All data removed.');
        }
        
        return $result;
    } else {
        log_activity('LMS Module: lms_uninstall_run() function not found');
        return false;
    }
}

function lms_module_init_menu_items()
{
    $CI = &get_instance();

    // Parent menu -> valid admin page
    $CI->app_menu->add_sidebar_menu_item('lms', [
        'name'     => _l('lms_menu_title'),
        'href'     => admin_url('lms/Lms_admin/courses'), // ✅ valid target
        'position' => 30,
        'icon'     => 'fa fa-graduation-cap',
    ]);

    // Submenus
    $CI->app_menu->add_sidebar_children_item('lms', [
        'slug'     => 'lms_dashboard',
        'name'     => _l('lms_dashboard'),
        'href'     => admin_url('lms/Lms_admin/dashboard'),
        'position' => 1,
    ]);
    $CI->app_menu->add_sidebar_children_item('lms', [
        'slug'     => 'lms_courses',
        'name'     => _l('lms_courses'),
        'href'     => admin_url('lms/Lms_admin/courses'),
        'position' => 2,
    ]);
        $CI->app_menu->add_sidebar_children_item('lms', [
        'slug'     => 'lms_students',
        'name'     => _l('lms_students'),
        'href'     => admin_url('lms/Lms_admin/students'),
        'position' => 3,
    ]);
    $CI->app_menu->add_sidebar_children_item('lms', [
            'slug'     => 'lms_enrollments',
            'name'     => _l('lms_enrollments'),
            'href'     => admin_url('lms/Lms_admin/enrollments'),
            'position' => 4,
        ]);

}

