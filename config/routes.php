<?php
defined('BASEPATH') or exit('No direct script access allowed');

// ✅ Correct: omit the module name on the RHS
$route['klms/courses_public'] = 'courses_public/index';

// (optional) allow /klms/courses_public/anything to still hit index
$route['klms/courses_public/(.+)'] = 'courses_public/index';
