<?php
defined('BASEPATH') or exit('No direct script access allowed');

function klms_do_install(){ /* future db stuff here */ }
add_permission('klms', 'view');
add_permission('klms', 'create');
add_permission('klms', 'edit');
add_permission('klms', 'delete');