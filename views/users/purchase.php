<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="content">
  <div class="row">
    <div class="col-md-8 col-md-offset-2">
      <div class="panel_s">
        <div class="panel-body text-center">
          <h3>Course Purchase</h3>
          <p class="text-muted">You are purchasing the course: <strong><?php echo html_escape($course['title'] ?? 'Selected Course'); ?></strong></p>
          <h4>Price: ₹<?php echo number_format($course['price'] ?? 0, 2); ?></h4>

          <hr>

          <!-- Simulate Payment -->
          <p>For testing, click the button below to simulate a successful payment.</p>
          <a href="<?php echo site_url('klms/lms_users/payment_success/' . $course['id']); ?>" class="btn btn-success btn-lg">
            <i class="fa fa-check"></i> Simulate Payment Success
          </a>
          <a href="<?php echo site_url('klms/lms_users/view_course/' . $course['id']); ?>" class="btn btn-default btn-lg">
            Cancel
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
