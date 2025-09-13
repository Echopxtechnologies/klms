<?php
defined('BASEPATH') or exit('No direct script access allowed');
$route = [];
$route['videos'] = 'klms/lms_users/index';
$route['lms_users'] = 'lms_users';
$route['lms_users/index'] = 'lms_users/index';
$route['lms_users/view_course/(:num)'] = 'lms_users/view_course/$1';
$route['lms_users/course_videos/(:num)'] = 'lms_users/course_videos/$1';
$route['lms_users/watch_video/(:num)/(:num)'] = 'lms_users/watch_video/$1/$2';