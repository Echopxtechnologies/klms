<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">


            <h4 class="no-margin"><?php echo html_escape($title ?? 'Add New Student'); ?></h4>
            <hr class="hr-panel-heading" />

           <?php
            // these are passed from the controller
            $base      = isset($module_base_url) ? $module_base_url : '/klms/Lms_users';
            $cid       = isset($course_id) ? (int)$course_id : 0;
            $returnTo  = isset($return_to) ? $return_to : site_url($base);

            // POST to /klms/lms_users/registration/<course_id>?return_to=...
            $action = site_url('klms/lms_users/registration/' . $cid) . '?return_to=' . rawurlencode($returnTo);
            ?>
            <?= form_open($action); ?>
            <input type="hidden" name="course_id" value="<?= $cid; ?>">


              <div class="row">
                <div class="col-md-8">

                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label><span class="text-danger">*</span> First Name</label>
                        <input type="text" name="firstname" class="form-control" required value="<?php echo set_value('firstname'); ?>">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label><span class="text-danger">*</span> Last Name</label>
                        <input type="text" name="lastname" class="form-control" required value="<?php echo set_value('lastname'); ?>">
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label><span class="text-danger">*</span> Email</label>
                        <input type="email" name="email" class="form-control" required value="<?php echo set_value('email'); ?>">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phonenumber" class="form-control" value="<?php echo set_value('phonenumber'); ?>">
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Company</label>
                        <input type="text" name="company" class="form-control" value="<?php echo set_value('company'); ?>">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Leave empty to auto-generate">
                        <p class="help-block">If empty, a random password will be generated.</p>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Country</label>
                        <input type="text" name="country" class="form-control" value="<?php echo set_value('country'); ?>">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" class="form-control" value="<?php echo set_value('city'); ?>">
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" rows="3" class="form-control"><?php echo set_value('address'); ?></textarea>
                  </div>
                </div>

              <div class="btn-bottom-toolbar text-right">
                <a href="<?php echo site_url(); ?>" class="btn btn-default">
                  <i class="fa fa-arrow-left"></i> Cancel
                </a>
                <button type="submit" class="btn btn-info">
                  <i class="fa fa-check"></i> Create Student
                </button>
              </div>

            <?php echo form_close(); ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.breadcrumb{background:#f8f9fa;border-radius:4px;padding:8px 15px;margin-bottom:20px}
.breadcrumb-item + .breadcrumb-item::before{content:'>';padding:0 5px;color:#6c757d}
.breadcrumb-item a{color:#007bff;text-decoration:none}
.breadcrumb-item a:hover{text-decoration:underline}
.breadcrumb-item.active{color:#6c757d}
</style>
