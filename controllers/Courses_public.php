<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * PUBLIC (no login required)
 * If you want it under client theme (header/footer), keep ClientsController.
 * If you want a truly standalone public page, use App_Controller.
 */
class Courses_public extends ClientsController // or App_Controller if you want it standalone
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('klms', 'english');
    }

    public function index()
    {
        // If extending ClientsController (uses client theme layout):
        $data['title'] = _l('klms_get_course');
        $this->data($data);
        $this->view('public/index');  // looks for modules/klms/views/public/index.php
        $this->layout();

        // If you switch to App_Controller (standalone), use:
        // $data['title'] = _l('klms_get_course');
        // $this->load->view('klms/public/index', $data);
    }
}
