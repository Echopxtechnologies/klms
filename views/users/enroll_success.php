<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
  <div class="col-md-12">
    <div class="panel_s">
      <div class="panel-body text-center">
        <i class="fa fa-check-circle text-success" style="font-size:64px;margin-bottom:10px;"></i>
        <h2>Registration Successful</h2>
        <p class="lead">You have been successfully enrolled in the course:</p>

        <h4 class="text-primary"><?php echo html_escape($course['title'] ?? ''); ?></h4>
        <p><?php echo html_escape($course['description'] ?? ''); ?></p>

        <a href="<?php echo site_url($module_base_url); ?>" class="btn btn-default">
          <i class="fa fa-arrow-left"></i> Back to Courses
        </a>

        <a href="<?php echo site_url($module_base_url . '/course_videos/' . $course['id']); ?>" class="btn btn-info">
          <i class="fa fa-play"></i> Start Learning
        </a>
      </div>
    </div>
  </div>
</div>
