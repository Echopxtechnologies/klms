<?php
defined('BASEPATH') or exit('No direct script access allowed');
function klms_uninstall_run(){ delete_option('klms_show_clients_my_course_button'); delete_option('klms_tab_on_clients_page'); }
