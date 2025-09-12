<?php defined('BASEPATH') or exit('No direct script access allowed');

hooks()->add_action('clients_init', 'klms_clients_area_get_course');

if (!function_exists('klms_clients_area_get_course')) {
    function klms_clients_area_get_course()
    {
        // ✅ Use the same key we add on activation
        $guestOpt = get_option('klms_show_clients_my_course_button');
        $tabOpt   = get_option('klms_tab_on_clients_page');

        $showGuest = ($guestOpt === null || $guestOpt === '' ? true : ((int)$guestOpt === 1));
        $showTab   = ($tabOpt === null || $tabOpt === '' ? true : ((int)$tabOpt === 1));

        if ($showGuest && !is_client_logged_in()) {
            add_theme_menu_item('klms-my-course-guest', [
                'name'     => _l('klms_get_course'),
                'href'     => site_url('videos'),
                'position' => 10,
            ]);
        }

        if (is_client_logged_in() && $showTab) {
            add_theme_menu_item('klms-my-course-logged', [
                'name'     => _l('klms_get_course'),
                'href'     => site_url('videos'),
                'position' => 1,
            ]);
        }
    }
}
